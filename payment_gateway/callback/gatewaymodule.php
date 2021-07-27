<?php
require_once __DIR__ . '/../../../init.php';
App::load_function('gateway');
App::load_function('invoice');

$gatewayModuleName = basename(__FILE__, '.php');
$gatewayParams = getGatewayVariables($gatewayModuleName);
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}
$success = $_POST["x_status"];
$invoiceId = $_POST["x_invoice_id"];
$transactionId = $_POST["x_trans_id"];
$paymentAmount = $_POST["x_amount"];
$paymentFee = $_POST["x_fee"]; 
$hash = $_POST["x_hash"];
try
{
    if(!empty($invoiceId)){

        checkCbInvoiceID($invoiceId, $gatewayParams['name']);
    }
    else
    { 
        logTransaction($gatewayParams['name'], $_POST, $success);
        header("location: http://localhost/whmcs/clientarea.php"); 
    }
    if(!empty($transactionId)){
        
        checkCbTransID($transactionId);
    }
    else
    {

        logTransaction($gatewayParams['name'], $_POST, $success);
        header("location: http://localhost/whmcs/clientarea.php");   
    }
    if ($success == 'success') {

        addInvoicePayment(
            $invoiceId,
            $transactionId,
            $paymentAmount,
            $paymentFee,
            $gatewayModuleName
        );
        logTransaction($gatewayParams['name'], $_POST, $success);
        header("location:/whmcs/viewinvoice.php?id=".$invoiceId."&view_as_client=1");   
    }
    else
    {
        return 'error';
    }
}
catch(Exception $e)
{
    return $e->getMessage();
}
?>    