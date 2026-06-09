# Ootjers Facilities — website

Strakke, moderne one-page website voor het glazenwassers- en gevelreinigingsbedrijf
**Ootjers Facilities**. Gebouwd met losse HTML/CSS/JS — geen build-stap, geen dependencies.

## Bestanden

| Bestand | Wat het is |
|---|---|
| `index.html` | De volledige pagina (alle secties) |
| `styles.css` | De vormgeving. Kleuren staan bovenin onder `:root` |
| `script.js` | Mobiel menu + formulierafhandeling |
| `assets/` | Map voor logo en foto's |

## Lokaal bekijken

Dubbelklik `index.html`, of start een lokale server:

```bash
cd ootjers-facilities
python3 -m http.server 8000
# open daarna http://localhost:8000
```

## ✏️ Wat je nog moet invullen (zoek-en-vervang)

Deze placeholders staan nu in de site. Vervang ze met de echte gegevens:

- **Telefoonnummer** — `06 - 00 00 00 00` en de link `tel:+31600000000` (in `index.html` én `script.js`)
- **E-mailadres** — `info@ootjersfacilities.nl` (in `index.html` én `script.js`)
- **Werkgebied** — `[REGIO / PLAATS]` en `[Plaats 1..4]` (sectie "Werkgebied")
- **KvK-nummer** — `KvK [nummer]` in de footer
- **Reviews** — vervang de voorbeeldreviews door echte
- **Logo** — zie hieronder

## Logo toevoegen

Zet je logo in `assets/` (bijv. `logo.svg` of `logo.png`) en vervang in
`index.html` het `<span class="logo-mark">…</span>`-blok door:

```html
<img src="assets/logo.svg" alt="Ootjers Facilities" height="38" />
```

## Contactformulier laten werken

Het formulier werkt nu al **zonder configuratie**: bij verzenden opent het de
mailapp van de bezoeker met de ingevulde gegevens (mailto-fallback).

Wil je dat aanvragen **direct in je mailbox** binnenkomen zonder dat de bezoeker
zelf hoeft te mailen? Gebruik dan het gratis [Formspree](https://formspree.io):

1. Maak een gratis account en een nieuw formulier aan.
2. Kopieer je Form-ID (bijv. `xmyzabcd`).
3. Vervang in `index.html` in `<form ... action="...">` de waarde
   `https://formspree.io/f/JOUW_FORM_ID` door jouw URL.

Het script schakelt dan automatisch over op nette verzending zonder de mailapp.

## Online zetten (hosting)

Allemaal gratis opties voor een statische site:

- **Netlify** of **Vercel** — sleep de map naar het dashboard, klaar.
- **GitHub Pages** — push naar een repo en zet Pages aan.
- **Eigen webhosting** — upload de bestanden via FTP naar de webmap.

## 🔍 SEO — vindbaar in Amsterdam

De site is geoptimaliseerd voor lokale zoekopdrachten (zoals "glazenwasser Amsterdam").
Wat er al in zit:

- **Titel & meta-description** met Amsterdam + duidelijke call-to-action
- **Locatie-keywords** door alle koppen en teksten (stadsdelen + omliggende plaatsen)
- **Structured data (JSON-LD)** — `LocalBusiness` met geo-coördinaten, openingstijden,
  diensten en werkgebied → voedt de lokale resultaten en Google Maps
- **FAQ met FAQ-schema** → kans op uitgebreide zoekresultaten (rich snippets)
- **`robots.txt` + `sitemap.xml`** voor snelle, complete indexering
- **Open Graph / Twitter cards / canonical / geo-meta**

### ⚠️ De #1 prioriteit: Google Bedrijfsprofiel

Voor lokale vindbaarheid telt een **gratis [Google Bedrijfsprofiel](https://www.google.com/business/)**
(Google Business Profile) vaak zwaarder dan de website zelf — dit is wat je in de
kaart/"local pack" bovenaan zet. Doen:

1. Maak een bedrijfsprofiel aan voor "Ootjers Facilities", categorie *Glazenwasser*.
2. Vul werkgebied (Amsterdam + plaatsen), telefoon, openingstijden en foto's in.
3. **Zorg dat naam, adres en telefoon (NAP) exact gelijk zijn** aan die op de website.
4. Vraag tevreden klanten om reviews — dat is de sterkste rankingfactor lokaal.

### Na het live zetten

- **Echte coördinaten** invullen: zoek je adres op Google Maps, kopieer de lat/long
  en vervang `52.370216` / `4.895168` (nu het centrum van Amsterdam) in `index.html`
  (`geo.position`, `ICBM` én het `geo`-blok in de JSON-LD).
- **Domein** overal vervangen: zoek-en-vervang `https://www.ootjersfacilities.nl/`.
- **Sitemap aanmelden** in [Google Search Console](https://search.google.com/search-console)
  (gratis) → je ziet daar ook hoe je gevonden wordt.
- **OG-afbeelding** maken (1200×630px) en als `assets/og-image.jpg` plaatsen.
- **Social links** in de JSON-LD (`sameAs`) invullen of verwijderen.

## Kleuren aanpassen

Open `styles.css` en pas de variabelen onder `:root` aan (bovenaan het bestand).
De blauw- en teal-tinten bepalen de hele uitstraling.
