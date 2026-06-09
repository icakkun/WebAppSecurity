# Security Testing Guide

Quick reference for demonstrating all security enhancements. Run both versions side by side at:
- **Original (vulnerable):** `http://localhost/WebAppSecurity/Original/index.php`
- **Enhanced (hardened):** `http://localhost/WebAppSecurity/Enhanced/index.php`

---

## Seed Accounts (no registration needed)

| Version | Email | Password | Role |
|---|---|---|---|
| Enhanced | `admin@example.com` | `admin123` | admin |
| Enhanced | `user1@example.com` | `password123` | user |
| Original | `admin_insecure@example.com` | `admin123` | admin |
| Original | `user1_insecure@example.com` | `password123` | user |

---

## 1. Input Validation

### Test A — Weak password (Enhanced register)
**URL:** `http://localhost/WebAppSecurity/Enhanced/index.php?page=register`

| Field | Input |
|---|---|
| Username | `testuser` |
| Email | `test@test.com` |
| Password | `password` |

**Expected:** Real-time indicators turn red (no uppercase, no number, no special character). Form is blocked before submission.

---

### Test B — Invalid username characters (Enhanced register)
**URL:** `http://localhost/WebAppSecurity/Enhanced/index.php?page=register`

| Field | Input |
|---|---|
| Username | `test user!` |

**Expected:** Browser immediately rejects it — username only allows letters, numbers, and underscores.

---

## 2. Authentication

### Test A — SQL Injection bypass (Original)
**URL:** `http://localhost/WebAppSecurity/Original/index.php?page=login`

| Field | Input |
|---|---|
| Email | `' OR 1=1#` |
| Password | `abc` |

**Expected:** Logs in as admin without any valid credentials.
> Note: You may first see a raw SQL error exposing the query — this itself is a vulnerability. Submit again and it logs you in as admin.

**How it works:** The `#` comments out the rest of the query, so the password check is ignored:
```sql
SELECT * FROM users WHERE email = '' OR 1=1#' AND password = 'abc'
```
`OR 1=1` is always true → returns the first user (admin).

---

### Test B — Same injection blocked (Enhanced)
**URL:** `http://localhost/WebAppSecurity/Enhanced/index.php?page=login`

| Field | Input |
|---|---|
| Email | `' OR 1=1#` |
| Password | `abc` |

**Expected:** Blocked at two layers:
1. **Client-side** — HTML5 `type="email"` rejects it instantly (no `@` symbol), never reaches the server
2. **Server-side** — even if the browser check is bypassed, prepared statements safely reject it

---

### Test C — Rate limiting / brute force (Enhanced)
**URL:** `http://localhost/WebAppSecurity/Enhanced/index.php?page=login`

| Field | Input |
|---|---|
| Email | `admin@example.com` |
| Password | `wrongpassword` |

Repeat **5 times** in a row.

**Expected:** "Too many failed login attempts. Please try again after 5 minutes."

> To reset the lockout during demo, run this in MySQL: `DELETE FROM login_attempts;`

---

## 3. Authorization

### Test A — Access admin panel as regular user (Original)
1. Login with `user1_insecure@example.com` / `password123` on Original
2. Visit: `http://localhost/WebAppSecurity/Original/index.php?page=admin`

**Expected:** Admin panel loads with full user list — no access check at all.

---

### Test B — Same blocked on Enhanced
1. Login with `user1@example.com` / `password123` on Enhanced
2. Visit: `http://localhost/WebAppSecurity/Enhanced/index.php?page=admin`

**Expected:** `403 Access Denied — You do not have permission.`

---

## 4. XSS (Cross-Site Scripting)

### Test A — Script injection (Original)
1. Login as admin on Original → go to **Add Product**

| Field | Input |
|---|---|
| Name | `<script>alert("XSS")</script>` |
| Description | `test` |
| Price | `10` |
| Quantity | `1` |

2. Submit → go to the **Products** list

**Expected:** Alert popup fires saying "XSS". The product name appears blank because the browser executed it as code instead of displaying it.

---

### Test B — Same input safe (Enhanced)
1. Login as admin on Enhanced → repeat exact same steps above

**Expected:** No popup. Product name shows as literal text `<script>alert("XSS")</script>` — the script is escaped and neutralised.

> In a real attack the script would steal session cookies or redirect users to a fake login page, not just show an alert.

---

## 5. CSRF (Cross-Site Request Forgery)

### Test A — Delete via GET request (Original)
1. Login as admin on Original first
2. Paste this directly into the browser address bar:

```
http://localhost/WebAppSecurity/Original/index.php?page=products&action=delete&id=2
```

**Expected:** Wireless Mouse deleted instantly — no confirmation, no token required.

> In a real attack, this URL would be hidden in an email or malicious website. The admin just needs to *visit* the link while logged in and the deletion happens silently.

---

### Test B — Same blocked on Enhanced
1. Login as admin on Enhanced first
2. Paste this directly into the browser address bar:

```
http://localhost/WebAppSecurity/Enhanced/index.php?page=products&action=delete&id=2
```

**Expected:** Error — only POST requests with a valid CSRF token are accepted. GET-based deletion is rejected.

---

## 6. File Upload Security

### Test A — PHP file upload (Original)
1. Open Notepad → type `<?php echo "hacked"; ?>` → save as `test.php`
2. Login as admin on Original → go to **Add Product**
3. Fill in any name/price/quantity → upload `test.php` as the image → submit

**Expected:** File accepted and saved. You can then visit:
```
http://localhost/WebAppSecurity/Original/uploads/test.php
```
The PHP code executes on the server — this is a **web shell**, giving an attacker full server control.

---

### Test B — Same blocked on Enhanced
1. Same steps on Enhanced — try uploading `test.php` as the image

**Expected:** "Invalid file type" — rejected by both extension whitelist and MIME type check. File is never saved.

---

### Test C — Oversized file (Enhanced)
1. Find any image larger than **2MB**
2. Login as admin on Enhanced → go to **Add Product** → try uploading it

**Expected:** "File size exceeds maximum allowed size" — rejected.
