# 🏥 Mabini Health Center Management System

A simple and elegant health center management system built with Laravel.

## ✨ Features

- **Dashboard** - Overview of daily operations and statistics
- **Patient Management** - Register and manage patient records
- **Queue System** - Digital queue management with priority handling
- **Medicine Inventory** - Track medicines, stock levels, and expiry dates
- **Responsive Design** - Works on desktop, tablet, and mobile devices
- **Custom Styling** - Unique design with custom CSS (no frameworks)

## 🚀 Quick Start

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL (optional for now - uses sample data)

### Installation

1. **Clone or download the project**
   ```bash
   # If using git
   git clone <repository-url>
   cd mabini-health-center
   
   # Or extract the downloaded files
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your database**
   - Edit `.env` and set your MySQL credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=mabini_health_center
     DB_USERNAME=root
     DB_PASSWORD=your_mysql_password
     ```
   - Create the database in MySQL:
     ```sql
     CREATE DATABASE mabini_health_center;
     ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

7. **Open your browser**
   ```
   http://localhost:8000
   ```

## 📱 Usage

### Dashboard
- View daily statistics and recent activity
- Quick access to main functions
- Real-time updates and alerts

### Patient Management
- Register new patients
- View patient records and medical history
- Search and filter patients

### Queue Management
- Add patients to digital queue
- Set priority levels (Normal, Urgent, Emergency)
- Track queue status and waiting times

### Medicine Inventory
- Add new medicines to inventory
- Monitor stock levels and expiry dates
- Get alerts for low stock and expired medicines

## 🎨 Design Features

- **Modern UI** - Clean and professional interface
- **Gradient Colors** - Beautiful color schemes throughout
- **Responsive Layout** - Adapts to all screen sizes
- **Custom Icons** - Font Awesome icons for better UX
- **Smooth Animations** - Subtle transitions and hover effects

## 🛠️ Technical Details

- **Framework**: Laravel 10
- **Frontend**: Blade templates with custom CSS
- **Icons**: Font Awesome 6
- **No Database Required**: Uses sample data for easy setup
- **Mobile Responsive**: Works on all devices

## 📁 Project Structure

```
mabini-health-center/
├── app/Http/Controllers/     # Application controllers
├── resources/views/          # Blade templates
├── public/css/              # Custom stylesheets
├── public/js/               # JavaScript files
├── routes/web.php           # Web routes
└── README.md               # This file
```

## 🔧 Customization

### Adding Database Support
To add database functionality:
1. Configure your database in `.env`
2. Create migrations: `php artisan make:migration create_patients_table`
3. Update controllers to use Eloquent models
4. Run migrations: `php artisan migrate`

### Styling
- Edit `public/css/app.css` to customize the design
- All styles are custom-written (no Tailwind or Bootstrap)
- Color scheme can be easily modified in CSS variables

### Adding Features
- Create new controllers: `php artisan make:controller FeatureController`
- Add routes in `routes/web.php`
- Create corresponding Blade views

## 🌟 Key Features Explained

### Unique Design Elements
- **Gradient Sidebar** - Beautiful purple gradient navigation
- **Card-based Layout** - Modern card design for content sections
- **Status Indicators** - Color-coded status badges and alerts
- **Interactive Elements** - Hover effects and smooth transitions

### User Experience
- **Intuitive Navigation** - Clear menu structure
- **Quick Actions** - Easy access to common tasks
- **Visual Feedback** - Loading states and success messages
- **Mobile-First** - Responsive design for all devices

## 📞 Support

This is a simple demonstration system. For production use:
- Add proper authentication
- Implement database models
- Add form validation
- Include error handling
- Add security measures

## 📄 License

This project is open-source and available under the MIT License.

---

**Mabini Health Center Management System** - Simple, elegant, and ready to use! 🎉
