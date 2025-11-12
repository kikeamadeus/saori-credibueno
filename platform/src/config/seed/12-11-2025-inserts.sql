-- =====================================================
-- Proyecto: SAORI - CREDIBUENO
-- Versión: v1.0.1 (Fase 1.1)
-- Fecha: 2025-11-12
-- Descripción:
--   Datos iniciales (seed) para entorno Credibueno.
--   Incluye roles, estatus y usuario administrador.
-- =====================================================

USE arcobit1_saoricb1;

-- =====================================================
-- 1. ESTATUS (para empleados)
-- =====================================================
INSERT INTO statuses (name, description, can_login, can_checkin)
VALUES
('Activo', 'Empleado con acceso al sistema', 1, 1),
('Suspendido', 'Empleado temporalmente inactivo', 0, 0),
('Baja', 'Empleado dado de baja', 0, 0),
('Vacaciones', 'Empleado en periodo vacacional', 0, 0),
('Incapacidad', 'Empleado en incapacidad médica', 0, 0);

-- =====================================================
-- 2. ROLES
-- =====================================================
INSERT INTO roles (name, description) VALUES
('Administrador', 'Configura horarios, genera reportes y aprueba actas. No puede checar asistencia.'),
('Auxiliar Administrativo', 'Genera reportes y checa asistencia. No puede configurar ni modificar empleados.'),
('Sistemas', 'Acceso total excepto aprobación de actas.'),
('Empleado', 'Puede checar asistencia y consultar su historial.');

-- =====================================================
-- 3. EMPLEADO ADMINISTRADOR INICIAL
-- =====================================================
INSERT INTO employees (
    names, surname1, surname2,
    email, phone,
    id_role, status_id,
    hire_date, vacation_days, vacation_used,
    created_at
) VALUES (
    'Oscar Armando', 'Navarro', 'González',
    'oscar.navarro@credibueno.mx', '8711114823',
    1, 1,  -- Rol Administrador, Estatus Activo
    '2020-01-15', 22, 0,  -- Fecha de ingreso + vacaciones por antigüedad
    NOW()
);

-- =====================================================
-- 4. USUARIO ADMINISTRADOR
-- =====================================================
-- Nota: Reemplazar el hash si se desea otro password.
--       Actual: password = 'admin1839'
INSERT INTO users (id_employee, username, password, created_at)
VALUES (
    1,
    'admin',
    '$2y$10$na1ig/JMFiWojW5RuGt7O.RbW4CNfj4N1sWC5rACpkQUZrnH4khFi',
    NOW()
);