# Railway Reservation & Management System

A comprehensive PHP-based railway reservation system that allows administrators to manage trains and routes while passengers can search, book, and manage their tickets.

## 🚀 Features

### Admin Module
- **Secure Authentication**: Admin login/logout with session management
- **Train Management**: Add, edit, delete trains with seat capacity
- **Route Management**: Configure routes with timing, pricing, and distance
- **Booking Monitoring**: View all passenger bookings and system statistics
- **Dashboard**: Real-time statistics and quick actions

### Passenger Module
- **User Registration**: Secure passenger account creation
- **Train Search**: Search trains by source, destination, and date
- **Real-time Availability**: Check seat availability before booking
- **Online Booking**: Book tickets with passenger details
- **Booking Management**: View booking history and ticket details
- **Cancellation**: Cancel bookings with automatic seat restoration

### Security Features
- **Password Hashing**: Secure password storage using PHP's password_hash()
- **SQL Injection Prevention**: Prepared statements for all database operations
- **Input Validation**: Sanitization and validation of all user inputs
- **Session Security**: Secure session management with timeout
- **CSRF Protection**: Token-based CSRF protection

## 🛠️ Technical Stack

- **Backend**: PHP 8.0+ (Object-oriented with PDO)
- **Database**: MySQL 5.7+
- **Frontend**: TailwindCSS 3.0
- **Icons**: Font Awesome 6.4
- **Architecture**: MVC-based structure with separate classes

## 📁 Project Structure

```
railway-system/
├── admin/
│   ├── dashboard.php      # Admin dashboard
│   ├── trains.php         # Train management
│   ├── routes.php         # Route management
│   └── bookings.php       # View all bookings
├── passenger/
│   ├── dashboard.php      # Passenger dashboard
│   ├── search.php         # Search trains
│   ├── book.php           # Book tickets
│   ├── history.php        # Booking history
│   ├── ticket.php         # View ticket details
│   └── cancel.php         # Cancel booking
├── includes/
│   ├── auth.php           # Authentication class
│   ├── database.php       # Database connection
│   ├── train.php          # Train management class
│   ├── route.php          # Route management class
│   ├── booking.php        # Booking management class
│   ├── functions.php      # Utility functions
│   └── header.php         # HTML header template
├── config/
│   └── config.php         # Configuration settings
├── assets/
│   ├── css/               # Custom CSS files
│   └── js/                # JavaScript files
├── database.sql           # Database schema
├── index.php              # Homepage
├── login.php              # Login page
├── register.php           # Registration page
└── logout.php             # Logout handler
```

## 🗄️ Database Schema

The system uses the following main tables:

- **users**: Stores admin and passenger accounts
- **trains**: Train information with seat capacity
- **routes**: Train routes with timing and pricing
- **bookings**: Passenger booking records

## 🚀 Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

### Setup Instructions

1. **Clone/Download the Project**
   ```bash
   git clone <repository-url>
   cd railway-system
   ```

2. **Database Setup**
   ```sql
   -- Create database
   CREATE DATABASE railway_system;
   
   -- Import the schema
   mysql -u username -p railway_system < database.sql
   ```

3. **Configuration**
   - Edit `config/config.php` to set your database credentials
   - Update `APP_URL` to match your domain

4. **Web Server Setup**
   - Place the project in your web root (e.g., `/var/www/html/`)
   - Ensure `uploads/` directory is writable if using file uploads

5. **Access the Application**
   - Open your browser and navigate to `http://localhost/railway-system`
   - Default admin credentials: `admin` / `admin123`

## 🎯 Usage Guide

### For Administrators
1. Login with admin credentials
2. Use the dashboard to monitor system statistics
3. Manage trains via the "Manage Trains" section
4. Configure routes with timing and pricing
5. Monitor all passenger bookings

### For Passengers
1. Register a new account or login
2. Search trains by entering source, destination, and date
3. View available trains with seat information
4. Book tickets by providing passenger details
5. Manage bookings through the dashboard

## 🔒 Security Considerations

- All passwords are hashed using PHP's `password_hash()`
- Database queries use prepared statements to prevent SQL injection
- User inputs are sanitized and validated
- Session management includes timeout protection
- File uploads are restricted to safe types

## 🎨 Customization

### Changing the Theme
- Modify TailwindCSS classes in the header template
- Update color schemes in the configuration file

### Adding New Features
- Follow the existing class structure
- Use the database abstraction layer for new tables
- Implement proper validation and security measures

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Check database credentials in `config/config.php`
   - Ensure MySQL server is running
   - Verify database exists and user has permissions

2. **Session Issues**
   - Check PHP session configuration
   - Ensure proper file permissions for session storage

3. **CSS/JS Not Loading**
   - Verify asset paths in the header template
   - Check web server configuration for static files

## 📞 Support

For issues and questions:
- Check the troubleshooting section above
- Review the code comments for implementation details
- Test with the provided sample data

## 📄 License

This project is provided for educational purposes. Feel free to modify and use it according to your needs.

---

**Happy Coding! 🚂**