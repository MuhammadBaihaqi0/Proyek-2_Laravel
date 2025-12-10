
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border border-gray-200">
                <div class="text-gray-900">
                    <h3 class="text-3xl font-bold mb-6 text-gray-700 flex items-center gap-2">
                        🗓️ Laporan Bulanan & Ringkasan Kinerja
                    </h3>
                    <p class="text-lg text-gray-600 mb-8">
                        Di sini Anda dapat melihat ringkasan kinerja bulanan dan detail kegiatan Anda.
                    </p>

                    <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-4">
                        <label for="month-select" class="font-bold text-gray-600">Pilih Bulan:</label>
                        <select id="month-select" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option>Desember 2025</option>
                            <option>November 2025</option>
                            </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-indigo-50 p-6 rounded-xl border-l-4 border-indigo-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Total Tugas Bulan Ini</p>
                            <p class="text-3xl font-extrabold text-indigo-800 mt-2">25</p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-xl border-l-4 border-green-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Tugas Selesai</p>
                            <p class="text-3xl font-extrabold text-green-800 mt-2">20</p>
                        </div>
                        <div class="bg-pink-50 p-6 rounded-xl border-l-4 border-pink-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Rasio Penyelesaian</p>
                            <p class="text-3xl font-extrabold text-pink-800 mt-2">80%</p>
                        </div>
                        <div class="bg-emerald-50 p-6 rounded-xl border-l-4 border-emerald-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Acara Terjadwal</p>
                            <p class="text-3xl font-extrabold text-emerald-800 mt-2">5</p>
                        </div>
                    </div>

                    <div class="mt-8 p-6 bg-white border border-gray-200 rounded-xl shadow-md">
                        <h4 class="text-xl font-bold text-gray-700 mb-4">Detail Tugas dan Acara Bulan Ini</h4>
                        <div class="h-48 flex items-center justify-center text-gray-400 border-dashed border-2 border-gray-300 rounded-lg">
                            Daftar detail tugas yang diselesaikan/acara yang dihadiri akan tampil di sini.
                        </div>
                    </div>
                    
                    <a href="{{ route('dashboard') }}" class="mt-10 inline-block text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                        ← Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>