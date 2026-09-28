-- =========================================================
-- EasyRDV
-- Script de création de la base de données relationnelle
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

SET NAMES utf8mb4;

-- Suppression des tables si elles existent déjà.
-- L'ordre respecte les dépendances des clés étrangères.

DROP TABLE IF EXISTS rendez_vous;
DROP TABLE IF EXISTS disponibilite;
DROP TABLE IF EXISTS prestation;
DROP TABLE IF EXISTS utilisateur;


-- =========================================================
-- TABLE UTILISATEUR
-- =========================================================

CREATE TABLE utilisateur (
    id INT(11) NOT NULL AUTO_INCREMENT,
    prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'client',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY email (email)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- TABLE PRESTATION
-- =========================================================

CREATE TABLE prestation (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    duree_minutes INT(11) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- TABLE DISPONIBILITE
-- =========================================================

CREATE TABLE disponibilite (
    id INT(11) NOT NULL AUTO_INCREMENT,
    date_disponibilite DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    disponible TINYINT(1) NOT NULL DEFAULT 1,

    PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- TABLE RENDEZ_VOUS
-- =========================================================

CREATE TABLE rendez_vous (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_utilisateur INT(11) NOT NULL,
    id_prestation INT(11) NOT NULL,
    date_rdv DATE NOT NULL,
    heure_rdv TIME NOT NULL,
    statut VARCHAR(20) NOT NULL DEFAULT 'confirme',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY id_utilisateur (id_utilisateur),
    KEY id_prestation (id_prestation),

    CONSTRAINT rendez_vous_ibfk_1
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT rendez_vous_ibfk_2
        FOREIGN KEY (id_prestation)
        REFERENCES prestation (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;