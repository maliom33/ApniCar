# 🚗 ApniCar — Car Rental Management System

ApniCar is a full-stack web-based car rental management system developed to simplify and manage vehicle rental operations through a centralized digital platform. The application integrates frontend, backend, and database technologies to provide customer authentication, vehicle browsing, booking management, invoice generation, and administrative operations.

The project demonstrates practical implementation of full-stack web development, server-side programming, database management, CRUD operations, authentication, session management, and database-driven application workflows.

---

## 🎯 Project Objective

The primary objective of ApniCar is to develop a centralized platform for managing car rental operations efficiently. The system reduces manual management by integrating customers, vehicles, bookings, authentication, and administrative operations into a single database-driven application.

---

## ✨ Key Features

### 👤 Customer Features
- Customer registration and login
- Secure session-based authentication
- Browse available vehicles
- View vehicle information
- Book rental vehicles
- View booking details
- Manage customer profile
- View previous bookings
- Return rented vehicles
- Booking invoice generation

### 🛠️ Admin Features
- Administrator authentication
- Admin dashboard
- Vehicle management
- Customer management
- Booking management
- Database-driven administrative operations
- Manage rental-related information

### 🗄️ Database Features
- MySQL database integration
- CRUD operations
- Customer data management
- Vehicle data management
- Booking data management
- Authentication data management
- SQL database setup and migration scripts

---

## 🏗️ Application Architecture

ApniCar follows a database-driven full-stack web application architecture:

```text
                    ┌─────────────────────┐
                    │       Customer      │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │   Frontend Layer    │
                    │ HTML • CSS • JS     │
                    │ Bootstrap • W3.CSS  │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │   Backend Layer     │
                    │        PHP          │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │   Database Layer    │
                    │       MySQL         │
                    └─────────────────────┘

💡 Project Highlights
Developed a complete full-stack web application.
Implemented frontend and backend integration.
Designed and integrated a relational MySQL database.
Implemented database-driven CRUD operations.
Developed customer and administrator authentication.
Implemented session-based user management.
Developed a complete vehicle booking workflow.
Implemented booking and invoice management.
Created separate customer and administrative functionalities.
Integrated SQL database scripts for project setup and migration.
🔐 Security Note

Sensitive database credentials are not included in this repository.

Developers should create their own local config.php file using the provided:

config.example.php

Never commit passwords, API keys, or other sensitive credentials to a public repository.

🔮 Future Scope

Potential future improvements include:

Online payment gateway integration
Email and SMS booking notifications
GPS-based vehicle tracking
Advanced analytics dashboard
REST API integration
Cloud deployment
Mobile application
Role-based access control
Automated payment and invoice management
Vehicle availability notifications
📌 Project Status

Status: Completed / Academic Project

The project is currently available as a source-code repository and can be executed locally using XAMPP, Apache, and MySQL.

👨‍💻 Developer

Om Mali

Computer Science Engineering Student
Backend & Full-Stack Developer

GitHub: @maliom33

📄 License

This project is intended for educational and portfolio purposes.


### One important change before you save it

Since you already have the repository created, **don't put fake screenshots or fake features in the README**. After you successfully push the code, take screenshots of your actual:

- Home page
- Cars/vehicle page
- Login
- Booking page
- Invoice
- Admin dashboard

Then we'll replace the `_Add ... screenshot here._` lines with the actual images.

Also, because your project is specifically **ApniCar**, I would use this short GitHub repository description:

> **Full-stack car rental management system built with PHP, MySQL, HTML, CSS, JavaScript, Bootstrap, and W3.CSS.**

That will look clean under your repository name.
