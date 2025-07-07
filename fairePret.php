<?php 
// En haut de chaque page admin
// include 'admin/sidebar.php'; 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prêt</title>
  </head>
  <body>
    <h1>Faire un prets</h1>
    <div>
      <p class="client"></p>
      <p class="type_pret"></p>
      <label for="montant"> Montant demandé : </label>
      <input
        type="number"
        id="montant"
        name="montant"
        min="0"
        placeholder="100000, 2000000, ..."
      />
      <br /><br />
      <label for="mois_max">Mois max : </label>
      <input
        type="number"
        id="mois_max"
        name="mois_max"
        min="0"
        placeholder="10, 20, ..."
      />
      <br /><br />
      <label for="assurance">Assurance (%) : </label>
      <input 
        type="number" 
        id="assurance" 
        name="assurance" 
        placeholder="2, 3.44, ..." 
        step="0.01"
        min="0"
      />

      <br /><br />
      <label for="">Delai 1er rembouresement : </label>
      <input type="number" id="delai" name="delai" placeholder="2">
      <br /><br />
      <button onclick="demanderPret()">Confirmer</button>
      <button onclick="simulerPret()">Simuler</button>
      <p id="messagePret" style="color: green; font-weight: bold"></p>
    </div>
    <div id="simulationResult"></div>

    <script>
      const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws";

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
        const assurance = parseFloat(document.getElementById("assurance").value || 0);
        const delai = parseInt(document.getElementById("delai").value || 0);

        const params =
          "id_type_pret=" +
          encodeURIComponent(id_type_pret) +
          "&montant=" +
          encodeURIComponent(montant) +
          "&mois_max=" +
          encodeURIComponent(mois_max) +
          "&id_client=" +
          encodeURIComponent(id_client) + 
          "&assurance=" + 
          encodeURIComponent(assurance) +
          "&delai=" +
          encodeURIComponent(delai) ;

        ajax("POST", "/prets", params, (data) => {
          const messageEl = document.getElementById("messagePret");
          if (data.message) {
            messageEl.textContent = data.message;
            messageEl.style.color = "blue";
          }
        });
      }

      function simulerPret() {
        const montant = parseFloat(document.getElementById("montant").value);
        const duree = parseInt(document.getElementById("mois_max").value);
        const id_type_pret = document.getElementById("type_pret").value;
        const assurance = parseFloat(document.getElementById("assurance").value || 0);
        const delai = parseInt(document.getElementById("delai").value || 0);

        if (!montant || !duree || !id_type_pret) {
          alert("Veuillez remplir tous les champs !");
          return;
        }

        ajax("GET", "/type_prets", null, (data) => {
          const typePret = data.find(item => item.id_type_pret == id_type_pret);
          if (!typePret) {
            alert("Type de prêt introuvable");
            return;
          }

          const taux_annuel = typePret.taux_interet / 100;
          const taux_mensuel = taux_annuel / 12;

          const annuite = (montant * taux_mensuel) / (1 - Math.pow(1 + taux_mensuel, -duree));
          const assurance_mensuelle = (montant * (assurance / 100)) / 12;
          const mensualite_totale = annuite + assurance_mensuelle;

          let html = `<h3>Simulation du prêt</h3>`;
          html += `<p>Montant: ${montant} Ar</p>`;
          html += `<p>Durée: ${duree} mois</p>`;
          html += `<p>Taux: ${typePret.taux_interet}% / an(s)</p>`;
          html += `<p>Assurance: ${assurance}%</p>`;
          html += `<p>Délai 1er remboursement: ${delai} mois</p>`;
          html += `<p>Mensualité totale: ${mensualite_totale.toFixed(2)} Ar</p>`;

          html += `<table border="1">
            <tr>
              <th>Mois</th>
              <th>Capital début</th>
              <th>Intérêt</th>
              <th>Capital remboursé</th>
              <th>Assurance</th>
              <th>Total payé</th>
              <th>Capital fin</th>
            </tr>`;

          let capital_restant = montant;
          for (let i = 1; i <= duree; i++) {
            const interet_mois = capital_restant * taux_mensuel;
            const capital_mois = annuite - interet_mois;
            const total_mois = annuite + assurance_mensuelle;

            html += `<tr>
              <td>${i + delai}</td>
              <td>${capital_restant.toFixed(2)}</td>
              <td>${interet_mois.toFixed(2)}</td>
              <td>${capital_mois.toFixed(2)}</td>
              <td>${assurance_mensuelle.toFixed(2)}</td>
              <td>${total_mois.toFixed(2)}</td>
              <td>${(capital_restant - capital_mois).toFixed(2)}</td>
            </tr>`;

            capital_restant -= capital_mois;
          }

          html += `</table>`;

          document.getElementById("simulationResult").innerHTML = html;
        });
      }
      document.addEventListener("DOMContentLoaded", chargerTypePrets);
      document.addEventListener("DOMContentLoaded", chargerAllClient);
    </script>
  </body>
</html>
