# 🏨 Room Booking System

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Status](https://img.shields.io/badge/Project-Active-success.svg)

A fully featured hotel room booking application built with **Laravel**, offering complete booking flow, separate customer and admin authentication, reporting system, and Stripe test payment support.

---

## ✨ Features

### 🎯 Core Functionalities
- Room management with full CRUD operations
- Real-time availability checking
- Complete booking workflow
- Customer dashboard and admin panel
- Comprehensive reports with insights
- Clean modern UI and responsive design

### 🔐 Authentication
- Separate login for Admins and Customers
- Password hashing for secure authentication
- Role-based access

### 📊 Reporting Features
- Booking history and status tracking
- Revenue analytics and trends
- Customer activity statistics

---

## 🛠️ Tech Stack
- **Backend:** Laravel 12
- **Frontend:** Blade, Bootstrap/Tailwind
- **Database:** MySQL / MariaDB
- **Payment:** Stripe Test Mode
- **Version Control:** Git & GitHub

---

## 🚀 Live Demo & Repository

| Item | Link |
|------|------|
| GitHub Repo | https://github.com/prabithavaikath/room-booking-system |
| Live Demo | https://prabitha.online/RBS/public/ |

---

## 👤 Default Login Credentials (Demo)

### Customer
| Field | Value |
|-------|-------|
| Email | john@example.com |
| Password | password123 |

### Admin
| Field | Value |
|-------|-------|
| Email | superadmin@hotel.acom |
| Password | admin123 |

---

## 💳 Stripe Test Payments

| Scenario | Card Number |
|---------|-------------|
| Generic Decline | 4000 0000 0000 0002 |
| Insufficient Funds | 4000 0000 0000 9995 |
| Expired Card | 4000 0000 0000 0069 |
| Requires 3D Secure | 4000 0025 0000 3155 |

> Use any future date + any 3-digit CVV.

---

## 📥 Local Installation

Clone the project:
```bash
git clone https://github.com/prabithavaikath/room-booking-system.git
cd room-booking-system
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve