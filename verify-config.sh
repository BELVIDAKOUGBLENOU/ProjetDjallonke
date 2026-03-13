#!/bin/bash

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}     Configuration Verification - gRPC via API Gateway${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

PROJECT_ROOT="/Users/ezechiel/Projects/ProjetDjallonke"
IAM_PATH="$PROJECT_ROOT/iam"
GATEWAY_PATH="$PROJECT_ROOT/api-gateway"
BACKEND_PATH="$PROJECT_ROOT"

# Track results
CHECKS_PASSED=0
CHECKS_FAILED=0

# Function to check file exists
check_file_exists() {
    local file=$1
    local description=$2

    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $description"
        echo "  📁 $file"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} $description"
        echo "  📁 $file (NOT FOUND)"
        ((CHECKS_FAILED++))
    fi
}

# Function to check file contains text
check_file_contains() {
    local file=$1
    local search_text=$2
    local description=$3

    if grep -q "$search_text" "$file" 2>/dev/null; then
        echo -e "${GREEN}✓${NC} $description"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} $description"
        echo "  ⚠️  Could not find: \"$search_text\" in $file"
        ((CHECKS_FAILED++))
    fi
}

# Function to check port availability
check_port() {
    local port=$1
    local service=$2

    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} $service is running on port $port"
        ((CHECKS_PASSED++))
    else
        echo -e "${YELLOW}⚠${NC} $service NOT running on port $port (expected if services not started)"
        # Don't count as failed for pre-startup check
    fi
}

echo -e "${YELLOW}1. Checking File Structures${NC}\n"

# Backend files
check_file_exists "$BACKEND_PATH/app/Services/IamGrpcService.php" "Backend: IamGrpcService created/updated"
check_file_exists "$BACKEND_PATH/app/Http/Middleware/VerifyIamToken.php" "Backend: VerifyIamToken middleware exists"
check_file_exists "$BACKEND_PATH/.env" "Backend: .env file exists"

echo ""

# IAM files
check_file_exists "$IAM_PATH/app/Http/Controllers/AuthTokenController.php" "IAM: AuthTokenController created"
check_file_exists "$IAM_PATH/routes/api.php" "IAM: routes/api.php updated"
check_file_exists "$IAM_PATH/.env" "IAM: .env file exists"
check_file_exists "$IAM_PATH/.rr.yaml" "IAM: .rr.yaml exists"

echo ""

# API Gateway files
check_file_exists "$GATEWAY_PATH/server_list_local.json" "API Gateway: server_list_local.json exists"
check_file_exists "$GATEWAY_PATH/server.js" "API Gateway: server.js exists"

echo ""

# Documentation files
check_file_exists "$BACKEND_PATH/ARCHITECTURE_GRPC.md" "Documentation: ARCHITECTURE_GRPC.md created"
check_file_exists "$BACKEND_PATH/SETUP_INSTRUCTIONS.md" "Documentation: SETUP_INSTRUCTIONS.md created"
check_file_exists "$BACKEND_PATH/GRPC_INTEGRATION_README.md" "Documentation: GRPC_INTEGRATION_README.md created"
check_file_exists "$BACKEND_PATH/start-services.sh" "Documentation: start-services.sh created"

echo ""
echo -e "${YELLOW}2. Checking File Configuration${NC}\n"

# Check IamGrpcService config
check_file_contains "$BACKEND_PATH/app/Services/IamGrpcService.php" \
    "API_GATEWAY_URL" \
    "IamGrpcService: Uses API_GATEWAY_URL from environment"

check_file_contains "$BACKEND_PATH/app/Services/IamGrpcService.php" \
    "/iam/api/auth/verify-token" \
    "IamGrpcService: Uses correct endpoint path"

echo ""

# Check IAM routes
check_file_contains "$IAM_PATH/routes/api.php" \
    "verify-token" \
    "IAM Routes: /api/auth/verify-token endpoint configured"

check_file_contains "$IAM_PATH/routes/api.php" \
    "AuthTokenController" \
    "IAM Routes: AuthTokenController referenced"

echo ""

# Check API Gateway config
check_file_contains "$GATEWAY_PATH/server_list_local.json" \
    '"/iam"' \
    "API Gateway: /iam route configured"

check_file_contains "$GATEWAY_PATH/server_list_local.json" \
    "127.0.0.1:5602" \
    "API Gateway: Routes to IAM on port 5602"

echo ""

# Check IAM env
check_file_contains "$IAM_PATH/.env" \
    "SERVE_PORT=5602" \
    "IAM Environment: SERVE_PORT set to 5602"

echo ""

# Check Backend env
check_file_contains "$BACKEND_PATH/.env" \
    "API_GATEWAY_URL" \
    "Backend Environment: API_GATEWAY_URL configured"

echo ""
echo -e "${YELLOW}3. Checking Running Services${NC}\n"

echo -e "Checking for active services (these should be ${YELLOW}WARNING${NC} if not started yet):\n"
check_port 5601 "Backend (Laravel)"
check_port 5602 "IAM (Laravel)"
check_port 9090 "IAM gRPC (RoadRunner)"
check_port 3000 "API Gateway (Node.js)"
check_port 3306 "MySQL Database"

echo ""
echo -e "${YELLOW}4. Summary${NC}\n"

TOTAL=$((CHECKS_PASSED + CHECKS_FAILED))

echo -e "Configuration Checks: ${GREEN}$CHECKS_PASSED passed${NC}"

if [ $CHECKS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ All configuration checks passed!${NC}\n"
    echo -e "${GREEN}═══════════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}Configuration is READY. You can now start the services.${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════════════════════${NC}\n"

    echo -e "${YELLOW}Quick Start:${NC}\n"
    echo "1. Start all services:"
    echo -e "   ${BLUE}cd $BACKEND_PATH${NC}"
    echo -e "   ${BLUE}./start-services.sh${NC}\n"

    echo "2. Create a test token in IAM:"
    echo -e "   ${BLUE}cd $IAM_PATH && php artisan tinker${NC}"
    echo -e "   ${BLUE}>>> \$user = User::first(); \$user->createToken('test')->accessToken${NC}\n"

    echo "3. Test verification:"
    echo -e "   ${BLUE}curl -X POST http://127.0.0.1:3000/iam/api/auth/verify-token \\${NC}"
    echo -e "   ${BLUE}  -H \"Authorization: Bearer TOKEN\"${NC}\n"

    echo "4. Test backend middleware:"
    echo -e "   ${BLUE}curl -X GET http://127.0.0.1:5601/api/animals \\${NC}"
    echo -e "   ${BLUE}  -H \"Authorization: Bearer TOKEN\"${NC}\n"

    exit 0
else
    echo -e "${RED}✗ $CHECKS_FAILED configuration checks failed${NC}\n"
    echo -e "${RED}═══════════════════════════════════════════════════════════════${NC}"
    echo -e "${RED}Please review the errors above and refer to:${NC}"
    echo -e "${RED}  - SETUP_INSTRUCTIONS.md${NC}"
    echo -e "${RED}  - ARCHITECTURE_GRPC.md${NC}"
    echo -e "${RED}═══════════════════════════════════════════════════════════════${NC}\n"

    exit 1
fi
