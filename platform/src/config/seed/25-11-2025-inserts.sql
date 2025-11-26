-- =====================================================
-- Proyecto: SAORI - CREDIBUENO
-- Versión: v1.0.2 (Fase 1)
-- Fecha: 2025-11-14
-- Descripción:
--   Carga inicial (seed) para entorno Credibueno.
--   Incluye áreas, sucursales, roles, estatus y usuario
--   administrador.
-- =====================================================

USE arcobit1_saoricb1;

-- =====================================================
-- 1. ÁREAS ORGANIZACIONALES
-- =====================================================
INSERT INTO areas (name, created_at) VALUES
('Administración', NOW()),
('Operaciones', NOW()),
('Sistemas', NOW());

-- =====================================================
-- 2. SUCURSALES
-- =====================================================
INSERT INTO branches (name, address, city, state, zip_code, latitude, longitude, checkin_radius_meters, is_active, created_at)
VALUES
('Torreón',
 'Avenida Morelos #1011, Colonia Centro. Torreón, Coahuila',
 'Torreón', 'Coahuila', '27000',
 25.5392898, -103.4628589, 300, 1, NOW()),

('Gómez Palacio',
 'Madero 408 Local 3, esquina con Degollado, Colonia Centro. Gómez Palacio, Durango',
 'Gómez Palacio', 'Durango', '35000',
 25.5644138, -103.4968769, 400, 1,  NOW()),

('Durango',
 'Calle Francisco Zarco #321, Colonia Centro, Durango, Durango',
 'Durango', 'Durango', '34000',
 24.0246569, -104.6649181, 400, 1, NOW());

-- =====================================================
-- 3. ESTATUS DE EMPLEADOS
-- =====================================================
INSERT INTO statuses (name, description, can_login, can_checkin, created_at)
VALUES
('Activo', 'Empleado con acceso al sistema y capacidad de checar asistencia.', 1, 1, NOW()),
('Suspendido', 'Empleado temporalmente inactivo, no puede iniciar sesión ni checar.', 0, 0, NOW()),
('Baja', 'Empleado dado de baja permanentemente.', 0, 0, NOW()),
('Vacaciones', 'Empleado de vacaciones, no puede iniciar sesión ni checar asistencia.', 0, 0, NOW()),
('Incapacidad', 'Empleado con incapacidad médica, no puede iniciar sesión ni checar.', 0, 0, NOW());

-- =====================================================
-- 4. ROLES DE SISTEMA
-- =====================================================
INSERT INTO roles (name, description, created_at)
VALUES
('Administrador', 'Configura horarios, genera reportes y aprueba actas. No puede checar asistencia.', NOW()),
('Auxiliar Administrativo', 'Genera reportes y checa asistencia. No puede modificar ni configurar empleados.', NOW()),
('Sistemas', 'Acceso total excepto aprobación de actas administrativas.', NOW()),
('Empleado', 'Puede checar asistencia y consultar su historial.', NOW());

-- =====================================================
-- 5. EMPLEADO ADMINISTRADOR INICIAL
-- =====================================================
INSERT INTO employees (
    names, surname1, surname2,
    email, phone,
    id_area, id_branch, can_check_all,
    id_role, status_id,
    hire_date, created_at
) VALUES (
    'Oscar Armando', 'Navarro', 'González',
    'oscar.navarro@credibueno.mx', '8711114823',
    1, 1, 0,           -- Área Administración, Sucursal Torreón, no puede checar en todas
    1, 1,              -- Rol Administrador, Estatus Activo
    '2022-05-10', NOW()
);

-- =====================================================
-- 6. USUARIO ADMINISTRADOR
-- Nota: Contraseña original → 'admin1839'
--       Generada con password_hash('admin1839', PASSWORD_BCRYPT);
-- =====================================================
INSERT INTO users (id_employee, username, password, created_at)
VALUES (
    1,
    'admin',
    '$2y$10$na1ig/JMFiWojW5RuGt7O.RbW4CNfj4N1sWC5rACpkQUZrnH4khFi',
    NOW()
);

-- =====================================================
-- 7. PERMISOS DEL SISTEMA
-- Descripción:
--   Lista inicial de permisos manejados por Web y App.
-- =====================================================

INSERT INTO permissions (permission_key, description) VALUES
('register_attendance', 'Permite registrar asistencia (entrada/salida).');
('view_branches', 'Puede ver el módulo de sucursales'),
('view_employees', 'Puede ver el módulo de empleados');

-- =====================================================
-- 8. ASIGNACIÓN DE PERMISOS A ROLES
-- Descripción:
--   Define qué roles tienen acceso a registrar asistencia.
--   Rol 1 (Administrador) → NO puede checar
--   Roles 2, 3, 4 → SÍ pueden checar
-- =====================================================

-- Obtener ID del permiso
SET @perm_register_attendance = (
    SELECT id FROM permissions WHERE permission_key = 'register_attendance'
);

-- Rol 2: Auxiliar Administrativo
INSERT INTO role_permissions (role_id, permission_id, created_at)
VALUES (2, @perm_register_attendance, NOW());

-- Rol 3: Sistemas
INSERT INTO role_permissions (role_id, permission_id, created_at)
VALUES (3, @perm_register_attendance, NOW());

-- Rol 4: Empleado
INSERT INTO role_permissions (role_id, permission_id, created_at)
VALUES (4, @perm_register_attendance, NOW());
