# SEO Audit Pro - Complete SEO Analysis Tool

A comprehensive Laravel-based SEO analysis platform that provides multiple SEO tools and features for website optimization.

## 🚀 Features

### Core SEO Tools
- **Site Audit Tool** - Complete website SEO analysis
- **Meta Tag Analyzer** - Analyze and optimize meta tags
- **Keyword Rank Tracker** - Track keyword rankings over time
- **Page Speed Checker** - Website performance analysis using Google PageSpeed Insights
- **Image Alt Text Checker** - Analyze and optimize image alt attributes
- **SERP Preview Tool** - Preview how your pages appear in search results
- **Lead Magnet Builder** - Create and manage lead magnets with email campaigns

### Advanced Features
- **Competitor Analysis** - Analyze competitor websites
- **Bulk Email Invites** - Send bulk invitations for lead magnets
- **PDF Report Generation** - Export analysis results to PDF
- **CSV/Excel Export** - Export data in multiple formats
- **User Authentication** - Secure user accounts and data
- **Real-time Analytics** - Track conversions and performance
- **Email Templates** - Professional email templates for lead magnets

## 🛠 Technology Stack

- **Backend**: Laravel 11.x
- **Frontend**: Alpine.js, Tailwind CSS
- **Database**: SQLite (can be configured for MySQL/PostgreSQL)
- **Email**: SMTP (Gmail, SendGrid, etc.)
- **File Storage**: Laravel Storage (local/cloud)
- **PDF Generation**: DomPDF
- **Charts**: Chart.js
- **Icons**: Font Awesome

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- Node.js and NPM (for asset compilation)
- SQLite/MySQL/PostgreSQL
- SMTP email service (Gmail, SendGrid, etc.)

## 🔧 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/seo-audit-pro.git
cd seo-audit-pro
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (if using)
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# Create database tables
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed
```

### 5. Storage Setup
```bash
# Create storage link
php artisan storage:link
```

### 6. Configure Environment
Edit `.env` file with your settings:

```env
APP_NAME="SEO Audit Pro"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Google PageSpeed API (optional)
GOOGLE_PAGESPEED_API_KEY=your-api-key
```

### 7. Start the Application
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
