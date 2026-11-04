<div class="top-navbar">
    <div class="user-greeting">
        <div class="user-avatar">{{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'AD' }}</div>
        <div>
            <h2>Hello, {{ Auth::check() ? Auth::user()->name : 'Admin' }}</h2>
            <p>{{ Auth::check() ? 'Executive Administrator' : 'Google Maps Lead Engine' }}</p>
        </div>
    </div>

    <!-- Center Capsule Nav -->
    <div class="nav-pill-group">
        <a href="{{ route('dashboard') }}" class="nav-pill-item active">
            <span class="material-symbols-outlined" style="font-size: 18px;">dashboard</span>
            Overview
        </a>
        <a href="#leadsTableSection" class="nav-pill-item">
            <span class="material-symbols-outlined" style="font-size: 18px;">group</span>
            Leads
        </a>
        <a href="javascript:void(0)" onclick="openScraperModal()" class="nav-pill-item">
            <span class="material-symbols-outlined" style="font-size: 18px;">bolt</span>
            Scraper
        </a>
        <a href="/api/leads/stats" target="_blank" class="nav-pill-item">
            <span class="material-symbols-outlined" style="font-size: 18px;">api</span>
            API Feed
        </a>
    </div>

    <!-- Right Quick Actions & Authentication -->
    <div class="nav-actions">
        @auth
            <div class="auth-user-pill">
                <span class="material-symbols-outlined" style="font-size: 18px; color: var(--lime-dark);">shield_person</span>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-dark);">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout" title="Sign Out">
                        <span class="material-symbols-outlined" style="font-size: 16px;">logout</span>
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-auth-login">
                <span class="material-symbols-outlined" style="font-size: 17px;">login</span>
                Sign In
            </a>
            <a href="{{ route('register') }}" class="btn-auth-signup">
                <span class="material-symbols-outlined" style="font-size: 17px;">person_add</span>
                Create Admin
            </a>
        @endauth
    </div>
</div>
