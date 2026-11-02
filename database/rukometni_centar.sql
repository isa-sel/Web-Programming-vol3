CREATE DATABASE IF NOT EXISTS rukometni_centar
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rukometni_centar;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    is_active TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles (id),
    CONSTRAINT chk_users_is_active CHECK (is_active IN (0, 1))
) ENGINE=InnoDB;

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    founded_year INT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_teams_name_city UNIQUE (name, city),
    CONSTRAINT chk_teams_founded_year CHECK (
        founded_year IS NULL OR founded_year BETWEEN 1800 AND 2100
    )
) ENGINE=InnoDB;

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birth_date DATE NULL,
    position VARCHAR(50) NOT NULL,
    jersey_number INT NOT NULL,
    team_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_players_team_jersey UNIQUE (team_id, jersey_number),
    CONSTRAINT fk_players_team FOREIGN KEY (team_id) REFERENCES teams (id),
    CONSTRAINT chk_players_jersey_number CHECK (jersey_number BETWEEN 1 AND 99)
) ENGINE=InnoDB;

CREATE TABLE venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_venues_name_city UNIQUE (name, city),
    CONSTRAINT chk_venues_capacity CHECK (capacity > 0)
) ENGINE=InnoDB;

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    venue_id INT NOT NULL,
    match_date DATETIME NOT NULL,
    home_score INT UNSIGNED NULL,
    away_score INT UNSIGNED NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'scheduled',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_matches_home_team FOREIGN KEY (home_team_id) REFERENCES teams (id),
    CONSTRAINT fk_matches_away_team FOREIGN KEY (away_team_id) REFERENCES teams (id),
    CONSTRAINT fk_matches_venue FOREIGN KEY (venue_id) REFERENCES venues (id),
    CONSTRAINT chk_matches_different_teams CHECK (home_team_id <> away_team_id),
    CONSTRAINT chk_matches_status CHECK (
        status IN ('scheduled', 'completed', 'postponed', 'cancelled')
    ),
    CONSTRAINT chk_matches_completed_score CHECK (
        status <> 'completed' OR (home_score IS NOT NULL AND away_score IS NOT NULL)
    )
) ENGINE=InnoDB;

CREATE TABLE standings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    season VARCHAR(20) NOT NULL,
    played INT NOT NULL DEFAULT 0,
    wins INT NOT NULL DEFAULT 0,
    draws INT NOT NULL DEFAULT 0,
    losses INT NOT NULL DEFAULT 0,
    goals_for INT NOT NULL DEFAULT 0,
    goals_against INT NOT NULL DEFAULT 0,
    points INT NOT NULL DEFAULT 0,
    position INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_standings_team_season UNIQUE (team_id, season),
    CONSTRAINT fk_standings_team FOREIGN KEY (team_id) REFERENCES teams (id),
    CONSTRAINT chk_standings_counts CHECK (
        played >= 0 AND wins >= 0 AND draws >= 0 AND losses >= 0
        AND played = wins + draws + losses
    ),
    CONSTRAINT chk_standings_goals_points CHECK (
        goals_for >= 0 AND goals_against >= 0 AND points = wins * 2 + draws
    ),
    CONSTRAINT chk_standings_position CHECK (position > 0)
) ENGINE=InnoDB;

INSERT INTO roles (id, name, description) VALUES
    (1, 'admin', 'Full league administration access'),
    (2, 'manager', 'Club and match management access'),
    (3, 'user', 'Standard registered user');

INSERT INTO users (id, first_name, last_name, email, password_hash, role_id, is_active) VALUES
    (1, 'Admin', 'User', 'admin@rukometnicentar.com',
     '$2y$10$atbWwQW.vbU.wkhWYGipSOaAnZpht5a3aIJy.gdeTITsAFiqHnQDa', 1, 1),
    (2, 'Lejla', 'Hadžić', 'manager@rukometnicentar.com',
     '$2y$10$c7OEPN/qTGooKyUm/0wbKe.XUmg3WLr0DG7V0NtvqdKs4XZScMF7G', 2, 1),
    (3, 'Amir', 'Kovač', 'user@rukometnicentar.com',
     '$2y$10$CfbJ66h9tsB6miNXf30W5u9KYlwmnvwSk5F5i2Wo6xXWHkiISJm52', 3, 1);

INSERT INTO teams (id, name, city, founded_year, category) VALUES
    (1, 'RK Bosna', 'Sarajevo', 1948, 'Senior Men'),
    (2, 'HRK Izviđač', 'Ljubuški', 1956, 'Senior Men'),
    (3, 'RK Krivaja', 'Zavidovići', 1952, 'Senior Men'),
    (4, 'ŽRK Hadžići', 'Hadžići', 1972, 'Senior Women'),
    (5, 'ŽRK Goražde', 'Goražde', 1980, 'Senior Women');

INSERT INTO venues (id, name, city, address, capacity) VALUES
    (1, 'Dvorana Skenderija', 'Sarajevo', 'Terezija bb, Sarajevo', 6000),
    (2, 'Gradska dvorana Zavidovići', 'Zavidovići', 'Radnička 3, Zavidovići', 2500),
    (3, 'Sportska dvorana Hadžići', 'Hadžići', 'Zlatnih ljiljana 24, Hadžići', 1800);

INSERT INTO players (id, first_name, last_name, birth_date, position, jersey_number, team_id) VALUES
    (1, 'Admir', 'Mehmedović', '1998-04-12', 'Centre back', 10, 1),
    (2, 'Haris', 'Delić', '2000-08-03', 'Goalkeeper', 1, 1),
    (3, 'Emir', 'Preldžić', '2001-02-19', 'Right wing', 7, 2),
    (4, 'Ivan', 'Pavlović', '1997-11-25', 'Left back', 9, 2),
    (5, 'Tarik', 'Alić', '1999-06-14', 'Pivot', 17, 3),
    (6, 'Nedim', 'Musić', '2002-01-30', 'Goalkeeper', 16, 3),
    (7, 'Selma', 'Hasanbegović', '2000-09-10', 'Goalkeeper', 1, 4),
    (8, 'Amina', 'Čengić', '2001-03-22', 'Left wing', 11, 4),
    (9, 'Mirela', 'Bećirović', '1999-07-18', 'Centre back', 8, 5),
    (10, 'Dina', 'Hodžić', '2002-05-09', 'Right wing', 14, 5);

INSERT INTO matches (
    id, home_team_id, away_team_id, venue_id, match_date, home_score, away_score, status
) VALUES
    (1, 1, 2, 1, '2026-10-24 18:00:00', 31, 28, 'completed'),
    (2, 3, 1, 2, '2026-11-07 19:00:00', NULL, NULL, 'scheduled'),
    (3, 2, 3, 2, '2026-10-31 17:30:00', 26, 26, 'completed'),
    (4, 4, 5, 3, '2026-11-14 16:00:00', NULL, NULL, 'scheduled');

INSERT INTO standings (
    id, team_id, season, played, wins, draws, losses,
    goals_for, goals_against, points, position
) VALUES
    (1, 1, '2026-2027', 6, 5, 1, 0, 172, 145, 11, 1),
    (2, 2, '2026-2027', 6, 4, 1, 1, 168, 149, 9, 2),
    (3, 3, '2026-2027', 6, 3, 2, 1, 162, 152, 8, 3),
    (4, 4, '2026-2027', 6, 2, 0, 4, 141, 160, 4, 4),
    (5, 5, '2026-2027', 6, 1, 1, 4, 136, 158, 3, 5);
