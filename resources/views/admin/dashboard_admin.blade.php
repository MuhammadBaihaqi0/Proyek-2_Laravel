<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }} 🛡️
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-bold uppercase tracking-wider">Total Pengguna</p>
                        <p class="text-4xl font-extrabold mt-1">{{ $total_users }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl text-3xl">👥</div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-bold uppercase">Status Server</p>
                        <p class="text-xl font-bold text-green-600 mt-1">Online 🟢</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Pengguna Terdaftar</h3>
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold">Latest Users</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">User Info</th>
                                <th class="p-4 font-semibold">Username</th>
                                <th class="p-4 font-semibold">Email</th>
                                <th class="p-4 font-semibold">Tanggal Bergabung</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($users as $u)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 text-gray-400 font-mono">#{{ $u->id }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $av = $u->avatar ? asset('storage/' . $u->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($u->username) . '&background=random&color=ffffff&size=128';
                                        @endphp
                                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200" src="{{ $av }}" alt="">
                                        @if($u->id == 1)
                                            <span class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold border border-red-200">ADMIN</span>
                                        @else
                                            <span class="bg-green-100 text-green-600 text-[10px] px-2 py-0.5 rounded-full font-bold border border-green-200">USER</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 font-bold text-gray-700">{{ $u->username }}</td>
                                <td class="p-4 text-gray-500">{{ $u->email }}</td>
                                <td class="p-4 text-gray-600">
                                    <div class="flex flex-col">
                                        <span class="font-semibold">{{ $u->created_at->translatedFormat('d M Y') }}</span>
                                        <span class="text-xs text-gray-400">{{ $u->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>