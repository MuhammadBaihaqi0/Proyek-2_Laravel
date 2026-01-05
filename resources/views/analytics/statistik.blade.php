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
                        Di halaman ini, Anda dapat melihat visualisasi data mengenai progres dan produktivitas tugas
                        Anda.
                    </p>

                    <div class="bg-white p-6 rounded-2xl shadow border border-gray-100">
                        <h3 class="font-bold text-gray-700 mb-4">
                            ⏱️ Waktu Fokus Harian (Minggu Ini)
                        </h3>

                        <div class="relative h-[320px]">
                            <canvas id="taskDailyChart"></canvas>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-6">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Pomodoro Hari Ini</p>
                            <p id="pomodoroToday" class="text-2xl font-bold text-indigo-600">
                                — menit
                            </p>
                            <p class="text-xs text-gray-500 uppercase font-bold mt-4">Pomodoro Minggu Ini (total)</p>
                            <p id="pomodoroWeek" class="text-2xl font-bold text-emerald-600">
                                — menit
                            </p>
                        </div>
                    </div>




                    <a href="{{ route('dashboard') }}"
                        class="mt-10 inline-block text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                        ← Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('taskDailyChart');
            if (!ctx) return;

            const labels = @json($labels);
            const dataValues = @json($dataFocusMinutes);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tugas Selesai',
                        data: dataValues,
                        borderRadius: 6,
                        barThickness: 10,
                        maxBarThickness: 16,
                        backgroundColor: dataValues.map(v => v > 0 ? '#6366f1' : '#e5e7eb')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 10,
                                color: '#6b7280',
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: '#e5e7eb'
                            }
                        }
                    },

                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleColor: '#fff',
                            bodyColor: '#e5e7eb',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.parsed.y} tugas selesai`;
                                }
                            }
                        }
                    }
                }
            });

            // Ambil data Pomodoro live dari endpoint dan update elemen
            fetch('/statistik/pomodoro-live', {
                headers: {
                    'Accept': 'application/json'
                }
            }).then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            }).then(data => {
                const todayEl = document.getElementById('pomodoroToday');
                const weekEl = document.getElementById('pomodoroWeek');
                if (todayEl && typeof data.today_minutes !== 'undefined') {
                    todayEl.textContent = `${data.today_minutes} menit`;
                }
                if (weekEl) {
                    // Preferensi: tampilkan total minggu jika tersedia, jika tidak gunakan avg*7
                    if (typeof data.week_total_minutes !== 'undefined') {
                        weekEl.textContent = `${data.week_total_minutes} menit`;
                    } else if (typeof data.avg_week_minutes !== 'undefined') {
                        weekEl.textContent = `${(data.avg_week_minutes * 7)} menit`;
                    }
                }
            }).catch(err => {
                console.error('Gagal memuat data Pomodoro:', err);
            });
        });
    </script>

</x-app-layout>
