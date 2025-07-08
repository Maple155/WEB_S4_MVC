<?php 
// En haut de chaque page admin
include 'sidebar.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Fond - Établissement Financier</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
            min-height: 100vh;
            padding-left: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .main-content {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        .form-container {
            background: #111111;
            padding: 40px;
            border-radius: 8px;
            border: 1px solid #1a4a3a;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .form-header {
            margin-bottom: 30px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #2d7a5f;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        h1 {
            color: #ffffff;
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .subtitle {
            color: #999999;
            font-size: 14px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            color: #b8b8b8;
            font-size: 14px;
            margin-bottom: 6px;
            font-weight: 300;
        }
        
        input {
            width: 100%;
            padding: 14px 16px;
            background-color: #1a1a1a;
            border: 1px solid #333333;
            border-radius: 4px;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            border-color: #2d7a5f;
            background-color: #1f1f1f;
            box-shadow: 0 0 0 3px rgba(45, 122, 95, 0.1);
        }
        
        input::placeholder {
            color: #666666;
        }
        
        input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.7;
            cursor: pointer;
        }
        
        button {
            background: linear-gradient(135deg, #2d7a5f 0%, #1a4a3a 100%);
            color: white;
            padding: 16px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 400;
            margin-top: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        button:hover {
            background: linear-gradient(135deg, #3d8a6f 0%, #2a5a4a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(45, 122, 95, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        button:disabled {
            background: #333333;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .loader {
            border: 2px solid transparent;
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: none;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .button-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
            display: none;
        }
        
        .message.error {
            background-color: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #ff6b6b;
        }
        
        .message.success {
            background-color: rgba(45, 122, 95, 0.1);
            border: 1px solid rgba(45, 122, 95, 0.3);
            color: #2d7a5f;
        }
        
        @media (max-width: 768px) {
            body {
                padding-left: 0;
                padding: 20px;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 20px;
            }
        }
        
        /* Animation d'entrée */
        .form-container {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <h1>AJOUTER FOND</h1>
                <div class="subtitle">Injection de liquidité dans le système</div>
            </div>
            
            <form id="fondForm">
                <div class="form-group">
                    <label for="montant">Montant à ajouter</label>
                    <input type="number" id="montant" placeholder="0.00" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Date d'injection</label>
                    <input type="date" id="date" required>
                </div>
                
                <button type="submit" id="submitBtn">
                    <div class="button-content">
                        <span id="btnText">Ajouter le fond</span>
                        <div class="loader" id="loader"></div>
                    </div>
                </button>
            </form>
            
            <div id="message" class="message"></div>
        </div>
    </div>

    <script>
        // const apiBase = "http://localhost/serveur/S4/WEB_S4_MVC/ws";
        const apiBase = "/ETU003113/t/WEB_S4_MVC/ws";
        
        // Définir la date d'aujourd'hui par défaut
        document.getElementById('date').valueAsDate = new Date();

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        callback(JSON.parse(xhr.responseText));
                    } else {
                        showMessage('Erreur de connexion au serveur', 'error');
                        hideLoader();
                    }
                }
            };
            xhr.send(data);
        }

        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }

        function showLoader() {
            document.getElementById('btnText').style.display = 'none';
            document.getElementById('loader').style.display = 'block';
            document.getElementById('submitBtn').disabled = true;
        }

        function hideLoader() {
            document.getElementById('btnText').style.display = 'block';
            document.getElementById('loader').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
        }

        function addFond() {
            const montant = document.getElementById("montant").value;
            const date = document.getElementById("date").value;
            
            if (!montant || !date) {
                showMessage('Veuillez remplir tous les champs', 'error');
                return;
            }
            
            if (parseFloat(montant) <= 0) {
                showMessage('Le montant doit être supérieur à 0', 'error');
                return;
            }
            
            showLoader();
            
            const data = `montant=${montant}&date=${date}`;
            ajax("POST", `/admin/addFond`, data, (response) => {
                hideLoader();
                
                if (response && response.success) {
                    showMessage('Fond ajouté avec succès!', 'success');
                    document.getElementById('fondForm').reset();
                    document.getElementById('date').valueAsDate = new Date();
                } else {
                    showMessage(response.message || 'Erreur lors de l\'ajout du fond', 'error');
                }
            });
        }

        // Gestion du formulaire
        document.getElementById('fondForm').addEventListener('submit', function(e) {
            e.preventDefault();
            addFond();
        });

        // Validation en temps réel
        document.getElementById('montant').addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = '';
            }
        });
    </script>
</body>
</html>