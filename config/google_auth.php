<?php
/**
 * Google OAuth Configuration
 * 
 * To set up Google OAuth:
 * 1. Go to https://console.cloud.google.com/
 * 2. Create a new project or select an existing one
 * 3. Navigate to "APIs & Services" > "Credentials"
 * 4. Click "Create Credentials" > "OAuth client ID"
 * 5. Set Application Type to "Web application"
 * 6. Add "http://localhost:8000/google_callback.php" to Authorized redirect URIs
 * 7. Copy your Client ID and Client Secret below
 */

// Google OAuth Client ID and Secret
// Replace these with your actual credentials from Google Cloud Console
define('GOOGLE_CLIENT_ID','845909708201-p0hoaanu3d2h1u61r3g7s0jg7mf6ket9.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET','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');


// Redirect URI after Google authentication
define('GOOGLE_REDIRECT_URI', 'http://localhost/Ecomme/public/google_callback.php');

// Required scopes for user information
define('GOOGLE_SCOPES', [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile',
    'openid'
]);