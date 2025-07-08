<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Types de prêts</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #0a0a0a;
      color: #ffffff;
    }

    .main-content {
      margin-left: 260px;
      padding: 20px;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
      }
    }

    input,
    button {
      padding: 10px;
      margin: 5px;
      border: 1px solid #2d7a5f;
      border-radius: 5px;
      background-color: #111111;
      color: #ffffff;
    }

    input::placeholder {
      color: #888;
    }

    button {
      background-color: #2d7a5f;
      cursor: pointer;
    }

    button:hover {
      background-color: #3d8a6f;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #1a4a3a;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #1a4a3a;
      color: #ffffff;
    }

    td {
      background-color: #111111;
    }

    .form-container {
      margin-bottom: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .form-container input {
      flex: 1;
      min-width: 150px;
    }
    td.numeric {
        text-align: right;
        font-variant-numeric: tabular-nums; /* alignement visuel propre */
    }
  </style>
</head>

<body>
  <div class="main-content">


    <input type="number" id="filtre-annee-debut" placeholder="Annee min">
    <input type="number" id="filtre-mois-debut" placeholder="Mois min">
    <input type="number" id="filtre-annee-fin" placeholder="Annee fin">
    <input type="number" id="filtre-mois-fin" placeholder="Mois fin">
    <button onclick="filtrerTypePrets()">Filtrer</button>
    <button onclick="resetFiltre()">Réinitialiser</button>
    <br><br>

    <table id="table-etudiants">
      <thead>
        <tr>
          <th>Annee</th>
          <th>Mois</th>
          <th>Montant disponible</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script>
    const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws";
    // const apiBase = "/ETU003113/t/WEB_S4_MVC/ws";
    let allTypePrets = [];

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

    function chargerTypePrets() {
      ajax("GET", "/admin/fonds", null, (data) => {
        allTypePrets = data;
        renderTypePrets(data);
      });
    }

    function renderTypePrets(typePrets) {
      const tbody = document.querySelector("#table-etudiants tbody");
      tbody.innerHTML = "";
      typePrets.forEach(e => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class = "numeric">${parseFloat(e.annee).toLocaleString('fr-FR', { minimumFractionDigits: 0 })}</td>
          <td class = "numeric">${parseFloat(e.mois).toLocaleString('fr-FR', { minimumFractionDigits: 0 })}</td>
          <td class = "numeric">${parseFloat(e.montant_disponible).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits:2 })}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    function filtrerTypePrets() {
      const annee_debut = parseFloat(document.getElementById("filtre-annee-debut").value) || 0;
      const mois_debut = parseFloat(document.getElementById("filtre-mois-debut").value) || 0;
      const annee_fin = parseFloat(document.getElementById("filtre-annee-fin").value) || 0;
      const mois_fin = parseFloat(document.getElementById("filtre-mois-fin").value) || 0;

      const resultat = allTypePrets.filter(e => {
        return (
          parseFloat(e.annee)>=annee_debut &&
          parseFloat(e.annee) <= annee_fin &&
          parseFloat(e.mois) >= mois_debut &&
          parseFloat(e.mois) <= mois_fin
        );
      });

      renderTypePrets(resultat);
    }

    function resetFiltre() {
      document.getElementById("filtre-annee-debut").value = "";
      document.getElementById("filtre-annee-fin").value = "";
      document.getElementById("filtre-mois-debut").value = "";
      document.getElementById("filtre-mois-fin").value = "";
      renderTypePrets(allTypePrets);
    }

    chargerTypePrets();
  </script>
</body>
</html>
