# 📧 Lead Assignment Notification System

## 🎯 Overview

The Mini CRM system includes a comprehensive notification system that automatically sends email notifications to agents when leads are assigned to them. The system uses Laravel's queued notifications for better performance and reliability.

## 🔧 How It Works

### 1. **Automatic Triggering**
Notifications are automatically sent when:
- A new lead is created and assigned to an agent
- An existing lead is reassigned to a different agent
- Lead assignment is changed during updates

### 2. **Queued Processing**
- All notifications implement `ShouldQueue` interface
- Notifications are stored in the `jobs` table
- Processed by queue workers for better performance
- Failed notifications are logged for debugging

### 3. **Email Delivery**
- Uses Laravel's built-in mail system
- Configurable mail drivers (SMTP, Mailgun, etc.)
- HTML email templates with lead details
- Direct links to view assigned leads

## 📋 Notification Content

Each notification email includes:

- **Subject**: "New Lead Assigned: [Lead Name]"
- **Greeting**: Personalized with agent's name
- **Lead Details**:
  - Name
  - Email address
  - Phone number (if provided)
  - Current status
- **Action Button**: Direct link to view the lead
- **Instructions**: Prompt to contact the lead

## 🚀 Testing the Notification System

### **Option 1: Using Artisan Command**
```bash
# Test with default data (creates test lead and finds first agent)
php artisan test:notification

# Test with specific lead and agent
php artisan test:notification --lead-id=1 --agent-id=2
```

### **Option 2: Manual Testing**
1. Create a lead and assign it to an agent
2. Check the `jobs` table for queued notifications
3. Process the queue to send emails

### **Option 3: Through the Web Interface**
1. Login as admin user
2. Create or edit a lead
3. Assign it to an agent
4. Notification will be queued automatically

## ⚙️ Configuration

### **Queue Driver**
Set in `.env` file:
```env
QUEUE_CONNECTION=database
```

### **Mail Configuration**
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourcompany.com"
MAIL_FROM_NAME="Mini CRM"
```

### **For Development/Testing**
```env
MAIL_MAILER=log
```
This will log emails to `storage/logs/laravel.log` instead of sending them.

## 🔄 Queue Processing

### **Start Queue Worker**
```bash
# Process jobs continuously
php artisan queue:work

# Process jobs once and exit
php artisan queue:work --once

# Process specific queue
php artisan queue:work --queue=default,emails
```

### **Monitor Queue Status**
```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## 📊 Database Tables

### **Jobs Table**
Stores queued notifications:
```sql
CREATE TABLE jobs (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    queue varchar(255) NOT NULL,
    payload longtext NOT NULL,
    attempts tinyint unsigned NOT NULL,
    reserved_at int unsigned NULL,
    available_at int unsigned NOT NULL,
    created_at int unsigned NOT NULL,
    PRIMARY KEY (id)
);
```

### **Failed Jobs Table**
Stores failed notification attempts:
```sql
CREATE TABLE failed_jobs (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    uuid varchar(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload longtext NOT NULL,
    exception longtext NOT NULL,
    failed_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY failed_jobs_uuid_unique (uuid)
);
```

## 🧪 Testing Commands

### **Create Test Data**
```bash
# Create admin and agent users
php artisan db:seed --class=AdminUserSeeder

# Test notification system
php artisan test:notification
```

### **Verify Queue**
```bash
# Check if jobs table exists
php artisan migrate:status

# View queued jobs
php artisan queue:monitor

# Process queue manually
php artisan queue:work --once
```

## 🚨 Troubleshooting

### **Common Issues**

1. **Notifications Not Sending**
   - Check queue worker is running: `php artisan queue:work`
   - Verify mail configuration in `.env`
   - Check `storage/logs/laravel.log` for errors

2. **Queue Jobs Not Processing**
   - Ensure `jobs` table exists: `php artisan migrate`
   - Check queue driver configuration
   - Verify database connection

3. **Email Delivery Issues**
   - Test mail configuration: `php artisan tinker` then `Mail::raw('test', function($msg) { $msg->to('test@example.com'); })`
   - Check SMTP credentials
   - Verify firewall/network settings

### **Debug Commands**
```bash
# Check queue status
php artisan queue:work --once --verbose

# View failed jobs
php artisan queue:failed

# Clear all queues
php artisan queue:clear
```

## 🔒 Security Features

- **Rate Limiting**: Built-in Laravel queue rate limiting
- **Job Encryption**: Sensitive data is encrypted in queue
- **Failed Job Handling**: Failed notifications are logged and can be retried
- **Queue Isolation**: Different queues for different notification types

## 📱 Future Enhancements

### **Potential Improvements**
- **SMS Notifications**: Add Twilio integration for SMS
- **Push Notifications**: Browser and mobile push notifications
- **Slack Integration**: Send notifications to Slack channels
- **Custom Templates**: Allow agents to customize notification preferences
- **Batch Notifications**: Send multiple lead assignments in one email
- **Notification History**: Track all sent notifications

## 📚 Related Files

- `app/Notifications/LeadAssignedNotification.php` - Main notification class
- `app/Http/Controllers/LeadController.php` - Triggers notifications
- `config/queue.php` - Queue configuration
- `database/migrations/0001_01_01_000002_create_jobs_table.php` - Queue table
- `app/Console/Commands/TestNotification.php` - Testing command

---

**The notification system is fully implemented and ready for production use!** 🎉
