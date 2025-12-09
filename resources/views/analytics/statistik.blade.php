
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistik Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border border-gray-200">
                <div class="text-gray-900">
                    <h3 class="text-3xl font-bold mb-6 text-gray-700 flex items-center gap-2">
                        📊 Statistik Kinerja Tugas Anda
                    </h3>
                    <p class="text-lg text-gray-600 mb-8">
                        Di halaman ini, Anda dapat melihat visualisasi data mengenai progres dan produktivitas tugas Anda.
                    </p>

                    <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-xl shadow-inner">
                        <h4 class="text-lg font-bold text-indigo-800 mb-4">Tugas Selesai per Bulan (6 Bulan Terakhir)</h4>
                        <div style="height: 400px;">
                            <canvas id="tasksChart"></canvas>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                         <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-sm text-gray-500 uppercase">Total Tugas Selesai</h4>
                            <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $totalSelesai }}</p>
                        </div>
                         <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-sm text-gray-500 uppercase">Waktu Rata-Rata</h4>
                            <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $avgTime }}</p>
                        </div>
                         <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-sm text-gray-500 uppercase">Tugas Aktif</h4>
                            <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $totalAktif }}</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('dashboard') }}" class="mt-10 inline-block text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                        ← Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Ambil data dari PHP menggunakan blade directive
        const chartLabels = @json($labels);
        const chartData = @json($dataCount);

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('tasksChart');

            const data = {
                labels: chartLabels, 
                datasets: [{
                    label: 'Tugas Selesai',
                    data: chartData, 
                    backgroundColor: 'rgba(99, 102, 241, 0.6)', 
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 2,
                    borderRadius: 4,
                }]
            };

            const config = {
                type: 'bar', 
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Tugas'
                            },
                            // Memastikan label y-axis adalah integer (angka bulat)
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
</x-app-layout>