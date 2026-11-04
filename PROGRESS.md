# Progress

Project: AI-powered inventory & supply chain system (XAMPP).
Rule: one step at a time. The next step is the first unticked box.

## Checklist

- [x] **p01 foundation** — Folders, config and core. `config/`, `core/*`, `assets/`, department folders and `.htaccess` blocks exist.
- [x] **p02 database** — MySQL schema and seed data. 24 tables in `database/schema.sql`; `database/seed.sql` has 5 roles/users and 30 products. Verified against the running database (29 products present; one seed row was removed earlier).
- [x] **p03 auth** — Login, account request, reset and forced password change. `auth/` pages, `auth/ajax/*`, `auth/js/auth.js` and placeholder dashboards exist.
- [x] **p04 admin-dashboard** — Admin shell and dashboard. `admin/pages/dashboard.php` + `admin/ajax/dashboard_stats.php` + `admin/js/dashboard.js`.
- [x] **p05 admin-users** — User management. `admin/pages/users.php`, `user_form.php` and the six user/request ajax actions exist.
- [x] **p06 admin-settings** — Settings, locations, categories and backup. `admin/pages/settings.php` + settings/location/category/backup ajax actions exist.
- [x] **p07 admin-logs** — Log viewer and activity log. `admin/pages/logs.php` + log/activity ajax actions exist.
- [x] **p08b admin-products** — Admin product CRUD with stock per location. `admin/pages/products.php`, `product_form.php`, product ajax actions and `core/models/product.php` exist.
- [x] **p09 wh-dashboard** — Warehouse shell and dashboard. `warehouse/pages/dashboard.php`, `warehouse/ajax/dashboard_stats.php`, `warehouse/js/dashboard.js`; sidebar link already existed. Command: `/p09-wh-dashboard`.
- [ ] **p10 wh-products** — Warehouse products reusing the product model. Command: `/p10-wh-products`.
- [ ] **p11 wh-stock** — Stock levels, issue, transfer, adjust, stock take. Command: `/p11-wh-stock`.
- [ ] **p12 wh-movements** — Stock movement history. Command: `/p12-wh-movements`.
- [ ] **p13 wh-receipt** — Goods receipt against purchase orders. Command: `/p13-wh-receipt`.
- [ ] **p15 pr-dashboard** — Procurement shell and dashboard. Command: `/p15-pr-dashboard`.
- [ ] **p16 pr-suppliers** — Suppliers and performance. Command: `/p16-pr-suppliers`.
- [ ] **p17 pr-purchase-orders** — Purchase orders. Command: `/p17-pr-purchase-orders`.
- [ ] **p18 pr-reorder** — Reorder recommendations page. Command: `/p18-pr-reorder`.
- [ ] **p19 pr-risk** — Supplier risk and alerts page. Command: `/p19-pr-risk`.
- [ ] **p20 sales-dashboard** — Sales shell and dashboard. Command: `/p20-sales-dashboard`.
- [ ] **p21 sales-new** — Record a sale with receipt. Command: `/p21-sales-new`.
- [ ] **p22 sales-history** — Sales history and void. Command: `/p22-sales-history`.
- [ ] **p23 mgr-dashboard** — Manager dashboard. Command: `/p23-mgr-dashboard`.
- [ ] **p24 mgr-forecasts** — Forecasts page. Command: `/p24-mgr-forecasts`.
- [ ] **p25 mgr-reports** — Reports. Command: `/p25-mgr-reports`.
- [ ] **p27 demo-data** — Demo sales history. Command: `/p27-demo-data`.
- [ ] **p28 ml-lstm** — LSTM demand forecasting. Command: `/p28-ml-lstm`.
- [ ] **p29 ml-rf** — Random Forest reorder optimisation. Command: `/p29-ml-rf`.
- [ ] **p30 ml-risk** — Supplier risk and anomaly detection. Command: `/p30-ml-risk`.
- [ ] **p31 ml-bridge** — PHP bridge and nightly schedule. Command: `/p31-ml-bridge`.
- [ ] **p32 chatbot** — Chatbot using the Claude API. Command: `/p32-chatbot`.
- [ ] **p33 notify** — Alerts by email and SMS. Command: `/p33-notify`.
- [ ] **p34 integration** — Integration tests. Command: `/p34-integration`.
- [ ] **p35 security** — Security audit and fixes. Command: `/p35-security`.
- [ ] **p36 docs** — README and user guides. Command: `/p36-docs`.

## Notes

- p08, p14 and p26 are not defined in the command list, so they are skipped.
- p09 tested with curl: warehouse and admin get 200, sales gets 403; `dashboard_stats.php` returned the KPI counts and 10 lowest-stock rows.
- The `.opencode/commands/` files were not present in this checkout; only AGENTS.md and PROGRESS.md were rebuilt.
