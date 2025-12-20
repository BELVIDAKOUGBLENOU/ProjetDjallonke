<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification réussie</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #0f172a;
        }

        img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .info {
            margin-bottom: 1.5rem;
        }

        .info h2 {
            font-size: 1.1rem;
            margin: 0;
            font-weight: 600;
        }

        .info p {
            font-size: 0.9rem;
            margin: 0;
            color: #475569;
        }

        button {
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

        button:hover {
            background-color: #1e293b;
        }

        .arrow {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 2rem;
            color: #334155;
            animation: bounce 1s infinite alternate;

            user-select: none;
        }

        @keyframes bounce {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(-10px);
            }
        }

        .hint {
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #475569;
            display: none;
        }

        .default-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>🔏 Authentification réussie</h1>
        @if ($user->photo)
            <img src="{{ asset($user->photo) }}" alt="Photo de profil">
        @else
            <div class="" style="display: flex; justify-content: center;">

                <div class="default-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-user-icon lucide-user">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg></div>
            </div>
        @endif
        <div class="info">
            <h2>{{ $user->name . ' ' . $user->last_name }}</h2>
            <p>{{ $user->email }}</p>
        </div>

        <button id="continueBtn">Continuer</button>
        <p id="hint" class="hint">Si la fenêtre ne se ferme pas, appuyez sur le “X” en haut à gauche.</p>
    </div>

    <div id="arrow" class="arrow">⬆️</div>

    <script>
        const redirectUrl = "{{ $redirect_url }}";
        const arrow = document.getElementById('arrow');
        const hint = document.getElementById('hint');
        const button = document.getElementById('continueBtn');

        button.addEventListener('click', function() {
            try {
                window.location.href = redirectUrl;
                window.close();
            } catch (e) {
                console.log("Impossible de fermer la fenêtre automatiquement");
            }

            // Afficher la flèche et le message d’aide
            arrow.style.display = 'block';
            hint.style.display = 'block';
            button.disabled = true;
            button.innerText = "Redirection...";
        });
    </script>

    @php
        Session::forget('redirect_to');
    @endphp
</body>

</html>
