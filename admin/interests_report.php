<?php 
// En haut de chaque page admin
include 'sidebar.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intérêts Mensuels - Admin</title>
    <style>
        /* Styles précédents... */
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .filter-container input {
            width: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="https://via.placeholder.com/80" alt="Logo Banque" class="logo">
        <h1>Intérêts Mensuels</h1>
        
        <div class="filter-container">
            <input type="month" id="startDate">
            <input type="month" id="endDate">
            <button onclick="loadInterests()">Filtrer</button>
        </div>
        
        <div id="results">
            <table id="interestsTable">
                <thead>
                    <tr>
                        <th>Mois/Année</th>
                        <th>Nombre de prêts</th>
                        <th>Capital</th>
                        <th>Intérêts</th>
                    </tr>
                </thead>
                <tbody id="interestsBody">
                    <!-- Les résultats seront insérés ici -->
                </tbody>
            </table>
        </div>
        
        <div id="message" class="error"></div>
    </div>

    <script>
        const apiBase = "http://localhost/Git/WEB_S4_MVC/ws/";
        
        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            const end = new Date();
            const start = new Date();
            start.setMonth(end.getMonth() - 11); // 12 mois glissants
            
            document.getElementById('startDate').value = formatDate(start);
            document.getElementById('endDate').value = formatDate(end);
            
            loadInterests();
        });
        
        function formatDate(date) {
            return date.toISOString().slice(0, 7);
        }
        
        function loadInterests() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const messageEl = document.getElementById('message');
            
            messageEl.textContent = 'Chargement...';
            messageEl.className = '';
            
            const data = `start=${startDate}&end=${endDate}`;
            
            ajax("GET", "admin/interets", data, function(response) {
                if (response.error) {
                    messageEl.textContent = response.error;
                    messageEl.className = 'error';
                    return;
                }
                
                // Affichage des résultats
                const tbody = document.getElementById('interestsBody');
                tbody.innerHTML = '';
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.periode}</td>
                            <td>${item.nombre_prets}</td>
                            <td>${item.capital_total.toLocaleString('fr-FR')} €</td>
                            <td>${item.interets_mensuels.toLocaleString('fr-FR', {minimumFractionDigits: 2})} €</td>
                        `;
                        tbody.appendChild(row);
                    });
                    
                    messageEl.textContent = `Total: ${response.total_interets.toLocaleString('fr-FR', {minimumFractionDigits: 2})} €`;
                    messageEl.className = 'success';
                } else {
                    messageEl.textContent = 'Aucun résultat trouvé';
                    messageEl.className = 'error';
                }
            });
        }
        
        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            callback(JSON.parse(xhr.responseText));
                        } catch (e) {
                            console.error("Erreur parsing JSON:", e);
                            document.getElementById('message').textContent = "Erreur serveur";
                        }
                    } else {
                        document.getElementById('message').textContent = "Erreur " + xhr.status;
                    }
                }
            };
            xhr.send(data);
        }
    </script>
</body>
</html>