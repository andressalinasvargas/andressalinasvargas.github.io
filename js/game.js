/* ══════════════════════════════════════
   game.js — Lógica del juego "Empareja la Técnica"
   Elicitación de Requisitos
══════════════════════════════════════ */

/* ─── DATA ─── */
const gamePairs = [
  { id: 1, tecnica: "Entrevistas",            desc: "Diálogo directo y estructurado con stakeholders clave",        color: "#7c6af7" },
  { id: 2, tecnica: "Brainstorming",          desc: "Generación libre de ideas sin crítica en grupo",               color: "#c8a96e" },
  { id: 3, tecnica: "Etnografía",             desc: "Integración prolongada del analista en el entorno del usuario", color: "#e8645a" },
  { id: 4, tecnica: "Arqueología de sistemas",desc: "Análisis de sistemas y documentación heredada existente",       color: "#4ecdc4" },
  { id: 5, tecnica: "Cuestionarios",          desc: "Recolección masiva de información mediante formularios",        color: "#7c6af7" },
  { id: 6, tecnica: "Design Thinking",        desc: "Proceso de 5 pasos centrado en empatizar con el usuario",       color: "#c8a96e" },
  { id: 7, tecnica: "Shadowing",              desc: "El analista sigue al usuario durante toda su jornada laboral",  color: "#e8645a" },
  { id: 8, tecnica: "Análisis normativo",     desc: "Identificación de requisitos derivados de leyes y regulaciones",color: "#4ecdc4" },
];

/* ─── STATE ─── */
let selectedTecnica = null;
let matchedPairs    = 0;
let attempts        = 0;
let shuffledDescs   = [];

/* ─── UTILS ─── */
function shuffle(arr) {
  const a = [...arr];
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }
  return a;
}

/* ─── INIT ─── */
function initGame() {
  selectedTecnica = null;
  matchedPairs    = 0;
  attempts        = 0;
  shuffledDescs   = shuffle(gamePairs);
  const shuffledTec = shuffle(gamePairs);

  // Reset stats
  document.getElementById('pairs-found').textContent = `0 / ${gamePairs.length}`;
  document.getElementById('attempts').textContent    = '0';
  document.getElementById('accuracy').textContent    = '—';

  // Reset UI
  document.getElementById('game-message').className = 'game-msg hidden';
  document.getElementById('game-message').textContent = '';
  document.getElementById('win-screen').classList.add('hidden');
  document.getElementById('game-board').classList.remove('hidden');
  document.getElementById('game-stats').classList.remove('hidden');
  document.getElementById('svg-lines').innerHTML = '';

  // Build columns
  const tList = document.getElementById('tecnicas-list');
  const dList = document.getElementById('descripciones-list');
  tList.innerHTML = '';
  dList.innerHTML = '';

  shuffledTec.forEach(p => {
    const el = document.createElement('div');
    el.className     = 'game-item';
    el.textContent   = p.tecnica;
    el.dataset.id    = p.id;
    el.dataset.type  = 'tecnica';
    el.onclick = () => selectTecnica(el, p);
    tList.appendChild(el);
  });

  shuffledDescs.forEach(p => {
    const el = document.createElement('div');
    el.className     = 'game-item';
    el.textContent   = p.desc;
    el.dataset.id    = p.id;
    el.dataset.type  = 'desc';
    el.onclick = () => selectDesc(el, p);
    dList.appendChild(el);
  });
}

/* ─── SELECT HANDLERS ─── */
function selectTecnica(el, pair) {
  if (el.classList.contains('matched')) return;
  document.querySelectorAll('[data-type="tecnica"].selected')
    .forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  selectedTecnica = { el, pair };
  showMsg('');
}

function selectDesc(el, pair) {
  if (!selectedTecnica) {
    showMsg('⬅ Primero selecciona una técnica', 'wrong');
    return;
  }
  if (el.classList.contains('matched')) return;

  attempts++;
  document.getElementById('attempts').textContent = attempts;

  if (selectedTecnica.pair.id === pair.id) {
    // ✅ Correct
    matchedPairs++;
    selectedTecnica.el.classList.remove('selected');
    selectedTecnica.el.classList.add('matched');
    el.classList.add('matched');
    drawLine(selectedTecnica.el, el, pair.color);
    showMsg(`✓ ¡Correcto! "${pair.tecnica}"`, 'correct');
    updateStats();
    if (matchedPairs === gamePairs.length) setTimeout(showWin, 600);
    selectedTecnica = null;
  } else {
    // ❌ Wrong
    el.classList.add('wrong-flash');
    selectedTecnica.el.classList.add('wrong-flash');
    setTimeout(() => {
      el.classList.remove('wrong-flash');
      selectedTecnica.el.classList.remove('wrong-flash', 'selected');
      selectedTecnica = null;
    }, 500);
    showMsg('✗ Incorrecto — intenta de nuevo', 'wrong');
    updateStats();
  }
}

/* ─── DRAW CONNECTOR ─── */
function drawLine(fromEl, toEl, color) {
  const svg       = document.getElementById('svg-lines');
  const boardRect = document.getElementById('game-board').getBoundingClientRect();
  const fromRect  = fromEl.getBoundingClientRect();
  const toRect    = toEl.getBoundingClientRect();

  const y1 = fromRect.top - boardRect.top + fromRect.height / 2;
  const y2 = toRect.top   - boardRect.top + toRect.height  / 2;

  const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
  path.setAttribute("d", `M0,${y1} C40,${y1} 40,${y2} 80,${y2}`);
  path.setAttribute("stroke", color);
  path.setAttribute("stroke-width", "2");
  path.setAttribute("fill", "none");
  path.setAttribute("opacity", "0.7");
  path.setAttribute("stroke-dasharray", "200");
  path.setAttribute("stroke-dashoffset", "200");
  svg.appendChild(path);

  // Animate draw
  let offset = 200;
  const anim = setInterval(() => {
    offset -= 8;
    path.setAttribute("stroke-dashoffset", Math.max(0, offset));
    if (offset <= 0) clearInterval(anim);
  }, 12);
}

/* ─── HELPERS ─── */
function showMsg(text, type = '') {
  const el = document.getElementById('game-message');
  if (!text) { el.className = 'game-msg hidden'; return; }
  el.className = `game-msg ${type}`;
  el.textContent = text;
}

function updateStats() {
  document.getElementById('pairs-found').textContent = `${matchedPairs} / ${gamePairs.length}`;
  const acc = attempts > 0 ? Math.round((matchedPairs / attempts) * 100) : 0;
  document.getElementById('accuracy').textContent = attempts > 0 ? `${acc}%` : '—';
}

function showWin() {
  document.getElementById('game-board').classList.add('hidden');
  document.getElementById('game-message').className = 'game-msg hidden';
  const acc = Math.round((gamePairs.length / attempts) * 100);
  document.getElementById('win-detail').textContent =
    `Completaste los ${gamePairs.length} pares en ${attempts} intentos con ${acc}% de precisión.`;
  document.getElementById('win-screen').classList.remove('hidden');
}

/* ─── START ─── */
document.addEventListener('DOMContentLoaded', initGame);
