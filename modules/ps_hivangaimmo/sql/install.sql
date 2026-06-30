CREATE TABLE IF NOT EXISTS `px_immo_type_immobilier` (
    id_type_immobilier INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS px_immo_profile(
   id_profile INT AUTO_INCREMENT PRIMARY KEY,
   code VARCHAR(50) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `px_immo_region` (
    id_region INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `px_immo_ville` (
    id_ville INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    id_region INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `px_immo_immobilier` (
    id_immobilier INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    surface DECIMAL(15,2),
    prix DECIMAL(20,2) DEFAULT 0,
    is_meuble TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    autres TEXT,
    slug VARCHAR(255) NULL,
    nb_etoiles FLOAT,
    id_ville INT NOT NULL,
    id_type_immobilier INT NOT NULL,
    id_customer INT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `px_immo_img_immobilier` (
    id_img_immobilier INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    position INT DEFAULT 0,
    cover TINYINT(1) DEFAULT 0,
    id_immobilier INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `px_immo_piece` (
    id_piece INT AUTO_INCREMENT PRIMARY KEY,
    nb_chambre INT DEFAULT 0,
    nb_toilette INT DEFAULT 0,
    nb_parking INT DEFAULT 0,
    id_immobilier INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `px_immo_favoris` (
    id_favoris INT AUTO_INCREMENT PRIMARY KEY,
    id_customer INT NOT NULL,
    id_immobilier INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;