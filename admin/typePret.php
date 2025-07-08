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


    <input type="text" id="filtre-nom" placeholder="Filtrer par nom">
    <input type="number" id="filtre-taux" placeholder="Taux min">
    <input type="number" id="filtre-montant-max" placeholder="Montant max">
    <button onclick="filtrerTypePrets()">Filtrer</button>
    <button onclick="resetFiltre()">Réinitialiser</button>
    <br><br>
    <div>
      <input type="hidden" id="id">
      <input type="text" id="nom" placeholder="Nom">
      <input type="number" id="taux_interet" placeholder="Taux d'intérêt">
      <input type="number" id="duree_max_mois" placeholder="Durée max (mois)">
      <input type="number" id="montant_min" placeholder="Montant min">
      <input type="number" id="montant_max" placeholder="Montant max">
      <input type="number" id="age_min" placeholder="Âge minimum">
      <button onclick="ajouterOuModifier()">Ajouter / Modifier</button>
    </div>

    <table id="table-etudiants">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Taux (%)</th>
          <th>Durée (mois)</th>
          <th>Montant min (Ar)</th>
          <th>Montant max (Ar)</th>
          <th>Âge min</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script>
    const apiBase = "http://localhost/WEB_S4_MVC/ws";
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
      ajax("GET", "/type_prets", null, (data) => {
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
          <td>${e.nom}</td>
          <td class = "numeric">${parseFloat(e.taux_interet).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits:2 })}</td>
          <td class = "numeric">${parseFloat(e.duree_max_mois).toLocaleString('fr-FR', { minimumFractionDigits: 0 })}</td>
          <td class = "numeric">${parseFloat(e.montant_min).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits:2 })}</td>
          <td class = "numeric">${parseFloat(e.montant_max).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits:2 })}</td>
          <td class = "numeric">${parseFloat(e.age_min).toLocaleString('fr-FR', { minimumFractionDigits: 0 })}</td>
          <td>
            <button onclick='remplirFormulaire(${JSON.stringify(e)})'>✏️</button>
            <button onclick='supprimerEtudiant(${e.id_type_pret})'>🗑️</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    function filtrerTypePrets() {
      const nom = document.getElementById("filtre-nom").value.toLowerCase();
      const tauxMin = parseFloat(document.getElementById("filtre-taux").value) || 0;
      const montantMax = parseFloat(document.getElementById("filtre-montant-max").value) || Infinity;

      const resultat = allTypePrets.filter(e => {
        return (
          e.nom.toLowerCase().includes(nom) &&
          parseFloat(e.taux_interet) >= tauxMin &&
          parseFloat(e.montant_max) <= montantMax
        );
      });

      renderTypePrets(resultat);
    }

    function resetFiltre() {
      document.getElementById("filtre-nom").value = "";
      document.getElementById("filtre-taux").value = "";
      document.getElementById("filtre-montant-max").value = "";
      renderTypePrets(allTypePrets);
    }

    function ajouterOuModifier() {
      const id = document.getElementById("id").value;
      const nom = document.getElementById("nom").value;
      const taux_interet = document.getElementById("taux_interet").value;
      const duree_max_mois = document.getElementById("duree_max_mois").value;
      const montant_min = document.getElementById("montant_min").value;
      const montant_max = document.getElementById("montant_max").value;
      const age_min = document.getElementById("age_min").value;

      const data = `nom=${encodeURIComponent(nom)}&taux_interet=${taux_interet}&duree_max_mois=${duree_max_mois}&montant_min=${montant_min}&montant_max=${montant_max}&age_min=${age_min}`;

      if (id) {
        ajax("PUT", `/type_prets/${id}`, data, () => {
          resetForm();
          chargerTypePrets();
        });
      } else {
        ajax("POST", "/type_prets", data, () => {
          resetForm();
          chargerTypePrets();
        });
      }
    }

    function remplirFormulaire(e) {
      document.getElementById("id").value = e.id_type_pret;
      document.getElementById("nom").value = e.nom;
      document.getElementById("taux_interet").value = e.taux_interet;
      document.getElementById("duree_max_mois").value = e.duree_max_mois;
      document.getElementById("montant_min").value = e.montant_min;
      document.getElementById("montant_max").value = e.montant_max;
      document.getElementById("age_min").value = e.age_min;
    }

    function supprimerEtudiant(id) {
      if (confirm("Supprimer ce type de prêt ?")) {
        ajax("DELETE", `/type_prets/${id}`, null, () => {
          chargerTypePrets();
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("nom").value = "";
      document.getElementById("taux_interet").value = "";
      document.getElementById("duree_max_mois").value = "";
      document.getElementById("montant_min").value = "";
      document.getElementById("montant_max").value = "";
      document.getElementById("age_min").value = "";
    }

    chargerTypePrets();
  </script>
</body>
</html>
