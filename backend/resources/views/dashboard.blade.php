<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadRadar - Executive Lead Management Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Pure Google Material Symbols Outlined -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        :root {
            --bg-outer: #e9ece4;
            --bg-canvas: #f6f8f2;
            --card-white: #ffffff;
            --border-subtle: #e3e7dc;
            --border-card: #e8ece2;
            
            /* Lime FinTech Palette */
            --lime-primary: #a3f23a;
            --lime-hover: #92e02e;
            --lime-dark: #19380d;
            --lime-surface: #edf8d8;
            --dark-card: #151e12;
            
            --text-dark: #12180e;
            --text-muted: #737c6c;
            --text-light: #9ba494;
            
            --radius-canvas: 34px;
            --radius-card: 26px;
            --radius-pill: 9999px;
            --radius-input: 14px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-outer);
            color: var(--text-dark);
            min-height: 100vh;
            padding: 24px 20px;
            display: flex;
            justify-content: center;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .icon-fill {
            font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }

        .dashboard-wrapper {
            width: 100%;
            max-width: 1420px;
            background: var(--bg-canvas);
            border-radius: var(--radius-canvas);
            padding: 28px 32px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.05), 0 2px 10px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.7);
            position: relative;
        }

        /* 1. Top Navigation Header */
        .top-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .user-greeting {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #d89665;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(216, 150, 101, 0.3);
            flex-shrink: 0;
        }

        .user-greeting h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.4px;
            line-height: 1.2;
        }

        .user-greeting p {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .nav-pill-group {
            display: flex;
            align-items: center;
            background: var(--card-white);
            padding: 5px;
            border-radius: var(--radius-pill);
            border: 1px solid var(--border-subtle);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .nav-pill-item {
            padding: 8px 18px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: var(--text-muted);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-pill-item.active {
            background: var(--lime-primary);
            color: var(--lime-dark);
            font-weight: 700;
        }

        .nav-pill-item:hover:not(.active) {
            color: var(--text-dark);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .auth-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--card-white);
            border: 1px solid var(--border-subtle);
            padding: 5px 8px 5px 14px;
            border-radius: var(--radius-pill);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .btn-logout {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f4f6ef;
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #b91c1c;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            transform: scale(1.05);
        }

        .btn-auth-login {
            background: var(--card-white);
            color: var(--text-dark);
            border: 1px solid var(--border-subtle);
            padding: 8px 18px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-auth-login:hover { background: #f4f6ef; }

        .btn-auth-signup {
            background: var(--lime-primary);
            color: var(--lime-dark);
            border: none;
            padding: 8px 18px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-auth-signup:hover {
            background: var(--lime-hover);
            transform: translateY(-1px);
        }

        /* 2. Top Hero Row */
        .hero-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
            margin-bottom: 24px;
        }

        @media (max-width: 992px) {
            .hero-row { grid-template-columns: 1fr; }
        }

        .hero-card-white {
            background: var(--card-white);
            border-radius: var(--radius-card);
            padding: 28px 30px;
            border: 1px solid var(--border-card);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hero-top-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .currency-pills {
            display: flex;
            gap: 6px;
            background: #f1f4ec;
            padding: 3px;
            border-radius: var(--radius-pill);
        }

        .curr-pill {
            padding: 5px 12px;
            border-radius: var(--radius-pill);
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-decoration: none;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .curr-pill.active {
            background: var(--card-white);
            color: var(--text-dark);
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
        }

        .account-num {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .balance-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 6px;
        }

        .balance-amount {
            font-size: 42px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 26px;
        }

        .hero-action-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-pill-dark {
            background: var(--dark-card);
            color: #ffffff;
            padding: 12px 22px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-pill-dark:hover {
            background: #23311f;
            transform: translateY(-1px);
        }

        .btn-pill-light {
            background: #ffffff;
            color: var(--text-dark);
            border: 1px solid var(--border-subtle);
            padding: 12px 22px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-pill-light:hover { background: #f6f8f2; }

        .circle-action-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--lime-primary);
            color: var(--lime-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            margin-left: auto;
            transition: all 0.2s;
            text-decoration: none;
        }

        .circle-action-btn:hover {
            background: var(--lime-hover);
            transform: scale(1.05);
        }

        .hero-card-lime {
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.45), transparent 70%), linear-gradient(135deg, #a4f33d 0%, #87df24 100%);
            border-radius: var(--radius-card);
            padding: 28px 30px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 10px 30px rgba(136, 223, 36, 0.2);
        }

        .lime-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .priority-badge {
            background: var(--lime-dark);
            color: var(--lime-primary);
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .rewards-label {
            font-size: 13px;
            color: #274b17;
            font-weight: 600;
            margin-bottom: 6px;
            position: relative;
            z-index: 2;
        }

        .rewards-amount {
            font-size: 42px;
            font-weight: 800;
            color: var(--lime-dark);
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 26px;
            position: relative;
            z-index: 2;
        }

        .growth-badge {
            font-size: 12px;
            background: rgba(255, 255, 255, 0.7);
            color: var(--lime-dark);
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            font-weight: 700;
        }

        .lime-sub-pills {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .lime-pill-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(10px);
            padding: 14px 18px;
            border-radius: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lime-pill-card span {
            font-size: 12px;
            font-weight: 700;
            color: var(--lime-dark);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .lime-pill-card strong {
            font-size: 15px;
            color: var(--lime-dark);
            font-weight: 800;
        }

        /* 3. Bottom Row: 3 Detail Cards */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr;
            gap: 22px;
            margin-bottom: 24px;
        }

        @media (max-width: 1080px) {
            .bottom-grid { grid-template-columns: 1fr; }
        }

        .detail-card {
            background: var(--card-white);
            border-radius: var(--radius-card);
            padding: 24px;
            border: 1px solid var(--border-card);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .card-title-icon {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-subtag {
            font-size: 11px;
            color: var(--text-muted);
            background: #f4f6ef;
            padding: 3px 8px;
            border-radius: var(--radius-pill);
            font-weight: 600;
        }

        .donut-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 10px 0 16px;
        }

        .donut-inner-text { text-align: center; }

        .donut-inner-text h3 {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -1px;
            line-height: 1;
        }

        .donut-inner-text span {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .donut-highlight-text {
            font-size: 12px;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 16px;
        }

        .legend-grid {
            display: flex;
            justify-content: space-around;
            border-top: 1px solid #edf1e8;
            padding-top: 14px;
        }

        .legend-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .legend-left {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .legend-val {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .category-bars {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin: 6px 0;
        }

        .cat-row-info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .cat-name { color: var(--text-muted); }
        .cat-count { color: var(--text-dark); font-weight: 700; }

        .progress-track {
            width: 100%;
            height: 6px;
            background: #edf1e8;
            border-radius: var(--radius-pill);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: var(--radius-pill);
            background: var(--lime-primary);
        }

        .stream-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stream-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid #f6f8f2;
        }

        .stream-item:last-child { border-bottom: none; }

        .stream-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stream-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: #ffffff;
            flex-shrink: 0;
        }

        .stream-info h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2px;
            max-width: 170px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stream-info p {
            font-size: 11px;
            color: var(--text-muted);
        }

        .stream-right { text-align: right; }

        .stream-score {
            font-size: 12px;
            font-weight: 700;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 3px;
        }

        /* 4. Leads Management Data Table Card */
        .leads-table-card {
            background: var(--card-white);
            border-radius: var(--radius-card);
            padding: 26px;
            border: 1px solid var(--border-card);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
            margin-top: 6px;
            position: relative;
            transition: opacity 0.2s ease;
        }

        .leads-table-card.loading tbody {
            opacity: 0.55;
            transition: opacity 150ms ease;
        }

        .table-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: nowrap;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
            flex-shrink: 0;
        }

        .search-box-wrapper {
            position: relative;
            width: 250px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        .search-icon-inside {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            pointer-events: none;
            font-size: 18px;
        }

        .search-box {
            width: 100%;
            height: 38px;
            background: #f4f6ef;
            border: 1px solid var(--border-subtle);
            padding: 0 16px 0 40px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            outline: none;
            color: var(--text-dark);
            transition: all 0.2s;
        }

        .search-box:focus {
            background: #ffffff;
            border-color: var(--lime-dark);
        }

        .select-filter {
            height: 38px;
            background: #f4f6ef;
            border: 1px solid var(--border-subtle);
            padding: 0 16px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
            outline: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        /* ==========================================================================
           GodUI FilterBar (@godui/filter-bar)
           ========================================================================== */
        .godui-filter-bar {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 8px;
            white-space: nowrap;
        }

        .godui-facet-wrapper {
            position: relative;
            display: inline-block;
        }

        /* Pill Container */
        .godui-facet-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            transition: all 150ms ease;
            user-select: none;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
        }

        .godui-facet-pill:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .godui-facet-pill.has-selection {
            border: 1px solid rgba(15, 23, 42, 0.15);
            background: #f1f5f9;
        }

        .godui-facet-pill.has-selection:hover {
            background: #e2e8f0;
        }

        .godui-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 500;
            background: transparent;
            border: none;
            cursor: pointer;
            outline: none;
            color: #64748b;
            border-radius: 9999px;
        }

        .godui-facet-pill.has-selection .godui-pill-btn {
            padding-right: 4px;
            color: #0f172a;
        }

        .godui-plus-icon {
            font-size: 15px;
            color: #64748b;
            display: inline-flex;
            align-items: center;
        }

        .godui-facet-label {
            font-weight: 500;
            color: inherit;
        }

        .godui-pill-divider {
            height: 14px;
            width: 1px;
            background: #cbd5e1;
            margin: 0 4px;
            display: inline-block;
        }

        .godui-pill-summary {
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
        }

        .godui-pill-clear-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 9999px;
            background: transparent;
            border: none;
            cursor: pointer;
            margin-right: 6px;
            color: #64748b;
            transition: all 150ms ease;
        }

        .godui-pill-clear-btn:hover {
            background: rgba(15, 23, 42, 0.1);
            color: #0f172a;
        }

        .godui-pill-clear-btn .material-symbols-outlined {
            font-size: 13px;
            font-weight: 700;
        }

        /* Popover Menu */
        .godui-facet-popover {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            width: 240px;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            padding: 6px;
            z-index: 1000;
            animation: goduiPop 150ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .godui-facet-popover.active {
            display: block;
        }

        @keyframes goduiPop {
            from { opacity: 0; transform: scale(0.96) translateY(-4px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Popover Search */
        .godui-popover-search-wrap {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }

        .godui-popover-search-wrap .material-symbols-outlined {
            position: absolute;
            left: 8px;
            font-size: 16px;
            color: #94a3b8;
            pointer-events: none;
        }

        .godui-popover-search {
            width: 100%;
            height: 32px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 10px 0 28px;
            font-size: 12px;
            color: #0f172a;
            outline: none;
            transition: all 0.15s ease;
        }

        .godui-popover-search:focus {
            background: #ffffff;
            border-color: #0f172a;
        }

        /* Popover Options List */
        .godui-popover-list {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 220px;
            overflow-y: auto;
        }

        .godui-popover-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 120ms ease;
            user-select: none;
        }

        .godui-popover-item:hover {
            background: #f1f5f9;
        }

        /* Square Checkbox */
        .godui-checkbox {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 120ms ease;
            flex-shrink: 0;
            background: #ffffff;
        }

        .godui-checkbox.checked {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
        }

        .godui-checkbox .check-icon {
            font-size: 13px;
            font-weight: 800;
        }

        .godui-item-label {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
        }

        .godui-item-count {
            font-size: 11px;
            color: #94a3b8;
            font-variant-numeric: tabular-nums;
            font-weight: 500;
        }

        /* Clear all button */
        .godui-clear-all {
            background: transparent;
            border: none;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color 150ms ease;
        }

        .godui-clear-all:hover {
            color: #0f172a;
        }

        /* ==========================================================================
           shadcn/ui DropdownMenu Design System
           ========================================================================== */
        .dropdown-menu-container,
        .custom-dropdown {
            position: relative;
            display: inline-block;
        }

        /* DropdownMenuTrigger: Clean Outline Button */
        .dropdown-menu-trigger,
        .custom-dropdown-trigger {
            height: 38px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 0 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
            outline: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.15s ease;
            user-select: none;
            white-space: nowrap;
        }

        .dropdown-menu-trigger:hover,
        .custom-dropdown-trigger:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .dropdown-menu-trigger.active,
        .dropdown-menu-trigger:focus-visible,
        .custom-dropdown-trigger.active,
        .custom-dropdown-trigger:focus-visible {
            background: #ffffff;
            border-color: #0f172a;
            box-shadow: 0 0 0 1px #0f172a;
        }

        .dropdown-trigger-icon {
            font-size: 16px;
            color: #64748b;
        }

        .dropdown-trigger-label {
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
        }

        .dropdown-chevron {
            font-size: 16px;
            color: #64748b;
            transition: transform 0.15s ease;
            margin-left: 2px;
        }

        .dropdown-menu-trigger.active .dropdown-chevron,
        .custom-dropdown-trigger.active .dropdown-chevron {
            transform: rotate(180deg);
        }

        /* DropdownMenuContent: Clean Floating Container */
        .dropdown-menu-content,
        .custom-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            min-width: 14rem; /* 224px (w-56) */
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            padding: 4px;
            z-index: 1000;
            animation: shadcnDropdownIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dropdown-menu-content.active,
        .custom-dropdown-menu.active {
            display: block;
        }

        @keyframes shadcnDropdownIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .dropdown-scroll-group {
            max-height: 200px;
            overflow-y: auto;
        }

        /* DropdownMenuLabel: Section title */
        .dropdown-menu-label {
            padding: 6px 8px 4px 8px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* DropdownMenuSeparator: 1px subtle divider */
        .dropdown-menu-separator {
            height: 1px;
            background: #f1f5f9;
            margin: 4px -4px;
        }

        /* DropdownMenuGroup */
        .dropdown-menu-group {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        /* DropdownMenuItem: Crisp item with hover state */
        .dropdown-menu-item,
        .custom-dropdown-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
            cursor: pointer;
            transition: background-color 0.12s ease, color 0.12s ease;
            user-select: none;
        }

        .dropdown-menu-item:hover,
        .custom-dropdown-option:hover {
            background: #f1f5f9; /* bg-accent */
            color: #0f172a;
        }

        .dropdown-menu-item.selected,
        .custom-dropdown-option.selected {
            background: #f8fafc;
            font-weight: 600;
        }

        .dropdown-menu-item .item-left,
        .custom-dropdown-option .option-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-menu-item .item-icon,
        .custom-dropdown-option .option-icon {
            font-size: 16px;
            color: #64748b;
        }

        .dropdown-menu-item .item-check,
        .custom-dropdown-option .checkmark {
            font-size: 16px;
            color: #0f172a;
            font-weight: 700;
        }

        /* Status Indicator Dot */
        .status-indicator-dot,
        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .dot-all { background: #94a3b8; }
        .dot-new { background: #16a34a; }
        .dot-contacted { background: #0284c7; }
        .dot-interested { background: #8b5cf6; }
        .dot-not_interested { background: #ef4444; }
        .dot-closed { background: #64748b; }

        /* Bulk Action Bar shadcn trigger */
        .bulk-trigger-shadcn,
        .bulk-status-trigger {
            height: 34px !important;
            padding: 0 12px !important;
            background: rgba(255, 255, 255, 0.12) !important;
            border: 1px solid rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }
        .bulk-trigger-shadcn:hover,
        .bulk-trigger-shadcn.active,
        .bulk-status-trigger:hover,
        .bulk-status-trigger.active {
            background: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.35) !important;
            box-shadow: none !important;
        }
        .bulk-trigger-shadcn .dropdown-trigger-label,
        .bulk-status-trigger .dropdown-trigger-label {
            color: #ffffff !important;
        }
        .bulk-trigger-shadcn .dropdown-chevron,
        .bulk-status-trigger .dropdown-chevron {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        .bulk-dropdown-content,
        .bulk-status-menu {
            bottom: calc(100% + 6px);
            top: auto;
        }

        .btn-filter-clear {
            height: 40px;
            padding: 0 16px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 700;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-filter-clear:hover {
            background: #fca5a5;
            color: #7f1d1d;
        }

        /* Floating Bulk Actions Bar */
        .bulk-actions-toolbar {
            display: none;
            position: sticky;
            top: 20px;
            z-index: 50;
            background: var(--dark-card);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: var(--radius-pill);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            margin-bottom: 18px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            animation: slideDown 0.2s ease-out;
        }

        .bulk-actions-toolbar.active {
            display: flex;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bulk-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .bulk-selected-badge {
            background: var(--lime-primary);
            color: var(--lime-dark);
            padding: 3px 10px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 800;
        }

        .bulk-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .bulk-select-status {
            height: 34px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            padding: 0 12px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        .bulk-select-status option {
            background: #1e291c;
            color: #ffffff;
        }

        .btn-bulk-status {
            height: 34px;
            padding: 0 14px;
            border-radius: var(--radius-pill);
            background: var(--lime-primary);
            color: var(--lime-dark);
            border: none;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .btn-bulk-status:hover { background: var(--lime-hover); }

        .btn-bulk-delete {
            height: 34px;
            padding: 0 14px;
            border-radius: var(--radius-pill);
            background: #ef4444;
            color: #ffffff;
            border: none;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .btn-bulk-delete:hover { background: #dc2626; }

        .btn-bulk-cancel {
            height: 34px;
            padding: 0 12px;
            border-radius: var(--radius-pill);
            background: transparent;
            color: #9ba494;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-bulk-cancel:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Leads Table Alignment */
        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #edf1e8;
        }

        .leads-table {
            width: 100%;
            min-width: 1080px;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        .leads-table th {
            padding: 14px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            background: #fafbf8;
            border-bottom: 2px solid #edf1e8;
            vertical-align: middle;
            white-space: nowrap;
        }

        .leads-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f2f5ed;
            vertical-align: middle;
            color: var(--text-dark);
            background: #ffffff;
        }

        .leads-table tr:hover td { background: #f8faf4; }
        .leads-table tr.selected td { background: #f2fadc; }

        .checkbox-cell {
            width: 44px;
            text-align: center;
        }

        .custom-chk {
            width: 16px;
            height: 16px;
            accent-color: var(--lime-dark);
            cursor: pointer;
            vertical-align: middle;
        }

        .business-title-cell {
            font-weight: 700;
            max-width: 220px;
        }

        .business-title-cell a {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-new { background: #eaf6fd; color: #0284c7; }
        .status-contacted { background: #fef3c7; color: #d97706; }
        .status-interested { background: #dcfce7; color: #15803d; }
        .status-not_interested { background: #fee2e2; color: #b91c1c; }
        .status-closed { background: #f1f5f9; color: #64748b; }

        .btn-edit-lead {
            background: #f1f4ec;
            color: var(--text-dark);
            border: 1px solid var(--border-subtle);
            padding: 6px 10px;
            border-radius: var(--radius-pill);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-edit-lead:hover {
            background: var(--lime-primary);
            color: var(--lime-dark);
            border-color: var(--lime-primary);
        }

        .btn-delete-single {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-delete-single:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        /* 5. Custom Clean Pagination */
        .custom-pagination-bar {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #edf1e8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .pagination-info {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .pagination-nav {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pagination-link {
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
            background: #f4f6ef;
            border: 1px solid var(--border-subtle);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pagination-link:hover:not(.disabled) {
            background: var(--lime-primary);
            color: var(--lime-dark);
            border-color: var(--lime-primary);
        }

        .pagination-link.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* 6. Modals */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(18, 24, 14, 0.45);
            backdrop-filter: blur(6px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-backdrop.active { display: flex; }

        .modal-dialog-box {
            background: var(--card-white);
            border-radius: 28px;
            width: 100%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-subtle);
            animation: modalFade 0.2s ease-out;
        }

        @keyframes modalFade {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .modal-title-row h3 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-close-btn {
            background: #f1f4ec;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-dark);
        }

        .form-group-item { margin-bottom: 16px; }

        .form-label-item {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .form-control-item {
            width: 100%;
            background: #f5f7f0;
            border: 1px solid var(--border-subtle);
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            color: var(--text-dark);
            outline: none;
        }

        .form-control-item:focus {
            background: #ffffff;
            border-color: var(--lime-dark);
        }

        .btn-submit-scrape {
            width: 100%;
            background: var(--lime-primary);
            color: var(--lime-dark);
            border: none;
            padding: 14px;
            border-radius: var(--radius-pill);
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit-scrape:hover { background: var(--lime-hover); }

        /* ==========================================================================
           shadcn / Sonner Toast Notification System
           ========================================================================== */
        .sonner-toaster {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 999999;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            pointer-events: none;
            max-width: 100%;
        }

        .sonner-toast {
            position: relative;
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 320px;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            font-family: inherit;
            font-size: 13px;
            color: #0f172a;
            line-height: 1.45;
            animation: sonnerSlideIn 200ms cubic-bezier(0.16, 1, 0.3, 1);
            transition: all 200ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sonner-toast.dismissing {
            opacity: 0;
            transform: translateY(10px) scale(0.95);
            pointer-events: none;
        }

        @keyframes sonnerSlideIn {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .sonner-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .sonner-icon .material-symbols-outlined {
            font-size: 20px;
        }

        .sonner-icon-default { color: #64748b; }
        .sonner-icon-success { color: #10b981; }
        .sonner-icon-info { color: #3b82f6; }
        .sonner-icon-warning { color: #f59e0b; }
        .sonner-icon-error { color: #ef4444; }
        .sonner-icon-loading { color: #0284c7; }

        .sonner-content {
            flex: 1;
            min-width: 0;
        }

        .sonner-title {
            font-weight: 600;
            font-size: 13px;
            color: #0f172a;
            word-break: break-word;
        }

        .sonner-description {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
            word-break: break-word;
        }

        .sonner-close-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 150ms ease, background 150ms ease;
            margin-top: 1px;
        }

        .sonner-close-btn:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .sonner-spin {
            animation: sonnerSpin 800ms linear infinite;
            display: inline-block;
        }

        @keyframes sonnerSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* In-App Action Confirmation Modal Styles */
        .confirm-dialog-box {
            max-width: 420px;
            text-align: center;
            padding: 34px 28px;
        }

        .confirm-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 18px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirm-icon-circle.icon-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .confirm-icon-circle.icon-warning {
            background: #fef3c7;
            color: #d97706;
        }

        .confirm-icon-circle.icon-primary {
            background: #ecfccb;
            color: var(--lime-dark);
        }

        .confirm-icon-circle .material-symbols-outlined {
            font-size: 32px;
        }

        .confirm-modal-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .confirm-modal-message {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 24px;
        }

        .confirm-modal-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-confirm-cancel {
            padding: 10px 22px;
            border-radius: var(--radius-pill);
            background: #f4f6ef;
            border: 1px solid var(--border-subtle);
            font-weight: 700;
            font-size: 13px;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-confirm-cancel:hover {
            background: #e6eae0;
        }

        .btn-confirm-proceed {
            padding: 10px 24px;
            border-radius: var(--radius-pill);
            font-weight: 700;
            font-size: 13px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-confirm-proceed.btn-danger {
            background: #dc2626;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25);
        }

        .btn-confirm-proceed.btn-danger:hover {
            background: #b91c1c;
        }

        .btn-confirm-proceed.btn-warning {
            background: #d97706;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.25);
        }

        .btn-confirm-proceed.btn-warning:hover {
            background: #b45309;
        }

        /* Animation */
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .spin-animation { animation: spin 1s linear infinite; }
    </style>
</head>
<body>

<div class="dashboard-wrapper">

    <!-- 1. Top Navbar Component -->
    @include('components.dashboard.navbar')

    <!-- 2. Hero Statistics Section Component -->
    @include('components.dashboard.hero-cards')

    <!-- 3. Bottom Analytics Grid Component -->
    @include('components.dashboard.analytics-grid')

    <!-- 4. Leads Management Data Table Component Container (Seamless Next.js SPA Swap) -->
    <div id="leadsTableContainer">
        @include('components.dashboard.leads-table')
    </div>

</div>

<!-- Sonner Toaster Floating Container -->
<div id="sonnerToaster" class="sonner-toaster" aria-live="polite"></div>

<!-- 5. Modals & Hidden Forms Component -->
@include('components.dashboard.modals')

<script>
    // Client-side CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let debounceTimer = null;

    // =========================================================================
    // shadcn / Sonner Toast Notification Engine
    // =========================================================================
    const toast = (function() {
        let toastCounter = 0;

        const icons = {
            default: '<span class="material-symbols-outlined sonner-icon-default">chat_bubble</span>',
            success: '<span class="material-symbols-outlined sonner-icon-success">check_circle</span>',
            info: '<span class="material-symbols-outlined sonner-icon-info">info</span>',
            warning: '<span class="material-symbols-outlined sonner-icon-warning">warning</span>',
            error: '<span class="material-symbols-outlined sonner-icon-error">cancel</span>',
            loading: '<span class="material-symbols-outlined sonner-icon-loading sonner-spin">progress_activity</span>'
        };

        function getContainer() {
            let container = document.getElementById('sonnerToaster');
            if (!container) {
                container = document.createElement('div');
                container.id = 'sonnerToaster';
                container.className = 'sonner-toaster';
                document.body.appendChild(container);
            }
            return container;
        }

        function createToastElement(id, type, message, options = {}) {
            const toastEl = document.createElement('div');
            toastEl.className = 'sonner-toast';
            toastEl.id = `toast-${id}`;

            const iconHtml = icons[type] || icons.default;
            const descriptionHtml = options.description ? `<div class="sonner-description">${options.description}</div>` : '';

            toastEl.innerHTML = `
                <div class="sonner-icon">${iconHtml}</div>
                <div class="sonner-content">
                    <div class="sonner-title">${message}</div>
                    ${descriptionHtml}
                </div>
                <button type="button" class="sonner-close-btn" onclick="toast.dismiss('${id}')" title="Close">
                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                </button>
            `;

            return toastEl;
        }

        function spawn(type, message, options = {}) {
            const id = options.id || String(++toastCounter);
            const container = getContainer();

            const toastEl = createToastElement(id, type, message, options);
            container.appendChild(toastEl);

            const duration = options.duration !== undefined ? options.duration : 4000;
            if (duration > 0) {
                setTimeout(() => {
                    dismiss(id);
                }, duration);
            }

            return id;
        }

        function update(id, type, message, options = {}) {
            const toastEl = document.getElementById(`toast-${id}`);
            if (!toastEl) {
                return spawn(type, message, { ...options, id });
            }

            const iconBox = toastEl.querySelector('.sonner-icon');
            const titleBox = toastEl.querySelector('.sonner-title');
            const descBox = toastEl.querySelector('.sonner-description');

            if (iconBox) iconBox.innerHTML = icons[type] || icons.default;
            if (titleBox) titleBox.innerHTML = message;

            if (options.description) {
                if (descBox) {
                    descBox.innerHTML = options.description;
                } else {
                    const newDesc = document.createElement('div');
                    newDesc.className = 'sonner-description';
                    newDesc.innerHTML = options.description;
                    toastEl.querySelector('.sonner-content').appendChild(newDesc);
                }
            }

            const duration = options.duration !== undefined ? options.duration : 4500;
            if (duration > 0) {
                setTimeout(() => {
                    dismiss(id);
                }, duration);
            }
        }

        function dismiss(id) {
            const toastEl = document.getElementById(`toast-${id}`);
            if (toastEl && !toastEl.classList.contains('dismissing')) {
                toastEl.classList.add('dismissing');
                setTimeout(() => {
                    if (toastEl.parentElement) toastEl.remove();
                }, 200);
            }
        }

        function fn(message, options) {
            return spawn('default', message, options);
        }

        fn.success = (message, options) => spawn('success', message, options);
        fn.info = (message, options) => spawn('info', message, options);
        fn.warning = (message, options) => spawn('warning', message, options);
        fn.error = (message, options) => spawn('error', message, options);

        fn.promise = function(promiseOrFn, { loading, success, error, ...options } = {}) {
            const id = String(++toastCounter);
            spawn('loading', loading || 'Loading...', { ...options, id, duration: 0 });

            const p = typeof promiseOrFn === 'function' ? promiseOrFn() : promiseOrFn;

            return Promise.resolve(p)
                .then((data) => {
                    const successMsg = typeof success === 'function' ? success(data) : (success || 'Success');
                    update(id, 'success', successMsg, options);
                    return data;
                })
                .catch((err) => {
                    const errorMsg = typeof error === 'function'
                        ? error(err)
                        : (typeof error === 'string' ? error : (err && err.message ? err.message : 'Something went wrong'));
                    update(id, 'error', errorMsg, options);
                    throw err;
                });
        };

        fn.dismiss = dismiss;

        return fn;
    })();

    window.toast = toast;

    // Backward-compatible bridge for existing showToast callers
    function showToast(message, type = 'success') {
        if (type === 'success') toast.success(message);
        else if (type === 'error') toast.error(message);
        else if (type === 'warning') toast.warning(message);
        else if (type === 'info') toast.info(message);
        else toast(message);
    }

    // Auto-display Laravel session flash messages as sleek toasts
    @if(session('success'))
        window.addEventListener('DOMContentLoaded', () => toast.success(@json(session('success'))));
    @endif
    @if(session('error'))
        window.addEventListener('DOMContentLoaded', () => toast.error(@json(session('error'))));
    @endif

    let filterAbortController = null;

    // Next.js Style AJAX Loader for Leads Table
    async function loadLeadsUrl(url, updateHistory = true) {
        if (filterAbortController) {
            filterAbortController.abort();
        }
        filterAbortController = new AbortController();

        const tableCard = document.getElementById('leadsTableSection');
        const spinner = document.getElementById('tableLoadingSpinner');
        
        if (tableCard) tableCard.classList.add('loading');
        if (spinner) spinner.style.display = 'inline-flex';

        try {
            const response = await fetch(url, {
                signal: filterAbortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network error loading leads');

            const data = await response.json();
            
            // Swap table component HTML seamlessly
            document.getElementById('leadsTableContainer').innerHTML = data.html;

            // Update browser URL history bar without page reload (Next.js feel)
            if (updateHistory) {
                window.history.pushState({ url }, '', url);
            }

            // Sync stats if returned
            if (data.stats) {
                if (document.getElementById('statTotalVerified')) document.getElementById('statTotalVerified').innerText = Number(data.stats.total).toLocaleString();
                if (document.getElementById('statNewLeads')) document.getElementById('statNewLeads').innerText = Number(data.stats.new).toLocaleString();
                if (document.getElementById('statHighRating')) document.getElementById('statHighRating').innerText = Number(data.stats.high_rating).toLocaleString();
                if (document.getElementById('statWith2ndPhone')) document.getElementById('statWith2ndPhone').innerText = Number(data.stats.with_secondary_phone).toLocaleString();
                if (document.getElementById('statNewBadge')) {
                    const pct = data.stats.total > 0 ? Math.round((data.stats.new / data.stats.total) * 100) : 0;
                    document.getElementById('statNewBadge').innerText = `${pct}% of Total`;
                }
            }

        } catch (err) {
            if (err.name === 'AbortError') {
                return; // User fired another filter, quietly abort
            }
            console.error('Error fetching leads:', err);
            showToast('Failed to refresh leads: ' + err.message, 'error');
        } finally {
            const updatedCard = document.getElementById('leadsTableSection');
            if (updatedCard) updatedCard.classList.remove('loading');
            const updatedSpinner = document.getElementById('tableLoadingSpinner');
            if (updatedSpinner) updatedSpinner.style.display = 'none';
        }
    }

    // Live Debounced Search Filter
    function debounceFilter() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            applyLeadsFilter();
        }, 300);
    }

    // Apply Filter to Leads Table
    function applyLeadsFilter() {
        const search = document.getElementById('filterSearch') ? document.getElementById('filterSearch').value.trim() : '';
        const city = document.getElementById('filterCity') ? document.getElementById('filterCity').value : '';
        const status = document.getElementById('filterStatus') ? document.getElementById('filterStatus').value : '';
        const minRating = document.getElementById('filterRating') ? document.getElementById('filterRating').value : '';

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (city) params.append('city', city);
        if (status) params.append('status', status);
        if (minRating) params.append('min_rating', minRating);

        const targetUrl = window.location.pathname + '?' + params.toString();
        loadLeadsUrl(targetUrl);
    }

    // Clear All Filters
    function clearLeadsFilter() {
        loadLeadsUrl(window.location.pathname);
    }

    // Load Specific Pagination Page
    function loadLeadsPage(pageUrl) {
        if (!pageUrl) return;
        loadLeadsUrl(pageUrl);
    }

    // Handle Browser Back / Forward buttons (Next.js popstate support)
    window.addEventListener('popstate', (e) => {
        loadLeadsUrl(window.location.href, false);
    });

    // Bulk Actions Logic
    function updateBulkBar() {
        const checkboxes = document.querySelectorAll('.lead-checkbox:checked');
        const count = checkboxes.length;
        const bar = document.getElementById('bulkActionBar');
        const countBadge = document.getElementById('selectedCountBadge');
        
        if (countBadge) countBadge.innerText = count;

        if (bar) {
            if (count > 0) {
                bar.classList.add('active');
            } else {
                bar.classList.remove('active');
                const master = document.getElementById('selectAllCheckbox');
                if (master) master.checked = false;
            }
        }

        // Highlight selected rows
        document.querySelectorAll('.lead-checkbox').forEach(cb => {
            const row = document.getElementById('row-lead-' + cb.value);
            if (row) {
                if (cb.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            }
        });
    }

    function handleRowCheckboxChange(cb, leadId) {
        updateBulkBar();
    }

    function toggleSelectAll(masterCb) {
        const checkboxes = document.querySelectorAll('.lead-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCb.checked;
        });
        updateBulkBar();
    }

    function deselectAllLeads() {
        document.querySelectorAll('.lead-checkbox').forEach(cb => cb.checked = false);
        const master = document.getElementById('selectAllCheckbox');
        if (master) master.checked = false;
        updateBulkBar();
    }

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // In-App Custom Action Confirmation Modal
    function openConfirmModal({
        title = 'Confirm Action',
        message = 'Are you sure you want to proceed?',
        icon = 'delete',
        confirmText = 'Confirm',
        type = 'danger',
        onConfirm = null
    }) {
        const modal = document.getElementById('actionConfirmModal');
        if (!modal) return;

        const titleEl = document.getElementById('confirmModalTitle');
        const msgEl = document.getElementById('confirmModalMessage');
        const iconEl = document.getElementById('confirmModalIcon');
        const iconCircle = document.getElementById('confirmModalIconCircle');
        const proceedBtn = document.getElementById('confirmModalProceedBtn');
        const proceedText = document.getElementById('confirmModalProceedText');
        const proceedIcon = document.getElementById('confirmModalProceedIcon');
        const cancelBtn = document.getElementById('confirmModalCancelBtn');

        if (titleEl) titleEl.innerText = title;
        if (msgEl) msgEl.innerHTML = message;
        if (iconEl) iconEl.innerText = icon;
        if (proceedText) proceedText.innerText = confirmText;
        if (proceedIcon) proceedIcon.innerText = icon;

        if (iconCircle) {
            iconCircle.className = `confirm-icon-circle icon-${type}`;
        }
        if (proceedBtn) {
            proceedBtn.className = `btn-confirm-proceed btn-${type}`;
        }

        const closeModal = () => {
            modal.classList.remove('active');
            proceedBtn.onclick = null;
            cancelBtn.onclick = null;
        };

        cancelBtn.onclick = closeModal;

        proceedBtn.onclick = async () => {
            closeModal();
            if (typeof onConfirm === 'function') {
                await onConfirm();
            }
        };

        modal.classList.add('active');
    }

    // Custom In-App Dropdowns
    function toggleCustomDropdown(dropdownId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const menu = document.getElementById(dropdownId + 'Menu');
        const trigger = event && event.currentTarget ? event.currentTarget : null;
        
        // Close other open dropdown menus first
        document.querySelectorAll('.dropdown-menu-content, .custom-dropdown-menu').forEach(m => {
            if (m !== menu) m.classList.remove('active');
        });
        document.querySelectorAll('.dropdown-menu-trigger, .custom-dropdown-trigger').forEach(t => {
            if (t !== trigger) t.classList.remove('active');
        });

        if (menu) {
            const isOpen = menu.classList.contains('active');
            if (isOpen) {
                menu.classList.remove('active');
                if (trigger) trigger.classList.remove('active');
            } else {
                menu.classList.add('active');
                if (trigger) trigger.classList.add('active');
            }
        }
    }

    function selectFilterOption(inputId, value, label, labelId, menuId) {
        const input = document.getElementById(inputId);
        const labelEl = document.getElementById(labelId);
        const menu = document.getElementById(menuId);

        if (input) input.value = value;
        if (labelEl) labelEl.textContent = label;
        if (menu) {
            menu.classList.remove('active');
            const trigger = menu.parentElement ? (menu.parentElement.querySelector('.dropdown-menu-trigger') || menu.parentElement.querySelector('.custom-dropdown-trigger')) : null;
            if (trigger) trigger.classList.remove('active');
            menu.querySelectorAll('.dropdown-menu-item, .custom-dropdown-option').forEach(opt => opt.classList.remove('selected'));
        }

        applyLeadsFilter();
    }

    function selectBulkStatus(value, label) {
        const input = document.getElementById('bulkStatusSelect');
        const labelEl = document.getElementById('bulkStatusDropdownLabel');
        const menu = document.getElementById('bulkStatusDropdownMenu');

        if (input) input.value = value;
        if (labelEl) labelEl.textContent = label;
        if (menu) {
            menu.classList.remove('active');
            const trigger = menu.parentElement ? (menu.parentElement.querySelector('.dropdown-menu-trigger') || menu.parentElement.querySelector('.custom-dropdown-trigger')) : null;
            if (trigger) trigger.classList.remove('active');
        }
    }

    async function executeBulkAction(action) {
        const selected = Array.from(document.querySelectorAll('.lead-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            showToast('Please select at least one lead first.', 'error');
            return;
        }

        let newStatus = null;

        if (action === 'delete') {
            openConfirmModal({
                title: 'Delete Selected Leads',
                message: `Are you sure you want to permanently delete <strong>${selected.length}</strong> selected leads? This action cannot be undone.`,
                icon: 'delete_sweep',
                confirmText: `Delete ${selected.length} Leads`,
                type: 'danger',
                onConfirm: async () => {
                    await sendBulkActionRequest(action, null, selected);
                }
            });
            return;
        } else if (action === 'update_status') {
            newStatus = document.getElementById('bulkStatusSelect') ? document.getElementById('bulkStatusSelect').value : null;
            if (!newStatus) {
                showToast('Please pick a target status from the dropdown first.', 'error');
                return;
            }

            const statusMap = {
                'new': 'New',
                'contacted': 'Contacted',
                'interested': 'Interested',
                'not_interested': 'Not Interested',
                'closed': 'Closed'
            };
            const statusLabel = statusMap[newStatus] || newStatus;

            openConfirmModal({
                title: 'Update Leads Status',
                message: `Are you sure you want to change status of <strong>${selected.length}</strong> selected leads to <strong>${statusLabel}</strong>?`,
                icon: 'published_with_changes',
                confirmText: 'Update Status',
                type: 'warning',
                onConfirm: async () => {
                    await sendBulkActionRequest(action, newStatus, selected);
                }
            });
            return;
        }
    }

    async function sendBulkActionRequest(action, newStatus, selected) {
        try {
            const response = await fetch('{{ route('leads.bulkAction') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bulk_action: action,
                    new_status: newStatus,
                    selected_ids: selected
                })
            });

            const result = await response.json();
            if (result.success) {
                showToast(result.message, 'success');
                loadLeadsUrl(window.location.href, false);
            } else {
                showToast(result.message || 'Bulk operation failed', 'error');
            }
        } catch (err) {
            showToast('Error executing bulk action: ' + err.message, 'error');
        }
    }

    // Single Lead Deletion via In-App Custom Modal & AJAX
    function deleteSingleLead(id, name) {
        openConfirmModal({
            title: 'Delete Lead',
            message: `Are you sure you want to permanently delete lead <strong style="color: var(--text-dark);">"${escapeHtml(name)}"</strong>? This action cannot be undone.`,
            icon: 'delete_forever',
            confirmText: 'Delete Permanently',
            type: 'danger',
            onConfirm: async () => {
                try {
                    const response = await fetch('/leads/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();
                    if (result.success) {
                        showToast(result.message, 'success');
                        loadLeadsUrl(window.location.href, false);
                    } else {
                        showToast(result.message || 'Failed to delete lead', 'error');
                    }
                } catch (err) {
                    showToast('Error deleting lead: ' + err.message, 'error');
                }
            }
        });
    }

    // Edit Modal Logic & AJAX Submission
    let activeEditingLeadId = null;

    function openEditModal(id, name, secPhone, notes, status) {
        activeEditingLeadId = id;
        document.getElementById('modalLeadTitle').innerText = 'Edit: ' + name;
        document.getElementById('modalSecPhone').value = secPhone;
        document.getElementById('modalNotes').value = notes;
        document.getElementById('modalStatus').value = status;
        document.getElementById('editModal').classList.add('active');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
        activeEditingLeadId = null;
    }

    async function submitEditLeadForm() {
        if (!activeEditingLeadId) return;

        const secPhone = document.getElementById('modalSecPhone').value;
        const notes = document.getElementById('modalNotes').value;
        const status = document.getElementById('modalStatus').value;

        try {
            const response = await fetch('/leads/' + activeEditingLeadId, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    secondary_phone: secPhone,
                    notes: notes,
                    status: status
                })
            });

            const result = await response.json();
            if (result.success) {
                showToast(result.message, 'success');
                closeEditModal();
                loadLeadsUrl(window.location.href, false);
            } else {
                showToast(result.message || 'Failed to update lead', 'error');
            }
        } catch (err) {
            showToast('Error saving updates: ' + err.message, 'error');
        }
    }

    // Scraper Modal Controls & Background Execution
    function openScraperModal() {
        document.getElementById('scraperModal').classList.add('active');
        const q = document.getElementById('scraperQuery');
        if (q) q.focus();
    }
    function closeScraperModal() {
        document.getElementById('scraperModal').classList.remove('active');
    }

    async function submitScraperAsync() {
        const queryEl = document.getElementById('scraperQuery');
        const locationEl = document.getElementById('scraperLocation');
        const limitEl = document.getElementById('scraperLimit');

        const query = queryEl ? queryEl.value.trim() : '';
        const location = locationEl ? locationEl.value.trim() : '';
        const limit = limitEl ? limitEl.value : 20;

        if (!query) {
            toast.warning('Please enter a business niche or keyword');
            return;
        }

        // Close the modal immediately as requested
        closeScraperModal();

        // Trigger background scraping with Sonner toast.promise & async polling
        toast.promise(
            (async () => {
                const response = await fetch('{{ route('scrape.trigger') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ query, location, limit })
                });

                const data = await response.json();
                if (!response.ok || !data.success || !data.job_id) {
                    throw new Error(data.error || data.message || 'Scraper execution encountered an issue.');
                }

                // Poll job status in the background without blocking any user interaction
                return await pollScrapeJobStatus(data.job_id);
            })(),
            {
                loading: `Scraping Google Maps leads for "${query}"${location ? ' in ' + location : ''}...`,
                success: (data) => data.message || 'Scraping completed successfully!',
                error: (err) => err.message || 'Failed to extract listings.'
            }
        );
    }

    async function pollScrapeJobStatus(jobId) {
        const pollInterval = 1500;
        const maxAttempts = 120; // 3 minutes timeout

        for (let attempt = 0; attempt < maxAttempts; attempt++) {
            await new Promise(resolve => setTimeout(resolve, pollInterval));

            try {
                const res = await fetch(`/scrape/status/${jobId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) continue;

                const job = await res.json();

                if (job.status === 'completed') {
                    // Seamlessly refresh table and hero statistics without reloading the page
                    loadLeadsUrl(window.location.href, false);
                    return job;
                }

                if (job.status === 'failed') {
                    throw new Error(job.error_message || 'Scraping process encountered an error');
                }
            } catch (err) {
                if (err.message && err.message !== 'Failed to fetch') {
                    throw err;
                }
            }
        }

        throw new Error('Scraper background task timed out.');
    }

    // =========================================================================
    // GodUI FilterBar Interaction Handlers (@godui/filter-bar)
    // =========================================================================
    function toggleGoduiFacet(facetName, event) {
        if (event) {
            event.stopPropagation();
        }
        const menuId = facetName + 'Menu';
        const targetMenu = document.getElementById(menuId);
        if (!targetMenu) return;

        const isCurrentlyOpen = targetMenu.classList.contains('active');

        // Close all facet popovers and dropdowns
        document.querySelectorAll('.godui-facet-popover.active').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.dropdown-menu-content.active').forEach(m => m.classList.remove('active'));

        if (!isCurrentlyOpen) {
            targetMenu.classList.add('active');
            const searchInput = targetMenu.querySelector('.godui-popover-search');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                targetMenu.querySelectorAll('.godui-popover-item').forEach(item => item.style.display = '');
            }
        }
    }

    function selectGoduiFacetOption(inputId, value) {
        const input = document.getElementById(inputId);
        if (!input) return;

        // Toggle behavior: unselect if already active
        if (input.value === String(value)) {
            input.value = '';
        } else {
            input.value = value;
        }

        document.querySelectorAll('.godui-facet-popover.active').forEach(p => p.classList.remove('active'));
        applyLeadsFilter();
    }

    function clearFacet(inputId, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const input = document.getElementById(inputId);
        if (input) {
            input.value = '';
        }
        document.querySelectorAll('.godui-facet-popover.active').forEach(p => p.classList.remove('active'));
        applyLeadsFilter();
    }

    function filterGoduiFacet(inputEl) {
        const query = (inputEl.value || '').trim().toLowerCase();
        const popover = inputEl.closest('.godui-facet-popover');
        if (!popover) return;

        const items = popover.querySelectorAll('.godui-popover-item');
        items.forEach(item => {
            const label = item.querySelector('.godui-item-label');
            const text = label ? label.textContent.toLowerCase() : item.textContent.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        });
    }

    // Modal background click & global outside dropdown closer
    window.addEventListener('click', function(event) {
        const sm = document.getElementById('scraperModal');
        const em = document.getElementById('editModal');
        const cm = document.getElementById('actionConfirmModal');
        if (event.target === sm) sm.classList.remove('active');
        if (event.target === em) em.classList.remove('active');
        if (event.target === cm) cm.classList.remove('active');

        // Close dropdowns when clicking outside
        if (!event.target.closest('.dropdown-menu-container') && !event.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.dropdown-menu-content.active, .custom-dropdown-menu.active').forEach(m => m.classList.remove('active'));
            document.querySelectorAll('.dropdown-menu-trigger.active, .custom-dropdown-trigger.active').forEach(t => t.classList.remove('active'));
        }

        // Close GodUI facet popovers when clicking outside
        if (!event.target.closest('.godui-facet-wrapper')) {
            document.querySelectorAll('.godui-facet-popover.active').forEach(p => p.classList.remove('active'));
        }
    });

    // Close open popups on Escape key
    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.dropdown-menu-content.active, .custom-dropdown-menu.active').forEach(m => m.classList.remove('active'));
            document.querySelectorAll('.dropdown-menu-trigger.active, .custom-dropdown-trigger.active').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.godui-facet-popover.active').forEach(p => p.classList.remove('active'));
            const cm = document.getElementById('actionConfirmModal');
            if (cm && cm.classList.contains('active')) cm.classList.remove('active');
            const em = document.getElementById('editModal');
            if (em && em.classList.contains('active')) em.classList.remove('active');
            const sm = document.getElementById('scraperModal');
            if (sm && sm.classList.contains('active')) sm.classList.remove('active');
        }
    });
</script>

</body>
</html>
