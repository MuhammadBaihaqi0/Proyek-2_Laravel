<x-app-layout>
    <x-slot name="header"></x-slot>

    <style>
        :root {
            --page-bg: #f5f5fb;
            --page-text: #1f1f2b;
        }
        body.theme-dark {
            background-color: #0f172a;
            color: #f8fafc;
        }
        body.theme-dark .bg-white,
        body.theme-dark .sidebar-mini,
        body.theme-dark .menu-link,
        body.theme-dark .bg-gray-50,
        body.theme-dark .bg-gray-100 {
            background-color: #1c2333 !important;
            color: #f3f4f6 !important;
        }
        body.theme-dark .menu-link span:first-child {
            color: #cabffd !important;
        }
        body.theme-dark .menu-link:hover {
            background-color: rgba(148, 119, 255, 0.18) !important;
        }
        body.theme-dark .menu-divider {
            background: rgba(148,163,184,0.35);
        }
        body.theme-dark .theme-toggle {
            background: rgba(148,163,184,0.15);
            border-color: rgba(148,163,184,0.3);
        }
        body.theme-dark .theme-toggle button {
            color: #e0e7ff;
        }
        body.theme-dark .theme-toggle button.active {
            background: #fff;
            color: #5b21b6;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .dashboard-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        @media (min-width: 1024px) {
            .dashboard-layout {
                flex-direction: row;
                align-items: flex-start;
            }
        }
        .sidebar-mini {
            background: #fff;
            border-radius: 24px;
            border: 1px solid rgba(99,102,241,0.1);
            box-shadow: 0 20px 45px rgba(15,23,42,0.08);
            position: sticky;
            top: 6rem;
            padding: 20px 18px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            width: 100%;
            transition: width 0.3s ease, padding 0.3s ease;
        }
        .dashboard-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 10px 20px rgba(99,102,241,0.25);
            transition: transform 0.3s ease;
            margin: 0 auto;
        }
        @media (min-width: 1024px) {
            .sidebar-mini {
                width: 84px;
                padding: 24px 16px;
                overflow: hidden;
            }
            .sidebar-mini:hover {
                width: 270px;
                padding: 28px 22px;
            }
            .sidebar-mini:not(:hover) .menu-label,
            .sidebar-mini:not(:hover) .menu-section-title,
            .sidebar-mini:not(:hover) .logout-label {
                opacity: 0;
                transform: translateX(-12px);
            }
            .sidebar-mini:hover .dashboard-icon {
                margin-left: 0;
            }
        }
        .menu-section-title {
            font-size: 0.65rem;
            letter-spacing: 0.35em;
            color: #b4b5d8;
            font-weight: 700;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 16px;
            font-weight: 600;
            color: #7b7da6;
            transition: background 0.2s ease, color 0.2s ease;
            position: relative;
        }
        .menu-link span:first-child {
            font-size: 1.1rem;
        }
        .menu-label {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .menu-label small {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            color: inherit;
        }
        .menu-link:hover {
            background: rgba(124,58,237,0.08);
            color: #4f46e5;
        }
        .menu-link-active {
            background: linear-gradient(140deg, #8b5cf6, #6366f1);
            color: #fff;
            box-shadow: 0 12px 28px rgba(99,102,241,0.25);
        }
        .menu-divider {
            height: 1px;
            width: 100%;
            background: rgba(148,163,184,0.25);
        }
        .theme-toggle {
            margin-top: 10px;
            border-radius: 16px;
            border: 1px solid rgba(99,102,241,0.18);
            background: rgba(244,241,255,0.9);
            padding: 4px;
            display: inline-flex;
            width: 100%;
            gap: 6px;
        }
        .theme-toggle button {
            flex: 1;
            border: none;
            background: transparent;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b21a8;
            padding: 6px 0;
            transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }
        .theme-toggle button.active {
            background: #fff;
            color: #5b21b6;
            box-shadow: 0 6px 12px rgba(111,76,255,0.18);
        }
        .logout-button {
            border-radius: 16px;
            border: 1px solid #fecdd3;
            background: #fff4f5;
            color: #be123c;
            font-weight: 700;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
            transition: background 0.2s ease;
        }
        .logout-button svg {
            flex-shrink: 0;
        }
        .logout-label {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .logout-button:hover {
            background: #fee2e2;
        }
    </style>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="w-full px-0">
            <div class="dashboard-layout">
                <aside class="sidebar-mini">
                    <!-- <div class="dashboard-icon">📊</div> -->

                    <div class="space-y-5">
                        <p class="menu-section-title">MENU UTAMA</p>
                        <nav class="space-y-2">
                            <a href="#dashboard" class="menu-link menu-link-active">
                                <span>📊</span>
                                <span class="menu-label">
                                    Dashboard
                                    <small>(Active)</small>
                                </span>
                            </a>
                            <a href="#tugas" class="menu-link">
                                <span>📌</span>
                                <span class="menu-label">Tugas Saya</span>
                            </a>
                            <a href="#acara" class="menu-link">
                                <span>🎉</span>
                                <span class="menu-label">Acara Saya</span>
                            </a>
                            <a href="#arsip" class="menu-link">
                                <span>📂</span>
                                <span class="menu-label">Arsip</span>
                            </a>
                        </nav>
                    </div>

                     <div class="menu-divider"></div>
 
                     <div class="space-y-2">
                         <p class="menu-section-title flex items-center gap-1">
                             ⚙️ PENGATURAN
                         </p>
                         <nav class="space-y-2">
                             <a href="{{ route('profile.edit') }}" class="menu-link">
                                 <span>⚙️</span>
                                 <span class="menu-label">Edit Profil</span>
                             </a>
                             <a href="#" class="menu-link">
                                 <span>🔐</span>
                                 <span class="menu-label">Ubah Password</span>
                             </a>
                         </nav>
                        <div>
                            <p class="menu-section-title flex items-center gap-1 mt-4">
                                🎨 TEMA & TAMPILAN
                            </p>
                            <div class="theme-toggle" role="group" aria-label="Theme toggle">
                                <button type="button" class="theme-switch" data-theme="light">Terang</button>
                                <button type="button" class="theme-switch" data-theme="dark">Gelap</button>
                            </div>
                        </div>
                     </div>

                    <div class="menu-divider"></div>

                    <div class="space-y-2">
                        <p class="menu-section-title flex items-center gap-1">
                            📈 ANALITIK
                        </p>
                        <nav class="space-y-2">
                            <a href="#" class="menu-link">
                                <span>📊</span>
                                <span class="menu-label">Statistik Tugas</span>
                            </a>
                            <a href="#" class="menu-link">
                                <span>📅</span>
                                <span class="menu-label">Laporan Bulanan</span>
                            </a>
                            <a href="#" class="menu-link">
                                <span>🎯</span>
                                <span class="menu-label">Progress Overview</span>
                            </a>
                        </nav>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="logout-button w-full">
                            <span>🚪</span>
                            <span class="logout-label">Logout</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4h4m-4 0H5a1 1 0 00-1 1v14a1 1 0 001 1h2m4 0h4" />
                            </svg>
                        </button>
                    </form>
                </aside>

                <div class="flex-1 space-y-8" id="dashboard">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden text-white relative animate-fade-in">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-pink-500 opacity-20 rounded-full -ml-10 -mb-10 blur-xl"></div>
                
                <div class="p-8 md:flex items-center justify-between relative z-10">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 to-pink-500 rounded-full blur opacity-50"></div>
                            <img class="relative h-24 w-24 rounded-full object-cover border-4 border-white/30 shadow-lg" 
                                 src="{{ $avatar_path }}" alt="{{ $user->username }}">
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold tracking-tight">{{ $greeting }}, {{ $user->username }}! {{ $icon }}</h3>
                            <p class="text-indigo-100 mt-1">Semoga harimu menyenangkan dan produktif!</p>
                        </div>
                    </div>
                    <div class="hidden md:block text-right max-w-xs opacity-90 border-l border-white/20 pl-6">
                        <p class="italic text-sm">"Success is a final, failure is not fatal: it is the courage to continue that counts."</p>
                        <p class="text-xs mt-2 font-bold">— Winston Churchill</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-fade-in delay-100">
                <div class="bg-white p-4 rounded-xl shadow-sm border-b-4 border-indigo-500 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Tugas</p>
                        <p class="text-2xl font-bold text-gray-800">{{ count($tugas) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600 text-xl">📝</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-b-4 border-emerald-500 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Acara Nanti</p>
                        <p class="text-2xl font-bold text-gray-800">{{ count($acara) }}</p>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600 text-xl">🎉</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-b-4 border-orange-400 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Tugas Selesai</p>
                        <p class="text-lg font-bold text-gray-800">{{ count($riwayat_tugas) }}</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg text-orange-500 text-xl">✨</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-b-4 border-pink-500 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Bulan Ini</p>
                        <p class="text-lg font-bold text-gray-800">{{ date('F') }}</p>
                    </div>
                    <div class="p-3 bg-pink-50 rounded-lg text-pink-500 text-xl">📅</div>
                </div>
            </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in delay-200" id="tugas">
                
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col h-full">
                    <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 text-lg flex items-center">
                            <span class="bg-white shadow-sm p-1.5 rounded-md mr-2 text-xl">📌</span> Tugas Saya
                        </h3>
                        <span class="text-xs font-semibold bg-gray-200 text-gray-600 px-2 py-1 rounded">Pending: {{ count($tugas) }}</span>
                    </div>

                    <div class="p-6 flex-grow flex flex-col">
                        <form action="{{ route('tugas.store') }}" method="POST" class="mb-6 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                            @csrf
                            <label class="block text-xs font-bold text-indigo-600 uppercase mb-2">Tambah Baru</label>
                            <div class="flex flex-col gap-3">
                                <input type="text" name="nama_tugas" class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Mau ngerjain apa?" required>
                                <div class="flex gap-2">
                                    <input type="date" name="deadline" class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm text-gray-600" required>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 rounded-lg text-sm font-bold shadow-md transition">+</button>
                                </div>
                            </div>
                        </form>

                        <div class="space-y-3 max-h-[350px] overflow-y-auto no-scrollbar pr-1">
                            @forelse($tugas as $t)
                                <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg shadow-sm hover:border-indigo-300 hover:shadow-md transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-indigo-500 group-hover:scale-125 transition"></div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $t->nama_tugas }}</p>
                                            <p class="text-xs text-red-500">Deadline: {{ $t->deadline }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('tugas.selesai', $t->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs bg-green-100 text-green-700 px-3 py-1.5 rounded-md hover:bg-green-200 transition font-bold shadow-sm">
                                            ✅ Selesai
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-10 opacity-60">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" class="w-16 h-16 mx-auto mb-3 opacity-50" alt="Empty">
                                    <p class="text-sm text-gray-500">Kerjaan beres semua! Mantap.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col h-full" id="acara">
                    <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 text-lg flex items-center">
                            <span class="bg-white shadow-sm p-1.5 rounded-md mr-2 text-xl">🎈</span> Acara Seru
                        </h3>
                        <span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2 py-1 rounded">Total: {{ count($acara) }}</span>
                    </div>

                    <div class="p-6 flex-grow flex flex-col">
                        <form action="{{ route('acara.store') }}" method="POST" class="mb-6 bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                            @csrf
                            <label class="block text-xs font-bold text-emerald-600 uppercase mb-2">Jadwalkan Acara</label>
                            <div class="flex flex-col gap-3">
                                <input type="text" name="nama_acara" class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Ada acara apa?" required>
                                <div class="flex gap-2">
                                    <input type="date" name="tanggal" class="w-full border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm text-gray-600" required>
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 rounded-lg text-sm font-bold shadow-md transition">+</button>
                                </div>
                            </div>
                        </form>

                        <div class="space-y-4 max-h-[350px] overflow-y-auto no-scrollbar pr-1">
                            @forelse($acara as $a)
                                <div class="relative pl-6 border-l-2 border-emerald-200 hover:border-emerald-500 transition group">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-emerald-100 border-2 border-emerald-400 group-hover:bg-emerald-500 transition"></div>
                                    <p class="text-xs text-gray-500 font-bold uppercase mb-0.5">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('l, d M Y') }}</p>
                                    <h4 class="font-bold text-gray-800 group-hover:text-emerald-700 transition">{{ $a->nama_acara }}</h4>
                                    <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($a->tanggal)->diffForHumans() }}</p>
                                </div>
                            @empty
                                <div class="text-center py-10 opacity-60">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" class="w-16 h-16 mx-auto mb-3 opacity-50" alt="Empty">
                                    <p class="text-sm text-gray-500">Belum ada agenda. Yuk jalan-jalan!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t-2 border-dashed border-gray-300" id="arsip">
                <h2 class="text-2xl font-bold text-gray-600 mb-6 flex items-center gap-2">
                    📂 Arsip Riwayat
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-100 p-6 rounded-2xl border border-gray-200 shadow-inner">
                        <h4 class="font-bold text-gray-500 uppercase text-xs mb-4 tracking-wider">Tugas Selesai</h4>
                        <ul class="space-y-2 max-h-[200px] overflow-y-auto no-scrollbar">
                            @forelse($riwayat_tugas as $rt)
                                <li class="flex justify-between items-center text-sm bg-white p-3 rounded-lg shadow-sm opacity-70 hover:opacity-100 transition">
                                    <span class="line-through text-gray-500">{{ $rt->nama_tugas }}</span>
                                    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">DONE</span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-400 italic text-center py-4">Belum ada tugas selesai.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="bg-gray-100 p-6 rounded-2xl border border-gray-200 shadow-inner">
                        <h4 class="font-bold text-gray-500 uppercase text-xs mb-4 tracking-wider">Acara Terlewat</h4>
                        <ul class="space-y-2 max-h-[200px] overflow-y-auto no-scrollbar">
                            @forelse($riwayat_acara as $ra)
                                <li class="flex justify-between items-center text-sm bg-white p-3 rounded-lg shadow-sm opacity-70 hover:opacity-100 transition">
                                    <span class="text-gray-600 font-medium">{{ $ra->nama_acara }}</span>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($ra->tanggal)->format('d M Y') }}</span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-400 italic text-center py-4">Belum ada acara lewat.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="text-center text-gray-400 text-xs py-4">
                &copy; {{ date('Y') }} Project Laravel by {{ $user->username }}.
            </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    (function() {
        const root = document.documentElement;
        const storedTheme = localStorage.getItem('dashboard-theme');
        const body = document.body;
        const themeButtons = document.querySelectorAll('.theme-switch');

        function applyTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('theme-dark');
            } else {
                body.classList.remove('theme-dark');
            }

            themeButtons.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.theme === theme);
            });

            localStorage.setItem('dashboard-theme', theme);
        }

        themeButtons.forEach(btn => {
            btn.addEventListener('click', () => applyTheme(btn.dataset.theme));
        });

        if (storedTheme) {
            applyTheme(storedTheme);
        } else {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(prefersDark ? 'dark' : 'light');
        }
    })();
</script>