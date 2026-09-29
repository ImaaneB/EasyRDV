# Documentation technique – EasyRDV

## 1. Présentation technique du projet

EasyRDV est une application web de prise de rendez-vous destinée à un salon de coiffure.

L'application permet à un client de créer un compte, de se connecter, de consulter les prestations proposées, de réserver un rendez-vous selon les disponibilités, de consulter ses rendez-vous et de les annuler.

Une interface d'administration permet également de gérer les données nécessaires au fonctionnement de l'application et de consulter des statistiques relatives aux rendez-vous.

Cette documentation technique présente l'environnement de développement, l'architecture de l'application, l'organisation du code, les bases de données SQL et NoSQL, les mécanismes de sécurité, la gestion de versions, les tests, la conception des interfaces, l'installation locale et le déploiement d'EasyRDV.

## 2. Environnement technique et prérequis

### 2.1 Environnement de développement

Le projet EasyRDV a été développé sous Windows avec Visual Studio Code comme éditeur de code.

L'environnement local repose principalement sur XAMPP, qui permet d'utiliser :

- Apache comme serveur web ;
- PHP pour les traitements côté serveur ;
- MySQL pour la base de données relationnelle ;
- phpMyAdmin pour l'administration de la base MySQL.

MongoDB Community Server est également utilisé pour la partie NoSQL du projet.

MongoDB Compass permet de consulter graphiquement les bases, les collections et les documents enregistrés dans MongoDB.

Git est utilisé pour le versionnement du projet et GitHub pour l'hébergement du dépôt distant.

Figma est utilisé dans le cadre de la conception et du maquettage des interfaces utilisateur.

### 2.2 Technologies utilisées

#### Front-end

- HTML5 ;
- CSS3 ;
- Bootstrap ;
- JavaScript.

#### Back-end

- PHP ;
- PDO pour l'accès à MySQL.

#### Bases de données

- MySQL pour les données métier relationnelles ;
- MongoDB pour les données statistiques NoSQL.

#### Outils de développement et de conception

- XAMPP ;
- phpMyAdmin ;
- MongoDB Community Server ;
- MongoDB Compass ;
- Visual Studio Code ;
- Git ;
- GitHub ;
- Figma.

### 2.3 Organisation des environnements

Deux environnements principaux sont distingués dans le projet.

#### Environnement local

L'application est développée et testée localement avec XAMPP.

Dans cet environnement :

- Apache exécute l'application web ;
- PHP réalise les traitements côté serveur ;
- MySQL contient les données métier ;
- MongoDB contient les données statistiques utilisées par l'administration.

L'environnement local permet donc de tester l'ensemble des fonctionnalités développées dans EasyRDV, y compris la partie NoSQL.

#### Environnement déployé

Une version d'EasyRDV est déployée sur InfinityFree afin de rendre l'application accessible depuis Internet.

Cette version utilise principalement :

- le serveur web fourni par l'hébergeur ;
- PHP ;
- MySQL.

La partie MongoDB utilisée pour les statistiques est actuellement mise en œuvre et testée dans l'environnement local. Elle n'est pas hébergée sur InfinityFree.

Cette différence entre l'environnement local et l'environnement déployé est prise en compte dans l'architecture et dans la documentation du projet.

## 3. Architecture et choix techniques

### 3.1 Architecture générale

EasyRDV repose sur une architecture web client-serveur.

Le navigateur constitue la partie cliente de l'application. Les interfaces sont réalisées en HTML5 et CSS3, avec Bootstrap pour faciliter la mise en page responsive. JavaScript est utilisé pour les interactions dynamiques côté navigateur, notamment dans le processus de réservation.

La partie serveur est développée en PHP. Elle prend notamment en charge :

- l'inscription et l'authentification ;
- la gestion des sessions ;
- la récupération des prestations et des disponibilités ;
- la création des rendez-vous ;
- l'annulation des rendez-vous ;
- les fonctionnalités d'administration ;
- les contrôles de sécurité.

PHP communique avec la base de données relationnelle MySQL à l'aide de PDO.

Une base MongoDB est utilisée en complément pour stocker et exploiter les statistiques de rendez-vous par prestation.

### 3.2 Choix de PHP et PDO

PHP a été choisi pour développer la partie serveur de l'application.

Il permet notamment de :

- traiter les formulaires ;
- gérer les sessions utilisateurs ;
- appliquer les règles métier ;
- contrôler les droits d'accès ;
- communiquer avec les bases de données ;
- générer dynamiquement les réponses envoyées au navigateur.

PDO est utilisé pour communiquer avec MySQL.

L'utilisation de PDO permet notamment d'utiliser des requêtes préparées lorsque des données fournies par l'utilisateur doivent être intégrées à une requête.

Cette approche sépare les paramètres de la requête SQL et participe à la sécurisation de l'accès aux données.

### 3.3 Choix de MySQL

MySQL constitue la base de données relationnelle principale d'EasyRDV.

Elle contient les données métier nécessaires au fonctionnement de l'application :

- les utilisateurs ;
- les prestations ;
- les disponibilités ;
- les rendez-vous.

Le modèle relationnel est adapté à ces informations car plusieurs relations existent entre les données.

Par exemple, un rendez-vous est associé à un utilisateur et à une prestation.

Les opérations de réservation et d'annulation utilisent également des transactions SQL afin de maintenir la cohérence des différentes modifications effectuées pendant une même opération métier.

### 3.4 Choix de MongoDB

MongoDB est utilisé comme base de données NoSQL complémentaire.

Dans EasyRDV, MongoDB est consacré aux statistiques de rendez-vous par prestation affichées dans l'interface d'administration.

Les données métier principales restent enregistrées dans MySQL.

Le fonctionnement est le suivant :

1. PHP récupère les données nécessaires depuis MySQL ;
2. les statistiques de rendez-vous par prestation sont calculées ;
3. ces statistiques sont enregistrées sous forme de documents dans MongoDB ;
4. les documents MongoDB sont ensuite récupérés ;
5. les résultats sont affichés dans l'administration.

Cette organisation permet de mettre en œuvre deux approches complémentaires :

- MySQL pour les données métier relationnelles nécessitant des relations et une cohérence transactionnelle ;
- MongoDB pour les données statistiques sous forme documentaire.

### 3.5 Organisation générale des traitements

Le fonctionnement général de l'application peut être résumé ainsi :

```text
Utilisateur
    ↓
Navigateur
HTML / CSS / Bootstrap / JavaScript
    ↓
Serveur PHP
    ↓
Contrôles métier et sécurité
    ↓
PDO
    ↓
MySQL
```

Pour la fonctionnalité de statistiques administratives, un second flux est utilisé :

```text
MySQL
    ↓
PHP
    ↓
Calcul des statistiques
    ↓
MongoDB
    ↓
PHP
    ↓
Interface d'administration
```

### 3.6 Diagramme d'architecture technique

Un diagramme d'architecture technique a été réalisé afin de représenter visuellement l'organisation du projet.

Il est disponible dans :

`docs/diagrams/diagramme-architecture-technique-easyrdv.png`

Ce diagramme présente notamment :

- les utilisateurs de l'application ;
- le navigateur et les technologies front-end ;
- le serveur PHP ;
- l'accès à MySQL avec PDO ;
- l'utilisation de MongoDB pour les statistiques ;
- l'environnement local de développement ;
- Git et GitHub ;
- l'environnement de déploiement.

Il permet également de visualiser la différence entre l'environnement local, dans lequel MongoDB est utilisé, et la version déployée sur InfinityFree, qui repose principalement sur PHP et MySQL.

## 4. Organisation et structure du projet

### 4.1 Organisation générale

Le projet EasyRDV est organisé de manière à séparer les ressources front-end, la configuration, les scripts de base de données, les pages fonctionnelles, les services et la documentation.

Cette organisation facilite la compréhension du projet et permet de retrouver rapidement les différents composants de l'application.

### 4.2 Arborescence principale

```text
EasyRDV/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── images/
│   │   └── salon.jpg
│   └── js/
│       └── app.js
│
├── config/
│   └── database.php
│
├── database/
│   ├── schema.sql
│   └── seed.sql
│
├── docs/
│   ├── diagrams/
│   │   ├── mcd-easyrdv.png
│   │   ├── diagramme-cas-utilisation-easyrdv.png
│   │   ├── diagramme-sequence-reservation-easyrdv.png
│   │   └── diagramme-architecture-technique-easyrdv.png
│   └── documentation-technique.md
│
├── pages/
│   ├── admin.php
│   ├── connexion.php
│   ├── deconnexion.php
│   ├── espace-client.php
│   ├── inscription.php
│   └── reservation.php
│
├── services/
│   └── MongoDBService.php
│
├── index.html
├── README.md
└── .gitignore
```

### 4.3 Dossier `assets`

Le dossier `assets` regroupe les ressources utilisées par les interfaces.

#### `assets/css/style.css`

Ce fichier contient les styles CSS personnalisés de l'application.

Il complète Bootstrap afin de définir notamment :

- l'identité visuelle d'EasyRDV ;
- les couleurs ;
- les espacements ;
- les cartes ;
- les boutons ;
- les formulaires ;
- les adaptations responsive ;
- certains styles liés à l'accessibilité, comme la visibilité du focus.

#### `assets/js/app.js`

Ce fichier contient les traitements JavaScript exécutés dans le navigateur.

Il est notamment utilisé pour gérer certaines interactions de la page de réservation, comme la sélection des éléments nécessaires à la prise de rendez-vous et l'affichage dynamique d'informations dans l'interface.

#### `assets/images`

Ce dossier contient les ressources graphiques utilisées par l'application, notamment l'image du salon.

### 4.4 Dossier `config`

Le dossier `config` contient :

`config/database.php`

Ce fichier centralise la configuration permettant à PHP de se connecter à MySQL avec PDO.

Il n'est pas publié dans le dépôt GitHub car il est exclu par `.gitignore`.

Cette exclusion permet d'éviter de versionner des paramètres de connexion propres à l'environnement local ou des informations sensibles.

### 4.5 Dossier `database`

Le dossier `database` contient les scripts nécessaires à la création et à l'initialisation de la base relationnelle.

#### `schema.sql`

Ce fichier contient la structure de la base MySQL :

- création des tables ;
- clés primaires ;
- contraintes ;
- clés étrangères ;
- types de données.

#### `seed.sql`

Ce fichier contient des données de démonstration permettant d'initialiser l'application.

Les données personnelles utilisées pendant le développement ne sont pas intégrées dans ce fichier.

### 4.6 Dossier `pages`

Le dossier `pages` contient les principales pages fonctionnelles de l'application.

#### `inscription.php`

Permet à un utilisateur de créer un compte.

#### `connexion.php`

Permet d'authentifier un utilisateur et d'ouvrir sa session.

#### `deconnexion.php`

Permet de fermer la session utilisateur.

#### `reservation.php`

Gère le parcours de réservation d'un rendez-vous.

Cette page permet notamment de consulter les prestations et les créneaux disponibles, puis d'enregistrer une réservation après les contrôles nécessaires.

#### `espace-client.php`

Présente les rendez-vous du client connecté et permet notamment d'annuler un rendez-vous.

#### `admin.php`

Regroupe les fonctionnalités destinées à l'administrateur.

Cette page permet notamment de gérer les données nécessaires au fonctionnement de l'application et de consulter les statistiques de rendez-vous.

L'accès à cette page est protégé par un contrôle de session et de rôle.

### 4.7 Dossier `services`

Le dossier `services` contient :

`services/MongoDBService.php`

Ce composant centralise la communication entre PHP et MongoDB.

Il permet notamment :

- de tester la connexion à MongoDB ;
- d'enregistrer les statistiques ;
- de récupérer les statistiques enregistrées ;
- de fournir ces données à l'administration.

Cette séparation évite de disperser le code d'accès à MongoDB dans différentes pages de l'application.

### 4.8 Dossier `docs`

Le dossier `docs` regroupe les éléments de documentation du projet.

Le sous-dossier `docs/diagrams` contient :

- le modèle de données ;
- le diagramme de cas d'utilisation ;
- le diagramme de séquence de réservation ;
- le diagramme d'architecture technique.

Le fichier :

`docs/documentation-technique.md`

constitue la présente documentation technique.

### 4.9 Fichiers situés à la racine

#### `index.html`

Il constitue la page d'accueil et le point d'entrée public de l'application.

#### `README.md`

Il présente le projet et fournit les informations générales utiles pour comprendre le dépôt.

#### `.gitignore`

Il définit les fichiers et dossiers qui ne doivent pas être suivis par Git.

Il exclut notamment :

```text
config/database.php
.vscode/
.DS_Store
Thumbs.db
```

### 4.10 Intérêt de cette organisation

Cette structure permet de séparer plusieurs responsabilités :

- les interfaces et ressources front-end dans `assets` ;
- la configuration dans `config` ;
- les scripts SQL dans `database` ;
- les fonctionnalités applicatives dans `pages` ;
- l'accès à MongoDB dans `services` ;
- la documentation dans `docs`.

Cette organisation reste volontairement simple et adaptée à la taille d'EasyRDV tout en facilitant la lecture, la maintenance et la présentation du projet.

## 5. Base de données relationnelle MySQL et modèle de données

### 5.1 Rôle de la base de données relationnelle

EasyRDV utilise MySQL comme base de données relationnelle principale.

Elle stocke les données métier nécessaires au fonctionnement de l'application et permet d'établir les relations entre les utilisateurs, les prestations et les rendez-vous.

La base de données utilisée dans l'environnement local porte le nom :

`easyrdv`

Elle est composée de quatre tables principales :

- `utilisateur` ;
- `prestation` ;
- `disponibilite` ;
- `rendez_vous`.

### 5.2 Table `utilisateur`

La table `utilisateur` contient les comptes des personnes utilisant l'application.

Elle enregistre notamment :

- l'identifiant ;
- le prénom ;
- le nom ;
- l'adresse e-mail ;
- le numéro de téléphone ;
- le mot de passe sous forme hachée ;
- le rôle ;
- la date de création du compte.

Le rôle permet notamment de distinguer un utilisateur ayant le rôle de client d'un administrateur.

L'adresse e-mail possède une contrainte d'unicité afin d'éviter la création de plusieurs comptes avec la même adresse.

### 5.3 Table `prestation`

La table `prestation` contient les services proposés par le salon de coiffure.

Elle enregistre notamment :

- l'identifiant ;
- le nom de la prestation ;
- la description ;
- la durée en minutes ;
- le prix ;
- l'état actif ou inactif ;
- la date de création.

L'état de la prestation permet de contrôler si celle-ci doit être proposée dans le parcours de réservation.

### 5.4 Table `disponibilite`

La table `disponibilite` contient les créneaux utilisables pour les réservations.

Elle enregistre :

- l'identifiant ;
- la date de disponibilité ;
- l'heure de début ;
- l'heure de fin ;
- l'état disponible ou indisponible du créneau.

Dans le modèle actuellement implémenté, cette table ne possède pas de clé étrangère vers la table `rendez_vous`.

Le rapprochement entre un rendez-vous et un créneau est donc réalisé par la logique métier de l'application à partir de la date et de l'heure concernées.

### 5.5 Table `rendez_vous`

La table `rendez_vous` contient les rendez-vous enregistrés dans l'application.

Elle contient notamment :

- l'identifiant du rendez-vous ;
- l'identifiant de l'utilisateur ;
- l'identifiant de la prestation ;
- la date du rendez-vous ;
- l'heure du rendez-vous ;
- le statut ;
- la date de création.

Les identifiants de l'utilisateur et de la prestation permettent de relier chaque rendez-vous aux données correspondantes.

### 5.6 Relations entre les données

La table `rendez_vous` constitue le lien principal entre les utilisateurs et les prestations.

Les relations réellement implémentées sont :

- `rendez_vous.id_utilisateur` référence `utilisateur.id` ;
- `rendez_vous.id_prestation` référence `prestation.id`.

Les cardinalités fonctionnelles peuvent être présentées ainsi :

```text
UTILISATEUR
1 utilisateur → 0 à plusieurs rendez-vous
1 rendez-vous → 1 utilisateur

PRESTATION
1 prestation → 0 à plusieurs rendez-vous
1 rendez-vous → 1 prestation
```

La table `disponibilite` est actuellement gérée indépendamment et ne possède pas de relation par clé étrangère avec `rendez_vous`.

### 5.7 Modèle de données

Un modèle de données a été réalisé afin de représenter les principales entités de l'application et leurs relations.

Le diagramme est disponible dans :

`docs/diagrams/mcd-easyrdv.png`

Il représente notamment les entités :

- `UTILISATEUR` ;
- `PRESTATION` ;
- `RENDEZ_VOUS` ;
- `DISPONIBILITE`.

Ce diagramme permet de comprendre l'organisation des données avant d'étudier leur implémentation dans MySQL.

### 5.8 Script de création de la base

Le fichier :

`database/schema.sql`

permet de créer la structure relationnelle nécessaire à EasyRDV.

Il définit notamment :

- les différentes tables ;
- les colonnes ;
- les types de données ;
- les clés primaires ;
- la contrainte d'unicité de l'adresse e-mail ;
- les clés étrangères ;
- l'encodage utilisé.

La présence de ce script permet de recréer la structure de la base dans un nouvel environnement sans devoir construire manuellement chaque table.

### 5.9 Données de démonstration

Le fichier :

`database/seed.sql`

permet d'ajouter des données de démonstration après la création de la structure.

Il contient notamment :

- des prestations de démonstration ;
- des disponibilités permettant de tester le parcours de réservation.

Les comptes utilisateurs réels et les données personnelles utilisées pendant le développement ne sont volontairement pas intégrés à ce fichier.

Cette séparation évite de publier des informations personnelles dans le dépôt GitHub.

### 5.10 Accès aux données avec PDO

L'application utilise PDO pour communiquer avec MySQL depuis PHP.

PDO intervient notamment pour :

- l'inscription ;
- l'authentification ;
- la récupération des prestations ;
- la consultation des disponibilités ;
- la création d'un rendez-vous ;
- l'annulation d'un rendez-vous ;
- les traitements de l'administration.

Lorsque des valeurs provenant de l'utilisateur sont utilisées dans une requête, l'application utilise des requêtes préparées.

Exemple de principe :

```php
$requete = $pdo->prepare(
    "SELECT * FROM utilisateur WHERE email = ?"
);

$requete->execute([$email]);
```

La valeur de l'adresse e-mail est ainsi transmise séparément de l'instruction SQL.

### 5.11 Gestion transactionnelle d'une réservation

Une réservation nécessite plusieurs opérations liées entre elles.

Le principe général est le suivant :

```text
Début de la transaction
        ↓
Vérification du créneau
        ↓
Enregistrement du rendez-vous
        ↓
Passage du créneau à indisponible
        ↓
COMMIT
```

Si une erreur intervient pendant une opération transactionnelle, les modifications concernées peuvent être annulées avec un :

`ROLLBACK`

Lorsque toutes les opérations sont correctement exécutées, elles sont validées avec :

`COMMIT`

Cette logique évite, par exemple, qu'un rendez-vous soit enregistré alors que la mise à jour associée du créneau n'a pas pu être réalisée.

### 5.12 Gestion de l'annulation

Le même principe de cohérence est appliqué lors de l'annulation d'un rendez-vous.

L'application doit notamment :

1. identifier le rendez-vous concerné ;
2. vérifier que l'utilisateur est autorisé à effectuer l'action ;
3. modifier le statut du rendez-vous ;
4. rendre de nouveau disponible le créneau correspondant ;
5. valider les modifications.

L'utilisation d'une transaction permet de conserver la cohérence entre le rendez-vous et la disponibilité du créneau.

### 5.13 Synthèse

MySQL constitue la source principale des données métier d'EasyRDV.

La combinaison du modèle relationnel, des clés étrangères, de PDO, des requêtes préparées et des transactions permet de gérer les principales données nécessaires au fonctionnement de l'application tout en maintenant leur cohérence.

## 6. MongoDB et accès aux données NoSQL

### 6.1 Objectif de l'utilisation de MongoDB

EasyRDV utilise MongoDB en complément de la base de données relationnelle MySQL.

MySQL reste la source principale des données métier de l'application. Les utilisateurs, les prestations, les disponibilités et les rendez-vous sont donc conservés dans la base relationnelle.

MongoDB est utilisé pour une fonctionnalité distincte : le stockage et l'exploitation de statistiques de rendez-vous destinées à l'interface d'administration.

Cette organisation permet au projet de mettre en œuvre deux types d'accès aux données :

- un accès relationnel avec MySQL et PDO ;
- un accès NoSQL documentaire avec MongoDB.

### 6.2 Environnement MongoDB

Dans l'environnement local de développement, MongoDB Community Server est utilisé comme serveur de base de données NoSQL.

MongoDB Compass est utilisé comme outil graphique afin de consulter :

- les bases de données ;
- les collections ;
- les documents ;
- les valeurs enregistrées.

La connexion MongoDB utilisée localement est :

`mongodb://localhost:27017`

La base NoSQL utilisée par EasyRDV porte le nom :

`easyrdv_nosql`

La collection utilisée pour les statistiques est :

`statistiques_rendez_vous`

### 6.3 Communication entre PHP et MongoDB

Pour permettre à PHP de communiquer avec MongoDB, l'extension MongoDB pour PHP a été installée et activée dans l'environnement local.

Le pilote est chargé par la configuration PHP utilisée par Apache.

La logique d'accès à MongoDB est centralisée dans le fichier :

`services/MongoDBService.php`

Cette organisation évite de placer directement toute la logique MongoDB dans la page d'administration.

### 6.4 Rôle de `MongoDBService.php`

Le service MongoDB permet notamment :

- de tester la communication entre PHP et MongoDB ;
- d'enregistrer les statistiques de rendez-vous par prestation ;
- de récupérer les statistiques enregistrées ;
- de transmettre les données nécessaires à l'interface d'administration.

Il constitue donc le composant d'accès aux données NoSQL du projet.

### 6.5 Origine des statistiques

Les statistiques sont calculées à partir des données métier présentes dans MySQL.

Une requête SQL permet de compter le nombre de rendez-vous confirmés pour chaque prestation.

Le principe est le suivant :

```text
Table prestation
        +
Table rendez_vous
        ↓
Requête SQL
        ↓
Nombre de rendez-vous confirmés
par prestation
```

Les résultats obtenus sont ensuite transmis au service MongoDB.

### 6.6 Enregistrement dans MongoDB

Après le calcul des statistiques, PHP transmet les données à `MongoDBService.php`.

Les statistiques sont enregistrées dans la collection :

`statistiques_rendez_vous`

Elles sont stockées sous forme de documents MongoDB.

Cette utilisation est différente de celle de MySQL : MongoDB ne remplace pas les tables métier de l'application mais reçoit des données statistiques préparées pour leur exploitation dans l'administration.

### 6.7 Lecture des données NoSQL

Après l'enregistrement des statistiques, le service MongoDB permet également de récupérer les documents présents dans la collection.

Les données sont ensuite utilisées dans l'interface d'administration afin d'afficher :

- les prestations ;
- le nombre de réservations correspondant ;
- une représentation graphique des statistiques.

Le flux complet peut être représenté ainsi :

```text
MySQL
    ↓
Calcul PHP des statistiques
    ↓
MongoDBService.php
    ↓
MongoDB
    ↓
MongoDBService.php
    ↓
PHP
    ↓
Interface d'administration
```

### 6.8 Justification du choix d'une base NoSQL

MongoDB utilise un modèle documentaire différent du modèle relationnel de MySQL.

Dans EasyRDV, son utilisation permet de séparer :

- les données métier transactionnelles, conservées dans MySQL ;
- les données statistiques, enregistrées sous forme de documents dans MongoDB.

Les utilisateurs, prestations et rendez-vous ne sont donc pas déplacés vers MongoDB.

MySQL reste la source de référence pour ces données.

MongoDB constitue une base complémentaire utilisée pour la partie statistique du projet.

### 6.9 Vérification du fonctionnement

Le fonctionnement de la partie NoSQL a été vérifié pendant les tests du projet.

Un scénario de vérification a notamment consisté à :

1. accéder à l'administration ;
2. consulter les statistiques affichées ;
3. vérifier les documents correspondants dans MongoDB Compass ;
4. annuler un rendez-vous ;
5. recharger l'administration ;
6. vérifier la modification de la statistique ;
7. contrôler la nouvelle valeur dans MongoDB Compass.

Cette vérification a permis de contrôler le flux complet :

`MySQL → PHP → MongoDB → PHP → Administration`

### 6.10 Séparation SQL / NoSQL

L'architecture des données d'EasyRDV peut être résumée ainsi :

```text
EasyRDV
│
├── MySQL
│   ├── utilisateurs
│   ├── prestations
│   ├── disponibilités
│   └── rendez-vous
│
└── MongoDB
    └── statistiques de rendez-vous
        par prestation
```

Cette séparation permet de démontrer l'utilisation de deux modèles de stockage dans une même application.

### 6.11 Limite de l'environnement déployé

La fonctionnalité MongoDB est actuellement mise en œuvre et testée dans l'environnement local de développement.

Le déploiement réalisé sur InfinityFree repose sur PHP et MySQL.

Le serveur MongoDB utilisé localement n'est pas hébergé sur InfinityFree.

Par conséquent, la partie statistique NoSQL ne doit pas être présentée comme une fonctionnalité MongoDB disponible sur l'environnement public actuel.

Cette différence est volontairement documentée afin de distinguer clairement :

- les fonctionnalités testées dans l'environnement local complet ;
- les fonctionnalités disponibles dans l'environnement PHP/MySQL déployé.

### 6.12 Évolution possible

Une évolution du projet pourrait consister à utiliser un service MongoDB distant compatible avec l'environnement de production.

Cela permettrait de conserver la même architecture SQL/NoSQL tout en rendant les statistiques MongoDB disponibles depuis la version publique de l'application.

### 6.13 Synthèse

L'utilisation de MongoDB dans EasyRDV permet de mettre en œuvre un composant d'accès aux données NoSQL réellement exploité par l'application.

Le projet combine ainsi :

- MySQL et PDO pour les données métier relationnelles ;
- MongoDB pour les données statistiques documentaires.

Cette organisation permet de démontrer l'utilisation complémentaire de bases SQL et NoSQL sans remettre en cause MySQL comme source principale des données métier.

## 7. Sécurité de l'application

### 7.1 Objectif

Plusieurs mécanismes de sécurité ont été intégrés à EasyRDV afin de protéger l'authentification, les actions sensibles, les accès aux pages protégées et les échanges avec la base de données.

Les principaux mécanismes mis en œuvre sont :

- le hachage des mots de passe ;
- la vérification sécurisée des mots de passe ;
- les requêtes préparées avec PDO ;
- la protection CSRF des actions sensibles ;
- la gestion de l'authentification avec les sessions PHP ;
- le contrôle du rôle administrateur ;
- l'échappement des données affichées ;
- l'utilisation de transactions SQL pour certaines opérations métier ;
- l'exclusion des paramètres sensibles du dépôt Git.

### 7.2 Hachage des mots de passe

Lors de l'inscription, le mot de passe de l'utilisateur n'est pas enregistré en clair dans la base de données.

EasyRDV utilise la fonction PHP `password_hash()` avec `PASSWORD_DEFAULT` :

```php
$motDePasseHash = password_hash(
    $motDePasse,
    PASSWORD_DEFAULT
);
```

Le résultat du hachage est enregistré dans la colonne correspondant au mot de passe de l'utilisateur.

Cette approche évite de stocker directement le mot de passe saisi par l'utilisateur.

### 7.3 Vérification du mot de passe lors de la connexion

Lors de l'authentification, EasyRDV ne compare pas directement le mot de passe saisi avec une valeur en clair.

La fonction `password_verify()` est utilisée :

```php
if (
    $utilisateur &&
    password_verify(
        $motDePasse,
        $utilisateur['mot_de_passe']
    )
) {
```

Cette fonction vérifie si le mot de passe fourni correspond au hash enregistré dans la base de données.

Le fonctionnement peut être résumé ainsi :

```text
Mot de passe saisi
        ↓
password_verify()
        ↓
Hash enregistré dans MySQL
        ↓
Correspondance ?
        ↓
Oui → authentification autorisée
Non → authentification refusée
```

### 7.4 Requêtes préparées avec PDO

L'application utilise PDO pour communiquer avec MySQL.

Lorsque des données provenant de l'utilisateur sont nécessaires dans une requête, des requêtes préparées sont utilisées.

Par exemple, lors de la recherche d'un utilisateur par son adresse e-mail :

```php
$requete = $pdo->prepare(
    "SELECT * FROM utilisateur WHERE email = ?"
);

$requete->execute([$email]);
```

L'adresse e-mail est transmise séparément de l'instruction SQL.

Cette méthode participe à la prévention des injections SQL en évitant de concaténer directement la valeur fournie par l'utilisateur dans la requête.

### 7.5 Protection contre les requêtes CSRF

Les actions sensibles utilisent un jeton CSRF associé à la session de l'utilisateur.

Un jeton est notamment transmis dans les formulaires concernés :

```php
<input
    type="hidden"
    name="csrf_token"
    value="<?php
    echo htmlspecialchars(
        $_SESSION['csrf_token']
    );
    ?>"
>
```

Lors du traitement de la requête, le serveur vérifie la présence et la validité du jeton.

Le contrôle utilise notamment `hash_equals()` :

```php
if (
    !isset($_POST['csrf_token'])
    || !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {
```

L'action ne doit donc pas être exécutée si le jeton attendu n'est pas présent ou ne correspond pas au jeton associé à la session.

Le principe est le suivant :

```text
Session utilisateur
        ↓
Génération / stockage du jeton CSRF
        ↓
Jeton intégré au formulaire
        ↓
Envoi du formulaire
        ↓
Vérification côté serveur
        ↓
Jeton valide → poursuite du traitement
Jeton invalide → action refusée
```

### 7.6 Gestion de l'authentification avec les sessions PHP

Après une authentification réussie, EasyRDV utilise les sessions PHP afin de conserver les informations nécessaires à l'identification de l'utilisateur connecté.

La session contient notamment :

- l'identifiant de l'utilisateur ;
- le prénom ;
- le nom ;
- le rôle.

Ces informations permettent notamment de contrôler l'accès aux pages nécessitant une authentification.

Un utilisateur non authentifié ne doit pas pouvoir accéder directement aux fonctionnalités réservées à un utilisateur connecté.

### 7.7 Contrôle du rôle administrateur

L'interface d'administration est réservée aux utilisateurs possédant le rôle `admin`.

L'accès ne dépend donc pas uniquement de l'existence d'une session : le rôle de l'utilisateur est également contrôlé.

Le principe peut être représenté ainsi :

```text
Utilisateur
    ↓
Session existante ?
    ↓
Oui
    ↓
Rôle = admin ?
    ↓
Oui → accès à l'administration
Non → accès refusé / redirection
```

Ce contrôle permet de distinguer les droits d'un client de ceux d'un administrateur.

### 7.8 Échappement des données affichées

L'application utilise `htmlspecialchars()` lors de différents affichages dynamiques.

Cette fonction transforme certains caractères spéciaux avant leur insertion dans le HTML.

Elle permet ainsi de limiter le risque qu'une donnée affichée soit directement interprétée comme du code HTML par le navigateur.

Elle est notamment utilisée lors de l'affichage de données dynamiques ou de valeurs intégrées dans certains formulaires.

### 7.9 Transactions et sécurité des opérations métier

Les transactions SQL utilisées lors des réservations et des annulations participent également à la fiabilité de l'application.

Une réservation nécessite plusieurs modifications liées.

Le principe est :

```text
BEGIN TRANSACTION
        ↓
Enregistrement du rendez-vous
        ↓
Modification de la disponibilité
        ↓
Tout est correct ?
   ↙             ↘
Oui              Non
 ↓                ↓
COMMIT          ROLLBACK
```

Cette approche évite de conserver une opération partiellement réalisée en cas d'erreur pendant le traitement.

Le même principe est appliqué lors de l'annulation afin de maintenir la cohérence entre le statut du rendez-vous et la disponibilité du créneau.

### 7.10 Protection des paramètres de connexion

Le fichier :

`config/database.php`

contient les paramètres nécessaires à la connexion MySQL.

Ce fichier est exclu du dépôt Git grâce à `.gitignore`.

La règle correspondante est notamment :

```text
config/database.php
```

Cette organisation permet d'éviter de publier involontairement des paramètres de connexion dans le dépôt GitHub.

Les identifiants et mots de passe utilisés pour l'environnement de production ne doivent pas être intégrés dans la documentation publique ni dans le dépôt.

### 7.11 Synthèse des mécanismes de sécurité

| Risque ou besoin | Mécanisme utilisé dans EasyRDV |
|---|---|
| Stockage des mots de passe | `password_hash()` |
| Vérification du mot de passe | `password_verify()` |
| Injection SQL | PDO et requêtes préparées |
| Requête CSRF | Jeton CSRF et `hash_equals()` |
| Identification de l'utilisateur | Sessions PHP |
| Accès à l'administration | Contrôle du rôle `admin` |
| Affichage de données dynamiques | `htmlspecialchars()` |
| Cohérence des opérations | Transactions SQL |
| Publication de la configuration | `.gitignore` |

### 7.12 Limites de la vérification de sécurité

Les mécanismes présentés dans cette section ont été vérifiés dans le code et lors des tests fonctionnels réalisés pendant le projet.

La présence de ces mécanismes ne signifie cependant pas qu'EasyRDV a fait l'objet d'un audit complet de cybersécurité.

Aucun test d'intrusion complet n'a été réalisé.

Il est donc préférable de présenter cette partie comme une mise en œuvre de plusieurs bonnes pratiques de sécurité adaptées au projet, et non comme une certification de sécurité de l'application.

### 7.13 Synthèse

EasyRDV intègre plusieurs mécanismes de sécurité à différents niveaux de l'application :

```text
Authentification
    ↓
password_hash() / password_verify()

Accès aux données
    ↓
PDO / requêtes préparées

Formulaires sensibles
    ↓
Protection CSRF

Gestion des accès
    ↓
Sessions / rôles

Affichage
    ↓
htmlspecialchars()

Opérations métier
    ↓
Transactions SQL

Configuration
    ↓
.gitignore
```

Cette approche permet d'intégrer la sécurité directement dans les principaux traitements de l'application plutôt que de la limiter à un mécanisme unique.

## 8. Gestion de versions avec Git et GitHub

### 8.1 Objectif du versionnement

Git est utilisé pour suivre les évolutions du projet EasyRDV et conserver un historique des modifications réalisées.

GitHub est utilisé comme dépôt distant. Il permet notamment :

- de sauvegarder le code source ;
- de conserver l'historique des commits ;
- de centraliser les différentes branches ;
- de stocker les scripts SQL ;
- de conserver les diagrammes et la documentation ;
- de présenter le projet dans un dépôt distant.

L'utilisation de Git permet également d'éviter de réaliser toutes les modifications directement sur la version stable du projet.

### 8.2 Organisation des branches

Le projet utilise trois types de branches principales.

#### Branche `main`

La branche `main` contient la version stable du projet.

Les nouvelles fonctionnalités et les nouveaux éléments de documentation ne sont pas développés directement sur cette branche.

#### Branche `develop`

La branche `develop` sert de branche d'intégration.

Les fonctionnalités terminées sont d'abord intégrées dans `develop` afin de regrouper les différents développements avant leur intégration dans `main`.

#### Branches `feature`

Les fonctionnalités ou ajouts spécifiques sont réalisés dans des branches dédiées créées à partir de `develop`.

Plusieurs branches ont réellement été utilisées pendant le développement d'EasyRDV :

- `feature/statistiques-nosql` ;
- `feature/scripts-sql` ;
- `feature/accessibilite` ;
- `feature/documentation-mcd` ;
- `feature/diagramme-cas-utilisation` ;
- `feature/diagramme-sequence` ;
- `feature/diagramme-architecture`.

Une branche spécifique peut également être utilisée pour intégrer la présente documentation technique avant sa fusion dans les branches principales.

### 8.3 Workflow Git utilisé

Le workflow appliqué pour les fonctionnalités et les éléments de documentation peut être représenté ainsi :

```text
develop
    ↓
Création d'une branche feature
    ↓
Développement / ajout
    ↓
Vérification
    ↓
git add
    ↓
git commit
    ↓
git push
    ↓
Fusion dans develop
    ↓
Vérification
    ↓
Push de develop
    ↓
Fusion de develop dans main
    ↓
Push de main
```

Cette organisation permet de séparer le travail en cours de la version stable.

### 8.4 Exemple : intégration de la fonctionnalité MongoDB

La fonctionnalité de statistiques NoSQL a été développée dans la branche :

`feature/statistiques-nosql`

Après sa réalisation et sa vérification :

1. les modifications ont été enregistrées dans un commit ;
2. la branche a été publiée sur GitHub ;
3. elle a été fusionnée dans `develop` ;
4. `develop` a ensuite été fusionnée dans `main`.

Ce workflow permet de conserver dans l'historique Git les différentes étapes de réalisation de la fonctionnalité.

### 8.5 Exemple : ajout des scripts SQL

Les fichiers :

- `database/schema.sql` ;
- `database/seed.sql`

ont été ajoutés dans une branche dédiée :

`feature/scripts-sql`

Après vérification, cette branche a été fusionnée dans `develop`, puis les modifications ont été intégrées dans `main`.

### 8.6 Exemple : amélioration de l'accessibilité

Une amélioration liée à la visibilité du focus clavier a été réalisée dans la branche :

`feature/accessibilite`

Cette modification a suivi le même workflow :

```text
feature/accessibilite
        ↓
develop
        ↓
main
```

Cela permet de conserver une trace distincte de l'amélioration réalisée.

### 8.7 Versionnement des diagrammes

Les principaux diagrammes ont également été ajoutés avec des branches dédiées.

#### Modèle de données

`feature/documentation-mcd`

Fichier :

`docs/diagrams/mcd-easyrdv.png`

#### Diagramme de cas d'utilisation

`feature/diagramme-cas-utilisation`

Fichier :

`docs/diagrams/diagramme-cas-utilisation-easyrdv.png`

#### Diagramme de séquence

`feature/diagramme-sequence`

Fichier :

`docs/diagrams/diagramme-sequence-reservation-easyrdv.png`

#### Diagramme d'architecture

`feature/diagramme-architecture`

Fichier :

`docs/diagrams/diagramme-architecture-technique-easyrdv.png`

Cette méthode permet de conserver dans l'historique Git la création progressive de la documentation technique.

### 8.8 Commits

Les commits enregistrent des étapes identifiables du projet.

Plusieurs messages de commit utilisés pendant le projet sont par exemple :

```text
Add MongoDB appointment statistics
Add EasyRDV data model diagram
Add EasyRDV use case diagram
Add EasyRDV reservation sequence diagram
Add EasyRDV technical architecture diagram
```

Les messages décrivent l'objectif principal de la modification enregistrée.

### 8.9 Fichier `.gitignore`

Le projet contient un fichier `.gitignore`.

Il exclut notamment :

```text
config/database.php
.vscode/
.DS_Store
Thumbs.db
```

L'exclusion de `config/database.php` est particulièrement importante car ce fichier contient la configuration de connexion à la base de données.

Les paramètres sensibles ne doivent pas être publiés dans le dépôt GitHub.

### 8.10 Contenu du dépôt distant

Le dépôt GitHub regroupe notamment :

- le code source de l'application ;
- `README.md` ;
- les scripts SQL ;
- les ressources front-end ;
- les diagrammes ;
- la documentation technique ;
- l'historique des commits ;
- les différentes branches utilisées pendant le développement.

### 8.11 Intérêt du workflow adopté

L'utilisation de `main`, `develop` et de branches `feature` permet de distinguer :

```text
main
│
└── Version stable

develop
│
└── Version d'intégration

feature/*
│
└── Fonctionnalité ou ajout en cours
```

Cette organisation apporte plusieurs avantages :

- isoler une modification pendant sa réalisation ;
- éviter de modifier directement la branche stable ;
- vérifier une fonctionnalité avant son intégration ;
- conserver un historique lisible ;
- faciliter l'identification des différentes étapes du projet.

### 8.12 Synthèse

Git et GitHub ne sont pas uniquement utilisés comme moyen de sauvegarde.

Ils font partie du processus de développement d'EasyRDV.

Le workflow peut être résumé par :

`feature → develop → main`

Les fonctionnalités techniques, les scripts SQL, les améliorations d'accessibilité et les diagrammes ont été intégrés progressivement selon cette organisation.

## 9. Tests et accessibilité

### 9.1 Objectif des tests

Des tests fonctionnels et des vérifications du code ont été réalisés afin de contrôler les principales fonctionnalités d'EasyRDV.

Les tests portent notamment sur :

- l'inscription ;
- l'authentification ;
- la réservation ;
- la consultation des rendez-vous ;
- l'annulation ;
- la libération d'un créneau ;
- les droits d'accès à l'administration ;
- les statistiques MongoDB ;
- certains mécanismes de sécurité ;
- la navigation au clavier ;
- le fonctionnement de la version déployée.

Les résultats permettent de distinguer les fonctionnalités réellement testées dans l'application des mécanismes vérifiés directement dans le code.

### 9.2 Tableau des tests

| ID | Fonction testée | Scénario | Résultat attendu | Statut |
|---|---|---|---|---|
| T01 | Inscription | Créer un nouveau compte client | Compte créé et confirmation affichée | Réussi |
| T02 | Connexion client | Connexion avec des identifiants valides | Accès à l'espace client | Réussi |
| T03 | Mot de passe | Authentification via `password_verify()` | Mot de passe vérifié contre son hash | Réussi |
| T04 | Réservation | Choisir une prestation, une date et un créneau | Rendez-vous enregistré | Réussi |
| T05 | Espace client | Consulter ses rendez-vous | Rendez-vous affiché | Réussi |
| T06 | Annulation | Annuler son rendez-vous | Statut annulé et créneau libéré | Réussi |
| T07 | Déconnexion | Se déconnecter | Retour à la connexion | Réussi |
| T08 | Accès admin | Un client tente d'accéder à l'administration | Accès refusé ou redirection | Réussi |
| T09 | Connexion admin | Connexion avec un compte administrateur | Administration accessible | Réussi |
| T10 | Statistiques | Afficher les statistiques administrateur | Statistiques affichées | Réussi |
| T11 | MongoDB | Générer les statistiques par prestation | Documents enregistrés dans MongoDB | Réussi |
| T12 | Lecture NoSQL | Afficher les statistiques MongoDB dans l'administration | Tableau et graphique affichés | Réussi |
| T13 | Synchronisation NoSQL | Annuler un rendez-vous puis recharger l'administration | Statistique MongoDB actualisée | Réussi |
| T14 | CSRF | Vérifier les formulaires sensibles | Jeton contrôlé côté serveur | Vérifié dans le code |
| T15 | Injection SQL | Vérifier l'utilisation de PDO préparé | Valeur séparée de la requête SQL | Vérifié dans le code |
| T16 | Navigation clavier | Atteindre le champ de date avec la touche `Tab` | Focus visible | Réussi |
| T17 | Déploiement | Ouvrir EasyRDV sur InfinityFree | Application accessible | Réussi |
| T18 | Réservation en production | Effectuer une réservation sur la version en ligne | Réservation enregistrée | Réussi |

### 9.3 Test du parcours client

Un parcours fonctionnel complet a été réalisé afin de vérifier l'enchaînement des principales fonctionnalités destinées au client.

Le scénario testé est :

```text
Inscription
    ↓
Connexion
    ↓
Réservation
    ↓
Consultation du rendez-vous
    ↓
Annulation
    ↓
Libération du créneau
    ↓
Déconnexion
```

Ce parcours permet de vérifier que les différentes fonctionnalités ne fonctionnent pas uniquement de manière isolée mais également lorsqu'elles sont utilisées successivement.

### 9.4 Test de réservation

Le processus de réservation a été testé en sélectionnant :

- une prestation ;
- une date ;
- un créneau disponible.

Après validation, le rendez-vous a été enregistré et le message de confirmation a été affiché.

Le rendez-vous était ensuite visible depuis l'espace client.

Ce test permet de vérifier la communication entre :

```text
Interface de réservation
        ↓
PHP
        ↓
MySQL
        ↓
Enregistrement du rendez-vous
```

### 9.5 Test de l'annulation

Un rendez-vous enregistré a ensuite été annulé depuis l'espace client.

Le test a permis de vérifier :

- la modification du statut du rendez-vous ;
- la prise en compte de l'annulation ;
- la remise à disposition du créneau correspondant.

Cette vérification est importante car l'annulation nécessite plusieurs opérations liées entre elles.

### 9.6 Tests des droits d'accès

Les accès réservés à l'administration ont également été vérifiés.

Un utilisateur client ne doit pas pouvoir accéder aux fonctionnalités réservées à l'administrateur.

L'accès à l'administration nécessite :

1. une session utilisateur valide ;
2. un rôle `admin`.

Un compte possédant le rôle administrateur a également été utilisé afin de vérifier l'accès normal à l'interface d'administration.

### 9.7 Tests de la partie MongoDB

La fonctionnalité de statistiques NoSQL a fait l'objet d'un scénario spécifique.

Le test a consisté à :

1. consulter les statistiques présentes dans l'administration ;
2. vérifier les documents correspondants dans MongoDB ;
3. annuler un rendez-vous ;
4. recharger l'administration ;
5. vérifier l'actualisation de la statistique ;
6. contrôler le résultat dans MongoDB Compass.

Ce scénario permet de vérifier le flux :

```text
MySQL
    ↓
PHP
    ↓
MongoDB
    ↓
PHP
    ↓
Administration
```

Il permet également de confirmer que MongoDB est réellement exploité par une fonctionnalité de l'application et pas uniquement installé dans l'environnement.

### 9.8 Vérifications de sécurité

Certaines mesures de sécurité ont été contrôlées directement dans le code.

C'est notamment le cas :

- du hachage des mots de passe avec `password_hash()` ;
- de leur vérification avec `password_verify()` ;
- des requêtes préparées PDO ;
- de la présence et de la vérification des jetons CSRF ;
- du contrôle des sessions ;
- du contrôle du rôle administrateur ;
- de l'utilisation de `htmlspecialchars()`.

Ces contrôles sont distingués des tests fonctionnels afin de ne pas présenter une lecture du code comme un test d'intrusion ou un audit complet de sécurité.

### 9.9 Prise en compte de l'accessibilité

Une vérification ciblée de plusieurs éléments d'accessibilité a été réalisée sur les interfaces EasyRDV.

Les points examinés comprennent notamment :

- la présence de l'attribut `lang="fr"` ;
- la présence d'un titre de page ;
- la configuration du `viewport` pour l'affichage responsive ;
- l'utilisation de titres structurés ;
- la présence de textes alternatifs sur les images utiles ;
- l'association de libellés aux champs de formulaire ;
- l'utilisation de types de boutons adaptés ;
- la présence de certains attributs `aria-label` ;
- la visibilité du focus lors de la navigation au clavier.

### 9.10 Amélioration du focus clavier

Une amélioration spécifique a été apportée au champ de sélection de la date sur la page de réservation.

Lors de la navigation au clavier, le composant doit rester visuellement identifiable.

Une règle CSS utilisant `:focus-within` a été ajoutée :

```css
.booking-v2-date-input:focus-within {
    border-color: var(--purple);
    box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.18);
}
```

Le fonctionnement a ensuite été vérifié avec la touche `Tab`.

Le contour visuel permet à l'utilisateur d'identifier le composant actuellement actif.

### 9.11 Responsive design

Le comportement responsive fait également partie des vérifications réalisées pendant le développement.

Bootstrap et les styles CSS personnalisés permettent d'adapter les interfaces aux différentes tailles d'écran.

Cette adaptation concerne notamment :

- les blocs de contenu ;
- les formulaires ;
- les boutons ;
- les espacements ;
- la disposition des éléments.

Le responsive est également pris en compte dans la partie maquettage avec les futures déclinaisons desktop et mobile.

### 9.12 Tests de la version déployée

Après le déploiement sur InfinityFree, plusieurs fonctionnalités ont été contrôlées directement sur la version en ligne.

Les vérifications réalisées comprennent notamment :

- l'ouverture de la page d'accueil ;
- l'accès à la connexion ;
- l'authentification ;
- l'accès à l'espace client ;
- l'accès à la réservation ;
- l'enregistrement d'un rendez-vous.

Une réservation a été enregistrée avec succès sur la version déployée.

La fonctionnalité MongoDB n'est pas incluse dans ce test de production, puisque le serveur MongoDB utilisé par EasyRDV fonctionne actuellement dans l'environnement local.

### 9.13 Limites de la vérification d'accessibilité

Les vérifications réalisées montrent une prise en compte de plusieurs bonnes pratiques d'accessibilité.

Elles ne constituent cependant pas un audit RGAA complet.

Il n'est donc pas possible d'affirmer qu'EasyRDV est entièrement conforme au RGAA.

Une évaluation complète nécessiterait une vérification systématique de l'ensemble des critères applicables avec une méthodologie d'audit dédiée.

### 9.14 Synthèse

Les tests réalisés couvrent les principales fonctionnalités métier d'EasyRDV ainsi que plusieurs aspects techniques.

Ils peuvent être regroupés en quatre catégories :

```text
Tests fonctionnels
├── inscription
├── connexion
├── réservation
├── consultation
├── annulation
└── administration

Tests des données
├── MySQL
└── MongoDB

Vérifications techniques
├── sécurité
├── sessions
├── rôles
└── CSRF

Interface
├── navigation clavier
├── focus visible
├── responsive
└── accessibilité ciblée
```

Cette démarche permet de disposer d'éléments concrets pour vérifier le fonctionnement du projet tout en distinguant clairement les tests réalisés des audits complets qui n'ont pas été effectués.

## 10. Conception fonctionnelle et diagrammes

### 10.1 Objectif des diagrammes

Plusieurs diagrammes ont été réalisés afin de représenter EasyRDV sous différents points de vue.

Ils permettent de documenter :

- la structure des données ;
- les relations entre les principales entités ;
- les fonctionnalités accessibles aux différents acteurs ;
- le déroulement du processus de réservation ;
- l'architecture technique générale de l'application.

Quatre représentations principales sont utilisées :

1. le modèle de données ;
2. le diagramme de cas d'utilisation ;
3. le diagramme de séquence de réservation ;
4. le diagramme d'architecture technique.

Ces diagrammes sont enregistrés dans :

`docs/diagrams/`

### 10.2 Modèle de données

Le modèle de données est disponible dans :

`docs/diagrams/mcd-easyrdv.png`

Il représente les principales entités utilisées par EasyRDV :

- `UTILISATEUR` ;
- `PRESTATION` ;
- `RENDEZ_VOUS` ;
- `DISPONIBILITE`.

Les relations principales concernent les utilisateurs, les prestations et les rendez-vous.

Un utilisateur peut posséder plusieurs rendez-vous, tandis qu'un rendez-vous appartient à un seul utilisateur.

Une prestation peut être associée à plusieurs rendez-vous, tandis qu'un rendez-vous concerne une seule prestation.

Dans l'implémentation actuelle, la table des disponibilités est gérée indépendamment et ne possède pas de clé étrangère vers la table des rendez-vous.

Le modèle permet ainsi de présenter visuellement l'organisation des données utilisée ensuite dans MySQL.

### 10.3 Diagramme de cas d'utilisation

Le diagramme de cas d'utilisation est disponible dans :

`docs/diagrams/diagramme-cas-utilisation-easyrdv.png`

Il présente les interactions entre les acteurs et le système EasyRDV.

Deux acteurs principaux sont identifiés :

- le client ;
- l'administrateur.

#### Fonctionnalités du client

Le client peut notamment :

- s'inscrire ;
- se connecter ;
- consulter les prestations ;
- réserver un rendez-vous ;
- consulter ses rendez-vous ;
- annuler un rendez-vous ;
- se déconnecter.

#### Fonctionnalités de l'administrateur

L'administrateur peut notamment :

- se connecter ;
- accéder à l'administration ;
- gérer les rendez-vous ;
- gérer les prestations ;
- gérer les disponibilités ;
- consulter les statistiques ;
- se déconnecter.

Le diagramme permet ainsi de délimiter les principales fonctionnalités proposées à chaque type d'utilisateur.

### 10.4 Diagramme de séquence de réservation

Le diagramme de séquence est disponible dans :

`docs/diagrams/diagramme-sequence-reservation-easyrdv.png`

Il décrit le scénario principal de réservation d'un rendez-vous.

Les principaux participants représentés sont :

- le client ;
- la page de réservation ;
- le serveur PHP ;
- la base MySQL.

Le scénario peut être résumé ainsi :

```text
Client
    ↓
Ouverture de la page de réservation
    ↓
PHP récupère les prestations et disponibilités
    ↓
MySQL retourne les données
    ↓
Affichage des possibilités
    ↓
Sélection d'une prestation
    ↓
Sélection d'une date et d'un créneau
    ↓
Validation du formulaire
    ↓
Envoi des données + jeton CSRF
    ↓
Contrôles côté PHP
    ↓
Début de la transaction SQL
    ↓
Création du rendez-vous
    ↓
Mise à jour de la disponibilité
    ↓
COMMIT
    ↓
Confirmation de la réservation
```

Avant l'enregistrement, le serveur vérifie notamment :

- la session utilisateur ;
- le jeton CSRF ;
- les données nécessaires à la réservation ;
- la prestation ;
- la disponibilité du créneau.

Si une erreur intervient pendant une opération transactionnelle, les modifications concernées peuvent être annulées avec un `ROLLBACK`.

Lorsque toutes les opérations réussissent, la transaction est validée avec un `COMMIT`.

Le client reçoit ensuite la confirmation de l'enregistrement du rendez-vous.

### 10.5 Intérêt du diagramme de séquence

Le diagramme de séquence complète le diagramme de cas d'utilisation.

Le cas d'utilisation indique que le client peut « réserver un rendez-vous ».

Le diagramme de séquence montre, quant à lui, comment cette fonctionnalité est réellement exécutée entre :

```text
Client
    ↕
Interface
    ↕
PHP
    ↕
MySQL
```

Il permet donc de représenter les échanges techniques nécessaires à la réalisation d'une fonctionnalité métier importante.

### 10.6 Diagramme d'architecture technique

Le diagramme d'architecture est disponible dans :

`docs/diagrams/diagramme-architecture-technique-easyrdv.png`

Il présente l'organisation technique générale d'EasyRDV.

Il représente notamment :

- les utilisateurs de l'application ;
- le navigateur ;
- HTML5 ;
- CSS3 ;
- Bootstrap ;
- JavaScript ;
- le serveur PHP ;
- l'accès à MySQL avec PDO ;
- MongoDB pour les statistiques ;
- l'environnement local ;
- Git et GitHub ;
- l'environnement de déploiement.

Il permet également de distinguer la partie MongoDB utilisée localement du déploiement PHP/MySQL réalisé sur InfinityFree.

### 10.7 Représentation simplifiée de l'architecture

L'architecture peut être résumée ainsi :

```text
                    UTILISATEURS
                 Client / Administrateur
                          │
                          ▼
                       Navigateur
                          │
              HTML / CSS / Bootstrap / JS
                          │
                          ▼
                         PHP
                  Logique applicative
                   Sécurité / Sessions
                    /             \
                   /               \
                  ▼                 ▼
             PDO / MySQL         MongoDB
             Données métier      Statistiques
                  │                 │
                  └───────┬─────────┘
                          │
                          ▼
                 Interface EasyRDV
```

Dans l'environnement déployé actuel, la partie publique repose principalement sur PHP et MySQL. MongoDB reste utilisé dans l'environnement local.

### 10.8 Complémentarité des diagrammes

Les différents diagrammes ne présentent pas les mêmes informations.

#### Modèle de données

Il répond principalement à la question :

**Quelles données sont utilisées et comment sont-elles liées ?**

#### Diagramme de cas d'utilisation

Il répond principalement à la question :

**Que peut faire chaque acteur dans EasyRDV ?**

#### Diagramme de séquence

Il répond principalement à la question :

**Comment se déroule techniquement une réservation ?**

#### Diagramme d'architecture

Il répond principalement à la question :

**Comment les différentes technologies et couches du projet sont-elles organisées ?**

Les quatre représentations sont donc complémentaires :

```text
Modèle de données
        ↓
Structure des informations

Cas d'utilisation
        ↓
Fonctionnalités et acteurs

Diagramme de séquence
        ↓
Déroulement d'une fonctionnalité

Architecture technique
        ↓
Organisation des technologies
```

### 10.9 Utilisation dans le dossier projet

Ces diagrammes constituent également des éléments de preuve importants pour le dossier projet.

Ils permettront notamment d'illustrer :

- la conception de la base de données ;
- l'analyse fonctionnelle ;
- la logique de réservation ;
- les choix d'architecture.

Les images correspondantes pourront être intégrées directement dans le dossier projet avec un titre, une légende et une explication.

### 10.10 Synthèse

La documentation graphique d'EasyRDV permet de présenter le projet à plusieurs niveaux de lecture.

Le modèle de données décrit les informations manipulées.

Le diagramme de cas d'utilisation présente les interactions des utilisateurs avec le système.

Le diagramme de séquence détaille le fonctionnement de la réservation.

Enfin, le diagramme d'architecture présente l'organisation générale des composants techniques.

Ensemble, ces éléments facilitent la compréhension du projet et complètent la documentation du code source.

## 11. Installation et lancement en environnement local

### 11.1 Objectif

Cette section décrit les principales étapes permettant d'installer et d'exécuter EasyRDV dans un environnement local similaire à celui utilisé pendant le développement.

L'environnement local permet de tester l'ensemble du projet, y compris :

- l'application PHP ;
- la base relationnelle MySQL ;
- les fonctionnalités client et administrateur ;
- la partie statistique NoSQL avec MongoDB.

### 11.2 Prérequis

Les principaux outils nécessaires sont :

- XAMPP avec Apache, PHP et MySQL ;
- un navigateur web ;
- phpMyAdmin ;
- MongoDB Community Server ;
- l'extension MongoDB activée dans PHP ;
- MongoDB Compass, facultatif mais utile pour consulter les documents ;
- Git si le projet est récupéré depuis le dépôt GitHub ;
- un éditeur de code tel que Visual Studio Code.

### 11.3 Installation des fichiers du projet

Le projet doit être placé dans le répertoire web utilisé par XAMPP.

Dans l'environnement de développement d'EasyRDV, le chemin utilisé est :

```text
C:\xampp\htdocs\EasyRDV
```

L'arborescence principale doit notamment contenir :

```text
EasyRDV/
├── assets/
├── config/
├── database/
├── docs/
├── pages/
├── services/
├── index.html
├── README.md
└── .gitignore
```

### 11.4 Démarrage des services XAMPP

Depuis le panneau de contrôle XAMPP, les services suivants doivent être démarrés :

- Apache ;
- MySQL.

Apache permet d'exécuter l'application PHP depuis le navigateur.

MySQL fournit la base de données relationnelle utilisée par EasyRDV.

### 11.5 Création de la base MySQL

La structure de la base peut être créée à partir du fichier :

`database/schema.sql`

Le script peut être exécuté avec phpMyAdmin.

La base locale utilisée par EasyRDV porte le nom :

`easyrdv`

Le script permet de créer les tables nécessaires au fonctionnement de l'application.

### 11.6 Initialisation des données de démonstration

Après la création de la structure, les données de démonstration peuvent être ajoutées avec :

`database/seed.sql`

Ce fichier permet notamment d'insérer des prestations et des disponibilités utiles aux tests.

Il ne contient volontairement pas les comptes utilisateurs réels utilisés pendant le développement.

### 11.7 Configuration de la connexion MySQL

Le fichier :

`config/database.php`

contient la configuration utilisée par PHP pour établir la connexion à MySQL avec PDO.

Les paramètres doivent correspondre à l'environnement local utilisé.

Ce fichier n'est pas versionné dans GitHub car il est exclu par `.gitignore`.

Il doit donc être présent et correctement configuré localement avant l'utilisation de l'application.

Les informations sensibles de connexion ne doivent pas être publiées dans la documentation ou dans le dépôt Git.

### 11.8 Configuration de MongoDB

Pour utiliser la fonctionnalité NoSQL, MongoDB Community Server doit être installé et démarré.

EasyRDV utilise localement la connexion :

```text
mongodb://localhost:27017
```

La base utilisée est :

```text
easyrdv_nosql
```

La collection utilisée pour les statistiques est :

```text
statistiques_rendez_vous
```

MongoDB Compass peut être utilisé pour vérifier graphiquement la présence de cette base, de la collection et des documents.

### 11.9 Extension MongoDB pour PHP

PHP doit disposer de l'extension nécessaire pour communiquer avec MongoDB.

Dans l'environnement utilisé pendant le développement, le pilote MongoDB PHP a été installé puis activé dans la configuration PHP utilisée par Apache.

L'extension doit être disponible avant de tester la fonctionnalité de statistiques NoSQL.

### 11.10 Premier lancement de l'application

Une fois Apache et MySQL démarrés et la base correctement configurée, l'application peut être ouverte depuis le navigateur à l'adresse locale correspondant au dossier EasyRDV.

La page `index.html` constitue le point d'entrée de l'application.

Le parcours client peut ensuite être testé dans l'ordre suivant :

```text
Page d'accueil
      ↓
Inscription
      ↓
Connexion
      ↓
Consultation des prestations
      ↓
Réservation
      ↓
Espace client
      ↓
Annulation
      ↓
Déconnexion
```

### 11.11 Création d'un compte client

Le fichier `seed.sql` ne contient volontairement pas de compte client réel.

Un compte peut être créé directement depuis le formulaire d'inscription de l'application.

Lors de cette opération, le mot de passe est haché avant son enregistrement dans MySQL.

Le compte créé peut ensuite être utilisé pour tester l'authentification et le parcours client.

### 11.12 Compte administrateur

Les fonctionnalités d'administration nécessitent un utilisateur possédant le rôle :

`admin`

Un compte de test avec ce rôle doit être préparé dans l'environnement de test pour vérifier les fonctionnalités administratives.

Les identifiants et mots de passe utilisés pour ce compte ne doivent pas être publiés dans le dépôt GitHub ni dans cette documentation technique.

### 11.13 Vérification du parcours client

Après l'installation, le parcours suivant peut être vérifié :

1. créer un compte ;
2. se connecter ;
3. consulter les prestations ;
4. choisir une prestation ;
5. sélectionner une date ;
6. sélectionner un créneau disponible ;
7. enregistrer le rendez-vous ;
8. consulter le rendez-vous dans l'espace client ;
9. annuler le rendez-vous ;
10. vérifier que le créneau est de nouveau disponible ;
11. se déconnecter.

Ce scénario permet de contrôler les principales fonctionnalités métier de l'application.

### 11.14 Vérification de l'administration

Avec un compte administrateur, il est possible de vérifier notamment :

- l'accès protégé à l'administration ;
- l'affichage des rendez-vous ;
- la gestion des prestations ;
- la gestion des disponibilités ;
- l'affichage des statistiques.

L'accès doit être refusé ou redirigé lorsque l'utilisateur ne possède pas le rôle nécessaire.

### 11.15 Vérification de MongoDB

Pour tester la partie NoSQL :

1. démarrer MongoDB Community Server ;
2. vérifier que l'extension MongoDB est disponible dans PHP ;
3. démarrer Apache et MySQL ;
4. accéder à l'administration avec un compte administrateur ;
5. consulter les statistiques ;
6. ouvrir MongoDB Compass ;
7. consulter la collection `statistiques_rendez_vous`.

Le flux à contrôler est :

```text
MySQL
    ↓
PHP
    ↓
MongoDB
    ↓
PHP
    ↓
Administration
```

### 11.16 Points de contrôle après installation

Après l'installation locale, les éléments suivants doivent être vérifiés :

- la page d'accueil est accessible ;
- l'inscription fonctionne ;
- la connexion fonctionne ;
- les prestations sont accessibles ;
- les disponibilités sont proposées ;
- une réservation peut être enregistrée ;
- le rendez-vous apparaît dans l'espace client ;
- l'annulation fonctionne ;
- le créneau est libéré après l'annulation ;
- la déconnexion fonctionne ;
- l'administration est protégée ;
- un administrateur peut accéder à son interface ;
- les statistiques MongoDB fonctionnent lorsque l'environnement NoSQL est démarré.

### 11.17 Résolution des principaux problèmes de configuration

En cas de problème au lancement, plusieurs éléments peuvent être contrôlés.

#### L'application ne s'affiche pas

Vérifier :

- qu'Apache est démarré ;
- que le projet est placé dans le répertoire `htdocs` ;
- que l'adresse locale utilisée correspond au dossier EasyRDV.

#### La connexion MySQL échoue

Vérifier :

- que MySQL est démarré ;
- que la base `easyrdv` existe ;
- que `config/database.php` contient les paramètres adaptés à l'environnement local.

#### Les statistiques MongoDB ne fonctionnent pas

Vérifier :

- que MongoDB Community Server est démarré ;
- que le serveur répond sur `mongodb://localhost:27017` ;
- que l'extension MongoDB est activée dans PHP ;
- qu'Apache utilise bien la configuration PHP dans laquelle l'extension a été activée.

### 11.18 Synthèse de l'installation locale

L'installation locale d'EasyRDV peut être résumée ainsi :

```text
Installation du projet dans htdocs
        ↓
Démarrage Apache + MySQL
        ↓
Création de la base avec schema.sql
        ↓
Ajout des données avec seed.sql
        ↓
Configuration de database.php
        ↓
Démarrage de MongoDB
        ↓
Vérification de l'extension PHP MongoDB
        ↓
Ouverture d'EasyRDV
        ↓
Tests client
        ↓
Tests administrateur
        ↓
Vérification MongoDB
```

Cette procédure permet de reproduire l'environnement principal utilisé pour le développement et les tests d'EasyRDV.

## 12. Déploiement de l'application

### 12.1 Objectif du déploiement

Une version d'EasyRDV a été déployée sur un hébergement web afin de rendre l'application accessible depuis Internet et de vérifier son fonctionnement en dehors de l'environnement local XAMPP.

L'hébergement utilisé pour le projet est :

`InfinityFree`

L'application est accessible avec le domaine :

`easyrdv-app.page.gd`

Le déploiement permet notamment de vérifier le fonctionnement de la partie PHP/MySQL dans un environnement distant.

### 12.2 Différence entre développement local et déploiement

Deux environnements sont distingués.

#### Environnement local

L'environnement local utilisé pour le développement comprend :

- XAMPP ;
- Apache ;
- PHP ;
- MySQL ;
- MongoDB ;
- MongoDB Compass.

Il permet de tester l'ensemble des fonctionnalités développées, y compris les statistiques NoSQL.

#### Environnement déployé

L'environnement InfinityFree utilise principalement :

- le serveur web fourni par l'hébergeur ;
- PHP ;
- MySQL.

MongoDB n'est actuellement pas hébergé dans cet environnement.

La fonctionnalité NoSQL de statistiques reste donc testée dans l'environnement local.

### 12.3 Préparation d'une copie de déploiement

Une copie spécifique du projet a été préparée avant le transfert vers InfinityFree.

Cette copie permet de conserver séparément :

```text
EasyRDV
    ↓
Version locale de développement

EasyRDV-PROD
    ↓
Version préparée pour le déploiement
```

Dans l'environnement utilisé pendant le projet, la copie de déploiement a été préparée dans :

```text
C:\xampp\htdocs\EasyRDV-PROD
```

Cette séparation permet notamment d'adapter la configuration de la base de données à l'hébergement sans modifier la configuration utilisée pour le développement local.

### 12.4 Préparation de la base MySQL distante

Une base MySQL a été créée depuis l'interface d'administration d'InfinityFree.

La base locale EasyRDV a ensuite été exportée au format SQL avec phpMyAdmin.

Le processus suivi peut être résumé ainsi :

```text
Base MySQL locale
        ↓
Export SQL avec phpMyAdmin
        ↓
Fichier SQL
        ↓
phpMyAdmin InfinityFree
        ↓
Import
        ↓
Base MySQL distante
```

L'importation a permis de recréer sur l'hébergement les tables nécessaires au fonctionnement de l'application.

### 12.5 Configuration de la connexion en production

La copie de :

`config/database.php`

destinée au déploiement a été configurée avec les paramètres MySQL fournis par InfinityFree.

Les paramètres nécessaires comprennent notamment :

- l'hôte MySQL ;
- le nom de la base ;
- le nom de l'utilisateur MySQL ;
- le mot de passe ;
- l'encodage de la connexion.

Les valeurs sensibles ne sont pas indiquées dans cette documentation.

Elles ne doivent pas non plus être publiées dans le dépôt GitHub.

### 12.6 Séparation des configurations

La configuration utilisée localement et celle utilisée pour le déploiement ne sont pas identiques.

Le principe est :

```text
Environnement local
        ↓
config/database.php
        ↓
MySQL local

Environnement déployé
        ↓
config/database.php adapté
        ↓
MySQL InfinityFree
```

Cette séparation permet d'éviter de remplacer accidentellement les paramètres de connexion d'un environnement par ceux de l'autre.

### 12.7 Préparation des fichiers

Les fichiers de la copie de déploiement ont été regroupés avant leur transfert vers InfinityFree.

La version destinée à l'hébergement conserve l'organisation générale du projet nécessaire au fonctionnement de l'application.

Les informations sensibles ne doivent pas être ajoutées au dépôt GitHub pendant cette opération.

### 12.8 Transfert vers InfinityFree

Les fichiers de l'application ont été transférés vers l'espace d'hébergement InfinityFree.

Les fichiers publics ont été placés dans le répertoire web :

`htdocs`

Une vérification de l'arborescence a ensuite été effectuée afin que la page d'accueil et les différents dossiers nécessaires se trouvent directement à l'emplacement attendu par le serveur.

Le principe est :

```text
InfinityFree
    │
    └── htdocs/
        ├── assets/
        ├── config/
        ├── pages/
        ├── services/
        └── index.html
```

### 12.9 Ouverture de l'application déployée

Après le transfert des fichiers et la configuration de la base distante, l'application a été ouverte depuis son domaine public :

`easyrdv-app.page.gd`

L'affichage de la page d'accueil a permis de vérifier que les fichiers étaient correctement accessibles depuis le serveur web.

### 12.10 Vérifications fonctionnelles après déploiement

Plusieurs contrôles ont été réalisés sur la version en ligne.

Les vérifications ont notamment porté sur :

- l'ouverture de la page d'accueil ;
- l'accès à la page de connexion ;
- l'authentification d'un utilisateur ;
- l'affichage de l'espace client ;
- l'accès à la page de réservation ;
- l'enregistrement d'un rendez-vous dans la base distante.

Une réservation a été réalisée avec succès sur la version déployée.

Cette vérification confirme le fonctionnement du flux principal :

```text
Navigateur
    ↓
Application EasyRDV en ligne
    ↓
PHP
    ↓
MySQL InfinityFree
    ↓
Enregistrement du rendez-vous
```

### 12.11 Vérification de la base distante

Le fonctionnement de la réservation permet également de vérifier que PHP communique correctement avec la base MySQL distante.

L'application déployée ne dépend donc pas de la base MySQL locale utilisée avec XAMPP pour enregistrer ses rendez-vous.

La version en ligne utilise la configuration propre à l'environnement InfinityFree.

### 12.12 Cas particulier de MongoDB

La fonctionnalité MongoDB nécessite le serveur MongoDB utilisé dans l'environnement local.

InfinityFree n'héberge pas ce serveur MongoDB dans le déploiement actuel d'EasyRDV.

Par conséquent, le fonctionnement complet du projet est actuellement réparti de la manière suivante :

```text
ENVIRONNEMENT LOCAL
PHP
├── MySQL
└── MongoDB
    └── statistiques

ENVIRONNEMENT INFINITYFREE
PHP
└── MySQL
    └── fonctionnalités web principales
```

Il est important de conserver cette distinction dans la présentation du projet.

La fonctionnalité NoSQL ne doit pas être présentée comme disponible dans l'environnement public actuel.

### 12.13 Sécurité du déploiement

Les identifiants de connexion à la base distante ne doivent pas être publiés dans :

- GitHub ;
- le README public ;
- la documentation technique ;
- le dossier projet ;
- les captures d'écran remises au jury.

Le fichier de configuration utilisé dans le projet de développement est exclu de Git avec `.gitignore`.

Une configuration adaptée est utilisée dans la copie destinée à l'hébergement.

### 12.14 Procédure synthétique de déploiement

Le processus suivi peut être résumé ainsi :

```text
Projet EasyRDV local
        ↓
Préparation de EasyRDV-PROD
        ↓
Création de la base MySQL distante
        ↓
Export de la base MySQL locale
        ↓
Import dans MySQL InfinityFree
        ↓
Configuration de database.php
        ↓
Préparation des fichiers
        ↓
Transfert dans htdocs
        ↓
Ouverture du domaine public
        ↓
Tests fonctionnels
        ↓
Réservation en production réussie
```

### 12.15 Limites du déploiement actuel

Le déploiement actuel permet de présenter et de tester les principales fonctionnalités PHP/MySQL d'EasyRDV depuis Internet.

La principale différence avec l'environnement local concerne MongoDB.

La partie statistique NoSQL est développée et testée localement mais n'est pas disponible sur InfinityFree.

Cette limite est identifiée et documentée plutôt que masquée.

### 12.16 Évolution possible du déploiement

Une évolution future pourrait consister à utiliser un service MongoDB distant compatible avec l'environnement de production.

L'architecture deviendrait alors :

```text
Application web déployée
        │
        ├── MySQL distant
        │   └── données métier
        │
        └── MongoDB distant
            └── statistiques
```

Cela permettrait de rendre également la fonctionnalité NoSQL disponible depuis la version publique d'EasyRDV.

### 12.17 Synthèse

Le déploiement sur InfinityFree a permis de vérifier qu'EasyRDV peut fonctionner en dehors de l'environnement local XAMPP.

La procédure a nécessité :

- la préparation d'une version de déploiement ;
- la création d'une base MySQL distante ;
- l'export et l'import de la base ;
- l'adaptation de la configuration PHP ;
- le transfert des fichiers ;
- des tests fonctionnels en ligne.

Le déploiement constitue ainsi une étape distincte du développement local et fait partie du cycle de réalisation d'EasyRDV.

## 13. Conception des interfaces, maquettage et charte graphique

### 13.1 Objectif de la conception des interfaces

La conception des interfaces constitue une étape importante du projet EasyRDV.

Elle permet de définir l'organisation visuelle de l'application avant ou pendant son intégration et de maintenir une cohérence entre les différentes pages.

Les principaux objectifs sont :

- proposer une navigation simple ;
- hiérarchiser clairement les informations ;
- faciliter la compréhension des formulaires ;
- conserver une identité graphique cohérente ;
- adapter les interfaces aux écrans desktop et mobile ;
- faciliter l'accès aux principales actions.

La démarche générale peut être représentée ainsi :

```text
Analyse du besoin
        ↓
Définition des principales interfaces
        ↓
Maquettage
        ↓
Définition de l'identité visuelle
        ↓
Intégration HTML / CSS / Bootstrap
        ↓
Interactions JavaScript
        ↓
Connexion aux traitements PHP
        ↓
Tests et ajustements
```

### 13.2 Outil de maquettage

Figma est retenu comme outil de conception des maquettes d'EasyRDV.

Il permet de préparer et de présenter visuellement les principales interfaces avant leur intégration définitive.

Le travail de maquettage porte notamment sur :

- l'organisation des contenus ;
- la hiérarchie visuelle ;
- le positionnement des composants ;
- les formulaires ;
- les boutons et actions principales ;
- la navigation ;
- la cohérence entre les différentes pages ;
- l'adaptation des interfaces aux formats desktop et mobile.

### 13.3 Maquettes à réaliser

Le livrable final de maquettage comprendra six maquettes :

- trois maquettes au format desktop ;
- trois maquettes au format mobile.

Ces maquettes permettront de présenter plusieurs interfaces représentatives d'EasyRDV dans les deux formats.

Elles seront exportées depuis Figma et intégrées aux livrables graphiques du projet.

**État actuel : les six maquettes Figma restent à finaliser avant la remise définitive du projet.**

Les noms exacts des interfaces retenues et les références des fichiers exportés seront ajoutés à cette documentation une fois les maquettes effectivement réalisées.

### 13.4 Conception responsive

EasyRDV est conçu pour s'adapter à différentes tailles d'écran.

La version desktop dispose d'un espace horizontal plus important, tandis que la version mobile nécessite une réorganisation de certains composants.

L'adaptation responsive concerne notamment :

- la largeur des blocs ;
- les espacements ;
- la disposition des éléments ;
- les formulaires ;
- les boutons ;
- les cartes ;
- la navigation ;
- la lisibilité des textes.

Lors de l'intégration, Bootstrap et les styles CSS personnalisés sont utilisés pour adapter l'affichage.

Le maquettage desktop/mobile permettra également de formaliser cette adaptation dans les livrables de conception.

### 13.5 Identité visuelle d'EasyRDV

EasyRDV utilise une identité graphique moderne, colorée et facilement identifiable.

L'interface repose notamment sur plusieurs familles de couleurs :

- violet ;
- turquoise ou menthe ;
- corail ou orange ;
- jaune ;
- fonds clairs et tons pastel.

Ces couleurs sont utilisées pour différencier les éléments importants, mettre en valeur les actions et conserver une identité cohérente entre les pages.

Les valeurs exactes des couleurs seront relevées dans les styles réellement utilisés par l'application lors de la réalisation de la charte graphique finale.

Cette méthode évite de documenter des valeurs qui ne correspondraient pas à l'interface effectivement développée.

### 13.6 Principes graphiques

Plusieurs principes visuels sont utilisés dans les interfaces EasyRDV :

- blocs clairement délimités ;
- cartes pour structurer les informations ;
- boutons facilement identifiables ;
- formulaires organisés ;
- espacements réguliers ;
- bordures visibles ;
- éléments arrondis ;
- utilisation de couleurs pour hiérarchiser les actions ;
- mise en page adaptée au type de contenu présenté.

L'objectif est de conserver une interface vivante tout en maintenant une lecture structurée.

### 13.7 Typographie et hiérarchie des textes

La typographie participe à la hiérarchie visuelle de l'application.

Plusieurs niveaux sont distingués :

- titre principal de page ;
- titres de sections ;
- sous-titres ;
- textes courants ;
- libellés de formulaires ;
- textes des boutons ;
- messages d'information ou de confirmation.

Les références exactes de la ou des polices réellement utilisées seront indiquées dans la charte graphique après vérification des styles finaux de l'application.

### 13.8 Composants graphiques récurrents

Plusieurs composants sont réutilisés dans EasyRDV afin de conserver une cohérence entre les différentes interfaces.

Il s'agit notamment :

- des boutons principaux ;
- des boutons secondaires ;
- des champs de formulaire ;
- des cartes ;
- des blocs d'information ;
- des messages de confirmation ;
- des éléments de navigation ;
- des composants de réservation ;
- des composants de l'espace client ;
- des composants de l'administration.

La réutilisation de principes visuels similaires facilite la compréhension de l'interface par l'utilisateur.

### 13.9 Conception de la page de réservation

La page de réservation constitue une interface importante du projet.

Elle doit permettre au client de comprendre facilement les différentes étapes nécessaires à la création d'un rendez-vous.

Le parcours peut être résumé ainsi :

```text
Choix de la prestation
        ↓
Choix de la date
        ↓
Choix du créneau
        ↓
Vérification des informations
        ↓
Validation
        ↓
Confirmation
```

L'organisation visuelle doit permettre d'identifier clairement l'étape en cours et les choix réalisés.

JavaScript complète ensuite l'interface afin de rendre certaines interactions dynamiques.

### 13.10 Cohérence avec l'espace client

L'espace client reprend les mêmes principes graphiques généraux que les autres pages d'EasyRDV.

Il permet notamment de présenter les rendez-vous de manière structurée et de rendre clairement identifiable l'action d'annulation.

La cohérence visuelle entre la réservation et l'espace client permet de conserver une continuité dans le parcours utilisateur.

### 13.11 Conception de l'administration

L'interface d'administration doit afficher davantage d'informations tout en restant structurée.

Elle regroupe notamment :

- les informations générales ;
- les rendez-vous ;
- les prestations ;
- les disponibilités ;
- les statistiques.

L'utilisation de cartes, de sections distinctes, de tableaux et d'une représentation graphique permet de séparer les différentes catégories d'informations.

L'administration conserve l'identité générale d'EasyRDV tout en proposant une présentation adaptée à un usage de gestion.

### 13.12 Charte graphique

Une charte graphique EasyRDV sera produite comme livrable distinct.

Elle permettra de formaliser les choix visuels réellement utilisés dans l'application.

Elle présentera notamment :

- l'identité visuelle du projet ;
- la palette de couleurs avec les références exactes ;
- les typographies ;
- la hiérarchie des textes ;
- les styles de boutons ;
- les champs de formulaire ;
- les cartes et composants principaux ;
- les principes d'espacement et de mise en page ;
- les adaptations desktop et mobile ;
- des exemples issus des interfaces ;
- les maquettes réalisées.

**État actuel : le PDF de charte graphique reste à produire avant la remise définitive du projet.**

Les couleurs et typographies exactes seront relevées à partir des styles réellement utilisés dans EasyRDV afin que la charte corresponde à l'application finale.

### 13.13 Passage de la conception à l'intégration

Le passage de la conception graphique à l'application fonctionnelle peut être représenté ainsi :

```text
Maquettes Figma
        ↓
HTML5
        ↓
CSS3 / Bootstrap
        ↓
JavaScript
        ↓
PHP
        ↓
MySQL / MongoDB
        ↓
Tests
```

HTML permet de structurer les contenus.

CSS et Bootstrap assurent la présentation et l'adaptation responsive.

JavaScript ajoute les interactions côté navigateur.

PHP relie ensuite les interfaces aux règles métier et aux traitements serveur.

MySQL et MongoDB fournissent les données nécessaires aux différentes fonctionnalités.

### 13.14 Démarche itérative

La conception d'une interface ne s'arrête pas nécessairement après la première maquette.

Des ajustements peuvent être nécessaires pendant le développement et les tests.

Le fonctionnement suivi peut être représenté ainsi :

```text
Conception
    ↓
Intégration
    ↓
Test
    ↓
Observation d'un problème
    ↓
Correction de l'interface
    ↓
Nouveau test
```

Cette démarche permet d'améliorer progressivement l'ergonomie et la cohérence des interfaces.

### 13.15 Accessibilité dans la conception

La conception des interfaces prend également en compte plusieurs principes d'accessibilité.

Cela concerne notamment :

- la lisibilité des contenus ;
- l'association de libellés aux formulaires ;
- la navigation au clavier ;
- la visibilité du focus ;
- la présence de textes alternatifs lorsque nécessaire ;
- la structuration des titres.

Ces éléments complètent les choix purement esthétiques afin que l'interface reste compréhensible et utilisable.

Cette prise en compte reste ciblée et ne constitue pas un audit complet de conformité RGAA.

### 13.16 État des livrables graphiques

Au moment de la rédaction de cette version de la documentation technique :

| Livrable | État |
|---|---|
| Interfaces fonctionnelles EasyRDV | Réalisées |
| Responsive de l'application | Mis en œuvre |
| Identité visuelle | Mise en œuvre dans l'application |
| Maquettes Figma desktop | À finaliser |
| Maquettes Figma mobile | À finaliser |
| Charte graphique PDF | À réaliser |

Cette distinction permet de documenter précisément l'état réel du projet sans présenter comme terminés des livrables qui restent à produire.

Une fois les six maquettes et la charte graphique terminées, cette section sera mise à jour avec :

- les interfaces retenues ;
- les noms des fichiers ;
- les références exactes des couleurs ;
- les typographies ;
- les exports définitifs.

### 13.17 Synthèse

La conception des interfaces d'EasyRDV associe :

```text
Ergonomie
    +
Identité visuelle
    +
Responsive
    +
Accessibilité
    +
Cohérence des composants
        ↓
Interface utilisateur EasyRDV
```

Le maquettage Figma et la charte graphique permettront de formaliser ces choix dans les livrables finaux.

Ils compléteront les interfaces déjà développées et permettront de présenter au jury la démarche allant de la conception visuelle jusqu'à l'application fonctionnelle.

## 14. Bilan technique, limites et évolutions possibles

### 14.1 Bilan technique du projet

EasyRDV a permis de mettre en œuvre les principales étapes de réalisation d'une application web dynamique.

Le projet combine plusieurs compétences front-end, back-end, base de données, sécurité, documentation, versionnement et déploiement.

Les principaux éléments techniques réalisés sont :

- des interfaces web en HTML5 ;
- une mise en forme avec CSS3 et Bootstrap ;
- une adaptation responsive ;
- des interactions dynamiques avec JavaScript ;
- un back-end développé en PHP ;
- une authentification avec gestion des sessions ;
- une gestion des rôles client et administrateur ;
- une base de données relationnelle MySQL ;
- un accès aux données avec PDO ;
- des requêtes préparées ;
- des transactions SQL ;
- une base NoSQL MongoDB ;
- un composant PHP d'accès à MongoDB ;
- des statistiques administratives ;
- plusieurs mécanismes de sécurité ;
- des tests fonctionnels ;
- une prise en compte ciblée de l'accessibilité ;
- une gestion de versions avec Git et GitHub ;
- une organisation `feature → develop → main` ;
- des scripts SQL de création et d'initialisation ;
- plusieurs diagrammes de conception et d'architecture ;
- une documentation d'installation ;
- un déploiement PHP/MySQL sur InfinityFree.

Le projet ne se limite donc pas à la réalisation des interfaces : il couvre également la gestion des données, les traitements serveur, la sécurité, les tests, le versionnement et le déploiement.

### 14.2 Correspondance avec l'architecture globale

L'architecture finale du projet peut être résumée ainsi :

```text
                 UTILISATEUR
             Client / Administrateur
                      │
                      ▼
                   Navigateur
                      │
          HTML / CSS / Bootstrap
                JavaScript
                      │
                      ▼
                     PHP
          ┌───────────┼───────────┐
          │           │           │
          ▼           ▼           ▼
     Sessions      Sécurité    Logique métier
          │           │           │
          └───────────┼───────────┘
                      │
              ┌───────┴───────┐
              ▼               ▼
          PDO / MySQL       MongoDB
              │               │
              ▼               ▼
        Données métier    Statistiques
```

Cette architecture permet de séparer les responsabilités entre l'interface, les traitements serveur et les différents systèmes de stockage.

### 14.3 Principales fonctionnalités réalisées

Du côté client, EasyRDV permet notamment :

- de créer un compte ;
- de se connecter ;
- de consulter les prestations ;
- de réserver un rendez-vous ;
- de consulter ses rendez-vous ;
- d'annuler un rendez-vous ;
- de se déconnecter.

Du côté administrateur, l'application permet notamment :

- de se connecter avec un rôle administrateur ;
- d'accéder à une interface protégée ;
- de gérer les rendez-vous ;
- de gérer les prestations ;
- de gérer les disponibilités ;
- de consulter des statistiques.

Ces fonctionnalités permettent de couvrir le parcours principal attendu pour une application de réservation destinée à un salon de coiffure.

### 14.4 Gestion des données

EasyRDV utilise deux technologies de stockage complémentaires.

#### MySQL

MySQL constitue la source principale des données métier :

- utilisateurs ;
- prestations ;
- disponibilités ;
- rendez-vous.

Le modèle relationnel permet de représenter les relations entre ces informations et de maintenir la cohérence des opérations métier.

#### MongoDB

MongoDB est utilisé pour stocker des statistiques de rendez-vous par prestation.

Cette partie permet de mettre en œuvre un accès NoSQL distinct de l'accès relationnel MySQL.

Le fonctionnement général est :

```text
MySQL
    ↓
Calcul PHP
    ↓
MongoDB
    ↓
Lecture PHP
    ↓
Administration
```

### 14.5 Sécurité mise en œuvre

Plusieurs mécanismes de sécurité sont intégrés au projet :

- `password_hash()` pour le hachage des mots de passe ;
- `password_verify()` pour leur vérification ;
- PDO et les requêtes préparées pour les accès SQL concernés ;
- les jetons CSRF pour les actions sensibles ;
- les sessions PHP pour l'authentification ;
- le contrôle du rôle `admin` ;
- `htmlspecialchars()` pour différents affichages dynamiques ;
- les transactions SQL pour maintenir la cohérence de certaines opérations ;
- `.gitignore` pour éviter de publier la configuration locale.

Ces mécanismes constituent des mesures de sécurité intégrées au développement.

Ils ne doivent cependant pas être assimilés à un audit complet de cybersécurité ou à un test d'intrusion.

### 14.6 Tests et qualité

Les principales fonctionnalités ont été testées pendant le développement.

Les scénarios vérifiés comprennent notamment :

- l'inscription ;
- la connexion ;
- la réservation ;
- la consultation des rendez-vous ;
- l'annulation ;
- la libération du créneau ;
- la déconnexion ;
- le contrôle de l'accès administrateur ;
- les statistiques ;
- le fonctionnement de MongoDB ;
- la navigation au clavier ;
- le déploiement ;
- une réservation depuis l'environnement en ligne.

Certaines mesures de sécurité ont également été vérifiées directement dans le code.

La séparation entre tests fonctionnels et vérifications du code permet de décrire précisément ce qui a réellement été contrôlé.

### 14.7 Accessibilité

Plusieurs bonnes pratiques d'accessibilité ont été prises en compte, notamment :

- la langue du document ;
- la structuration des titres ;
- les libellés de formulaires ;
- les textes alternatifs ;
- la navigation au clavier ;
- la visibilité du focus ;
- l'adaptation responsive.

Une amélioration spécifique du focus clavier a également été réalisée pendant le projet.

Cette démarche constitue une prise en compte ciblée de l'accessibilité.

Elle ne correspond pas à un audit complet et ne permet pas d'affirmer une conformité totale au RGAA.

### 14.8 Versionnement et traçabilité

Git et GitHub ont été utilisés pendant le développement afin de conserver un historique des modifications.

Le workflow principal est :

```text
feature
    ↓
develop
    ↓
main
```

Plusieurs fonctionnalités et éléments de documentation ont été intégrés avec des branches dédiées.

Cette organisation permet de distinguer le développement en cours, l'intégration et la version stable.

### 14.9 Documentation du projet

Plusieurs éléments ont été produits afin de documenter EasyRDV :

- `README.md` ;
- `database/schema.sql` ;
- `database/seed.sql` ;
- le modèle de données ;
- le diagramme de cas d'utilisation ;
- le diagramme de séquence ;
- le diagramme d'architecture technique ;
- la présente documentation technique.

D'autres livrables seront finalisés avant la remise du projet :

- les six maquettes Figma ;
- la charte graphique ;
- le manuel utilisateur ;
- la documentation de gestion de projet avec le tableau Kanban.

### 14.10 Déploiement

Une version PHP/MySQL d'EasyRDV a été déployée sur InfinityFree.

Ce déploiement a permis de vérifier que l'application pouvait fonctionner dans un environnement distant et communiquer avec une base MySQL différente de celle utilisée localement.

Une réservation a notamment été enregistrée depuis la version en ligne.

La partie MongoDB reste actuellement utilisée et testée dans l'environnement local.

### 14.11 Limites actuelles

Même si les principales fonctionnalités du projet sont opérationnelles, plusieurs limites doivent être identifiées.

#### MongoDB uniquement en local

Le serveur MongoDB utilisé pour les statistiques fonctionne actuellement dans l'environnement local.

La fonctionnalité NoSQL n'est donc pas disponible dans le déploiement InfinityFree actuel.

#### Accessibilité

Plusieurs éléments d'accessibilité ont été vérifiés et améliorés, mais aucun audit RGAA complet n'a été réalisé.

Il n'est donc pas possible d'affirmer une conformité complète au référentiel.

#### Tests

Les principaux parcours fonctionnels ont été testés manuellement.

Le projet ne dispose pas actuellement d'une suite complète de tests automatisés.

#### Environnement de production

Le déploiement actuel est adapté à la démonstration du projet.

Une application destinée à une exploitation réelle nécessiterait une configuration de production plus complète, notamment concernant la gestion des secrets, les journaux, les sauvegardes, la supervision et le renforcement de la configuration serveur.

#### Livrables graphiques

Les maquettes Figma et la charte graphique restent à finaliser avant la remise définitive.

Cette limite concerne la documentation et les livrables de conception, et non le fonctionnement actuel des interfaces déjà développées.

### 14.12 Évolutions possibles

Plusieurs évolutions pourraient être apportées à EasyRDV.

#### Héberger MongoDB à distance

L'utilisation d'un service MongoDB distant permettrait de rendre les statistiques NoSQL disponibles depuis la version publique.

#### Ajouter des tests automatisés

Des tests automatisés pourraient compléter les tests manuels afin de détecter plus rapidement les régressions.

Ils pourraient notamment concerner :

- l'authentification ;
- la réservation ;
- l'annulation ;
- les droits d'accès ;
- les traitements métier.

#### Approfondir l'accessibilité

Un audit plus complet pourrait être réalisé afin d'évaluer systématiquement les critères applicables du RGAA.

#### Améliorer la journalisation

Une gestion plus complète des journaux applicatifs pourrait faciliter le diagnostic des erreurs en environnement de production.

#### Renforcer la gestion de la configuration

Une évolution pourrait permettre de séparer davantage les configurations locales et de production, par exemple avec des variables d'environnement ou un mécanisme dédié de gestion des secrets.

#### Enrichir les statistiques

La partie administration pourrait proposer de nouveaux indicateurs, par exemple :

- l'évolution du nombre de rendez-vous dans le temps ;
- la répartition des rendez-vous par prestation ;
- le nombre d'annulations ;
- les périodes les plus demandées.

#### Poursuivre les améliorations responsive

Des tests supplémentaires sur différents appareils et différentes tailles d'écran permettraient d'affiner encore l'affichage mobile.

### 14.13 Livrables restant à finaliser

Avant de considérer le projet comme totalement prêt pour le dossier et la soutenance, les éléments suivants doivent encore être terminés :

```text
Documentation technique
        ↓
Finalisée après validation et versionnement Git

Maquettage
        ↓
3 maquettes desktop + 3 maquettes mobile

Charte graphique
        ↓
PDF avec couleurs, typographies et composants

Manuel utilisateur
        ↓
PDF de prise en main d'EasyRDV

Gestion de projet
        ↓
Tableau Kanban / Trello

Vérification finale
        ↓
Audit des compétences et des livrables

Dossier projet
        ↓
Rédaction finale à partir des réalisations réelles

Soutenance
        ↓
Présentation + démonstration + préparation aux questions
```

Cette liste permet de distinguer les fonctionnalités techniques déjà réalisées des livrables qui doivent encore être finalisés.

### 14.14 Conclusion technique

EasyRDV permet de mettre en pratique les différentes couches nécessaires à la réalisation d'une application web dynamique.

Le projet associe :

```text
Conception des interfaces
        +
Développement front-end
        +
Développement back-end
        +
Base de données relationnelle
        +
Base de données NoSQL
        +
Sécurité
        +
Tests
        +
Accessibilité
        +
Git / GitHub
        +
Documentation
        +
Déploiement
        ↓
Application web EasyRDV
```

La réalisation du projet a permis de travailler sur le cycle complet d'une application web : de la conception des interfaces et des données jusqu'au développement, aux tests, au versionnement et au déploiement.

Les limites identifiées sont documentées afin de présenter le projet de manière transparente.

Les derniers livrables de conception et de documentation seront finalisés avant la rédaction définitive du dossier projet et la préparation de la soutenance.

