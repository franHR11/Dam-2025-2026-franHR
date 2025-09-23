<?php
/**
 * SessionManager.php
 * Componente principal de gestión de sesiones
 * Verificación automática al ser incluido
 */

// Inicio de sesión automático
session_start();

// Carga de configuración
require_once __DIR__ . '/AuthConfig.php';

/**
 * Clase SessionManager
 * Gestiona las sesiones de usuario y autenticación
 */
class SessionManager {
    
    /**
     * Verificación automática de sesión
     * Se ejecuta automáticamente al incluir el archivo
     */
    public static function checkSession() {
        // Verificar si hay sesión válida
        if (!self::isLoggedIn()) {
            self::redirectToLogin();
            exit();
        }
        
        // Verificar timeout de sesión
        if (self::checkTimeout()) {
            self::destroySession();
            self::redirectToLogin();
            exit();
        }
        
        // Actualizar última actividad
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Verificar si el usuario está logueado
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['username']) && !empty($_SESSION['username']);
    }
    
    /**
     * Obtener información del usuario actual
     * @return array|null
     */
    public static function getUserInfo() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];
    }
    
    /**
     * Verificar timeout de sesión
     * @return bool true si la sesión ha expirado
     */
    public static function checkTimeout() {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return false;
        }
        
        $inactive_time = time() - $_SESSION['last_activity'];
        return $inactive_time > SESSION_TIMEOUT;
    }
    
    /**
     * Redirigir al login
     */
    public static function redirectToLogin() {
        header('Location: ' . LOGIN_URL);
        exit();
    }
    
    /**
     * Destruir sesión completamente
     */
    public static function destroySession() {
        session_destroy();
        session_start();
    }
    
    /**
     * Inicializar sesión de usuario (para uso en login)
     * @param string $usuario
     */
    public static function initUserSession($user) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['Identificador'];
        $_SESSION['username'] = $user['usuario'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
    }
}

// La verificación de sesión ahora se debe llamar explícitamente en cada página protegida.

?>