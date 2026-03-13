📋 CHECKLIST DE MISE EN PLACE - gRPC via API Gateway
═══════════════════════════════════════════════════════════════════════════════

🚀 DÉMARRAGE (À faire en premier)
─────────────────────────────────────────────────────────────────────────────

[ ] Lire le guide rapide
👉 ./GRPC_INTEGRATION_README.md

[ ] Vérifier la configuration
👉 ./verify-config.sh

[ ] Démarrer tous les services
👉 ./start-services.sh

🧪 TESTS BASIQUES (Valider que tout fonctionne)
─────────────────────────────────────────────────────────────────────────────

[ ] Tester la santé de l'API Gateway
curl http://127.0.0.1:3000/health

[ ] Créer un utilisateur de test dans le IAM
cd iam && php artisan tinker >>> $user = User::first(); >>> $token = $user->createToken('test')->accessToken;

[ ] Vérifier le token via API Gateway
curl -X POST http://127.0.0.1:3000/iam/api/auth/verify-token \
 -H "Authorization: Bearer TOKEN" \
 -H "Content-Type: application/json"

[ ] Tester le middleware du Backend
curl -X GET http://127.0.0.1:5601/api/animals \
 -H "Authorization: Bearer TOKEN"

📖 DOCUMENTATION (À consulter selon besoins)
─────────────────────────────────────────────────────────────────────────────

[ ] Quick Start
👉 ./GRPC_INTEGRATION_README.md (5 min)

[ ] Architecture Détaillée
👉 ./ARCHITECTURE_GRPC.md (15 min)

[ ] Instructions Complètes
👉 ./SETUP_INSTRUCTIONS.md (20 min)

[ ] Rapport Technique
👉 ./IMPLEMENTATION_REPORT.md (30 min)

[ ] Index des Guides
👉 ./INDEX_GUIDES.md

🔍 VÉRIFICATION DES SERVICES
─────────────────────────────────────────────────────────────────────────────

[ ] Backend (http://127.0.0.1:5601)
curl http://127.0.0.1:5601

[ ] IAM HTTP (http://127.0.0.1:5602)
curl http://127.0.0.1:5602

[ ] IAM gRPC (127.0.0.1:9090)
Vérifier avec: lsof -i :9090

[ ] API Gateway (http://127.0.0.1:3000)
curl http://127.0.0.1:3000/health

[ ] MySQL (localhost:3306)
Vérifier que les BDs existent: - projetdjallonke - djallonke_iam

🧩 CONFIGURATION (Vérifier les fichiers modifiés)
─────────────────────────────────────────────────────────────────────────────

[ ] app/Services/IamGrpcService.php
✓ Utilise API_GATEWAY_URL
✓ Endpoint: /iam/api/auth/verify-token

[ ] iam/app/Http/Controllers/AuthTokenController.php
✓ Vérifie les tokens avec Passport
✓ Retourne JSON

[ ] iam/routes/api.php
✓ Route /api/auth/verify-token existe

[ ] api-gateway/server_list_local.json
✓ /iam route vers 5602

[ ] iam/.env
✓ SERVE_PORT=5602

[ ] bootstrap/app.php
✓ Middleware 'auth.iam' enregistré

⚙️ CONFIGURATION AVANCÉE (Optionnel)
─────────────────────────────────────────────────────────────────────────────

[ ] Ajouter du caching Redis - Installer Redis - Configurer CACHE_STORE=redis - Cacher les tokens vérifiés

[ ] Implémenter le rate limiting - Ajouter middleware rate limit - Configurer throttle sur /verify-token

[ ] Ajouter les logs détaillés - Configurer LOG_CHANNEL - Ajouter logs dans IamGrpcService

[ ] Configurer SSH/TLS - Générer certificats SSL - Configurer HTTPS sur les services

🔐 SÉCURITÉ (À vérifier)
─────────────────────────────────────────────────────────────────────────────

[ ] CORS configuré correctement
API Gateway: ✓
IAM: À vérifier

[ ] Authentication headers vérifiés
Bearer tokens utilisés: ✓

[ ] Tokens n'expirés jamais loggés
À implémenter

[ ] Rate limiting sur verify-token
À ajouter

[ ] Logs d'audit des authentifications
À implémenter

📊 MONITORING (À configurer)
─────────────────────────────────────────────────────────────────────────────

[ ] Health checks - /health sur API Gateway - /up sur Backend - /up sur IAM

[ ] Logs centralisés - Backend logs - IAM logs - Gateway logs

[ ] Métriques de performance - Temps de réponse - Nombre de requêtes - Taux d'erreur

🐛 TROUBLESHOOTING (Si problèmes)
─────────────────────────────────────────────────────────────────────────────

[ ] Token invalide?
→ Vérifier que l'utilisateur existe dans la DB IAM
→ Vérifier que le token n'est pas expiré
→ Consulter: ./SETUP_INSTRUCTIONS.md

[ ] Connection refused?
→ Vérifier que le service écoute sur le bon port
→ curl http://127.0.0.1:[PORT]

[ ] API Gateway pas réactive?
→ Vérifier: npm run dev dans le répertoire api-gateway
→ Vérifier la configuration: server_list_local.json

[ ] Middleware pas invoqué?
→ Vérifier que la route utilise le middleware @auth.iam
→ Consulter: routes/api.php

📝 PROCHAINES ÉTAPES (Dans 1-2 semaines)
─────────────────────────────────────────────────────────────────────────────

[ ] Phase 2: Optimisation - [ ] Redis caching - [ ] Rate limiting - [ ] Monitoring complet - [ ] Logs détaillés

[ ] Phase 3: Production (Dans 1-2 mois) - [ ] SSL/TLS certificates - [ ] Load balancing - [ ] Docker containerization - [ ] CI/CD pipeline

✅ FINAL CHECKLIST (Avant utilisation en production)
─────────────────────────────────────────────────────────────────────────────

[ ] Tous les tests passent
php artisan test

[ ] Configuration validée
./verify-config.sh ✅

[ ] Version de code formatée
vendor/bin/pint

[ ] Documentation à jour
Tous les guides lus et compris

[ ] Équipe formée
Tous comprennent l'architecture

[ ] Backups des BD
Avant première utilisation

[ ] Monitoring configuré
Logs, métriques, alertes

═══════════════════════════════════════════════════════════════════════════════

STATUT: ✅ PRÊT À L'EMPLOI

Une fois cette checklist complétée, le système est prêt pour la production!

Pour commencer: ./GRPC_INTEGRATION_README.md

═══════════════════════════════════════════════════════════════════════════════
