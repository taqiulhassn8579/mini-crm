# Mini CRM - Quick Setup Guide

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=minicrm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations
```bash
php artisan migrate:fresh
```

**Note**: The system includes these additional migrations:
- `personal_access_tokens` - For API authentication (Sanctum)
- `failed_jobs` - For queue system and notifications
- `jobs` - For queued notifications

### 5. Create Test Users
```bash
php artisan db:seed --class=AdminUserSeeder
```

### 6. Start Development Server
```bash
php artisan serve
```

## 👥 Default Login Credentials

**Admin User:**
- Email: `admin@example.com`
- Password: `password`

**Agent User:**
- Email: `agent@example.com`
- Password: `password`

## 🌐 Access URLs

- **Application**: http://localhost:8000
- **Login**: http://localhost:8000/login
- **Register**: http://localhost:8000/register
- **Dashboard**: http://localhost:8000/dashboard
- **Leads**: http://localhost:8000/leads

## 🔧 API Testing

Import the `Mini_CRM_API.postman_collection.json` file into Postman for API testing.

### API Base URL
```
http://localhost:8000/api
```

### Authentication
1. Use the login endpoint to get a token
2. Include the token in the Authorization header: `Bearer {token}`

## 🧪 Running Tests

```bash
php artisan test
```

## 📧 Email Configuration

For production, update the mail settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check your database credentials in `.env`
   - Ensure MySQL service is running
   - Verify database exists

2. **"Call to undefined method authorizeResource()" Error**
   - This error occurs in Laravel 11+ as the method was removed
   - The application has been updated to use proper authorization checks
   - If you encounter this error, ensure you're using the latest code

2. **Permission Denied Errors**
   - Check storage and bootstrap/cache permissions
   - Run: `chmod -R 775 storage bootstrap/cache`

3. **Class Not Found Errors**
   - Clear composer autoload: `composer dump-autoload`
   - Clear Laravel cache: `php artisan cache:clear`

4. **Migration Errors**
   - Check database connection
   - Ensure all required tables exist
   - Run: `php artisan migrate:fresh`

## 📱 Features Overview

- ✅ User Authentication & Registration
- ✅ Role-Based Access Control (Admin/Agent)
- ✅ Lead Management (CRUD)
- ✅ Lead Assignment System
- ✅ Search & Filtering
- ✅ RESTful API
- ✅ Email Notifications
- ✅ Responsive Design
- ✅ Comprehensive Testing

## 🔒 Security Features

- CSRF Protection
- Form Validation
- Role-Based Authorization
- SQL Injection Prevention
- XSS Protection
- Soft Deletes

## 📚 Next Steps

1. **Customize the application** for your needs
2. **Add more user roles** if required
3. **Extend the lead model** with additional fields
4. **Implement reporting** and analytics
5. **Add audit logging** for compliance
6. **Set up monitoring** and error tracking

---

**Need Help?** Check the main README.md for detailed documentation.
