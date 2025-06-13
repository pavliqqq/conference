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

### 2. Database Setup

1. Create a MySQL database:

```bash
CREATE DATABASE conference CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
2. Import the MySQL dump:

```bash
mysql -u root -p conference < database/dump.sql
```

### 3. Start Local Server

```bash
php -S localhost:8000 -t public
```
Project will be accessible at:
http://localhost:8000