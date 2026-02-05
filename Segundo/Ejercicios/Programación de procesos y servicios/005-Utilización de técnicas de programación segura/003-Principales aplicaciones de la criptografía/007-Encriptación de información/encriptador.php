<?php
class Encriptador {
    public function encripta($cadena) {
        $resultado = '';
        for ($i = 0; $i < strlen($cadena); $i++) {
            $resultado .= chr(ord($cadena[$i]) + 5);
        }
        return $resultado;
    }

    public function desencripta($cadena) {
        $resultado = '';
        for ($i = 0; $i < strlen($cadena); $i++) {
            $resultado .= chr(ord($cadena[$i]) - 5);
        }
        return $resultado;
    }
}

$conexion = mysqli_connect('localhost', 'root', '', 'tienda2526');
$encriptador = new Encriptador();
$resultado = mysqli_query($conexion, 'SELECT * FROM clientes');
$datos = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    foreach ($fila as $campo => $valor) {
        $fila[$campo] = $encriptador->encripta($valor);
    }
    $datos[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($datos, JSON_UNESCAPED_UNICODE);
mysqli_close($conexion);
?>
