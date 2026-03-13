# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**dovevoto.comune.messina.it** is a PHP web application for the Comune di Messina that lets citizens find their electoral polling section (seggio elettorale) by entering their fiscal code (codice fiscale) and surname.

## Stack

- **Backend:** PHP with PDO (MySQL/MariaDB database)
- **Frontend:** Vanilla JS, Leaflet.js (maps), Font Awesome, Design Italia CSS palette
- **Analytics:** Matomo
- **Database:** `elezioniDoveVoto` on `192.168.128.29`

## Architecture

The app has no framework — it's plain PHP with included partials:

- `config_db.php` — PDO connection (imported at top of pages that need DB)
- `navbar.php` / `footer.php` — included in every page
- `index.php` — landing page, no DB access
- `search.php` — main feature: validates Italian codice fiscale + cognome, queries voter table joined with section table, returns section location + Leaflet map
- `map.php` — fetches all sections from DB and renders a full Leaflet map with markers and Leaflet.Search control
- `pagina.php` — placeholder for authenticated content (session-based)

### Database Tables

- `anagSezioni2025_ref` — voter registry (`codFisc`, `cognome`, `nome`, `dataNascita`, `sezione`)
- `sezioniMessina2026` — polling section details (`sezione`, `descrizione`, `ubicazione`, `latit`, `longi`, `circoscrizione`, `note`, `accessibilita`)

The core query in `search.php` joins these two tables on `sezione`.

## Development Notes

- No build system, no package manager. Serve with any PHP-capable web server (Apache with `.htaccess` rewriting, or PHP's built-in server).
- Run locally: `php -S localhost:8000` from the project root.
- The `.htaccess` routes all non-file requests to `index.php` — this requires `mod_rewrite` on Apache.
- `.bak` files are manual snapshots of previous versions; they are not served.
- Input validation for the codice fiscale uses an identical regex on both client (JS) and server (PHP) sides — keep them in sync when modifying.
- Leaflet map in `search.php` is conditionally rendered only when a valid result is returned.
- The `accessibilita` column is a boolean flag (1 = accessible); displayed as a badge in both search results and map popups.
