-- =====================================================
-- Proyecto: SAORI - CREDIBUENO
-- Versión: v1.0.3 (Fase 1)
-- Fecha: 2025-11-29
-- Descripción:
--   Script oficial de creación de estructura para SAORI.
--   Compatible con Web + App (Flutter).
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
    can_login TINYINT(1) DEFAULT 1,
    can_checkin TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. TABLA: roles
-- =====================================================
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 4. TABLA: areas
-- =====================================================
CREATE TABLE IF NOT EXISTS areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
);

-- =====================================================
-- 5. TABLA: branches
-- =====================================================
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    address VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    zip_code VARCHAR(10) DEFAULT NULL,
    latitude DECIMAL(10,8) DEFAULT NULL,
    longitude DECIMAL(11,8) DEFAULT NULL,
    checkin_radius_meters INT DEFAULT 300,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL
);

-- =====================================================
-- 6. TABLA: employees
-- =====================================================
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Datos personales
    names VARCHAR(100) NOT NULL,
    surname1 VARCHAR(100) NOT NULL,
    surname2 VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,

    -- Relaciones
    id_area INT NOT NULL,
    id_branch INT DEFAULT NULL,
    id_role INT NOT NULL,
    status_id INT NOT NULL,

    -- Control de asistencia
    can_check_all TINYINT(1) DEFAULT 0,
    tolerance_minutes INT NOT NULL DEFAULT 15,
    entry_time_weekday TIME NOT NULL DEFAULT '08:30:00',
    entry_time_saturday TIME NOT NULL DEFAULT '09:00:00',

    -- Datos laborales
    hire_date DATE NOT NULL,

    -- Auditoría
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
-- =====================================================
CREATE TABLE IF NOT EXISTS session_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    event_type ENUM('login','refresh','logout','revoked') NOT NULL,
    event_time DATETIME NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- =====================================================
-- 10. TABLA: attendance_records
-- =====================================================
CREATE TABLE IF NOT EXISTS attendance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,

    employee_id INT NOT NULL,

    attendance_date DATE NOT NULL,
    attendance_hour TIME NOT NULL,

    attendance_type CHAR(2) NOT NULL COMMENT 'A, R, F, D, V, I, FR, FJ, VT',

    attendance_latitude DECIMAL(10,8) DEFAULT NULL,
    attendance_longitude DECIMAL(11,8) DEFAULT NULL,

    source ENUM('web','mobile') NOT NULL,

    created_at DATETIME NOT NULL,

    FOREIGN KEY (employee_id) REFERENCES employees(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX idx_attendance_employee ON attendance_records(employee_id);
CREATE INDEX idx_attendance_date ON attendance_records(attendance_date);

-- =====================================================
-- 11. TABLA: permissions
-- =====================================================
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permission_key VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 12. TABLA: role_permissions
-- =====================================================
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (role_id, permission_id),

    FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    FOREIGN KEY (permission_id) REFERENCES permissions(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);