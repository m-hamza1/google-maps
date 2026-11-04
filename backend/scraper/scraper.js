/**
 * Google Maps Scraper Worker
 * Fast, headless extraction of targeted leads with clean data normalization.
 */

const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');

function findChromePath() {
    const candidates = [
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
        '/usr/bin/google-chrome',
        '/usr/bin/google-chrome-stable',
        '/usr/bin/chromium-browser',
        '/usr/bin/chromium',
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
    ];

    for (const p of candidates) {
        if (fs.existsSync(p)) return p;
    }
    return null;
}

const args = process.argv.slice(2);
let query = 'Electric Store in Lahore';
let limit = 20;
let fetchDetails = true;

for (let i = 0; i < args.length; i++) {
    if (args[i].startsWith('--query=')) {
        query = args[i].replace('--query=', '');
    } else if (args[i].startsWith('--limit=')) {
        limit = parseInt(args[i].replace('--limit=', ''), 10) || 20;
    } else if (args[i] === '--fast') {
        fetchDetails = false;
    }
}

async function scrapeGoogleMaps(searchQuery, maxResults, shouldFetchDetails) {
    const executablePath = findChromePath();
    if (!executablePath) {
        throw new Error('Chrome/Edge executable not found on host machine.');
    }

    const browser = await puppeteer.launch({
        executablePath,
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--window-size=1280,900',
            '--lang=en-US,en'
        ]
    });

    try {
        const page = await browser.newPage();
        await page.setViewport({ width: 1280, height: 900 });
        await page.setUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
        );

        const url = `https://www.google.com/maps/search/${encodeURIComponent(searchQuery)}?hl=en`;
        await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });

        const feedSelector = 'div[role="feed"]';
        try {
            await page.waitForSelector(feedSelector, { timeout: 8000 });
        } catch (e) {}

        // Hover over feed to ensure wheel and scroll events target it
        const feedHandle = await page.$('div[role="feed"]');
        if (feedHandle) {
            const box = await feedHandle.boundingBox();
            if (box) {
                await page.mouse.move(box.x + box.width / 2, box.y + box.height / 2);
            }
        }

        // Scroll feed to load requested limit
        const scrollRounds = Math.max(50, Math.ceil(maxResults / 2) + 20);
        let prevCount = 0;
        let stagnationCount = 0;

        for (let s = 0; s < scrollRounds; s++) {
            const count = await page.evaluate(() => {
                return document.querySelectorAll('div[role="feed"] a.hfpxzc').length;
            });
            if (count >= maxResults) break;

            if (count === prevCount && count > 0) {
                stagnationCount++;
                // Allow up to 12 rounds (~16 seconds) for Google Maps network batch to arrive
                if (stagnationCount >= 12) {
                    const isEnd = await page.evaluate(() => {
                        const endSpan = document.querySelector('div[role="feed"] span.HlvSq');
                        return endSpan && endSpan.innerText && endSpan.innerText.toLowerCase().includes('reached the end');
                    });
                    if (isEnd) break;
                    // If no explicit end marker but stuck for 15+ rounds, stop
                    if (stagnationCount >= 16) break;
                }
            } else {
                stagnationCount = 0;
            }
            prevCount = count;

            // Trigger mouse wheel for native infinite scroll
            await page.mouse.wheel({ deltaY: 2500 });

            // Also programmatically scroll the last card into view
            await page.evaluate(() => {
                const feed = document.querySelector('div[role="feed"]');
                if (feed) {
                    const cards = feed.querySelectorAll('div.Nv2PK');
                    if (cards.length > 0) {
                        cards[cards.length - 1].scrollIntoView({ behavior: 'smooth', block: 'end' });
                    }
                    feed.scrollTop = feed.scrollHeight;
                }
            });
            await new Promise((r) => setTimeout(r, 1300));
        }

        // Collect card links and card-level details first
        const items = await page.evaluate((max) => {
            const results = [];
            const links = Array.from(document.querySelectorAll('div[role="feed"] a.hfpxzc'));

            for (const link of links) {
                if (results.length >= max) break;
                const card = link.closest('div.Nv2PK') || link.closest('div[jsaction]');
                const name = link.getAttribute('aria-label') || (card ? card.querySelector('div.qBF1Pd')?.innerText : null);
                if (!name) continue;

                const href = link.getAttribute('href') || '';
                let placeId = null;
                const matchId = href.match(/!1s([^!]+)/);
                if (matchId) placeId = matchId[1];

                let lat = null;
                let lng = null;
                const coordsMatch = href.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
                if (coordsMatch) {
                    lat = parseFloat(coordsMatch[1]);
                    lng = parseFloat(coordsMatch[2]);
                }

                let rating = null;
                let reviewsCount = 0;
                let phone = null;
                let address = null;
                let category = null;

                if (card) {
                    const ratingEl = card.querySelector('span.MW4etd') || card.querySelector('span[aria-label*="stars" i]');
                    if (ratingEl) {
                        const rText = ratingEl.innerText || ratingEl.getAttribute('aria-label') || '';
                        const matchR = rText.match(/(\d+\.\d+|\d+)/);
                        if (matchR) rating = parseFloat(matchR[1]);
                    }
                    const reviewCountEl = card.querySelector('span.UY7F9');
                    if (reviewCountEl) {
                        const revText = reviewCountEl.innerText.replace(/[^0-9]/g, '');
                        if (revText) reviewsCount = parseInt(revText, 10);
                    }

                    // Extract text lines from card
                    const textLines = Array.from(card.querySelectorAll('div.W4Efsd, span')).map(el => el.innerText.trim()).filter(Boolean);
                    for (const line of textLines) {
                        // Phone pattern
                        if (!phone) {
                            const pMatch = line.match(/(?:\+?\d{1,4}[-.\s]?)?\(?\d{2,4}\)?[-.\s]?\d{3,4}[-.\s]?\d{3,4}/);
                            if (pMatch && pMatch[0].length >= 8) phone = pMatch[0].trim();
                        }
                        // Address pattern
                        if (!address && (line.includes('Rd') || line.includes('St') || line.includes('Market') || line.includes('Plaza') || line.includes('Bazaar') || line.includes('Lahore') || line.includes('Block') || line.includes('Town') || line.includes('TX') || line.includes('Ave'))) {
                            address = line;
                        }
                    }
                }

                results.push({
                    index: results.length,
                    place_id: placeId,
                    name: name.trim(),
                    href: href,
                    rating,
                    reviews_count: reviewsCount,
                    latitude: lat,
                    longitude: lng,
                    phone,
                    address,
                    website: null,
                    category: category || 'Business'
                });
            }
            return results;
        }, maxResults);

        // Enrich items with complete Phone, Address, Website, and Category using parallel worker tabs
        if (shouldFetchDetails && items.length > 0) {
            const CONCURRENCY = Math.min(4, items.length);
            const workers = await Promise.all(
                Array.from({ length: CONCURRENCY }).map(() => browser.newPage())
            );

            let nextIdx = 0;
            async function worker(workerPage) {
                while (nextIdx < items.length) {
                    const i = nextIdx++;
                    const it = items[i];
                    if (!it.href) continue;

                    try {
                        await workerPage.goto(it.href, { waitUntil: 'domcontentloaded', timeout: 12000 });
                        try {
                            await workerPage.waitForSelector(
                                'button[data-item-id*="address"], button[data-item-id*="phone"], a[data-item-id*="authority"]',
                                { timeout: 2500 }
                            );
                        } catch (e) {}

                        const data = await workerPage.evaluate(() => {
                            const clean = (t) => t ? t.replace(/[\uE000-\uF8FF]/g, '').trim() : null;
                            const phoneEl = document.querySelector('button[data-item-id*="phone"] .fontBodyMedium, button[data-tooltip*="phone" i]');
                            const addrEl = document.querySelector('button[data-item-id*="address"] .fontBodyMedium, button[data-tooltip*="address" i]');
                            const webEl = document.querySelector('a[data-item-id*="authority"], a[data-tooltip*="website" i]');
                            const catEl = document.querySelector('button.DkEaL, span.DkEaL');

                            return {
                                phone: clean(phoneEl ? phoneEl.innerText : null),
                                address: clean(addrEl ? addrEl.innerText : null),
                                website: webEl ? webEl.getAttribute('href') : null,
                                category: clean(catEl ? catEl.innerText : null)
                            };
                        });

                        if (data.phone) it.phone = data.phone;
                        if (data.address) it.address = data.address;
                        if (data.website) it.website = data.website;
                        if (data.category) it.category = data.category;
                    } catch (err) {
                        // Silently fallback to card-level data
                    }
                }
            }

            await Promise.all(workers.map((w) => worker(w)));
        }

        // Clean up temporary href from result items
        for (const item of items) {
            delete item.href;
        }

        return items;
    } finally {
        await browser.close();
    }
}

scrapeGoogleMaps(query, limit, fetchDetails)
    .then((data) => {
        console.log(JSON.stringify({ success: true, count: data.length, data }));
        process.exit(0);
    })
    .catch((err) => {
        console.error(JSON.stringify({ success: false, error: err.message }));
        process.exit(1);
    });
