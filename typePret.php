<?php 
// En haut de chaque page admin
include 'admin/sidebar.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des étudiants</title>
  <style>
    body { font-family: sans-serif; padding: 20px; }
    input, button { margin: 5px; padding: 5px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
  </style>
</head>
<body>

  <h1>Gestion des étudiants</h1>

  <div>
    <input type="hidden" id="id">
    <input type="text" id="nom" placeholder="Nom">
    <input type="number" id="taux_interet" placeholder="Taux d'interet">
    <input type="number" id="duree_max_mois" placeholder="Duree max de pret (mois)">
    <input type="number" id="montant_min" placeholder="Montant minimum">
    <input type="number" id="montant_max" placeholder="Montant maximum">
    <input type="number" id="age_min" placeholder="Age minimum">
    <button onclick="ajouterOuModifier()">Ajouter / Modifier</button>
  </div>

  <table id="table-etudiants">
    <thead>
      <tr>
        <th>ID</th><th>Nom</th><th>Taux d'interet</th><th>duree_max_mois</th><th>Montant minimum</th><th>Montant maximum</th><th>Age minimum</th><th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

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

    function chargerTypePrets() {
      ajax("GET", "/type_prets", null, (data) => {
        const tbody = document.querySelector("#table-etudiants tbody");
        tbody.innerHTML = "";
        data.forEach(e => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${e.id_type_pret}</td>
            <td>${e.nom}</td>
            <td>${e.taux_interet}</td>
            <td>${e.duree_max_mois}</td>
            <td>${e.montant_min}</td>
            <td>${e.montant_max}</td>
            <td>${e.age_min}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(e)})'>✏️</button>
              <button onclick='supprimerEtudiant(${e.id})'>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function ajouterOuModifier() {
      const id = document.getElementById("id").value;
      const nom = document.getElementById("nom").value;
      const taux_interet = document.getElementById("taux_interet").value;
      const duree_max_mois = document.getElementById("duree_max_mois").value;
      const montant_min = document.getElementById("montant_min").value;
      const montant_max = document.getElementById("montant_max").value;
      const age_min = document.getElementById("age_min").value;

      const data = `nom=${encodeURIComponent(nom)}&taux_interet=${encodeURIComponent(taux_interet)}&duree_max_mois=${encodeURIComponent(duree_max_mois)}&montant_min=${montant_min}&montant_max=${montant_max}&age_min=${age_min}`;

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
      document.getElementById("id").value = e.id;
      document.getElementById("nom").value = e.nom;
      document.getElementById("taux_interet").value = e.taux_interet;
      document.getElementById("duree_max_mois").value = e.duree_max_mois;
      document.getElementById("montant_min").value = e.montant_min;
      document.getElementById("montant_max").value = e.montant_max;
      document.getElementById("age_min").value = e.age_min;
    }

    function supprimerEtudiant(id) {
      if (confirm("Supprimer cet étudiant ?")) {
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