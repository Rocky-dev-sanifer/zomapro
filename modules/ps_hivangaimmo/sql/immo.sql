CREATE TABLE type_immobilier(
   id INT AUTO_INCREMENT,
   nom VARCHAR(150) NOT NULL,
   PRIMARY KEY(id)
);

CREATE TABLE profile(
   id INT AUTO_INCREMENT,
   code VARCHAR(50) NOT NULL,
   PRIMARY KEY(id)
);

CREATE TABLE cercle(
   id INT AUTO_INCREMENT,
   PRIMARY KEY(id)
);

CREATE TABLE region(
   id INT AUTO_INCREMENT,
   nom VARCHAR(255) NOT NULL,
   PRIMARY KEY(id)
);

CREATE TABLE ville(
   id INT AUTO_INCREMENT,
   nom VARCHAR(250) NOT NULL,
   id_region INT NOT NULL,
   PRIMARY KEY(id),
   CONSTRAINT fk_ville_region
      FOREIGN KEY(id_region) REFERENCES region(id)
);

CREATE TABLE collaborateur(
   id INT AUTO_INCREMENT,
   nom VARCHAR(250) NOT NULL,
   prenoms VARCHAR(250),
   titre VARCHAR(250) NOT NULL,
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id)
);

CREATE TABLE immobilier(
   id INT AUTO_INCREMENT,
   description VARCHAR(150) NOT NULL,
   surface DECIMAL(15,2),
   is_meuble BOOLEAN NOT NULL DEFAULT FALSE,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   autres TEXT,
   nb_etoiles FLOAT,
   id_ville INT NOT NULL,
   id_type_immobilier INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_immo_ville
      FOREIGN KEY(id_ville) REFERENCES ville(id),

   CONSTRAINT fk_immo_type
      FOREIGN KEY(id_type_immobilier) REFERENCES type_immobilier(id)
);

CREATE TABLE utilisateur(
   id INT AUTO_INCREMENT,
   nom VARCHAR(250) NOT NULL,
   email VARCHAR(255) NOT NULL,
   contact VARCHAR(150) NOT NULL,
   mdp VARCHAR(150) NOT NULL,
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   prenoms VARCHAR(150),
   date_naissance DATE,
   id_profile INT NOT NULL,
   PRIMARY KEY(id),
   UNIQUE(email),

   CONSTRAINT fk_user_profile
      FOREIGN KEY(id_profile) REFERENCES profile(id)
);

CREATE TABLE matching_simple(
   id INT AUTO_INCREMENT,
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   id_immobilier INT NOT NULL,
   id_bailleur INT NOT NULL,
   id_client INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_matching_immo
      FOREIGN KEY(id_immobilier) REFERENCES immobilier(id),

   CONSTRAINT fk_matching_bailleur
      FOREIGN KEY(id_bailleur) REFERENCES utilisateur(id),

   CONSTRAINT fk_matching_client
      FOREIGN KEY(id_client) REFERENCES utilisateur(id)
);

CREATE TABLE service_multimedia(
   id INT AUTO_INCREMENT,
   nom VARCHAR(255) NOT NULL,
   contact VARCHAR(150) NOT NULL,
   email VARCHAR(255),
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL DEFAULT NULL,
   is_valid BOOLEAN DEFAULT FALSE,
   id_utilisateur INT,
   PRIMARY KEY(id),

   CONSTRAINT fk_service_user
      FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id)
);

CREATE TABLE img_immobilier(
   id INT AUTO_INCREMENT,
   url VARCHAR(150) NOT NULL,
   id_immobilier INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_img_immo
      FOREIGN KEY(id_immobilier) REFERENCES immobilier(id)
);

CREATE TABLE prix_immobilier(
   id INT AUTO_INCREMENT,
   prix DECIMAL(20,2) NOT NULL,
   updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   is_par_m2 BOOLEAN NOT NULL DEFAULT FALSE,
   id_immobilier INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_prix_immo
      FOREIGN KEY(id_immobilier) REFERENCES immobilier(id)
);

CREATE TABLE immo_utilisateur(
   id INT AUTO_INCREMENT,
   is_dispo BOOLEAN NOT NULL DEFAULT TRUE,
   a_vendre BOOLEAN NOT NULL DEFAULT TRUE,
   id_utilisateur INT NOT NULL,
   id_immobilier INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_immo_user
      FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id),

   CONSTRAINT fk_immo_immo
      FOREIGN KEY(id_immobilier) REFERENCES immobilier(id)
);

CREATE TABLE piece(
   id INT AUTO_INCREMENT,
   nb_chambre INT,
   nb_toilette INT,
   nb_parking INT,
   id_immobilier INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_piece_immo
      FOREIGN KEY(id_immobilier) REFERENCES immobilier(id)
);

CREATE TABLE plainte(
   id INT AUTO_INCREMENT,
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   description TEXT NOT NULL,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_plainte_user
      FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id)
);

CREATE TABLE note(
   id INT AUTO_INCREMENT,
   description TEXT NOT NULL,
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
      ON UPDATE CURRENT_TIMESTAMP,
   id_collaborateur INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_note_collab
      FOREIGN KEY(id_collaborateur) REFERENCES collaborateur(id)
);

CREATE TABLE expertise(
   id INT AUTO_INCREMENT,
   nom VARCHAR(255) NOT NULL,
   contact VARCHAR(150) NOT NULL,
   email VARCHAR(255),
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL DEFAULT NULL,
   is_valid BOOLEAN DEFAULT FALSE,
   id_utilisateur INT,
   PRIMARY KEY(id),

   CONSTRAINT fk_expertise_user
      FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id)
);

CREATE TABLE media_immobilier(
   id INT AUTO_INCREMENT,
   video_url VARCHAR(255),
   earth_url VARCHAR(255),
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   id_immobilier INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_media_immo
      FOREIGN KEY(id_immobilier) REFERENCES immobilier(id)
);

CREATE TABLE favoris(
   id INT AUTO_INCREMENT,
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   id_utilisateur INT NOT NULL,
   id_immobilier INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_favoris_user
      FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id),

   CONSTRAINT fk_favoris_immo
      FOREIGN KEY(id_immobilier) REFERENCES immobilier(id)
);

CREATE TABLE vente(
   id INT AUTO_INCREMENT,
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   id_immo_utilisateur INT NOT NULL,
   id_client INT NOT NULL,
   PRIMARY KEY(id),

   CONSTRAINT fk_vente_immo_user
      FOREIGN KEY(id_immo_utilisateur) REFERENCES immo_utilisateur(id),

   CONSTRAINT fk_vente_client
      FOREIGN KEY(id_client) REFERENCES utilisateur(id)
);