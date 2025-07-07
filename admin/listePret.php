<?php
// En haut de chaque page admin
include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Types de prêts</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .main-content {
            margin-left: 250px;
            /* largeur de la sidebar */
            padding: 20px;
            transition: margin-left 0.3s;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        input,
        button {
            margin: 5px;
            padding: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="main-content">

        <h1>Type de prêts</h1>

        <table id="table-etudiants">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Montant</th>
                    <th>Date de début</th>
                    <th>Duree mois</th>
                    <th>Assurance</th>
                    <th>Delais premier remboursement</th>
                    <th>Type pret</th>
                    <th>Client</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script>
        const apiBase = "http://localhost/Git/WEB_S4_MVC/ws";

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
                const tbody = document.querySelector("#table-etudiants tbody");
                tbody.innerHTML = "";
                data.forEach(e => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
            <td>${e.id_pret}</td>
            <td>${e.montant}</td>
            <td>${e.date_debut}</td>
            <td>${e.duree_mois}</td>
            <td>${e.assurance}</td>
            <td>${e.delai_mois}</td>
            <td>${e.nom_type_pret}</td>
            <td>${e.prenom}</td>
            <td>
              <button onclick='telechargerPdf(${e.id_pret})'>✏️</button>
            </td>
          `;
                    tbody.appendChild(tr);
                });
            });
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