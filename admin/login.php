<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion Admin - Établissement Financier</title>
    <style>
        /* Reset et base */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            height: 100vh;
            background-color: #0a0a0a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #d4ffd4;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Conteneur du formulaire */
        .login-box {
            background-color: #111111;
            padding: 2rem 2.5rem;
            border-radius: 8px;
            box-shadow: 0 0 15px #2d7a5faa;
            width: 320px;
            text-align: center;
            border: 1px solid #1a4a3a;
        }

        /* Logo */
        .login-box img {
            width: 70px;
            margin-bottom: 20px;
            border-radius: 50%;
            border: 2px solid #2d7a5f;
            opacity: 0.9;
        }

        /* Titre */
        .login-box h1 {
            font-weight: 300;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }

        /* Champs input */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            background-color: #000000;
            border: 1px solid #2d7a5f;
            color: #d4ffd4;
            padding: 12px 15px;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 1rem;
            transition: border-color 0.3s ease;
        }
        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #799d8a;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #3d8a6f;
            box-shadow: 0 0 6px #3d8a6faa;
        }

        /* Bouton */
        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #2d7a5f;
            border: none;
            border-radius: 5px;
            color: #d4ffd4;
            cursor: pointer;
            transition: background-color 0.3s ease;
            user-select: none;
            position: relative;
        }
        button:hover {
            background-color: #3d8a6f;
        }

        /* Loader dans le bouton */
        .loader {
            position: absolute;
            right: 15px;
            top: 50%;
            width: 18px;
            height: 18px;
            margin-top: -9px;
            border: 3px solid #111111;
            border-top: 3px solid #d4ffd4;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }

        /* Message erreur / info */
        #message {
            margin-top: 12px;
            font-size: 14px;
            min-height: 20px;
            color: #e74c3c;
            user-select: none;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="../sql/banque.png" alt="Logo Banque" />
        <h1>Espace Administrateur</h1>

        <input type="text" id="nom" placeholder="Nom de l'établissement" value="MicroFinance ITU" autocomplete="username" />
        <input type="password" id="mdp" placeholder="Mot de passe" value="admin123" autocomplete="current-password" />
        
        <button id="submitBtn" onclick="connect()">
            Se connecter
            <div class="loader" id="loader"></div>
        </button>

        <div id="message"></div>
    </div>

    <script>
        // const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws";
        const apiBase = "/ETU003113/t/WEB_S4_MVC/ws";
        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            callback(JSON.parse(xhr.responseText));
                        } catch {
                            callback(null);
                        }
                    } else {
                        callback(null);
                    }
                }
            };
            xhr.send(data);
        }

        function connect() {
            const nom = document.getElementById("nom").value.trim();
            const mdp = document.getElementById("mdp").value.trim();
            const message = document.getElementById("message");
            const loader = document.getElementById("loader");
            const btn = document.getElementById("submitBtn");

            message.textContent = "";
            if (!nom || !mdp) {
                message.textContent = "Veuillez remplir tous les champs.";
                return;
            }

            loader.style.display = "inline-block";
            btn.disabled = true;

            const data = `nom=${encodeURIComponent(nom)}&mdp=${encodeURIComponent(mdp)}`;

            ajax("POST", "/admin/login", data, (response) => {
                loader.style.display = "none";
                btn.disabled = false;

                if (response) {
                    // Redirection réussie
                    window.location.href = "add-fond.php";
                } else {
                    message.textContent = "Identifiants incorrects.";
                }
            });
        }
    </script>
</body>
</html>
