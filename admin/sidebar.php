<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Établissement Financier</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
        }
        
        .sidebar {
            width: 260px;
            height: 100vh;
            background-color: #111111;
            color: #ffffff;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid #1a4a3a;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #111111;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #1a4a3a;
            border-radius: 2px;
        }
        
        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #1a4a3a;
        }
        
        .sidebar-header img {
            width: 80px;      
            height: 80px;     
            border-radius: 50%;
            border: 2px solid #2d7a5f;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        
        .sidebar-header h3 {
            font-size: 18px;
            font-weight: 300;
            color: #ffffff;
            letter-spacing: 1px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            color: #b8b8b8;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 300;
            border-left: 3px solid transparent;
        }
        
        .menu-item:hover {
            background-color: #1a1a1a;
            color: #ffffff;
            border-left-color: #2d7a5f;
        }
        
        .menu-item.active {
            background-color: #1a4a3a;
            color: #ffffff;
            border-left-color: #2d7a5f;
        }
        
        .menu-item i {
            margin-right: 15px;
            width: 18px;
            text-align: center;
            font-size: 16px;
            opacity: 0.8;
        }
        
        .menu-label {
            flex-grow: 1;
        }
        
        .submenu {
            background-color: #0a0a0a;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .submenu .menu-item {
            padding: 12px 24px 12px 50px;
            font-size: 13px;
            color: #999999;
            border-left: none;
        }
        
        .submenu .menu-item:hover {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        
        .submenu .menu-item i {
            font-size: 14px;
            margin-right: 12px;
        }
        
        .menu-item.has-submenu {
            position: relative;
        }
        
        .menu-item.has-submenu::after {
            content: '▸';
            position: absolute;
            right: 20px;
            font-size: 12px;
            transition: transform 0.3s ease;
            color: #666666;
        }
        
        .menu-item.has-submenu.open::after {
            transform: rotate(90deg);
            color: #2d7a5f;
        }
        
        .menu-item.has-submenu.open + .submenu {
            max-height: 300px;
        }
        
        .menu-item.logout {
            color: #666666;
            margin-top: 30px;
            border-top: 1px solid #1a4a3a;
            padding-top: 20px;
        }
        
        .menu-item.logout:hover {
            color: #999999;
            background-color: #1a1a1a;
        }
        
        .sidebar-toggle {
            position: fixed;
            left: 20px;
            top: 20px;
            background: #2d7a5f;
            color: white;
            border: none;
            border-radius: 4px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            z-index: 1001;
            display: none;
            font-size: 16px;
            transition: all 0.2s ease;
        }
        
        .sidebar-toggle:hover {
            background: #3d8a6f;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }
            .sidebar.open {
                left: 0;
            }
            .sidebar-toggle {
                display: block;
            }
        }
        
        /* Overlay pour mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../sql/banque.png" alt="Logo Bank">
            <h3>ADMIN BANK</h3>
        </div>
        
        <div class="sidebar-menu">
            <a href="compare_sim.php" class="menu-item">
                <i class="fas fa-chart-line"></i>
                <span class="menu-label">Comparer Simulation</span>
            </a>
            
            <div class="menu-item has-submenu" onclick="toggleSubmenu(this)">
                <i class="fas fa-wallet"></i>
                <span class="menu-label">Gestion des fonds</span>
            </div>
            <div class="submenu">
                <a href="add-fond.php" class="menu-item">
                    <i class="fas fa-plus"></i>
                    <span class="menu-label">Ajouter des fonds</span>
                </a>
                <a href="tableauEF.php" class="menu-item">
                    <i class="fas fa-plus"></i>
                    <span class="menu-label">Fonds disponibles</span>
                </a>
            </div>
            
            <div class="menu-item has-submenu" onclick="toggleSubmenu(this)">
                <i class="fas fa-handshake"></i>
                <span class="menu-label">Gestion des prêts</span>
            </div>
            <div class="submenu">
                <a href="typePret.php" class="menu-item">
                    <i class="fas fa-tag"></i>
                    <span class="menu-label">Types de prêt</span>
                </a>
                <a href="fairePret.php" class="menu-item">
                    <i class="fas fa-file-contract"></i>
                    <span class="menu-label">Faire un prêt</span>
                </a>
                <a href="listePret.php" class="menu-item">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="menu-label">Liste des prêts</span>
                </a>
            </div>
            
            <div class="menu-item has-submenu" onclick="toggleSubmenu(this)">
                <i class="fas fa-chart-bar"></i>
                <span class="menu-label">Rapports</span>
            </div>
            <div class="submenu">
                <a href="interests_report.php" class="menu-item">
                    <i class="fas fa-percentage"></i>
                    <span class="menu-label">Intérêts mensuels</span>
                </a>
                <a href="interests_chart.php" class="menu-item">
                    <i class="fas fa-percentage"></i>
                    <span class="menu-label">Graphique</span>
                </a>
            </div>
            
            <a href="../index.html" class="menu-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-label">Déconnexion</span>
            </a>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
        
        function toggleSubmenu(element) {
            element.classList.toggle('open');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const menuItems = document.querySelectorAll('.menu-item[href]');
            
            menuItems.forEach(item => {
                if (item.getAttribute('href') === currentPage) {
                    item.classList.add('active');
                    
                    const submenu = item.closest('.submenu');
                    if (submenu) {
                        const parentItem = submenu.previousElementSibling;
                        parentItem.classList.add('open');
                    }
                }
            });
        });
    </script>
</body>
</html>