<?php
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

    <h1>Liste de tous les prêts</h1>
    <input type="text" id="filtre-montant" placeholder="Filtrer par montant min">
    <input type="text" id="filtre-client" placeholder="Filtrer par client">
    <input type="text" id="filtre-type" placeholder="Filtrer par type de prêt">
    <button onclick="filtrerPrets()">Filtrer</button>
    <button onclick="resetFiltre()">Réinitialiser</button>

    <table id="table-etudiants">
      <thead>
        <tr>
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
    const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws";
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
          <td>${e.montant}</td>
          <td>${e.date_debut}</td>
          <td>${e.duree_mois}</td>
          <td>${e.assurance}</td>
          <td>${e.delai_mois}</td>
          <td>${e.nom_type_pret}</td>
          <td>${e.prenom}</td>
          <td>
            <button onclick='creerPdf(${e.id_pret})'>📄 PDF</button>
          </td>
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

    function creerPdf($id) {
        ajax("POST", `/prets/${id}`);
    }

    chargerPrets();
  </script>

</body>

</html>