<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dove voto? - Comune di Messina</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Trova la tua sezione elettorale a Messina. Servizio ufficiale del Comune con mappe interattive e indicazioni stradali per le elezioni.">
    <meta name="keywords" content="dove voto, sezioni elettorali, elezioni messina, comune messina, voto">
    <meta name="author" content="Pietro Giglio - Sistemi informativi Comune di Messina">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="Dove voto? - Visualizza tutte le sezioni elettorali di Messina">
    <meta property="og:description" content="Trova la tua sezione elettorale a Messina con mappe interattive e indicazioni stradali.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://dovevoto.comune.messina.it">
    <meta property="og:image" content="https://dovevoto.comune.messina.it/immagini/doveVotoMessinaFB.png">
    <meta property="og:site_name" content="Dove voto? Comune di Messina">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,300;0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    <style>
        /* Hero-page navbar override: transparent at top */
        #dvNavbar { background: transparent; box-shadow: none; }
        #dvNavbar.dv-navbar--scrolled { background: var(--blue); box-shadow: var(--shadow-md); }
    </style>
</head>
<body>
    <?php require 'navbar.php'; ?>

    <!-- ===== HERO ===== -->
    <section class="hero" id="hero">
        <div class="hero-bg"></div>

        <!-- Decorative rings -->
        <div class="hero-deco hero-deco--1"></div>
        <div class="hero-deco hero-deco--2"></div>
        <div class="hero-deco hero-deco--3"></div>

        <div class="hero-inner">
            <!-- Text column -->
            <div class="hero-text">
                <div class="hero-eyebrow">Comune di Messina</div>
                <h1 class="hero-title">Dove <em>voto</em>&thinsp;?</h1>
                <p class="hero-subtitle">
                    Trova facilmente la tua sezione elettorale o esplora tutte le sedi di voto nella citt&agrave; di Messina con mappe interattive e indicazioni stradali.
                </p>
                <div class="hero-actions">
                    <a href="search.php" class="btn-primary-dv">
                        <i class="fas fa-search-location"></i>
                        Trova la tua sezione
                    </a>
                    <a href="map.php" class="btn-secondary-dv">
                        <i class="fas fa-map-marked-alt"></i>
                        Esplora le sezioni
                    </a>
                </div>
            </div>

            <!-- Visual column: floating preview cards -->
            <div class="hero-visual">
                <div class="preview-card preview-card--section">
                    <div class="preview-section-badge">142</div>
                    <div>
                        <div class="preview-section-label">Sezione Elettorale</div>
                        <div class="preview-section-text">La tua sezione</div>
                    </div>
                </div>

                <div class="preview-card preview-card--location">
                    <div class="preview-location-name">
                        <i class="fas fa-school" style="color:var(--blue);margin-right:0.35rem;font-size:0.85rem;"></i>
                        Scuola G. Mazzini
                    </div>
                    <div class="preview-location-address">
                        <i class="fas fa-map-pin"></i>
                        Via Garibaldi, 28 &mdash; Messina
                    </div>
                </div>

                <div class="preview-card preview-card--navigate">
                    <i class="fas fa-route"></i>
                    Indicazioni stradali
                </div>

                <div class="preview-pulse"></div>
            </div>
        </div>

        <!-- Scroll hint -->
        <div class="scroll-hint" id="scrollHint">
            <span>Scopri di pi&ugrave;</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- ===== FEATURES ===== -->
    <section class="features" id="features">
        <div class="features-inner">
            <div class="features-eyebrow">Servizio elettorale online</div>
            <h2 class="features-title">Come funziona</h2>

            <div class="features-grid">
                <div class="feature-card animate-in animate-delay-1">
                    <div class="feature-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>Inserisci i tuoi dati</h3>
                    <p>Cognome e codice fiscale: bastano pochi secondi per trovare la tua sezione elettorale.</p>
                </div>

                <div class="feature-card animate-in animate-delay-2">
                    <div class="feature-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Trova la sezione</h3>
                    <p>Visualizza il plesso scolastico, l'indirizzo e il numero di sezione assegnato.</p>
                </div>

                <div class="feature-card animate-in animate-delay-3">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>Raggiungi il seggio</h3>
                    <p>Ottieni indicazioni stradali su Google Maps per arrivare comodamente al tuo seggio.</p>
                </div>
            </div>
        </div>
    </section>

    <?php require 'footer.php'; ?>

    <script>
        // Hero background fade-in on load
        window.addEventListener('load', function() {
            document.getElementById('hero').classList.add('loaded');
        });

        // Scroll hint
        document.getElementById('scrollHint').addEventListener('click', function() {
            document.getElementById('features').scrollIntoView({ behavior: 'smooth' });
        });

        // Navbar scroll effect
        var navbar = document.getElementById('dvNavbar');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 60) {
                navbar.classList.add('dv-navbar--scrolled');
            } else {
                navbar.classList.remove('dv-navbar--scrolled');
            }
        });

        // Intersection Observer for feature card animations
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.animate-in').forEach(function(el) {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    </script>
</body>
</html>
