# Inventory AI — project rules

AI-powered inventory and supply chain system (final-year project). Simple, readable code for a student team. Read this file fully before every task. Progress is tracked in PROGRESS.md.

## Stack (do not change)
- PHP 8 with PDO and prepared statements only, MySQL/MariaDB, HTML/CSS, vanilla JavaScript using fetch() for AJAX.
- Python is allowed only inside `ml/` (LSTM and Random Forest). Claude API is called from PHP in `chatbot/`.
- No frameworks, no Composer, no build tools. Must run on XAMPP (Windows) with default PHP: do not require the mbstring extension (use `clean_str`/`str_cut`).

## Folder rules (prevents code mixing)
- One folder per department: `admin/ warehouse/ procurement/ sales/ manager/`, each with `pages/ ajax/ js/`. Also `auth/` (pages at its root, plus `ajax/ js/`), `chatbot/`, `ml/`, `cron/`.
- Shared code lives only in `core/` (one file per concern) and `core/models/` (one file per entity). Never copy shared logic into a department; move it to `core/models/` and call it.
- Never put one department's code inside another's folder. Admin may use every department's data but its own pages stay in `admin/`.
- One ajax file per action. No file longer than about 150 lines; split if it grows.

## Logging (one log per section)
- Each section logs only to `logs/<section>.log`: auth, admin, warehouse, procurement, sales, manager, ml, chatbot, alerts, database.
- `log_error($section,$msg,$ctx=[])`, `log_info(...)`. `guard()`/`guard_ajax()` already call `init_section($section)` so PHP errors and exceptions reach the right log.
- Wrap every multi-step write in try/catch + transaction, log failures, and return a friendly message. Never show raw errors to users.

## Comments
Only where truly needed. Maximum one line per comment and the comment must fit on one line.

## Existing core API (use it, do not reinvent)
PHP (all via `require __DIR__.'/../../core/bootstrap.php';`):
- `db_query($sql,$p)`, `db_all`, `db_one`, `db_val`, `db_insert`, `db()` (for transactions: `beginTransaction/commit/rollBack`).
- `json_ok($data)` -> `{"ok":true,"data":{...}}`; `json_fail($msg,$code=400)` -> `{"ok":false,"message":"..."}`; `request_input()` reads the JSON body.
- `guard($section,$roles)` at top of every page; `guard_ajax($section,$roles)` at top of every ajax file (checks session, role, CSRF on POST). Check `$_SERVER['REQUEST_METHOD']!=='POST'` -> `json_fail('Invalid request.',405)` in write actions.
- `page_header($title,$activeMenuLabel)` and `page_footer(['dept/js/file.js'])` build the page. Sidebar links live in `core/menu.php` (add each new page there).
- `setting($key,$default)`, `setting_save($k,$v)`, `log_activity($action,$details)`, `valid_email`, `valid_password`, `clean_str($v,$max)`, `str_cut`, `url($path)`, `e($s)` (escape output), `csrf_token()`.
- Existing models: `core/models/product.php` (`product_clean`, `product_set_stock`, `product_stock_status`), `core/models/setting_keys.php`.
JavaScript (`assets/js/common.js`, loaded on every page): `BASE`, `CSRF`, `esc()`, `show_message(msg,'ok'|'error')`, `post_json(url,obj)`, `get_json(url,params)`, `form_data(form)`, `badge(text)`.

## Database conventions
- Schema is `database/schema.sql` (MySQL, InnoDB, utf8mb4). Settings table columns are `setting_key`, `setting_value`. Do not rename existing tables or columns; add new ones with a migration file `database/migrations/NNN_name.sql` and also update schema.sql.
- `stock(product_id,location_id,quantity)` holds balances. `stock_movements.quantity` is signed: positive adds stock, negative removes it. Types: receipt, sale, issue, transfer (two rows with the same reference), adjustment.
- Every stock change = update `stock` + insert `stock_movements` in ONE transaction, using `SELECT ... FOR UPDATE`; never allow negative stock.
- Roles (roles.name): admin, warehouse, procurement, sales, manager. Statuses and enums are lowercase strings.

## Security (every file)
Role check at the top, CSRF on every POST (done by `guard_ajax`), prepared statements, validate input, escape output with `e()` in PHP and `esc()` in JS, `password_hash`, never put secrets in JavaScript. Config files, logs, backups, core and ml are blocked from the web by `.htaccess`.

## UI rules
Plain, clean, responsive, for non-technical users. Reuse classes in `assets/css/main.css` (`card grid kpi row btn badge tabs pane tablewrap pager note empty`); add new shared styles there, not inline. Every list page: filters, pagination, friendly empty state, loading text. Use AJAX; show results with `show_message`.

## STEP PROTOCOL (applies to every /pNN command)
1. Read AGENTS.md and PROGRESS.md. If this step is already ticked or its main files exist, do NOT overwrite: report what exists and stop.
2. Read the existing files the step touches (menu.php, models, a similar page) and copy their style.
3. Build only this step. Do not start the next step.
4. Check your work: run `php -l` on every PHP file you changed and `node --check` on every JS file (skip a tool that is not installed). If a PHP server and database are available, run a quick request test of the new ajax actions and fix failures.
5. Update PROGRESS.md: tick the step and add one line of notes (files created, anything deferred).
6. Finish with: files created/changed, how to test in the browser (numbered steps with expected results), and any decision I must make.

## Demo data
Seed logins (password `Demo@123`): admin@demo.test, warehouse@demo.test, procurement@demo.test, sales@demo.test, manager@demo.test.

## Out of scope unless asked
Frameworks, Docker, multi-tenant support, payment integration, any rewrite of finished steps.
