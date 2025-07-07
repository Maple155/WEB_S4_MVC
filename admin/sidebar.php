<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Établissement Financier</title>
    <style>
        /* Style de base pour la sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header img {
            width: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-item:hover {
            background-color: #34495e;
            padding-left: 25px;
        }
        
        .menu-item.active {
            background-color: #3498db;
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .menu-label {
            flex-grow: 1;
        }
        
        .submenu {
            padding-left: 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .menu-item.has-submenu::after {
            content: '▾';
            transition: transform 0.3s;
        }
        
        .menu-item.has-submenu.open::after {
            transform: rotate(180deg);
        }
        
        .menu-item.has-submenu.open + .submenu {
            max-height: 500px;
        }
        
        /* Bouton de toggle pour la sidebar */
        .sidebar-toggle {
            position: fixed;
            left: 260px;
            top: 10px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            z-index: 1001;
            display: none;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            .sidebar.open {
                left: 0;
            }
            .sidebar-toggle {
                display: block;
            }
        }
    </style>
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Bouton de toggle pour mobile -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="https://via.placeholder.com/80" alt="Logo Banque">
            <h3>Admin Bank</h3>
        </div>
        
        <div class="sidebar-menu">
            <!-- Tableau de bord -->
            <a href="interests_report.php" class="menu-item">
                <i class="fas fa-tachometer-alt"></i>
                <span class="menu-label">Tableau de bord</span>
            </a>
            
            <!-- Gestion des fonds -->
            <div class="menu-item has-submenu" onclick="toggleSubmenu(this)">
                <i class="fas fa-money-bill-wave"></i>
                <span class="menu-label">Gestion des fonds</span>
            </div>
            <div class="submenu">
                <a href="add-fond.php" class="menu-item">
                    <i class="fas fa-plus-circle"></i>
                    <span class="menu-label">Ajouter des fonds</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-list"></i>
                    <span class="menu-label">Voir les mouvements</span>
                </a>
            </div>
            
            <!-- Gestion des prêts -->
            <div class="menu-item has-submenu" onclick="toggleSubmenu(this)">
                <i class="fas fa-hand-holding-usd"></i>
                <span class="menu-label">Gestion des prêts</span>
            </div>
            <div class="submenu">
                <a href="loan_types.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span class="menu-label">Types de prêt</span>
                </a>
                <a href="view_loans.php" class="menu-item">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="menu-label">Voir les prêts</span>
                </a>
            </div>
            
            <!-- Rapports -->
            <div class="menu-item has-submenu" onclick="toggleSubmenu(this)">
                <i class="fas fa-chart-bar"></i>
                <span class="menu-label">Rapports</span>
            </div>
            <div class="submenu">
                <a href="monthly_interests.php" class="menu-item">
                    <i class="fas fa-coins"></i>
                    <span class="menu-label">Intérêts mensuels</span>
                </a>
                <a href="annual_report.php" class="menu-item">
                    <i class="fas fa-file-alt"></i>
                    <span class="menu-label">Rapport annuel</span>
                </a>
            </div>
            
            <!-- Administration -->
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i>
                <span class="menu-label">Paramètres</span>
            </a>
            
            <!-- Déconnexion -->
            <a href="logout.php" class="menu-item" style="color: #e74c3c;">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-label">Déconnexion</span>
            </a>
        </div>
    </div>

    <script>
        // Fonction pour toggle la sidebar sur mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }
        
        // Fonction pour toggle les sous-menus
        function toggleSubmenu(element) {
            element.classList.toggle('open');
        }
        
        // Marquer l'item actif selon la page courante
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                if (item.getAttribute('href') === currentPage) {
                    item.classList.add('active');
                    
                    // Ouvrir le sous-menu parent si nécessaire
                    const submenu = item.closest('.submenu');
                    if (submenu) {
                        const parentItem = submenu.previousElementSibling;
                        parentItem.classList.add('open');
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    }
                }
            });
        });
    </script>
</body>
</html>