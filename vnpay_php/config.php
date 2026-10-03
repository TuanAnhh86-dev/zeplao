<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

$vnp_TmnCode = ($_ENV['VNPAY_TMN_CODE'] ?? getenv('VNPAY_TMN_CODE')) ?: '';
$vnp_HashSecret = ($_ENV['VNPAY_HASH_SECRET'] ?? getenv('VNPAY_HASH_SECRET')) ?: '';
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$appUrl = ($_ENV['APP_URL'] ?? getenv('APP_URL')) ?: 'http://localhost';
$vnp_Returnurl = rtrim($appUrl, '/').'/payments/vnpay/return';
$vnp_apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+10 minutes', strtotime($startTime)));
