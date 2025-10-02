<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/google_auth.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Initialize the Google Client
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope(GOOGLE_SCOPES);

// Handle the OAuth 2.0 server response
if (isset($_GET['code'])) {
    // Exchange authorization code for an access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Get user profile information
    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    // Extract user data
    $google_id = $userInfo->getId();
    $email = $userInfo->getEmail();
    $name = $userInfo->getName();
    $picture = $userInfo->getPicture();

    // Check if user exists in database
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // User exists, update Google ID if not set
        $user = mysqli_fetch_assoc($result);
        $user_id = $user['id'];
        
        if (empty($user['google_id'])) {
            $update_query = "UPDATE users SET google_id = '$google_id' WHERE id = $user_id";
            mysqli_query($conn, $update_query);
        }
    } else {
        // Create new user
        $password = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT); // Random password
        $insert_query = "INSERT INTO users (name, email, password, google_id, created_at) 
                         VALUES ('$name', '$email', '$password', '$google_id', NOW())";
        
        if (mysqli_query($conn, $insert_query)) {
            $user_id = mysqli_insert_id($conn);
        } else {
            set_flash_message('error', 'Failed to create account. Please try again.');
            redirect('/Ecomme/public/login.php');
        }
    }

    // Set session and redirect
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['logged_in'] = true;
    
    set_flash_message('success', 'Successfully logged in with Google!');
    redirect('/Ecomme/public/dashboard.php');
} else {
    // If no authorization code, redirect to login page
    set_flash_message('error', 'Google authentication failed. Please try again.');
    redirect('/Ecomme/public/login.php');
}