## 🌍 Language
[English](#english) | [فارسی](#farsi)
<div align="center">
  <h1>📊 Visitors Analytics Package</h1>
  <h3>پکیج تحلیل بازدیدکنندگان</h3>
</div>

<!-- فارسی -->
<div dir="rtl" id="farsi">

## 📦 نصب
با استفاده از Composer:
```bash
composer require sajjadef98/visitors
```

## ⚙️ راه اندازی اولیه
### 1. ایجاد جدول دیتابیس
```sql
CREATE TABLE `device_info` (
  `ip` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `device_model` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `phone_model` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci
```

### 2. تنظیمات دیتابیس
```php
require __DIR__.'/vendor/autoload.php';

use Visitors\Visitors;

$config = [
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '',
    'db'       => 'your_database',
    'port'     => 3306,
    'prefix'   => '',
    'charset'  => 'utf8mb4',
    'errmode'  => 'exception'
];
```

### 3. مقداردهی اولیه
```php
$url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$visitors = new Visitors(new MysqliDb($config), $url);
```

## 🚀 نحوه استفاده
### ردیابی بازدیدها (در هر صفحه):
```php
$tracker = new Visitors\Insert_User_Data(new MysqliDb($config));
$tracker->GUDI(); // ذخیره خودکار اطلاعات بازدید
```

### نمایش نمودار (در صفحه مدیریت):
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitors->chartApi(); // خروجی JSON برای AJAX
    die();
}

if (isset($_GET)) {
    $visitors->chart(); // نمایش صفحه با نمودار
    die();
}
```

## 🔑 ویژگی‌های کلیدی
- 📊 نمایش نمودارهای تعاملی با Chart.js
- 🌍 شناسایی موقعیت جغرافیایی
- 📱 تشخیص خودکار نوع دستگاه
- 🔒 سیستم امنیتی پیشرفته
- 📈 گزارش‌گیری حرفه‌ای

## 📄 لایسنس
این پروژه تحت لایسنس MIT منتشر شده است.

</div>

<!-- English -->
<div dir="ltr" id="english">

## 📦 Installation
Using Composer:
```bash
composer require sajjadef98/visitors
```

## ⚙️ Initial Setup
### 1. Create Database Table
```sql
CREATE TABLE `device_info` (
  `ip` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `device_model` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `phone_model` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci
```

### 2. Database Configuration
```php
require __DIR__.'/vendor/autoload.php';

use Visitors\Visitors;

$config = [
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '',
    'db'       => 'your_database',
    'port'     => 3306,
    'prefix'   => '',
    'charset'  => 'utf8mb4',
    'errmode'  => 'exception'
];
```

### 3. Initialization
```php
$url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$visitors = new Visitors(new MysqliDb($config), $url);
```

## 🚀 Usage
### Track Visits (on every page):
```php
$tracker = new Visitors\Insert_User_Data(new MysqliDb($config));
$tracker->GUDI(); // Auto-save visit data
```

### Display Charts (in admin panel):
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitors->chartApi(); // JSON output for AJAX
    die();
}

if (isset($_GET)) {
    $visitors->chart(); // Display chart page
    die();
}
```

## 🔑 Key Features
- 📊 Interactive Charts with Chart.js
- 🌍 Geolocation Tracking
- 📱 Automatic Device Detection
- 🔒 Advanced Security System
- 📈 Professional Reporting

## 📄 License
This project is licensed under the MIT License.

</div>

<!-- Common Sections -->
<div align="center">

## 💖 حمایت/Support
[![GitHub Stars](https://img.shields.io/github/stars/sajjadef98/visitors?style=social)](https://github.com/sajjadef98/visitors)

</div>
