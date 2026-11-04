<!-- Leads Management Data Table Component -->
@php
    $statusMap = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'interested' => 'Interested',
        'not_interested' => 'Not Interested',
        'closed' => 'Closed',
    ];
@endphp
<div class="leads-table-card" id="leadsTableSection">

    <!-- Top Search and Filter Bar -->
    <div class="table-top-bar">
        <div>
            <h3 style="font-size: 18px; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="color: var(--lime-dark); font-size: 22px;">dataset</span>
                Leads Repository
                <span id="tableLoadingSpinner" class="table-spinner" style="display: none;">
                    <span class="material-symbols-outlined spin-animation" style="font-size: 18px; color: var(--lime-dark);">sync</span>
                </span>
            </h3>
            <p style="font-size: 12px; color: var(--text-muted);">Real-time search, city & status filters without page reload</p>
        </div>

        <!-- Search and Filter Controls -->
        <form id="leadsFilterForm" onsubmit="event.preventDefault(); applyLeadsFilter();" class="search-form">
            <div class="search-box-wrapper">
                <span class="material-symbols-outlined search-icon-inside">search</span>
                <input type="text" id="filterSearch" name="search" class="search-box" placeholder="Search business, phone, notes..." value="{{ request('search') }}" oninput="debounceFilter();">
            </div>

            <!-- GodUI FilterBar Facets (@godui/filter-bar) -->
            <div class="godui-filter-bar">
                <!-- Facet: City -->
                @php
                    $selectedCity = request('city');
                @endphp
                <div class="godui-facet-wrapper" id="facetCityWrapper">
                    <input type="hidden" id="filterCity" name="city" value="{{ $selectedCity }}">
                    <div class="godui-facet-pill {{ $selectedCity ? 'has-selection' : '' }}">
                        <button type="button" class="godui-pill-btn" onclick="toggleGoduiFacet('cityFacet', event)">
                            @if(!$selectedCity)
                                <span class="material-symbols-outlined godui-plus-icon">add</span>
                            @endif
                            <span class="godui-facet-label">City</span>
                            @if($selectedCity)
                                <span class="godui-pill-divider"></span>
                                <span class="godui-pill-summary">{{ $selectedCity }}</span>
                            @endif
                        </button>
                        @if($selectedCity)
                            <button type="button" class="godui-pill-clear-btn" onclick="clearFacet('filterCity', event)" title="Clear City">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        @endif
                    </div>
                    
                    <div class="godui-facet-popover" id="cityFacetMenu">
                        <div class="godui-popover-search-wrap">
                            <span class="material-symbols-outlined">search</span>
                            <input type="text" class="godui-popover-search" placeholder="Search city…" oninput="filterGoduiFacet(this)">
                        </div>
                        <ul class="godui-popover-list">
                            @foreach($cities ?? [] as $c)
                                <li class="godui-popover-item" onclick="selectGoduiFacetOption('filterCity', '{{ addslashes($c) }}')">
                                    <span class="godui-checkbox {{ $selectedCity === $c ? 'checked' : '' }}">
                                        @if($selectedCity === $c)
                                            <span class="material-symbols-outlined check-icon">check</span>
                                        @endif
                                    </span>
                                    <span class="godui-item-label">{{ $c }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Facet: Status -->
                @php
                    $selectedStatus = request('status');
                    $statusOptions = [
                        'new' => ['label' => 'New', 'count' => $stats['new'] ?? 0],
                        'contacted' => ['label' => 'Contacted', 'count' => $stats['contacted'] ?? 0],
                        'interested' => ['label' => 'Interested', 'count' => $stats['interested'] ?? 0],
                        'not_interested' => ['label' => 'Not Interested', 'count' => 0],
                        'closed' => ['label' => 'Closed', 'count' => $stats['closed'] ?? 0],
                    ];
                    $currentStatusLabel = $selectedStatus && isset($statusOptions[$selectedStatus]) ? $statusOptions[$selectedStatus]['label'] : null;
                @endphp
                <div class="godui-facet-wrapper" id="facetStatusWrapper">
                    <input type="hidden" id="filterStatus" name="status" value="{{ $selectedStatus }}">
                    <div class="godui-facet-pill {{ $selectedStatus ? 'has-selection' : '' }}">
                        <button type="button" class="godui-pill-btn" onclick="toggleGoduiFacet('statusFacet', event)">
                            @if(!$selectedStatus)
                                <span class="material-symbols-outlined godui-plus-icon">add</span>
                            @endif
                            <span class="godui-facet-label">Status</span>
                            @if($selectedStatus)
                                <span class="godui-pill-divider"></span>
                                <span class="godui-pill-summary">{{ $currentStatusLabel }}</span>
                            @endif
                        </button>
                        @if($selectedStatus)
                            <button type="button" class="godui-pill-clear-btn" onclick="clearFacet('filterStatus', event)" title="Clear Status">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        @endif
                    </div>
                    
                    <div class="godui-facet-popover" id="statusFacetMenu">
                        <div class="godui-popover-search-wrap">
                            <span class="material-symbols-outlined">search</span>
                            <input type="text" class="godui-popover-search" placeholder="Search status…" oninput="filterGoduiFacet(this)">
                        </div>
                        <ul class="godui-popover-list">
                            @foreach($statusOptions as $sKey => $sData)
                                <li class="godui-popover-item" onclick="selectGoduiFacetOption('filterStatus', '{{ $sKey }}')">
                                    <span class="godui-checkbox {{ $selectedStatus === $sKey ? 'checked' : '' }}">
                                        @if($selectedStatus === $sKey)
                                            <span class="material-symbols-outlined check-icon">check</span>
                                        @endif
                                    </span>
                                    <span class="godui-item-label">{{ $sData['label'] }}</span>
                                    @if(isset($sData['count']) && $sData['count'] > 0)
                                        <span class="godui-item-count">{{ $sData['count'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Facet: Rating -->
                @php
                    $selectedRating = request('min_rating');
                    $ratingOptions = [
                        '4.0' => ['label' => '4.0+ High Rating', 'summary' => '4.0+', 'count' => $stats['high_rating'] ?? 0],
                        '4.5' => ['label' => '4.5+ Top Rated', 'summary' => '4.5+', 'count' => $stats['high_rating'] ?? 0],
                    ];
                    $currentRatingLabel = $selectedRating && isset($ratingOptions[$selectedRating]) ? $ratingOptions[$selectedRating]['summary'] : null;
                @endphp
                <div class="godui-facet-wrapper" id="facetRatingWrapper">
                    <input type="hidden" id="filterRating" name="min_rating" value="{{ $selectedRating }}">
                    <div class="godui-facet-pill {{ $selectedRating ? 'has-selection' : '' }}">
                        <button type="button" class="godui-pill-btn" onclick="toggleGoduiFacet('ratingFacet', event)">
                            @if(!$selectedRating)
                                <span class="material-symbols-outlined godui-plus-icon">add</span>
                            @endif
                            <span class="godui-facet-label">Rating</span>
                            @if($selectedRating)
                                <span class="godui-pill-divider"></span>
                                <span class="godui-pill-summary">{{ $currentRatingLabel }}</span>
                            @endif
                        </button>
                        @if($selectedRating)
                            <button type="button" class="godui-pill-clear-btn" onclick="clearFacet('filterRating', event)" title="Clear Rating">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        @endif
                    </div>
                    
                    <div class="godui-facet-popover" id="ratingFacetMenu">
                        <div class="godui-popover-search-wrap">
                            <span class="material-symbols-outlined">search</span>
                            <input type="text" class="godui-popover-search" placeholder="Search rating…" oninput="filterGoduiFacet(this)">
                        </div>
                        <ul class="godui-popover-list">
                            @foreach($ratingOptions as $rKey => $rData)
                                <li class="godui-popover-item" onclick="selectGoduiFacetOption('filterRating', '{{ $rKey }}')">
                                    <span class="godui-checkbox {{ $selectedRating === $rKey ? 'checked' : '' }}">
                                        @if($selectedRating === $rKey)
                                            <span class="material-symbols-outlined check-icon">check</span>
                                        @endif
                                    </span>
                                    <span class="godui-item-label">{{ $rData['label'] }}</span>
                                    @if(isset($rData['count']) && $rData['count'] > 0)
                                        <span class="godui-item-count">{{ $rData['count'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if(request()->hasAny(['search', 'city', 'status', 'min_rating']))
                    <button type="button" class="godui-clear-all" onclick="clearLeadsFilter();">Clear all</button>
                @endif
            </div>
        </form>
    </div>

    <!-- Sticky / Floating Bulk Action Bar -->
    <div id="bulkActionBar" class="bulk-actions-toolbar">
        <div class="bulk-left">
            <span class="material-symbols-outlined" style="font-size: 20px; color: var(--lime-primary);">check_circle</span>
            <span id="selectedCountBadge" class="bulk-selected-badge">0</span>
            <span>leads selected</span>
        </div>
        <div class="bulk-right">
            <!-- shadcn/ui DropdownMenu: Bulk Status -->
            <div class="dropdown-menu-container" id="bulkStatusDropdownContainer">
                <input type="hidden" id="bulkStatusSelect" value="">
                <button type="button" class="dropdown-menu-trigger bulk-trigger-shadcn" onclick="toggleCustomDropdown('bulkStatusDropdown', event)">
                    <span class="dropdown-trigger-label" id="bulkStatusDropdownLabel">Update Status To...</span>
                    <span class="material-symbols-outlined dropdown-chevron">unfold_more</span>
                </button>
                <div class="dropdown-menu-content w-56 bulk-dropdown-content" id="bulkStatusDropdownMenu">
                    <div class="dropdown-menu-label">Target Pipeline Status</div>
                    <div class="dropdown-menu-group">
                        @foreach($statusMap as $sKey => $sVal)
                            <div class="dropdown-menu-item" onclick="selectBulkStatus('{{ $sKey }}', '{{ $sVal }}')">
                                <span class="item-left">
                                    <span class="status-indicator-dot dot-{{ $sKey }}"></span>
                                    <span>{{ $sVal }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <button type="button" class="btn-bulk-status" onclick="executeBulkAction('update_status')">
                <span class="material-symbols-outlined" style="font-size: 16px;">done_all</span> Apply Status
            </button>
            <button type="button" class="btn-bulk-delete" onclick="executeBulkAction('delete')">
                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span> Delete Selected
            </button>
            <button type="button" class="btn-bulk-cancel" onclick="deselectAllLeads()">
                <span class="material-symbols-outlined" style="font-size: 16px;">close</span> Deselect
            </button>
        </div>
    </div>

    <!-- Master Form for Bulk Actions -->
    <form id="bulkForm" method="POST" action="{{ route('leads.bulkAction') }}">
        @csrf
        <input type="hidden" name="bulk_action" id="bulkActionInput" value="">
        <input type="hidden" name="new_status" id="bulkNewStatusInput" value="">

        <div class="table-responsive-wrapper">
            <table class="leads-table">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAllCheckbox" class="custom-chk" title="Select All On This Page" onchange="toggleSelectAll(this)">
                        </th>
                        <th style="width: 20%;">Business Name</th>
                        <th style="width: 10%;">City</th>
                        <th style="width: 11%;">Category</th>
                        <th style="width: 9%;">Rating</th>
                        <th style="width: 12%;">Primary Phone</th>
                        <th style="width: 12%;">2nd Phone</th>
                        <th style="width: 14%;">Address</th>
                        <th style="width: 7%;">Status</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr id="row-lead-{{ $lead->id }}">
                            <td class="checkbox-cell">
                                <input type="checkbox" name="selected_ids[]" value="{{ $lead->id }}" class="custom-chk lead-checkbox" onchange="handleRowCheckboxChange(this, {{ $lead->id }})">
                            </td>
                            <td class="business-title-cell">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span>{{ $lead->name }}</span>
                                    @if($lead->website)
                                        <a href="{{ $lead->website }}" target="_blank" title="Visit Website" style="color: #0284c7;">
                                            <span class="material-symbols-outlined" style="font-size: 15px;">open_in_new</span>
                                        </a>
                                    @endif
                                </div>
                                @if($lead->notes)
                                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 3px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $lead->notes }}">
                                        📝 {{ $lead->notes }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 3px; font-weight: 700; font-size: 11px; color: var(--text-dark); background: #f1f4ec; padding: 4px 9px; border-radius: var(--radius-pill); border: 1px solid var(--border-subtle);">
                                    <span class="material-symbols-outlined" style="font-size: 13px; color: #15803d;">location_on</span>
                                    {{ $lead->city ?? 'Lahore' }}
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 12px; font-weight: 600;">{{ $lead->category ?? '—' }}</span>
                            </td>
                            <td>
                                @if($lead->rating)
                                    <div style="display: inline-flex; align-items: center; gap: 3px; font-weight: 700; color: #b45309;">
                                        <span class="material-symbols-outlined icon-fill" style="font-size: 16px; color: #d97706;">star</span>
                                        {{ number_format($lead->rating, 1) }}
                                        <span style="font-size: 11px; color: var(--text-light); font-weight: 500;">({{ $lead->reviews_count }})</span>
                                    </div>
                                @else
                                    <span style="color: var(--text-light);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->primary_phone)
                                    <a href="tel:{{ $lead->primary_phone }}" style="color: #0284c7; text-decoration: none; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <span class="material-symbols-outlined" style="font-size: 15px;">call</span>
                                        {{ $lead->primary_phone }}
                                    </a>
                                @else
                                    <span style="color: var(--text-light); font-size: 12px;">None</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->secondary_phone)
                                    <span style="color: #15803d; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <span class="material-symbols-outlined" style="font-size: 15px;">smartphone</span>
                                        {{ $lead->secondary_phone }}
                                    </span>
                                @else
                                    <span style="color: var(--text-light);">—</span>
                                @endif
                            </td>
                            <td style="max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 12px; color: var(--text-muted);" title="{{ $lead->address }}">
                                {{ $lead->address ?? '—' }}
                            </td>
                            <td>
                                <span class="status-pill status-{{ $lead->status }}">{{ str_replace('_', ' ', $lead->status) }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <button type="button" class="btn-edit-lead" title="Edit Lead" onclick="openEditModal({{ $lead->id }}, '{{ addslashes($lead->name) }}', '{{ addslashes($lead->secondary_phone ?? '') }}', '{{ addslashes($lead->notes ?? '') }}', '{{ $lead->status }}')">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">edit</span>
                                        Edit
                                    </button>
                                    <button type="button" class="btn-delete-single" title="Delete Lead" onclick="deleteSingleLead({{ $lead->id }}, '{{ addslashes($lead->name) }}')">
                                        <span class="material-symbols-outlined" style="font-size: 15px;">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                    <span class="material-symbols-outlined" style="font-size: 40px; color: var(--text-light);">travel_explore</span>
                                    <span>No leads matched your query. Click <strong>Run Scraper</strong> above to extract fresh listings.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- Custom Next.js Style AJAX Pagination Bar -->
    <div class="custom-pagination-bar">
        <div class="pagination-info">
            Showing <strong>{{ $leads->firstItem() ?? 0 }}</strong> to <strong>{{ $leads->lastItem() ?? 0 }}</strong> of <strong>{{ $leads->total() }}</strong> total leads
        </div>

        <div class="pagination-nav">
            @if($leads->onFirstPage())
                <span class="pagination-link disabled">
                    <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span> Previous
                </span>
            @else
                <button type="button" class="pagination-link" onclick="loadLeadsPage('{{ $leads->previousPageUrl() }}')">
                    <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span> Previous
                </button>
            @endif

            <span style="font-size: 12px; font-weight: 700; padding: 6px 12px; background: #eaf6fd; color: #0284c7; border-radius: var(--radius-pill);">
                Page {{ $leads->currentPage() }} of {{ $leads->lastPage() }}
            </span>

            @if($leads->hasMorePages())
                <button type="button" class="pagination-link" onclick="loadLeadsPage('{{ $leads->nextPageUrl() }}')">
                    Next <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
                </button>
            @else
                <span class="pagination-link disabled">
                    Next <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
                </span>
            @endif
        </div>
    </div>

</div>
