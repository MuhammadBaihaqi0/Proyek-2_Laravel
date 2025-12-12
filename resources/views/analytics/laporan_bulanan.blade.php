

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
                        🗓️ Laporan Bulanan: {{ $currentMonthYear }}
                    </h3>
                    <p class="text-lg text-gray-600 mb-8">
                        Di sini Anda dapat melihat ringkasan kinerja bulanan dan detail kegiatan Anda.
                    </p>

                    <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-4">
                        <label for="month-select" class="font-bold text-gray-600">Pilih Bulan:</label>
                        <select id="month-select" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @foreach ($availableMonths as $month)
                                <option 
                                    value="{{ $month['year'] }}/{{ $month['month'] }}"
                                    {{ $month['year'] == $selectedYear && $month['month'] == $selectedMonth ? 'selected' : '' }}
                                >
                                    {{ $month['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-indigo-50 p-6 rounded-xl border-l-4 border-indigo-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Total Tugas Dibuat</p>
                            <p class="text-3xl font-extrabold text-indigo-800 mt-2">{{ $totalTugasBulanIni }}</p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-xl border-l-4 border-green-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Tugas Selesai</p>
                            <p class="text-3xl font-extrabold text-green-800 mt-2">{{ $tugasSelesaiBulanIni->count() }}</p>
                        </div>
                        <div class="bg-pink-50 p-6 rounded-xl border-l-4 border-pink-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Rasio Penyelesaian</p>
                            <p class="text-3xl font-extrabold text-pink-800 mt-2">{{ $rasioPenyelesaianBulanIni }}%</p>
                        </div>
                        <div class="bg-emerald-50 p-6 rounded-xl border-l-4 border-emerald-500 shadow-sm">
                            <p class="text-xs text-gray-600 font-bold uppercase">Acara Terjadwal</p>
                            <p class="text-3xl font-extrabold text-emerald-800 mt-2">{{ $acaraBulanIni->count() }}</p>
                        </div>
                    </div>

                    <div class="mt-8 p-6 bg-white border border-gray-200 rounded-xl shadow-md">
                        <h4 class="text-xl font-bold text-gray-700 mb-4">Detail Tugas dan Acara Bulan Ini</h4>
                        
                        @if ($tugasSelesaiBulanIni->isEmpty() && $acaraBulanIni->isEmpty())
                            <div class="py-10 text-center text-gray-500 italic">
                                Tidak ada tugas yang diselesaikan atau acara yang dihadiri pada {{ $currentMonthYear }}.
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="font-bold text-green-700 mb-2 border-b pb-1">Tugas Selesai ({{ $tugasSelesaiBulanIni->count() }})</h5>
                                    <ul class="space-y-2 max-h-64 overflow-y-auto no-scrollbar pr-2">
                                        @foreach ($tugasSelesaiBulanIni as $tugas)
                                            <li class="bg-green-50 p-3 rounded-lg text-sm flex justify-between items-center">
                                                <span class="font-medium text-gray-700 truncate">{{ $tugas->nama_tugas }}</span>
                                                <span class="text-xs text-green-600 font-bold flex-shrink-0 ml-2">Selesai: {{ \Carbon\Carbon::parse($tugas->updated_at)->format('d M') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-700 mb-2 border-b pb-1">Acara Terjadwal ({{ $acaraBulanIni->count() }})</h5>
                                    <ul class="space-y-2 max-h-64 overflow-y-auto no-scrollbar pr-2">
                                        @foreach ($acaraBulanIni as $acara)
                                            <li class="bg-emerald-50 p-3 rounded-lg text-sm flex justify-between items-center">
                                                <span class="font-medium text-gray-700 truncate">{{ $acara->nama_acara }}</span>
                                                <span class="text-xs text-emerald-600 font-bold flex-shrink-0 ml-2">Tanggal: {{ \Carbon\Carbon::parse($acara->tanggal)->format('d M') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <a href="{{ route('dashboard') }}" class="mt-10 inline-block text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                        ← Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('month-select');
            
            if (selectElement) {
                selectElement.addEventListener('change', function() {
                    const selectedValue = this.value; // Format: YYYY/M
                    // Arahkan browser ke rute baru dengan parameter bulan/tahun
                    window.location.href = "{{ url('/laporan-bulanan') }}/" + selectedValue;
                });
            }
        });
    </script>
</x-app-layout>