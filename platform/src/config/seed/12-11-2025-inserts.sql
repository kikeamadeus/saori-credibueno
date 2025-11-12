-- =====================================================
-- Proyecto: SAORI - CREDIBUENO
-- Versión: v1.0.1 (Fase 1)
-- Fecha: 2025-11-14
-- Descripción:
--   Datos iniciales (seed) para entorno Credibueno.
--   Incluye estatus, roles, áreas, sucursales y usuario inicial.
-- =====================================================

USE arcobit1_saoricb1;

-- =====================================================
-- 1. ESTATUS (statuses)
-- =====================================================
INSERT INTO statuses (name, description, can_login, can_checkin)
VALUES
('Activo', 'Empleado con acceso total al sistema.', 1, 1),
('Suspendido', 'Empleado temporalmente suspendido.', 0, 0),
('Baja', 'Empleado dado de baja del sistema.', 0, 0),
('Vacaciones', 'Empleado en periodo vacacional.', 0, 0),
('Incapacidad', 'Empleado en incapacidad médica.', 0, 0);

-- =====================================================
-- 2. ROLES (roles)
-- =====================================================
INSERT INTO roles (name, description) VALUES
('Administrador', 'Configura horarios, genera reportes y aprueba actas. No puede checar asistencia.'),
('Auxiliar Administrativo', 'Genera reportes y checa asistencia. No puede modificar empleados.'),
('Sistemas', 'Acceso total excepto aprobación de actas.'),
('Empleado', 'Puede checar asistencia y consultar su historial.');

-- =====================================================
-- 3. ÁREAS (areas)
-- =====================================================
INSERT INTO areas (name, created_at)
VALUES
('Administración', NOW()),
('Sistemas', NOW()),
('Operaciones', NOW());

-- =====================================================
-- 4. SUCURSALES (branches)
-- =====================================================
INSERT INTO branches (
    name,
    address,
    city,
    state,
    zip_code,
    latitude,
    longitude,
    checkin_radius_meters,
    created_at
) VALUES
('Torreón',
 'Avenida Morelos #1011, Colonia Centro. Torreón, Coahuila C.P.: 27000',
 'Torreón', 'Coahuila', '27000',
 25.5392898, -103.4628589, 300, NOW()),
('Gómez Palacio',
 'Madero 408 Local 3, esquina con Degollado, Colonia Centro. Gómez Palacio, Durango C.P.: 35000',
 'Gómez Palacio', 'Durango', '35000',
 25.5644138, -103.4968769, 400, NOW()),
('Durango',
 'Calle Francisco Zarco #321, Colonia Centro, Durango, Durango C.P.: 34000',
 'Durango', 'Durango', '34000',
 24.0246569, -104.6649181, 400, NOW());

-- =====================================================
-- 5. EMPLEADO ADMINISTRADOR INICIAL
-- =====================================================
INSERT INTO employees (
    names, surname1, surname2,
    email, phone,
    id_area, id_branch, can_check_all,
    id_role, status_id,
    created_at
) VALUES (
    'Oscar Armando', 'Navarro', 'González',
    'oscar.navarro@credibueno.mx', '8711114823',
    1, 1, 1,
    1, 1,
    NOW()
);

-- =====================================================
-- 6. USUARIO ADMINISTRADOR
-- Nota: Password hash generado con password_hash('admin1839', PASSWORD_BCRYPT)
-- =====================================================
INSERT INTO users (id_employee, username, password, created_at)
VALUES (
    1,
    'admin',
    '$2y$10$na1ig/JMFiWojW5RuGt7O.RbW4CNfj4N1sWC5rACpkQUZrnH4khFi',
    NOW()
);