# Christmas Music App with Tree Decoration - Back-End

### 🎄 Secure and Functional CMS Back-End

A back-end system built with PHP and MySQL to manage user interactions, data integrity, and advanced CMS functionalities.

---

## 🎯 Features and Objectives

- **Secure CMS Back-End**: Built using PHP and MySQL.
- **Advanced OOP**: Implements Object-Oriented Programming for scalable and maintainable code.
- **Authentication and Role Management**: Includes secure login, user roles, and session handling.
- **File Management**: Handles secure uploads and media asset storage.
- **Database Interaction**: Manages CRUD operations while maintaining data integrity.
- **Error Handling and Logging**: Implements robust mechanisms to log errors and handle unexpected issues gracefully.
- **Audit Trails**: Records user activities for transparency.
- **Web Security**: Adheres to best practices like input sanitization and HTTPS support.

---

## 📦 Installation Instructions

1. **Clone the Repository**
   ```
   git clone https://github.com/yourusername/music-points-tree-backend.git
Set Up the Database

Import the provided database.sql file into your MySQL server:


mysql -u your-username -p your-database-name < database.sql


Ensure the following tables are included:
register: Stores user data (e.g., email, hashed passwords, roles).
music: Contains metadata for music tracks.
points: Tracks points earned by users.
decoration: Holds tree decoration data.
deco_itenms: Holds user tree decoration data.
recent_music:Stores which song user listened.

Configure the PHP Application

Open the config.php file and update the following database credentials:
php

define('DB_HOST', 'your-database-host');
define('DB_USER', 'your-database-username');
define('DB_PASS', 'your-database-password');
define('DB_NAME', 'your-database-name');
Run the Server

Use the built-in PHP server - **
php -S localhost:8000
Alternatively, deploy the application to your preferred hosting provider.

##⚙️ Functionality Overview
- ** User Management:
Role-based access control (Admin, User).
- ** Secure password handling using PHP's password_hash.
- ** File Handling
Media uploads with size/type validation.
- ** Securely store paths in the database.
- ** Database Management
CRUD operations for users, music, points, and decorations.
Enforces foreign key constraints to maintain data relationships.
- ** Error Handling
Logs errors to a secure server-side file.
Provides user-friendly error messages.
- ** Audit Trails
Tracks user activities like login, point usage, and decoration updates.
- ** Security Measures
Input sanitization to prevent SQL injection.
HTTPS enforcement for secure communication.

##📚 Technology Stack
Backend: PHP
Database: MySQL
Frontend: React (integration-ready)
Authentication: PHP Sessions

##📑 Database Design
Ensure your MySQL server reflects the schema in the database.sql file.
