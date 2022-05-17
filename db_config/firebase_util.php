<?php
require_once '/var/www/html/vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;

function sendFirebaseNotification($fireToken, $notification) {
    $factory = (new Factory)->withServiceAccount("/var/www/secret/privatekey.json");
    $messaging = $factory->createMessaging();
    $message = CloudMessage::withTarget("token", $fireToken)
    ->withNotification($notification);
    $messaging->send($message);
}