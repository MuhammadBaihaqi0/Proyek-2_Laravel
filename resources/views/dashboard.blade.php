<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-200">
                📅 {{ $tanggalHariIni }}
            </div>
        </div>
    </x-slot>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
    </style>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
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
                            <div class="mt-4 flex gap-3">
                                <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg text-sm font-semibold transition border border-white/20">
                                    ⚙️ Edit Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-500/80 hover:bg-red-500 text-white rounded-lg text-sm font-semibold transition shadow-md">
                                        Log Out
                                    </button>
                                </form>
                            </div>
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in delay-200">
                
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

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col h-full">
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

            <div class="pt-8 border-t-2 border-dashed border-gray-300">
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
</x-app-layout>