// Liemen — Bouw & Advies · minimale, dependency-vrije interacties

document.addEventListener('DOMContentLoaded', function () {
  // Jaartal
  var year = document.getElementById('year');
  if (year) year.textContent = new Date().getFullYear();

  // Mobiel menu
  var btn = document.querySelector('.nav__btn');
  var menu = document.getElementById('menu');
  if (btn && menu) {
    btn.addEventListener('click', function () {
      var open = menu.classList.toggle('open');
      btn.setAttribute('aria-expanded', String(open));
    });
    menu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        menu.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // Subtiele reveal bij scrollen
  var targets = document.querySelectorAll('.sec, .values, .hero');
  targets.forEach(function (el) { el.classList.add('reveal'); });
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.08 });
    targets.forEach(function (el) { io.observe(el); });
  } else {
    targets.forEach(function (el) { el.classList.add('is-in'); });
  }

  // Formulier — bevestiging zonder backend
  var form = document.querySelector('.form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.reportValidity(); return; }
      var b = form.querySelector('button[type="submit"]');
      if (b) { b.textContent = 'Bedankt — we nemen contact op'; b.disabled = true; }
      form.reset();
    });
  }
});
