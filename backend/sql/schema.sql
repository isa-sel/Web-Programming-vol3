CREATE DATABASE IF NOT EXISTS rukometni_centar;
USE rukometni_centar;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    founded_year INT NULL,
    category VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birth_date DATE NULL,
    position VARCHAR(50) NOT NULL,
    jersey_number INT NOT NULL,
    team_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_players_team FOREIGN KEY (team_id) REFERENCES teams(id)
);

CREATE TABLE venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    venue_id INT NOT NULL,
    match_date DATETIME NOT NULL,
    home_score INT DEFAULT 0,
    away_score INT DEFAULT 0,
    status VARCHAR(30) DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_match_home_team FOREIGN KEY (home_team_id) REFERENCES teams(id),
    CONSTRAINT fk_match_away_team FOREIGN KEY (away_team_id) REFERENCES teams(id),
    CONSTRAINT fk_match_venue FOREIGN KEY (venue_id) REFERENCES venues(id),
    CONSTRAINT chk_different_teams CHECK (home_team_id <> away_team_id)
);

CREATE TABLE standings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    season VARCHAR(20) NOT NULL,
    played INT DEFAULT 0,
    wins INT DEFAULT 0,
    draws INT DEFAULT 0,
    losses INT DEFAULT 0,
    goals_for INT DEFAULT 0,
    goals_against INT DEFAULT 0,
    points INT DEFAULT 0,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_standings_team FOREIGN KEY (team_id) REFERENCES teams(id)
);

INSERT INTO roles (name, description) VALUES
('admin', 'System administrator'),
('user', 'Standard application user');

INSERT INTO teams (name, city, founded_year, category) VALUES
('RK Bosna', 'Sarajevo', 1948, 'Senior Men'),
('HRK Izviđač', 'Ljubuški', 1956, 'Senior Men'),
('RK Krivaja', 'Zavidovići', 1952, 'Senior Men'),
('ŽRK Hadžići', 'Hadžići', 1972, 'Women');

INSERT INTO users (first_name, last_name, email, password_hash, role_id) VALUES
('Admin', 'User', 'admin@rukometnicentar.com', '$2y$10$examplehashreplace', 1),
('Regular', 'User', 'user@rukometnicentar.com', '$2y$10$examplehashreplace', 2);

INSERT INTO venues (name, city, address, capacity) VALUES
('Dvorana Skenderija', 'Sarajevo', 'Terezija bb, Sarajevo', 6000),
('Gradska dvorana Zavidovići', 'Zavidovići', 'Radnička 3, Zavidovići', 2500),
('Sportska dvorana Hadžići', 'Hadžići', 'Zlatnih ljiljana 24, Hadžići', 1800);
