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
INSERT INTO branches (
    name, address, city, state, zip_code,
    latitude, longitude,
    checkin_radius_meters, is_active, created_at
) VALUES
('Torreón',
 'Avenida Morelos #1011, Colonia Centro. Torreón, Coahuila',
 'Torreón', 'Coahuila', '27000',
 25.5392898, -103.4628589,
 300, 1, NOW()
),
('Gómez Palacio',
 'Madero 408 Local 3, esquina con Degollado, Colonia Centro. Gómez Palacio, Durango',
 'Gómez Palacio', 'Durango', '35000',
 25.5644138, -103.4968769,
 400, 1, NOW()
),
('Durango',
 'Calle Zarco #321, Colonia Centro. Durango, Durango',
 'Durango', 'Durango', '34000',
 24.0246569, -104.6649181,
 400, 1, NOW()
);

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
('Asistente Administrativo', 'Reporte y asistencia limitada.', NOW()),
('Sistemas', 'Acceso general excepto aprobar actas.', NOW()),
('Empleado', 'Solo checa asistencia.', NOW());

-- =====================================================
-- 5. EMPLEADO INICIAL (Oscar)
-- =====================================================
INSERT INTO employees (
    names, surname1, surname2,
    email, phone,
    id_area, id_branch, id_role, status_id,
    can_check_all, tolerance_minutes,
    hire_date, created_at
) VALUES (
    'Oscar Armando', 'Navarro', 'González',
    'oscar.navarro@credibueno.mx', '8711114823',
    1, 1, 1, 1,
    0, 15,
    '2022-05-10', NOW()
);

-- =====================================================
-- 6. HORARIO INICIAL (schedules) PARA EMPLEADO 1
--    Estándar:
--    Lunes-Viernes: 08:30–14:00 / 16:00–18:00
--    Sábado:        09:00–14:00 (sin comida)
--    Domingo:       Descanso (todo NULL)
-- =====================================================
INSERT INTO schedules (
    employee_id, day_of_week,
    entry_time, lunch_out_time, lunch_in_time, exit_time,
    created_at
) VALUES
-- Lunes (1)
(1, 1, '08:30:00', '14:00:00', '16:00:00', '18:00:00', NOW()),
-- Martes (2)
(1, 2, '08:30:00', '14:00:00', '16:00:00', '18:00:00', NOW()),
-- Miércoles (3)
(1, 3, '08:30:00', '14:00:00', '16:00:00', '18:00:00', NOW()),
-- Jueves (4)
(1, 4, '08:30:00', '14:00:00', '16:00:00', '18:00:00', NOW()),
-- Viernes (5)
(1, 5, '08:30:00', '14:00:00', '16:00:00', '18:00:00', NOW()),
-- Sábado (6) - solo entrada/salida
(1, 6, '09:00:00', NULL, NULL, '14:00:00', NOW()),
-- Domingo (7) - descanso
(1, 7, NULL, NULL, NULL, NULL, NOW());

-- =====================================================
-- 7. USUARIO ADMINISTRADOR
-- =====================================================
INSERT INTO users (id_employee, username, password, created_at)
VALUES (
    1,
    'admin',
    '$2y$10$na1ig/JMFiWojW5RuGt7O.RbW4CNfj4N1sWC5rACpkQUZrnH4khFi',
    NOW()
);

-- =====================================================
-- 8. PERMISOS
-- =====================================================
INSERT INTO permissions (permission_key, description) VALUES
('register_attendance', 'Permite registrar asistencia.'),
('view_branches', 'Puede ver sucursales.'),
('view_employees', 'Puede ver empleados.');

-- =====================================================
-- 9. ASIGNAR PERMISOS
-- =====================================================

SET @perm_register_attendance = (SELECT id FROM permissions WHERE permission_key = 'register_attendance');
SET @perm_view_branches      = (SELECT id FROM permissions WHERE permission_key = 'view_branches');
SET @perm_view_employees     = (SELECT id FROM permissions WHERE permission_key = 'view_employees');

-- =====================================================
-- ADMINISTRADOR (rol 1)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id) VALUES
(1, @perm_register_attendance),
(1, @perm_view_branches),
(1, @perm_view_employees);

-- =====================================================
-- ASISTENTE ADMINISTRATIVO (rol 2)
-- SOLO registrar asistencia
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, @perm_register_attendance);

-- =====================================================
-- SISTEMAS (rol 3)
-- Tiene todos los permisos
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id) VALUES
(3, @perm_register_attendance),
(3, @perm_view_branches),
(3, @perm_view_employees);

-- =====================================================
-- EMPLEADO (rol 4)
-- SOLO registrar asistencia
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id) VALUES
(4, @perm_register_attendance);