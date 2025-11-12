-- =====================================================
-- Proyecto: SAORI - CREDIBUENO
-- Versión: v1.0.1 (Fase 1.1)
-- Fecha: 2025-11-12
-- Descripción:
--   Migración mejorada con gestión inicial de vacaciones.
--   Incluye días de vacaciones según antigüedad laboral
--   y estructura para futuras solicitudes de vacaciones.
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
-- =====================================================
CREATE TABLE IF NOT EXISTS statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    can_login TINYINT(1) DEFAULT 1 COMMENT '0 = No puede iniciar sesión',
    can_checkin TINYINT(1) DEFAULT 1 COMMENT '0 = No puede registrar asistencia'
);

-- =====================================================
-- 3. TABLA: roles
-- =====================================================
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
);

-- =====================================================
-- 4. TABLA: employees
-- =====================================================
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    names VARCHAR(100) NOT NULL,
    surname1 VARCHAR(100) NOT NULL,
    surname2 VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    id_role INT NOT NULL COMMENT 'Rol del empleado (Administrador, Auxiliar, Sistemas, Empleado)',
    status_id INT NOT NULL COMMENT 'Activo / Suspendido / Baja / Vacaciones / Incapacidad',
    hire_date DATE NOT NULL COMMENT 'Fecha de ingreso del empleado',
    vacation_days INT DEFAULT 0 COMMENT 'Días totales de vacaciones según antigüedad',
    vacation_used INT DEFAULT 0 COMMENT 'Días de vacaciones tomados en el año actual',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    FOREIGN KEY (id_role) REFERENCES roles(id),
    FOREIGN KEY (status_id) REFERENCES statuses(id)
);

-- =====================================================
-- 5. TABLA: users
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employee INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    FOREIGN KEY (id_employee) REFERENCES employees(id)
);

-- =====================================================
-- 6. TABLA: sessions
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
);

-- =====================================================
-- 7. TABLA: session_logs
-- =====================================================
CREATE TABLE IF NOT EXISTS session_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    event_type ENUM('login', 'refresh', 'logout', 'revoked') NOT NULL,
    event_time DATETIME NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (session_id) REFERENCES sessions(id)
);

-- =====================================================
-- 8. TABLA: vacation_requests
-- Descripción: Solicitudes de vacaciones por empleado,
--              con control de fechas, días y aprobación.
-- =====================================================
CREATE TABLE IF NOT EXISTS vacation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employee INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days INT NOT NULL,
    status ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    approved_by INT DEFAULT NULL COMMENT 'ID del usuario que aprueba la solicitud',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    FOREIGN KEY (id_employee) REFERENCES employees(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);