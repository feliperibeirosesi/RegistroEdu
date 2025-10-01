#!/bin/bash

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
BOLD='\033[1m'
DIM='\033[2m'
UNDERLINE='\033[4m'
NC='\033[0m'

PROJECT_REPO="https://github.com/joaopaulopereirarezendesesi/RegistroEdu"
PROJECT_NAME="RegistroEdu"
DB_NAME="registroedu"
REQUIRED_PHP_VERSION="8.2"
REQUIRED_NODE_VERSION="18"

print_banner() {
    clear
    echo -e "${CYAN}${BOLD}"
    cat << "EOF"
    ╔══════════════════════════════════════════════════════════════════════╗
    ║                                                                      ║
    ║    ██████╗ ███████╗ ██████╗ ██╗███████╗████████╗██████╗  ██████╗     ║
    ║    ██╔══██╗██╔════╝██╔════╝ ██║██╔════╝╚══██╔══╝██╔══██╗██╔═══██╗    ║
    ║    ██████╔╝█████╗  ██║  ███╗██║███████╗   ██║   ██████╔╝██║   ██║    ║
    ║    ██╔══██╗██╔══╝  ██║   ██║██║╚════██║   ██║   ██╔══██╗██║   ██║    ║
    ║    ██║  ██║███████╗╚██████╔╝██║███████║   ██║   ██║  ██║╚██████╔╝    ║
    ║    ╚═╝  ╚═╝╚══════╝ ╚═════╝ ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝ ╚═════╝     ║
    ║                                                                      ║
    ║                    ███████╗██████╗ ██╗   ██╗                         ║
    ║                    ██╔════╝██╔══██╗██║   ██║                         ║
    ║                    █████╗  ██║  ██║██║   ██║                         ║
    ║                    ██╔══╝  ██║  ██║██║   ██║                         ║
    ║                    ███████╗██████╔╝╚██████╔╝                         ║
    ║                    ╚══════╝╚═════╝  ╚═════╝                          ║
    ║                                                                      ║
    ║                 Sistema de Administração Escolar                     ║
    ║                      Instalador Automático v3.0                      ║
    ║                                                                      ║
    ╚══════════════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
    echo
    echo -e "${YELLOW}${BOLD}🚀 Transformando a gestão educacional, uma escola por vez${NC}"
    echo -e "${DIM}   Instalação inteligente com detecção automática do sistema${NC}"
    echo
    sleep 2
}

print_status() {
    echo -e "${BLUE}${BOLD}[ℹ️  INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}${BOLD}[✅ SUCESSO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}${BOLD}[⚠️  ATENÇÃO]${NC} $1"
}

print_error() {
    echo -e "${RED}${BOLD}[❌ ERRO]${NC} $1"
}

print_step() {
    echo
    echo -e "${PURPLE}${BOLD}[🔧 EXECUTANDO]${NC} $1"
}

show_progress() {
    local pid=$1
    local message="$2"
    local delay=0.15
    local frames=('⠋' '⠙' '⠹' '⠸' '⠼' '⠴' '⠦' '⠧' '⠇' '⠏')
    local i=0

    sleep 0.5

    while kill -0 $pid 2>/dev/null; do
        printf "\r${CYAN}${BOLD}[${frames[$i]}]${NC} ${message:-Processando...}"
        i=$(( (i+1) % ${#frames[@]} ))
        sleep $delay
    done

    printf "\r${GREEN}${BOLD}[✓]${NC} ${message:-Concluído}                    \n"
}

print_separator() {
    echo -e "${DIM}─────────────────────────────────────────────────────────────────${NC}"
}

detect_os() {
    print_step "🔍 Detectando ambiente do sistema"
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        if [ -f /etc/debian_version ]; then
            OS="debian"
            PKG_MANAGER="apt"
            DISTRO=$(lsb_release -si 2>/dev/null || echo "Debian/Ubuntu")
        elif [ -f /etc/redhat-release ]; then
            OS="redhat"
            PKG_MANAGER="yum"
            DISTRO=$(cat /etc/redhat-release | cut -d' ' -f1)
        else
            OS="linux"
            PKG_MANAGER="apt"
            DISTRO="Linux"
        fi
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        OS="macos"
        PKG_MANAGER="brew"
        DISTRO="macOS"
    else
        print_error "Sistema operacional não suportado: $OSTYPE"
        echo -e "${YELLOW}💡 Sistemas compatíveis: Ubuntu, Debian, CentOS, RHEL, macOS${NC}"
        exit 1
    fi
    print_success "Sistema detectado: ${BOLD}$DISTRO${NC} (${DIM}$OS${NC})"
}

check_user() {
    if [ "$EUID" -eq 0 ]; then
        print_error "Não execute este script como root (sudo)"
        print_warning "O script solicitará privilégios administrativos quando necessário"
        exit 1
    fi
}

install_base_dependencies() {
    print_step "📦 Preparando dependências do sistema"
    case $PKG_MANAGER in
        apt)
            (sudo apt update -qq > /dev/null 2>&1 && sudo apt install -y curl lsb-release gnupg apt-transport-https ca-certificates > /dev/null 2>&1) &
            show_progress $! "Atualizando repositórios e instalando ferramentas essenciais"
            ;;
        yum)
            (sudo yum update -y -q > /dev/null 2>&1 && sudo yum install -y curl > /dev/null 2>&1) &
            show_progress $! "Atualizando sistema e instalando curl"
            ;;
        brew)
            (brew install curl > /dev/null 2>&1) &
            show_progress $! "Verificando ferramentas do Homebrew"
            ;;
    esac
    print_success "Dependências básicas preparadas"
}

update_system() {
    print_step "🔄 Sincronizando repositórios do sistema"
    case $PKG_MANAGER in
        apt)
            (sudo apt update -qq && sudo apt upgrade -y -qq > /dev/null 2>&1) &
            show_progress $! "Atualizando pacotes do sistema"
            ;;
        yum)
            (sudo yum update -y -q > /dev/null 2>&1) &
            show_progress $! "Atualizando pacotes do sistema"
            ;;
        brew)
            (brew update && brew upgrade > /dev/null 2>&1) &
            show_progress $! "Atualizando fórmulas do Homebrew"
            ;;
    esac
    print_success "Sistema atualizado e sincronizado"
}

install_git() {
    print_step "🔧 Configurando Git"
    if command -v git &> /dev/null; then
        GIT_VERSION=$(git --version | cut -d' ' -f3)
        print_success "Git ${BOLD}v$GIT_VERSION${NC} já configurado"
    else
        case $PKG_MANAGER in
            apt) (sudo apt install -y git > /dev/null 2>&1) ;;
            yum) (sudo yum install -y git > /dev/null 2>&1) ;;
            brew) (brew install git > /dev/null 2>&1) ;;
        esac &
        show_progress $! "Instalando sistema de controle de versão Git"
        print_success "Git instalado e configurado"
    fi
}

install_php() {
    print_step "🐘 Configurando PHP"
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1-2)
        if [[ $(echo "$PHP_VERSION >= $REQUIRED_PHP_VERSION" | bc -l 2>/dev/null || echo 0) -eq 1 ]]; then
            print_success "PHP ${BOLD}v$PHP_VERSION${NC} compatível com RegistroEdu"
        else
            print_warning "PHP v$PHP_VERSION encontrado, atualizando para v$REQUIRED_PHP_VERSION"
            install_php_new_version
        fi
    else
        install_php_new_version
    fi
}

install_php_new_version() {
    case $PKG_MANAGER in
        apt)
            (sudo add-apt-repository ppa:ondrej/php -y > /dev/null 2>&1 && sudo apt update -qq > /dev/null 2>&1) &
            show_progress $! "Adicionando repositório oficial do PHP"
            (sudo apt install -y php${REQUIRED_PHP_VERSION} php${REQUIRED_PHP_VERSION}-cli php${REQUIRED_PHP_VERSION}-fpm > /dev/null 2>&1) &
            show_progress $! "Instalando PHP ${REQUIRED_PHP_VERSION}"
            ;;
        yum)
            (sudo yum install -y epel-release > /dev/null 2>&1 && sudo yum install -y php82 php82-cli php82-fpm > /dev/null 2>&1) &
            show_progress $! "Instalando PHP ${REQUIRED_PHP_VERSION}"
            ;;
        brew)
            (brew tap shivammathur/php > /dev/null 2>&1 && brew install shivammathur/php/php@${REQUIRED_PHP_VERSION} > /dev/null 2>&1 && brew link --overwrite --force php@${REQUIRED_PHP_VERSION} > /dev/null 2>&1) &
            show_progress $! "Configurando PHP ${REQUIRED_PHP_VERSION} via Homebrew"
            ;;
    esac
    print_success "PHP ${BOLD}v${REQUIRED_PHP_VERSION}${NC} instalado e ativo"
}

install_php_extensions() {
    print_step "🔌 Configurando extensões PHP para RegistroEdu"
    REQUIRED_EXTENSIONS=( "mbstring" "xml" "bcmath" "curl" "fileinfo" "json" "openssl" "pdo" "pdo_pgsql" "tokenizer" "zip" "gd" "intl" "redis" )
    MISSING_EXTENSIONS=()

    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if ! php -m | grep -q "^$ext$"; then
            MISSING_EXTENSIONS+=($ext)
        fi
    done

    if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
        print_warning "Instalando extensões: ${BOLD}${MISSING_EXTENSIONS[*]}${NC}"
        case $PKG_MANAGER in
            apt)
                PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1-2)
                for ext in "${MISSING_EXTENSIONS[@]}"; do
                    (sudo apt install -y php${PHP_VERSION}-${ext} > /dev/null 2>&1) &
                    show_progress $! "Instalando extensão php-$ext"
                done
                ;;
            yum)
                for ext in "${MISSING_EXTENSIONS[@]}"; do
                    (sudo yum install -y php-${ext} > /dev/null 2>&1) &
                    show_progress $! "Instalando extensão php-$ext"
                done
                ;;
            brew)
                (brew install php-redis > /dev/null 2>&1) &
                show_progress $! "Configurando extensões PHP no macOS"
                ;;
        esac
        print_success "Todas as extensões PHP foram instaladas"
    else
        print_success "Todas as extensões PHP necessárias já estão ${BOLD}ativas${NC}"
    fi
}

install_postgresql() {
    print_step "🐘 Configurando PostgreSQL"
    if command -v psql &> /dev/null; then
        PG_VERSION=$(psql --version | cut -d' ' -f3 | cut -d'.' -f1)
        print_success "PostgreSQL ${BOLD}v$PG_VERSION${NC} já configurado"
    else
        case $PKG_MANAGER in
            apt)
                if [ ! -f /etc/apt/sources.list.d/pgdg.list ]; then
                    (sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list' && wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add - > /dev/null 2>&1 && sudo apt-get update -qq > /dev/null 2>&1) &
                    show_progress $! "Configurando repositório oficial do PostgreSQL"
                fi
                (sudo apt install -y postgresql-15 postgresql-contrib-15 postgresql-client-15 > /dev/null 2>&1 && sudo systemctl start postgresql && sudo systemctl enable postgresql) &
                show_progress $! "Instalando e configurando PostgreSQL 15"
                ;;
            yum)
                (sudo yum install -y postgresql15-server postgresql15-contrib > /dev/null 2>&1 && sudo /usr/pgsql-15/bin/postgresql-15-setup initdb && sudo systemctl start postgresql-15 && sudo systemctl enable postgresql-15) &
                show_progress $! "Instalando e configurando PostgreSQL 15"
                ;;
            brew)
                (brew install postgresql@15 > /dev/null 2>&1 && brew services start postgresql@15) &
                show_progress $! "Instalando PostgreSQL 15 via Homebrew"
                ;;
        esac
        print_success "PostgreSQL instalado e ${BOLD}em execução${NC}"
    fi
}

install_redis() {
    print_step "⚡ Configurando Redis"
    if command -v redis-server &> /dev/null; then
        print_success "Redis já está ${BOLD}configurado${NC} e ativo"
    else
        case $PKG_MANAGER in
            apt)
                (sudo apt install -y redis-server > /dev/null 2>&1 && sudo systemctl start redis-server && sudo systemctl enable redis-server) &
                show_progress $! "Instalando e iniciando Redis Server"
                ;;
            yum)
                (sudo yum install -y redis > /dev/null 2>&1 && sudo systemctl start redis && sudo systemctl enable redis) &
                show_progress $! "Instalando e iniciando Redis"
                ;;
            brew)
                (brew install redis > /dev/null 2>&1 && brew services start redis) &
                show_progress $! "Instalando Redis via Homebrew"
                ;;
        esac
        print_success "Redis instalado e ${BOLD}em execução${NC}"
    fi
}

install_composer() {
    print_step "🎼 Configurando Composer"
    if command -v composer &> /dev/null; then
        COMPOSER_VERSION=$(composer --version | cut -d' ' -f3)
        print_success "Composer ${BOLD}v$COMPOSER_VERSION${NC} já configurado"
        (composer self-update --quiet > /dev/null 2>&1) &
        show_progress $! "Atualizando Composer para última versão"
    else
        (curl -sS https://getcomposer.org/installer | php > /dev/null 2>&1 && sudo mv composer.phar /usr/local/bin/composer && sudo chmod +x /usr/local/bin/composer) &
        show_progress $! "Baixando e instalando Composer globalmente"
        print_success "Composer instalado e ${BOLD}configurado${NC}"
    fi
}

install_nodejs() {
    print_step "🟢 Configurando Node.js"
    if command -v node &> /dev/null; then
        NODE_VERSION=$(node -v | sed 's/v//' | cut -d'.' -f1)
        if [[ $NODE_VERSION -ge $REQUIRED_NODE_VERSION ]]; then
            print_success "Node.js ${BOLD}$(node -v)${NC} compatível"
        else
            print_warning "Node.js v$(node -v) encontrado, atualizando para v$REQUIRED_NODE_VERSION"
            install_nodejs_new_version
        fi
    else
        install_nodejs_new_version
    fi
}

install_nodejs_new_version() {
    case $PKG_MANAGER in
        apt)
            (curl -fsSL https://deb.nodesource.com/setup_${REQUIRED_NODE_VERSION}.x | sudo -E bash - > /dev/null 2>&1 && sudo apt-get install -y nodejs > /dev/null 2>&1) &
            show_progress $! "Instalando Node.js ${REQUIRED_NODE_VERSION} LTS"
            ;;
        yum)
            (curl -fsSL https://rpm.nodesource.com/setup_${REQUIRED_NODE_VERSION}.x | sudo bash - > /dev/null 2>&1 && sudo yum install -y nodejs > /dev/null 2>&1) &
            show_progress $! "Instalando Node.js ${REQUIRED_NODE_VERSION} LTS"
            ;;
        brew)
            (brew install node@${REQUIRED_NODE_VERSION} > /dev/null 2>&1 && brew link node@${REQUIRED_NODE_VERSION} --force > /dev/null 2>&1) &
            show_progress $! "Instalando Node.js ${REQUIRED_NODE_VERSION} via Homebrew"
            ;;
    esac
    print_success "Node.js ${BOLD}v${REQUIRED_NODE_VERSION}${NC} instalado e configurado"
}

clone_project() {
    print_step "📥 Clonando repositório RegistroEdu"
    if [ -d "$PROJECT_NAME" ]; then
        echo -e "${YELLOW}${BOLD}⚠️  Diretório $PROJECT_NAME já existe${NC}"
        echo -e "${DIM}   Localização: $(pwd)/$PROJECT_NAME${NC}"
        echo
        read -p "$(echo -e ${CYAN}Deseja sobrescrever? ${BOLD}[y/N]:${NC} )" OVERWRITE
        if [[ $OVERWRITE =~ ^[Yy]$ ]]; then
            rm -rf "$PROJECT_NAME"
            (git clone "$PROJECT_REPO" "$PROJECT_NAME" > /dev/null 2>&1) &
            show_progress $! "Clonando projeto RegistroEdu"
        else
            print_status "Utilizando diretório existente"
        fi
    else
        (git clone "$PROJECT_REPO" "$PROJECT_NAME" > /dev/null 2>&1) &
        show_progress $! "Clonando repositório do GitHub"
    fi

    if [ -d "$PROJECT_NAME" ]; then
        cd "$PROJECT_NAME"
        print_success "Projeto clonado em ${BOLD}$(pwd)${NC}"
        cd application
        print_success "Acessando módulo principal: ${BOLD}application/${NC}"
    else
        print_error "Falha no clone do repositório. Verifique sua conexão com a internet."
        exit 1
    fi
}

setup_laravel_project() {
    print_step "⚙️  Configurando projeto Laravel"

    if [ ! -f "composer.json" ]; then
        print_error "Arquivo composer.json não encontrado! Projeto pode estar corrompido."
        exit 1
    fi

    (composer install --no-interaction --optimize-autoloader > /dev/null 2>&1) &
    show_progress $! "Instalando dependências PHP com Composer"

    (npm install > /dev/null 2>&1) &
    show_progress $! "Instalando dependências JavaScript com NPM"

    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        cp .env.example .env
        print_success "Arquivo de configuração ${BOLD}.env${NC} criado"
    fi

    (php artisan key:generate --force > /dev/null 2>&1) &
    show_progress $! "Gerando chave de segurança da aplicação"

    print_success "Projeto RegistroEdu ${BOLD}configurado${NC} com sucesso"
}

setup_database() {
    print_step "🗄️  Configurando banco de dados"

    (sudo -u postgres createdb "$DB_NAME" > /dev/null 2>&1) && print_success "Banco '${BOLD}$DB_NAME${NC}' criado" || print_warning "Banco '$DB_NAME' já existe"

    echo
    echo -e "${CYAN}${BOLD}📋 Configuração da Conexão com Banco de Dados${NC}"
    print_separator

    echo -e "${BLUE}🏠 Host do PostgreSQL:${NC}"
    read -p "   $(echo -e ${DIM})Digite o endereço (padrão: 127.0.0.1): $(echo -e ${NC})" DB_HOST
    DB_HOST=${DB_HOST:-127.0.0.1}

    echo -e "${BLUE}🔌 Porta do PostgreSQL:${NC}"
    read -p "   $(echo -e ${DIM})Digite a porta (padrão: 5432): $(echo -e ${NC})" DB_PORT
    DB_PORT=${DB_PORT:-5432}

    echo -e "${BLUE}👤 Usuário do banco:${NC}"
    read -p "   $(echo -e ${DIM})Digite o usuário (padrão: postgres): $(echo -e ${NC})" DB_USER
    DB_USER=${DB_USER:-postgres}

    echo -e "${BLUE}🔒 Senha do usuário:${NC}"
    read -s -p "   $(echo -e ${DIM})Digite a senha: $(echo -e ${NC})" DB_PASS
    echo

    if [ -f ".env" ]; then
        sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
        sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
        sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
        sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
        sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
        sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
        sed -i "s/REDIS_HOST=.*/REDIS_HOST=127.0.0.1/" .env
        sed -i "s/REDIS_PASSWORD=.*/REDIS_PASSWORD=null/" .env
        sed -i "s/REDIS_PORT=.*/REDIS_PORT=6379/" .env
        sed -i "s/CACHE_DRIVER=.*/CACHE_DRIVER=redis/" .env
        sed -i "s/SESSION_DRIVER=.*/SESSION_DRIVER=redis/" .env
        print_success "Configurações de banco e cache ${BOLD}atualizadas${NC}"
    fi
}

run_migrations() {
    print_step "🔄 Executando migrações do banco"
    if php artisan migrate:status &> /dev/null; then
        (php artisan migrate --force > /dev/null 2>&1) &
        show_progress $! "Criando estrutura das tabelas do banco"
        print_success "Migrações executadas com ${BOLD}sucesso${NC}"

        echo
        echo -e "${CYAN}🌱 Deseja popular o banco com dados de exemplo?${NC}"
        read -p "$(echo -e ${DIM}   Isso criará usuários e dados de teste ${BOLD}[Y/n]:${NC} )" RUN_SEEDERS
        if [[ $RUN_SEEDERS =~ ^[Yy]$|^$ ]]; then
            (php artisan db:seed --force > /dev/null 2>&1) &
            show_progress $! "Populando banco com dados de demonstração"
            print_success "Dados de exemplo ${BOLD}inseridos${NC}"
        fi
    else
        print_error "Falha na conexão com o banco de dados"
        print_warning "Verifique as configurações no arquivo .env"
        echo -e "${YELLOW}💡 Dica: Execute 'php artisan migrate' após corrigir a conexão${NC}"
    fi
}

set_permissions() {
    print_step "🔐 Configurando permissões e links"
    if [ ! -L "public/storage" ]; then
        php artisan storage:link
        print_success "Link simbólico do storage ${BOLD}criado${NC}"
    fi

    chmod -R 755 storage bootstrap/cache
    if id "www-data" &>/dev/null; then
        sudo chown -R "$USER:www-data" storage bootstrap/cache public
        chmod -R 775 storage bootstrap/cache
        print_success "Permissões para servidor web ${BOLD}configuradas${NC}"
    else
        print_success "Permissões básicas ${BOLD}configuradas${NC}"
    fi
}

compile_assets() {
    print_step "🎨 Compilando assets frontend"
    (npm run build > /dev/null 2>&1) &
    show_progress $! "Compilando CSS e JavaScript com Vite"
    print_success "Assets frontend ${BOLD}compilados${NC}"
}

show_completion_info() {
    clear
    echo -e "${GREEN}${BOLD}"
    cat << "EOF"
    ╔══════════════════════════════════════════════════════════════════════╗
    ║                                                                      ║
    ║                    🎉  INSTALAÇÃO CONCLUÍDA!  🎉                     ║
    ║                                                                      ║
    ║                     RegistroEdu está pronto para uso!                ║
    ║                                                                      ║
    ╚══════════════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
    echo
    echo -e "${CYAN}${BOLD}📊 RESUMO DA INSTALAÇÃO REALIZADA${NC}"
    print_separator
    echo -e "   ${GREEN}✅${NC} PHP $(php -v | head -n1 | cut -d' ' -f2) ${DIM}com todas as extensões necessárias${NC}"
    echo -e "   ${GREEN}✅${NC} PostgreSQL ${DIM}com banco '${BOLD}$DB_NAME${NC}${DIM}' configurado${NC}"
    echo -e "   ${GREEN}✅${NC} Redis ${DIM}para cache e sessões otimizadas${NC}"
    echo -e "   ${GREEN}✅${NC} Node.js $(node -v) ${DIM}e NPM para desenvolvimento frontend${NC}"
    echo -e "   ${GREEN}✅${NC} Composer ${DIM}com dependências PHP instaladas${NC}"
    echo -e "   ${GREEN}✅${NC} Assets ${DIM}compilados e otimizados com Vite${NC}"
    echo

    echo -e "${YELLOW}${BOLD}🚀 COMO INICIAR O SISTEMA${NC}"
    print_separator
    echo
    echo -e "${PURPLE}${BOLD}1.${NC} ${BLUE}Iniciar servidor de desenvolvimento:${NC}"
    echo -e "   ${CYAN}cd $INSTALL_DIR/$PROJECT_NAME/application${NC}"
    echo -e "   ${CYAN}php artisan serve${NC}"
    echo
    echo -e "${PURPLE}${BOLD}2.${NC} ${BLUE}Para desenvolvimento frontend (terminal separado):${NC}"
    echo -e "   ${CYAN}cd $INSTALL_DIR/$PROJECT_NAME/application${NC}"
    echo -e "   ${CYAN}npm run dev${NC}"
    echo
    echo -e "${PURPLE}${BOLD}3.${NC} ${BLUE}Acessar o sistema:${NC}"
    echo -e "   ${UNDERLINE}${CYAN}http://localhost:8000${NC}"
    echo
    echo -e "${PURPLE}${BOLD}4.${NC} ${BLUE}Documentação da API:${NC}"
    echo -e "   ${UNDERLINE}${CYAN}http://localhost:8000/docs${NC}"
    echo

    echo -e "${BLUE}${BOLD}📚 INFORMAÇÕES DO PROJETO${NC}"
    print_separator
    echo -e "   ${BOLD}Localização:${NC} ${CYAN}$INSTALL_DIR/$PROJECT_NAME${NC}"
    echo -e "   ${BOLD}Banco de dados:${NC} ${CYAN}$DB_NAME${NC} (PostgreSQL)"
    echo -e "   ${BOLD}Sistema de cache:${NC} ${CYAN}Redis${NC}"
    echo -e "   ${BOLD}Logs do sistema:${NC} ${CYAN}storage/logs/${NC}"
    echo -e "   ${BOLD}Configurações:${NC} ${CYAN}.env${NC}"
    echo

    echo -e "${GREEN}${BOLD}🎓 RegistroEdu - Transformando a gestão educacional!${NC}"
    echo -e "${DIM}   Sistema completo para administração escolar centralizada${NC}"
    echo
    echo -e "${PURPLE}${BOLD}💡 DICAS IMPORTANTES:${NC}"
    echo -e "   • Use ${CYAN}php artisan tinker${NC} para interagir com o sistema"
    echo -e "   • Execute ${CYAN}php artisan migrate:fresh --seed${NC} para resetar dados"
    echo -e "   • Monitore logs em tempo real com ${CYAN}tail -f storage/logs/laravel.log${NC}"
    echo -e "   • Para produção, configure um servidor web (Apache/Nginx)"
    echo
    echo -e "${YELLOW}${BOLD}🔧 SUPORTE E DOCUMENTAÇÃO:${NC}"
    echo -e "   • Repositório: ${UNDERLINE}${CYAN}$PROJECT_REPO${NC}"
    echo -e "   • Issues: ${UNDERLINE}${CYAN}$PROJECT_REPO/issues${NC}"
    echo
}

main() {
    print_banner
    check_user
    detect_os
    install_base_dependencies

    INITIAL_DIR=$(pwd)
    if [ -d "$HOME/Documents" ]; then
        INSTALL_DIR="$HOME/Documents"
    else
        INSTALL_DIR="$HOME"
    fi

    echo -e "${BLUE}${BOLD}📍 Local de instalação selecionado:${NC} ${CYAN}$INSTALL_DIR${NC}"
    echo -e "${DIM}   O projeto será clonado como: $INSTALL_DIR/$PROJECT_NAME${NC}"
    echo
    sleep 2

    cd "$INSTALL_DIR"

    print_status "Iniciando processo de instalação automatizada..."
    sleep 1

    update_system
    install_git
    install_php
    install_php_extensions
    install_postgresql
    install_redis
    install_composer
    install_nodejs

    echo
    print_separator
    echo -e "${YELLOW}${BOLD}🎯 FASE 2: CONFIGURAÇÃO DO PROJETO${NC}"
    print_separator

    clone_project
    setup_laravel_project
    setup_database
    run_migrations
    set_permissions
    compile_assets

    cd "$INITIAL_DIR"

    show_completion_info
}

main "$@"
