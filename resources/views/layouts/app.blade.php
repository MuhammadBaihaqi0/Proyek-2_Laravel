<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }
        
        body {
            background-color: #f3f4f6;
            overflow: hidden;
        }
        
        .main-container {
            display: flex;
            width: 100vw;
            height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            height: 100%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            padding: 20px;
            gap: 20px;
            flex-shrink: 0;
            z-index: 10;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: #f0f0f0;
            border-radius: 12px;
            color: #6366f1;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sidebar-header:hover {
            background: #e0e0e0;
            transform: scale(1.05);
        }
        
        .sidebar-header svg {
            width: 24px;
            height: 24px;
        }
        
        .sidebar-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .sidebar-label {
            font-size: 11px;
            font-weight: 700;
            color: #b8b8d1;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            padding: 0 12px;
        }
        
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: #8b8ba3;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .sidebar-item:hover {
            background: #f5f5f9;
            color: #6366f1;
        }
        
        .sidebar-item.active {
            background: #f0f0ff;
            color: #6366f1;
        }
        
        .sidebar-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        
        .sidebar-divider {
            border-top: 1px solid #e8e8ec;
            margin: 12px 0;
        }
        
        .theme-toggle {
            display: flex;
            gap: 8px;
            padding: 12px;
            background: #f5f5f9;
            border-radius: 12px;
            margin-top: 4px;
        }
        
        .theme-btn {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid transparent;
            background: transparent;
            color: #8b8ba3;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .theme-btn:hover {
            background: #ffffff;
            color: #6366f1;
        }
        
        .theme-btn.active {
            background: #ffffff;
            color: #6366f1;
            border-color: #e0dff9;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
        }
        
        .sidebar-footer {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid #e8e8ec;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 16px;
            background: #d32f2f;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            color: #8b8ba3;
            background: #ffd1d1;
            transform: translateX(4px);
        }
        
        .logout-btn svg {
            width: 18px;
            height: 18px;
        }
        
        .content {
            flex: 1;
            background: #f5f5f7;
            overflow-y: auto;
            padding: 30px;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <aside class="sidebar">
            <!-- Header Icon -->
            <div class="sidebar-header" title="Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </div>
            
            <!-- Menu Utama -->
            <div class="sidebar-section">
                <div class="sidebar-label">Menu Utama</div>
                <a href="/dashboard" class="sidebar-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path></svg>
                    Dashboard
                </a>
            </div>
            
            <div class="sidebar-divider"></div>
            
            <!-- Pengaturan -->
            <div class="sidebar-section">
                <div class="sidebar-label">Pengaturan</div>
                <a href="/profile" class="sidebar-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                    Edit Profil
                </a>
                <a href="/password" class="sidebar-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5s-5 2.24-5 5v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-4 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                    Ubah Password
                </a>
            </div>
            
            <div class="sidebar-divider"></div>
            
            <!-- Tema & Tampilan -->
            <div class="sidebar-section">
                <div class="sidebar-label">Tema & Tampilan</div>
                <div class="theme-toggle">
                    <button class="theme-btn active">Terang</button>
                    <button class="theme-btn">Gelap</button>
                </div>
            </div>
            
            <!-- Analitik -->
            <div class="sidebar-section">
                <div class="sidebar-label">Analitik</div>
                <a href="/statistik-tugas" class="sidebar-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"></path></svg>
                    Statistik Tugas
                </a>
                <a href="/laporan-bulanan" class="sidebar-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2V17zm4 0h-2V7h2V17zm4 0h-2v-4h2V17z"></path></svg>
                    Laporan Bulanan
                </a>
                <a href="/progress-overview" class="sidebar-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
                    Progress Overview
                </a>
            </div>
            
            <!-- Footer -->
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><rect x="10" y="2" width="4" height="8" rx="1"></rect><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5z"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <main class="content">
            {{ $slot }}
        </main>
    </div>

    {{-- Masukkan partial pomodoro (safe: includeIf) --}}
    @includeIf('partials._pomodoro')

</body>
</html>
