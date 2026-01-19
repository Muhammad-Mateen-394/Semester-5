# Core Banking System (CBS) - PHP + MySQL + Bootstrap

## Requirements
- PHP 7.4+ (with MySQLi)
- MySQL or MariaDB
- Apache / Nginx (XAMPP, WAMP recommended for local)
- Bootstrap 5 (assets/bootstrap folder or use CDN)

## Install
1. Place `cbs` folder in your webserver root (e.g., `htdocs/cbs`).
2. Import SQL schema — run `CREATE DATABASE cbs` and run the SQL in the provided file.
3. Edit `config/db.php` to set DB credentials.
4. Run browser: `http://localhost/cbs/auth/install_admin.php` to create default admin (username: `admin`, password: `admin123`). Delete this file afterwards.
5. Login at `http://localhost/cbs/auth/login.php`.
6. Use dashboard to add customers, open accounts and perform transactions.

## Notes
- Passwords are hashed with `password_hash`.
- All transactions use MySQL transactions (`begin_transaction`, `commit`, `rollback`).
- Audit logs are in the `auditlog` table.
