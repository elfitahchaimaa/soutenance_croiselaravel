<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap');

        .profile-page * {
            font-family: 'DM Sans', sans-serif;
        }

        .profile-page {
            min-height: 100vh;
            background: #f0ede8;
            padding: 2.5rem 1rem;
        }

        /* ── HERO CARD ── */
        .hero-card {
            background: #1a1a2e;
            border-radius: 24px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(255,200,100,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-card::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -40px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(100,200,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .avatar-ring {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffc864, #ff7eb3);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem; font-weight: 800;
            color: #1a1a2e;
            flex-shrink: 0;
        }

        .hero-name {
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }

        .hero-email {
            color: rgba(255,255,255,0.45);
            font-size: 0.85rem;
            font-weight: 300;
            margin-top: 2px;
        }

        /* ── BADGES ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        .badge-admin {
            background: rgba(200,170,255,0.15);
            color: #c8aaff;
            border: 1px solid rgba(200,170,255,0.25);
        }

        .badge-member {
            background: rgba(100,200,255,0.12);
            color: #64c8ff;
            border: 1px solid rgba(100,200,255,0.2);
        }

        .badge-banned {
            background: rgba(255,100,100,0.12);
            color: #ff6464;
            border: 1px solid rgba(255,100,100,0.2);
        }

        .badge-active {
            background: rgba(80,220,140,0.12);
            color: #50dc8c;
            border: 1px solid rgba(80,220,140,0.2);
        }

        .badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .badge-active .badge-dot {
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.7); }
        }

        /* ── STAT PILLS ── */
        .stat-pills {
            display: flex;
            gap: 10px;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-pill {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 10px 18px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-pill-label {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .stat-pill-value {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
        }

        /* ── SECTION CARDS ── */
        .section-card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(0,0,0,0.05);
            transition: box-shadow 0.2s ease;
        }

        .section-card:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        }

        .section-header {
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid #f0ede8;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .section-icon-profile  { background: #fff3e0; }
        .section-icon-password { background: #e8f4ff; }
        .section-icon-danger   { background: #fff0f0; }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.98rem;
            color: #1a1a2e;
        }

        .section-subtitle {
            font-size: 0.78rem;
            color: #9e9b94;
            margin-top: 1px;
        }

        .section-body {
            padding: 1.75rem;
        }

        /* ── DANGER ZONE ── */
        .danger-card {
            background: #fff8f8;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 1.25rem;
            border: 1px solid #ffd6d6;
        }

        .danger-card .section-header {
            border-bottom: 1px solid #ffd6d6;
        }

        /* ── DIVIDER ── */
        .section-divider {
            height: 1px;
            background: #f0ede8;
            margin: 0 1.75rem;
        }

        /* ── LAYOUT ── */
        .profile-grid {
            max-width: 720px;
            margin: 0 auto;
        }

        @media (max-width: 640px) {
            .hero-card { padding: 1.75rem; }
            .section-body { padding: 1.25rem; }
        }
    </style>

    <div class="profile-page">
        <div class="profile-grid">

            {{-- ── HERO CARD ── --}}
            <div class="hero-card">
                <div style="display:flex; align-items:flex-start; gap:1.25rem;">
                    <div class="avatar-ring">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <div class="hero-name">{{ auth()->user()->name }}</div>
                        <div class="hero-email">{{ auth()->user()->email }}</div>
                        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:14px;">
                            @if (auth()->user()->isAdmin())
                                <span class="badge badge-admin">
                                    <span class="badge-dot"></span> Global Admin
                                </span>
                            @else
                                <span class="badge badge-member">
                                    <span class="badge-dot"></span> Member
                                </span>
                            @endif

                            @if (auth()->user()->is_banned)
                                <span class="badge badge-banned">
                                    <span class="badge-dot"></span> Banned
                                </span>
                            @else
                                <span class="badge badge-active">
                                    <span class="badge-dot"></span> Active
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="stat-pills">
                    <div class="stat-pill">
                        <span class="stat-pill-label">Member since</span>
                        <span class="stat-pill-value">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                    <div class="stat-pill">
                        <span class="stat-pill-label">Last update</span>
                        <span class="stat-pill-value">{{ auth()->user()->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="stat-pill">
                        <span class="stat-pill-label">ID</span>
                        <span class="stat-pill-value">#{{ auth()->user()->id }}</span>
                    </div>
                </div>
            </div>

            {{-- ── UPDATE PROFILE ── --}}
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon section-icon-profile">profile</div>
                    <div>
                        <div class="section-title">Personal Information</div>
                        <div class="section-subtitle">Update your name and email address</div>
                    </div>
                </div>
                <div class="section-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- ── UPDATE PASSWORD ── --}}
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon section-icon-password">security</div>
                    <div>
                        <div class="section-title">Security</div>
                        <div class="section-subtitle">Change your password</div>
                    </div>
                </div>
                <div class="section-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- ── DELETE ACCOUNT ── --}}
            <div class="danger-card">
                <div class="section-header">
                    <div class="section-icon section-icon-danger">warning</div>
                    <div>
                        <div class="section-title" style="color:#c0392b;">Danger Zone</div>
                        <div class="section-subtitle">Permanently delete your account</div>
                    </div>
                </div>
                <div class="section-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>