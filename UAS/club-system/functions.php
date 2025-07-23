<?php
function encrypt_data($data, $key) {
    $method = "AES-256-CBC";
    $iv = substr(hash('sha256', 'iv_for_demo'), 0, 16);
    return openssl_encrypt($data, $method, $key, 0, $iv);
}

function decrypt_data($encrypted, $key) {
    $method = "AES-256-CBC";
    $iv = substr(hash('sha256', 'iv_for_demo'), 0, 16);
    return openssl_decrypt($encrypted, $method, $key, 0, $iv);
}
?>