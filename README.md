# Product Inventory Management System - Security Enhancement Report

## Group Members

| No. | Name | Matric No. |
|-----|------|------------|
| 1 | EZMIR NA Q SHARIAL BIN SUHAIZI | 2229669 |
| 2 | ISYRAQ HAZIQ BIN MOHD RIDZA | 2225321 |
| 3 | MOHAMAD NUR HAKIMI BIN ASMADI | 2213091 |
| 4 | AMMAR REDZA BIN MOHD RADZI | 2226293 |            |
| 5 | LUQMAN HAKIM BIN MUHAMMAD SUKRI | 2225438 |            |


---

## Title of Web Application

**Product Inventory Management System**

---

## Introduction

The Product Inventory Management System is a web-based application developed using PHP with a Model-View-Controller (MVC) architecture and MySQL database. The application allows users to register, log in, and perform CRUD (Create, Read, Update, Delete) operations on products in an inventory. It also includes an admin panel for user management.

The original application was developed during the Web Application Development course (INFO 3305) with a focus on functionality. However, the application contained several common security vulnerabilities, including SQL injection, Cross-Site Scripting (XSS), Cross-Site Request Forgery (CSRF), weak authentication, missing authorization, and insecure file handling.

This report documents the security enhancements applied to harden the application against these vulnerabilities, following best practices and principles learned in the Web Application Security course.

---

## Objectives of the Enhancements

1. **Input Validation** – Implement comprehensive client-side and server-side input validation to prevent malicious data from being processed.
2. **Authentication** – Strengthen user authentication by implementing secure password hashing, session management, and brute-force protection.
3. **Authorization** – Enforce role-based access control (RBAC) to restrict access to resources based on user roles.
4. **XSS and CSRF Prevention** – Protect against Cross-Site Scripting and Cross-Site Request Forgery attacks through output encoding and token-based validation.
5. **Database Security** – Prevent SQL injection attacks by using prepared statements and parameterized queries with PDO.
6. **File Security** – Secure file uploads and server configuration to prevent unauthorized access and malicious file execution.

---

## Web Application Security Enhancements

### i. Input Validation

Input validation ensures that all user-supplied data is checked and sanitized before being processed by the application. We implemented both **client-side** and **server-side** validation.

#### Client-Side Validation

HTML5 validation attributes are used on all form inputs to provide immediate feedback to users.

**Original (No validation):**
```html
<!-- Original/Views/register.php -->
<input type="text" name="username">
<input type="text" name="email">
<input type="password" name="password">
```

**Enhanced (HTML5 + JavaScript validation):**
```html
<!-- Enhanced/Views/register.php -->
<input type="text" id="username" name="username" required 
       minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+"
       title="Only letters, numbers, and underscores">
<input type="email" id="email" name="email" required>
<input type="password" id="password" name="password" required minlength="8">
```

Additionally, JavaScript validation provides real-time password strength feedback:

```javascript
// Enhanced/Views/register.php - Real-time password validation
passwordInput.addEventListener('input', function() {
    var val = this.value;
    updateReq('req-length', val.length >= 8);
    updateReq('req-upper', /[A-Z]/.test(val));
    updateReq('req-lower', /[a-z]/.test(val));
    updateReq('req-number', /[0-9]/.test(val));
    updateReq('req-special', /[!@#$%^&*(),.?":{}|<>]/.test(val));
});
```

#### Server-Side Validation

A dedicated `InputValidator` class provides comprehensive server-side validation:

```php
// Enhanced/Middleware/InputValidator.php
class InputValidator {
    private $errors = [];

    public function required($field, $value, $label = null) {
        $label = $label ?: $field;
        if (empty(trim($value))) {
            $this->errors[$field] = "$label is required.";
        }
        return $this;
    }

    public function email($field, $value) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Please enter a valid email address.";
        }
        return $this;
    }

    public function passwordStrength($field, $value) {
        if (strlen($value) < PASSWORD_MIN_LENGTH) {
            $this->errors[$field] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters.";
            return $this;
        }
        if (!preg_match('/[A-Z]/', $value)) {
            $this->errors[$field] = "Password must contain at least one uppercase letter.";
        }
        // ... additional checks for lowercase, number, special character
        return $this;
    }

    public function passes() {
        return empty($this->errors);
    }
}
```

**Usage in controllers:**

```php
// Enhanced/Controllers/AuthController.php
$validator = new InputValidator();
$validator->required('username', $username, 'Username')
          ->minLength('username', $username, 3, 'Username')
          ->maxLength('username', $username, 50, 'Username')
          ->username('username', $username)
          ->required('email', $email, 'Email')
          ->email('email', $email)
          ->required('password', $password, 'Password')
          ->passwordStrength('password', $password)
          ->matches('confirm_password', $password, $confirmPassword, 'Passwords');

if (!$validator->passes()) {
    $errors = $validator->getErrors();
    // Display errors to user
}
```

**Input elements validated:**
| Input Element | Client-Side | Server-Side | Technique |
|--------------|-------------|-------------|----------|
| Username | `required`, `minlength`, `maxlength`, `pattern` | `required()`, `minLength()`, `maxLength()`, `username()` | HTML5 attributes + regex |
| Email | `type="email"`, `required` | `filter_var(FILTER_VALIDATE_EMAIL)` | HTML5 type + PHP filter |
| Password | `required`, `minlength` | `passwordStrength()` with regex checks | JS real-time + PHP regex |
| Product Name | `required`, `maxlength` | `required()`, `maxLength()` | HTML5 + PHP validation |
| Price | `type="number"`, `min`, `step` | `numeric()`, `positive()` | HTML5 number + PHP filter |
| Quantity | `type="number"`, `min`, `step` | `integer()`, `positive()` | HTML5 number + PHP filter |
| File Upload | `accept` attribute | MIME type, extension, size check | HTML accept + PHP finfo |
| Search | `maxlength` | `htmlspecialchars()`, prepared statements | Encoding + parameterized query |

---

### ii. Authentication

Authentication enhancements ensure that user identity verification is robust and resistant to common attacks.

#### Password Hashing

**Original (Plain text storage):**
```php
// Original/Models/User.php - INSECURE: Plain text password
public function register($username, $email, $password) {
    $sql = "INSERT INTO users (username, email, password) 
            VALUES ('$username', '$email', '$password')";
    $this->db->query($sql);
}

public function login($email, $password) {
    // INSECURE: Plain text comparison
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    return $this->db->fetchOne($sql);
}
```

**Enhanced (Bcrypt hashing):**
```php
// Enhanced/Models/User.php - SECURE: Bcrypt password hashing
public function register($username, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $sql = "INSERT INTO users (username, email, password, role) 
            VALUES (:username, :email, :password, 'user')";
    $this->db->execute($sql, [
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);
}

public function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}
```

#### Secure Session Management

**Original (Basic session):**
```php
// Original/index.php
session_start();
$_SESSION['user_id'] = $user['id'];
```

**Enhanced (Hardened session):**
```php
// Enhanced/index.php - Secure session configuration
ini_set('session.cookie_httponly', 1);     // Prevent JS access
ini_set('session.cookie_secure', 0);       // Set 1 for HTTPS
ini_set('session.use_only_cookies', 1);    // No session ID in URL
ini_set('session.cookie_samesite', 'Strict'); // SameSite cookie
ini_set('display_errors', 0);              // Hide errors
session_name('SECURE_INVENTORY_SESSION');
session_start();

// On successful login - regenerate session ID
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['last_activity'] = time();
$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
```

#### Rate Limiting (Brute-Force Protection)

```php
// Enhanced/Middleware/RateLimiter.php
class RateLimiter {
    public static function isRateLimited($email) {
        $db = Database::getInstance();
        $cutoff = date('Y-m-d H:i:s', time() - LOGIN_LOCKOUT_TIME);
        $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                WHERE (email = :email OR ip_address = :ip) 
                AND attempted_at > :cutoff";
        $result = $db->fetchOne($sql, [
            ':email' => $email,
            ':ip' => self::getClientIP(),
            ':cutoff' => $cutoff
        ]);
        return ($result['attempts'] >= MAX_LOGIN_ATTEMPTS); // Default: 5 attempts
    }

    public static function recordAttempt($email) {
        $db = Database::getInstance();
        $sql = "INSERT INTO login_attempts (email, ip_address) VALUES (:email, :ip)";
        $db->execute($sql, [':email' => $email, ':ip' => self::getClientIP()]);
    }
}
```

**Authentication methods summary:**
| Method | Original | Enhanced |
|--------|----------|----------|
| Password Storage | Plain text | `password_hash()` with `PASSWORD_BCRYPT` (cost 12) |
| Password Verification | SQL string comparison | `password_verify()` |
| Session Configuration | Default `session_start()` | HttpOnly, SameSite=Strict, custom name |
| Session Fixation | No protection | `session_regenerate_id(true)` on login |
| Session Timeout | None | 30-minute inactivity timeout |
| Login Attempts | Unlimited | Max 5 attempts per 15 minutes |
| Error Messages | Specific errors | Generic "Invalid email or password" |
| Session Destruction | `session_destroy()` only | Full cleanup: unset, cookie removal, destroy |

---

### iii. Authorization

Authorization ensures users can only access resources they are permitted to use.

#### Role-Based Access Control (RBAC)

**Original (No authorization):**
```php
// Original/Controllers/AdminController.php - INSECURE: No check
public function index() {
    $users = $this->userModel->getAllUsers();
    require_once __DIR__ . '/../Views/admin/users.php';
}
```

```html
<!-- Original/Views/layout.php - Admin link visible to ALL users -->
<a href="index.php?page=admin">Admin Panel</a>
```

**Enhanced (Middleware-based authorization):**
```php
// Enhanced/Middleware/AuthMiddleware.php
class AuthMiddleware {
    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
                session_unset();
                session_destroy();
                header('Location: index.php?page=login&timeout=1');
                exit;
            }
        }
        $_SESSION['last_activity'] = time();
    }

    public static function requireAdmin() {
        self::requireLogin();
        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die('Access Denied: You do not have permission.');
        }
    }

    public static function requireOwnerOrAdmin($resourceUserId) {
        self::requireLogin();
        if ($_SESSION['user_id'] != $resourceUserId && $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die('Access Denied: You cannot modify this resource.');
        }
    }
}
```

```php
// Enhanced/Controllers/AdminController.php - SECURE: Admin only
public function index() {
    AuthMiddleware::requireAdmin();
    $users = $this->userModel->getAllUsers();
    require_once __DIR__ . '/../Views/admin/users.php';
}
```

```html
<!-- Enhanced/Views/layout.php - Admin link only for admins -->
<?php if ($role === 'admin'): ?>
    <a href="index.php?page=admin">Admin Panel</a>
<?php endif; ?>
```

#### Resource Ownership

```php
// Enhanced/Controllers/ProductController.php - Ownership check
public function edit($id) {
    AuthMiddleware::requireLogin();
    $product = $this->productModel->getProductById($id);
    AuthMiddleware::requireOwnerOrAdmin($product['user_id']);
    require_once __DIR__ . '/../Views/products/edit.php';
}
```

**Authorization methods summary:**
| Aspect | Original | Enhanced |
|--------|----------|----------|
| Login Required | No check on protected routes | `AuthMiddleware::requireLogin()` |
| Admin Access | No restriction | `AuthMiddleware::requireAdmin()` |
| Resource Ownership | None | `AuthMiddleware::requireOwnerOrAdmin()` |
| Admin UI Visibility | Visible to all | Conditionally rendered |
| Self-Deletion | Possible | Prevented in AdminController |

---

### iv. XSS and CSRF Prevention

#### Cross-Site Scripting (XSS) Prevention

**Original (Raw output - vulnerable):**
```php
<!-- Original/Views/products/index.php -->
<td><?php echo $product['name']; ?></td>
<input type="text" name="search" value="<?php echo $_GET['search']; ?>">
<span>Welcome, <?php echo $_SESSION['username']; ?></span>
```

**Enhanced (Output encoding):**
```php
<!-- Enhanced/Views/products/index.php -->
<td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
<input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8'); ?>">
<span>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></span>
```

**Enhanced (Content Security Policy headers):**
```php
// Enhanced/Views/layout.php - Security headers
header('Content-Security-Policy: default-src \'self\'; script-src \'self\'');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
```

#### Cross-Site Request Forgery (CSRF) Prevention

**Original (No CSRF protection):**
```html
<!-- Original/Views/login.php -->
<form method="POST" action="index.php?page=login">
    <input type="text" name="email">
    <input type="password" name="password">
    <button type="submit">Login</button>
</form>

<!-- Original: Delete via GET request -->
<a href="index.php?page=products&action=delete&id=5">Delete</a>
```

**Enhanced (CSRF token validation):**
```php
// Enhanced/Middleware/CSRFMiddleware.php
class CSRFMiddleware {
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' 
               . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validateToken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
                self::rejectRequest();
            }
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                self::rejectRequest();
            }
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
```

```html
<!-- Enhanced/Views/login.php - CSRF token in form -->
<form method="POST" action="index.php?page=login">
    <?php echo CSRFMiddleware::getTokenField(); ?>
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>

<!-- Enhanced: Delete via POST with CSRF -->
<form method="POST" action="index.php?page=products&action=delete&id=5">
    <?php echo CSRFMiddleware::getTokenField(); ?>
    <button type="submit">Delete</button>
</form>
```

**XSS and CSRF Prevention summary:**
| Method | Original | Enhanced |
|--------|----------|----------|
| Output Encoding | Raw `echo` | `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` |
| CSRF Tokens | None | Per-session token with `random_bytes(32)` |
| Token Validation | None | `hash_equals()` comparison |
| Delete Actions | GET request | POST request with CSRF token |
| CSP Headers | None | Strict Content-Security-Policy |
| Cookie Flags | Default | HttpOnly, SameSite=Strict |
| X-Frame-Options | None | DENY |
| X-XSS-Protection | None | 1; mode=block |

---

### v. Database Security Principles

#### SQL Injection Prevention

**Original (String concatenation - vulnerable):**
```php
// Original/Models/User.php - VULNERABLE to SQL Injection
public function login($email, $password) {
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    return $this->db->fetchOne($sql);
}

// Original/Models/Product.php - VULNERABLE
public function searchProducts($keyword) {
    $sql = "SELECT * FROM products WHERE name LIKE '%$keyword%'";
    return $this->db->fetchAll($sql);
}
```

> An attacker could input `' OR '1'='1` as email and password to bypass login, or inject malicious SQL through the search field.

**Enhanced (Prepared statements with PDO):**
```php
// Enhanced/Models/User.php - SECURE: Prepared statements
public function findByEmail($email) {
    $sql = "SELECT * FROM users WHERE email = :email";
    return $this->db->fetchOne($sql, [':email' => $email]);
}

// Enhanced/Models/Product.php - SECURE: Parameterized search
public function searchProducts($keyword) {
    $sql = "SELECT products.*, users.username FROM products 
            LEFT JOIN users ON products.user_id = users.id 
            WHERE products.name LIKE :keyword";
    return $this->db->fetchAll($sql, [':keyword' => '%' . $keyword . '%']);
}
```

#### Database Connection Security

**Original (Insecure connection):**
```php
// Original/config/database.php - Root user, error exposed
$conn = mysqli_connect('localhost', 'root', '', 'inventory_db');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
```

**Enhanced (Secure PDO connection):**
```php
// Enhanced/Models/Database.php - Secure connection
class Database {
    private function __construct() {
        try {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            die('A database error occurred. Please try again later.');
        }
    }
}
```

**Database security summary:**
| Aspect | Original | Enhanced |
|--------|----------|----------|
| Driver | `mysqli` | `PDO` |
| Queries | String concatenation | Prepared statements with bound parameters |
| DB User | `root` (full privileges) | Dedicated user with limited privileges |
| Error Handling | `die(mysqli_error())` | `error_log()` + generic user message |
| Charset | Not specified | `utf8mb4` |
| Emulated Prepares | N/A | Disabled (`ATTR_EMULATE_PREPARES => false`) |
| Connection Pattern | Multiple instances | Singleton pattern |

---

### vi. File Security Principles

#### File Upload Validation

**Original (No validation):**
```php
// Original/Controllers/ProductController.php - INSECURE
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $uploadDir = __DIR__ . '/../uploads/';
    $image = $_FILES['image']['name']; // Original filename used
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
}
```

**Enhanced (Comprehensive validation):**
```php
// Enhanced/Controllers/ProductController.php - SECURE
private function handleFileUpload($file) {
    // Check file size (max 2MB)
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return false;
    }

    // Check file extension (whitelist)
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) { // ['jpg','jpeg','png','gif']
        return false;
    }

    // Check actual MIME type (not just extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
        return false;
    }

    // Generate random filename to prevent path traversal
    $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
    
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Restrictive permissions
    }

    move_uploaded_file($file['tmp_name'], $uploadDir . $newFilename);
    return $newFilename;
}
```

#### Web Server Configuration

**Original (.htaccess - minimal):**
```apache
# Original/.htaccess
RewriteEngine On
RewriteBase /
# No security configurations
```

**Enhanced (.htaccess - hardened):**
```apache
# Enhanced/.htaccess
# Disable directory listing
Options -Indexes

# Disable server signature
ServerSignature Off

# Block access to sensitive directories
<IfModule mod_rewrite.c>
    RewriteRule ^config/ - [F,L]
    RewriteRule ^Middleware/ - [F,L]
    RewriteRule ^Models/ - [F,L]
    RewriteRule ^Controllers/ - [F,L]
</IfModule>

# Block hidden files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent PHP execution in uploads
<IfModule mod_rewrite.c>
    RewriteRule ^uploads/.*\.php$ - [F,L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "DENY"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# PHP security settings
php_flag display_errors Off
php_flag log_errors On
php_value upload_max_filesize 2M
php_value post_max_size 2M
```

**Additional uploads directory protection:**
```apache
# Enhanced/uploads/.htaccess
<FilesMatch "\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>
php_flag engine off
```

**File security summary:**
| Aspect | Original | Enhanced |
|--------|----------|----------|
| File Type Check | None | Extension whitelist + MIME type verification |
| File Size Limit | None | 2MB maximum |
| Filename | Original name used | Random hex name (`bin2hex(random_bytes(16))`) |
| Directory Listing | Enabled | `Options -Indexes` |
| PHP in Uploads | Possible | Blocked via `.htaccess` |
| Config File Access | Accessible | Blocked via rewrite rules |
| Error Display | Visible | `display_errors Off`, logged only |
| Directory Permissions | 0777 | 0755 |
| Server Signature | Visible | `ServerSignature Off` |

---

## Project Structure

```
├── README.md                    ← This report
├── database/
│   └── schema.sql               ← Database schema
├── Original/                    ← Before Enhancement
│   ├── config/
│   │   └── database.php
│   ├── Models/
│   │   ├── Database.php
│   │   ├── User.php
│   │   └── Product.php
│   ├── Views/
│   │   ├── layout.php
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── dashboard.php
│   │   ├── products/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── show.php
│   │   └── admin/
│   │       └── users.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   └── AdminController.php
│   ├── uploads/
│   ├── index.php
│   └── .htaccess
│
└── Enhanced/                    ← After Enhancement
    ├── config/
    │   ├── database.php
    │   └── security.php
    ├── Models/
    │   ├── Database.php
    │   ├── User.php
    │   └── Product.php
    ├── Views/
    │   ├── layout.php
    │   ├── login.php
    │   ├── register.php
    │   ├── dashboard.php
    │   ├── products/
    │   │   ├── index.php
    │   │   ├── create.php
    │   │   ├── edit.php
    │   │   └── show.php
    │   └── admin/
    │       └── users.php
    ├── Controllers/
    │   ├── AuthController.php
    │   ├── ProductController.php
    │   └── AdminController.php
    ├── Middleware/
    │   ├── AuthMiddleware.php
    │   ├── CSRFMiddleware.php
    │   ├── RateLimiter.php
    │   └── InputValidator.php
    ├── uploads/
    ├── index.php
    └── .htaccess
```

---

## References

1. OWASP Foundation. (2021). *OWASP Top Ten Web Application Security Risks*. https://owasp.org/www-project-top-ten/
2. OWASP Foundation. (2023). *OWASP Cheat Sheet Series - Input Validation*. https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html
3. OWASP Foundation. (2023). *OWASP Cheat Sheet Series - Authentication*. https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html
4. OWASP Foundation. (2023). *OWASP Cheat Sheet Series - Authorization*. https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html
5. OWASP Foundation. (2023). *OWASP Cheat Sheet Series - Cross-Site Request Forgery Prevention*. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
6. OWASP Foundation. (2023). *OWASP Cheat Sheet Series - SQL Injection Prevention*. https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html
7. OWASP Foundation. (2023). *OWASP Cheat Sheet Series - File Upload*. https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html
8. PHP Documentation. (2023). *Password Hashing*. https://www.php.net/manual/en/function.password-hash.php
9. PHP Documentation. (2023). *PDO - PHP Data Objects*. https://www.php.net/manual/en/book.pdo.php
10. PHP Documentation. (2023). *Session Security*. https://www.php.net/manual/en/session.security.php
11. Mozilla Developer Network. (2023). *Content Security Policy (CSP)*. https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
12. Mozilla Developer Network. (2023). *HTTP Headers - Security*. https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers
