<nav class="dv-navbar dv-navbar--solid" id="dvNavbar">
    <div class="navbar-inner">
        <a href="./" class="nav-brand">
            <img src="immagini/logoComuneMessina.png" alt="Stemma Comune di Messina">
            <span class="nav-brand-text">Dove voto&thinsp;?</span>
        </a>

        <button class="menu-toggle" id="menuToggle" aria-label="Apri menu di navigazione" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="nav-links" id="navLinks">
            <li>
                <a href="search.php">
                    <i class="fas fa-search-location"></i>
                    Cerca Sezione
                </a>
            </li>
            <li>
                <a href="map.php">
                    <i class="fas fa-map-marked-alt"></i>
                    Mappa Sezioni
                </a>
            </li>
            <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] === true): ?>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
(function() {
    var toggle = document.getElementById('menuToggle');
    var links  = document.getElementById('navLinks');
    if (toggle && links) {
        toggle.addEventListener('click', function() {
            var open = links.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open);
            toggle.querySelector('i').className = open ? 'fas fa-times' : 'fas fa-bars';
        });
    }
})();
</script>
