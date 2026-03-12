<?php
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

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
    ->withServiceAccount(__DIR__ . '/../firebase_credentials.json') // Ganti dengan file JSON Firebase Anda
    ->withDatabaseUri('https://afl-2-cc-default-rtdb.asia-southeast1.firebasedatabase.app/'); // Ganti dengan URL database Anda

$database = $factory->createDatabase();
$auth = $factory->createAuth();

?>