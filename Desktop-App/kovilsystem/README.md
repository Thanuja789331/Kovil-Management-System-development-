# 🛕 Kovil Management System

A complete full-stack web application for managing temple (kovil) activities, built with PHP (MVC architecture), MySQL, and Tailwind CSS.

## ✨ Features

### For Devotees
- **User Registration & Login** - Create account with role selection
- **Quick Action Cards** - Prominent dashboard buttons for easy navigation
- **View Pooja Schedule** - Browse all available poojas with dates and time slots
- **Book Pooja** - Reserve pooja slots with instant on-screen confirmation
- **SMS Confirmations** - Receive booking confirmation on mobile (optional)
- **View My Bookings** - Track all confirmed bookings
- **Make Donations** - Contribute to temple with various purposes
- **View Festivals** - Check upcoming temple festivals and events
- **View Announcements** - Stay updated with temple news

### For Priests
- **Dedicated Dashboard** - View assigned duties
- **Schedule Management** - See upcoming pooja assignments
- **Status Tracking** - Monitor completed and pending duties

### For Management/Admin
- **Admin Dashboard** - Comprehensive statistics and quick actions
- **Assign Priest Duties** - Allocate poojas to priests
- **Reports & Analytics** - Detailed statistics with progress bars
- **Donation Tracking** - Monitor all temple donations
- **Manage Announcements** - Post temple updates
- **Festival Management** - Add and manage temple events
- **Schedule Management** - Create, edit, and delete pooja schedules
- **Booking Visibility** - See which poojas are booked and by whom

## 🏗️ Technology Stack

- **Backend**: Pure PHP (No frameworks) - MVC Architecture
- **Database**: MySQL (via XAMPP)
- **Frontend**: HTML + Tailwind CSS (Local installation)
- **Design**: Glassmorphism UI with modern animations
- **Security**: Prepared statements, password hashing, session management
- **SMS Integration**: SMS notification support for booking confirmations

## 📁 Project Structure

```
kovilSystem_fixed/
├── app/
│   ├── controllers/
│   │   └── MainController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Schedule.php
│   │   ├── Booking.php
│   │   ├── Duty.php
│   │   ├── Donation.php
│   │   ├── Announcement.php
│   │   └── Festival.php
│   └── views/
│       ├── layouts/
│       │   ├── header.php
│       │   └── footer.php
│       ├── login/
│       ├── register/
│       ├── dashboard/
│       ├── schedule/
│       ├── book/
│       ├── confirmation/
│       ├── priest/
│       ├── assign/
│       ├── donation/
│       ├── report/
│       ├── announcement/
│       ├── festival/
│       └── my-bookings/
├── config/
│   ├── config.php
│   ├── database.php
│   ├── bootstrap.php
│   ├── helpers.php
│   ├── sms_helper.php
│   └── translations.php
├── public/
│   ├── css/
│   │   └── style.css (compiled Tailwind)
│   └── images/
│       └── bg.jpg (temple background)
├── src/
│   └── input.css (Tailwind source)
├── index.php (entry point)
├── .htaccess (URL rewriting)
├── database_complete.sql
├── package.json
├── tailwind.config.js
└── README.md
```

## 🚀 Installation & Setup

### Prerequisites
- XAMPP (or any PHP development environment with Apache + MySQL)
- Node.js and npm installed
- Modern web browser

### Step 1: Install Dependencies

```bash
# Navigate to project directory
cd kovilSystem

# Install Node.js dependencies (Tailwind CSS)
npm install
```

### Step 2: Build Tailwind CSS

```bash
# Build production CSS
npm run build:css

# OR watch for changes during development
npm run watch:css
```

### Step 3: Setup Database

1. Start XAMPP (Apache and MySQL)
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Import the database:
   - Click "Import" tab
   - Choose file: `database_complete.sql`
   - Click "Go"

OR use MySQL command line:
```bash
mysql -u root -p < database_complete.sql
```

### Step 4: Configure Application

The default configuration should work out of the box. If needed, update:

**config/config.php**
```php
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "kovil_db");
```

### Step 5: Add Background Image (Optional)

Place a temple background image at:
```
public/images/bg.jpg
```

### Step 6: Access the Application

Open your browser and navigate to:
```
http://localhost/kovilSystem_fixed
```

**Note:** The project folder name should be `kovilSystem_fixed` or update the URL accordingly.

## 👤 Default User Credentials

### Management/Admin Account
- **Email**: admin@kovil.com
- **Password**: password

### Priest Account
- **Email**: priest@kovil.com
- **Password**: password

### Devotee Account
- **Email**: devotee@kovil.com
- **Password**: password

## 🎨 Custom Tailwind Colors

The application uses custom colors defined in `tailwind.config.js`:

- **Primary**: Green shades (primary-50 to primary-900)
- **Secondary**: Orange shades (secondary-50 to secondary-900)
- **Accent**: Blue shades (accent-50 to accent-900)

## 🔒 Security Features

- Password hashing using `password_hash()` and `password_verify()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Secure session configuration
- Role-based access control
- Input validation and sanitization
- CSRF protection via session tokens

## 📱 Responsive Design

The application is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile devices

## 🛠️ Development Commands

```bash
# Install dependencies
npm install

# Build CSS for production
npm run build:css

# Watch CSS for changes
npm run watch:css
```

## 🐛 Troubleshooting

### Issue: CSS not loading
**Solution**: Run `npm run build:css` and ensure the path in header.php is correct

### Issue: Database connection error
**Solution**: 
- Check XAMPP MySQL is running
- Verify database credentials in config/config.php
- Ensure kovil_db database exists
- Check database user permissions

### Issue: Page not found errors
**Solution**: 
- Check that mod_rewrite is enabled in Apache
- Verify .htaccess if using one
- Ensure all files are in correct directories
- Clear browser cache

### Issue: Booking form shows error
**Solution**:
- Ensure you're logged in as a devotee
- Check that the schedule ID is valid
- Verify phone number format (10 digits)
- Check that the pooja slot is still available

### Issue: SMS not sending
**Solution**:
- Verify SMS helper functions in config/sms_helper.php
- Check SMS gateway API credentials
- Review error logs in PHP error_log

## 📊 Database Schema

### Tables Created:
1. **users** - User accounts with roles (management, priest, devotee)
2. **pooja_schedule** - Pooja schedules with availability status
3. **bookings** - Pooja booking records with unique references
4. **priest_duties** - Priest assignment tracking
5. **donations** - Donation records
6. **announcements** - Temple announcements
7. **festivals** - Festival and event information

### Key Database Features:
- Unique booking reference generation
- Status tracking (available/booked)
- Timestamp tracking for all records
- Foreign key relationships

## 🎯 Key Features Implemented

✅ Complete authentication system with role-based redirects
✅ Glassmorphism UI design with blur effects
✅ Card-based dashboard with hover animations
✅ Real-time statistics and analytics
✅ Responsive navigation menu
✅ Form validation and error handling
✅ Success/error message notifications
✅ Empty state handling for all lists
✅ Date formatting throughout the app
✅ Professional footer with copyright
✅ On-spot booking confirmation with success modal
✅ SMS notification support for bookings
✅ Quick action cards on devotee dashboard
✅ Visual indicators for booked poojas
✅ Booking history with detailed information
✅ Multi-role support (Admin, Priest, Devotee)
✅ Priest duty assignment system
✅ Festival and announcement management
✅ Donation tracking system
✅ Responsive mobile-first design
✅ Clean, professional UI with proper color contrast

## 📝 Notes

- The application uses pure PHP without any frameworks for educational purposes
- All passwords are hashed using bcrypt
- The system shows all poojas including past ones with booking status
- Sessions are configured with security best practices
- Booked poojas are clearly marked with red "Booked" badges
- SMS notifications require proper SMS gateway configuration
- The system uses null coalescing for safe array access in views
- All user inputs are sanitized before display

## 🙏 Credits

Developed with devotion for the Kovil Management System.

## 📄 License

This project is open-source and available for educational purposes.

---

**Need Help?** Check the troubleshooting section or review the code comments for detailed explanations.
