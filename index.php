<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Bracket26 - Mundial 2026</title>
    
    <link rel="manifest" href="manifest.json">
    
    <!-- Meta tags PWA / iOS -->
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Bracket26">
    <link rel="apple-touch-icon" href="public/img/icon-192.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="public/css/style.css?v=<?php echo filemtime('public/css/style.css'); ?>">
</head>
<body>
    <header class="app-header">
        <h1>Bracket26</h1>
        <p>Road to Glory</p>
    </header>

    <main class="app-content">
        <!-- VISTA GRUPOS -->
        <section id="vista-grupos" class="tab-content active">
            <!-- Simulated data. A real app would fetch from API and render here -->
            <div class="grupo-container">
                <h2 class="grupo-title">Grupo A</h2>
                <div class="grupo-table">
                    <!-- Fila 1 -->
                    <div class="grupo-row" data-id="1">
                        <div class="row-main">
                            <span class="pos">1</span>
                            <span class="flag">🇪🇸</span>
                            <span class="name">España</span>
                            <span class="pts">6 pts</span>
                        </div>
                        <div class="row-details">
                            <div class="details-content">
                                <p><strong>Historial reciente:</strong></p>
                                <ul>
                                    <li>Victoria vs Alemania (2-1)</li>
                                    <li>Victoria vs Japón (3-0)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Fila 2 -->
                    <div class="grupo-row" data-id="2">
                        <div class="row-main">
                            <span class="pos">2</span>
                            <span class="flag">🇦🇷</span>
                            <span class="name">Argentina</span>
                            <span class="pts">4 pts</span>
                        </div>
                        <div class="row-details">
                            <div class="details-content">
                                <p><strong>Historial reciente:</strong></p>
                                <ul>
                                    <li>Empate vs México (1-1)</li>
                                    <li>Victoria vs Polonia (2-0)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- VISTA ELIMINATORIAS -->
        <section id="vista-eliminatorias" class="tab-content">
            <!-- NAVEGACIÓN FASES -->
            <div class="fases-nav-container">
                <nav class="fases-nav">
                    <button class="fase-btn active" data-fase="32">Dieciseisavos</button>
                    <button class="fase-btn" data-fase="16">Octavos</button>
                    <button class="fase-btn" data-fase="8">Cuartos</button>
                    <button class="fase-btn" data-fase="4">Semifinales</button>
                    <button class="fase-btn" data-fase="2">Final</button>
                </nav>
            </div>

            <div class="eliminatorias-container" id="fase-match-container">
                <!-- Se inyectarán tarjetas de partido dinámicamente con JS -->
            </div>
        </section>
    </main>

    <!-- NAVEGACIÓN INFERIOR (Bottom Navigation) -->
    <nav class="bottom-nav">
        <button class="nav-btn active" data-target="vista-grupos">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Grupos</span>
        </button>
        <button class="nav-btn" data-target="vista-eliminatorias">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 2 22 22 22"></polygon>
            </svg>
            <span>Eliminatorias</span>
        </button>
    </nav>

    <!-- Scripts -->
    <script src="public/js/app.js?v=<?php echo filemtime('public/js/app.js'); ?>"></script>
</body>
</html>
