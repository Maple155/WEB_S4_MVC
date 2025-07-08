<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Comparer deux simulations</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #0a0a0a;
      color: #ffffff;
      margin: 0;
    }

    .main-content {
      margin-left: 260px;
      padding: 30px;
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
      border: 1px solid #2d7a5f;
      padding: 10px;
      text-align: right;
    }

    th {
      background-color: #1a1a1a;
      text-align: center;
    }

    td:first-child {
      text-align: left;
    }

    #comparisonResult {
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }
  </style>
</head>
<body>
<div class="main-content">
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
</div>

<script>
  const apiBase = "http://localhost/WEB_S4_MVC/ws";

  function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    const fullUrl = apiBase + url + (method === "GET" && data ? "?" + data : "");
    xhr.open(method, fullUrl, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        callback(JSON.parse(xhr.responseText));
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
          const option = `<option value="${sim.id_pret}">${label}</option>`;
          select.innerHTML += option;
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

    ajax("GET", `/admin/compareSimulations`, `id1=${id1}&id2=${id2}`, (data) => {
      const s1 = data.sim1;
      const s2 = data.sim2;

      const html = `
        <table>
          <thead>
            <tr>
              <th>Champ</th>
              <th>Simulation #${s1.id_pret}</th>
              <th>Simulation #${s2.id_pret}</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Montant (Ar)</td><td>${parseFloat(s1.montant).toLocaleString('fr-FR')}</td><td>${parseFloat(s2.montant).toLocaleString('fr-FR')}</td></tr>
            <tr><td>Durée (mois)</td><td>${s1.duree_mois}</td><td>${s2.duree_mois}</td></tr>
            <tr><td>Taux annuel (%)</td><td>${s1.taux_annuel}</td><td>${s2.taux_annuel}</td></tr>
            <tr><td>Assurance (%)</td><td>${s1.assurance}</td><td>${s2.assurance}</td></tr>
            <tr><td>Délai 1er remboursement</td><td>${s1.delai_mois}</td><td>${s2.delai_mois}</td></tr>
            <tr><td>Mensualité totale (Ar)</td><td>${parseFloat(s1.mensualite).toLocaleString('fr-FR', { minimumFractionDigits: 2 })}</td><td>${parseFloat(s2.mensualite).toLocaleString('fr-FR', { minimumFractionDigits: 2 })}</td></tr>
            <tr><td>Intérêt total (Ar)</td><td>${parseFloat(s1.interet_total).toLocaleString('fr-FR', { minimumFractionDigits: 2 })}</td><td>${parseFloat(s2.interet_total).toLocaleString('fr-FR', { minimumFractionDigits: 2 })}</td></tr>
            <tr><td>Date de début</td><td>${s1.date_debut}</td><td>${s2.date_debut}</td></tr>
          </tbody>
        </table>
      `;

      document.getElementById("comparisonResult").innerHTML = html;
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    chargerSimulations();
  });
</script>

</body>
</html>
