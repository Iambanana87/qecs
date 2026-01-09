<?php

// app/Helpers/constants.php (tạo file nếu chưa có)
if (!defined('IAM_DB')) {
    define('IAM_DB', env('IAM_DATABASE', 'iam'));
}
if (!defined('TDLS_DB')) {
    define('TDLS_DB', env('DB_DATABASE', 'tdls'));
}
if (!defined('IAM_CONNECTION')) {
    define('IAM_CONNECTION', env('IAM_CONNECTION', 'mysql2'));
}