<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Intérêts Mensuels - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    .filter-container {
      margin-bottom: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .filter-container input,
    .filter-container button {
      padding: 10px;
      background-color: #111;
      border: 1px solid #2d7a5f;
      color: #fff;
      border-radius: 5px;
    }

    .filter-container button {
      background-color: #2d7a5f;
      cursor: pointer;
    }

    .filter-container button:hover {
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
    }

    td {
      background-color: #111;
    }

    #message {
      margin-top: 20px;
      padding: 10px;
      border-radius: 5px;
    }

    .success {
      background-color: #1a4a3a;
      color: #d4ffd4;
    }

    .error {
      background-color: #661111;
      color: #ffdada;
    }
  </style>
</head>

<body>
  <div class="main-content">

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
          <!-- Résultats dynamiques -->
        </tbody>
      </table>
    </div>

    <div id="message"></div>
  </div>

  <script>
    const apiBase = "http://localhost/Git/WEB_S4_MVC/ws/";

    document.addEventListener('DOMContentLoaded', () => {
      const end = new Date();
      const start = new Date();
      start.setMonth(end.getMonth() - 11);

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

      const data = `date_debut=${startDate}&date_fin=${endDate}`;

      ajax("GET", "admin/interets", data, function(response) {
        const tbody = document.getElementById('interestsBody');
        tbody.innerHTML = '';

        if (response.error) {
          messageEl.textContent = response.error;
          messageEl.className = 'error';
          return;
        }

        if (response.details && response.details.length > 0) {
          response.details.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${item.periode}</td>
              <td>${item.nombre_prets}</td>
              <td>${parseFloat(item.capital_total).toLocaleString('fr-FR')} €</td>
              <td>${parseFloat(item.interets_mensuels).toLocaleString('fr-FR', { minimumFractionDigits: 2 })} €</td>
            `;
            tbody.appendChild(row);
          });

          messageEl.textContent = `Total des intérêts : ${parseFloat(response.total_interets).toLocaleString('fr-FR', { minimumFractionDigits: 2 })} €`;
          messageEl.className = 'success';
        } else {
          messageEl.textContent = 'Aucun résultat trouvé';
          messageEl.className = 'error';
        }
      });
    }

    function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        if (method === "GET" && data) {
            xhr.open(method, apiBase + url + "?" + data, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            }
            };
            xhr.send(null);  // Pas de body pour GET
        } else {
            // Pour POST/PUT/DELETE avec body
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            }
            };
            xhr.send(data);
        }
    }

  </script>
</body>
</html>
