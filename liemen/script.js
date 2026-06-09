// Liemen — Bouw en Advies · minimale, dependency-vrije interacties

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

  // ---- Lightbox: klik op een projectfoto voor een grote weergave ----
  initLightbox();
});

function initLightbox() {
  var images = Array.prototype.slice.call(document.querySelectorAll('.card__media img'));
  if (!images.length) return;

  var ov = document.createElement('div');
  ov.className = 'lightbox';
  ov.setAttribute('role', 'dialog');
  ov.setAttribute('aria-modal', 'true');
  ov.innerHTML =
    '<button class="lightbox__close" aria-label="Sluiten">&times;</button>' +
    '<button class="lightbox__nav lightbox__prev" aria-label="Vorige foto">&#8249;</button>' +
    '<figure class="lightbox__stage"><img alt="" /><figcaption></figcaption></figure>' +
    '<button class="lightbox__nav lightbox__next" aria-label="Volgende foto">&#8250;</button>';
  document.body.appendChild(ov);

  var lbImg = ov.querySelector('img');
  var lbCap = ov.querySelector('figcaption');
  var group = [];
  var idx = 0;

  function captionFor(img) {
    var card = img.closest('.card');
    var title = card && card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : '';
    var fc = img.parentNode.querySelector('figcaption');
    var label = fc ? fc.textContent.trim() : '';
    return title + (label ? ' — ' + label : '');
  }

  function show(i) {
    idx = (i + group.length) % group.length;
    var img = group[idx];
    lbImg.src = img.currentSrc || img.src;
    lbImg.alt = img.alt || '';
    lbCap.textContent = captionFor(img);
  }

  function open(img) {
    var card = img.closest('.card');
    group = card ? Array.prototype.slice.call(card.querySelectorAll('.card__media img')) : [img];
    ov.classList.toggle('single', group.length < 2);
    show(group.indexOf(img));
    ov.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function close() {
    ov.classList.remove('open');
    document.body.style.overflow = '';
  }

  images.forEach(function (img) {
    img.style.cursor = 'zoom-in';
    img.addEventListener('click', function () { open(img); });
  });

  ov.querySelector('.lightbox__close').addEventListener('click', close);
  ov.querySelector('.lightbox__prev').addEventListener('click', function (e) { e.stopPropagation(); show(idx - 1); });
  ov.querySelector('.lightbox__next').addEventListener('click', function (e) { e.stopPropagation(); show(idx + 1); });
  ov.addEventListener('click', function (e) { if (e.target === ov || e.target.classList.contains('lightbox__stage')) close(); });
  document.addEventListener('keydown', function (e) {
    if (!ov.classList.contains('open')) return;
    if (e.key === 'Escape') close();
    else if (e.key === 'ArrowLeft') show(idx - 1);
    else if (e.key === 'ArrowRight') show(idx + 1);
  });
}
