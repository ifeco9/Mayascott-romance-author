<?php
$password = password_hash('admin123', PASSWORD_DEFAULT);
return "INSERT IGNORE INTO users (username, password) VALUES ('admin', '$password')";
