# 📚 Projeto Biblioteca

Sistema de gestão de biblioteca desenvolvido com Laravel 11, Jetstream (Sanctum), Vue.js e estilizado com DaisyUI. O projeto foca-se na segurança (dados cifrados e 2FA) e na facilidade de utilização com componentes reutilizáveis.

---

## 🚀 Como Instalar e Utilizar

Segue estes passos para configurar o projeto no teu ambiente local:

---

### 1. Clonar o Repositório

```bash
git clone https://github.com/Rohyller-inovcorp/biblioteca.git
cd biblioteca
```

---

### 2. Instalar Dependências

Instala as bibliotecas de PHP (Backend) e JavaScript (Frontend):

```bash
composer install
npm install
```

---

### 3. Configuração do Ambiente

Cria o teu ficheiro de configuração a partir do exemplo:

```bash
copy .env.example .env
```

Gera a chave de segurança da aplicação:

```bash
php artisan key:generate
```

---

### 4. Base de Dados (MySQL)

Cria a base de dados no teu MySQL:

```sql
CREATE DATABASE biblioteca;
```

No teu ficheiro `.env`, configura as credenciais de acesso:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca
DB_USERNAME=root
DB_PASSWORD=
```

Executa as migrações para criar as tabelas:

```bash
php artisan migrate
```

---

### 5. Links de Armazenamento

Para que as imagens das capas, fotos e logótipos fiquem visíveis, cria o link simbólico:

```bash
php artisan storage:link
```

---

## 🛠️ Execução do Projeto

Para colocar o sistema a funcionar, deves abrir dois terminais em simultâneo:

**Terminal 1** (Compilação do Frontend):

```bash
npm run dev
```

**Terminal 2** (Servidor Local):

```bash
php artisan serve
```

---

## ✨ Funcionalidades Principais

### 📖 Gestão Completa (CRUD)
Módulos de **Livros**, **Autores** e **Editoras** com todas as operações de criação, leitura, atualização e eliminação.

---

### 🧩 Componentes Reutilizáveis

- **DataTable**: Componente único para as 3 vistas com funções de pesquisa, ordenação e filtros
- **Edit**: Componente centralizado para modificações e implementações rápidas

---

### 🔐 Segurança Avançada

- Dados sensíveis cifrados na base de dados (Casts)
- Autenticação via Laravel Jetstream
- Verificação em dois passos (2FA) disponível
- Proteção de rotas via Middleware (Sanctum/Verified)

---

### 📊 Exportação

Botão na página principal para exportar a lista de Livros diretamente para **Excel**.

---

### 🎨 UI/UX

Interface moderna utilizando componentes da biblioteca **DaisyUI**.

---

## 🔑 Acesso ao Sistema

O sistema está protegido por autenticação. Para começar a utilizar:

1. Acede à página inicial
2. Clica no botão de **Registo** (disponível no ecrã de Login) para criar a tua conta
3. Após o registo, terás acesso total aos menus e funcionalidades de gestão

---

## 📦 Tecnologias Utilizadas

- **Backend**: Laravel 11 + Jetstream (Sanctum)
- **Frontend**: Vue.js + DaisyUI
- **Base de Dados**: MySQL
- **Autenticação**: Laravel Sanctum + 2FA

---

## 📝 Licença

Este projeto está sob a licença MIT.

---

## 👨‍💻 Autor

Desenvolvido por **Rohyller** - [GitHub](https://github.com/Rohyller-inovcorp)
