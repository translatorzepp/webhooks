<?php

$message = "subscription_webhooks is running\n";
file_put_contents("/var/www/html/webhooks/webhook.log", $message, FILE_APPEND);

require_once "/var/www/braintree-php-3.9.0/lib/Braintree.php";

date_default_timezone_set('America/Chicago');

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('ryqy4yyw7m5bf92h');
Braintree_Configuration::publicKey('ymtqgy8773zq2fw3');
Braintree_Configuration::privateKey('7dd7253c4c53d675f15e869212659579');

if(isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
  
    $to = 'zoe.palmer+webhooks@braintreepayments.com';
    $subject = 'Webhook Notification';
    $message = 'You got a webhook!';
    $headers = 'From: zoe.palmer@braintreepayments.com' . "\r\n";

    mail($to, $subject, $message, $headers);

    $message = "Webhook Recieved";
    file_put_contents("/var/www/html/webhooks/webhook.log", $message, FILE_APPEND);

    $webhookNotification = Braintree_WebhookNotification::parse(
        $_POST["bt_signature"], $_POST["bt_payload"]
    );

    $message = " and Parsed";
    file_put_contents("/var/www/html/webhooks/webhook.log", $message, FILE_APPEND);

    if ($webhookNotification->kind == 'check') {
        // $message = " " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . " Kind: " . $webhookNotification->kind . " | url check successful\n";
        $message = " Check\n";
    }
    else {
        // $message = " " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . " Kind: " . $webhookNotification->kind . " | " . "Subscription: " . $webhookNotification->subscription->id . "\n";
        $message = " Subscription\n";
    }

    file_put_contents("/var/www/html/webhooks/webhook.log", $message, FILE_APPEND);
}

?>