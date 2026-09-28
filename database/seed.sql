-- =========================================================
-- EasyRDV
-- Jeu de données de démonstration
-- =========================================================
--
-- Ce fichier contient uniquement des données fictives.
-- Aucune donnée personnelle réelle n'est utilisée.
--
-- Exécuter schema.sql avant ce fichier.
-- =========================================================


-- =========================================================
-- PRESTATIONS
-- =========================================================

INSERT INTO prestation
    (nom, description, duree_minutes, prix, actif)
VALUES
    (
        'Coupe Homme',
        'Coupe de cheveux pour homme',
        30,
        20.00,
        1
    ),
    (
        'Barbe',
        'Taille et entretien de la barbe',
        20,
        15.00,
        1
    ),
    (
        'Coupe + Barbe',
        'Coupe de cheveux et entretien de la barbe',
        45,
        30.00,
        1
    ),
    (
        'Shampoing + Coupe',
        'Shampoing suivi d''une coupe de cheveux',
        40,
        28.00,
        1
    );


-- =========================================================
-- DISPONIBILITES
-- =========================================================

INSERT INTO disponibilite
    (
        date_disponibilite,
        heure_debut,
        heure_fin,
        disponible
    )
VALUES
    (
        '2026-10-01',
        '09:00:00',
        '09:30:00',
        1
    ),
    (
        '2026-10-01',
        '10:00:00',
        '10:30:00',
        1
    ),
    (
        '2026-10-02',
        '14:00:00',
        '14:30:00',
        1
    ),
    (
        '2026-10-02',
        '15:00:00',
        '15:30:00',
        1
    );
    