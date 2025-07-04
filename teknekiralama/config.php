<?php
// Komisyon oranı (yüzde)
define('COMMISSION_RATE', 15); // %15, admin panelden değiştirilebilir
// Desteklenen diller
$supported_languages = ['tr', 'en', 'ar', 'ru'];
// Varsayılan dil
$default_language = 'tr';

// iyzico test API anahtarları
// Bunları canlıya geçerken değiştirin!
define('IYZICO_API_KEY', 'sandbox-API-KEYINIZ');
define('IYZICO_SECRET_KEY', 'sandbox-SECRET-KEYINIZ');
define('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com');

// SMTP ayarları (örnek Gmail, canlıda değiştirin)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'mailadresiniz@gmail.com');
define('SMTP_PASSWORD', 'uygulama-şifresi');
define('MAIL_FROM', 'mailadresiniz@gmail.com');
define('MAIL_FROM_NAME', 'Tekne Kiralama'); 