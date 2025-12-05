PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS booking_details;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS work_schedule;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS employees;

CREATE TABLE employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    phone TEXT,
    commission_percent REAL NOT NULL CHECK (commission_percent >= 0 AND commission_percent <= 100),
    is_active INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0, 1))
);

CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    duration_minutes INTEGER NOT NULL CHECK (duration_minutes > 0),
    price REAL NOT NULL CHECK (price >= 0)
);

CREATE TABLE work_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    shift_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT
);

CREATE TABLE bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    client_name TEXT NOT NULL,
    client_phone TEXT,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    status TEXT NOT NULL DEFAULT 'scheduled' CHECK (status IN ('scheduled', 'completed', 'cancelled')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT
);

CREATE TABLE booking_details (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    actual_price REAL NOT NULL CHECK (actual_price >= 0),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

INSERT INTO employees (full_name, phone, commission_percent, is_active) VALUES
('Рыжкин Владислав Дмитриевич', '+79610996848', 30.0, 1),
('Четайкин Владислав Александрович', '+79271970445', 35.0, 1),
('Шарунов Максим Игоревич', '+79809534935', 25.0, 0);

INSERT INTO services (title, duration_minutes, price) VALUES
('Замена масла', 30, 1500.00),
('Диагностика ходовой', 45, 2000.00),
('Шиномонтаж (4 колеса)', 60, 3000.00),
('Замена тормозных колодок', 90, 2500.00);

INSERT INTO work_schedule (employee_id, shift_date, start_time, end_time) VALUES
(1, '2023-10-25', '09:00', '18:00'),
(2, '2023-10-25', '10:00', '19:00'),
(1, '2023-10-26', '09:00', '18:00');

INSERT INTO bookings (employee_id, client_name, client_phone, booking_date, start_time, status) VALUES
(1, 'Василий Паркаев', '+79271729373', '2023-10-25', '10:00', 'completed');

INSERT INTO bookings (employee_id, client_name, client_phone, booking_date, start_time, status) VALUES
(2, 'Сергей Маклаков', '+79221758200', '2023-10-25', '14:00', 'scheduled');

INSERT INTO booking_details (booking_id, service_id, actual_price) VALUES
(1, 1, 1500.00),
(1, 2, 2000.00);

INSERT INTO booking_details (booking_id, service_id, actual_price) VALUES
(2, 3, 3000.00);

SELECT
    e.full_name,
    SUM(bd.actual_price) * (e.commission_percent / 100) as salary
FROM employees e
JOIN bookings b ON e.id = b.employee_id
JOIN booking_details bd ON b.id = bd.booking_id
WHERE b.status = 'completed' AND b.booking_date BETWEEN '2023-10-01' AND '2023-10-31'
GROUP BY e.id;