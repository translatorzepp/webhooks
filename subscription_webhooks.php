<?php

require "/var/www/html/autoload_braintree.php";
$logpath = "/tmp/webhook.log";

if(isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {

    // dump request to logs:
    file_put_contents($logpath, var_dump($_REQUEST) . "\n", FILE_APPEND);
    file_put_contents($logpath, var_dump($_POST) . "\n", FILE_APPEND);

    $webhookNotification = Braintree_WebhookNotification::parse(
        $_POST["bt_signature"], $_POST["bt_payload"]
    );

    $to = 'zoe.palmer+webhooks@braintreepayments.com';
    $subject = 'Webhook Notification';
    $headers = 'From: zoe.palmer@braintreepayments.com' . "\r\n";

    $message = "Webhook received! " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . " Kind: " . $webhookNotification->kind;

    if ($webhookNotification->kind == 'check') {
        $message = $message . " | URL check successful\n";
    }
    else {
        $message = $message . " | Subscription: " . $webhookNotification->subscription->id . "\n";
    }
    // Turn this into a case statement for the different possible webhook kinds

    mail($to, $subject, $message, $headers);

    file_put_contents($logpath, $message, FILE_APPEND);

}
else {

    file_put_contents($logpath, "Non-Braintree-webhook-post made to this endpoint.\n", FILE_APPEND);
    echo "Webhook endpoint reached by not a webhook.";

}

?>
