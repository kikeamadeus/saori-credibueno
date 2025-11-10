-- =====================================================
-- Proyecto: BITORA
-- Versión: v1.0.1
-- Fecha: 2025-11-04
-- Descripción: Extensión de la estructura base agregando
--              persistencia de sesiones para autenticación
--              mediante JWT y control de sesiones activas.
-- =====================================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS arcobit1_saoricb1
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

-- Seleccionar la base de datos
USE arcobit1_saoricb1;

-- =====================================================
-- TABLA: areas
-- =====================================================
CREATE TABLE IF NOT EXISTS areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
);

-- =====================================================
-- TABLA: departments
-- =====================================================
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    id_area INT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (id_area) REFERENCES areas(id)
);

-- =====================================================
-- TABLA: positions
-- =====================================================
CREATE TABLE IF NOT EXISTS positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    id_department INT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (id_department) REFERENCES departments(id)
);

-- =====================================================
-- TABLA: statuses
-- =====================================================
CREATE TABLE IF NOT EXISTS statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
);

-- =====================================================
-- TABLA: employees
-- =====================================================
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    names VARCHAR(100) NOT NULL,
    surname1 VARCHAR(100) NOT NULL,
    surname2 VARCHAR(100) DEFAULT NULL,
    id_department INT NOT NULL,
    id_position INT NOT NULL,
    email VARCHAR(100) DEFAULT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    rfc VARCHAR(13) DEFAULT NULL,
    curp VARCHAR(18) DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    street VARCHAR(255) DEFAULT NULL,
    exterior_number VARCHAR(10) DEFAULT NULL,
    interior_number VARCHAR(10) DEFAULT NULL,
    settlement VARCHAR(100) DEFAULT NULL,
    municipality VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    zip_code VARCHAR(10) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    status_id INT NOT NULL,
    FOREIGN KEY (id_department) REFERENCES departments(id),
    FOREIGN KEY (id_position) REFERENCES positions(id),
    FOREIGN KEY (status_id) REFERENCES statuses(id)
);

-- =====================================================
-- TABLA: users
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
-- TABLA: sessions
-- Descripción: Control de sesiones activas por usuario
--              para permitir persistencia de inicio de
--              sesión mediante Refresh Tokens.
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
-- TABLA: session_logs
-- Descripción: Historial de eventos de sesión (login,
--              refresh, logout, revocación).
--              Útil para auditoría y seguridad.
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
