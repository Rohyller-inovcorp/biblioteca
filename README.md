📚 Projeto Biblioteca
Este é um sistema de gestão de biblioteca desenvolvido com Laravel 11, Jetstream (Sanctum), Vue.js e estilizado com DaisyUI. O projeto foca-se na segurança (dados cifrados e 2FA) e na facilidade de utilização com componentes reutilizáveis.

🚀 Como instalar e utilizar
Segue estes passos para configurar o projeto no teu ambiente local:

1. Clonar o Repositório
Bash

git clone https://github.com/Rohyller-inovcorp/biblioteca.git
cd biblioteca
2. Instalar Dependências
Instala as bibliotecas de PHP (Backend) e JavaScript (Frontend):

Bash

composer install
npm install
3. Configuração do Ambiente
Cria o teu ficheiro de configuração a partir do exemplo:

Bash

copy .env.example .env
Gera a chave de segurança da aplicação:

Bash

php artisan key:generate
4. Base de Dados (MySQL)
Cria a base de dados no teu MySQL:

SQL

CREATE DATABASE biblioteca;
No teu ficheiro .env, configura as credenciais de acesso:

Fragmento de código

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca
DB_USERNAME=root
DB_PASSWORD=
Executa as migrações para criar as tabelas:

Bash

php artisan migrate
5. Links de Armazenamento
Para que as imagens das capas, fotos e logótipos fiquem visíveis, cria o link simbólico:

Bash

php artisan storage:link
🛠️ Execução do Projeto
Para colocar o sistema a funcionar, deves abrir dois terminais em simultâneo:

Terminal 1 (Compilação do Frontend):

Bash

npm run dev
Terminal 2 (Servidor Local):

Bash

php artisan serve
✨ Funcionalidades Principais
Gestão Completa (CRUD): Módulos de Livros, Autores e Editoras.

Componentes Reutilizáveis:

DataTable: Componente único para as 3 vistas com funções de pesquisa, ordenação e filtros.

Edit: Componente centralizado para modificações e implementações rápidas.

Segurança Avançada:

Dados sensíveis cifrados na base de dados (Casts).

Autenticação via Laravel Jetstream.

Verificação em dois passos (2FA) disponível.

Proteção de rotas via Middleware (Sanctum/Verified).

Exportação: Botão na página principal para exportar a lista de Livros diretamente para Excel.

UI/UX: Interface moderna utilizando componentes da biblioteca DaisyUI.

🔐 Acesso ao Sistema
O sistema está protegido por autenticação. Para começar a utilizar:

Acede à página inicial.

Clica no botão de Registo (disponível no ecrã de Login) para criar a tua conta.

Após o registo, terás acesso total aos menus e funcionalidades de gestão.
