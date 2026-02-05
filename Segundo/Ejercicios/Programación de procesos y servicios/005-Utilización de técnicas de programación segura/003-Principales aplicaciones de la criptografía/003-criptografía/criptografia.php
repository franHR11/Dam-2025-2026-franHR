<?php
// Cadena de texto original
$texto = "Mi pesca del día: 2 kilos de lubina";

// Cifrado con MD5 y codificación Base64
$hash_md5 = md5($texto);
$codificado_md5 = base64_encode($hash_md5);

// Cifrado con SHA1 y codificación Base64
$hash_sha1 = sha1($texto);
$codificado_sha1 = base64_encode($hash_sha1);

// Mostrar resultados
echo "Texto original: " . $texto . "\n";
echo "MD5 Hash: " . $hash_md5 . "\n";
echo "MD5 Base64: " . $codificado_md5 . "\n";
echo "SHA1 Hash: " . $hash_sha1 . "\n";
echo "SHA1 Base64: " . $codificado_sha1 . "\n";
?>
