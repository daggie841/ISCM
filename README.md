# Inventory AI (PHP + MySQL)

## Setup (XAMPP)
1. Copy this folder to `htdocs/inventory-ai`. Start Apache and MySQL.
2. In phpMyAdmin (or the mysql client) import `database/schema.sql`, then `database/seed.sql`.
3. Edit `config/db.php` if your MySQL user or password is not root with no password.
4. Open http://localhost/inventory-ai/

If the folder name differs, change `base_url` in `config/app.php`.

## Demo logins (password for all: Demo@123)
admin@demo.test, warehouse@demo.test, procurement@demo.test, sales@demo.test, manager@demo.test

## Folders
- `core/` shared code (db, logger, auth, layout). `core/models/` one file per entity.
- `auth/`, `admin/`, `warehouse/`, `procurement/`, `sales/`, `manager/` each have pages/, ajax/, js/.
- `logs/` one error log per section (auth.log, admin.log, database.log ...). View them in Admin > Logs.

## Built so far
Setup, auth, and the System Administrator department (users, products, settings, logs). Other dashboards are placeholders.
