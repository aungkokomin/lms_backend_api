# README

# Laravel LMS API

A robust backend API for a Learning Management System (LMS) built with Laravel. This system provides endpoints for managing users, roles, permissions, courses, lessons, bundles, certificates, wallets, and more.

---

## 🚀 Features

- **User Management:** Registration, authentication, roles, and permissions ([spatie/laravel-permission](https://github.com/spatie/laravel-permission))
- **Course & Lesson Management:** CRUD for courses, modules, and lessons, including content image uploads
- **Bundles:** Group multiple courses into bundles
- **Certificates:** Issue, upload, and download certificates for students
- **Wallet System:** Manage user wallets and transactions
- **File Uploads:** Support for uploading images, documents, and certificates
- **Queue Support:** Configurable queue drivers for background jobs
- **Excel Import/Export:** ([maatwebsite/excel](https://github.com/Maatwebsite/Laravel-Excel))
- **Notifications & Mail:** Integration with third-party mail services (Mailgun, Postmark, SES, etc.)
- **API-First:** All features are exposed via RESTful API endpoints

---

## 📁 Project Structure

```
app/
  Console/
  Events/
  Exceptions/
  Exports/
  Helpers/
  Http/
  Interfaces/
  Mail/
  Models/
  Notifications/
  Providers/
  Repositories/
  Services/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
vendor/
```

---

## 🛠️ Getting Started

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL or compatible database
- Node.js & npm (for asset building)

### Installation

- **Clone the repository**
    
    `git clone <repository-url> cd backend-lms-api`
    
- **Install PHP dependencies**
    
    `composer install`
    
- **Install Node dependencies**
    
    `npm install` 
    
- **Copy and configure environment variables**
    
    `cp .env.example .env` 
    
    - Edit `.env` to set your database and mail credentials
- **Generate application key**
    
    `php artisan key:generate`
    
- **Run migrations**
    
    `php artisan migrate`
    
- **(Optional) Seed the database**
    
    `php artisan db:seed`
    
- **Build frontend assets**
    
    `npm run build`
    
- **Start the development server**
    
    `php artisan serve`
    

---

## 🧪 Running Tests

```bash
php artisan test
```

---

## ⚙️ Configuration

- **Queue:** Configure your queue driver in
    
    `config/queue.php`
    
- **Permissions:** Manage roles and permissions in
    
    `config/permission.php`
    
- **Mail & Services:** Set up third-party services in
    
    `config/services.php`
    
- **Excel:** Configure Excel import/export in
    
    `config/excel.php`
    

---

## 💡 Useful Commands

- `php artisan migrate:fresh --seed` — Reset and seed the database
- `php artisan queue:work` — Start processing queued jobs
- `php artisan storage:link` — Create a symbolic link for file uploads

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

*For more details, see the codebase and configuration files.*