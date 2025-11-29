USE arcobit1_saoricb1;

-- =====================================================
-- 1. ÁREAS
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
 25.5644138, -103.4968769, 400, 1, NOW()),

('Durango',
 'Calle Zarco #321, Colonia Centro. Durango, Durango',
 'Durango', 'Durango', '34000',
 24.0246569, -104.6649181, 400, 1, NOW());

-- =====================================================
-- 3. ESTATUS
-- =====================================================
INSERT INTO statuses (name, description, can_login, can_checkin, created_at) VALUES
('Activo', 'Empleado con acceso total.', 1, 1, NOW()),
('Suspendido', 'No puede iniciar sesión ni checar.', 0, 0, NOW()),
('Baja', 'Empleado dado de baja.', 0, 0, NOW()),
('Vacaciones', 'No puede checar ni iniciar sesión.', 0, 0, NOW()),
('Incapacidad', 'No puede checar ni iniciar sesión.', 0, 0, NOW());

-- =====================================================
-- 4. ROLES
-- =====================================================
INSERT INTO roles (name, description, created_at) VALUES
('Administrador', 'Control total, no checa asistencia.', NOW()),
('Auxiliar Administrativo', 'Reporte y asistencia limitada.', NOW()),
('Sistemas', 'Acceso general excepto aprobar actas.', NOW()),
('Empleado', 'Solo checa asistencia.', NOW());

-- =====================================================
-- 5. EMPLEADO INICIAL
-- =====================================================
INSERT INTO employees (
    names, surname1, surname2,
    email, phone,
    id_area, id_branch, id_role, status_id,
    can_check_all, tolerance_minutes,
    entry_time_weekday, entry_time_saturday,
    hire_date, created_at
) VALUES (
    'Oscar Armando', 'Navarro', 'González',
    'oscar.navarro@credibueno.mx', '8711114823',
    1, 1, 1, 1,
    0, 15,
    '08:30:00', '09:00:00',
    '2022-05-10', NOW()
);

-- =====================================================
-- 6. USUARIO ADMINISTRADOR
-- =====================================================
INSERT INTO users (id_employee, username, password, created_at)
VALUES (
    1,
    'admin',
    '$2y$10$na1ig/JMFiWojW5RuGt7O.RbW4CNfj4N1sWC5rACpkQUZrnH4khFi',
    NOW()
);

-- =====================================================
-- 7. PERMISOS
-- =====================================================
INSERT INTO permissions (permission_key, description) VALUES
('register_attendance', 'Permite registrar asistencia.'),
('view_branches', 'Puede ver sucursales.'),
('view_employees', 'Puede ver empleados.');

-- =====================================================
-- 8. ASIGNAR PERMISOS
-- =====================================================
SET @perm_register_attendance = (SELECT id FROM permissions WHERE permission_key = 'register_attendance');
SET @perm_view_branches = (SELECT id FROM permissions WHERE permission_key = 'view_branches');
SET @perm_view_employees = (SELECT id FROM permissions WHERE permission_key = 'view_employees');

-- Administrador (rol 1)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(1, @perm_view_branches),
(1, @perm_view_employees);

-- Auxiliar Administrativo (rol 2)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, @perm_register_attendance),
(2, @perm_view_branches),
(2, @perm_view_employees);

-- Sistemas (rol 3)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(3, @perm_register_attendance),
(3, @perm_view_branches),
(3, @perm_view_employees);

-- Empleado (rol 4)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(4, @perm_register_attendance);