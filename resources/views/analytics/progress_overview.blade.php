
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Progress Overview') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border border-gray-200">
                <div class="text-gray-900">
                    <h3 class="text-3xl font-bold mb-6 text-gray-700 flex items-center gap-2">
                        🚀 Tinjauan Kemajuan & Pencapaian
                    </h3>
                    <p class="text-lg text-gray-600 mb-8">
                        Lihat kemajuan tugas Anda dalam jangka panjang (kumulatif) dan persentase penyelesaian keseluruhan.
                    </p>

                    <div class="mb-8 p-6 bg-blue-50 border border-blue-200 rounded-xl shadow-md">
                        <h4 class="text-xl font-bold text-blue-800 mb-3">Progress Keseluruhan Tugas Selesai</h4>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $overallProgressPercent }}%"></div>
                        </div>
                        <p class="text-sm text-blue-600 mt-2 font-semibold">{{ $overallProgressPercent }}% Tugas Selesai Secara Keseluruhan</p>
                    </div>

                    <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-xl shadow-inner">
                        <h4 class="text-lg font-bold text-purple-800 mb-4">Kinerja Tugas Kumulatif (12 Bulan Terakhir)</h4>
                        <div style="height: 400px;">
                            <canvas id="cumulativeChart"></canvas>
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
        const chartData = @json($cumulativeData);
        
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('cumulativeChart');
            
            const data = {
                labels: chartLabels, 
                datasets: [{
                    label: 'Total Tugas Selesai Kumulatif',
                    data: chartData, 
                    fill: true,
                    backgroundColor: 'rgba(167, 139, 250, 0.2)', 
                    borderColor: 'rgb(167, 139, 250)', 
                    tension: 0.3
                }]
            };

            const config = {
                type: 'line', 
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Tugas Selesai'
                            },
                            ticks: {
                                precision: 0 // Memastikan sumbu Y hanya menampilkan angka bulat
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
</x-app-layout>