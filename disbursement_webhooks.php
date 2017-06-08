<?php

require "/var/www/html/autoload_braintree.php";
$logpath = "/tmp/webhook.log";

//// dump request to logs:
//$requestlogpath = "/tmp/requests.log";
//$request = var_export($_REQUEST, true);
//$request_log_message = "Dumping _REQUEST:\n" . $request . "\n";
//file_put_contents($requestlogpath, $request_log_message, FILE_APPEND);
//$server = var_export($_SERVER, true);
//$server_log_message = "Dumping _SERVER:\n" . $server . "\n";
//file_put_contents($requestlogpath, $server_log_message, FILE_APPEND);

if(isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {

    $webhookNotification = Braintree_WebhookNotification::parse(
        $_POST["bt_signature"], $_POST["bt_payload"]
    );

    $to = 'zoe.palmer+webhooks@braintreepayments.com';
    $subject = 'Webhook Notification';
    $headers = 'From: zoe.palmer@braintreepayments.com' . "\r\n";

    $message = "Webhook received! " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . " Kind: " . $webhookNotification->kind;

    if ($webhookNotification->kind == Braintree_WebhookNotification::CHECK) {
        $message = $message . " | URL check successful\n";
    }
    elseif ($webhookNotification->kind == Braintree_WebhookNotification::DISBURSEMENT) {
        $disbursement = $webhookNotification->disbursement;
        $disburseDate = $disbursement->disbursementDate->format('Y-m-d');
        $disburseIsARetry = "";
        $disburseSuccess = "";
        $transactionList = implode(", ", $disbursement->transactionIds);
        if ($disbursement->retry == True) {
            $disburseIsARetry = "true";
        }
        else {
            $disburseIsARetry = "false";
        }
        if ($disbursement->success == True) {
            $disburseSuccess = "true";
        }
        else {
            $disburseSuccess = "false";
        }
        $message = $message . " | Disbursement: " . $disbursement->id . "\nDisbursement Date: " . $disburseDate . "\nAmount: " . $disbursement->amount . "\Merchant Account ID: " . $disbursement->merchantAccount->id . "\nRetry? ". $disburseIsARetry . "\nSuccessful? " . $disburseSuccess . "\nList of Transaction IDs: " . $transactionList . "\n";
    }
    else {
        $message = $message . " | Unknown Type.\n";
    }

    mail($to, $subject, $message, $headers);

    file_put_contents($logpath, $message, FILE_APPEND);

}
else {

    file_put_contents($logpath, "Non-Braintree-webhook-post made to this endpoint.\n", FILE_APPEND);
    echo "Webhook endpoint reached by not a webhook.";

}

?>
