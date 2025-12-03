{{-- resources/views/partials/_pomodoro.blade.php --}}
{{-- Partial Pomodoro — menampilkan widget floating + sinkronisasi backend --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
  /* very small scoped reset for widget */
  #pomodoro-widget *{ box-sizing:border-box; }
</style>

<div id="pomodoro-widget" aria-hidden="false" style="position:fixed; right:18px; bottom:18px; z-index:9999; font-family:Inter,system-ui,Segoe UI, Roboto, 'Helvetica Neue', Arial;">
  <div id="pomodoro-card" style="width:280px; background:#ffffff; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.12); padding:14px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
      <div>
        <strong id="pom-title">Pomodoro</strong>
        <div style="font-size:12px; color:#666;" id="pom-mode">Siap</div>
        <div id="pom-active-task" style="font-size:12px; color:#333; margin-top:6px;">Tidak ada tugas aktif</div>
      </div>
      <div style="text-align:right;">
        <div id="pom-timer" style="font-size:20px; font-weight:600;">25:00</div>
        <div style="font-size:11px; color:#888;" id="pom-cycle">Siklus: 0</div>
      </div>
    </div>

    <div style="margin-top:12px; display:flex; gap:8px;">
      <button id="pom-start" class="btn" style="flex:1; padding:8px; border-radius:8px; border: none; background:#10b981; color:white; cursor:pointer;">Start Work</button>
      <button id="pom-stop" class="btn" style="flex:1; padding:8px; border-radius:8px; border:none; background:#ef4444; color:white; cursor:pointer;">Stop</button>
    </div>

    <div style="margin-top:8px; display:flex; gap:8px;">
      <button id="pom-pause" style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd; background:#fff; cursor:pointer;">Pause</button>
      <button id="pom-settings" style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd; background:#fff; cursor:pointer;">Settings</button>
    </div>

    <div id="pom-settings-panel" style="display:none; margin-top:10px; font-size:13px; color:#444;">
      <label style="display:block; font-size:12px;">Durasi fokus (menit)
        <input id="pom-focus-min" type="number" min="1" value="25" style="width:70px; margin-left:6px;">
      </label>
      <label style="display:block; font-size:12px; margin-top:6px;">Durasi istirahat singkat (menit)
        <input id="pom-short-min" type="number" min="1" value="5" style="width:70px; margin-left:6px;">
      </label>
      <label style="display:block; font-size:12px; margin-top:6px;">Durasi istirahat panjang (menit)
        <input id="pom-long-min" type="number" min="1" value="15" style="width:70px; margin-left:6px;">
      </label>
      <label style="display:block; font-size:12px; margin-top:6px;">
        Long break tiap <input id="pom-cycles-before-long" type="number" min="1" value="4" style="width:50px; margin-left:6px;"> siklus
      </label>
    </div>
  </div>
</div>

<audio id="pom-sound">
  <source src="https://actions.google.com/sounds/v1/alarms/medium_bell_ring.ogg" type="audio/ogg">
</audio>

<script>
(function(){
  // Defaults & state
  const DEFAULTS = { focusMin:25, shortMin:5, longMin:15, cyclesBeforeLong:4 };
  const STORAGE_KEY = 'pomodoro_state_v1';

  // DOM
  const startBtn = document.getElementById('pom-start');
  const stopBtn = document.getElementById('pom-stop');
  const pauseBtn = document.getElementById('pom-pause');
  const settingsBtn = document.getElementById('pom-settings');
  const settingsPanel = document.getElementById('pom-settings-panel');
  const modeEl = document.getElementById('pom-mode');
  const timerEl = document.getElementById('pom-timer');
  const cycleEl = document.getElementById('pom-cycle');
  const sound = document.getElementById('pom-sound');
  const activeTaskEl = document.getElementById('pom-active-task');

  const focusMinInput = document.getElementById('pom-focus-min');
  const shortMinInput = document.getElementById('pom-short-min');
  const longMinInput = document.getElementById('pom-long-min');
  const cyclesBeforeLongInput = document.getElementById('pom-cycles-before-long');

  let state = {
    running:false, paused:false, mode:'focus',
    remaining: DEFAULTS.focusMin * 60,
    cycleCount:0, settings:{...DEFAULTS}, timerId:null, lastTick:null,
    activeTask: null  // { id, title }
  };

  // --- persistence ---
  function saveState(){
    const s = Object.assign({}, state);
    // don't serialize timerId
    s.timerId = null;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(s));
  }
  function loadState(){
    try{
      const s = JSON.parse(localStorage.getItem(STORAGE_KEY));
      if(s){
        state = Object.assign(state, s);
      } else {
        state.settings = {...DEFAULTS};
        state.remaining = state.settings.focusMin * 60;
      }
    }catch(e){ console.warn('pom: load failed', e); }
  }

  // --- helpers ---
  function formatTime(sec){
    const m = Math.floor(sec/60); const s = Math.floor(sec%60);
    return String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
  }
  function askNotificationPermission(){ if(!("Notification" in window)) return; if(Notification.permission === 'default') Notification.requestPermission(); }
  function playNotify(){
    try{ sound.currentTime = 0; sound.play().catch(()=>{}); }catch(e){}
  }
  function notify(title, body){
    try{
      if(window.Notification && Notification.permission === 'granted'){
        new Notification(title, { body });
      }
    }catch(e){}
    playNotify();
    // flash title briefly
    const prev = document.title; let c=0;
    const id = setInterval(()=>{ document.title = (c%2===0? '🔔 '+title : prev); if(++c>4){ clearInterval(id); document.title = prev; } }, 700);
  }

  // --- timer core ---
  function tick(){
    if(!state.lastTick) state.lastTick = Date.now();
    const now = Date.now();
    const elapsed = Math.floor((now - state.lastTick)/1000);
    if(!state.paused){
      state.remaining = Math.max(0, state.remaining - elapsed);
      state.lastTick = now;
    }

    if(state.remaining <= 0 && !state.paused){
      // transition
      if(state.mode === 'focus'){
        state.cycleCount++;
        if(state.cycleCount % state.settings.cyclesBeforeLong === 0){
          state.mode = 'long';
          state.remaining = state.settings.longMin * 60;
          notify('Pomodoro — Fokus selesai', 'Istirahat panjang dimulai.');
        } else {
          state.mode = 'short';
          state.remaining = state.settings.shortMin * 60;
          notify('Pomodoro — Fokus selesai', 'Istirahat singkat dimulai.');
        }
      } else {
        state.mode = 'focus';
        state.remaining = state.settings.focusMin * 60;
        notify('Pomodoro — Istirahat selesai', 'Kembali fokus sekarang.');
      }
      state.lastTick = Date.now();
    }

    saveState();
    updateUI();
  }

  function startTimer(){
    if(state.running) return;
    state.running = true; state.paused = false; state.lastTick = Date.now();
    tick();
    state.timerId = setInterval(tick, 1000);
    saveState();
  }
  function pauseTimer(){
    if(!state.running || state.paused) return;
    state.paused = true;
    clearInterval(state.timerId); state.timerId = null;
    if(state.lastTick){ const elapsed = Math.floor((Date.now() - state.lastTick)/1000); state.remaining = Math.max(0, state.remaining - elapsed); }
    state.lastTick = null;
    saveState(); updateUI();
  }
  function resumeTimer(){
    if(!state.running || !state.paused) return;
    state.paused = false; state.lastTick = Date.now();
    state.timerId = setInterval(tick, 1000);
    saveState(); updateUI();
  }
  function stopTimer(){
    state.running = false; state.paused = false;
    clearInterval(state.timerId); state.timerId = null;
    state.mode = 'focus'; state.cycleCount = 0;
    state.remaining = state.settings.focusMin * 60;
    state.lastTick = null;
    state.activeTask = null;
    saveState(); updateUI();
  }

  // --- UI updates ---
  function updateUI(){
    timerEl.textContent = formatTime(state.remaining);
    cycleEl.textContent = 'Siklus: ' + state.cycleCount;
    modeEl.textContent = !state.running ? 'Siap' : (state.paused ? 'Paused — ' + state.mode : (state.mode==='focus' ? 'Fokus' : state.mode==='short' ? 'Istirahat singkat' : 'Istirahat panjang'));
    // active task label
    activeTaskEl.textContent = state.activeTask && state.activeTask.title ? ('Sedang: ' + state.activeTask.title) : 'Tidak ada tugas aktif';
    // button labels
    if(!state.running){ startBtn.textContent = 'Start Work'; pauseBtn.textContent = 'Pause'; }
    else { startBtn.textContent = state.paused ? 'Resume Work' : 'Working...'; pauseBtn.textContent = state.paused ? 'Resume' : 'Pause'; }
  }

  // --- Bind UI ---
  startBtn.addEventListener('click', function(){
    askNotificationPermission();
    // read settings
    state.settings.focusMin = Math.max(1, parseInt(focusMinInput.value) || DEFAULTS.focusMin);
    state.settings.shortMin = Math.max(1, parseInt(shortMinInput.value) || DEFAULTS.shortMin);
    state.settings.longMin = Math.max(1, parseInt(longMinInput.value) || DEFAULTS.longMin);
    state.settings.cyclesBeforeLong = Math.max(1, parseInt(cyclesBeforeLongInput.value) || DEFAULTS.cyclesBeforeLong);

    if(!state.running){
      state.mode = 'focus'; state.remaining = state.settings.focusMin * 60; startTimer(); notify('Pomodoro dimulai', 'Fokus selama ' + state.settings.focusMin + ' menit.');
    } else if(state.paused) resumeTimer();
    updateUI();
  });

  pauseBtn.addEventListener('click', function(){ if(!state.running) return; if(state.paused) resumeTimer(); else pauseTimer(); });
  stopBtn.addEventListener('click', function(){ // when user stops, notify backend to finish for the active task if any
    if(state.activeTask && state.activeTask.id){
      finishSessionOnServer(state.activeTask.id).catch(()=>{});
    }
    stopTimer();
  });
  settingsBtn.addEventListener('click', function(){ settingsPanel.style.display = (settingsPanel.style.display === 'none' ? 'block' : 'none'); });

  // --- Backend helpers ---
  function getCsrfToken(){
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : null;
  }
  async function postJson(url, data){
    const token = getCsrfToken();
    const headers = {'Content-Type':'application/json'};
    if(token) headers['X-CSRF-TOKEN'] = token;
    try{
      const resp = await fetch(url, { method:'POST', headers, body: JSON.stringify(data), credentials:'same-origin' });
      return resp.ok ? await resp.json().catch(()=>null) : null;
    }catch(e){ console.warn('postJson error', e); return null; }
  }

  async function startSessionOnServer(taskId){
    try{
      await postJson('/pomodoro/start', { task_id: taskId || null });
    }catch(e){ console.warn('startSessionOnServer', e); }
  }
  async function finishSessionOnServer(taskId){
    try{
      await postJson('/pomodoro/finish', { task_id: taskId || null });
    }catch(e){ console.warn('finishSessionOnServer', e); }
  }
  async function focusEndedOnServer(taskId, mode){
    try{
      await postJson('/pomodoro/focus-ended', { task_id: taskId || null, mode: mode });
    }catch(e){ console.warn('focusEndedOnServer', e); }
  }

  // --- Public API for other scripts ---
  window.startTaskPomodoro = async function(taskId, taskTitle){
    // set active task in widget
    state.activeTask = taskId ? { id: taskId, title: taskTitle || ('Task ' + taskId) } : null;
    // ensure widget settings reflect saved settings
    focusMinInput.value = state.settings.focusMin || DEFAULTS.focusMin;
    shortMinInput.value = state.settings.shortMin || DEFAULTS.shortMin;
    longMinInput.value = state.settings.longMin || DEFAULTS.longMin;
    cyclesBeforeLongInput.value = state.settings.cyclesBeforeLong || DEFAULTS.cyclesBeforeLong;

    // start UI timer
    askNotificationPermission();
    if(!state.running) startTimer(); else if(state.paused) resumeTimer();
    updateUI();

    // notify backend
    if(taskId) await startSessionOnServer(taskId);
    else await startSessionOnServer(null);
  };

  window.stopTaskPomodoro = async function(taskId){
    if(taskId) await finishSessionOnServer(taskId);
    stopTimer();
  };

  window.getPomodoroState = () => {
    // shallow copy excluding timerId
    const copy = Object.assign({}, state);
    copy.timerId = null;
    return copy;
  };

  // --- Polling to sync lifecycle events (for widget started manually) ---
  let lastState = null;
  setInterval(async function(){
    try{
      const s = window.getPomodoroState ? window.getPomodoroState() : state;
      if(!lastState){ lastState = JSON.parse(JSON.stringify(s)); return; }

      // start (user clicked on widget directly)
      if(!lastState.running && s.running){
        await startSessionOnServer(s.activeTask ? s.activeTask.id : null);
      }
      // focus ended -> break started
      if(lastState.mode === 'focus' && s.mode !== 'focus'){
        await focusEndedOnServer(s.activeTask ? s.activeTask.id : null, s.mode);
      }
      // stopped
      if(lastState.running && !s.running){
        await finishSessionOnServer(lastState.activeTask ? lastState.activeTask.id : null);
      }
      lastState = JSON.parse(JSON.stringify(s));
    }catch(e){
      // non-fatal
      //console.warn('pom sync poll error', e);
    }
  }, 2000);

  // --- auto-bind buttons with data-start-pomodoro / data-stop-pomodoro ---
  document.addEventListener('click', function(e){
    const startBtn = e.target.closest && e.target.closest('[data-start-pomodoro]');
    if(startBtn){
      const id = startBtn.getAttribute('data-start-pomodoro') || null;
      const title = startBtn.getAttribute('data-task-title') || startBtn.getAttribute('data-task-name') || null;
      // call public api
      window.startTaskPomodoro(id ? id : null, title);
    }
    const stopBtn = e.target.closest && e.target.closest('[data-stop-pomodoro]');
    if(stopBtn){
      const id = stopBtn.getAttribute('data-stop-pomodoro') || null;
      window.stopTaskPomodoro(id ? id : null);
    }
  });

  // --- initialize widget from saved state ---
  loadState();
  // apply settings to inputs
  focusMinInput.value = state.settings.focusMin || DEFAULTS.focusMin;
  shortMinInput.value = state.settings.shortMin || DEFAULTS.shortMin;
  longMinInput.value = state.settings.longMin || DEFAULTS.longMin;
  cyclesBeforeLongInput.value = state.settings.cyclesBeforeLong || DEFAULTS.cyclesBeforeLong;

  // resume timer if it was running
  if(state.running && !state.paused){
    if(state.lastTick){
      const elapsedSince = Math.floor((Date.now() - state.lastTick)/1000);
      state.remaining = Math.max(0, state.remaining - elapsedSince);
      state.lastTick = Date.now();
    }
    state.timerId = setInterval(tick, 1000);
  }
  updateUI();

})();
</script>
