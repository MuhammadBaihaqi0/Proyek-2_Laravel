<x-app-layout>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* Hide scrollbar for clean look */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight tracking-wide">
                {{ __('Dashboard') }}
            </h2>
            <div
                class="hidden md:flex items-center space-x-2 text-sm font-medium text-indigo-600 bg-indigo-50 px-4 py-2 rounded-full shadow-sm border border-indigo-100">
                <span>🗓️</span>
                <span>{{ $tanggalHariIni }}</span>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 relative overflow-hidden pb-12">
        <div
            class="absolute top-0 left-0 w-64 h-64 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -translate-x-1/2 -translate-y-1/2 animate-blob">
        </div>
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-x-1/2 -translate-y-1/2 animate-blob animation-delay-2000">
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10 pt-8">

            <div
                class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-xl overflow-hidden mb-10 animate-fade-in transform hover:scale-[1.01] transition duration-300">
                <div class="p-8 md:flex items-center justify-between relative">
                    <div class="absolute inset-0 bg-white opacity-10"
                        style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;">
                    </div>

                    <div class="relative z-10 flex items-center space-x-6">
                        <div class="relative">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-yellow-400 to-pink-500 rounded-full blur opacity-75">
                            </div>
                            <img class="relative h-24 w-24 rounded-full object-cover border-4 border-white shadow-md"
                                src="{{ $avatar_path }}" alt="{{ $user->username }}">
                        </div>
                        <div class="text-white">
                            <h3 class="text-3xl font-extrabold tracking-tight">Halo, {{ $user->username }}! 👋</h3>
                            <p class="text-indigo-100 mt-1 text-lg">Siap menjadi produktif hari ini?</p>
                            <div class="mt-4">
                                <a href="{{ route('profile.edit') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-sm font-semibold text-white hover:bg-white hover:text-indigo-600 transition duration-300 ease-in-out group">
                                    <span>⚙️ Edit Profil</span>
                                    <svg class="w-4 h-4 ml-2 transform group-hover:rotate-90 transition duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 md:mt-0 md:text-right text-white opacity-80 relative z-10 md:hidden">
                        <p class="text-sm font-medium">{{ $tanggalHariIni }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div
                    class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden animate-fade-in delay-100 hover:shadow-xl transition duration-300 flex flex-col h-full">
                    <div
                        class="bg-gradient-to-r from-indigo-50 to-white p-6 border-b border-indigo-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-indigo-800 flex items-center">
                                <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg mr-3 text-lg">📝</span>
                                Tugas Baru
                            </h3>
                            <p class="text-xs text-indigo-400 mt-1 ml-12">Apa yang harus diselesaikan?</p>
                        </div>
                    </div>

                    <div class="p-6 flex-grow flex flex-col">
                        <form action="{{ route('tugas.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="relative group">
                                <input type="text" name="nama_tugas"
                                    class="block w-full pl-4 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-colors peer"
                                    placeholder=" " required>
                                <label
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-transparent px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Nama
                                    Tugas</label>
                            </div>

                            <div class="flex space-x-3">
                                <div class="w-full">
                                    <input type="date" name="deadline"
                                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm text-gray-600 focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                        required>
                                </div>
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-indigo-200 transform active:scale-95 transition-all duration-200 flex items-center justify-center">
                                    <span>+</span>
                                </button>
                            </div>
                        </form>

                        <div class="mt-8 flex-grow">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Daftar Tugas Anda
                            </h4>
                            <div class="space-y-3 max-h-[300px] overflow-y-auto no-scrollbar pr-2">
                                @forelse($tugas as $t)
                                    <div
                                        class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-200 cursor-default">
                                        <div class="flex items-center space-x-3 overflow-hidden">
                                            <div class="flex-shrink-0 w-2 h-10 bg-indigo-500 rounded-full"></div>
                                            <div class="truncate">
                                                <p
                                                    class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors truncate">
                                                    {{ $t->nama_tugas }}</p>
                                                <p class="text-xs text-gray-500 flex items-center mt-1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $t->deadline }}
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="text-xs font-semibold px-2 py-1 bg-indigo-50 text-indigo-600 rounded-md">Active</span>
                                    </div>
                                @empty
                                    <div class="text-center py-8 opacity-50">
                                        <div class="text-4xl mb-2">🍃</div>
                                        <p class="text-sm text-gray-500">Belum ada tugas. Santai dulu!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden animate-fade-in delay-200 hover:shadow-xl transition duration-300 flex flex-col h-full">
                    <div
                        class="bg-gradient-to-r from-emerald-50 to-white p-6 border-b border-emerald-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-emerald-800 flex items-center">
                                <span class="bg-emerald-100 text-emerald-600 p-2 rounded-lg mr-3 text-lg">🎉</span>
                                Acara Seru
                            </h3>
                            <p class="text-xs text-emerald-500 mt-1 ml-12">Jangan sampai terlewat!</p>
                        </div>
                    </div>

                    <div class="p-6 flex-grow flex flex-col">
                        <form action="{{ route('acara.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="relative group">
                                <input type="text" name="nama_acara"
                                    class="block w-full pl-4 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm focus:border-emerald-500 focus:ring-emerald-500 focus:bg-white transition-colors peer"
                                    placeholder=" " required>
                                <label
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-transparent px-2 peer-focus:px-2 peer-focus:text-emerald-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Nama
                                    Acara</label>
                            </div>

                            <div class="flex space-x-3">
                                <div class="w-full">
                                    <input type="date" name="tanggal"
                                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm text-gray-600 focus:border-emerald-500 focus:ring-emerald-500 transition-colors"
                                        required>
                                </div>
                                <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-emerald-200 transform active:scale-95 transition-all duration-200 flex items-center justify-center">
                                    <span>+</span>
                                </button>
                            </div>
                        </form>

                        <div class="mt-8 flex-grow">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Agenda Mendatang
                            </h4>
                            <div class="space-y-3 max-h-[300px] overflow-y-auto no-scrollbar pr-2">
                                @forelse($acara as $a)
                                    <div
                                        class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-200 cursor-default">
                                        <div class="flex items-center space-x-3 overflow-hidden">
                                            <div
                                                class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex flex-col items-center justify-center text-emerald-600 font-bold border border-emerald-200">
                                                <span
                                                    class="text-xs uppercase">{{ \Carbon\Carbon::parse($a->tanggal)->format('M') }}</span>
                                                <span
                                                    class="text-lg leading-none">{{ \Carbon\Carbon::parse($a->tanggal)->format('d') }}</span>
                                            </div>
                                            <div class="truncate">
                                                <p
                                                    class="text-sm font-bold text-gray-800 group-hover:text-emerald-700 transition-colors truncate">
                                                    {{ $a->nama_acara }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('l') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 opacity-50">
                                        <div class="text-4xl mb-2">🎈</div>
                                        <p class="text-sm text-gray-500">Belum ada acara. Bikin party dong!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
