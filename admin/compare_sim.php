<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Comparer deux simulations</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
            margin: 0;
            padding: 30px;
            margin-left: 260px; /* sidebar largeur */
            transition: margin-left 0.3s;
        }
        @media (max-width: 768px) {
            body {
                margin-left: 0;
                padding: 10px;
            }
        }
        h2 {
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        select, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #2d7a5f;
            background-color: #111;
            color: #fff;
        }
        button {
            background-color: #2d7a5f;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #3d8a6f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #fff;
        }
        th, td {
            border: 1px solid #2d7a5f;
            padding: 10px;
            text-align: right;
            transition: all 0.3s ease;
        }
        th {
            background-color: #1a1a1a;
            text-align: center;
        }
        td:first-child {
            text-align: center;
        }
        
        /* Animation pour les cellules */
        .cell-appear {
            animation: cellAppear 0.5s ease forwards;
        }
        
        @keyframes cellAppear {
            from { 
                opacity: 0;
                transform: translateY(10px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Surbrillance verte pour les meilleurs valeurs */
        .better-value {
            background-color: rgba(45, 122, 95, 0.3);
        }
        
        /* Hover effects pour les lignes */
        tbody tr {
            transition: background-color 0.3s ease;
        }
        
        tbody tr:hover {
            background-color: rgba(45, 122, 95, 0.1);
        }
        
        /* Animation pour les tableaux */
        .table-container {
            opacity: 0;
            transform: translateY(20px);
            animation: tableSlideIn 0.8s ease forwards;
        }
        
        @keyframes tableSlideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Indicateurs visuels pour les différences */
        .difference-indicator {
            position: relative;
            display: inline-block;
        }
        
        .difference-indicator::after {
            content: '';
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
        }
        
        .difference-indicator.better::after {
            border-bottom: 8px solid #2d7a5f;
        }
        
        .difference-indicator.worse::after {
            border-top: 8px solid #d32f2f;
        }
        
        #comparisonResult {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Loading animation */
        .loading {
            text-align: center;
            padding: 20px;
        }
        
        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #2d7a5f;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Styles pour les cellules sans données */
        .no-data {
            background-color: rgba(128, 128, 128, 0.1);
            color: #666;
            font-style: italic;
        }
        
        /* Animation pour les lignes impaires/paires */
        tbody tr:nth-child(odd) {
            background-color: rgba(255, 255, 255, 0.02);
        }
        
        tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.01);
        }
        
        /* Mise en évidence des colonnes importantes */
        .monthly-table td:nth-child(6), /* Total payé sim1 */
        .monthly-table td:nth-child(7), /* Capital restant sim1 */
        .monthly-table td:nth-child(12), /* Total payé sim2 */
        .monthly-table td:nth-child(13) { /* Capital restant sim2 */
            font-weight: bold;
        }
        
        /* Animation spéciale pour les valeurs critiques - supprimée */
        
        /* Responsive améliorations */
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
            th, td {
                padding: 5px;
            }
        }
        .filtre-container {
            background-color: #1a1a1a;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #2d7a5f;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
        }

        .filtre-container label {
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: bold;
            display: block;
            color: #ccc;
        }

        .filtre-container input[type="text"],
        .filtre-container input[type="number"] {
            width: 100%;
            padding: 8px;
            background-color: #111;
            color: #fff;
            border: 1px solid #2d7a5f;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: border-color 0.3s;
        }

        .filtre-container input:focus {
            border-color: #3d8a6f;
            outline: none;
        }

        .filtre-container button {
            background-color: #2d7a5f;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filtre-container button:hover {
            background-color: #3d8a6f;
        }
        .filter-and-list-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            align-items: flex-start;
            margin-top: 20px;
        }

        /* Cartes */
        .filter-card {
            background-color: #1a1a1a;
            border: 1px solid #2d7a5f;
            border-radius: 10px;
            padding: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .filter-card h3, .simulation-card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #2d7a5f;
            font-size: 1.3em;
            border-bottom: 1px solid #2d7a5f;
            padding-bottom: 5px;
        }
        .simulation-card {
            flex: 1.5; /* Augmente un peu la largeur par rapport à la carte filtres */
            max-height: 500px; /* Hauteur max fixe, ajuste selon besoin */
            overflow-y: auto; /* Scroll vertical si dépassement */
            padding: 20px;
            background-color: #1b1b1b;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(45, 122, 95, 0.5);
            color: #eee;
        }

        /* Form controls */
        .form-control {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-control label {
            margin-bottom: 5px;
            color: #ccc;
            font-weight: 500;
        }

        .form-control input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #2d7a5f;
            background-color: #111;
            color: #fff;
        }

        .form-row {
            display: flex;
            gap: 10px;
        }

        /* Bouton principal */
        .full-button {
            margin-top: 10px;
            width: 100%;
            padding: 10px;
            background-color: #2d7a5f;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .full-button:hover {
            background-color: #3d8a6f;
        }

        /* Liste des simulations */
        .simulation-card div {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .simulation-card input[type="checkbox"] {
            transform: scale(1.2);
            margin-right: 10px;
            accent-color: #2d7a5f;
        }

        .simulation-card span {
            color: #eee;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .filter-and-list-container {
                flex-direction: column;
            }

            .filter-card, .simulation-card {
                max-width: 100%;
            }

            .form-row {
                flex-direction: column;
            }
        }
        .buttons-row {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .full-button {
            flex: 1; /* Les boutons prennent la même largeur */
            padding: 10px;
            background-color: #2d7a5f;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
        }

        .full-button:hover {
            background-color: #3d8a6f;
        }

        /* Optionnel: pour différencier légèrement le bouton comparer */
        .compare-button {
            background-color: #226644;
        }

        .compare-button:hover {
            background-color: #2e8054;
        }


    </style>
</head>
<body>
<h2>Comparer deux simulations</h2>

    <div class="filter-and-list-container">

        <!-- Carte Filtres -->
        <div class="filter-card">
            <h3> Filtres</h3>

            <div class="form-control">
                <label for="filtreType">Type de prêt</label>
                <input type="text" id="filtreType" placeholder="ex: Immobilier" />
            </div>

            <div class="form-control">
                <label for="filtreClient">Client</label>
                <input type="text" id="filtreClient" placeholder="ex: Rakoto" />
            </div>

            <div class="form-row">
                <div class="form-control">
                    <label for="filtreMontantMin">Montant min</label>
                    <input type="number" id="filtreMontantMin" placeholder="ex: 500000" />
                </div>
                <div class="form-control">
                    <label for="filtreMontantMax">Montant max</label>
                    <input type="number" id="filtreMontantMax" placeholder="ex: 5000000" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-control">
                    <label for="filtreDureeMin">Durée min (mois)</label>
                    <input type="number" id="filtreDureeMin" />
                </div>
                <div class="form-control">
                    <label for="filtreDureeMax">Durée max (mois)</label>
                    <input type="number" id="filtreDureeMax" />
                </div>
            </div>
            <div class="buttons-row">
                <button class="full-button" onclick="appliquerFiltres()">Appliquer les filtres</button>
                <button class="full-button compare-button" onclick="comparerSimulations()">Comparer</button>
            </div>
        </div>

        <!-- Carte Simulations -->
        <div class="simulation-card" id="simulationsList">
            <h3> Choisissez deux simulations</h3>
            <!-- les cases à cocher seront injectées ici -->
        </div>

    </div>
    <div id="comparisonResult"></div>

    


    <div id="comparisonResult"></div>

    <script>
        // const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws";
        const apiBase = "/ETU003113/t/WEB_S4_MVC/ws";

        let simulationsGlobales = [];
        function appliquerFiltres() {
            const type = document.getElementById("filtreType").value.toLowerCase();
            const client = document.getElementById("filtreClient").value.toLowerCase();
            const montantMin = parseFloat(document.getElementById("filtreMontantMin").value) || 0;
            const montantMax = parseFloat(document.getElementById("filtreMontantMax").value) || Infinity;
            const dureeMin = parseInt(document.getElementById("filtreDureeMin").value) || 0;
            const dureeMax = parseInt(document.getElementById("filtreDureeMax").value) || Infinity;

            const filtres = simulationsGlobales.filter(sim => {
                const typeMatch = !type || (sim.nom && sim.nom.toLowerCase().includes(type));
                const clientMatch = !client || (sim.nom_client && sim.nom_client.toLowerCase().includes(client));
                const montant = parseFloat(sim.montant);
                const montantMatch = montant >= montantMin && montant <= montantMax;
                const duree = parseInt(sim.duree_mois);
                const dureeMatch = duree >= dureeMin && duree <= dureeMax;

                return typeMatch && clientMatch && montantMatch && dureeMatch;
            });

            afficherSimulations(filtres);
        }

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            const fullUrl = apiBase + url + (method === "GET" && data ? "?" + data : "");
            xhr.open(method, fullUrl, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        callback(JSON.parse(xhr.responseText));
                    } else {
                        alert("Erreur API: " + xhr.status);
                    }
                }
            };
            xhr.send(method === "GET" ? null : data);
        }
        
        function chargerSimulations() {
            ajax("GET", "/admin/simulations", null, (data) => {
                simulationsGlobales = data;
                afficherSimulations(data);
            });
        }

        function afficherSimulations(data) {
            const container = document.getElementById("simulationsList");
            container.innerHTML = "<strong>Choisissez deux simulations :</strong>";

            data.forEach(sim => {
                const label = `#${sim.id_pret} - ${parseFloat(sim.montant).toLocaleString('fr-FR')} Ar - ${sim.duree_mois} mois - ${sim.nom || "Type inconnu"} - ${sim.nom_client || "client inconnu"}`;

                const div = document.createElement("div");
                div.style.marginBottom = "8px";

                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "simulation";
                checkbox.value = sim.id_pret;

                const span = document.createElement("span");
                span.textContent = " " + label;

                div.appendChild(checkbox);
                div.appendChild(span);
                container.appendChild(div);
            });
        }
        document.addEventListener("DOMContentLoaded", () => {
            chargerSimulations();
        });
        
        function comparerSimulations() {

            const checked = document.querySelectorAll('input[name="simulation"]:checked');
            if (checked.length !== 2) {
                alert("Veuillez sélectionner exactement deux simulations.");
                return;
            }

            const id1 = checked[0].value;
            const id2 = checked[1].value;

            if (!id1 || !id2 || id1 === id2) {
                alert("Veuillez sélectionner deux simulations différentes.");
                return;
            }

            // Afficher un indicateur de chargement
            document.getElementById("comparisonResult").innerHTML = '<div class="loading">Chargement de la comparaison...</div>';

            ajax("GET", `/admin/compareSimulations`, `id1=${id1}&id2=${id2}`, (data) => {
                const s1 = data.sim1;
                const s2 = data.sim2;

                function calculDetail(sim) {
                    const montant = parseFloat(sim.montant);
                    const duree_initiale = parseInt(sim.duree_mois);
                    const assurance = parseFloat(sim.assurance);
                    const delai = parseInt(sim.delai_mois);
                    const taux_annuel = parseFloat(sim.taux_interet) / 100;
                    const taux_mensuel = taux_annuel / 12;

                    const duree = duree_initiale - delai;
                    const annuite = (montant * taux_mensuel) / (1 - Math.pow(1 + taux_mensuel, -duree));
                    const assurance_mensuelle = (montant * (assurance / 100)) / 12;
                    const mensualite_totale = annuite + assurance_mensuelle;

                    let interet_total = 0;
                    let capital_restant = montant;
                    const mois = [];

                    for (let i = 1; i <= duree; i++) {
                        const interet_mois = capital_restant * taux_mensuel;
                        const capital_mois = annuite - interet_mois;
                        const total_mois = annuite + assurance_mensuelle;

                        interet_total += interet_mois;
                        mois.push({
                            mois: i + delai,
                            capital_debut: capital_restant,
                            interet: interet_mois,
                            capital_rembourse: capital_mois,
                            assurance: assurance_mensuelle,
                            total_paye: total_mois,
                            capital_restant: capital_restant - capital_mois
                        });

                        capital_restant -= capital_mois;
                    }

                    return {
                        montant,
                        duree,
                        assurance,
                        delai,
                        taux_annuel,
                        mensualite_totale,
                        interet_total,
                        mois
                    };
                }

                const detail1 = calculDetail(s1);
                const detail2 = calculDetail(s2);

                // Fonction pour déterminer quelle valeur est meilleure
                function getBetterValueClass(val1, val2, lowerIsBetter = true) {
                    if (val1 === val2) return ['', ''];
                    
                    if (lowerIsBetter) {
                        return val1 < val2 ? ['better-value', ''] : ['', 'better-value'];
                    } else {
                        return val1 > val2 ? ['better-value', ''] : ['', 'better-value'];
                    }
                }

                // Comparaisons pour le tableau de résumé
                const comparisons = {
                    montant: getBetterValueClass(detail1.montant, detail2.montant, false), // Plus élevé = mieux
                    duree: getBetterValueClass(detail1.duree, detail2.duree, true), // Plus court = mieux
                    assurance: getBetterValueClass(detail1.assurance, detail2.assurance, true), // Plus bas = mieux
                    delai: getBetterValueClass(detail1.delai, detail2.delai, true), // Plus court = mieux
                    taux: getBetterValueClass(detail1.taux_annuel, detail2.taux_annuel, true), // Plus bas = mieux
                    mensualite: getBetterValueClass(detail1.mensualite_totale, detail2.mensualite_totale, true), // Plus bas = mieux
                    interet: getBetterValueClass(detail1.interet_total, detail2.interet_total, true) // Plus bas = mieux
                };

                let html = `
                    <div class="table-container">
                        <h3>Résumé général</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Caractéristique</th>
                                    <th>Simulation #${s1.id_pret}</th>
                                    <th>Simulation #${s2.id_pret}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Montant (Ar)</td>
                                    <td class="${comparisons.montant[0]}">${detail1.montant.toLocaleString('fr-FR')}</td>
                                    <td class="${comparisons.montant[1]}">${detail2.montant.toLocaleString('fr-FR')}</td>
                                </tr>
                                <tr>
                                    <td>Durée (mois)</td>
                                    <td class="${comparisons.duree[0]}">${detail1.duree}</td>
                                    <td class="${comparisons.duree[1]}">${detail2.duree}</td>
                                </tr>
                                <tr>
                                    <td>Assurance (%)</td>
                                    <td class="${comparisons.assurance[0]}">${detail1.assurance}</td>
                                    <td class="${comparisons.assurance[1]}">${detail2.assurance}</td>
                                </tr>
                                <tr>
                                    <td>Délai 1er remboursement (mois)</td>
                                    <td class="${comparisons.delai[0]}">${detail1.delai}</td>
                                    <td class="${comparisons.delai[1]}">${detail2.delai}</td>
                                </tr>
                                <tr>
                                    <td>Taux annuel</td>
                                    <td class="${comparisons.taux[0]}">${(detail1.taux_annuel * 100).toFixed(2)}%</td>
                                    <td class="${comparisons.taux[1]}">${(detail2.taux_annuel * 100).toFixed(2)}%</td>
                                </tr>
                                <tr>
                                    <td>Mensualité totale (Ar)</td>
                                    <td class="${comparisons.mensualite[0]}">${detail1.mensualite_totale.toLocaleString('fr-FR')}</td>
                                    <td class="${comparisons.mensualite[1]}">${detail2.mensualite_totale.toLocaleString('fr-FR')}</td>
                                </tr>
                                <tr>
                                    <td>Intérêt total (Ar)</td>
                                    <td class="${comparisons.interet[0]}">${detail1.interet_total.toLocaleString('fr-FR')}</td>
                                    <td class="${comparisons.interet[1]}">${detail2.interet_total.toLocaleString('fr-FR')}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container">
                        <h3>Détail mensuel</h3>
                        <table class="monthly-table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th colspan="6" style="text-align:center">Simulation #${s1.id_pret}</th>
                                    <th colspan="6" style="text-align:center">Simulation #${s2.id_pret}</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>Capital début</th>
                                    <th>Intérêt</th>
                                    <th>Capital remboursé</th>
                                    <th>Assurance</th>
                                    <th>Mensualité</th>
                                    <th>Capital restant</th>
                                    <th>Capital début</th>
                                    <th>Intérêt</th>
                                    <th>Capital remboursé</th>
                                    <th>Assurance</th>
                                    <th>Mensualité</th>
                                    <th>Capital restant</th>
                                </tr>
                            </thead>
                            <tbody>`;

                const maxMois = Math.max(detail1.duree + detail1.delai, detail2.duree + detail2.delai);

                for (let i = 0; i < maxMois; i++) {
                    const m1 = detail1.mois[i];
                    const m2 = detail2.mois[i];

                    html += `<tr><td>${i+1}</td>`;

                    if (m1) {
                        // Comparaisons pour chaque colonne du détail mensuel
                        let capitalDebutClass = ['', ''];
                        let interetClass = ['', ''];
                        let capitalRemClass = ['', ''];
                        let assuranceClass = ['', ''];
                        let totalPayeClass = ['', ''];
                        let capitalRestantClass = ['', ''];

                        if (m2) {
                            capitalDebutClass = getBetterValueClass(m1.capital_debut, m2.capital_debut, false);
                            interetClass = getBetterValueClass(m1.interet, m2.interet, true);
                            
                            // Comparer le rapport capital remboursé / mensualité totale
                            const ratio1 = m1.capital_rembourse / m1.total_paye;
                            const ratio2 = m2.capital_rembourse / m2.total_paye;
                            capitalRemClass = getBetterValueClass(ratio1, ratio2, false);
                            
                            assuranceClass = getBetterValueClass(m1.assurance, m2.assurance, true);
                            totalPayeClass = getBetterValueClass(m1.total_paye, m2.total_paye, true);
                            capitalRestantClass = getBetterValueClass(m1.capital_restant, m2.capital_restant, true);
                        }

                        html += `<td class="${capitalDebutClass[0]}">${m1.capital_debut.toLocaleString('fr-FR')}</td>
                                 <td class="${interetClass[0]}">${m1.interet.toLocaleString('fr-FR')}</td>
                                 <td class="${capitalRemClass[0]}">${m1.capital_rembourse.toLocaleString('fr-FR')}</td>
                                 <td class="${assuranceClass[0]}">${m1.assurance.toLocaleString('fr-FR')}</td>
                                 <td class="${totalPayeClass[0]}">${m1.total_paye.toLocaleString('fr-FR')}</td>
                                 <td class="${capitalRestantClass[0]}">${m1.capital_restant.toLocaleString('fr-FR')}</td>`;
                        
                        if (m2) {
                            html += `<td class="${capitalDebutClass[1]}">${m2.capital_debut.toLocaleString('fr-FR')}</td>
                                     <td class="${interetClass[1]}">${m2.interet.toLocaleString('fr-FR')}</td>
                                     <td class="${capitalRemClass[1]}">${m2.capital_rembourse.toLocaleString('fr-FR')}</td>
                                     <td class="${assuranceClass[1]}">${m2.assurance.toLocaleString('fr-FR')}</td>
                                     <td class="${totalPayeClass[1]}">${m2.total_paye.toLocaleString('fr-FR')}</td>
                                     <td class="${capitalRestantClass[1]}">${m2.capital_restant.toLocaleString('fr-FR')}</td>`;
                        } else {
                            html += `<td colspan="6" class="no-data">-</td>`;
                        }
                    } else {
                        html += `<td colspan="6" class="no-data">-</td>`;
                        if (m2) {
                            html += `<td class="better-value">${m2.capital_debut.toLocaleString('fr-FR')}</td>
                                     <td class="better-value">${m2.interet.toLocaleString('fr-FR')}</td>
                                     <td class="better-value">${m2.capital_rembourse.toLocaleString('fr-FR')}</td>
                                     <td class="better-value">${m2.assurance.toLocaleString('fr-FR')}</td>
                                     <td class="better-value">${m2.total_paye.toLocaleString('fr-FR')}</td>
                                     <td class="better-value">${m2.capital_restant.toLocaleString('fr-FR')}</td>`;
                        } else {
                            html += `<td colspan="6" class="no-data">-</td>`;
                        }
                    }

                    html += `</tr>`;
                }

                html += `</tbody></table></div>`;
                html += `
                    <div style="text-align:center; margin-top: 30px;">
                        <button onclick='sauverPretSimulation(${JSON.stringify(s1)})'> Changer la simulation #${s1.id_pret} en pret</button>
                        <button onclick='sauverPretSimulation(${JSON.stringify(s2)})'> Changer la simulation #${s2.id_pret} en pret</button>
                    </div>
                `;

                document.addEventListener("change", function (e) {
                if (e.target && e.target.name === "simulation" && e.target.type === "checkbox") {
                    const checkboxes = document.querySelectorAll('input[name="simulation"]:checked');
                    if (checkboxes.length > 2) {
                        e.target.checked = false;
                        alert("Vous ne pouvez sélectionner que deux simulations à comparer.");
                    }
                }
            });

                document.getElementById("comparisonResult").innerHTML = html;
                
                // Ajouter des animations aux cellules
                setTimeout(() => {
                    const cells = document.querySelectorAll('#comparisonResult td');
                    cells.forEach((cell, index) => {
                        setTimeout(() => {
                            cell.classList.add('cell-appear');
                        }, index * 10);
                    });
                }, 100);
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            chargerSimulations();
        });
        function sauverPretSimulation(sim) {
            const params = `id_type_pret=${encodeURIComponent(sim.id_type_pret)}&montant=${encodeURIComponent(sim.montant)}&mois_max=${encodeURIComponent(sim.duree_mois)}&id_client=${encodeURIComponent(sim.id_client)}&assurance=${encodeURIComponent(sim.assurance)}&delai=${encodeURIComponent(sim.delai_mois)}&datePret=${encodeURIComponent(sim.date_debut)}`;

            ajax("POST", "/prets", params, (data) => {
                alert(data.message || "Simulation sauvegardée !");
            });
        }
        document.addEventListener("DOMContentLoaded", () => {
            chargerSimulations();
        });


    </script>

</body>
</html>