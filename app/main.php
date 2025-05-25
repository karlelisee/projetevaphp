<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Accueil - Gestionnaire de fichiers</title>
    <style>
        body {
            background: linear-gradient(120deg, #74b9ff, #a29bfe);
            color: #fff;
            font-family: 'Arial', sans-serif;
        }
        .header {
            padding: 2rem 0;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .header h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .header p {
            font-size: 1.2rem;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 2rem;
            margin: 2rem 0;
        }
        .feature {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            flex: 1 1 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .feature h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .feature p {
            font-size: 1rem;
        }
        .cta {
            text-align: center;
            margin-top: 2rem;
        }
        .cta a {
            display: inline-block;
            padding: 0.8rem 2rem;
            font-size: 1.2rem;
            background-color: #0984e3;
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }
        .cta a:hover {
            background-color: #6c5ce7;
        }
        footer {
            text-align: center;
            padding: 1rem 0;
            background-color: rgba(0, 0, 0, 0.7);
            color: #ddd;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Bienvenue sur le Gestionnaire de fichiers</h1>
    <p>Un espace sécurisé pour organiser, partager et gérer vos fichiers en toute simplicité.</p>
</div>

<div class="container">
    <h2 class="text-center mt-4">Pourquoi utiliser notre plateforme ?</h2>
    <div class="features">
        <div class="feature">
            <h3>Stockage Sécurisé</h3>
            <p>Vos fichiers sont stockés de manière sécurisée avec une confidentialité garantie.</p>
        </div>
        <div class="feature">
            <h3>Organisation Simplifiée</h3>
            <p>Classez vos fichiers par catégories, dossiers ou ajoutez-les à vos favoris pour un accès rapide.</p>
        </div>
        <div class="feature">
            <h3>Collaboration Facile</h3>
            <p>Ajoutez des collaborateurs pour partager des fichiers et travailler ensemble en temps réel.</p>
        </div>
        <div class="feature">
            <h3>Accès Rapide</h3>
            <p>Recherchez rapidement vos fichiers grâce à notre barre de recherche avancée.</p>
        </div>
    </div>
    
    <div class="cta">
        <a href="index.php">Commencer maintenant</a>
    </div>
</div>



</body>
</html>
