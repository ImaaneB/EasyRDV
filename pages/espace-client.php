<?php

session_start();

/*
    Création d'un jeton CSRF
    pour sécuriser les formulaires.
*/
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );
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
    Message affiché après une action.
*/
$message = null;


/*
    ANNULATION D'UN RENDEZ-VOUS
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['annuler_rdv'])
) {

    /*
        Vérification du jeton CSRF.
    */
    if (
        !isset($_POST['csrf_token'])
        || !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {
        die('Requête non autorisée.');
    }


    $idRendezVous = (int) $_POST['annuler_rdv'];


    /*
        On vérifie que le rendez-vous :
        - existe,
        - appartient à l'utilisateur connecté,
        - est encore confirmé.
    */
    $requeteVerification = $pdo->prepare(
        "SELECT
            id,
            date_rdv,
            heure_rdv,
            statut
         FROM rendez_vous
         WHERE id = ?
         AND id_utilisateur = ?
         AND statut = 'confirme'"
    );

    $requeteVerification->execute([
        $idRendezVous,
        $_SESSION['utilisateur_id']
    ]);

    $rendezVousAAnnuler =
        $requeteVerification->fetch(PDO::FETCH_ASSOC);


    if ($rendezVousAAnnuler) {

        try {

            /*
                Début de la transaction.
            */
            $pdo->beginTransaction();


            /*
                1. Le rendez-vous passe au statut "annule".
            */
            $requeteAnnulation = $pdo->prepare(
                "UPDATE rendez_vous
                 SET statut = 'annule'
                 WHERE id = ?
                 AND id_utilisateur = ?"
            );

            $requeteAnnulation->execute([
                $idRendezVous,
                $_SESSION['utilisateur_id']
            ]);


            /*
                2. Le créneau redevient disponible.
            */
            $requeteDisponibilite = $pdo->prepare(
                "UPDATE disponibilite
                 SET disponible = 1
                 WHERE date_disponibilite = ?
                 AND heure_debut = ?"
            );

            $requeteDisponibilite->execute([
                $rendezVousAAnnuler['date_rdv'],
                $rendezVousAAnnuler['heure_rdv']
            ]);


            /*
                Validation des modifications.
            */
            $pdo->commit();

            $message =
                "Votre rendez-vous a bien été annulé.";

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message =
                "Une erreur est survenue pendant l'annulation.";
        }

    } else {

        $message =
            "Impossible d'annuler ce rendez-vous.";
    }
}


/*
    RÉCUPÉRATION DES RENDEZ-VOUS À VENIR

    On récupère uniquement :
    - les rendez-vous confirmés,
    - dont la date est aujourd'hui ou dans le futur.
*/
$requeteAVenir = $pdo->prepare(
    "SELECT
        rendez_vous.id,
        rendez_vous.date_rdv,
        rendez_vous.heure_rdv,
        rendez_vous.statut,
        prestation.nom AS nom_prestation
     FROM rendez_vous
     INNER JOIN prestation
        ON rendez_vous.id_prestation = prestation.id
     WHERE rendez_vous.id_utilisateur = ?
     AND rendez_vous.statut = 'confirme'
     AND rendez_vous.date_rdv >= CURDATE()
     ORDER BY rendez_vous.date_rdv ASC,
              rendez_vous.heure_rdv ASC"
);

$requeteAVenir->execute([
    $_SESSION['utilisateur_id']
]);

$rendezVousAVenir =
    $requeteAVenir->fetchAll(PDO::FETCH_ASSOC);


/*
    RÉCUPÉRATION DE L'HISTORIQUE

    L'historique contient :
    - les rendez-vous annulés,
    - les rendez-vous dont la date est passée.
*/
$requeteHistorique = $pdo->prepare(
    "SELECT
        rendez_vous.id,
        rendez_vous.date_rdv,
        rendez_vous.heure_rdv,
        rendez_vous.statut,
        prestation.nom AS nom_prestation
     FROM rendez_vous
     INNER JOIN prestation
        ON rendez_vous.id_prestation = prestation.id
     WHERE rendez_vous.id_utilisateur = ?
     AND (
        rendez_vous.statut = 'annule'
        OR rendez_vous.date_rdv < CURDATE()
     )
     ORDER BY rendez_vous.date_rdv DESC,
              rendez_vous.heure_rdv DESC"
);

$requeteHistorique->execute([
    $_SESSION['utilisateur_id']
]);

$historiqueRendezVous =
    $requeteHistorique->fetchAll(PDO::FETCH_ASSOC);

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
        content="Gérez vos rendez-vous depuis votre espace client EasyRDV."
    >

    <title>Mon espace | EasyRDV</title>

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
        href="../assets/css/style.css?v=6"
    >

</head>


<body>


    <!-- HEADER -->

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

                <a href="../index.html">
                    Accueil
                </a>

                <a href="../index.html#prestations">
                    Prestations
                </a>

                <a
                    href="espace-client.php"
                    class="active"
                >
                    Mon espace
                </a>

                <a href="reservation.php">
                    Réserver
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



    <!-- ESPACE CLIENT -->

    <main class="client-page">

        <div class="client-decoration client-decoration-one"></div>
        <div class="client-decoration client-decoration-two"></div>


        <div class="client-container">


            <!-- EN-TÊTE DU TABLEAU DE BORD -->

            <section class="client-welcome">

                <div>

                    <span class="client-label">
                        ✦ MON ESPACE
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
                        Retrouvez et gérez vos rendez-vous
                        EasyRDV depuis votre espace personnel.
                    </p>

                </div>


                <a
                    href="reservation.php"
                    class="client-new-rdv"
                >
                    <span>+</span>

                    Prendre rendez-vous
                </a>

            </section>



            <!-- INFORMATIONS CLIENT -->

            <section class="client-profile-strip">

                <div class="client-avatar">

                    <?php
                        echo htmlspecialchars(
                            strtoupper(
                                substr($_SESSION['prenom'], 0, 1)
                            )
                        );
                    ?>

                </div>


                <div class="client-profile-name">

                    <span>
                        Connecté en tant que
                    </span>

                    <strong>
                        <?php
                            echo htmlspecialchars(
                                $_SESSION['prenom']
                                . ' '
                                . $_SESSION['nom']
                            );
                        ?>
                    </strong>

                </div>


                <div class="client-profile-status">
                    <i></i>
                    Compte actif
                </div>

            </section>



            <!-- MESSAGE APRÈS ANNULATION -->

            <?php if ($message !== null): ?>

                <div class="client-message">

                    <span>✓</span>

                    <p>
                        <?php
                            echo htmlspecialchars($message);
                        ?>
                    </p>

                </div>

            <?php endif; ?>



            <!-- =============================================
                 RENDEZ-VOUS À VENIR
            ============================================== -->

            <section class="client-section">


                <div class="client-section-heading">

                    <div>

                        <span class="client-section-number purple">
                            01
                        </span>

                        <div>

                            <h2>
                                Mes rendez-vous à venir
                            </h2>

                            <p>
                                Vos prochaines réservations confirmées.
                            </p>

                        </div>

                    </div>


                    <span class="client-count">

                        <?php
                            echo count($rendezVousAVenir);
                        ?>

                        rendez-vous

                    </span>

                </div>



                <?php if (count($rendezVousAVenir) > 0): ?>


                    <div class="client-upcoming-grid">


                        <?php foreach ($rendezVousAVenir as $rdv): ?>


                            <article class="client-rdv-card">


                                <div class="client-rdv-top">

                                    <span class="client-rdv-icon">
                                        ✂
                                    </span>


                                    <span class="client-status confirmed">
                                        <i></i>
                                        Confirmé
                                    </span>

                                </div>


                                <h3>
                                    <?php
                                        echo htmlspecialchars(
                                            $rdv['nom_prestation']
                                        );
                                    ?>
                                </h3>


                                <div class="client-rdv-details">


                                    <div>

                                        <span class="client-detail-icon">
                                            ◫
                                        </span>

                                        <div>

                                            <small>
                                                Date
                                            </small>

                                            <strong>
                                                <?php
                                                    echo date(
                                                        'd/m/Y',
                                                        strtotime(
                                                            $rdv['date_rdv']
                                                        )
                                                    );
                                                ?>
                                            </strong>

                                        </div>

                                    </div>



                                    <div>

                                        <span class="client-detail-icon mint">
                                            ◷
                                        </span>

                                        <div>

                                            <small>
                                                Heure
                                            </small>

                                            <strong>
                                                <?php
                                                    echo substr(
                                                        $rdv['heure_rdv'],
                                                        0,
                                                        5
                                                    );
                                                ?>
                                            </strong>

                                        </div>

                                    </div>


                                </div>



                                <!-- ANNULATION -->

                                <form
                                    method="POST"
                                    class="client-cancel-form"
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


                                    <button
                                        type="submit"
                                        name="annuler_rdv"
                                        value="<?php
                                            echo (int) $rdv['id'];
                                        ?>"
                                        class="client-cancel-button"
                                    >
                                        Annuler le rendez-vous
                                    </button>

                                </form>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <div class="client-empty">

                        <div class="client-empty-icon">
                            ◷
                        </div>

                        <div>

                            <strong>
                                Aucun rendez-vous à venir
                            </strong>

                            <p>
                                Vous n'avez aucune réservation prévue
                                pour le moment.
                            </p>

                        </div>


                        <a href="reservation.php">
                            Réserver
                            <span>→</span>
                        </a>

                    </div>


                <?php endif; ?>


            </section>



            <!-- =============================================
                 HISTORIQUE
            ============================================== -->

            <section class="client-section client-history-section">


                <div class="client-section-heading">

                    <div>

                        <span class="client-section-number coral">
                            02
                        </span>

                        <div>

                            <h2>
                                Historique
                            </h2>

                            <p>
                                Retrouvez vos anciens rendez-vous.
                            </p>

                        </div>

                    </div>


                    <span class="client-count">

                        <?php
                            echo count($historiqueRendezVous);
                        ?>

                        rendez-vous

                    </span>

                </div>



                <?php if (count($historiqueRendezVous) > 0): ?>


                    <div class="client-history-list">


                        <?php foreach ($historiqueRendezVous as $rdv): ?>


                            <article class="client-history-item">


                                <div class="client-history-service">

                                    <span>
                                        ✂
                                    </span>

                                    <strong>
                                        <?php
                                            echo htmlspecialchars(
                                                $rdv['nom_prestation']
                                            );
                                        ?>
                                    </strong>

                                </div>



                                <div class="client-history-info">

                                    <small>
                                        Date
                                    </small>

                                    <strong>
                                        <?php
                                            echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $rdv['date_rdv']
                                                )
                                            );
                                        ?>
                                    </strong>

                                </div>



                                <div class="client-history-info">

                                    <small>
                                        Heure
                                    </small>

                                    <strong>
                                        <?php
                                            echo substr(
                                                $rdv['heure_rdv'],
                                                0,
                                                5
                                            );
                                        ?>
                                    </strong>

                                </div>



                                <?php
                                    $estAnnule =
                                        $rdv['statut'] === 'annule';
                                ?>


                                <span
                                    class="client-status
                                    <?php
                                        echo $estAnnule
                                            ? 'cancelled'
                                            : 'finished';
                                    ?>"
                                >

                                    <i></i>

                                    <?php
                                        echo $estAnnule
                                            ? 'Annulé'
                                            : 'Terminé';
                                    ?>

                                </span>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <div class="client-empty history-empty">

                        <div class="client-empty-icon coral">
                            ✦
                        </div>

                        <div>

                            <strong>
                                Votre historique est vide
                            </strong>

                            <p>
                                Vos anciens rendez-vous apparaîtront ici.
                            </p>

                        </div>

                    </div>


                <?php endif; ?>


            </section>


        </div>

    </main>



    <!-- FOOTER -->

    <footer class="footer">

        <div class="footer-content">


            <div class="footer-brand">

                <a
                    href="../index.html"
                    class="brand footer-logo"
                >

                    <span class="brand-symbol">✦</span>

                    <span class="brand-name">
                        <span>Easy</span><strong>RDV</strong>
                    </span>

                </a>

                <p>
                    Votre rendez-vous coiffure,
                    simplement.
                </p>

            </div>



            <div class="footer-column">

                <strong>
                    Navigation
                </strong>

                <a href="../index.html">
                    Accueil
                </a>

                <a href="../index.html#prestations">
                    Prestations
                </a>

                <a href="reservation.php">
                    Réserver
                </a>

            </div>



            <div class="footer-column">

                <strong>
                    Horaires
                </strong>

                <span>
                    Lun — Ven : 9h — 19h
                </span>

                <span>
                    Samedi : 9h — 18h
                </span>

                <span>
                    Dimanche : fermé
                </span>

            </div>



            <div class="footer-column">

                <strong>
                    Contact
                </strong>

                <span>
                    10 rue de la République
                </span>

                <span>
                    75000 Paris
                </span>

                <span>
                    01 00 00 00 00
                </span>

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