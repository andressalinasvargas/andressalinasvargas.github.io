/* ══════════════════════════════════════
   quiz.js — Lógica del Quiz de conocimiento
   Elicitación de Requisitos
══════════════════════════════════════ */

/* ─── DATA ─── */
const quizData = [
  {
    q: "¿Cuál es el objetivo principal de la elicitación de requisitos?",
    opts: [
      "Diseñar la base de datos del sistema",
      "Descubrir y capturar las necesidades reales de los stakeholders",
      "Codificar el sistema de software",
      "Documentar el plan de pruebas"
    ],
    ans: 1,
    fb: "La elicitación busca identificar, recopilar y articular las necesidades reales de los stakeholders, no simplemente lo que dicen querer."
  },
  {
    q: "¿Qué distingue a la técnica de Etnografía respecto a otras técnicas de observación?",
    opts: [
      "Es una encuesta en línea",
      "El analista se integra de forma prolongada en el entorno del usuario",
      "Consiste en revisar documentos legados",
      "Es una lluvia de ideas con stakeholders"
    ],
    ans: 1,
    fb: "La etnografía implica que el analista convive con los usuarios durante un período extendido para descubrir patrones culturales y comportamientos naturales."
  },
  {
    q: "¿En qué se diferencia el Brainstorming del método SCAMPER?",
    opts: [
      "Brainstorming es silencioso; SCAMPER es verbal",
      "Brainstorming es libre y sin restricciones; SCAMPER usa preguntas estructuradas guiadas",
      "SCAMPER es para observación; Brainstorming para documentación",
      "Son exactamente lo mismo"
    ],
    ans: 1,
    fb: "El Brainstorming genera ideas libremente sin crítica, mientras que SCAMPER guía el pensamiento mediante categorías: Sustituir, Combinar, Adaptar, Modificar, Poner en otros usos, Eliminar, Reorganizar."
  },
  {
    q: "¿Cuándo resulta más útil la técnica de Arqueología de Sistemas?",
    opts: [
      "Cuando no existen stakeholders disponibles ni sistemas previos",
      "Al diseñar un sistema completamente innovador sin precedentes",
      "Al migrar o reemplazar un sistema legado con poca documentación actualizada",
      "En la fase de pruebas de software"
    ],
    ans: 2,
    fb: "La arqueología de sistemas permite extraer requisitos implícitos analizando sistemas existentes, código fuente, bases de datos y comportamientos del sistema actual."
  },
  {
    q: "¿Qué significa la sigla JAD en el contexto de elicitación de requisitos?",
    opts: [
      "Java Application Development",
      "Joint Application Development",
      "Just Another Diagram",
      "Journal of Agile Design"
    ],
    ans: 1,
    fb: "JAD (Joint Application Development) son talleres colaborativos donde desarrolladores y usuarios trabajan juntos en tiempo real para definir y validar requisitos."
  },
  {
    q: "¿Cuál es la principal ventaja de las técnicas de observación frente a las entrevistas?",
    opts: [
      "Son más rápidas de ejecutar",
      "Capturan el conocimiento tácito que los usuarios no pueden verbalizar",
      "Requieren menos recursos y planificación",
      "Permiten recolectar datos de más personas simultáneamente"
    ],
    ans: 1,
    fb: "Los usuarios frecuentemente saben cómo hacer su trabajo pero no pueden articularlo verbalmente. La observación directa captura este conocimiento tácito y revela la brecha entre lo que dicen y lo que hacen."
  },
  {
    q: "En Design Thinking, ¿cuál es la primera y más importante fase del proceso?",
    opts: [
      "Prototipar",
      "Definir el problema",
      "Empatizar con el usuario",
      "Testear soluciones"
    ],
    ans: 2,
    fb: "Empatizar es la fase inicial del Design Thinking: comprender profundamente las necesidades, motivaciones y contexto del usuario antes de intentar definir o resolver el problema."
  },
  {
    q: "¿Qué tipo de requisitos suelen identificar principalmente las técnicas centradas en la documentación?",
    opts: [
      "Requisitos innovadores no existentes previamente",
      "Requisitos derivados de regulaciones, normativas y sistemas existentes",
      "Requisitos creativos surgidos de talleres grupales",
      "Requisitos de comportamiento observados en campo"
    ],
    ans: 1,
    fb: "Las técnicas de documentación son especialmente útiles para identificar restricciones legales, normativas y requisitos heredados de sistemas previos que ya están documentados."
  },
  {
    q: "¿Qué técnica consiste en que el usuario piensa en voz alta mientras realiza una tarea?",
    opts: [
      "Shadowing",
      "Análisis de protocolo (Think Aloud)",
      "Cuestionario estructurado",
      "Mapa mental colaborativo"
    ],
    ans: 1,
    fb: "El Análisis de Protocolo o Think Aloud pide al usuario verbalizar su proceso de pensamiento durante la tarea, revelando decisiones, dudas y estrategias mentales que de otro modo serían invisibles."
  },
  {
    q: "¿Cuál es la mejor estrategia general al seleccionar técnicas de elicitación para un proyecto?",
    opts: [
      "Usar únicamente entrevistas, ya que son la técnica más completa",
      "Elegir una sola técnica y aplicarla de forma exhaustiva",
      "Combinar múltiples técnicas complementarias según el contexto",
      "Aplicar siempre las técnicas de observación antes que cualquier otra"
    ],
    ans: 2,
    fb: "Ninguna técnica es suficiente por sí sola. La práctica recomendada es combinar técnicas de diferentes familias para obtener una visión completa y robusta de los requisitos."
  }
];

/* ─── STATE ─── */
let currentQ = 0;
let score    = 0;
let answered = [];

/* ─── RENDER QUESTION ─── */
function renderQuestion() {
  const q = quizData[currentQ];

  document.getElementById('quiz-question-num').textContent = `Pregunta ${currentQ + 1}`;
  document.getElementById('quiz-question').textContent     = q.q;
  document.getElementById('quiz-counter').textContent      = `Pregunta ${currentQ + 1} de ${quizData.length}`;
  document.getElementById('quiz-progress-bar').style.width = `${(currentQ / quizData.length) * 100}%`;

  document.getElementById('quiz-feedback').className = 'hidden';
  document.getElementById('quiz-next').classList.add('hidden');

  // Reset animation
  const card = document.getElementById('quiz-card');
  card.style.animation = 'none';
  requestAnimationFrame(() => { card.style.animation = 'fadeUp 0.35s ease'; });

  // Build options
  const optsEl  = document.getElementById('quiz-options');
  optsEl.innerHTML = '';
  const letters = ['A', 'B', 'C', 'D'];

  q.opts.forEach((opt, i) => {
    const btn = document.createElement('button');
    btn.className = 'quiz-opt';
    btn.innerHTML = `<span class="opt-letter">${letters[i]}</span>${opt}`;
    btn.onclick   = () => answerQuestion(i, btn, q);
    optsEl.appendChild(btn);
  });
}

/* ─── ANSWER ─── */
function answerQuestion(chosen, btn, q) {
  document.querySelectorAll('.quiz-opt').forEach(b => b.disabled = true);
  const correct = chosen === q.ans;

  if (correct) {
    score++;
    btn.classList.add('correct-ans');
    document.getElementById('quiz-score-live').textContent = `⭐ ${score} correctas`;
  } else {
    btn.classList.add('wrong-ans');
    document.querySelectorAll('.quiz-opt')[q.ans].classList.add('correct-ans');
  }

  answered.push({ q: q.q, correct, fb: q.fb });

  const fb = document.getElementById('quiz-feedback');
  fb.className = `quiz-feedback ${correct ? 'correct-fb' : 'wrong-fb'}`;
  fb.innerHTML  = (correct ? '✓ ¡Correcto! ' : '✗ Incorrecto. ') + q.fb;
  fb.classList.remove('hidden');

  const nextBtn = document.getElementById('quiz-next');
  nextBtn.classList.remove('hidden');
  nextBtn.textContent = currentQ < quizData.length - 1
    ? 'Siguiente pregunta →'
    : 'Ver resultados →';
}

/* ─── NEXT QUESTION ─── */
function nextQuestion() {
  currentQ++;
  if (currentQ < quizData.length) {
    renderQuestion();
  } else {
    showResult();
  }
}

/* ─── SHOW RESULT ─── */
function showResult() {
  document.getElementById('quiz-card').classList.add('hidden');
  document.getElementById('quiz-progress-bar').style.width = '100%';
  document.getElementById('quiz-result').classList.remove('hidden');
  document.getElementById('result-num').textContent = score;

  // Animate circle
  const pct          = score / quizData.length;
  const circumference = 339.3;
  const offset       = circumference - (pct * circumference);
  const arc          = document.getElementById('result-arc');
  arc.style.stroke   = pct >= 0.8 ? 'var(--accent5)' : pct >= 0.5 ? 'var(--accent1)' : 'var(--accent4)';
  setTimeout(() => {
    arc.style.transition = 'stroke-dashoffset 1s ease';
    arc.setAttribute('stroke-dashoffset', offset);
  }, 100);

  // Title & message
  let title, msg;
  if      (score === 10) { title = "🏆 ¡Perfecto!";       msg = "Dominaste completamente el tema. ¡Excelente!"; }
  else if (score >= 8)   { title = "⭐ ¡Muy bien!";       msg = "Tienes un sólido conocimiento de las técnicas de elicitación."; }
  else if (score >= 6)   { title = "👍 Buen trabajo";      msg = "Conoces los fundamentos, pero hay áreas por reforzar."; }
  else if (score >= 4)   { title = "📚 Sigue estudiando";  msg = "Repasa las técnicas de observación y creatividad. ¡Puedes mejorar!"; }
  else                   { title = "💪 No te rindas";      msg = "Revisa el material de la página y vuelve a intentarlo."; }

  document.getElementById('result-title').textContent = title;
  document.getElementById('result-msg').textContent   = msg;

  // Breakdown
  const bd = document.getElementById('result-breakdown');
  bd.innerHTML = '';
  answered.forEach((a, i) => {
    const div = document.createElement('div');
    div.className = 'breakdown-item';
    div.innerHTML = `<span class="bd-icon">${a.correct ? '✅' : '❌'}</span>
      <div class="bd-text">
        <strong>P${i + 1}: ${a.q.substring(0, 55)}…</strong>${a.fb}
      </div>`;
    bd.appendChild(div);
  });
}

/* ─── RESET ─── */
function resetQuiz() {
  currentQ = 0; score = 0; answered = [];
  document.getElementById('quiz-result').classList.add('hidden');
  document.getElementById('quiz-card').classList.remove('hidden');
  document.getElementById('quiz-score-live').textContent = '⭐ 0 correctas';
  document.getElementById('result-arc').setAttribute('stroke-dashoffset', '339.3');
  renderQuestion();
}

/* ─── START ─── */
document.addEventListener('DOMContentLoaded', renderQuestion);
