# Adrow Time Record

> Manage time record of Adrow Creatives Inc.

## Quick Start

Add your DB_HOST, DB_PORT, DB_NAME, DB_USERNAME, DB_PASSWORD, EMAIL, PASSWORD to the .env file. Make sure you set an env variable for that.

```
# Install dependencies
composer install
```

## Database

Import the adrow_time_record_db.sql in your database.

## Auth

For your email and password, just create your password_hash in php and add the $encrypt_txt variable in .env file. Just change the value of $txt variable for email or password.

```php
$txt = "admin";
$encrypt_txt = password_hash($txt, PASSWORD_DEFAULT);
echo $encrypt_txt;
```

## App Info

### Author
Joshwa Facistol

### Version

5.0.1

### License

This project is licensed under the MIT License