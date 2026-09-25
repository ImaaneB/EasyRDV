# EasyRDV

EasyRDV est une application web de prise de rendez-vous en ligne destinée à un salon de coiffure.

Ce projet a été réalisé dans le cadre de ma formation **Développeur Web et Web Mobile (DWWM)**.

L'application permet aux clients de créer un compte, de se connecter, de consulter les créneaux disponibles, de réserver une prestation et de gérer leurs rendez-vous.

Une interface d'administration permet également de gérer les prestations, les disponibilités et les rendez-vous.

---

## Fonctionnalités

### Espace client

- Création d'un compte utilisateur
- Connexion et déconnexion
- Consultation des prestations proposées
- Consultation des créneaux disponibles
- Réservation d'un rendez-vous
- Consultation de ses rendez-vous
- Annulation d'un rendez-vous
- Remise à disposition automatique d'un créneau après annulation

### Espace administrateur

- Connexion avec un compte administrateur
- Tableau de bord d'administration
- Consultation des rendez-vous
- Gestion des prestations
- Gestion des disponibilités
- Ajout, modification et suppression de données selon les fonctionnalités prévues dans l'administration

---

## Technologies utilisées

### Front-end

- HTML5
- CSS3
- Bootstrap 5
- JavaScript

### Back-end

- PHP 8
- PDO
- MySQL

### Environnement de développement

- XAMPP
- Apache
- phpMyAdmin
- Visual Studio Code
- Git
- GitHub

### Hébergement

L'application est déployée sur **InfinityFree** avec une base de données MySQL distante.

---

## Architecture du projet

```text
EasyRDV/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
│   └── images/
│
├── config/
│   └── database.php
│
├── pages/
│   ├── connexion.php
│   ├── inscription.php
│   ├── reservation.php
│   ├── espace-client.php
│   ├── admin.php
│   └── deconnexion.php
│
├── index.html
├── README.md
└── .gitignore