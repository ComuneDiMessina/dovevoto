# Dove voto?

Applicazione web per la ricerca della sezione elettorale, sviluppata dal Comune di Messina e resa disponibile in **riuso** per altre Pubbliche Amministrazioni.

Il cittadino inserisce cognome e codice fiscale e ottiene: numero di sezione, edificio, indirizzo, mappa interattiva e link a Google Maps per le indicazioni stradali. È disponibile anche una mappa pubblica con tutti i seggi del territorio.

![Screenshot homepage](immagini/doveVotoMessina.png)

---

## Prerequisiti

- PHP ≥ 7.4 con estensione PDO e PDO_MySQL
- MySQL ≥ 5.7 o MariaDB ≥ 10.3
- Apache con `mod_rewrite` abilitato (per il routing via `.htaccess`)

---

## Installazione

### 1. Clona il repository

```bash
git clone <url-repository> dovevoto
```

### 2. Configura il database

Crea il database e l'utente MySQL:

```sql
CREATE DATABASE nome_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'utente_db'@'localhost' IDENTIFIED BY 'password_sicura';
GRANT SELECT ON nome_database.* TO 'utente_db'@'localhost';
FLUSH PRIVILEGES;
```

Importa lo schema (struttura tabelle, senza dati):

```bash
mysql -u root -p nome_database < schema.sql
```

Rinomina le tabelle create secondo la tornata elettorale (es. `anagSezioni2026_ref`, `sezioniComune2026`) e popola:

- **Tabella anagrafica** (`TABLE_ANAGRAFICA`): importa l'elenco elettorale dalla fonte istituzionale (Prefettura / anagrafe comunale). Campi: `cognome`, `nome`, `codFisc`, `dataNascita`, `sezione`.
- **Tabella sezioni** (`TABLE_SEZIONI`): inserisci i seggi con indirizzo, coordinate GPS (latitudine/longitudine decimali) e flag `accessibilita`. Campi dettagliati nello `schema.sql`.

### 3. Configura l'applicazione

```bash
cp config_db.php.example config_db.php
```

Modifica `config_db.php` con i valori della tua installazione:

| Costante | Descrizione |
|----------|-------------|
| `DB_HOST` | Host del database |
| `DB_NAME` | Nome del database |
| `DB_USER` | Utente del database |
| `DB_PASS` | Password del database |
| `TABLE_ANAGRAFICA` | Nome tabella anagrafica elettori |
| `TABLE_SEZIONI` | Nome tabella sezioni con coordinate |
| `MAP_CENTER_LAT` / `MAP_CENTER_LON` | Coordinate del centro mappa (comune) |
| `MAP_ZOOM` | Zoom iniziale della mappa (12 = vista comunale) |
| `MATOMO_SITE_ID` | Site ID Matomo Analytics (lasciare vuoto per disabilitare) |

### 4. Configura Apache

Assicurati che `mod_rewrite` sia attivo e che `AllowOverride All` sia impostato per la directory. Il file `.htaccess` incluso gestisce il routing.

---

## Struttura del progetto

```
├── config_db.php.example   # Template configurazione (copiare in config_db.php)
├── schema.sql              # Struttura database da importare
├── index.php               # Homepage
├── search.php              # Ricerca per cognome e codice fiscale
├── map.php                 # Mappa pubblica di tutti i seggi
├── navbar.php / footer.php # Componenti comuni
├── style.css               # Stile (Design Italia)
└── leaflet_search/         # Libreria Leaflet Search (inclusa)
```

---

## Adattamento ad ogni tornata elettorale

Per ogni nuova elezione è sufficiente:

1. Creare nuove tabelle (o rinominare le esistenti) con i nuovi dati elettorali.
2. Aggiornare `TABLE_ANAGRAFICA` e `TABLE_SEZIONI` in `config_db.php`.

Il codice applicativo non richiede modifiche.

---

## Sviluppato da

**Sistemi Informativi e Innovazione Tecnologica** — Comune di Messina
Referente tecnico: Pietro Giglio — [p.giglio@comune.messina.it](mailto:p.giglio@comune.messina.it)

---

## Licenza

Questo software è rilasciato sotto licenza **EUPL-1.2** (European Union Public Licence).
È possibile utilizzarlo, modificarlo e redistribuirlo nel rispetto dei termini della licenza, compatibile con il [Catalogo del riuso](https://developers.italia.it/it/riuso) di Developers Italia.
