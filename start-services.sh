#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

PROJECT_ROOT="/Users/ezechiel/Projects/ProjetDjallonke"
IAM_PATH="$PROJECT_ROOT/iam"
GATEWAY_PATH="$PROJECT_ROOT/api-gateway"
BACKEND_PATH="$PROJECT_ROOT"

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}  Djallonke Services Startup${NC}"
echo -e "${BLUE}================================${NC}\n"

# Function to check if port is in use
check_port() {
    local port=$1
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null; then
        return 0  # Port is in use
    else
        return 1  # Port is free
    fi
}

# Check ports before starting
echo -e "${YELLOW}Vérification des ports...${NC}"
check_port 5601 && echo -e "${RED}⚠️  Port 5601 (Backend) déjà utilisé${NC}" || echo -e "${GREEN}✓ Port 5601 libre${NC}"
check_port 5602 && echo -e "${RED}⚠️  Port 5602 (IAM) déjà utilisé${NC}" || echo -e "${GREEN}✓ Port 5602 libre${NC}"
check_port 3000 && echo -e "${RED}⚠️  Port 3000 (API Gateway) déjà utilisé${NC}" || echo -e "${GREEN}✓ Port 3000 libre${NC}"
check_port 9090 && echo -e "${RED}⚠️  Port 9090 (gRPC) déjà utilisé${NC}" || echo -e "${GREEN}✓ Port 9090 libre${NC}"

echo -e "\n${YELLOW}Démarrage des services...${NC}\n"

# Open new terminal windows (macOS)
# Terminal 1: Backend
echo -e "${BLUE}[1/4]${NC} Démarrage du Backend (Port 5601)..."
osascript <<EOF
tell app "Terminal"
    do script "cd '$BACKEND_PATH' && echo '🚀 Backend Djallonke - Port 5601' && php artisan serve --port=5601"
end tell
EOF
sleep 2

# Terminal 2: IAM HTTP
echo -e "${BLUE}[2/4]${NC} Démarrage du IAM (Port 5602)..."
osascript <<EOF
tell app "Terminal"
    do script "cd '$IAM_PATH' && echo '🚀 IAM Service - Port 5602' && php artisan serve --port=5602"
end tell
EOF
sleep 2

# Terminal 3: IAM gRPC (RoadRunner)
echo -e "${BLUE}[3/4]${NC} Démarrage du gRPC Server (Port 9090)..."
osascript <<EOF
tell app "Terminal"
    do script "cd '$IAM_PATH' && echo '🚀 gRPC Server - Port 9090' && rr serve"
end tell
EOF
sleep 2

# Terminal 4: API Gateway
echo -e "${BLUE}[4/4]${NC} Démarrage de l'API Gateway (Port 3000)..."
osascript <<EOF
tell app "Terminal"
    do script "cd '$GATEWAY_PATH' && echo '🚀 API Gateway - Port 3000' && npm run dev"
end tell
EOF

sleep 3

echo -e "\n${GREEN}================================${NC}"
echo -e "${GREEN}  ✓ Tous les services démarrés${NC}"
echo -e "${GREEN}================================${NC}\n"

echo -e "${YELLOW}Services disponibles:${NC}"
echo -e "  • Backend:       ${BLUE}http://127.0.0.1:5601${NC}"
echo -e "  • IAM HTTP:      ${BLUE}http://127.0.0.1:5602${NC}"
echo -e "  • IAM gRPC:      ${BLUE}127.0.0.1:9090${NC}"
echo -e "  • API Gateway:   ${BLUE}http://127.0.0.1:3000${NC}"

echo -e "\n${YELLOW}Tests rapides:${NC}"
echo -e "  • Health check gateway: ${BLUE}curl http://127.0.0.1:3000/health${NC}"
echo -e "  • Verify token:         ${BLUE}curl -X POST http://127.0.0.1:3000/iam/api/auth/verify-token -H 'Authorization: Bearer TOKEN'${NC}"

echo -e "\n${YELLOW}Pour arrêter tous les services, fermez les terminaux.${NC}\n"

# Keep this script running
wait
