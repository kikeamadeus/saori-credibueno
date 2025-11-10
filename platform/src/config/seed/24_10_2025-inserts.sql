-- =====================================================
-- INSERTS INICIALES PARA BITORA
-- Versión: v1.0.0
-- Fecha: 2025-10-24
-- =====================================================

-- 1. ÁREAS
INSERT INTO areas (name, created_at)
VALUES ('Administración', NOW());

-- 2. DEPARTAMENTOS
INSERT INTO departments (name, id_area, created_at)
VALUES ('Administración', 1, NOW());

-- 3. PUESTOS
INSERT INTO positions (name, id_department, created_at)
VALUES ('Administrador', 1, NOW());

-- 4. ESTATUS (para empleados)
INSERT INTO statuses (name, description)
VALUES
('activo', 'empleados'),
('inactivo', 'empleados');

-- 5. EMPLEADO ADMINISTRADOR
INSERT INTO employees (
    names, surname1, surname2, 
    id_department, id_position, 
    email, phone, rfc, curp, birth_date,
    street, exterior_number, interior_number,
    settlement, municipality, state, zip_code,
    created_at, status_id
) VALUES (
    'Juan', 'Gutierrez', 'Arizpe',
    1, 1,
    'juan.arizpe@gmail.com', '8717903366',
    NULL, NULL, '1990-09-19',
    'Paseo la Rosita', '1020', NULL,
    'Campestre la Rosita', 'Torreón', 'Coahuila', '27000',
    NOW(), 1
);

-- 6. USUARIO ADMINISTRADOR
-- Nota: Reemplazar el hash con el generado desde PHP usando password_hash('admin1839', PASSWORD_BCRYPT)
INSERT INTO users (id_employee, username, password, created_at)
VALUES (
    1, 'admin',
    '$2y$10$na1ig/JMFiWojW5RuGt7O.RbW4CNfj4N1sWC5rACpkQUZrnH4khFi',
    NOW()
);