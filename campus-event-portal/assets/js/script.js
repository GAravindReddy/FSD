/* CampusVerse — Global Script */

// ── Reveal on scroll
const revealEls = document.querySelectorAll('.reveal');
if (revealEls.length) {
  const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); }
    });
  }, { threshold: 0.15 });
  revealEls.forEach(el => revealObs.observe(el));
}

// ── Flash messages auto-dismiss
document.querySelectorAll('.alert').forEach(el => {
  setTimeout(() => {
    el.style.transition = 'opacity 0.5s, transform 0.5s';
    el.style.opacity = '0'; el.style.transform = 'translateY(-10px)';
    setTimeout(() => el.remove(), 500);
  }, 4000);
});

// ── Confirm delete
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', (e) => {
    if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
  });
});

// ── Active nav item
const currentPath = window.location.pathname;
document.querySelectorAll('.nav-item').forEach(link => {
  if (link.getAttribute('href') && currentPath.endsWith(link.getAttribute('href').split('/').pop())) {
    link.classList.add('active');
  }
});