CREATE DATABASE IF NOT EXISTS rijdwerk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rijdwerk;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS request_status_log, request_notes, requests, car_features, features, car_photos, cars, models, brands, admins, rate_limits, rdw_cache;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE brands (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL,
    slug VARCHAR(60) NOT NULL,
    UNIQUE KEY uq_brands_name (name),
    UNIQUE KEY uq_brands_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE models (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand_id SMALLINT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL,
    UNIQUE KEY uq_models_brand_slug (brand_id, slug),
    CONSTRAINT fk_models_brand FOREIGN KEY (brand_id) REFERENCES brands (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cars (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(160) NOT NULL,
    model_id SMALLINT UNSIGNED NOT NULL,
    variant VARCHAR(120) NOT NULL DEFAULT '',
    plate CHAR(6) NULL,
    price INT UNSIGNED NOT NULL,
    year SMALLINT UNSIGNED NOT NULL,
    first_registration DATE NULL,
    mileage_km INT UNSIGNED NOT NULL,
    fuel ENUM('petrol','diesel','hybrid','phev','electric','lpg') NOT NULL,
    transmission ENUM('manual','automatic') NOT NULL,
    body_type ENUM('hatchback','sedan','estate','suv','mpv','coupe','convertible') NOT NULL,
    color VARCHAR(40) NOT NULL DEFAULT '',
    power_kw SMALLINT UNSIGNED NULL,
    engine_cc SMALLINT UNSIGNED NULL,
    battery_kwh DECIMAL(5,1) NULL,
    range_km SMALLINT UNSIGNED NULL,
    doors TINYINT UNSIGNED NULL,
    seats TINYINT UNSIGNED NULL,
    owners TINYINT UNSIGNED NULL,
    apk_until DATE NULL,
    warranty_months TINYINT UNSIGNED NOT NULL DEFAULT 12,
    description TEXT NOT NULL,
    status ENUM('available','reserved','sold') NOT NULL DEFAULT 'available',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    view_count INT UNSIGNED NOT NULL DEFAULT 0,
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    sold_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cars_slug (slug),
    UNIQUE KEY uq_cars_plate (plate),
    KEY idx_cars_listing (is_published, status, published_at),
    KEY idx_cars_price (price),
    KEY idx_cars_year (year),
    KEY idx_cars_mileage (mileage_km),
    KEY idx_cars_model (model_id),
    CONSTRAINT fk_cars_model FOREIGN KEY (model_id) REFERENCES models (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE car_photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_id INT UNSIGNED NOT NULL,
    filename CHAR(37) NOT NULL,
    width SMALLINT UNSIGNED NOT NULL,
    height SMALLINT UNSIGNED NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_car_photos_filename (filename),
    KEY idx_car_photos_order (car_id, sort_order),
    CONSTRAINT fk_car_photos_car FOREIGN KEY (car_id) REFERENCES cars (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE features (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    category ENUM('comfort','safety','multimedia','exterior') NOT NULL,
    UNIQUE KEY uq_features_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE car_features (
    car_id INT UNSIGNED NOT NULL,
    feature_id SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (car_id, feature_id),
    CONSTRAINT fk_car_features_car FOREIGN KEY (car_id) REFERENCES cars (id) ON DELETE CASCADE,
    CONSTRAINT fk_car_features_feature FOREIGN KEY (feature_id) REFERENCES features (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(20) NOT NULL,
    type ENUM('test_drive','question') NOT NULL,
    car_id INT UNSIGNED NULL,
    car_label VARCHAR(200) NOT NULL DEFAULT '',
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NOT NULL DEFAULT '',
    preferred_date DATE NULL,
    preferred_time CHAR(5) NULL,
    message TEXT NOT NULL,
    status ENUM('new','contacted','scheduled','completed','cancelled') NOT NULL DEFAULT 'new',
    consent_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_requests_reference (reference),
    KEY idx_requests_status (status, created_at),
    KEY idx_requests_type (type, created_at),
    CONSTRAINT fk_requests_car FOREIGN KEY (car_id) REFERENCES cars (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE request_notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    admin_id INT UNSIGNED NULL,
    body TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_request_notes_request (request_id),
    CONSTRAINT fk_request_notes_request FOREIGN KEY (request_id) REFERENCES requests (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE request_status_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    admin_id INT UNSIGNED NULL,
    from_status VARCHAR(20) NULL,
    to_status VARCHAR(20) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_request_status_log_request (request_id),
    CONSTRAINT fk_request_status_log_request FOREIGN KEY (request_id) REFERENCES requests (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rate_limits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bucket VARCHAR(40) NOT NULL,
    key_hash CHAR(64) NOT NULL,
    created_at DATETIME NOT NULL,
    KEY idx_rate_limits_lookup (bucket, key_hash, created_at),
    KEY idx_rate_limits_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rdw_cache (
    plate CHAR(6) PRIMARY KEY,
    payload JSON NOT NULL,
    fetched_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
