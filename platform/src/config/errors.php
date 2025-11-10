<?php
return [

    // =====================================================
    // ERRORES DE AUTENTICACIÓN
    // =====================================================
    'AUTH_INVALID_CREDENTIALS' => 'Usuario y/o contraseña incorrectos, verifica por favor.',
    'AUTH_UNAUTHORIZED'        => 'Acceso no autorizado, inicia sesión primero.',
    'AUTH_UNKNOWN_ERROR'       => 'Ocurrió un error inesperado durante la autenticación.',
    'SESSION_EXPIRED'          => 'Tu sesión ha expirado, inicia sesión nuevamente.',
    'TOKEN_NOT_AVAILABLE'      => 'Token no disponible o inválido, por favor vuelve a iniciar sesión.',

    // =====================================================
    // ERRORES DE PRODUCTOS
    // =====================================================
    'PRODUCT_ALREADY_EXISTS'   => 'El producto ya está registrado en el sistema.',
    'PRODUCT_NOT_FOUND'        => 'El producto solicitado no existe.',

    // =====================================================
    // ERRORES DE VALIDACIONES GENERALES
    // =====================================================
    'VALIDATION_REQUIRED_FIELD' => 'Todos los campos obligatorios deben estar completos.',
    'VALIDATION_INVALID_FORMAT' => 'El formato de los datos enviados no es válido.',

    // =====================================================
    // ERRORES DE BASE DE DATOS
    // =====================================================
    'DB_ERROR'                 => 'Error en la base de datos, por favor intenta más tarde.',
    'DB_CONNECTION_FAILED'     => 'No fue posible conectar con la base de datos.',
    'DB_QUERY_FAILED'          => 'No se pudo ejecutar la consulta solicitada.',

    // =====================================================
    // ERRORES DE CONEXIÓN / SERVIDOR
    // =====================================================
    'SERVER_CONNECTION_ERROR'  => 'No se pudo conectar con el servidor.',
    'SERVER_UNAVAILABLE'       => 'El servidor no está disponible temporalmente.',
    'SERVER_TIMEOUT'           => 'Tiempo de espera excedido, por favor intenta más tarde.',
];