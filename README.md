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
