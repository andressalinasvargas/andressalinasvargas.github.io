/* ══════════════════════════════════════
   animations.js — Animaciones de scroll y tarjetas
   Elicitación de Requisitos
══════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

  /* ── Section reveal on scroll ── */
  const sections = document.querySelectorAll('.section');
  const sectionObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) e.target.classList.add('visible');
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -50px 0px' });
  sections.forEach(s => sectionObs.observe(s));

  /* ── Card stagger on scroll ── */
  const cards = document.querySelectorAll('.card, .step');
  const cardObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const idx = Array.from(cards).indexOf(e.target);
        setTimeout(() => {
          e.target.style.opacity = '1';
          e.target.style.transform = 'translateY(0)';
        }, 80 * (idx % 6));
        cardObs.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });

  cards.forEach(c => {
    c.style.opacity = '0';
    c.style.transform = 'translateY(20px)';
    c.style.transition = 'opacity 0.5s ease, transform 0.5s ease, border-color 0.25s, box-shadow 0.25s';
    cardObs.observe(c);
  });

});
