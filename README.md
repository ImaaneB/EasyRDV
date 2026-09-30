# EasyRDV

EasyRDV est une application web de prise de rendez-vous en ligne destinée à un salon de coiffure.

Ce projet a été réalisé dans le cadre de ma formation **Développeur Web et Web Mobile (DWWM)**.

L'application permet aux clients de créer un compte, de se connecter, de consulter les prestations et les créneaux disponibles, de réserver un rendez-vous et de gérer leurs réservations.

Une interface d'administration permet également de gérer les rendez-vous, les prestations, les disponibilités et de consulter des statistiques.

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
- Consultation et gestion des rendez-vous
- Gestion des prestations
- Gestion des disponibilités
- Consultation des statistiques de réservation

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
- MongoDB

### Environnement de développement

- XAMPP
- Apache
- phpMyAdmin
- MongoDB Community Server
- MongoDB Compass
- Visual Studio Code
- Git
- GitHub

### Hébergement

L'application est déployée sur **InfinityFree** avec une base de données MySQL distante.

MongoDB est utilisé dans l'environnement local pour enregistrer et consulter les statistiques de réservation.

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
│       └── salon.jpg
│
├── config/
│   └── database.php
│
├── database/
│   ├── schema.sql
│   └── seed.sql
│
├── docs/
│   ├── documentation-technique.md
│   └── diagrams/
│       ├── mcd-easyrdv.png
│       ├── diagramme-cas-utilisation-easyrdv.png
│       ├── diagramme-sequence-reservation-easyrdv.png
│       └── diagramme-architecture-technique-easyrdv.png
│
├── pages/
│   ├── connexion.php
│   ├── inscription.php
│   ├── reservation.php
│   ├── espace-client.php
│   ├── admin.php
│   └── deconnexion.php
│
├── services/
│   └── MongoDBService.php
│
├── index.html
├── README.md
└── .gitignore