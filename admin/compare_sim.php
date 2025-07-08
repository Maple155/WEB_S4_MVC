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
    </style>
</head>
<body>

    <h2>Comparer deux simulations</h2>

    <div class="form-group">
        <select id="sim1">
            <option value="">-- Simulation 1 --</option>
        </select>

        <select id="sim2">
            <option value="">-- Simulation 2 --</option>
        </select>

        <button onclick="comparerSimulations()">Comparer</button>
    </div>

    <div id="comparisonResult"></div>

    <script>
        // const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws";
        const apiBase = "/ETU003113/t/WEB_S4_MVC/ws";
        
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
                const select1 = document.getElementById("sim1");
                const select2 = document.getElementById("sim2");

                [select1, select2].forEach(select => {
                    select.innerHTML = `<option value="">-- Choisir une simulation --</option>`;
                    data.forEach(sim => {
                        const label = `#${sim.id_pret} - ${parseFloat(sim.montant).toLocaleString('fr-FR')} Ar - ${sim.duree_mois} mois`;
                        const option = document.createElement("option");
                        option.value = sim.id_pret;
                        option.textContent = label;
                        select.appendChild(option);
                    });
                });
            });
        }

        function comparerSimulations() {
            const id1 = document.getElementById("sim1").value;
            const id2 = document.getElementById("sim2").value;

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
                    const duree = parseInt(sim.duree_mois);
                    const assurance = parseFloat(sim.assurance);
                    const delai = parseInt(sim.delai_mois);
                    const taux_annuel = parseFloat(sim.taux_interet) / 100;
                    const taux_mensuel = taux_annuel / 12;

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
                        <button onclick='sauverPretSimulation(${JSON.stringify(s1)})'>💾 Sauvegarder Simulation #${s1.id_pret}</button>
                        <button onclick='sauverPretSimulation(${JSON.stringify(s2)})'>💾 Sauvegarder Simulation #${s2.id_pret}</button>
                    </div>
                `;


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

    </script>

</body>
</html>