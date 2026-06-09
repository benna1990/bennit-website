<?php
// Admin-instellingen voor Liemen Bouw en Advies.
//
// Het wachtwoord staat hier als beveiligde 'hash' (niet leesbaar als platte tekst).
// STANDAARD WACHTWOORD: liemen2026   →  verander dit z.s.m.!
//
// Nieuw wachtwoord instellen:
//   1) Open de pagina  wachtwoord.php  in de admin-map (in je browser),
//      typ je nieuwe wachtwoord en kopieer de gegenereerde regel hieronder.
//   2) Of via de terminal:
//      php -r 'echo password_hash("JOUW-WACHTWOORD", PASSWORD_DEFAULT), "\n";'
//   Plak de uitkomst tussen de aanhalingstekens bij LB_ADMIN_PASS_HASH.

define('LB_ADMIN_PASS_HASH', '$2y$10$yJZZ/9r3KwvVDPR4W401GewOI7UtmQUNT2bKOqpcyRsA2nuHhvIem');

// Naam die bovenin de admin verschijnt.
define('LB_ADMIN_TITLE', 'Liemen — beheer');
