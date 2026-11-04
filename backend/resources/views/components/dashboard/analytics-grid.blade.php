<div class="bottom-grid">

    <!-- Card 1: Pipeline Donut Ring -->
    <div class="detail-card">
        <div class="card-header-row">
            <div class="card-title-icon">
                <span class="material-symbols-outlined" style="color: var(--lime-dark); font-size: 20px;">pie_chart</span>
                Pipeline Health
            </div>
            <span class="card-subtag">Real-Time</span>
        </div>

        <div class="donut-container">
            <svg width="130" height="130" viewBox="0 0 42 42">
                <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#edf1e8" stroke-width="4.5"></circle>
                <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#a3f23a" stroke-width="4.5"
                        stroke-dasharray="{{ $stats['total'] > 0 ? round(($stats['contacted'] + $stats['interested'] + $stats['closed']) / $stats['total'] * 100) : 10 }} {{ $stats['total'] > 0 ? 100 - round(($stats['contacted'] + $stats['interested'] + $stats['closed']) / $stats['total'] * 100) : 90 }}"
                        stroke-dashoffset="25"></circle>
            </svg>
            <div class="donut-inner-text" style="position: absolute;">
                <h3>{{ $stats['total'] > 0 ? round(($stats['contacted'] + $stats['interested'] + $stats['closed']) / $stats['total'] * 100) : 0 }}%</h3>
                <span>Actioned</span>
            </div>
        </div>

        <div class="donut-highlight-text">
            • <strong>{{ $stats['contacted'] + $stats['interested'] }}</strong> active leads in conversion flow
        </div>

        <div class="legend-grid">
            <div class="legend-item">
                <div class="legend-left">
                    <span class="legend-dot" style="background: #a3f23a;"></span>
                    <span>New</span>
                </div>
                <span class="legend-val">{{ $stats['new'] }}</span>
            </div>
            <div class="legend-item">
                <div class="legend-left">
                    <span class="legend-dot" style="background: #17320c;"></span>
                    <span>Contacted</span>
                </div>
                <span class="legend-val">{{ $stats['contacted'] }}</span>
            </div>
            <div class="legend-item">
                <div class="legend-left">
                    <span class="legend-dot" style="background: #0284c7;"></span>
                    <span>Interested</span>
                </div>
                <span class="legend-val">{{ $stats['interested'] }}</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Top Business Categories -->
    <div class="detail-card">
        <div class="card-header-row">
            <div class="card-title-icon">
                <span class="material-symbols-outlined" style="color: var(--lime-dark); font-size: 20px;">category</span>
                Top Categories
            </div>
            <span class="card-subtag">Distribution</span>
        </div>

        <div class="category-bars">
            @forelse($topCategories as $cat)
                <div>
                    <div class="cat-row-info">
                        <span class="cat-name">{{ Str::limit($cat->category ?? 'General Business', 22) }}</span>
                        <span class="cat-count">{{ $cat->count }}</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $stats['total'] > 0 ? min(100, round(($cat->count / $stats['total']) * 100)) : 0 }}%;"></div>
                    </div>
                </div>
            @empty
                <div style="font-size: 13px; color: var(--text-muted); padding: 18px 0; text-align: center;">
                    No categorized leads recorded yet.
                </div>
            @endforelse
        </div>

        <div style="font-size: 11px; color: var(--text-muted); text-align: right; padding-top: 10px; border-top: 1px solid #edf1e8;">
            Total Categories: <strong>{{ count($categories) }}</strong>
        </div>
    </div>

    <!-- Card 3: Live Incoming Stream -->
    <div class="detail-card">
        <div class="card-header-row">
            <div class="card-title-icon">
                <span class="material-symbols-outlined" style="color: var(--lime-dark); font-size: 20px;">history</span>
                Recent Activity
            </div>
            <span class="card-subtag">Latest</span>
        </div>

        <div class="stream-list">
            @php $colors = ['#1a3311', '#c77838', '#0284c7', '#059669']; @endphp
            @forelse($recentLeads as $idx => $rLead)
                <div class="stream-item">
                    <div class="stream-left">
                        <div class="stream-avatar" style="background: {{ $colors[$idx % 4] }};">
                            {{ strtoupper(substr($rLead->name, 0, 1)) }}
                        </div>
                        <div class="stream-info">
                            <h4>{{ $rLead->name }}</h4>
                            <p>{{ $rLead->category ?? 'Google Maps Lead' }}</p>
                        </div>
                    </div>
                    <div class="stream-right">
                        <div class="stream-score">
                            @if($rLead->rating)
                                <span class="material-symbols-outlined icon-fill" style="font-size: 15px; color: #b45309;">star</span>
                                {{ number_format($rLead->rating, 1) }}
                            @else
                                <span style="font-size: 11px; color: var(--text-muted);">—</span>
                            @endif
                        </div>
                        <div style="font-size: 11px; color: var(--text-muted); display: flex; align-items: center; justify-content: flex-end; gap: 3px;">
                            <span class="material-symbols-outlined" style="font-size: 13px;">call</span>
                            {{ $rLead->primary_phone ? 'Phone' : 'No Phone' }}
                        </div>
                    </div>
                </div>
            @empty
                <div style="font-size: 13px; color: var(--text-muted); padding: 18px 0; text-align: center;">
                    No recent leads stream.
                </div>
            @endforelse
        </div>
    </div>

</div>
