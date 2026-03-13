-- Schema per "Dove voto?" - Applicazione ricerca sezione elettorale
-- Adattare i nomi delle tabelle alla tornata elettorale specifica e
-- aggiornarli di conseguenza in config_db.php (costanti TABLE_ANAGRAFICA e TABLE_SEZIONI).
--
-- Esempio:
--   TABLE_ANAGRAFICA = 'anagSezioni2026_ref'
--   TABLE_SEZIONI    = 'sezioniComune2026'

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- Tabella anagrafica elettori
-- Rinominare secondo la tornata elettorale (es. anagSezioni2026_ref).
-- Importare i dati dall'elenco elettorale fornito dalla Prefettura/Comune.
-- ---------------------------------------------------------------------------

CREATE TABLE `anagSezioniAAAA_ref` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `cognome`     varchar(255) DEFAULT NULL,
  `nome`        varchar(60)  DEFAULT NULL,
  `codFisc`     varchar(16)  DEFAULT NULL,
  `dataNascita` date         DEFAULT NULL,
  `sezione`     int(10)      DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `codFisc` (`codFisc`),
  KEY `sezione` (`sezione`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Tabella sezioni elettorali con coordinate geografiche
-- Rinominare secondo la tornata elettorale (es. sezioniComune2026).
-- Popolare con i dati dei seggi: indirizzo, coordinate GPS, accessibilità.
-- ---------------------------------------------------------------------------

CREATE TABLE `sezioniComuneAAAA` (
  `idSezione`      int(11)      NOT NULL AUTO_INCREMENT,
  `sezione`        int(3)       DEFAULT NULL,
  `descrizione`    varchar(150) DEFAULT NULL,  -- nome edificio (es. "Scuola G. Mazzini")
  `ubicazione`     varchar(128) DEFAULT NULL,  -- indirizzo completo
  `latit`          varchar(64)  DEFAULT NULL,  -- latitudine (decimale)
  `longi`          varchar(64)  DEFAULT NULL,  -- longitudine (decimale)
  `circoscrizione` tinyint(1)   DEFAULT NULL,  -- numero circoscrizione/municipio
  `note`           text         DEFAULT NULL,
  `accessibilita`  tinyint(1)   DEFAULT NULL,  -- 1 = accessibile ai disabili, 0 = non accessibile
  PRIMARY KEY (`idSezione`),
  KEY `sezione` (`sezione`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Vista di comodo (opzionale)
-- Utile per query manuali; l'applicazione usa JOIN diretti.
-- Aggiornare i nomi delle tabelle prima di creare la vista.
-- ---------------------------------------------------------------------------

-- CREATE OR REPLACE VIEW `TrovaLaSezione` AS
--   SELECT a.cognome, a.nome, a.codFisc, a.dataNascita,
--          s.sezione, s.descrizione, s.ubicazione, s.latit, s.longi
--   FROM `anagSezioniAAAA_ref` a
--   JOIN `sezioniComuneAAAA` s ON a.sezione = s.sezione;
