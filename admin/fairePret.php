<?php 
//En haut de chaque page admin
include 'sidebar.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faire un Prêt - Établissement Financier</title>
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
            min-height: 100vh;
            padding-left: 260px;
            padding: 20px 20px 20px 280px;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #1a4a3a;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 300;
            color: #ffffff;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .subtitle {
            color: #999999;
            font-size: 14px;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .form-section {
            background: #111111;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #1a4a3a;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 400;
            color: #ffffff;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #1a4a3a;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #b8b8b8;
            font-size: 14px;
            margin-bottom: 6px;
            font-weight: 300;
        }
        
        input, select {
            width: 100%;
            padding: 14px 16px;
            background-color: #1a1a1a;
            border: 1px solid #333333;
            border-radius: 4px;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #2d7a5f;
            background-color: #1f1f1f;
            box-shadow: 0 0 0 3px rgba(45, 122, 95, 0.1);
        }
        
        input::placeholder {
            color: #666666;
        }
        
        select {
            cursor: pointer;
        }
        
        select option {
            background-color: #1a1a1a;
            color: #ffffff;
            padding: 10px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        button {
            padding: 14px 28px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 400;
            transition: all 0.3s ease;
            flex: 1;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2d7a5f 0%, #1a4a3a 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #3d8a6f 0%, #2a5a4a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(45, 122, 95, 0.3);
        }
        
        .btn-secondary {
            background: #333333;
            color: #ffffff;
            border: 1px solid #555555;
        }
        
        .btn-secondary:hover {
            background: #444444;
            border-color: #666666;
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 14px;
            display: none;
        }
        
        .message.success {
            background-color: rgba(45, 122, 95, 0.1);
            border: 1px solid rgba(45, 122, 95, 0.3);
            color: #2d7a5f;
        }
        
        .message.info {
            background-color: rgba(52, 152, 219, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            color: #3498db;
        }
        
        .simulation-section {
            background: #111111;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #1a4a3a;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-top: 30px;
        }
        
        .simulation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #1a4a3a;
        }
        
        .simulation-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .summary-item {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #333333;
        }
        
        .summary-label {
            color: #b8b8b8;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .summary-value {
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
        }
        
        .simulation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .simulation-table th,
        .simulation-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #333333;
            font-size: 13px;
        }
        
        .simulation-table th {
            background-color: #1a1a1a;
            color: #b8b8b8;
            font-weight: 500;
            position: sticky;
            top: 0;
        }
        
        .simulation-table td {
            color: #ffffff;
        }
        
        .simulation-table tr:hover {
            background-color: #1a1a1a;
        }
        
        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #333333;
            border-radius: 4px;
        }
        
        .table-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: #333333;
            border-radius: 4px;
        }
        
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .simulation-summary {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-left: 0;
                padding: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .simulation-table th,
            .simulation-table td {
                padding: 8px;
                font-size: 12px;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h1>FAIRE UN PRÊT</h1>
            <div class="subtitle">Gestion des demandes de financement</div>
        </div>
        
        <div class="content-grid">
            <div class="form-section">
                <h2 class="section-title">Informations du prêt</h2>
                
                <div class="form-group">
                    <label for="client">Client</label>
                    <div class="client">
                        <select id="client" name="client">
                            <option value="">Chargement...</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="type_pret">Type de prêt</label>
                    <div class="type_pret">
                        <select id="type_pret" name="type_pret">
                            <option value="">Chargement...</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="montant">Montant demandé (Ar)</label>
                    <input type="number" id="montant" name="montant" min="0" placeholder="Ex: 1000000" />
                </div>
                
                <div class="form-group">
                    <label for="mois_max">Durée (mois)</label>
                    <input type="number" id="mois_max" name="mois_max" min="1" placeholder="Ex: 12" />
                </div>
            </div>
            
            <div class="form-section">
                <h2 class="section-title">Conditions spéciales</h2>
                
                <div class="form-group">
                    <label for="assurance">Assurance (%)</label>
                    <input type="number" id="assurance" name="assurance" placeholder="Ex: 2.5" step="0.01" min="0" />
                </div>
                
                <div class="form-group">
                    <label for="delai">Délai 1er remboursement (mois)</label>
                    <input type="number" id="delai" name="delai" placeholder="Ex: 1" min="0" />
                </div>

                <div class="form-group">
                    <label for="delai">Date du pret </label>
                    <input type="date" id="datePret" name="datePret" />
                </div>
                
                <div class="button-group">
                    <button type="button" class="btn-primary" onclick="demanderPret()">
                        Confirmer le prêt
                    </button>
                    <button type="button" class="btn-secondary" onclick="simulerPret()">
                        Simuler
                    </button>
                </div>
                
                <div id="messagePret" class="message"></div>
            </div>
        </div>
        
        <div id="simulationResult" class="simulation-section" style="display: none;">
            <!-- Résultat de simulation -->
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/WEB_S4_MVC/ws";

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                }
            };
            xhr.send(data);
        }

        function showMessage(text, type = 'info') {
            const messageEl = document.getElementById("messagePret");
            messageEl.textContent = text;
            messageEl.className = `message ${type}`;
            messageEl.style.display = 'block';
            
            setTimeout(() => {
                messageEl.style.display = 'none';
            }, 5000);
        }

        function chargerAllClient() {
            ajax("GET", "/allClients", null, (data) => {
                const clientDiv = document.querySelector(".client");
                const select = document.createElement("select");
                select.id = "client";
                select.name = "client";

                const optionDefault = document.createElement("option");
                optionDefault.value = "";
                optionDefault.textContent = "-- Choisir un client --";
                select.appendChild(optionDefault);

                data.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.id_client;
                    option.textContent = item.nom;
                    select.appendChild(option);
                });

                clientDiv.innerHTML = "";
                clientDiv.appendChild(select);
            });
        }

        function chargerTypePrets() {
            ajax("GET", "/type_prets", null, (data) => {
                const pTypePret = document.querySelector(".type_pret");
                const select = document.createElement("select");
                select.id = "type_pret";
                select.name = "type_pret";

                const optionDefault = document.createElement("option");
                optionDefault.value = "";
                optionDefault.textContent = "-- Choisir un type de prêt --";
                select.appendChild(optionDefault);

                data.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.id_type_pret;
                    option.textContent = item.nom;
                    select.appendChild(option);
                });

                pTypePret.innerHTML = "";
                pTypePret.appendChild(select);
            });
        }

        function demanderPret() {
            const id_type_pret = document.getElementById("type_pret").value;
            const montant = document.getElementById("montant").value;
            const mois_max = document.getElementById("mois_max").value;
            const id_client = document.getElementById("client").value;
            const assurance = parseFloat(document.getElementById("assurance").value || 0);
            const delai = parseInt(document.getElementById("delai").value || 0);
            const date = document.getElementById("datePret").value; 

            if (!id_type_pret || !montant || !mois_max || !id_client) {
                showMessage("Veuillez remplir tous les champs obligatoires", "error");
                return;
            }

            const params = `id_type_pret=${encodeURIComponent(id_type_pret)}&montant=${encodeURIComponent(montant)}&mois_max=${encodeURIComponent(mois_max)}&id_client=${encodeURIComponent(id_client)}&assurance=${encodeURIComponent(assurance)}&delai=${encodeURIComponent(delai)}&datePret=${encodeURIComponent(date)}`;

            ajax("POST", "/prets", params, (data) => {
                if (data.message) {
                    showMessage(data.message, "success");
                    document.getElementById("montant").value = "";
                    document.getElementById("mois_max").value = "";
                    document.getElementById("assurance").value = "";
                    document.getElementById("delai").value = "";
                    document.getElementById("client").value = "";
                    document.getElementById("type_pret").value = "";
                    document.getElementById("datePret").value = "";
                }
            });
        }

        function simulerPret() {
            const montant = parseFloat(document.getElementById("montant").value);
            const duree = parseInt(document.getElementById("mois_max").value);
            const id_type_pret = document.getElementById("type_pret").value;
            const assurance = parseFloat(document.getElementById("assurance").value || 0);
            const delai = parseInt(document.getElementById("delai").value || 0);

            if (!montant || !duree || !id_type_pret) {
                showMessage("Veuillez remplir le montant, la durée et le type de prêt", "error");
                return;
            }

            ajax("GET", "/type_prets", null, (data) => {
                const typePret = data.find(item => item.id_type_pret == id_type_pret);
                if (!typePret) {
                    showMessage("Type de prêt introuvable", "error");
                    return;
                }

                const taux_annuel = typePret.taux_interet / 100;
                const taux_mensuel = taux_annuel / 12;
                const annuite = (montant * taux_mensuel) / (1 - Math.pow(1 + taux_mensuel, -duree));
                const assurance_mensuelle = (montant * (assurance / 100)) / 12;
                const mensualite_totale = annuite + assurance_mensuelle;
                let interet_total = 0;

                let capitalRestant = montant;
                for (let i = 1; i <= duree; i++) {
                    const interetMois = capitalRestant * taux_mensuel;
                    const capitalMois = annuite - interetMois;
                    const totalMois = annuite + assurance_mensuelle;

                    interet_total += interetMois;
                    capitalRestant -= capitalMois;
                }

                let html = `
                    <div class="simulation-header">
                        <h2 class="section-title">Simulation du prêt</h2>
                    </div>
                    
                    <div class="simulation-summary">
                        <div class="summary-item">
                            <div class="summary-label">Montant</div>
                            <div class="summary-value">${montant.toLocaleString()} Ar</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Durée</div>
                            <div class="summary-value">${duree} mois</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Taux annuel</div>
                            <div class="summary-value">${typePret.taux_interet}%</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Assurance</div>
                            <div class="summary-value">${assurance}%</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Délai 1er remboursement</div>
                            <div class="summary-value">${delai} mois</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Mensualité totale</div>
                            <div class="summary-value">${mensualite_totale.toLocaleString()} Ar</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">interêt totale</div>
                            <div class="summary-value">${interet_total.toLocaleString()} Ar</div>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="simulation-table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Capital début</th>
                                    <th>Intérêt</th>
                                    <th>Capital remboursé</th>
                                    <th>Assurance</th>
                                    <th>Total payé</th>
                                    <th>Capital restant</th>
                                </tr>
                            </thead>
                            <tbody>`;

                let capital_restant = montant;
                for (let i = 1; i <= duree; i++) {
                    const interet_mois = capital_restant * taux_mensuel;
                    const capital_mois = annuite - interet_mois;
                    const total_mois = annuite + assurance_mensuelle;

                    html += `
                        <tr>
                            <td>${i + delai}</td>
                            <td>${capital_restant.toLocaleString()}</td>
                            <td>${interet_mois.toLocaleString()}</td>
                            <td>${capital_mois.toLocaleString()}</td>
                            <td>${assurance_mensuelle.toLocaleString()}</td>
                            <td>${total_mois.toLocaleString()}</td>
                            <td>${(capital_restant - capital_mois).toLocaleString()}</td>
                        </tr>`;

                    capital_restant -= capital_mois;
                }

                html += `
                            </tbody>
                        </table>
                    </div>`;

                const simulationDiv = document.getElementById("simulationResult");
                simulationDiv.innerHTML = html;
                simulationDiv.style.display = 'block';
                simulationDiv.classList.add('fade-in');
                
                // Scroll vers la simulation
                simulationDiv.scrollIntoView({ behavior: 'smooth' });
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const dateInput = document.getElementById("datePret");
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
            chargerTypePrets();
            chargerAllClient();
        });
    </script>
</body>
</html>
