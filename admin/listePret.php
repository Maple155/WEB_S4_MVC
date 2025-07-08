<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Types de prêts</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            transition: margin-left 0.3s;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        input {
            background-color: #111;
            color: #fff;
            border: 1px solid #2d7a5f;
            padding: 10px;
            margin: 8px 10px 8px 0;
            border-radius: 4px;
        }

        input::placeholder {
            color: #aaa;
        }

        button {
            background-color: #2d7a5f;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            margin: 8px 8px 8px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #3d8a6f;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #111;
            color: #fff;
        }

        th,
        td {
            border: 1px solid #2d7a5f;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #1a1a1a;
        }

        td button {
            padding: 6px 10px;
            font-size: 14px;
        }
        td.numeric {
            text-align: right;
            font-variant-numeric: tabular-nums; /* alignement visuel propre */
        }

    </style>
</head>

<body>
    <div class="main-content">
        <h1>Liste de tous les prêts</h1>

        <input type="text" id="filtre-montant" placeholder="Filtrer par montant min">
        <input type="text" id="filtre-client" placeholder="Filtrer par client">
        <input type="text" id="filtre-type" placeholder="Filtrer par type de prêt">
        <button onclick="filtrerPrets()">Filtrer</button>
        <button onclick="resetFiltre()">Réinitialiser</button>

        <table id="table-etudiants">
            <thead>
                <tr>
                    <th>Montant (Ar)</th>
                    <th>Date de début</th>
                    <th>Durée (mois)</th>
                    <th>Assurance (%)</th>
                    <th>Délai 1er remboursement (mois)</th>
                    <th>Type prêt</th>
                    <th>Client</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script>
        const apiBase = "http://localhost/Git/WEB_S4_MVC/ws";

        let allPrets = [];

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

        function chargerPrets() {
            ajax("GET", "/prets", null, (data) => {
                allPrets = data;
                renderPrets(data);
            });
        }

        function renderPrets(prets) {
            const tbody = document.querySelector("#table-etudiants tbody");
            tbody.innerHTML = "";
            prets.forEach(e => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
            <td class="numeric">${parseFloat(e.montant).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits:2 })}</td>
          <td class = "numeric">${parseFloat(e.date_debut).toLocaleString('fr-FR', { minimumFractionDigits: 0 })}</td>
          <td class = "numeric">${parseFloat(e.duree_mois).toLocaleString('fr-FR', { minimumFractionDigits: 0 })}</td>
          <td class = "numeric">${parseFloat(e.assurance).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits:2 })}</td>
          <td class = "numeric">${parseFloat(e.delai_mois).toLocaleString('fr-FR', { minimumFractionDigits: 0 })}</td>
          <td >${e.nom_type_pret}</td>
          <td>${e.prenom}</td>
          <td><button onclick='telechargerPdf(${e.id_pret})'>📄 PDF</button></td>
        `;
                tbody.appendChild(tr);
            });
        }

        function filtrerPrets() {
            const montantMinStr = document.getElementById("filtre-montant").value.trim();
            const montantMin = montantMinStr === "" ? null : parseFloat(montantMinStr);
            const client = document.getElementById("filtre-client").value.trim().toLowerCase();
            const type = document.getElementById("filtre-type").value.trim().toLowerCase();

            const resultat = allPrets.filter(e => {
                const okMontant = montantMin === null || parseFloat(e.montant) >= montantMin;
                const nomComplet = ((e.prenom || "") + " " + (e.nom || "")).toLowerCase();
                const okClient = !client || nomComplet.includes(client) || (e.prenom && e.prenom.toLowerCase().includes(client));
                const okType = !type || (e.nom_type_pret && e.nom_type_pret.toLowerCase().includes(type));
                return okMontant && okClient && okType;
            });

            renderPrets(resultat);
        }

        function resetFiltre() {
            document.getElementById("filtre-montant").value = "";
            document.getElementById("filtre-client").value = "";
            document.getElementById("filtre-type").value = "";
            renderPrets(allPrets);
        }

        function telechargerPdf(id) {
            // Méthode simple avec window.open
            const url = apiBase + "/prets/" + id;
            window.open(url, '_blank');
        }

        chargerPrets();
    </script>
</body>

</html>