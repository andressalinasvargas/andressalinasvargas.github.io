/* main.js */

// Tabs de tipos
function showTipo(id) {
  document.querySelectorAll('.tpanel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.ttab').forEach(b => b.classList.remove('active'));
  document.getElementById('tp-' + id).classList.add('active');
  event.target.classList.add('active');
}

// Scroll reveal
document.addEventListener('DOMContentLoaded', () => {
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
    });
  }, { threshold: 0.08 });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));
});
