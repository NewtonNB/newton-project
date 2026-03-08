# MySQL Setup Guide

## ✅ Your Project Now Uses MySQL!

Your project has been successfully converted from SQLite to MySQL.

## 🚀 Quick Start

### Step 1: Start MySQL in XAMPP

1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for it to show "Running" status

### Step 2: Find Your Old Database

Run this command to see all your databases:
```bash
php find_mysql_databases.php
```

This will show you:
- All available databases
- Tables in each database
- Number of rows in each table

### Step 3: Configure Database Connection

The project is configured to use:
- **Host:** localhost
- **User:** root
- **Password:** (empty - default XAMPP)
- **Database:** schoolproject

If your old database has a different name, you have two options:

#### Option A: Use Your Old Database Name
Edit `config.php` and change this line:
```php
$db_name = 'schoolproject';  // Change to your old database name
```

#### Option B: Import Old Data to New Database
The project will automatically create a new `schoolproject` database with sample data.

### Step 4: Access Your Project

Visit: `http://localhost/school-project`

The system will automatically:
- Create the database if it doesn't exist
- Create all required tables
- Add sample data (students, teachers, events, etc.)

## 🔐 Default Login Credentials

- **Username:** admin
- **Password:** admin123

## 📊 Database Structure

The following tables will be created automatically:

1. **students** - Student records and admin users
2. **teachers** - Teacher information
3. **classes** - Class definitions (S1-S6)
4. **fees** - Fee payment records
5. **contact_messages** - Contact form submissions
6. **announcements** - School announcements and events
7. **olevel_subjects** - O-Level subject list
8. **alevel_subjects** - A-Level subject list
9. **admission** - Admission applications
10. **events** - School events
11. **event_registrations** - Event registration records

## 🔄 Importing Old Data

If you have an old MySQL database with data you want to keep:

### Method 1: Using phpMyAdmin

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select your old database
3. Click "Export" tab
4. Choose "Quick" export method
5. Click "Go" to download SQL file
6. Create new database "schoolproject" (if needed)
7. Select "schoolproject" database
8. Click "Import" tab
9. Choose your SQL file
10. Click "Go"

### Method 2: Using MySQL Command Line

```bash
# Export old database
mysqldump -u root old_database_name > backup.sql

# Import to new database
mysql -u root schoolproject < backup.sql
```

### Method 3: Rename Your Old Database

If your old database is called something else (e.g., "school_db"):

```sql
-- In phpMyAdmin or MySQL command line
RENAME DATABASE old_database_name TO schoolproject;
```

Or simply update `config.php` to use your old database name.

## 🛠️ Troubleshooting

### MySQL Won't Start in XAMPP

1. Check if port 3306 is already in use
2. Try stopping other MySQL services
3. Check XAMPP error logs
4. Restart XAMPP as Administrator

### Can't Connect to Database

1. Make sure MySQL is running in XAMPP
2. Check username/password in `config.php`
3. Verify database name exists
4. Check MySQL error logs

### Tables Not Created

1. Check MySQL user has CREATE privileges
2. Verify database exists
3. Check PHP error logs
4. Try accessing the site - tables create automatically

### Old Data Not Showing

1. Verify you're using the correct database name
2. Check if tables have data: `SELECT COUNT(*) FROM students;`
3. Make sure you imported data correctly
4. Check table names match (case-sensitive on Linux)

## 📝 Configuration Files

- **config.php** - Database connection settings
- **config_email.php** - Email configuration (already set up)
- **.gitignore** - Excludes sensitive files from Git

## 🔒 Security Notes

1. Change default admin password after first login
2. Use strong MySQL password in production
3. Don't commit `config.php` with production credentials
4. Keep XAMPP updated

## 📞 Need Help?

If you encounter issues:
1. Check XAMPP MySQL logs
2. Check PHP error logs
3. Verify MySQL service is running
4. Test database connection with `find_mysql_databases.php`

## ✨ What's New

Your project now has:
- ✅ MySQL database (more robust than SQLite)
- ✅ Automatic table creation
- ✅ Sample data for testing
- ✅ Foreign key constraints
- ✅ Better performance for large datasets
- ✅ Compatible with most hosting providers

---

**Ready to go!** Just start MySQL in XAMPP and visit your project.
