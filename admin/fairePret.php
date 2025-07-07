<?php
// En haut de chaque page admin
include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prêt</title>
  <style>
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
  </style>
</head>

<body>
  <div class="main-content">
    <h1>Faire un prets</h1>
    <div>
      <p class="client"></p>
      <p class="type_pret"></p>
      <label for="montant"> Montant demandé : </label>
      <input type="number" id="montant" name="montant" min="0" placeholder="100000, 2000000, ..." />
      <br /><br />
      <label for="mois_max">Mois max : </label>
      <input type="number" id="mois_max" name="mois_max" min="0" placeholder="10, 20, ..." />
      <br /><br />
      <button onclick="demanderPret()">Confirmer</button>
      <button onclick="simulerPret()">Simuler</button>
      <p id="messagePret" style="color: green; font-weight: bold"></p>
    </div>
  </div>
  <script>
    const apiBase = "http://localhost/Git/WEB_S4_MVC/ws";

    function ajax(method, url, data, callback) {
      const xhr = new XMLHttpRequest();
      xhr.open(method, apiBase + url, true);
      xhr.setRequestHeader(
        "Content-Type",
        "application/x-www-form-urlencoded"
      );
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
          callback(JSON.parse(xhr.responseText));
        }
      };
      xhr.send(data);
    }

    function chargerAllClient() {
      ajax("GET", "/allClients", null, (data) => {
        const client = document.querySelector(".client");

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

        client.innerHTML = "";
        client.appendChild(select);
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

      const params =
        "id_type_pret=" +
        encodeURIComponent(id_type_pret) +
        "&montant=" +
        encodeURIComponent(montant) +
        "&mois_max=" +
        encodeURIComponent(mois_max) +
        "&id_client=" +
        encodeURIComponent(id_client);

      ajax("POST", "/prets", params, (data) => {
        const messageEl = document.getElementById("messagePret");
        if (data.message) {
          messageEl.textContent = data.message;
          messageEl.style.color = "blue";
        }
      });
    }

    document.addEventListener("DOMContentLoaded", chargerTypePrets);
    document.addEventListener("DOMContentLoaded", chargerAllClient);
  </script>
</body>

</html>