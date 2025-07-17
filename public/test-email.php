<?php

// Simple email test script
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->boot();

try {
    echo "Testing email configuration...\n";
    echo "Mail driver: " . $app['config']->get('mail.default') . "\n";
    echo "Mail host: " . $app['config']->get('mail.mailers.smtp.host') . "\n";
    echo "From address: " . $app['config']->get('mail.from.address') . "\n";
    
    // Test sending a simple email
    $app['mailer']->raw('This is a test email from your Laravel application.', function($message) {
        $message->to('rramees60@gmail.com')
                ->subject('Test Email - Laravel Bulk Invite');
    });
    
    echo "✅ Test email sent successfully!\n";
    echo "Check your Mailtrap inbox to see the email.\n";
    
} catch (\Exception $e) {
    echo "❌ Error sending email: " . $e->getMessage() . "\n";
}
