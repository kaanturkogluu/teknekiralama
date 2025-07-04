-- Kullanıcı rolleri
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Kullanıcılar
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255), -- Google ile girişte null olabilir
    google_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Şehirler
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Kalkış noktaları
CREATE TABLE departure_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

-- Tekneler
CREATE TABLE boats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    city_id INT NOT NULL,
    departure_point_id INT NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    featured BOOLEAN DEFAULT 0,
    instant_booking BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id),
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (departure_point_id) REFERENCES departure_points(id)
);

-- Tekne resimleri
CREATE TABLE boat_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    boat_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (boat_id) REFERENCES boats(id)
);

-- Rezervasyonlar
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    boat_id INT NOT NULL,
    customer_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    special_package_id INT,
    person_count INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (boat_id) REFERENCES boats(id),
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (special_package_id) REFERENCES packages(id)
);

-- Blog yazıları
CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id)
);

-- Özel gün/paketler
CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    is_active BOOLEAN DEFAULT 1
);

-- Favoriler
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    boat_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (customer_id, boat_id),
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (boat_id) REFERENCES boats(id)
);

-- Ödemeler
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','paid','failed') DEFAULT 'pending',
    FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);

-- Başlangıç rolleri
INSERT INTO roles (name) VALUES ('admin'), ('owner'), ('customer'); 