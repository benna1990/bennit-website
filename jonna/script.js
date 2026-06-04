/* ============================================================
   Ootjers Facilities — script.js
   Mobiel menu, jaartal, en formulierafhandeling.
   ============================================================ */

(function () {
  'use strict';

  // ----- Mobiel menu -----
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('nav');

  function closeNav() {
    nav.classList.remove('open');
    toggle.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
  }

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('open');
      toggle.classList.toggle('open', isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
    });
    // Sluit het menu na het klikken op een link
    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeNav);
    });
  }

  // ----- Jaartal in footer -----
  var jaar = document.getElementById('jaar');
  if (jaar) {
    jaar.textContent = String(new Date().getFullYear());
  }

  // ----- Offerteformulier -----
  var form = document.getElementById('offerteForm');
  var status = document.getElementById('formStatus');

  function setStatus(message, type) {
    if (!status) return;
    status.textContent = message;
    status.className = 'form-status' + (type ? ' ' + type : '');
  }

  if (form) {
    form.addEventListener('submit', function (e) {
      // Basisvalidatie
      if (!form.checkValidity()) {
        return; // browser toont zelf de meldingen
      }

      var action = form.getAttribute('action') || '';
      var usesFormspree = action.indexOf('formspree.io') !== -1 &&
                          action.indexOf('JOUW_FORM_ID') === -1;

      // Als Formspree nog niet is ingesteld: val terug op een mailto-link,
      // zodat het formulier sowieso werkt zonder configuratie.
      if (!usesFormspree) {
        e.preventDefault();
        var naam = encodeURIComponent(form.naam.value);
        var email = encodeURIComponent(form.email.value);
        var tel = encodeURIComponent(form.telefoon.value);
        var dienst = encodeURIComponent(form.dienst.value);
        var bericht = encodeURIComponent(form.bericht.value);

        var body =
          'Naam: ' + naam + '%0D%0A' +
          'E-mail: ' + email + '%0D%0A' +
          'Telefoon: ' + tel + '%0D%0A' +
          'Dienst: ' + dienst + '%0D%0A%0D%0A' +
          'Bericht:%0D%0A' + bericht;

        var onderwerp = encodeURIComponent('Offerteaanvraag via website — ' + form.naam.value);
        // PAS DIT E-MAILADRES AAN naar het echte adres:
        window.location.href = 'mailto:info@ootjersfacilities.nl?subject=' + onderwerp + '&body=' + body;
        setStatus('Uw mailprogramma wordt geopend om de aanvraag te versturen.', 'ok');
        return;
      }

      // Formspree-verzending via fetch (AJAX), zodat de gebruiker op de pagina blijft.
      e.preventDefault();
      setStatus('Bezig met versturen…');

      fetch(action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json' }
      })
        .then(function (response) {
          if (response.ok) {
            form.reset();
            setStatus('Bedankt! Uw aanvraag is verstuurd. We nemen snel contact op.', 'ok');
          } else {
            setStatus('Er ging iets mis. Bel of mail ons gerust rechtstreeks.', 'err');
          }
        })
        .catch(function () {
          setStatus('Geen verbinding. Probeer het later opnieuw of bel ons.', 'err');
        });
    });
  }
})();
