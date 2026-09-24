```php
<?php

session_start();

/*
    Création du jeton CSRF.
*/
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../config/database.php';


/*
    Vérification de la connexion.
*/
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}


/*
    Vérification du rôle administrateur.
*/
if (
    !isset($_SESSION['role'])
    || $_SESSION['role'] !== 'admin'
) {
    header('Location: espace-client.php');
    exit;
}


$message = '';
$erreur = '';
$prestationAModifier = null;


/*
    Vérification CSRF pour toutes les actions POST.
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token'])
        || !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {
        die('Requête non autorisée.');
    }
}


/* =========================================================
   ANNULATION D'UN RENDEZ-VOUS
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['annuler_rdv'])
) {

    $idRendezVous = (int) $_POST['annuler_rdv'];

    try {

        $pdo->beginTransaction();

        $requeteRdv = $pdo->prepare(
            "SELECT
                id,
                date_rdv,
                heure_rdv,
                statut
             FROM rendez_vous
             WHERE id = ?"
        );

        $requeteRdv->execute([
            $idRendezVous
        ]);

        $rdvAAnnuler = $requeteRdv->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$rdvAAnnuler) {
            throw new Exception(
                "Le rendez-vous n'existe pas."
            );
        }

        if ($rdvAAnnuler['statut'] !== 'confirme') {
            throw new Exception(
                "Ce rendez-vous est déjà annulé."
            );
        }

        $requeteAnnulation = $pdo->prepare(
            "UPDATE rendez_vous
             SET statut = 'annule'
             WHERE id = ?"
        );

        $requeteAnnulation->execute([
            $idRendezVous
        ]);

        /*
            Le créneau redevient disponible.
        */
        $requeteDisponibilite = $pdo->prepare(
            "UPDATE disponibilite
             SET disponible = 1
             WHERE date_disponibilite = ?
             AND heure_debut = ?"
        );

        $requeteDisponibilite->execute([
            $rdvAAnnuler['date_rdv'],
            $rdvAAnnuler['heure_rdv']
        ]);

        $pdo->commit();

        $message =
            "Le rendez-vous a bien été annulé.";

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $erreur = $e->getMessage();
    }
}


/* =========================================================
   AJOUT D'UNE PRESTATION
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['ajouter_prestation'])
) {

    $nomPrestation = trim(
        $_POST['nom'] ?? ''
    );

    $descriptionPrestation = trim(
        $_POST['description'] ?? ''
    );

    $dureePrestation = (int) (
        $_POST['duree_minutes'] ?? 0
    );

    $prixPrestation = (float) (
        $_POST['prix'] ?? 0
    );

    if (
        $nomPrestation === ''
        || $descriptionPrestation === ''
        || $dureePrestation <= 0
        || $prixPrestation <= 0
    ) {

        $erreur =
            "Veuillez remplir correctement tous les champs.";

    } else {

        try {

            $requeteAjoutPrestation = $pdo->prepare(
                "INSERT INTO prestation
                (
                    nom,
                    description,
                    duree_minutes,
                    prix,
                    actif
                )
                VALUES (?, ?, ?, ?, 1)"
            );

            $requeteAjoutPrestation->execute([
                $nomPrestation,
                $descriptionPrestation,
                $dureePrestation,
                $prixPrestation
            ]);

            $message =
                "La prestation a bien été ajoutée.";

        } catch (PDOException $e) {

            $erreur =
                "Une erreur est survenue pendant l'ajout de la prestation.";
        }
    }
}


/* =========================================================
   MODIFICATION D'UNE PRESTATION
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['modifier_prestation'])
) {

    $idPrestation = (int) (
        $_POST['id_prestation'] ?? 0
    );

    $nomModifie = trim(
        $_POST['modifier_nom'] ?? ''
    );

    $descriptionModifiee = trim(
        $_POST['modifier_description'] ?? ''
    );

    $dureeModifiee = (int) (
        $_POST['modifier_duree'] ?? 0
    );

    $prixModifie = (float) (
        $_POST['modifier_prix'] ?? 0
    );

    if (
        $idPrestation <= 0
        || $nomModifie === ''
        || $descriptionModifiee === ''
        || $dureeModifiee <= 0
        || $prixModifie <= 0
    ) {

        $erreur =
            "Veuillez remplir correctement tous les champs.";

    } else {

        try {

            $requeteVerification = $pdo->prepare(
                "SELECT id
                 FROM prestation
                 WHERE id = ?"
            );

            $requeteVerification->execute([
                $idPrestation
            ]);

            $prestationExiste =
                $requeteVerification->fetch(
                    PDO::FETCH_ASSOC
                );

            if (!$prestationExiste) {
                throw new Exception(
                    "La prestation n'existe pas."
                );
            }

            $requeteModification = $pdo->prepare(
                "UPDATE prestation
                 SET
                    nom = ?,
                    description = ?,
                    duree_minutes = ?,
                    prix = ?
                 WHERE id = ?"
            );

            $requeteModification->execute([
                $nomModifie,
                $descriptionModifiee,
                $dureeModifiee,
                $prixModifie,
                $idPrestation
            ]);

            $message =
                "La prestation a bien été modifiée.";

            $prestationAModifier = null;

        } catch (Exception $e) {

            $erreur = $e->getMessage();
        }
    }
}


/* =========================================================
   ACTIVATION / DÉSACTIVATION D'UNE PRESTATION
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['changer_etat_prestation'])
) {

    $idPrestationEtat = (int) (
        $_POST['changer_etat_prestation'] ?? 0
    );

    try {

        $requeteEtat = $pdo->prepare(
            "SELECT
                id,
                actif
             FROM prestation
             WHERE id = ?"
        );

        $requeteEtat->execute([
            $idPrestationEtat
        ]);

        $prestationEtat = $requeteEtat->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$prestationEtat) {
            throw new Exception(
                "La prestation n'existe pas."
            );
        }

        $nouvelEtat =
            (int) $prestationEtat['actif'] === 1
                ? 0
                : 1;

        $requeteChangementEtat = $pdo->prepare(
            "UPDATE prestation
             SET actif = ?
             WHERE id = ?"
        );

        $requeteChangementEtat->execute([
            $nouvelEtat,
            $idPrestationEtat
        ]);

        if ($nouvelEtat === 1) {
            $message =
                "La prestation a bien été activée.";
        } else {
            $message =
                "La prestation a bien été désactivée.";
        }

    } catch (Exception $e) {

        $erreur = $e->getMessage();
    }
}


/* =========================================================
   SÉLECTION D'UNE PRESTATION À MODIFIER
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['choisir_prestation'])
) {

    $idPrestation =
        (int) $_POST['choisir_prestation'];

    $requetePrestationAModifier = $pdo->prepare(
        "SELECT
            id,
            nom,
            description,
            duree_minutes,
            prix,
            actif
         FROM prestation
         WHERE id = ?"
    );

    $requetePrestationAModifier->execute([
        $idPrestation
    ]);

    $prestationAModifier =
        $requetePrestationAModifier->fetch(
            PDO::FETCH_ASSOC
        );

    if (!$prestationAModifier) {
        $erreur =
            "La prestation demandée n'existe pas.";
    }
}


/* =========================================================
   AJOUT D'UNE DISPONIBILITÉ
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['ajouter_disponibilite'])
) {

    $dateDisponibilite =
        $_POST['date_disponibilite'] ?? '';

    $heureDebut =
        $_POST['heure_debut'] ?? '';

    $heureFin =
        $_POST['heure_fin'] ?? '';

    if (
        $dateDisponibilite === ''
        || $heureDebut === ''
        || $heureFin === ''
    ) {

        $erreur =
            "Veuillez remplir tous les champs de la disponibilité.";

    } elseif (
        !preg_match(
            '/^\d{4}-\d{2}-\d{2}$/',
            $dateDisponibilite
        )
        || !checkdate(
            (int) substr($dateDisponibilite, 5, 2),
            (int) substr($dateDisponibilite, 8, 2),
            (int) substr($dateDisponibilite, 0, 4)
        )
    ) {

        $erreur =
            "La date de disponibilité n'est pas valide.";

    } elseif ($dateDisponibilite < date('Y-m-d')) {

        $erreur =
            "Impossible d'ajouter une disponibilité dans le passé.";

    } elseif (
        !preg_match(
            '/^([01]\d|2[0-3]):[0-5]\d$/',
            $heureDebut
        )
        || !preg_match(
            '/^([01]\d|2[0-3]):[0-5]\d$/',
            $heureFin
        )
    ) {

        $erreur =
            "Les heures renseignées ne sont pas valides.";

    } elseif ($heureFin <= $heureDebut) {

        $erreur =
            "L'heure de fin doit être supérieure à l'heure de début.";

    } else {

        try {

            /*
                Vérification des chevauchements.
            */
            $requeteChevauchement = $pdo->prepare(
                "SELECT id
                 FROM disponibilite
                 WHERE date_disponibilite = ?
                 AND heure_debut < ?
                 AND heure_fin > ?"
            );

            $requeteChevauchement->execute([
                $dateDisponibilite,
                $heureFin,
                $heureDebut
            ]);

            $creneauExistant =
                $requeteChevauchement->fetch(
                    PDO::FETCH_ASSOC
                );

            if ($creneauExistant) {
                throw new Exception(
                    "Un créneau existe déjà sur cette plage horaire."
                );
            }

            $requeteAjoutDisponibilite = $pdo->prepare(
                "INSERT INTO disponibilite
                (
                    date_disponibilite,
                    heure_debut,
                    heure_fin,
                    disponible
                )
                VALUES (?, ?, ?, 1)"
            );

            $requeteAjoutDisponibilite->execute([
                $dateDisponibilite,
                $heureDebut,
                $heureFin
            ]);

            $message =
                "La disponibilité a bien été ajoutée.";

        } catch (Exception $e) {

            /*
                Correction :
                on capture aussi l'Exception générée
                en cas de chevauchement.
            */
            $erreur = $e->getMessage();
        }
    }
}


/* =========================================================
   SUPPRESSION D'UNE DISPONIBILITÉ
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['supprimer_disponibilite'])
) {

    $idDisponibilite = (int) (
        $_POST['supprimer_disponibilite'] ?? 0
    );

    try {

        $requeteDisponibilite = $pdo->prepare(
            "SELECT
                id,
                date_disponibilite,
                heure_debut
             FROM disponibilite
             WHERE id = ?"
        );

        $requeteDisponibilite->execute([
            $idDisponibilite
        ]);

        $disponibiliteASupprimer =
            $requeteDisponibilite->fetch(
                PDO::FETCH_ASSOC
            );

        if (!$disponibiliteASupprimer) {
            throw new Exception(
                "Cette disponibilité n'existe pas."
            );
        }

        /*
            Impossible de supprimer un créneau
            contenant un rendez-vous confirmé.
        */
        $requeteRendezVous = $pdo->prepare(
            "SELECT id
             FROM rendez_vous
             WHERE date_rdv = ?
             AND heure_rdv = ?
             AND statut = 'confirme'"
        );

        $requeteRendezVous->execute([
            $disponibiliteASupprimer[
                'date_disponibilite'
            ],
            $disponibiliteASupprimer[
                'heure_debut'
            ]
        ]);

        $rendezVousConfirme =
            $requeteRendezVous->fetch(
                PDO::FETCH_ASSOC
            );

        if ($rendezVousConfirme) {
            throw new Exception(
                "Impossible de supprimer un créneau avec un rendez-vous confirmé."
            );
        }

        $requeteSuppression = $pdo->prepare(
            "DELETE FROM disponibilite
             WHERE id = ?"
        );

        $requeteSuppression->execute([
            $idDisponibilite
        ]);

        $message =
            "La disponibilité a bien été supprimée.";

    } catch (Exception $e) {

        $erreur = $e->getMessage();
    }
}


/* =========================================================
   STATISTIQUES
========================================================= */

$requeteTotal = $pdo->query(
    "SELECT COUNT(*) FROM rendez_vous"
);

$nombreTotal =
    $requeteTotal->fetchColumn();


$requeteConfirmes = $pdo->query(
    "SELECT COUNT(*)
     FROM rendez_vous
     WHERE statut = 'confirme'"
);

$nombreConfirmes =
    $requeteConfirmes->fetchColumn();


$requeteAnnules = $pdo->query(
    "SELECT COUNT(*)
     FROM rendez_vous
     WHERE statut = 'annule'"
);

$nombreAnnules =
    $requeteAnnules->fetchColumn();


/* =========================================================
   LISTE DES RENDEZ-VOUS
========================================================= */

$requeteListe = $pdo->query(
    "SELECT
        rendez_vous.id,
        rendez_vous.date_rdv,
        rendez_vous.heure_rdv,
        rendez_vous.statut,

        utilisateur.prenom,
        utilisateur.nom,
        utilisateur.email,

        prestation.nom AS nom_prestation

     FROM rendez_vous

     INNER JOIN utilisateur
        ON rendez_vous.id_utilisateur = utilisateur.id

     INNER JOIN prestation
        ON rendez_vous.id_prestation = prestation.id

     ORDER BY
        rendez_vous.date_rdv DESC,
        rendez_vous.heure_rdv DESC"
);

$rendezVous = $requeteListe->fetchAll(
    PDO::FETCH_ASSOC
);


/* =========================================================
   LISTE DES PRESTATIONS
========================================================= */

$requetePrestations = $pdo->query(
    "SELECT
        id,
        nom,
        description,
        duree_minutes,
        prix,
        actif
     FROM prestation
     ORDER BY id ASC"
);

$prestations = $requetePrestations->fetchAll(
    PDO::FETCH_ASSOC
);


/* =========================================================
   LISTE DES DISPONIBILITÉS
========================================================= */

$requeteDisponibilites = $pdo->query(
    "SELECT
        id,
        date_disponibilite,
        heure_debut,
        heure_fin,
        disponible
     FROM disponibilite
     ORDER BY
        date_disponibilite ASC,
        heure_debut ASC"
);

$disponibilites = $requeteDisponibilites->fetchAll(
    PDO::FETCH_ASSOC
);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Administration EasyRDV"
    >

    <title>Administration | EasyRDV</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/style.css?v=8"
    >

</head>


<body>


<header class="site-header">

    <nav class="main-nav">

        <a href="../index.html" class="brand">

            <span class="brand-symbol">✦</span>

            <span class="brand-name">
                <span>Easy</span><strong>RDV</strong>
            </span>

            <span class="brand-dot"></span>

        </a>


        <div class="nav-links">

            <a href="admin.php" class="active">
                Administration
            </a>

            <a href="../index.html">
                Voir le site
            </a>

            <a
                href="deconnexion.php"
                class="nav-reservation"
            >
                Déconnexion
                <span>↗</span>
            </a>

        </div>

    </nav>

</header>



<main class="admin-page">

    <div class="admin-decoration admin-decoration-one"></div>
    <div class="admin-decoration admin-decoration-two"></div>


    <div class="admin-container">


        <!-- EN-TÊTE -->

        <section class="admin-welcome">

            <div>

                <span class="admin-label">
                    ✦ ADMINISTRATION
                </span>

                <h1>
                    Bonjour
                    <span>
                        <?php
                            echo htmlspecialchars(
                                $_SESSION['prenom']
                            );
                        ?> !
                    </span>
                </h1>

                <p>
                    Pilotez les rendez-vous, prestations
                    et disponibilités de votre salon.
                </p>

            </div>

            <div class="admin-profile">

                <div class="admin-avatar">
                    A
                </div>

                <div>
                    <span>Connecté en tant que</span>
                    <strong>Administrateur</strong>
                </div>

            </div>

        </section>



        <!-- MESSAGES -->

        <?php if ($message !== ''): ?>

            <div class="admin-alert admin-alert-success">

                <span>✓</span>

                <p>
                    <?php
                        echo htmlspecialchars($message);
                    ?>
                </p>

            </div>

        <?php endif; ?>


        <?php if ($erreur !== ''): ?>

            <div class="admin-alert admin-alert-error">

                <span>!</span>

                <p>
                    <?php
                        echo htmlspecialchars($erreur);
                    ?>
                </p>

            </div>

        <?php endif; ?>



        <!-- STATISTIQUES -->

        <section class="admin-stats">

            <article class="admin-stat purple">

                <div class="admin-stat-icon">
                    ✦
                </div>

                <div>
                    <span>Total rendez-vous</span>

                    <strong>
                        <?php echo (int) $nombreTotal; ?>
                    </strong>
                </div>

            </article>


            <article class="admin-stat mint">

                <div class="admin-stat-icon">
                    ✓
                </div>

                <div>
                    <span>Confirmés</span>

                    <strong>
                        <?php echo (int) $nombreConfirmes; ?>
                    </strong>
                </div>

            </article>


            <article class="admin-stat coral">

                <div class="admin-stat-icon">
                    ×
                </div>

                <div>
                    <span>Annulés</span>

                    <strong>
                        <?php echo (int) $nombreAnnules; ?>
                    </strong>
                </div>

            </article>

        </section>



        <!-- =====================================================
             RENDEZ-VOUS
        ====================================================== -->

        <section class="admin-section">

            <div class="admin-section-heading">

                <div class="admin-section-title">

                    <span class="admin-section-number purple">
                        01
                    </span>

                    <div>
                        <h2>Rendez-vous</h2>

                        <p>
                            Consultez et gérez les réservations clients.
                        </p>
                    </div>

                </div>

                <span class="admin-section-count">
                    <?php echo count($rendezVous); ?>
                    rendez-vous
                </span>

            </div>


            <?php if (empty($rendezVous)): ?>

                <div class="admin-empty">
                    Aucun rendez-vous enregistré.
                </div>

            <?php else: ?>

                <div class="admin-table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>
                                <th>Client</th>
                                <th>Prestation</th>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach ($rendezVous as $rdv): ?>

                            <tr>

                                <td>

                                    <div class="admin-client-cell">

                                        <span>
                                            <?php
                                                echo htmlspecialchars(
                                                    strtoupper(
                                                        substr(
                                                            $rdv['prenom'],
                                                            0,
                                                            1
                                                        )
                                                    )
                                                );
                                            ?>
                                        </span>

                                        <div>

                                            <strong>
                                                <?php
                                                    echo htmlspecialchars(
                                                        $rdv['prenom']
                                                        . ' '
                                                        . $rdv['nom']
                                                    );
                                                ?>
                                            </strong>

                                            <small>
                                                <?php
                                                    echo htmlspecialchars(
                                                        $rdv['email']
                                                    );
                                                ?>
                                            </small>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    <?php
                                        echo htmlspecialchars(
                                            $rdv['nom_prestation']
                                        );
                                    ?>
                                </td>


                                <td>
                                    <?php
                                        echo date(
                                            'd/m/Y',
                                            strtotime(
                                                $rdv['date_rdv']
                                            )
                                        );
                                    ?>
                                </td>


                                <td>
                                    <?php
                                        echo substr(
                                            $rdv['heure_rdv'],
                                            0,
                                            5
                                        );
                                    ?>
                                </td>


                                <td>

                                    <?php
                                        $rdvConfirme =
                                            $rdv['statut']
                                            === 'confirme';
                                    ?>

                                    <span
                                        class="admin-status
                                        <?php
                                            echo $rdvConfirme
                                                ? 'active'
                                                : 'cancelled';
                                        ?>"
                                    >
                                        <i></i>

                                        <?php
                                            echo $rdvConfirme
                                                ? 'Confirmé'
                                                : 'Annulé';
                                        ?>
                                    </span>

                                </td>


                                <td>

                                    <?php if ($rdvConfirme): ?>

                                        <form
                                            method="POST"
                                            action="admin.php"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $_SESSION[
                                                            'csrf_token'
                                                        ]
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="annuler_rdv"
                                                value="<?php
                                                    echo (int) $rdv['id'];
                                                ?>"
                                                class="admin-action danger"
                                            >
                                                Annuler
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="admin-no-action">
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>



        <!-- =====================================================
             PRESTATIONS
        ====================================================== -->

        <section class="admin-section">

            <div class="admin-section-heading">

                <div class="admin-section-title">

                    <span class="admin-section-number mint">
                        02
                    </span>

                    <div>
                        <h2>Prestations</h2>

                        <p>
                            Gérez les services proposés aux clients.
                        </p>
                    </div>

                </div>

                <span class="admin-section-count">
                    <?php echo count($prestations); ?>
                    prestations
                </span>

            </div>


            <?php if (!empty($prestations)): ?>

                <div class="admin-table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>
                                <th>Prestation</th>
                                <th>Durée</th>
                                <th>Prix</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach ($prestations as $prestation): ?>

                            <tr>

                                <td>

                                    <div class="admin-service-cell">

                                        <strong>
                                            <?php
                                                echo htmlspecialchars(
                                                    $prestation['nom']
                                                );
                                            ?>
                                        </strong>

                                        <small>
                                            <?php
                                                echo htmlspecialchars(
                                                    $prestation[
                                                        'description'
                                                    ]
                                                );
                                            ?>
                                        </small>

                                    </div>

                                </td>


                                <td>
                                    <?php
                                        echo (int)
                                            $prestation[
                                                'duree_minutes'
                                            ];
                                    ?>
                                    min
                                </td>


                                <td>

                                    <strong class="admin-price">
                                        <?php
                                            echo number_format(
                                                (float)
                                                $prestation['prix'],
                                                2,
                                                ',',
                                                ' '
                                            );
                                        ?>
                                        €
                                    </strong>

                                </td>


                                <td>

                                    <?php
                                        $prestationActive =
                                            (int)
                                            $prestation['actif']
                                            === 1;
                                    ?>

                                    <span
                                        class="admin-status
                                        <?php
                                            echo $prestationActive
                                                ? 'active'
                                                : 'inactive';
                                        ?>"
                                    >
                                        <i></i>

                                        <?php
                                            echo $prestationActive
                                                ? 'Actif'
                                                : 'Inactif';
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <div class="admin-actions">

                                        <form
                                            method="POST"
                                            action="admin.php"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $_SESSION[
                                                            'csrf_token'
                                                        ]
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="choisir_prestation"
                                                value="<?php
                                                    echo (int)
                                                        $prestation['id'];
                                                ?>"
                                                class="admin-action edit"
                                            >
                                                Modifier
                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="admin.php"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php
                                                    echo htmlspecialchars(
                                                        $_SESSION[
                                                            'csrf_token'
                                                        ]
                                                    );
                                                ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="changer_etat_prestation"
                                                value="<?php
                                                    echo (int)
                                                        $prestation['id'];
                                                ?>"
                                                class="admin-action
                                                <?php
                                                    echo $prestationActive
                                                        ? 'danger'
                                                        : 'success';
                                                ?>"
                                            >

                                                <?php
                                                    echo $prestationActive
                                                        ? 'Désactiver'
                                                        : 'Réactiver';
                                                ?>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>



            <!-- MODIFICATION PRESTATION -->

            <?php if ($prestationAModifier): ?>

                <div class="admin-form-panel edit-panel">

                    <div class="admin-form-heading">

                        <span>✎</span>

                        <div>
                            <h3>Modifier la prestation</h3>

                            <p>
                                Modifiez les informations puis enregistrez.
                            </p>
                        </div>

                    </div>


                    <form
                        method="POST"
                        action="admin.php"
                        class="admin-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php
                                echo htmlspecialchars(
                                    $_SESSION['csrf_token']
                                );
                            ?>"
                        >

                        <input
                            type="hidden"
                            name="id_prestation"
                            value="<?php
                                echo (int)
                                    $prestationAModifier['id'];
                            ?>"
                        >


                        <div class="admin-form-grid">

                            <div class="admin-field">

                                <label for="modifier_nom">
                                    Nom
                                </label>

                                <input
                                    type="text"
                                    id="modifier_nom"
                                    name="modifier_nom"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $prestationAModifier[
                                                'nom'
                                            ]
                                        );
                                    ?>"
                                    required
                                >

                            </div>


                            <div class="admin-field">

                                <label for="modifier_duree">
                                    Durée
                                </label>

                                <input
                                    type="number"
                                    id="modifier_duree"
                                    name="modifier_duree"
                                    min="1"
                                    value="<?php
                                        echo (int)
                                            $prestationAModifier[
                                                'duree_minutes'
                                            ];
                                    ?>"
                                    required
                                >

                            </div>


                            <div class="admin-field">

                                <label for="modifier_prix">
                                    Prix (€)
                                </label>

                                <input
                                    type="number"
                                    id="modifier_prix"
                                    name="modifier_prix"
                                    min="0.01"
                                    step="0.01"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $prestationAModifier[
                                                'prix'
                                            ]
                                        );
                                    ?>"
                                    required
                                >

                            </div>


                            <div class="admin-field admin-field-full">

                                <label for="modifier_description">
                                    Description
                                </label>

                                <textarea
                                    id="modifier_description"
                                    name="modifier_description"
                                    required
                                ><?php
                                    echo htmlspecialchars(
                                        $prestationAModifier[
                                            'description'
                                        ]
                                    );
                                ?></textarea>

                            </div>

                        </div>


                        <button
                            type="submit"
                            name="modifier_prestation"
                            class="admin-primary-button"
                        >
                            Enregistrer les modifications
                            <span>→</span>
                        </button>

                    </form>

                </div>

            <?php endif; ?>



            <!-- AJOUT PRESTATION -->

            <div class="admin-form-panel">

                <div class="admin-form-heading">

                    <span>+</span>

                    <div>
                        <h3>Ajouter une prestation</h3>

                        <p>
                            Créez un nouveau service pour vos clients.
                        </p>
                    </div>

                </div>


                <form
                    method="POST"
                    action="admin.php"
                    class="admin-form"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                            echo htmlspecialchars(
                                $_SESSION['csrf_token']
                            );
                        ?>"
                    >


                    <div class="admin-form-grid">

                        <div class="admin-field">

                            <label for="nom">
                                Nom
                            </label>

                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                placeholder="Ex. Coupe enfant"
                                required
                            >

                        </div>


                        <div class="admin-field">

                            <label for="duree_minutes">
                                Durée
                            </label>

                            <input
                                type="number"
                                id="duree_minutes"
                                name="duree_minutes"
                                min="1"
                                placeholder="30"
                                required
                            >

                        </div>


                        <div class="admin-field">

                            <label for="prix">
                                Prix (€)
                            </label>

                            <input
                                type="number"
                                id="prix"
                                name="prix"
                                min="0.01"
                                step="0.01"
                                placeholder="20.00"
                                required
                            >

                        </div>


                        <div class="admin-field admin-field-full">

                            <label for="description">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                placeholder="Description de la prestation..."
                                required
                            ></textarea>

                        </div>

                    </div>


                    <button
                        type="submit"
                        name="ajouter_prestation"
                        class="admin-primary-button"
                    >
                        Ajouter la prestation
                        <span>+</span>
                    </button>

                </form>

            </div>

        </section>



        <!-- =====================================================
             DISPONIBILITÉS
        ====================================================== -->

        <section class="admin-section">

            <div class="admin-section-heading">

                <div class="admin-section-title">

                    <span class="admin-section-number coral">
                        03
                    </span>

                    <div>
                        <h2>Disponibilités</h2>

                        <p>
                            Organisez les créneaux proposés aux clients.
                        </p>
                    </div>

                </div>

                <span class="admin-section-count">
                    <?php echo count($disponibilites); ?>
                    créneaux
                </span>

            </div>


            <?php if (empty($disponibilites)): ?>

                <div class="admin-empty">
                    Aucune disponibilité enregistrée.
                </div>

            <?php else: ?>

                <div class="admin-table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>
                                <th>Date</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>État</th>
                                <th>Action</th>
                            </tr>

                        </thead>


                        <tbody>

                        <?php
                        foreach (
                            $disponibilites
                            as $disponibilite
                        ):
                        ?>

                            <tr>

                                <td>

                                    <strong>
                                        <?php
                                            echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $disponibilite[
                                                        'date_disponibilite'
                                                    ]
                                                )
                                            );
                                        ?>
                                    </strong>

                                </td>


                                <td>
                                    <?php
                                        echo substr(
                                            $disponibilite[
                                                'heure_debut'
                                            ],
                                            0,
                                            5
                                        );
                                    ?>
                                </td>


                                <td>
                                    <?php
                                        echo substr(
                                            $disponibilite[
                                                'heure_fin'
                                            ],
                                            0,
                                            5
                                        );
                                    ?>
                                </td>


                                <td>

                                    <?php
                                        $estDisponible =
                                            (int)
                                            $disponibilite[
                                                'disponible'
                                            ] === 1;
                                    ?>

                                    <span
                                        class="admin-status
                                        <?php
                                            echo $estDisponible
                                                ? 'active'
                                                : 'inactive';
                                        ?>"
                                    >
                                        <i></i>

                                        <?php
                                            echo $estDisponible
                                                ? 'Disponible'
                                                : 'Réservé';
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <form
                                        method="POST"
                                        action="admin.php"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?php
                                                echo htmlspecialchars(
                                                    $_SESSION[
                                                        'csrf_token'
                                                    ]
                                                );
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="supprimer_disponibilite"
                                            value="<?php
                                                echo (int)
                                                    $disponibilite['id'];
                                            ?>"
                                            class="admin-action danger"
                                        >
                                            Supprimer
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>



            <!-- AJOUT DISPONIBILITÉ -->

            <div class="admin-form-panel availability-panel">

                <div class="admin-form-heading">

                    <span>◷</span>

                    <div>
                        <h3>Ajouter une disponibilité</h3>

                        <p>
                            Ajoutez un nouveau créneau réservable.
                        </p>
                    </div>

                </div>


                <form
                    method="POST"
                    action="admin.php"
                    class="admin-form"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                            echo htmlspecialchars(
                                $_SESSION['csrf_token']
                            );
                        ?>"
                    >


                    <div class="admin-availability-grid">

                        <div class="admin-field">

                            <label for="date_disponibilite">
                                Date
                            </label>

                            <input
                                type="date"
                                id="date_disponibilite"
                                name="date_disponibilite"
                                min="<?php
                                    echo date('Y-m-d');
                                ?>"
                                required
                            >

                        </div>


                        <div class="admin-field">

                            <label for="heure_debut">
                                Heure de début
                            </label>

                            <input
                                type="time"
                                id="heure_debut"
                                name="heure_debut"
                                required
                            >

                        </div>


                        <div class="admin-field">

                            <label for="heure_fin">
                                Heure de fin
                            </label>

                            <input
                                type="time"
                                id="heure_fin"
                                name="heure_fin"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            name="ajouter_disponibilite"
                            class="admin-primary-button availability-button"
                        >
                            Ajouter
                            <span>+</span>
                        </button>

                    </div>

                </form>

            </div>

        </section>


    </div>

</main>



<footer class="footer">

    <div class="footer-content">

        <div class="footer-brand">

            <a href="../index.html" class="brand footer-logo">

                <span class="brand-symbol">✦</span>

                <span class="brand-name">
                    <span>Easy</span><strong>RDV</strong>
                </span>

            </a>

            <p>
                Administration EasyRDV.
            </p>

        </div>


        <div class="footer-column">

            <strong>Navigation</strong>

            <a href="admin.php">
                Administration
            </a>

            <a href="../index.html">
                Voir le site
            </a>

        </div>


        <div class="footer-column">

            <strong>Gestion</strong>

            <span>Rendez-vous</span>
            <span>Prestations</span>
            <span>Disponibilités</span>

        </div>


        <div class="footer-column">

            <strong>Session</strong>

            <a href="deconnexion.php">
                Déconnexion
            </a>

        </div>

    </div>


    <div class="footer-bottom">

        <span>
            © 2026 EasyRDV
        </span>

        <div class="footer-colors">
            <i></i>
            <i></i>
            <i></i>
            <i></i>
        </div>

    </div>

</footer>


</body>
</html>
```
