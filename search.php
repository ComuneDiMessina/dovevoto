<?php
session_start();
require 'config_db.php';

$section = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codice_fiscale']) && isset($_POST['cognome'])) {
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));
    $cognome_input = trim($_POST['cognome']);

    $cf_valid = preg_match('/^(?:[A-Z][AEIOU][AEIOUX]|[AEIOU]X{2}|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}(?:[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[15MR][\dLMNP-V]|[26NS][0-8LMNP-U])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM]|[AC-EHLMPR-T][26NS][9V])|(?:[02468LNQSU][048LQU]|[13579MPRTV][26NS])B[26NS][9V])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/i', $codice_fiscale);

    if (empty($cognome_input)) {
        $error = "Il campo Cognome è obbligatorio.";
    } elseif (!$cf_valid) {
        $error = "Il formato del Codice Fiscale inserito non è valido. Deve contenere 16 caratteri alfanumerici.";
    } else {
        try {
            $stmt = $pdo->prepare('SELECT a.cognome, a.nome, a.dataNascita, s.descrizione, s.ubicazione, s.sezione, s.latit, s.longi, s.circoscrizione, s.note, s.accessibilita
                                     FROM ' . TABLE_ANAGRAFICA . ' a
                                     JOIN ' . TABLE_SEZIONI . ' s ON a.sezione = s.sezione
                                     WHERE a.codFisc = :codice_fiscale AND a.cognome = :cognome');
            $stmt->execute([
                'codice_fiscale' => $codice_fiscale,
                'cognome' => $cognome_input
            ]);
            $section = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$section) {
                $stmt_check_cf = $pdo->prepare('SELECT cognome FROM ' . TABLE_ANAGRAFICA . ' WHERE codFisc = :codice_fiscale');
                $stmt_check_cf->execute(['codice_fiscale' => $codice_fiscale]);
                $cf_exists = $stmt_check_cf->fetch();

                if ($cf_exists) {
                     $error = "Codice Fiscale trovato, ma il cognome non corrisponde. Verifica i dati inseriti o contatta l'ufficio elettorale.";
                } else {
                     $error = "Nessuna sezione elettorale trovata per i dati inseriti. Verifica che siano corretti o contatta l'ufficio elettorale.";
                }
            }
        } catch (PDOException $e) {
            error_log("Errore PDO: " . $e->getMessage());
            $error = "Si è verificato un errore durante la ricerca. Riprova più tardi.";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['codice_fiscale']) || trim($_POST['codice_fiscale']) === '') {
        $error = "Il campo Codice Fiscale è obbligatorio.";
    }
    if (!isset($_POST['cognome']) || trim($_POST['cognome']) === '') {
        $error = ($error ? $error . " " : "") . "Il campo Cognome è obbligatorio.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trova la tua sezione elettorale - Dove voto? Comune di Messina</title>

    <meta name="description" content="Trova la tua sezione elettorale inserendo il codice fiscale e cognome. Servizio gratuito del Comune di Messina con mappa interattiva e indicazioni stradali.">
    <meta name="keywords" content="sezione elettorale, codice fiscale, cognome, elezioni, dove voto, comune di messina">
    <meta name="author" content="Pietro Giglio - Sistemi informativi Comune di Messina">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="Trova la tua sezione elettorale - Comune di Messina">
    <meta property="og:description" content="Trova la tua sezione elettorale inserendo il codice fiscale e cognome. Servizio del Comune di Messina.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://dovevoto.comune.messina.it/search.php">
    <meta property="og:image" content="https://dovevoto.comune.messina.it/immagini/doveVotoMessinaFB.png">
    <meta property="og:site_name" content="Dove voto? Comune di Messina">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,300;0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.0.2/Control.FullScreen.min.css" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="style.css">

    <?php if (defined('MATOMO_SITE_ID') && MATOMO_SITE_ID !== ''): ?>
    <!-- Matomo -->
    <script>
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="https://ingestion.webanalytics.italia.it/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', <?php echo json_encode(MATOMO_SITE_ID); ?>]);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <?php endif; ?>
</head>
<body>
    <?php require 'navbar.php'; ?>

    <div class="page-wrapper">
        <div class="search-page">

            <!-- Header -->
            <div class="search-header animate-in">
                <div class="search-header-icon">
                    <i class="fas fa-search-location"></i>
                </div>
                <h1>Trova la tua sezione</h1>
                <p>Inserisci cognome e codice fiscale per individuare la tua sezione elettorale a Messina.</p>
            </div>

            <!-- Search Form -->
            <div class="search-card animate-in animate-delay-2">
                <form method="post" action="search.php" id="searchForm">
                    <div class="form-group">
                        <label for="cognome">
                            <i class="fas fa-user-edit"></i>
                            Cognome
                        </label>
                        <input
                            type="text"
                            class="dv-input"
                            id="cognome"
                            name="cognome"
                            required
                            placeholder="Es: ROSSI"
                            aria-describedby="cognomeHelp"
                            value="<?php echo isset($_POST['cognome']) ? htmlspecialchars($_POST['cognome']) : ''; ?>"
                            oninput="this.value = this.value.toUpperCase()"
                        >
                        <div id="cognomeHelp" class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Come riportato sulla tessera elettorale o documento.
                        </div>
                        <div class="form-error" id="cognomeError">
                            Il cognome &egrave; obbligatorio.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="codice_fiscale">
                            <i class="fas fa-id-card"></i>
                            Codice Fiscale
                        </label>
                        <input
                            type="text"
                            class="dv-input"
                            id="codice_fiscale"
                            name="codice_fiscale"
                            required
                            maxlength="16"
                            minlength="16"
                            placeholder="Es: RSSMRA80A01F158K"
                            aria-describedby="cfHelp"
                            value="<?php echo isset($_POST['codice_fiscale']) ? htmlspecialchars($_POST['codice_fiscale']) : ''; ?>"
                        >
                        <div id="cfHelp" class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Il codice fiscale deve essere di 16 caratteri alfanumerici.
                        </div>
                        <div class="form-error" id="cfError">
                            Formato codice fiscale non valido.
                        </div>
                    </div>

                    <div class="search-submit">
                        <button type="submit" id="submitBtn" class="btn-action">
                            <i class="fas fa-search"></i>
                            Cerca la mia sezione
                        </button>
                    </div>
                </form>
            </div>

            <!-- Alert container -->
            <div id="alertContainer">
                <?php if ($error): ?>
                    <div class="dv-alert dv-alert--error animate-in" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- /.search-page -->

        <!-- ===== RESULTS ===== -->
        <?php if ($section): ?>
        <div class="search-page" style="max-width:900px;padding-top:0;">
            <div class="result-card animate-in">
                <div class="result-header">
                    <i class="fas fa-check-circle"></i>
                    <h3>Sezione Elettorale Trovata</h3>
                </div>
                <div class="result-body">
                    <div class="result-name">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($section->cognome . ' ' . $section->nome); ?>
                    </div>

                    <div class="result-layout">
                        <div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-vote-yea"></i> Sezione
                                    </div>
                                    <div class="info-value">
                                        <span class="section-badge"><?php echo htmlspecialchars($section->sezione); ?></span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-alt"></i> Data di Nascita
                                    </div>
                                    <div class="info-value">
                                        <?php
                                            try {
                                                $date = new DateTime($section->dataNascita);
                                                echo htmlspecialchars($date->format('d/m/Y'));
                                            } catch (Exception $e) {
                                                echo 'Non disponibile';
                                            }
                                        ?>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-building"></i> Edificio
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($section->descrizione); ?>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt"></i> Indirizzo
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($section->ubicazione); ?>
                                    </div>
                                </div>

                                <?php if ($section->circoscrizione): ?>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-map-marked-alt"></i> Circoscrizione
                                    </div>
                                    <div class="info-value">
                                        <span class="circoscrizione-badge"><?php echo htmlspecialchars($section->circoscrizione); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($section->accessibilita): ?>
                                <div class="info-item info-item--accessible">
                                    <div class="info-label">
                                        <i class="fas fa-wheelchair"></i> Accessibilità
                                    </div>
                                    <div class="info-value">
                                        <span class="accessible-badge"><i class="fas fa-wheelchair"></i> Seggio accessibile</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($section->note): ?>
                            <div class="result-note">
                                <i class="fas fa-info-circle"></i>
                                <span><?php echo htmlspecialchars($section->note); ?></span>
                            </div>
                            <?php endif; ?>

                            <div class="result-actions">
                                <a href="https://maps.google.com/maps?q=<?php echo urlencode($section->latit . ',' . $section->longi); ?>"
                                   target="_blank"
                                   class="btn-success-dv">
                                    <i class="fas fa-route"></i>
                                    Indicazioni stradali
                                </a>
                            </div>
                        </div>

                        <div class="result-map">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.page-wrapper -->

    <?php require 'footer.php'; ?>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.0.2/Control.FullScreen.min.js"></script>

    <script>
        var cfInput        = document.getElementById('codice_fiscale');
        var cognomeInput   = document.getElementById('cognome');
        var cfHelp         = document.getElementById('cfHelp');
        var cfError        = document.getElementById('cfError');
        var cognomeError   = document.getElementById('cognomeError');
        var submitBtn      = document.getElementById('submitBtn');
        var originalBtnText = submitBtn.innerHTML;
        var alertContainer = document.getElementById('alertContainer');
        var searchForm     = document.getElementById('searchForm');

        function showAlert(message, type) {
            type = type || 'danger';
            var cls  = type === 'danger' ? 'dv-alert--error' : 'dv-alert--success';
            var icon = type === 'danger' ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';
            clearAlert();
            alertContainer.innerHTML =
                '<div class="dv-alert ' + cls + ' animate-in" role="alert">' +
                '<i class="' + icon + '"></i><span>' + message + '</span></div>';
        }

        function clearAlert() {
            var el = alertContainer.querySelector('.dv-alert:not([data-server-alert])');
            if (el) alertContainer.removeChild(el);
        }

        <?php if ($error): ?>
        (function() {
            var sa = alertContainer.querySelector('.dv-alert');
            if (sa) sa.setAttribute('data-server-alert', 'true');
        })();
        <?php endif; ?>

        function showLoading() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ricerca in corso\u2026';
            submitBtn.disabled = true;
        }

        function hideLoading() {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }

        function validateCodiceFiscale(cf) {
            return /^(?:[A-Z][AEIOU][AEIOUX]|[AEIOU]X{2}|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}(?:[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[15MR][\dLMNP-V]|[26NS][0-8LMNP-U])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM]|[AC-EHLMPR-T][26NS][9V])|(?:[02468LNQSU][048LQU]|[13579MPRTV][26NS])B[26NS][9V])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/i.test(cf);
        }

        cognomeInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
            if (e.target.value.trim() !== '') {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
                cognomeError.style.display = 'none';
            } else {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
                cognomeError.style.display = 'block';
            }
        });

        cfInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

            if (e.target.value.length === 16) {
                if (validateCodiceFiscale(e.target.value)) {
                    e.target.classList.remove('is-invalid');
                    e.target.classList.add('is-valid');
                    cfHelp.innerHTML = '<i class="fas fa-check-circle"></i> Codice Fiscale valido.';
                    cfHelp.className = 'form-hint text-success';
                    cfError.style.display = 'none';
                } else {
                    e.target.classList.remove('is-valid');
                    e.target.classList.add('is-invalid');
                    cfHelp.innerHTML = '<i class="fas fa-info-circle"></i> Il codice fiscale deve essere di 16 caratteri alfanumerici.';
                    cfHelp.className = 'form-hint';
                    cfError.textContent = 'Formato codice fiscale non valido.';
                    cfError.style.display = 'block';
                }
            } else if (e.target.value.length > 0) {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
                cfHelp.innerHTML = '<i class="fas fa-info-circle"></i> Il codice fiscale deve essere di 16 caratteri.';
                cfHelp.className = 'form-hint text-warning';
                cfError.style.display = 'none';
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
                cfHelp.innerHTML = '<i class="fas fa-info-circle"></i> Il codice fiscale deve essere di 16 caratteri alfanumerici.';
                cfHelp.className = 'form-hint';
                cfError.style.display = 'none';
            }
        });

        searchForm.addEventListener('submit', function(event) {
            var clientAlert = alertContainer.querySelector('.dv-alert:not([data-server-alert])');
            if (clientAlert) alertContainer.removeChild(clientAlert);

            var isValid = true;
            var firstErrorMessage = '';

            if (cognomeInput.value.trim() === '') {
                event.preventDefault();
                isValid = false;
                cognomeInput.classList.add('is-invalid');
                cognomeError.style.display = 'block';
                if (!firstErrorMessage) firstErrorMessage = 'Il campo Cognome \u00e8 obbligatorio.';
            } else {
                cognomeInput.classList.remove('is-invalid');
                cognomeError.style.display = 'none';
            }

            if (!validateCodiceFiscale(cfInput.value)) {
                event.preventDefault();
                isValid = false;
                cfInput.classList.add('is-invalid');
                cfError.textContent = 'Formato codice fiscale non valido.';
                cfError.style.display = 'block';
                if (!firstErrorMessage) firstErrorMessage = 'Il formato del Codice Fiscale non \u00e8 valido.';
            } else {
                cfInput.classList.remove('is-invalid');
                cfError.style.display = 'none';
            }

            if (!isValid) {
                showAlert(firstErrorMessage || 'Controlla i campi evidenziati.');
                hideLoading();
                return false;
            }

            showLoading();
            return true;
        });

        <?php if ($error || $section): ?>
        hideLoading();
        <?php endif; ?>
    </script>

    <?php if ($section && isset($section->latit) && isset($section->longi)): ?>
    <script>
        try {
            var lat = parseFloat(<?php echo json_encode($section->latit); ?>);
            var lon = parseFloat(<?php echo json_encode($section->longi); ?>);

            if (isNaN(lat) || isNaN(lon)) throw new Error('Coordinate non valide.');

            var map = L.map('map', {
                center: [lat, lon],
                zoom: 16,
                fullscreenControl: true,
                fullscreenControlOptions: {
                    title: "Mostra mappa a schermo intero",
                    titleCancel: "Esci da schermo intero"
                }
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '\u00a9 <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
                minZoom: 1
            }).addTo(map);

            var customIcon = L.divIcon({
                html: '<i class="fas fa-map-marker-alt" style="font-size:2.5rem;color:var(--navy);text-shadow:1px 1px 2px rgba(0,0,0,0.3);"></i>',
                iconSize: [32, 40],
                iconAnchor: [16, 40],
                popupAnchor: [0, -40],
                className: 'custom-map-marker'
            });

            var marker = L.marker([lat, lon], { icon: customIcon }).addTo(map);

            var popupContent =
                '<div class="map-popup">' +
                '<h6><i class="fas fa-school" style="margin-right:0.4rem;color:var(--navy-light);"></i><?php echo htmlspecialchars(addslashes($section->descrizione)); ?></h6>' +
                '<p><i class="fas fa-map-pin" style="margin-right:0.4rem;"></i><?php echo htmlspecialchars(addslashes($section->ubicazione)); ?></p>' +
                '<div style="text-align:center;">' +
                '<a href="https://maps.google.com/maps?daddr=' + lat + ',' + lon + '" target="_blank" class="btn-navigate-popup">' +
                '<i class="fas fa-directions"></i> Indicazioni</a>' +
                '</div></div>';

            marker.bindPopup(popupContent).openPopup();

            L.circle([lat, lon], {
                color: '#1E5289',
                fillColor: '#1E5289',
                fillOpacity: 0.08,
                radius: 80
            }).addTo(map);

            setTimeout(function() { map.invalidateSize(); }, 150);
            map.on('fullscreenchange', function() { map.invalidateSize(); });

        } catch (e) {
            console.error('Errore mappa:', e);
            var mc = document.getElementById('map');
            if (mc) mc.innerHTML = '<p style="text-align:center;color:var(--error);padding:2rem;">Impossibile caricare la mappa.</p>';
        }
    </script>
    <?php endif; ?>
</body>
</html>
