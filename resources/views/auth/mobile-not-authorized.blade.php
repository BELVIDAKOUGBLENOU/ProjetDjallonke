<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès non autorisé</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 1rem;
        }

        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            text-align: center;
            max-width: 350px;
            width: 100%;
        }

        h1 {
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .icon {
            font-size: 2.5rem;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .info {
            margin-bottom: 1.5rem;
        }

        .info p {
            font-size: 0.95rem;
            margin: 0;
            color: #475569;
        }

        .back-btn {
            background-color: #334155;
            color: white;
            border: none;
            width: 100%;
            padding: 0.9rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .back-btn:hover {
            background-color: #1e293b;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">🚫</div>
        <h1>Accès non autorisé</h1>
        <div class="info">
            <p>Vous n'êtes pas autorisé à utiliser l'application mobile.<br>
                Veuillez contacter l'administrateur si vous pensez qu'il s'agit d'une erreur.</p>
        </div>
        <button class="back-btn" onclick="window.history.back()">Retour</button>
    </div>
</body>

</html>
