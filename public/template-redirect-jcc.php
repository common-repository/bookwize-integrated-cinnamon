<?php
/**
 *
 * Template Name: Redirect Page
 *
 */

//Version
$version = "1.0.0";
//Merchant ID

$password = get_option('bw_jcc_password') ? get_option('bw_jcc_password') : '';
$merchantID = get_option('bw_jcc_merchant') ? get_option('bw_jcc_merchant') : '';
$acquirerID = get_option('bw_jcc_acquirer') ? get_option('bw_jcc_acquirer') : '';

$responseURL = $_POST['url'];
//Purchase Amount
$purchaseAmt = $_POST['purchaseAmt'];
$purchaseAmt = str_pad($purchaseAmt, 13, "0", STR_PAD_LEFT);
$formattedPurchaseAmt = substr($purchaseAmt, 0, 10) . substr($purchaseAmt, 11);
$currencyExp = 2;
$orderID = $_POST['orderID'];
$captureFlag = "A";
$currency = $_POST['currency'];
if ($currency === 'EUR') {
    $currency = '978';
} else if ($currency === 'GBP') {
    $currency = '826';
} else if ($currency === 'RUB') {
    $currency = '643';
} else {
    $currency = '840';
}

$toEncrypt = $password . $merchantID . $acquirerID . $orderID . $formattedPurchaseAmt . $currency;

$sha1Signature = sha1($toEncrypt);

$base64Sha1Signature = base64_encode(pack("H*", $sha1Signature));

$signatureMethod = "SHA1";
?>
<div class="ibe-container">
    <div class="bookwize">
        <div class="hp-wait text-center">
            <p data-bind="translate: 'pleaseWait'"></p>
        </div>
        <?php if($orderID){ ?>
        <form method="post" name="paymentForm" id="paymentForm"
              action="https://jccpg.jccsecure.com/EcomPayment/RedirectAuthLink" data-bind="with:reservation">
            <input type="hidden" name="Version" value="<?= $version ?>"><br>
            <input type="hidden" name="MerID" value="<?= $merchantID ?>"><br>
            <input type="hidden" name="AcqID" value="<?= $acquirerID ?>"><br>
            <input type="hidden" name="MerRespURL" value="<?= $responseURL ?>"><br>
            <input type="hidden" name="PurchaseAmt" value="<?= $formattedPurchaseAmt ?>"><br>
            <input type="hidden" name="PurchaseCurrency" value="<?= $currency ?>"><br>
            <input type="hidden" name="PurchaseCurrencyExponent" value="<?= $currencyExp ?>"><br>
            <input type="hidden" name="OrderID" value="<?= $orderID ?>"><br>
            <input type="hidden" name="CaptureFlag" value="<?= $captureFlag ?>"><br>
            <input type="hidden" name="Signature" value="<?= $base64Sha1Signature ?>"><br>
            <input type="hidden" name="SignatureMethod" value="<?= $signatureMethod ?>"><br>
        </form>
        <?php
            try {
                $root= realpath($_SERVER["DOCUMENT_ROOT"]);

                $filePath = "$root/jcc.log";
                $myfile = fopen($filePath, "a");
                $log = "User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . PHP_EOL . "Merchant: " . var_export($merchantID, true) . PHP_EOL. "Acquirer: " . var_export($acquirerID, true) . PHP_EOL . "Amount: " . var_export($formattedPurchaseAmt, true). PHP_EOL . "OrderID: " . var_export($orderID, true).PHP_EOL;
                fwrite($myfile, $log);
                fclose($myfile);
            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
        } ?>
    </div>
</div>
