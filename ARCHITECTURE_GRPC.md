# Architecture de Communication gRPC via API Gateway

## 📋 Vue d'ensemble

Ce projet utilise une architecture microservices où deux applications Laravel communiquent via gRPC, mais **toutes les communications passent obligatoirement par l'API Gateway**.

### 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│  Projet Principal (Backend Djallonke)                           │
│  - Port HTTP: 5601                                              │
│  - URL: http://127.0.0.1:5601                                   │
│  - Middleware: VerifyIamToken @auth.iam                         │
└──────────────────────────────┬──────────────────────────────────┘
                               │
                    request(bearer_token)
                               │
                               ▼
                   ┌─────────────────────────┐
                   │   API Gateway (Node.js) │
                   │   Port: 3000            │
                   │   http://127.0.0.1:3000 │
                   └──────┬──────────────────┘
                          │
                ┌─────────┴─────────┐
                │                   │
                ▼                   ▼
        ┌─────────────────┐  ┌──────────────────┐
        │ Backend         │  │ IAM (gRPC)       │
        │ Port: 5601      │  │ HTTP: 5602       │
        │                 │  │ gRPC: 9090       │
        └─────────────────┘  └──────────────────┘
                                    │
                    expose /iam/api/auth/verify-token
                                    │
                         ┌──────────┴──────────┐
                         │                     │
                         ▼                     ▼
                   ┌──────────────┐   ┌─────────────────┐
                   │ HTTP Handler │   │ gRPC Service    │
                   │ (REST)       │   │ (RoadRunner)    │
                   │ Port: 5602   │   │ Port: 9090      │
                   └──────────────┘   └─────────────────┘
```

## 🔄 Flux de Communication

### 1. Request de Token Verification

```
1. Client envoie: GET /api/animals
   Authorization: Bearer <token>

2. VerifyIamToken Middleware intercepte
   - Extrait le token du header Authorization
   - Appelle IamGrpcService::verifyToken($token)

3. IamGrpcService (utilise l'API Gateway)
   - URL: http://127.0.0.1:3000/iam/api/auth/verify-token
   - Method: POST
   - Header: Authorization: Bearer <token>

4. API Gateway proxy la requête
   - Route /iam/* vers http://127.0.0.1:5602
   - Donc: http://127.0.0.1:5602/api/auth/verify-token

5. IAM Application (Laravel)
   - AuthTokenController::verifyToken() traite la requête
   - Utilise Passport pour vérifier le token
   - Retourne les données utilisateur en JSON

6. Response retourne au middleware
   - User créé/updated dans User::firstOrCreate()
   - Requête continue vers le contrôleur
```

## 🚀 Démarrage des Services

### Prérequis

- PHP 8.2+
- Node.js 18+
- MySQL 8.0+
- RoadRunner CLI (pour gRPC)

### 1. Base de Données

```bash
# IAM Database
mysql -u root -p -e "CREATE DATABASE djallonke_iam;"

# Backend Database
mysql -u root -p -e "CREATE DATABASE projetdjallonke;"
```

### 2. IAM Project (Port 5602)

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke/iam

# Installation des dépendances
composer install

# Migration et seeders
php artisan migrate --seed

# Lancer le serveur PHP (HTTP)
php artisan serve --port=5602

# Dans un autre terminal: Démarrer le gRPC server (RoadRunner)
rr serve
```

### 3. Backend Principal (Port 5601)

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke

# Installation des dépendances
composer install

# Migration
php artisan migrate

# Lancer le serveur
php artisan serve --port=5601
```

### 4. API Gateway (Port 3000)

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke/api-gateway

# Installation des dépendances
npm install

# Lancer en mode watch
npm run dev

# Ou en mode production
npm start
```

## 📡 Configuration

### IAM (.env)

```env
SERVE_PORT=5602
API_GATEWAY_URL=http://127.0.0.1:3000
```

### Backend Principal (.env)

```env
SERVE_PORT=5601
API_GATEWAY_URL=http://127.0.0.1:3000
```

### API Gateway Configuration

**File**: `api-gateway/server_list_local.json`

```json
{
    "redirections": [
        {
            "origin": "/backend-djallonke",
            "destination": "http://127.0.0.1:5601",
            "rewrite": ""
        },
        {
            "origin": "/iam",
            "destination": "http://127.0.0.1:5602",
            "rewrite": ""
        }
    ]
}
```

## 🧪 Test de Vérification

### 1. Créer un utilisateur dans l'IAM

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke/iam

php artisan tinker

# Dans tinker
>>> $user = User::factory()->create([
...     'email' => 'test@example.com',
...     'password' => Hash::make('password123')
... ]);
>>> $token = $user->createToken('test-token')->accessToken;
>>> echo $token;
```

### 2. Tester la verification du token via API Gateway

```bash
curl -X POST http://127.0.0.1:3000/iam/api/auth/verify-token \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"

# Response expected:
# {
#   "id": 1,
#   "name": "Test User",
#   "email": "test@example.com",
#   "uid": "uuid-string",
#   "email_verified_at": "2026-03-10T10:30:00Z",
#   "created_at": "2026-03-10T10:30:00Z",
#   "updated_at": "2026-03-10T10:30:00Z",
#   "fcm_token": null
# }
```

### 3. Tester via le middleware du projet principal

```bash
# Vérifier qu'un endpoint protégé par @auth.iam fonctionne
curl -X GET http://127.0.0.1:5601/api/animals \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Si le token est valide, la requête passe
# Sinon: {"error": "Unauthenticated"} - 401
```

### 4. Test avec Tinker (Backend)

```bash
cd /Users/ezechiel/Projects/ProjetDjallonke

php artisan tinker

>>> $service = app(\App\Services\IamGrpcService::class);
>>> $user = $service->verifyToken('YOUR_TOKEN_HERE');
>>> dd($user);

# Devrait retourner les données utilisateur du IAM
```

## 🔧 Troubleshooting

### Erreur: "Unauthenticated" sur tous les tokens

**Vérifier**:

1. Le token est correct dans l'IAM
2. La base de données IAM contient l'utilisateur
3. Le token n'est pas expiré

```bash
# Vérifier dans l'IAM
php artisan tinker
>>> use Laravel\Passport\Token;
>>> Token::find('token_id')->accessToken;
```

### Erreur: "API Gateway connection refused"

**Vérifier**:

1. L'API Gateway est active: `curl http://127.0.0.1:3000/health`
2. La configuration `API_GATEWAY_URL` est correcte
3. Les redirections sont bien configurées dans `server_list_local.json`

### Erreur: "IAM service unavailable"

**Vérifier**:

1. Le serveur IAM est actif: `curl http://127.0.0.1:5602`
2. Le port 5602 n'est pas utilisé par un autre service
3. La base de données IAM est accessible

## 📚 Fichiers Modifiés

1. **iam/app/Http/Controllers/AuthTokenController.php** (NOUVEAU)
    - Expose le endpoint HTTP `/api/auth/verify-token`
    - Traite les requêtes du middleware

2. **iam/routes/api.php**
    - Ajoute la route `/api/auth/verify-token`

3. **iam/.env**
    - Ajoute `SERVE_PORT=5602`

4. **app/Services/IamGrpcService.php**
    - Utilise l'API Gateway au lieu de gRPC direct
    - URL: `http://127.0.0.1:3000/iam/api/auth/verify-token`

5. **api-gateway/server_list_local.json**
    - Redirige `/iam` vers `http://127.0.0.1:5602`
    - Remplace l'ancien `/iam-grpc` vers le port RPC

## 🎯 Prochaines Étapes Optionnelles

- [ ] Ajouter la vérification du token côté gRPC (garder les deux endpoints)
- [ ] Implémenter le caching des tokens pour améliorer les perfs
- [ ] Ajouter le rate limiting sur l'endpoint `/api/auth/verify-token`
- [ ] Ajouter des logs détaillés pour le debugging
- [ ] Tester la failover en cas d'indisponibilité du IAM
