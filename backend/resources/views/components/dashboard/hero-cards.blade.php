<div class="hero-row">

    <!-- Left Hero: White Active Balance Style Card -->
    <div class="hero-card-white">
        <div class="hero-top-meta">
            <div class="currency-pills">
                <button type="button" class="curr-pill active">ALL LEADS</button>
                <button type="button" class="curr-pill">VERIFIED</button>
                <button type="button" class="curr-pill">FRESH</button>
            </div>
            <div class="account-num">DATABASE REPOSITORY ID: <strong>9034-0222-748</strong></div>
        </div>

        <div>
            <div class="balance-label">Total Verified Leads</div>
            <div class="balance-amount">
                <span id="statTotalVerified">{{ number_format($stats['total']) }}</span>
                <span class="material-symbols-outlined" style="color: var(--text-light); font-size: 26px;">visibility</span>
            </div>
        </div>

        <div class="hero-action-buttons">
            <button type="button" class="btn-pill-dark" onclick="openScraperModal()">
                <span class="material-symbols-outlined" style="font-size: 18px; color: var(--lime-primary);">bolt</span> Run Scraper
            </button>
            <a href="{{ route('leads.export') }}" class="btn-pill-light">
                <span class="material-symbols-outlined" style="font-size: 18px;">download</span> Export CSV
            </a>
            <button type="button" class="circle-action-btn" onclick="openScraperModal()" title="Start New Scrape">
                <span class="material-symbols-outlined" style="font-size: 20px;">north_east</span>
            </button>
        </div>
    </div>

    <!-- Right Hero: Vibrant Lime Gradient Rewards Style Card -->
    <div class="hero-card-lime">
        <div class="lime-top-row">
            <div class="priority-badge">
                <span class="material-symbols-outlined" style="font-size: 16px;">verified</span>
                <span>Priority Queue</span>
            </div>
            <button type="button" class="btn-pill-dark" style="padding: 6px 14px; font-size: 11px;" onclick="openScraperModal()">
                + Add Niche
            </button>
        </div>

        <div>
            <div class="rewards-label">Fresh Uncontacted Leads</div>
            <div class="rewards-amount">
                <span id="statNewLeads">{{ number_format($stats['new']) }}</span>
                @php
                    $newPct = $stats['total'] > 0 ? round(($stats['new'] / $stats['total']) * 100) : 0;
                @endphp
                <span class="growth-badge" id="statNewBadge">{{ $newPct }}% of Total</span>
            </div>
        </div>

        <div class="lime-sub-pills">
            <div class="lime-pill-card">
                <span>
                    <span class="material-symbols-outlined icon-fill" style="font-size: 16px; color: var(--lime-dark);">star</span>
                    High Rating (4.5+)
                </span>
                <strong id="statHighRating">{{ number_format($stats['high_rating']) }}</strong>
            </div>
            <div class="lime-pill-card">
                <span>
                    <span class="material-symbols-outlined" style="font-size: 16px; color: var(--lime-dark);">smartphone</span>
                    With 2nd Phone
                </span>
                <strong id="statWith2ndPhone">{{ number_format($stats['with_secondary_phone']) }}</strong>
            </div>
        </div>
    </div>

</div>
