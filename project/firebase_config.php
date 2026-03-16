<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$serviceAccount = [
    "type" => $_ENV['FIREBASE_TYPE'],
    "project_id" => $_ENV['FIREBASE_PROJECT_ID'],
    "private_key_id" => $_ENV['FIREBASE_PRIVATE_KEY_ID'],
    "private_key" => str_replace("\\n", "\n", $_ENV['FIREBASE_PRIVATE_KEY']),
    "client_email" => $_ENV['FIREBASE_CLIENT_EMAIL'],
    "client_id" => $_ENV['FIREBASE_CLIENT_ID'],
    "auth_uri" => $_ENV['FIREBASE_AUTH_URI'],
    "token_uri" => $_ENV['FIREBASE_TOKEN_URI'],
    "auth_provider_x509_cert_url" => $_ENV['FIREBASE_AUTH_PROVIDER_CERT_URL'],
    "client_x509_cert_url" => $_ENV['FIREBASE_CLIENT_CERT_URL'],
    "universe_domain" => $_ENV['FIREBASE_UNIVERSE_DOMAIN']
];

// // Ambil credentials dari environment variable
// $firebaseCredentials = getenv('FIREBASE_CREDENTIALS');

// if (!$firebaseCredentials) {
//     die("Firebase credentials not set in environment variables.");
// }

// // Decode JSON credentials
// $serviceAccount = json_decode($firebaseCredentials, true);

// if (!$serviceAccount) {
//     die("Invalid Firebase credentials.");
// }

// Konfigurasi Firebase (Ganti dengan file JSON yang diunduh dari Firebase)
$factory = (new Factory)
    ->withServiceAccount($serviceAccount) // Ganti dengan file JSON Firebase Anda
    ->withDatabaseUri($_ENV['FIREBASE_DATABASE_URI']); // Ganti dengan URL database Anda

$database = $factory->createDatabase();
$auth = $factory->createAuth();

?>