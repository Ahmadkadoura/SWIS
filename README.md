# Swis Project - Warehouse Management System

![Laravel Logo](logo.png)

[![Build Status](https://img.shields.io/travis/gothinkster/laravel-realworld-example-app/master.svg)](https://travis-ci.org/gothinkster/laravel-realworld-example-app)  
[![GitHub stars](https://img.shields.io/github/stars/Ahmadkadoura/Swis-Project.svg)](https://github.com/Ahmadkadoura/Swis-Project/stargazers)  
[![GitHub license](https://img.shields.io/github/license/Ahmadkadoura/Swis-Project.svg)](https://raw.githubusercontent.com/Ahmadkadoura/Swis-Project/master/LICENSE)

---

> **Swis Project** is a Warehouse Management System (WMS) developed for the Red Crescent organization. It streamlines warehouse operations by implementing efficient CRUD operations, authentication, and advanced system patterns. The system adheres to modern API development standards, ensuring scalability and maintainability.

---

## Features

### **Warehouse Operations**
- Manage inventory with CRUD operations for products, categories, and stock levels.
- Track incoming and outgoing shipments with detailed records.

### **Authentication and Security**
- Role-based access control (RBAC) for administrators and staff.
- Secure user authentication with Laravel's built-in features.

### **Advanced Design Patterns**
- Built using **Repository** and **Trait** design patterns for modular and clean architecture.
- Follows best practices for API development.

### **Reporting and Insights**
- Generate reports on stock levels, shipment history, and product movement.
- Dashboard with real-time analytics for warehouse performance.

---

## Getting Started

### Prerequisites

Ensure you have the following installed on your system:
- PHP >= 8.0
- Composer
- Laravel Framework
- A database system (e.g., MySQL, PostgreSQL)

For more details, refer to the [Laravel Official Documentation](https://laravel.com/docs).

---

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Ahmadkadoura/SWIS 
Switch to the repo folder

    cd SWIS

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate


Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000
**TL;DR command list**

    git clone https://github.com/Ahmadkadoura/SWIS
    cd SWIS
    composer install
    cp .env.example .env
    php artisan key:generate

**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    php artisan serve
## Database seeding

**Populate the database with seed data with relationships which includes users, articles, comments, tags, favorites and follows. This can help you to quickly start testing the api or couple a frontend and start using it with ready content.**

Run the database seeder and you're done

    php artisan db:seed
    
