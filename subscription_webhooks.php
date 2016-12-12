<?php

require "/var/www/html/autoload_braintree.php";

if(isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
  
    $webhookNotification = Braintree_WebhookNotification::parse(
        $_POST["bt_signature"], $_POST["bt_payload"]
    );

    $to = 'zoe.palmer+webhooks@braintreepayments.com';
    $subject = 'Webhook Notification';
    $headers = 'From: zoe.palmer@braintreepayments.com' . "\r\n";

    $message = "Webhook received! " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . "\nKind: " . $webhookNotification->kind;

    if ($webhookNotification->kind == 'check') {
        $message = $message . " | URL check successful\n";
    }
    else {
        $message = $message . "\nSubscription: " . $webhookNotification->subscription->id;
    }
    // Turn this into a case statement for the different possible webhook kinds

    mail($to, $subject, $message, $headers);

    // This isn't working. Work on it.
    file_put_contents("/var/www/html/webhooks/webhook.txt", $message, FILE_APPEND);
}
else {
    echo "Webhook endpoint reached by not a webhook.";
}

?>
