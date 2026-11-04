<?php
// Allowed settings: key => [type, secret?]. Types: str, int, bool, time, email, horizons.
return [
    'company_name' => ['str', 0], 'currency' => ['str', 0], 'session_timeout_min' => ['int', 0],
    'low_stock_threshold' => ['int', 0], 'overstock_multiplier' => ['int', 0], 'supplier_delay_days' => ['int', 0],
    'email_enabled' => ['bool', 0], 'sms_enabled' => ['bool', 0],
    'smtp_host' => ['str', 0], 'smtp_port' => ['int', 0], 'smtp_user' => ['str', 0], 'smtp_pass' => ['str', 1],
    'mail_from' => ['email', 0], 'sms_username' => ['str', 0], 'sms_api_key' => ['str', 1],
    'forecast_schedule' => ['time', 0], 'forecast_horizons' => ['horizons', 0],
];
