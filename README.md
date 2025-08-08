# Maal LMS API

## Overview

Maal LMS API is a robust Learning Management System backend built with PHP using the Laravel framework. It provides a comprehensive set of RESTful endpoints to manage courses, students, instructors, and other educational resources.

## Features

- Course management
- Student enrollment and progress tracking
- Instructor management
- Lesson and content organization
- User authentication and authorization
- File uploads for educational materials and certifications

## Requirements

- PHP 8.1+
- Composer
- MySQL or compatible database
- Node.js and NPM (for asset compilation)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/MaalTijarah/maal-lms-api.git
   cd maal-lms-api
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Create a copy of the `.env.example` file and rename it to `.env`:
   ```bash
   cp .env.example .env
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database settings in the `.env` file.

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. (Optional) Seed the database with sample data:
   ```bash
   php artisan db:seed
   ```

## Usage

To start the development server:

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`.

## Development

### Custom Artisan Commands

This project includes custom Artisan commands to streamline development:

- Create a new repository:
  ```bash
  php artisan make:repository RepositoryName
  ```

- Create a new service:
  ```bash
  php artisan make:service ServiceName
  ```

- Create a new repository interface:
  ```bash
  php artisan make:repository-interface InterfaceName
  ```

### File Structure

The project follows a modular structure with separate directories for:

- Models
- Controllers
- Services
- Repositories
- Interfaces

### Testing

To run the test suite:

```bash
php artisan test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

[MIT License](LICENSE.md)

## Contact

For any inquiries, please contact [support@maaledu.com].