<?php
/**
 * Google OAuth Configuration
 */

// Google OAuth Client ID and Secret
// You need to create these in the Google Cloud Console: https://console.cloud.google.com/
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');

// Redirect URI after Google authentication
define('GOOGLE_REDIRECT_URI', 'http://localhost:8000/google_callback.php');

// Required scopes for user information
define('GOOGLE_SCOPES', [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile',
    'openid'
]);