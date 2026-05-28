/* BennIT — main.js */

// Nav scroll effect
const nav = document.getElementById('nav');
const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 40);
window.addEventListener('scroll', onScroll, { passive: true });
onScroll();

// Mobile menu
const toggle = document.getElementById('navToggle');
const links  = document.getElementById('navLinks');

toggle.addEventListener('click', () => {
  const open = links.classList.toggle('open');
  toggle.classList.toggle('open', open);
  toggle.setAttribute('aria-label', open ? 'Menu sluiten' : 'Menu openen');
  document.body.style.overflow = open ? 'hidden' : '';
});

links.querySelectorAll('a').forEach(a => {
  a.addEventListener('click', () => {
    links.classList.remove('open');
    toggle.classList.remove('open');
    document.body.style.overflow = '';
  });
});

// Close on outside click
document.addEventListener('click', e => {
  if (links.classList.contains('open') && !links.contains(e.target) && !toggle.contains(e.target)) {
    links.classList.remove('open');
    toggle.classList.remove('open');
    document.body.style.overflow = '';
  }
});

// Scroll reveal
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.08, rootMargin: '0px 0px -48px 0px' });

document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id === '#') return;
    const target = document.querySelector(id);
    if (!target) return;
    e.preventDefault();
    const offset = nav.offsetHeight + 12;
    const top = target.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top, behavior: 'smooth' });
  });
});

// Contact form (Formspree)
const form    = document.getElementById('contactForm');
const success = document.getElementById('formSuccess');
const submitBtn = document.getElementById('submitBtn');

if (form) {
  form.addEventListener('submit', async e => {
    e.preventDefault();
    submitBtn.disabled = true;
    submitBtn.textContent = 'Versturen…';

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json' }
      });

      if (res.ok) {
        form.reset();
        success.classList.add('show');
        submitBtn.textContent = 'Verstuurd ✓';
        // GA4 conversie — contactformulier verstuurd
        if (typeof gtag !== 'undefined') {
          gtag('event', 'generate_lead', { event_category: 'contact' });
        }
      } else {
        throw new Error('Network error');
      }
    } catch {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Probeer opnieuw';
    }
  });
}
