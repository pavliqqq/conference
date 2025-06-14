# Conference Registration Form (PHP Project)

## 🧰 Requirements

- PHP 8.0 or higher
- MySQL or MariaDB
- Web server (built-in PHP server)
- Git (optional)
- Composer 2.8 or higher

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/pavliqqq/conference.git
cd conference
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
```
Edit the .env file if necessary

### 3. Database Setup

1) Connect to MySQL server:

```bash
mysql -u root -p
```

2) Create the database:

```bash
CREATE DATABASE conference CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3) Exit MySQL:

```bash
exit
```

4) Run the import command (for PowerShell):

```bash
Get-Content .\database\dump.sql | mysql -u root -p conference
```

If the above command didn’t work, follow these steps:
5) Open your command prompt (cmd).
6) Navigate to the project directory, for example:

```bash
cd path\to\your\project
```

7) Run the import command:

```bash
mysql -u root -p conference < database/dump.sql
```

### 4. Start Local Server

```bash
php -S localhost:8000 -t public
```

Project will be accessible at:
http://localhost:8000