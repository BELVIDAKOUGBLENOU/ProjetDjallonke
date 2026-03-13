# 🔗 Intégration gRPC via API Gateway - Guide Rapide

## 🎯 Ce qui a été fait

Vous avez maintenant une **architecture de communication inter-services** complète où :

✅ **Le Backend Principal** et **le Service IAM** communiquent **uniquement via l'API Gateway**
✅ **Zéro communication directe** entre les deux projets
✅ **Tous les tokens** sont vérifiés par le IAM via HTTP/REST (exposant la logique gRPC)

## 🏗️ Architecture Finale

```
┌─────────────────────────────────────┐
│   Projet Principal (Backend)        │
│   - Port HTTP: 5601                 │
│   - Middleware: @auth.iam           │
└────────────────┬────────────────────┘
                 │
         💼 IamGrpcService
                 │
                 ▼
    ┌─────────────────────────────┐
    │   API Gateway (Node.js)     │
    │   Port: 3000                │
    │   Route: /iam/*             │
    └────────────┬────────────────┘
                 │
                 ▼
    ┌─────────────────────────────┐
    │   IAM Service (Laravel)     │
    │   Port HTTP: 5602           │
    │   Port gRPC: 9090           │
    │   Endpoint: /api/auth/verify-token
    └─────────────────────────────┘
```

## 📦 Fichiers Créés/Modifiés

### ✨ Nouveau Contrôleur IAM

**File**: [iam/app/Http/Controllers/AuthTokenController.php](./iam/app/Http/Controllers/AuthTokenController.php)

- Expose le service gRPC via HTTP
- Route: `POST /api/auth/verify-token`
- Vérifie les tokens Passport et retourne les données utilisateur

### 🔄 Service Principal Mis à Jour

**File**: [app/Services/IamGrpcService.php](./app/Services/IamGrpcService.php)

- **Avant**: Se connectait directement au gRPC (port 6001 - incorrect)
- **Après**: Passe par l'API Gateway (`http://127.0.0.1:3000/iam/api/auth/verify-token`)

### ⚡ Routes API IAM Mises à Jour

**File**: [iam/routes/api.php](./iam/routes/api.php)

- Ajout de la route: `POST /api/auth/verify-token`

### 🚪 API Gateway Reconfigurée

**File**: [api-gateway/server_list_local.json](./api-gateway/server_list_local.json)

- **Avant**: `/iam-grpc` → `http://127.0.0.1:6001` (port RPC) ❌
- **Après**: `/iam` → `http://127.0.0.1:5602` (port HTTP du IAM) ✅

### 📖 Documentation Complète

- [ARCHITECTURE_GRPC.md](./ARCHITECTURE_GRPC.md) - Architecture détaillée
- [SETUP_INSTRUCTIONS.md](./SETUP_INSTRUCTIONS.md) - Instructions de démarrage et troubleshooting

### 🚀 Scripts de Démarrage

- [start-services.sh](./start-services.sh) - Démarre les 4 services automatiquement

## 🎬 Démarrage Rapide

### Option 1: Automatique (Recommandé)

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke
chmod +x start-services.sh
./start-services.sh
```

### Option 2: Manuel (4 terminaux)

**Terminal 1 - Backend**:

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke
php artisan serve --port=5601
```

**Terminal 2 - IAM HTTP**:

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke/iam
php artisan serve --port=5602
```

**Terminal 3 - IAM gRPC**:

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke/iam
rr serve  # RoadRunner pour le gRPC
```

**Terminal 4 - API Gateway**:

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke/api-gateway
npm run dev
```

## ✅ Test de Vérification

### 1️⃣ Créer un utilisateur de test dans l'IAM

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke/iam
php artisan tinker

>>> use App\Models\User;
>>> use Illuminate\Support\Facades\Hash;
>>> $user = User::create([
...     'name' => 'Test User',
...     'email' => 'test@example.com',
...     'password' => Hash::make('password123'),
...     'uid' => \Illuminate\Support\Str::uuid(),
... ]);
>>> $token = $user->createToken('test-token')->accessToken;
>>> echo $token;
# Copier le token
```

### 2️⃣ Tester via l'API Gateway

```bash
curl -X POST http://127.0.0.1:3000/iam/api/auth/verify-token \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Content-Type: application/json"
```

**Response attendue** (200 OK):

```json
{
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "uid": "uuid-string",
    "email_verified_at": null,
    "created_at": "2026-03-10T...",
    "updated_at": "2026-03-10T...",
    "fcm_token": null
}
```

### 3️⃣ Tester le middleware du Backend

```bash
curl -X GET http://127.0.0.1:5601/api/animals \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI"
```

Si le middleware fonctionne:

- ✅ Token vérifié via l'API Gateway
- ✅ Utilisateur créé/synchronisé dans la DB du Backend
- ✅ Requête continue vers le contrôleur

## 🔑 Points Clés de l'Architecture

| Aspect                 | Détail                                     |
| ---------------------- | ------------------------------------------ |
| **Communication**      | HTTP/REST via API Gateway (port 3000)      |
| **Vérification Token** | Endpoint: `/iam/api/auth/verify-token`     |
| **Authentification**   | Bearer Token (header Authorization)        |
| **Token Storage**      | Passport OAuth dans la DB du IAM           |
| **Services Isolation** | Pas de communication directe entre projets |
| **Format Réponse**     | JSON (Array en PHP)                        |

## 📊 Ports En Service

| Service | Port | Status          |
| ------- | ---- | --------------- |
| Backend | 5601 | HTTP/Laravel    |
| IAM     | 5602 | HTTP/Laravel    |
| gRPC    | 9090 | RoadRunner/gRPC |
| Gateway | 3000 | Node.js/Express |
| MySQL   | 3306 | Database        |

## 🐛 Troubleshooting Rapide

### ❌ "Connection refused" sur /iam/api/auth/verify-token

```bash
# Vérifier que le IAM tourne
curl http://127.0.0.1:5602
```

### ❌ "Invalid token" même avec token valide

```bash
# Vérifier que l'utilisateur existe dans la DB IAM
cd iam && php artisan tinker
>>> User::find(1);
```

### ❌ "Gateway connection refused"

```bash
# API Gateway startup check
curl http://127.0.0.1:3000/health
```

## 📚 Ressources

| Document                                                                                               | Contenu                                            |
| ------------------------------------------------------------------------------------------------------ | -------------------------------------------------- |
| [ARCHITECTURE_GRPC.md](./ARCHITECTURE_GRPC.md)                                                         | Architecture complète avec diagrammes              |
| [SETUP_INSTRUCTIONS.md](./SETUP_INSTRUCTIONS.md)                                                       | Guide détaillé de configuration et troubleshooting |
| [app/Services/IamGrpcService.php](./app/Services/IamGrpcService.php)                                   | Service de vérification de tokens                  |
| [app/Http/Middleware/VerifyIamToken.php](./app/Http/Middleware/VerifyIamToken.php)                     | Middleware utilisant le service                    |
| [iam/app/Http/Controllers/AuthTokenController.php](./iam/app/Http/Controllers/AuthTokenController.php) | Contrôleur qui expose le gRPC en HTTP              |

## 🎓 Prochaines Étapes (Optionnel)

- [ ] Ajouter un caching des tokens vérifiés (Redis)
- [ ] Implémenter le rate limiting sur l'endpoint
- [ ] Ajouter des logs détaillés pour le monitoring
- [ ] Configurer les certificats SSL/TLS
- [ ] Dockeriser toute la stack
- [ ] Ajouter un système de fallback gRPC

## ✨ Résumé des Bénéfices

✅ **Pas de couplage direct** entre les projets
✅ **Point d'entrée unique** via l'API Gateway
✅ **Facilement scalable** - peut ajouter plus de services
✅ **Architecture moderne** et mainttenable
✅ **Permet le load balancing** et failover
✅ **Centralize monitoring** et logging
✅ **Communication standardisée** (HTTP/REST)

---

🎉 **Vous êtes prêt!** Lancez les services et testez la communication.

Pour plus de détails, consultez [SETUP_INSTRUCTIONS.md](./SETUP_INSTRUCTIONS.md) et [ARCHITECTURE_GRPC.md](./ARCHITECTURE_GRPC.md).
