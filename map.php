<?php
session_start();
require 'config_db.php';
$stmt = $pdo->query('SELECT sezione, descrizione, TRIM(ubicazione) as ubicazione, latit, longi, circoscrizione, note, accessibilita FROM ' . TABLE_SEZIONI);
$sections = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mappa Sezioni Elettorali - Dove voto?</title>

    <meta name="description" content="Dove voto? Trova la tua sezione elettorale, visualizza tutte le sezioni elettorali con mappe interattive e indicazioni stradali.">
    <meta name="keywords" content="elezioni, dove voto, mappa sezioni elettorali">
    <meta name="robots" content="index, follow">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,300;0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="./leaflet_search/leaflet-search.css" />

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

    <div class="map-fullpage">
        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="./leaflet_search/leaflet-search.js"></script>

    <script>
        function escHtml(s) {
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        var map = L.map('map').setView([<?php echo json_encode(MAP_CENTER_LAT); ?>, <?php echo json_encode(MAP_CENTER_LON); ?>], <?php echo json_encode(MAP_ZOOM); ?>);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '\u00a9 <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var customIcon = L.icon({
            iconUrl: 'immagini/logoComune_512.png',
            iconSize: [50, 50],
            iconAnchor: [25, 50],
            popupAnchor: [0, -50]
        });

        var markersLayer = new L.LayerGroup();
        map.addLayer(markersLayer);

        var sections = <?php echo json_encode($sections); ?>;

        sections.forEach(function(section) {
            if (section.latit && section.longi) {
                var popupContent =
                    '<div class="map-popup">' +
                    '<div class="popup-header-row">' +
                    '<h5>Sezione ' + escHtml(section.sezione) + '</h5>' +
                    (section.accessibilita == 1 ? '<span class="popup-accessible" title="Seggio accessibile ai disabili"><i class="fas fa-wheelchair"></i></span>' : '') +
                    '</div>' +
                    (section.circoscrizione ? '<div class="popup-circoscrizione"><i class="fas fa-map-marked-alt"></i> Circoscrizione ' + escHtml(section.circoscrizione) + '</div>' : '') +
                    '<p><strong>Descrizione:</strong> ' + escHtml(section.descrizione) + '</p>' +
                    '<p><strong>Ubicazione:</strong> ' + escHtml(section.ubicazione) + '</p>' +
                    (section.note ? '<div class="popup-note"><i class="fas fa-info-circle"></i> ' + escHtml(section.note) + '</div>' : '') +
                    '<a href="https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(section.latit + ',' + section.longi) + '" ' +
                    'target="_blank" class="btn-navigate-popup"><i class="fas fa-directions"></i> Indicazioni</a>' +
                    '</div>';

                var marker = L.marker([parseFloat(section.latit), parseFloat(section.longi)], { icon: customIcon })
                    .bindPopup(popupContent);

                marker.feature = {
                    properties: {
                        sezione: section.sezione
                    }
                };

                markersLayer.addLayer(marker);
            }
        });

        var searchControl = new L.Control.Search({
            layer: markersLayer,
            initial: false,
            zoom: 15,
            marker: false,
            textPlaceholder: 'Cerca sezione\u2026',
            propertyName: 'sezione'
        });

        searchControl.on('search:locationfound', function(e) {
            e.layer.openPopup();
        });

        map.addControl(searchControl);

        // Expand search input
        var searchInput = document.querySelector('.leaflet-control-search input');
        var searchBtn   = document.querySelector('.leaflet-control-search .search-button');
        if (searchInput) searchInput.style.display = 'inline-block';
        if (searchBtn)   searchBtn.style.display = 'none';
    </script>
</body>
</html>
