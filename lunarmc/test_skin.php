<?php
header('Content-Type: text/plain; charset=utf-8');

$url = 'https://api.mojang.com/users/profiles/minecraft/Jxeremias_';

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_USERAGENT => 'LunarMC/1.0',
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$respuesta = curl_exec($ch);

echo "HTTP: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo "Error: " . curl_error($ch) . "\n";
echo "Respuesta: " . ($respuesta === false ? '(sin respuesta)' : $respuesta) . "\n";

curl_close($ch);