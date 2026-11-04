<!-- Hidden Delete Form for Single Deletion -->
<form id="singleDeleteForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal: Trigger Scraper -->
<div id="scraperModal" class="modal-backdrop">
    <div class="modal-dialog-box">
        <div class="modal-title-row">
            <h3>
                <span class="material-symbols-outlined" style="font-size: 24px; color: var(--lime-dark);">bolt</span>
                Run Google Maps Scraper
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeScraperModal()">
                <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
            </button>
        </div>
        <form id="scraperForm" action="{{ route('scrape.trigger') }}" method="POST" onsubmit="event.preventDefault(); submitScraperAsync();">
            @csrf
            <div class="form-group-item">
                <label class="form-label-item">Business Niche / Keyword</label>
                <input type="text" id="scraperQuery" name="query" class="form-control-item" placeholder="e.g. Electric Store, Dentists, Cafes" required autofocus>
            </div>
            <div class="form-group-item">
                <label class="form-label-item">Target Location / City</label>
                <input type="text" id="scraperLocation" name="location" class="form-control-item" placeholder="e.g. Lahore or New York, NY">
            </div>
            <div class="form-group-item">
                <label class="form-label-item">Max Listings to Extract</label>
                <select id="scraperLimit" name="limit" class="form-control-item">
                    <option value="20">20 Leads (Quick ~25s)</option>
                    <option value="50">50 Leads (~45s)</option>
                    <option value="100" selected>100 Leads (Recommended ~1.5 min)</option>
                    <option value="200">200 Leads (High Volume ~2.5 min)</option>
                    <option value="300">300 Leads (Deep Extraction ~4 min)</option>
                    <option value="500">500 Leads (Maximum ~6 min)</option>
                </select>
            </div>
            <button type="submit" class="btn-submit-scrape" id="submitScrapeBtn">
                <span class="material-symbols-outlined" style="font-size: 20px;">play_arrow</span>
                Start Scraping Now
            </button>
        </form>
    </div>
</div>

<!-- Modal: Edit Lead Custom Info -->
<div id="editModal" class="modal-backdrop">
    <div class="modal-dialog-box">
        <div class="modal-title-row">
            <h3 id="modalLeadTitle">Edit Lead</h3>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()">
                <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" action="" onsubmit="event.preventDefault(); submitEditLeadForm();">
            @csrf
            @method('PATCH')
            <div class="form-group-item">
                <label class="form-label-item">Secondary / Alternate Phone</label>
                <input type="text" id="modalSecPhone" name="secondary_phone" class="form-control-item" placeholder="e.g. +92 300 1234567 or +1 555-0199">
            </div>
            <div class="form-group-item">
                <label class="form-label-item">Pipeline Status</label>
                <select id="modalStatus" name="status" class="form-control-item">
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="interested">Interested</option>
                    <option value="not_interested">Not Interested</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div class="form-group-item">
                <label class="form-label-item">Custom Notes & Follow-up</label>
                <textarea id="modalNotes" name="notes" class="form-control-item" rows="4" placeholder="Add custom CRM notes against this lead..."></textarea>
            </div>
            <button type="submit" class="btn-submit-scrape">
                <span class="material-symbols-outlined" style="font-size: 20px;">check</span>
                Save Updates
            </button>
        </form>
    </div>
</div>

<!-- In-App Custom Action Confirmation Modal -->
<div id="actionConfirmModal" class="modal-backdrop">
    <div class="modal-dialog-box confirm-dialog-box">
        <div class="confirm-icon-circle icon-danger" id="confirmModalIconCircle">
            <span class="material-symbols-outlined" id="confirmModalIcon">delete</span>
        </div>
        <h3 id="confirmModalTitle" class="confirm-modal-title">Confirm Action</h3>
        <p id="confirmModalMessage" class="confirm-modal-message">Are you sure you want to proceed?</p>
        
        <div class="confirm-modal-actions">
            <button type="button" class="btn-confirm-cancel" id="confirmModalCancelBtn">Cancel</button>
            <button type="button" class="btn-confirm-proceed btn-danger" id="confirmModalProceedBtn">
                <span class="material-symbols-outlined" id="confirmModalProceedIcon" style="font-size: 18px;">delete</span>
                <span id="confirmModalProceedText">Delete</span>
            </button>
        </div>
    </div>
</div>
