# PHP User Authentication System

A simple PHP-based user authentication system with registration, login, logout, and profile management.

---

## 🚀 Features
- User registration with validation
- Secure login/logout
- Profile display
- Database connection using `db.php`

---

## 🛠️ Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/YOUR-USERNAME/user-auth-php.git
   cd user-auth-php
   ```

2. **Database Setup**
   - Create a MySQL database (e.g., `auth_system`)
   - Import the SQL schema (if you have one)
   - Update `db.php` with your credentials:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "user_portal_db";
     ```

3. **Run Locally**
   - Place the folder in your web server directory (e.g., `htdocs` for XAMPP)
   - Start Apache and MySQL
   - Open in browser:  
     👉 `http://localhost/user_portal/login.php`

---

## 📁 File Structure
```
db.php          # Database connection
login.php       # Login page
logout.php      # Logout script
profile.php     # User profile
register.php    # Registration page
```

---

## 🧑‍💻 Technologies Used
- PHP
- MySQL
- HTML/CSS
- (Optional) Bootstrap for styling

---

## 📜 License
This project is open-source and available under the [MIT License](LICENSE).
# user_portal
