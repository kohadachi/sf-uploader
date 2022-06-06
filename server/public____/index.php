<?php

$gmo['cardNo'] = "375987000000088";
$gmo['expire'] = "202610";
$gmo['securityCode'] = "1234";
$gmo['holderName'] = "TEST";
$gmo['tokenNumber'] = "1";
$gmo = json_encode($gmo);
echo $gmo;
$keyPath = dirname(__FILE__).'/token_pubkey.pub';
$key = file_get_contents($keyPath);
$keyFinal = "-----BEGIN PUBLIC KEY-----\r\n" . chunk_split($key) . "-----END PUBLIC KEY-----";

// 結果が$crypted変数に入っている
openssl_public_encrypt($gmo, $crypted, $keyFinal);
echo base64_encode($crypted).PHP_EOL;


?>