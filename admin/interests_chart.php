<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Graphique Intérêts Mensuels</title>
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

    canvas {
      background-color: #111;
      border: 1px solid #2d7a5f;
      border-radius: 8px;
      padding: 10px;
      max-width: 100%;
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="main-content">

    <div class="filter-container">
      <input type="month" id="startDate">
      <input type="month" id="endDate">
      <button onclick="loadChart()">Filtrer</button>
    </div>

    <canvas id="interestChart" height="100"></canvas>
    <div id="message"></div>
  </div>

  <script>
    const apiBase = "http://localhost/Git/WEB_S4_MVC/ws";
    let chartInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
      const end = new Date();
      const start = new Date();
      start.setMonth(end.getMonth() - 11);

      document.getElementById('startDate').value = formatDate(start);
      document.getElementById('endDate').value = formatDate(end);

      loadChart();
    });

    function formatDate(date) {
      return date.toISOString().slice(0, 7);
    }

    function loadChart() {
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      const messageEl = document.getElementById('message');

      messageEl.textContent = 'Chargement...';
      messageEl.className = '';

      const data = `date_debut=${startDate}&date_fin=${endDate}`;

      ajax("GET", "/admin/interets", data, (response) => {
        if (response.error) {
          messageEl.textContent = response.error;
          messageEl.className = 'error';
          return;
        }

        if (response.details && response.details.length > 0) {
          const labels = response.details.map(item => item.periode);
          const dataValues = response.details.map(item => parseFloat(item.interets_mensuels));

          renderChart(labels, dataValues);

          messageEl.textContent = `Total des intérêts : ${parseFloat(response.total_interets).toLocaleString('fr-FR', { minimumFractionDigits: 2 })} €`;
          messageEl.className = 'success';
        } else {
          if (chartInstance) chartInstance.destroy();
          messageEl.textContent = 'Aucun résultat trouvé';
          messageEl.className = 'error';
        }
      });
    }

    function renderChart(labels, dataValues) {
      const ctx = document.getElementById('interestChart').getContext('2d');

      if (chartInstance) chartInstance.destroy();

      chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Intérêts (€)',
            data: dataValues,
            backgroundColor: '#2d7a5f',
            borderColor: '#3d8a6f',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: context => `${context.parsed.y.toLocaleString('fr-FR', {minimumFractionDigits: 2})} €`
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                color: '#ffffff',
                callback: value => value.toLocaleString('fr-FR') + ' €'
              },
              grid: { color: '#333' }
            },
            x: {
              ticks: { color: '#ffffff' },
              grid: { color: '#333' }
            }
          }
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
