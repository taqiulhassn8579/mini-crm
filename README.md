# Mini CRM - Laravel 11 Application

A comprehensive Customer Relationship Management (CRM) system built with Laravel 11, featuring role-based access control, lead management, and RESTful API endpoints.

## 🎯 Features

### Core Functionality
- **User Authentication & Registration**: Secure login/registration system
- **Role-Based Access Control**: Admin and Agent roles with different permissions
- **Lead Management**: Complete CRUD operations for leads
- **Assignment System**: Admins can assign leads to sales agents
- **Status Tracking**: Lead status management (New, Contacted, Closed)
- **Search & Filtering**: Advanced search with multiple filters
- **Pagination**: Efficient data display with pagination

### Technical Features
- **RESTful API**: Complete API endpoints for mobile/app integration
- **Form Validation**: Comprehensive validation using Form Request classes
- **Queued Notifications**: Email notifications when leads are assigned
- **Soft Deletes**: Safe deletion with data recovery capability
- **Responsive Design**: Modern Bootstrap-based UI
- **Comprehensive Testing**: Feature tests covering all functionality

## 🛠 Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL/PostgreSQL/SQLite
- **Frontend**: Bootstrap 5, Font Awesome
- **Authentication**: Laravel's built-in auth system
- **Testing**: PHPUnit with Laravel Testing
- **Notifications**: Laravel Notifications with queuing

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (for frontend assets)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd laravel-crm
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=minicrm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup
```bash
php artisan migrate:fresh
```

### 5. Create Admin User
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

### 6. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 👥 User Roles & Permissions

### Admin Users
- ✅ Create, read, update, and delete all leads
- ✅ Assign leads to agents
- ✅ View all leads and statistics
- ✅ Manage system settings

### Agent Users
- ✅ View assigned leads only
- ✅ Update assigned leads
- ✅ Cannot create or delete leads
- ✅ Cannot assign leads to others

## 🔌 API Endpoints

All API endpoints require authentication via Laravel Sanctum.

### Leads API
```
GET    /api/leads          - List all leads (with filters)
POST   /api/leads          - Create a new lead
GET    /api/leads/{id}     - Get lead details
PUT    /api/leads/{id}     - Update lead
DELETE /api/leads/{id}     - Soft delete lead
```

### API Parameters
- `status`: Filter by status (new, contacted, closed)
- `agent`: Filter by assigned agent
- `search`: Search in name, email, or phone
- `sort_by`: Sort field (name, email, status, created_at)
- `sort_direction`: asc or desc

### Example API Response
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "123-456-7890",
            "status": "new",
            "assigned_to": 2,
            "notes": "Interested in premium package",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "current_page": 1,
    "per_page": 15,
    "total": 1
}
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

### Test Coverage
- Role-based access control
- Lead CRUD operations
- API endpoint authentication
- Form validation
- Search and filtering functionality

## 📱 API Testing

### **Postman Collection**
Import the complete API collection for testing:
- **Collection**: `Mini_CRM_API.postman_collection.json`
- **Environment**: `Mini_CRM_API.postman_environment.json`
- **Guide**: [POSTMAN_IMPORT_GUIDE.md](POSTMAN_IMPORT_GUIDE.md)

### **Quick Test**
```bash
# Test notification system
php artisan test:notification

# Demo script
php demo-notification.php
```

## 📧 Notifications

The system sends email notifications when:
- A lead is assigned to an agent
- Lead assignment is changed

### **Features:**
- **Queued Notifications**: All notifications implement `ShouldQueue` for better performance
- **Automatic Triggering**: Notifications are sent automatically when leads are assigned
- **Professional Email Templates**: Beautiful HTML emails with lead details and action buttons
- **Configurable Mail System**: Support for SMTP, Mailgun, and other mail drivers

### **Configuration:**
```env
QUEUE_CONNECTION=database
MAIL_MAILER=smtp  # or 'log' for development
MAIL_HOST=your_smtp_host
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

### **Testing:**
```bash
# Test the notification system
php artisan test:notification

# Process queued notifications
php artisan queue:work
```

**📖 See [NOTIFICATIONS.md](NOTIFICATIONS.md) for complete documentation.**

## 🎨 Customization

### Adding New Lead Statuses
1. Update the migration file
2. Modify the Lead model
3. Update the LeadRequest validation
4. Modify the views to include new statuses

### Adding New User Roles
1. Update the User model's role enum
2. Modify the LeadPolicy
3. Update the views to handle new roles

## 🔒 Security Features

- CSRF protection on all forms
- Role-based access control
- Form request validation
- SQL injection prevention
- XSS protection
- Soft deletes for data safety

## 📱 Mobile Responsiveness

The application is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set up proper mail configuration
- [ ] Configure queue workers
- [ ] Set up SSL certificate
- [ ] Configure web server (Apache/Nginx)

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the Laravel documentation
- Review the test files for usage examples

## 🔄 Updates & Maintenance

### Regular Maintenance Tasks
- Update dependencies: `composer update`
- Clear caches: `php artisan cache:clear`
- Optimize for production: `php artisan optimize`
- Monitor queue workers
- Backup database regularly

---

**Built with ❤️ using Laravel 11**
