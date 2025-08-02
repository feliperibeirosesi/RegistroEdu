# 🎓 RegistroEdu

### Sistema de Administração Escolar Centralizada

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![React](https://img.shields.io/badge/React-18.x-61DAFB?style=for-the-badge&logo=react&logoColor=black)](https://react.dev/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15.x-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg?style=for-the-badge)](https://www.gnu.org/licenses/gpl-3.0)

</div>

<div align="center">

**🔐 Segurança em Primeiro Lugar** • **🎯 Especializado em Gestão Escolar** • **📊 Inteligência Digital**

</div>

---

## 🌟 Visão Geral

**RegistroEdu** é uma plataforma completa de gestão administrativa escolar, construída com foco em **segurança**, **organização de dados** e **controle rigoroso de permissões**.

Transformamos a burocracia das planilhas em uma **experiência digital moderna e eficiente**, centralizando dados de faltas, ocorrências, aulas, suspensões e muito mais em um sistema unificado e seguro.

### 🚀 Por que RegistroEdu?

<table>
<tr>
<td align="center" width="33%">
<img src="https://img.icons8.com/fluency/96/000000/database.png" alt="Centralização"/>
<br><strong>Centralização Total</strong>
<br><small>Todos os dados escolares em uma única plataforma segura</small>
</td>
<td align="center" width="33%">
<img src="https://img.icons8.com/fluency/96/000000/artificial-intelligence.png" alt="Automação"/>
<br><strong>Automação Inteligente</strong>
<br><small>Redução drástica de erros através de processos automatizados</small>
</td>
<td align="center" width="33%">
<img src="https://img.icons8.com/fluency/96/000000/security-checked.png" alt="Segurança"/>
<br><strong>Máxima Segurança</strong>
<br><small>Controle de acesso hierárquico e auditoria completa</small>
</td>
</tr>
</table>

---

## ✨ Funcionalidades Principais

### 👨‍🎓 **Gestão Completa de Alunos**

- 📝 **Cadastro detalhado** com dados pessoais e acadêmicos
- 🏫 **Organização por turmas**, séries e períodos letivos
- 📚 **Histórico acadêmico** completo e permanente
- 🔍 **Busca avançada** com filtros inteligentes

### 📊 **Controle Acadêmico Avançado**

- ✅ **Registro de faltas** por aula e período com justificativas
- 📋 **Sistema de ocorrências** disciplinares categorizadas
- ⏸️ **Gestão de suspensões** e medidas educativas
- 📈 **Frequência em tempo real** com alertas automáticos
- 📄 **Relatórios personalizados** com exportação em múltiplos formatos

### 🎛️ **Painel Administrativo Inteligente**

- 🔐 **Sistema de permissões** baseado em cargos e hierarquias
- 📊 **Dashboard interativo** com métricas e indicadores
- 👥 **Gestão completa de usuários** e controle de sessões
- 🕵️ **Logs de auditoria** detalhados para compliance
- 🔔 **Sistema de notificações** em tempo real

---

## 🔐 Arquitetura de Segurança

> 🚨 **Princípio Zero Trust**: Nenhuma ação crítica é executada sem validação e autorização

### 🛡️ **Camadas de Proteção**

<table align="center">
<thead>
<tr>
<th>🔒 Aspecto</th>
<th>🛠️ Implementação</th>
<th>📊 Nível</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>Autenticação</strong></td>
<td>JWT + Bcrypt + 2FA</td>
<td><code>🟢 Alto</code></td>
</tr>
<tr>
<td><strong>Autorização</strong></td>
<td>RBAC + Middleware + Policies</td>
<td><code>🟢 Alto</code></td>
</tr>
<tr>
<td><strong>Proteção Web</strong></td>
<td>CSRF + XSS + SQL Injection</td>
<td><code>🟢 Alto</code></td>
</tr>
<tr>
<td><strong>Auditoria</strong></td>
<td>Logs detalhados + Tracking</td>
<td><code>🟡 Médio</code></td>
</tr>
<tr>
<td><strong>Criptografia</strong></td>
<td>AES-256 + TLS 1.3</td>
<td><code>🟢 Alto</code></td>
</tr>
</tbody>
</table>

---

## 🛠️ Stack Tecnológica

### 🏗️ **Arquitetura do Sistema**

<div align="center">

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   React 18.x    │◄───┤   Laravel 12.x  │◄───┤  PostgreSQL 15  │
│   + CSS Puro    │    │   + Sanctum     │    │   + Redis       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

</div>

### 📚 **Tecnologias Detalhadas**

<table>
<thead>
<tr>
<th>🏗️ Camada</th>
<th>⚡ Tecnologia</th>
<th>📋 Versão</th>
<th>🎯 Função</th>
<th>📊 Status</th>
</tr>
</thead>
<tbody>
<tr>
<td rowspan="3"><strong>Frontend</strong></td>
<td>React</td>
<td>18.x</td>
<td>Interface de Usuário</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td>CSS3 Puro</td>
<td>-</td>
<td>Estilização Customizada</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td>Vite</td>
<td>5.x</td>
<td>Build Tool</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td rowspan="4"><strong>Backend</strong></td>
<td>Laravel</td>
<td>12.x</td>
<td>API REST + Business Logic</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td>Laravel Sanctum</td>
<td>Latest</td>
<td>Autenticação API</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td>DomPDF</td>
<td>Latest</td>
<td>Geração de Relatórios</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td>PhpSpreadsheet</td>
<td>Latest</td>
<td>Import/Export Excel</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td rowspan="2"><strong>Database</strong></td>
<td>PostgreSQL</td>
<td>15.x</td>
<td>Banco Principal</td>
<td><code>✅ Stable</code></td>
</tr>
<tr>
<td>Redis</td>
<td>7.x</td>
<td>Cache + Sessions</td>
<td><code>✅ Stable</code></td>
</tr>
</tbody>
</table>

---

## 🚀 Guia de Instalação

### 📋 **Pré-requisitos**

Antes de iniciar, certifique-se de ter instalado:

<table>
<tr>
<td align="center">
<img src="https://img.icons8.com/color/48/000000/php.png" alt="PHP"/>
<br><strong>PHP 8.2.x</strong>
</td>
<td align="center">
<img src="https://img.icons8.com/fluency/48/000000/node-js.png" alt="Node.js"/>
<br><strong>Node.js 18+</strong>
</td>
<td align="center">
<img src="https://img.icons8.com/color/48/000000/postgreesql.png" alt="PostgreSQL"/>
<br><strong>PostgreSQL 15+</strong>
</td>
<td align="center">
<img src="https://img.icons8.com/color/48/000000/git.png" alt="Git"/>
<br><strong>Git</strong>
</td>
</tr>
</table>

### 🔧 **Instalação Passo a Passo**

<details>
<summary><strong>1️⃣ Clone e Configure o Projeto</strong></summary>

```bash
# Clone o repositório
git clone https://github.com/joaopaulopereirarezendesesi/RegistroEdu
cd RegistroEdu

# Instale dependências do backend
composer install

# Instale dependências do frontend
npm install
```

</details>

<details>
<summary><strong>2️⃣ Configure o Ambiente</strong></summary>

```bash
# Copie o arquivo de configuração
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate

# Configure as variáveis de ambiente no .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=registroedu
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

</details>

<details>
<summary><strong>3️⃣ Configure o Banco de Dados</strong></summary>

```bash
# Execute as migrations
php artisan migrate

# Crie o link simbólico para storage
php artisan storage:link
```

</details>

<details>
<summary><strong>4️⃣ Inicie o Servidor</strong></summary>

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Servidor de desenvolvimento React
npm run dev
```

</details>

### 🌐 **Acesso ao Sistema**

- **Frontend**: http://localhost:3000
- **API**: http://localhost:8000
- **Documentação**: http://localhost:8000/docs

---

## 🎨 Design e Estilização

### 🖌️ **CSS Customizado**

O **RegistroEdu** utiliza **CSS3 puro** para máximo controle sobre a aparência e performance:

<table>
<tr>
<td>

**✅ Vantagens do CSS Puro:**

- 🚀 **Performance superior** - sem overhead de frameworks
- 🎯 **Controle total** sobre cada estilo
- 📦 **Bundle menor** - carrega apenas o que precisa
- 🔧 **Customização completa** - sem limitações de framework
- 🎨 **Design único** - identidade visual própria

</td>
</table>

### 🎨 **Padrões de Design**

- **Design System** próprio com variáveis CSS
- **Responsive Design** com Grid e Flexbox
- **Animações** suaves com CSS transitions
- **Temas** claro/escuro nativos
- **Componentes** modulares e reutilizáveis

---

## 🧪 Testes e Qualidade

### 📊 **Cobertura de Testes**

<div align="center">

| **Camada**   | **Cobertura** | **Tipos de Teste**              |
| ------------ | :-----------: | ------------------------------- |
| **Backend**  |   `🟢 85%`    | Unit, Feature, Integration      |
| **Frontend** |   `🟡 70%`    | Component, Hook, E2E            |
| **API**      |   `🟢 90%`    | Endpoint, Security, Performance |

</div>

---

## 👥 Equipe de Desenvolvimento

<table align="center">
<tr>
<td align="center">
<img src="https://avatars.githubusercontent.com/u/163848013?v=4" width="100px;" alt="João Paulo"/><br />
<sub><b>João Paulo</b></sub><br />
<sub>Backend Developer</sub><br />
<small>🏗️ Arquitetura • 🔐 Segurança • ⚡ API</small>
</td>
<td align="center">
<img src="https://avatars.githubusercontent.com/u/163859023?v=4" width="100px;" alt="Kauan Vitor"/><br />
<sub><b>Kauan Vitor</b></sub><br />
<sub>Frontend Developer</sub><br />
<small>⚛️ React • 🎨 CSS • 📱 Responsivo</small>
</td>
<td align="center">
<img src="https://avatars.githubusercontent.com/u/163848058?v=4" width="100px;" alt="Felipe José"/><br />
<sub><b>Felipe José</b></sub><br />
<sub>Product Owner & Backend Developer</sub><br />
<small>📋 Requisitos • 👥 Usuários • 🔐 Segurança • ⚡ API</small>
</td>
<td align="center">
<img src="https://avatars.githubusercontent.com/u/178203493?v=4" width="100px;" alt="Victor Viana"/><br />
<sub><b>Victor Viana</b></sub><br />
<sub>QA & Frontend Developer</sub><br />
<small>⚛️ React • 🎨 CSS • 📱 Responsivo • 📖 Docs</small>
</td>
</tr>
</table>

---

## 🤝 Como Contribuir

Adoramos contribuições! Siga estes passos:

<details>
<summary><strong>📝 Processo de Contribuição</strong></summary>

1. **Fork** o projeto
2. **Crie** uma branch para sua feature
   ```bash
   git checkout -b feature/MinhaNovaFeature
   ```
3. **Commit** suas mudanças
   ```bash
   git commit -m '✨ Add: Minha nova feature incrível'
   ```
4. **Push** para a branch
   ```bash
   git push origin feature/MinhaNovaFeature
   ```
5. **Abra** um Pull Request

</details>

### 📋 **Padrões de Commit**

- `✨ feat:` Nova funcionalidade
- `🐛 fix:` Correção de bug
- `📚 docs:` Documentação
- `🎨 style:` Formatação
- `♻️ refactor:` Refatoração
- `⚡ perf:` Performance
- `🧪 test:` Testes

---

## 🎯 Filosofia do Projeto

<div align="center">

### 🌟 **Nossos Valores**

</div>

<table>
<tr>
<td align="center" width="20%">
<img src="https://img.icons8.com/fluency/64/000000/security-shield-green.png" alt="Segurança"/>
<br><strong>🔒 Segurança</strong>
<br><small>Dados escolares são sensíveis e merecem proteção máxima</small>
</td>
<td align="center" width="20%">
<img src="https://img.icons8.com/fluency/64/000000/user-interface.png" alt="Interface"/>
<br><strong>📱 Simplicidade</strong>
<br><small>Design intuitivo que qualquer educador pode usar</small>
</td>
<td align="center" width="20%">
<img src="https://img.icons8.com/fluency/64/000000/artificial-intelligence.png" alt="Automação"/>
<br><strong>⚡ Automação</strong>
<br><small>Reduzir erro humano através de processos inteligentes</small>
</td>
<td align="center" width="20%">
<img src="https://img.icons8.com/fluency/64/000000/combo-chart.png" alt="Organização"/>
<br><strong>📊 Organização</strong>
<br><small>Substituição completa de planilhas por dados estruturados</small>
</td>
<td align="center" width="20%">
<img src="https://img.icons8.com/fluency/64/000000/speed.png" alt="Performance"/>
<br><strong>🚀 Performance</strong>
<br><small>Sistema rápido e responsivo para uso diário intenso</small>
</td>
</tr>
</table>

---

## 📜 Licença

<div align="center">

**GNU General Public License v3.0**

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg?style=for-the-badge)](https://www.gnu.org/licenses/gpl-3.0)

</div>

### ℹ️ **O que isso significa?**

<table>
<tr>
<td width="50%">

**✅ Você PODE:**

- ✅ Usar comercialmente
- ✅ Modificar o código
- ✅ Distribuir
- ✅ Uso privado
- ✅ Uso em patentes

</td>
<td width="50%">

**❗ Você DEVE:**

- ❗ Incluir copyright e licença
- ❗ Documentar mudanças
- ❗ Disponibilizar código fonte
- ❗ Usar a mesma licença

</td>
</tr>
</table>

Para mais detalhes, consulte o [arquivo LICENSE](LICENSE) ou acesse [GNU GPL v3.0](https://www.gnu.org/licenses/gpl-3.0.pt-br.html).

---

### 📈 **Roadmap**

- 🔄 **v2.0** - Sistema de Notas e Avaliações
- 📱 **v2.1** - App Mobile Nativo
- 🤖 **v2.2** - IA para Análise Preditiva
- 🌐 **v2.3** - Integração com outros sistemas educacionais

---

<div align="center">

### ❤️ **Feito com amor para a educação brasileira**

<br>

**Se este projeto te ajudou, considere dar uma ⭐!**

[![GitHub stars](https://img.shields.io/github/stars/joaopaulopereirarezendesesi/RegistroEdu.svg?style=social&label=Star)](https://github.com/joaopaulopereirarezendesesi/RegistroEdu)
[![GitHub forks](https://img.shields.io/github/forks/joaopaulopereirarezendesesi/RegistroEdu.svg?style=social&label=Fork)](https://github.com/joaopaulopereirarezendesesi/RegistroEdu/fork)
[![GitHub watchers](https://img.shields.io/github/watchers/joaopaulopereirarezendesesi/RegistroEdu.svg?style=social&label=Watch)](https://github.com/joaopaulopereirarezendesesi/RegistroEdu)

</div>

---

<div align="center">
<small>

**RegistroEdu** © 2024 • Transformando a gestão educacional, uma escola por vez.

</small>
</div>
