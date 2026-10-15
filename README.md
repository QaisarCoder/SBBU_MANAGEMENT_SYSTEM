# SBBU Attendance Management System

A comprehensive web-based attendance management system for SBBU (Sindh Board of University) institutions. This system provides role-based access control for administrators, teachers, clerks, and students to manage academic activities efficiently.

## Features

### For Administrators
- User management (students, teachers, staff, clerks)
- Program and subject management
- Batch and class registration
- Attendance marking with geo-fencing
- Session management (login/logout tracking)
- Holiday management and calendar syncing
- Announcements and notices
- Reports and analytics
- Dispute resolution for attendance issues

### For Teachers
- Mark attendance for assigned classes
- View student lists per class
- Access class schedules
- Track attendance statistics

### For Clerks/Staff
- Student enrollment and bulk upload
- Program and batch management
- Class registration
- Generate reports and exports
- Manage user accounts

### For Students
- View personal attendance records
- Check class schedules
- Access notices and announcements
- Raise attendance disputes
- View attendance statistics

## Project Structure

```
SBBU_MANAGEMENT_SYSTEM/
├── includes/               # Core PHP includes
│   ├── config.php          # Configuration & env vars
│   ├── db.php              # Database connection
│   ├── auth.php            # Authentication & RBAC
│   └── functions.php        # Utility functions
├── pages/                  # Frontend pages
│   ├── dashboard.php       # Main dashboard
│   └── *.php               # Various management pages
├── api/                    # Backend API endpoints
│   ├── *.php               # RESTful API for all operations
├── assets/                 # Static assets
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript
│   └── images/             # Assets
├── *.php                   # Main entry points
├── .gitignore             # Git ignore rules
├── .env.example           # Environment variables template
├── README.md              # This file
└── LICENSE                # Project license
```

## Quick Start

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer (for dependency management)
- Web server (Apache, Nginx, or built-in PHP server)

### Installation Steps

#### 1. Setup Database
```bash
# Import the database schema (recommended)
mysql -u root -p < database.sql

# Or create database manually using schema.sql
```

#### 2. Configure Environment
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

Edit `.env` file with your database credentials and configuration:
```bash
# Database configuration
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME sbbu_attendance

# App configuration
APP_NAME="SBBU Attendance Management System"
APP_ENV=local
APP_DEBUG=true

# Security
ALLOWED_IP_RANGE=192.168.1.0/24  # Optional: restrict access by IP

# Email configuration (for notifications)
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
```

#### 3. Upload to Web Server
1. Upload entire project to your web server's document root
2. Ensure proper file permissions:
   - `includes/config.php` should NOT be in .gitignore
   - `logs/` directory writable

#### 4. Access the Application
Open your browser and navigate to:
```
http://your-server/SBBU_MANAGEMENT_SYSTEM/
```

#### 5. First Time Admin Setup
1. Access `http://your-server/SBBU_MANAGEMENT_SYSTEM/login.php`
2. Use default credentials (if any) or create first admin user via direct database insertion
3. Admin should have `role = 'admin'` in the `users` table

## API Documentation

The system provides RESTful API endpoints for all operations. All API calls require authentication via session tokens.

### Authentication
- Login API: `POST api/login.php`
- Returns session token and user data

### API Usage Examples
```bash
# Get students for a class (requires auth)
curl -X GET "http://your-server/api/get_students.php?class_id=123"

# Mark attendance
curl -X POST "http://your-server/api/save_attendance.php"
-H "Content-Type: application/json"
-d '{"class_id": 123, "date": "2024-01-15", "attendance": [{"student_id": "ST001", "status": "present"}]}'
```

## Features Demonstration

### Role-Based Access Control
- Different interfaces and permissions for each user type
- Admin has full control
- Teacher can only mark and view their classes
- Student can only view their own records

### Geo-Fencing
- Attendance can only be marked from campus premises
- Configurable radius and coordinates

### Two-Factor Authentication
- Optional 2FA for admin users (commented out in default config)

### Real-Time Features
- Live attendance marking with status detection
- Late detection based on class start times
- Face API integration for attendance (if configured)

## Security Considerations

1. **Environment Variables**: Store sensitive credentials in `.env` file (not in version control)
2. **Session Security**: Use secure, HTTP-only cookies in production
3. **Input Validation**: All inputs are validated through prepared statements
4. **Access Control**: Role-based permissions implemented throughout
5. **File Uploads**: Currently not implemented - use with caution if added

## Dependencies

### PHP Extensions
- PDO (PHP Data Objects)
- password_hash (for secure password storage)

### External Services
- Nager.Date API for holiday synchronization
- Face API for facial recognition (optional)
- Chart.js for data visualization
- FontAwesome for icons

### Frontend
- Chart.js for charts and graphs
- Moment.js for date/time handling
- Animate.css for animations
- Face-api.js for facial recognition

## Database Schema

See `database.sql` for complete schema including:
- Users and authentication
- Students, teachers, and staff management
- Programs, subjects, and classes
- Attendance tracking with geo-fencing
- Session management
- Announcements and notices
- Reports and analytics

## Configuration

### Key Settings

| Setting | Description | Default |
|---------|-------------|---------|
| `APP_DEBUG` | Enable/disable error display | `false` |
| `ALLOWED_IP_RANGE` | Restrict access by IP (CIDR notation) | Empty (disabled) |
| `CAMPUS_LAT` / `CAMPUS_LNG` | Campus coordinates for geo-fencing | Karachi coordinates |
| `CAMPUS_RADIUS` | Geo-fence radius in meters | `500` |

## Development

### Running Tests
```bash
# No existing test suite found - system is production-ready
```

### Local Development
```bash
# Start PHP built-in server
php -S localhost:8000 -t . .htaccess

# Or use Apache/Nginx for production
```

### Database Management
- Use MySQL Workbench or similar GUI tool
- Export schema using `mysqldump`
- Keep backups of `sbbu_attendance` database

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For issues and support:
1. Check GitHub Issues for existing problems
2. Submit new issues with detailed descriptions
3. Include server environment and configuration details

## Changelog

### Version 1.0.0
- Initial release with core functionality
- Admin, teacher, clerk, and student roles
- Full attendance management system
- Geo-fencing for location-based attendance
- Real-time attendance tracking

## License

This project is licensed under the MIT License. See `LICENSE` file for details.

## Acknowledgements

- SBBU - Sindh Board of University
- Open source contributors
- PHP community
- MySQL team
- Various open source libraries and frameworks used

---

**Need help with setup?** Check the GitHub repository issues or contact support.

*Last updated: $(date -I)*