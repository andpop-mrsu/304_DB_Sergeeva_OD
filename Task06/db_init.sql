DROP TABLE IF EXISTS performed_services;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS service_categories;
DROP TABLE IF EXISTS specialties;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS patients;

CREATE TABLE specialties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE doctors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    specialty_id INTEGER NOT NULL,
    salary_percentage REAL NOT NULL CHECK(salary_percentage >= 0 AND salary_percentage <= 100),
    hire_date DATE NOT NULL DEFAULT (date('now')),
    dismissal_date DATE,
    phone TEXT,
    email TEXT,
    CHECK(dismissal_date IS NULL OR dismissal_date >= hire_date),
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE RESTRICT
);

CREATE TABLE service_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    duration_minutes INTEGER NOT NULL CHECK(duration_minutes > 0),
    price REAL NOT NULL CHECK(price >= 0),
    category_id INTEGER NOT NULL,
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT,
    UNIQUE(name, category_id)
);

CREATE TABLE patients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT,
    birth_date DATE
);

CREATE TABLE appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INTEGER NOT NULL,
    doctor_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status TEXT NOT NULL DEFAULT 'ожидание' CHECK(status IN ('ожидание', 'завершено', 'отменено')),
    created_at DATETIME NOT NULL DEFAULT (datetime('now')),
    notes TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

CREATE TABLE performed_services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER,
    doctor_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    performed_date DATE NOT NULL DEFAULT (date('now')),
    performed_time TIME NOT NULL,
    actual_duration_minutes INTEGER NOT NULL CHECK(actual_duration_minutes > 0),
    actual_price REAL NOT NULL CHECK(actual_price >= 0),
    notes TEXT,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);



INSERT INTO specialties (name) VALUES
('Терапевт'),
('Хирург'),
('Ортодонт');

INSERT INTO doctors (name, specialty_id, salary_percentage, hire_date, dismissal_date, phone, email) VALUES
('Иванова Анна Петровна', 1, 25.0, '2023-01-10', NULL, '+7-900-111-11-11', 'anna@example.com'),
('Петров Сергей Иванович', 2, 30.0, '2023-02-01', NULL, '+7-900-222-22-22', 'sergey@example.com'),
('Сидорова Марина Викторовна', 3, 20.0, '2023-03-05', NULL, '+7-900-333-33-33', 'marina@example.com'),
('Илюхин Петр Николаевич', 3, 27.0, '2023-04-10', NULL, '+7-900-444-44-44', 'iluhin@example.com'),
('Николаев Константин Денисович', 1, 35.0, '2024-01-27', NULL, '+7-900-555-55-55', 'nikolaev@example.com');

INSERT INTO service_categories (name, description) VALUES
('Терапевтическая стоматология', 'Лечение кариеса, пломбирование'),
('Хирургическая стоматология', 'Удаление зубов, операции'),
('Имплантация', 'Установка зубных имплантов');

INSERT INTO services (name, duration_minutes, price, category_id, description) VALUES
('Пломбирование', 30, 2000.0, 1, 'Лечение кариеса и установка пломбы'),
('Удаление зуба', 45, 3500.0, 2, 'Хирургическое удаление зуба'),
('Установка импланта', 120, 15000.0, 3, 'Имплантация зуба');

INSERT INTO patients (name, phone, birth_date) VALUES
('Смирнов Алексей', '+7-901-123-45-67', '1990-05-10'),
('Любавин Илья', '+7-902-123-45-67', '1980-02-21'),
('Кирилова Анастасия', '+7-903-123-45-67', '2001-09-09'),
('Горшков Ярослав', '+7-904-123-45-67', '2005-04-18'),
('Платонова Елизавета', '+7-905-123-45-67', '1999-02-06'),
('Кузнецова Елена', '+7-906-123-45-67', '1985-09-15');

INSERT INTO appointments (patient_id, doctor_id, service_id, appointment_date, appointment_time, status, notes) VALUES
(1, 1, 1, '2025-11-29', '10:00', 'ожидание', 'Пациент просил процедуру под седацией'),
(2, 2, 2, '2025-12-21', '12:00', 'ожидание', NULL),
(3, 3, 3, '2025-12-06', '11:00', 'ожидание', 'Проверить записи с прошлого посещения');

INSERT INTO performed_services (appointment_id, doctor_id, service_id, performed_date, performed_time, actual_duration_minutes, actual_price, notes) VALUES
(1, 1, 1, '2024-12-20', '10:30', 35, 2000.0, 'Процедура прошла успешно'),
(NULL, 2, 2, '2025-04-18', '11:00', 50, 3500.0, 'Без предварительной записи'),
(3, 3, 3, '2024-06-24', '12:30', 120, 5000.0, 'Придет повторно');