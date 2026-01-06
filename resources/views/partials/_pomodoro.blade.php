<div id="pomodoro-widget" aria-hidden="false"
    style="position:fixed; right:18px; bottom:18px; z-index:9999; font-family:Inter,system-ui,Segoe UI, Roboto, 'Helvetica Neue', Arial; display: none;">
    <div id="pomodoro-card"
        style="width:280px; background:#ffffff; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.12); padding:14px;">

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
            <div>
                <strong id="pom-title">Pomodoro</strong>
                <div style="font-size:12px; color:#666;" id="pom-mode">Siap</div>
                <div id="pom-active-task" style="font-size:12px; color:#333; margin-top:6px;">Tidak ada tugas aktif</div>
            </div>
            <div style="text-align:right;">
                <div id="pom-timer" style="font-size:20px; font-weight:600;">25:00</div>
            </div>
        </div>

        <div id="pom-main-controls" style="margin-top:12px;">
            <div style="display:flex; gap:8px;">
                <button id="pom-start" class="btn" type="button"
                    style="flex:1; padding:8px; border-radius:8px; border: none; background:#10b981; color:white; cursor:pointer;">Start</button>
                <button id="pom-finish" class="btn" type="button"
                    style="flex:1; padding:8px; border-radius:8px; border:none; background:#2563eb; color:white; cursor:pointer; display:none;">Finish</button>
                <button id="pom-stop" class="btn" type="button"
                    style="flex:1; padding:8px; border-radius:8px; border:none; background:#ef4444; color:white; cursor:pointer;">Stop</button>
            </div>

            <div style="margin-top:8px; display:flex; gap:8px;">
                <button id="pom-pause" type="button"
                    style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd; background:#fff; cursor:pointer;">Pause</button>
            </div>
        </div>

        <div id="pom-settings-panel" style="display:none; margin-top:10px; font-size:13px; color:#444;">
            <div style="display:flex; gap:8px; align-items:center;">
                <label style="display:block; font-size:12px;">Durasi fokus (menit)
                    <input id="pom-focus-min" type="number" min="1" value="25"
                        style="width:70px; margin-left:6px;">
                </label>
                <div style="display:flex; gap:6px;">
                    <button id="pom-confirm-start" type="button"
                        style="padding:6px 10px; border-radius:8px; background:#10b981; color:#fff; border:none; cursor:pointer;">Mulai</button>
                    <button id="pom-cancel" type="button"
                        style="padding:6px 10px; border-radius:8px; background:#fff; color:#111; border:1px solid #ddd; cursor:pointer;">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        /* =====================
         * DEFAULT & STATE
         * ===================== */
        const DEFAULT_FOCUS_MIN = 25;
        const STORAGE_KEY = 'pomodoro_focus_only_v1';

        let state = {
            running: false,
            paused: false,
            endTime: null, // timestamp (ms)
            focusMin: DEFAULT_FOCUS_MIN,
            timerId: null,
            activeTask: null, // { id, title }
            remainingSeconds: null // saved remaining time when stopped
        };

        /* =====================
         * DOM
         * ===================== */
        const widget = document.getElementById('pomodoro-widget');
        const startBtn = document.getElementById('pom-start');
        const stopBtn = document.getElementById('pom-stop');
        const pauseBtn = document.getElementById('pom-pause');
        const settingsBtn = document.getElementById('pom-settings');
        const settingsPanel = document.getElementById('pom-settings-panel');
        const confirmStartBtn = document.getElementById('pom-confirm-start');
        const cancelStartBtn = document.getElementById('pom-cancel');
        const finishBtn = document.getElementById('pom-finish');

        const timerEl = document.getElementById('pom-timer');
        const modeEl = document.getElementById('pom-mode');
        const activeTaskEl = document.getElementById('pom-active-task');
        const focusMinInput = document.getElementById('pom-focus-min');
        const mainControls = document.getElementById('pom-main-controls');

        /* =====================
         * UTIL
         * ===================== */
        function formatTime(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        }

        function notify(title, body) {
            try {
                if ("Notification" in window && Notification.permission === 'granted') {
                    new Notification(title, {
                        body
                    });
                }
            } catch (e) {}
        }

        function askPermission() {
            if ("Notification" in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        }

        /* =====================
         * STORAGE
         * ===================== */
        function saveState() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        }

        function loadState() {
            try {
                const s = JSON.parse(localStorage.getItem(STORAGE_KEY));
                if (s) state = Object.assign(state, s);
            } catch (e) {}
        }

        /* =====================
         * TIMER CORE (REAL TIME)
         * ===================== */
        function tick() {
            if (!state.running || state.paused || !state.endTime) return;

            const now = Date.now();
            const remainingSec = Math.ceil((state.endTime - now) / 1000);

            if (remainingSec <= 0) {
                // Jika ada tugas aktif, beritahu server untuk menyelesaikan session terlebih dahulu
                if (state.activeTask && state.activeTask.id) {
                    finishSessionOnServer(state.activeTask.id).then(() => {
                        notify('Pomodoro selesai', 'Waktu fokus telah berakhir.');
                        stopTimer(true);
                    }).catch(() => {
                        // fallback: tetap stop timer meskipun server gagal
                        notify('Pomodoro selesai', 'Waktu fokus telah berakhir.');
                        stopTimer(true);
                    });
                } else {
                    // tidak ada task terkait, panggil finish tanpa taskId
                    finishSessionOnServer(null).then(() => {
                        notify('Pomodoro selesai', 'Waktu fokus telah berakhir.');
                        stopTimer(true);
                    }).catch(() => {
                        notify('Pomodoro selesai', 'Waktu fokus telah berakhir.');
                        stopTimer(true);
                    });
                }
                return;
            }

            timerEl.textContent = formatTime(remainingSec);
        }

        /* =====================
         * CONTROLS
         * ===================== */
        function startTimer() {
            if (state.running) return;

            state.running = true;
            state.paused = false;

            // if resuming from stopped state, use remaining time; otherwise use full duration
            const durationSec = state.remainingSeconds !== null ? state.remainingSeconds : state.focusMin * 60;
            state.endTime = Date.now() + durationSec * 1000;
            state.remainingSeconds = null; // clear saved remaining time

            widget.style.display = 'block';
            state.timerId = setInterval(tick, 1000);

            // hide settings button while session is running
            if (settingsBtn) settingsBtn.style.display = 'none';

            notify('Pomodoro dimulai', `Fokus selama ${Math.ceil(durationSec / 60)} menit.`);
            saveState();
            updateUI();
        }

        function pauseTimer() {
            if (!state.running) return;

            state.paused = !state.paused;

            if (state.paused) {
                // compute and save remaining seconds, then pause
                if (state.endTime) {
                    const now = Date.now();
                    const remainingSec = Math.ceil((state.endTime - now) / 1000);
                    state.remainingSeconds = Math.max(0, remainingSec);
                }
                clearInterval(state.timerId);
                state.timerId = null;
                // clear endTime so background doesn't keep counting
                state.endTime = null;
            } else {
                // resume from saved remainingSeconds if present
                const durationSec = state.remainingSeconds !== null ? state.remainingSeconds : state.focusMin * 60;
                state.endTime = Date.now() + durationSec * 1000;
                state.remainingSeconds = null;
                state.timerId = setInterval(tick, 1000);
            }

            saveState();
            updateUI();
        }

        function stopTimer(autoFinish = false) {
            clearInterval(state.timerId);

            // if already paused we may already have remainingSeconds saved
            if (!autoFinish) {
                if (state.paused && state.remainingSeconds !== null) {
                    // keep existing remainingSeconds
                } else if (state.endTime) {
                    const now = Date.now();
                    const remainingSec = Math.ceil((state.endTime - now) / 1000);
                    state.remainingSeconds = Math.max(0, remainingSec);
                } else {
                    state.remainingSeconds = null;
                }
            } else {
                state.remainingSeconds = null;
            }

            state.running = false;
            state.paused = false;
            state.endTime = null;
            state.timerId = null;

            // hide finish button when stopped
            if (finishBtn) finishBtn.style.display = 'none';

            // restore settings button visibility when stopped
            if (settingsBtn) settingsBtn.style.display = 'inline-flex';

            // keep activeTask so user can resume the same task later
            widget.style.display = 'none';

            saveState();
            updateUI();
        }

        /* =====================
         * UI
         * ===================== */
        function updateUI() {
            modeEl.textContent = state.running ?
                (state.paused ? 'Paused' : 'Fokus') :
                'Siap';

            activeTaskEl.textContent = state.activeTask ?
                'Sedang mengerjakan: ' + state.activeTask.title :
                'Tidak ada tugas aktif';

            if (!state.running) {
                if (state.remainingSeconds !== null) {
                    timerEl.textContent = formatTime(state.remainingSeconds);
                } else {
                    timerEl.textContent = formatTime(state.focusMin * 60);
                }
            }

            // toggle finish button visibility
            if (finishBtn) {
                finishBtn.style.display = state.running ? 'inline-flex' : 'none';
            }
        }

        /* =====================
         * BACKEND
         * ===================== */
        function csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        }

        function post(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf()
                },
                body: JSON.stringify(data),
                credentials: 'same-origin'
            });
        }

        function startSessionOnServer(id) {
            return post('/pomodoro/start', {
                task_id: id
            });
        }

        async function finishSessionOnServer(taskId) {
            console.log('finishSessionOnServer called with taskId:', taskId);

            // Akhiri session di server
            try {
                const res = await post('/pomodoro/finish', {
                    task_id: taskId || null
                });
                const data = await res.json();
                console.log('Pomodoro finish response:', data);
            } catch (e) {
                console.warn('Gagal mengakhiri session di server', e);
            }

            // Jika ada tugas terkait, tandai tugas selesai (patch)
            if (taskId) {
                try {
                    const res = await fetch(`/tugas/${taskId}/selesai`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrf(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    const data = await res.json();
                    console.log('Task selesai response:', data);
                } catch (e) {
                    console.warn('Gagal menandai tugas selesai', e);
                }
            }

            // Wait a bit then update statistics to ensure data is saved
            await new Promise(r => setTimeout(r, 500));
            await updateStatisticLive();
            console.log('Statistics updated');
        }

        async function updateStatisticLive() {
            try {
                const res = await fetch('/statistik/pomodoro-live', {
                    credentials: 'same-origin'
                });
                const data = await res.json();

                // Update Total Fokus Hari Ini
                const todayEl = document.getElementById('totalFocusToday');
                if (todayEl) {
                    todayEl.textContent = data.today_minutes + ' menit';
                }

                // Update Rata-rata Mingguan
                const avgEl = document.getElementById('avgFocusWeek');
                if (avgEl) {
                    avgEl.textContent = data.avg_week_minutes + ' menit / hari';
                }

                // Update Pomodoro Hari Ini
                const pomTodayEl = document.getElementById('pomodoroToday');
                if (pomTodayEl && typeof data.today_minutes !== 'undefined') {
                    pomTodayEl.textContent = `${data.today_minutes} menit`;
                }

                // Update Pomodoro Minggu Ini
                const pomWeekEl = document.getElementById('pomodoroWeek');
                if (pomWeekEl) {
                    if (typeof data.week_total_minutes !== 'undefined') {
                        pomWeekEl.textContent = `${data.week_total_minutes} menit`;
                    } else if (typeof data.avg_week_minutes !== 'undefined') {
                        pomWeekEl.textContent = `${(data.avg_week_minutes * 7)} menit`;
                    }
                }

                console.log('Statistics updated:', data);
            } catch (e) {
                console.warn('Live statistic update failed', e);
            }
        }


        /* =====================
         * PUBLIC API
         * ===================== */
        window.startTaskPomodoro = function(id, title) {
            // Set active task (may be null), show widget and open choose-duration panel
            state.activeTask = id ? {
                id,
                title
            } : null;
            if (widget) widget.style.display = 'block';
            // hide primary controls and show only duration panel (Mulai / Batal)
            if (mainControls) mainControls.style.display = 'none';
            if (settingsPanel) settingsPanel.style.display = 'block';
            focusMinInput.focus();
            updateUI();
        };

        window.stopTaskPomodoro = function(id) {
            // Stop locally without marking task finished on server
            stopTimer();
        };

        /* =====================
         * EVENTS
         * ===================== */
        // Start button shows the duration panel when not running, or pauses/resumes when running
        startBtn.onclick = () => {
            if (state.running) {
                // when running, pressing start toggles pause
                pauseTimer();
                return;
            }
            if (settingsPanel) {
                settingsPanel.style.display = 'block';
                focusMinInput.focus();
            }
        };

        // Confirm start after choosing duration
        if (confirmStartBtn) {
            confirmStartBtn.onclick = () => {
                state.focusMin = Math.max(1, parseInt(focusMinInput.value) || DEFAULT_FOCUS_MIN);
                if (state.activeTask && state.activeTask.id) {
                    startSessionOnServer(state.activeTask.id).catch(e => console.warn(e));
                }
                startTimer();
                // after confirming start, hide settings and reveal controls
                if (settingsPanel) settingsPanel.style.display = 'none';
                if (mainControls) mainControls.style.display = 'block';
            };
        }

        if (cancelStartBtn) {
            cancelStartBtn.onclick = () => {
                // Cancel choosing duration: hide panel and close widget
                if (settingsPanel) settingsPanel.style.display = 'none';
                if (mainControls) mainControls.style.display = 'none';
                if (widget) widget.style.display = 'none';
            };
        }

        // Finish button: end session and mark task complete
        if (finishBtn) {
            finishBtn.onclick = async () => {
                const taskId = state.activeTask?.id;
                if (taskId) {
                    await finishSessionOnServer(taskId);
                } else {
                    await finishSessionOnServer(null);
                }
                stopTimer(true);
                notify('Tugas selesai', 'Tugas telah ditandai sebagai selesai.');
            };
        }
        pauseBtn.onclick = pauseTimer;
        stopBtn.onclick = () => window.stopTaskPomodoro(state.activeTask?.id);
        settingsBtn.onclick = () => {
            if (settingsPanel) {
                settingsPanel.style.display =
                    settingsPanel.style.display === 'none' ? 'block' : 'none';
            }
        };

        /* =====================
         * INIT
         * ===================== */
        loadState();
        focusMinInput.value = state.focusMin || DEFAULT_FOCUS_MIN;
        updateUI();

        if (state.running && state.endTime) {
            widget.style.display = 'block';
            state.timerId = setInterval(tick, 1000);
        }

    })();
</script>
