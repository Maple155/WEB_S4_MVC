<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Établissement Financier</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .logo {
            width: 80px;
            margin-bottom: 1rem;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error {
            color: #e74c3c;
            margin-top: 10px;
            font-size: 14px;
        }
        .success {
            color: #27ae60;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
            display: none;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../sql/banque.png" alt="Logo Banque" class="logo">
        <h1>Espace Administrateur</h1>
            <input type="text" id="nom" placeholder="Nom de l'établissement" required>
            <input type="password" id="mdp" placeholder="Mot de passe" required>
            <button id="submitBtn" onclick="connect()">
                <span id="btnText">Se connecter</span>
                <div class="loader" id="loader"></div>
            </button>        
        <div id="message" class="error"></div>
    </div>

    <script>
        const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws/";

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
    function connect(){
        const nom = document.getElementById("nom").value;
        const mdp = document.getElementById("mdp").value;
        const data = `nom=${nom}&mdp=${mdp}`;
        ajax("POST", `admin/login`, data, (response)=>{
            if(response){
                window.location.href = "add-fond.php";
            }else
            {
                alert("Identifiants Incorrects");
            }
        });
    }
    </script>
</body>
</html>