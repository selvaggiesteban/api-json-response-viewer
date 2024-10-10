<?php
// Configuración de la API
$api_url = 'https://www.promoproductos.com/api/product';
$api_user = 'cecilia@marking.com.ar';
$api_password = 'e3cef9e17dcd1da5a3e0759c18c16aaa82adce4e';
// Función para manejar errores de cURL
function handleCurlError($ch) {
    echo "Error al conectar con la API: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit(1);
}
// Función para manejar errores de respuesta HTTP
function handleHttpError($http_code, $response) {
    echo "Error HTTP " . $http_code . ": " . $response . "\n";
    exit(1);
}
// Inicializar cURL
$ch = curl_init();
// Configurar opciones de cURL
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
    CURLOPT_USERPWD => $api_user . ":" . $api_password,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => true,  // Activar verificación SSL
    CURLOPT_SSL_VERIFYHOST => 2,     // Verificar el nombre de host del certificado
]);
// Realizar la solicitud a la API
$response = curl_exec($ch);
// Manejar errores de cURL
if ($response === false) {
    handleCurlError($ch);
}
// Obtener información sobre la respuesta HTTP
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// Cerrar la sesión cURL
curl_close($ch);
// Manejar errores de respuesta HTTP
if ($http_code < 200 || $http_code >= 300) {
    handleHttpError($http_code, $response);
}
// Intentar decodificar el JSON
$data = json_decode($response, true);
// Verificar si hubo algún error en la decodificación del JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON: " . json_last_error_msg() . "\n";
    exit(1);
}
// Mostrar información básica sobre la respuesta
echo "Código de respuesta HTTP: " . $http_code . "\n";
echo "Longitud de la respuesta: " . strlen($response) . " bytes\n\n";
// Función para mostrar datos de manera segura
function displaySafeData($data, $depth = 0) {
    foreach ($data as $key => $value) {
        echo str_repeat("  ", $depth) . htmlspecialchars($key) . ": ";
        if (is_array($value)) {
            echo "\n";
            displaySafeData($value, $depth + 1);
        } else {
            echo htmlspecialchars(substr($value, 0, 100)) . (strlen($value) > 100 ? "..." : "") . "\n";
        }
    }
}
// Mostrar los datos de manera segura
echo "Contenido de la respuesta:\n";
displaySafeData($data);