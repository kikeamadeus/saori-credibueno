-- =====================================================
-- Proyecto: SAORI - CREDIBUENO
-- Versión: v1.0.2 (Fase 1)
-- Fecha: 2025-11-14
-- Descripción:
--   Migración actualizada del sistema SAORI-Credibueno.
--   Incluye autenticación, empleados, roles, estatus,
--   sucursales, áreas organizacionales y trazabilidad.
-- =====================================================

-- =====================================================
-- 1. CREACIÓN DE BASE DE DATOS
-- =====================================================
CREATE DATABASE IF NOT EXISTS arcobit1_saoricb1
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE arcobit1_saoricb1;

-- =====================================================
-- 2. TABLA: statuses
-- Descripción:
--   Define los estados del empleado y controla si puede
--   iniciar sesión o registrar asistencia.
-- =====================================================
CREATE TABLE IF NOT EXISTS statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    can_login TINYINT(1) DEFAULT 1 COMMENT '0 = No puede iniciar sesión',
    can_checkin TINYINT(1) DEFAULT 1 COMMENT '0 = No puede registrar asistencia',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. TABLA: roles
-- Descripción:
--   Controla los permisos y capacidades de cada tipo
--   de usuario dentro del sistema.
-- =====================================================
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 4. TABLA: areas
-- Descripción:
--   Agrupa a los empleados por división funcional para
--   control de vacaciones y reportes.
-- =====================================================
CREATE TABLE IF NOT EXISTS areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
);

-- =====================================================
-- 5. TABLA: branches
-- Descripción:
--   Sucursales físicas con ubicación GPS y radio de
--   validación de asistencia.
-- =====================================================
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    address VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    zip_code VARCHAR(10) DEFAULT NULL,
    latitude DECIMAL(10,8) DEFAULT NULL COMMENT 'Latitud del punto central de la sucursal',
    longitude DECIMAL(11,8) DEFAULT NULL COMMENT 'Longitud del punto central de la sucursal',
    checkin_radius_meters INT DEFAULT 300 COMMENT 'Radio máximo de validación en metros',
    is_active TINYINT(1) DEFAULT 1 COMMENT '0 = Inactiva, 1 = Activa',
    created_at DATETIME NOT NULL
);

-- =====================================================
-- 6. TABLA: employees
-- Descripción:
--   Registro de empleados con rol, estatus, sucursal y área.
-- =====================================================
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    names VARCHAR(100) NOT NULL,
    surname1 VARCHAR(100) NOT NULL,
    surname2 VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    id_area INT NOT NULL COMMENT 'Área a la que pertenece el empleado',
    id_branch INT DEFAULT NULL COMMENT 'Sucursal asignada',
    can_check_all TINYINT(1) DEFAULT 0 COMMENT '1 = Puede checar en todas las sucursales',
    id_role INT NOT NULL COMMENT 'Rol del empleado',
    status_id INT NOT NULL COMMENT 'Estado del empleado',
    hire_date DATE NOT NULL COMMENT 'Fecha de ingreso del empleado',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    FOREIGN KEY (id_role) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (status_id) REFERENCES statuses(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_branch) REFERENCES branches(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (id_area) REFERENCES areas(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- =====================================================
-- 7. TABLA: users
-- Descripción:
--   Credenciales de acceso vinculadas a un empleado.
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employee INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    FOREIGN KEY (id_employee) REFERENCES employees(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- =====================================================
-- 8. TABLA: sessions
-- Descripción:
--   Control de sesiones activas (Refresh Tokens).
-- =====================================================
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    refresh_token VARCHAR(512) NOT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    last_used_at DATETIME DEFAULT NULL,
    is_revoked TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- =====================================================
-- 9. TABLA: session_logs
-- Descripción:
--   Historial de eventos de sesión (login, logout, etc).
-- =====================================================
CREATE TABLE IF NOT EXISTS session_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    event_type ENUM('login', 'refresh', 'logout', 'revoked') NOT NULL,
    event_time DATETIME NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- =====================================================
-- 10. TABLA: attendance_records
-- Descripción:
--   Registra cada evento de asistencia realizado por un
--   empleado (entrada, salida a comida, regreso, salida).
--   Cada asistencia es un evento independiente, con fecha
--   y hora exacta del registro, ubicación GPS y origen
--   (web o móvil).
--
--   Esta tabla es escalable porque:
--     • Cada registro es independiente (no hay columnas fijas por día)
--     • Permite miles/millones de filas sin afectar rendimiento
--     • Las consultas por periodo usan índices eficientes
-- =====================================================

CREATE TABLE IF NOT EXISTS attendance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,

    employee_id INT NOT NULL COMMENT 'Empleado que realizó el registro',

    -- Tipo de evento registrado
    event_type ENUM(
        'entrada',
        'comida_salida',
        'comida_regreso',
        'salida'
    ) NOT NULL,

    -- Fecha y hora exacta del registro
    event_datetime DATETIME NOT NULL,

    -- Ubicación de la checada
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,

    -- Origen del registro
    source ENUM('web','mobile') NOT NULL COMMENT 'Origen del registro (web o app móvil)',

    -- Control de creación
    created_at DATETIME NOT NULL,

    FOREIGN KEY (employee_id) REFERENCES employees(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Índices recomendados para alto rendimiento
CREATE INDEX idx_attendance_employee ON attendance_records(employee_id);
CREATE INDEX idx_attendance_event_dt ON attendance_records(event_datetime);
CREATE INDEX idx_attendance_event_type ON attendance_records(event_type);

-- =====================================================
-- 11. TABLA: permissions
-- Descripción:
--   Lista de permisos del sistema.
--   Cada permiso controla un comportamiento en Web y App.
--   El campo "permission_key" debe ser único y estable,
--   ya que será usado por APIs y por la app móvil.
-- =====================================================

CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permission_key VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 12. TABLA: role_permissions
-- Descripción:
--   Relación N:M entre roles y permisos.
--   Define qué permisos tiene cada rol.
-- =====================================================

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (role_id, permission_id),

    FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    FOREIGN KEY (permission_id)
        REFERENCES permissions(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);