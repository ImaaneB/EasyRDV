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
    Variables utilisées dans la page.
*/
$message = null;
$erreur = null;

$idPrestationSelectionnee = null;
$dateSelectionnee = null;
$idDisponibiliteSelectionnee = null;

$creneauxDisponibles = [];
$creneauSelectionne = null;


/*
    Récupération des prestations actives.
*/
$requetePrestations = $pdo->prepare(
    "SELECT
        id,
        nom,
        description,
        duree_minutes,
        prix
     FROM prestation
     WHERE actif = 1
     ORDER BY nom ASC"
);

$requetePrestations->execute();

$prestations =
    $requetePrestations->fetchAll(PDO::FETCH_ASSOC);


/*
    TRAITEMENT DU FORMULAIRE
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    /*
        Récupération de la prestation.
    */
    if (
        isset($_POST['prestation'])
        && $_POST['prestation'] !== ''
    ) {
        $idPrestationSelectionnee =
            (int) $_POST['prestation'];
    }


    /*
        Récupération de la date.
    */
    if (
        isset($_POST['date_rdv'])
        && $_POST['date_rdv'] !== ''
    ) {
        $dateSelectionnee =
            $_POST['date_rdv'];
    }


    /*
        Récupération du créneau sélectionné.
    */
    if (
        isset($_POST['disponibilite'])
        && $_POST['disponibilite'] !== ''
    ) {
        $idDisponibiliteSelectionnee =
            (int) $_POST['disponibilite'];
    }


    /*
        Si un créneau a été sélectionné,
        on le récupère directement depuis la base.

        On vérifie également qu'il est toujours disponible.
    */
    if ($idDisponibiliteSelectionnee !== null) {

        $requeteCreneau = $pdo->prepare(
            "SELECT
                id,
                date_disponibilite,
                heure_debut,
                heure_fin
             FROM disponibilite
             WHERE id = ?
             AND disponible = 1"
        );

        $requeteCreneau->execute([
            $idDisponibiliteSelectionnee
        ]);

        $creneauSelectionne =
            $requeteCreneau->fetch(PDO::FETCH_ASSOC);
    }


    /*
        CONFIRMATION DÉFINITIVE DU RENDEZ-VOUS
    */
    if (isset($_POST['confirmer_rdv'])) {

        /*
            Vérification des informations nécessaires.
        */
        if (
            $idPrestationSelectionnee === null
            || $dateSelectionnee === null
            || $idDisponibiliteSelectionnee === null
            || !$creneauSelectionne
        ) {

            $erreur =
                "Impossible de confirmer ce rendez-vous.";

       } elseif (
    !preg_match(
        '/^\d{4}-\d{2}-\d{2}$/',
        $dateSelectionnee
    )
    || !checkdate(
        (int) substr($dateSelectionnee, 5, 2),
        (int) substr($dateSelectionnee, 8, 2),
        (int) substr($dateSelectionnee, 0, 4)
    )
) {

    /*
        Sécurité :
        la date doit être une vraie date
        au format AAAA-MM-JJ.
    */
    $erreur =
        "La date sélectionnée n'est pas valide.";

} elseif ($dateSelectionnee < date('Y-m-d')) {

    /*
        Sécurité :
        impossible de réserver une date passée.
    */
    $erreur =
        "Impossible de réserver un rendez-vous à une date passée.";

        } elseif (
            $creneauSelectionne['date_disponibilite']
            !== $dateSelectionnee
        ) {

            /*
                Sécurité :
                le créneau doit appartenir à la date choisie.
            */
            $erreur =
                "Le créneau sélectionné ne correspond pas à la date choisie.";

        } else {

            /*
                Sécurité :
                vérification de la prestation directement
                dans la base de données.

                Une prestation désactivée ne peut pas
                être réservée.
            */
            $requetePrestationActive = $pdo->prepare(
                "SELECT id
                 FROM prestation
                 WHERE id = ?
                 AND actif = 1"
            );

            $requetePrestationActive->execute([
                $idPrestationSelectionnee
            ]);

            $prestationActive =
                $requetePrestationActive->fetch(PDO::FETCH_ASSOC);


            if (!$prestationActive) {

                $erreur =
                    "Cette prestation n'est plus disponible.";

            } else {

                /*
                    Vérification anti-double réservation.

                    Un rendez-vous annulé ne doit plus
                    bloquer le créneau.
                */
                $verificationRdv = $pdo->prepare(
                    "SELECT id
                     FROM rendez_vous
                     WHERE date_rdv = ?
                     AND heure_rdv = ?
                     AND statut <> 'annule'"
                );

                $verificationRdv->execute([
                    $dateSelectionnee,
                    $creneauSelectionne['heure_debut']
                ]);

                $rendezVousExistant =
                    $verificationRdv->fetch(PDO::FETCH_ASSOC);


                if ($rendezVousExistant) {

                    $erreur =
                        "Ce créneau vient d'être réservé.";

                } else {

                    try {

                        /*
                            Début de la transaction.

                            La création du rendez-vous
                            et le blocage du créneau
                            doivent réussir ensemble.
                        */
                        $pdo->beginTransaction();


                        /*
                            Création du rendez-vous.
                        */
                        $requeteRendezVous = $pdo->prepare(
                            "INSERT INTO rendez_vous
                            (
                                id_utilisateur,
                                id_prestation,
                                date_rdv,
                                heure_rdv,
                                statut
                            )
                            VALUES (?, ?, ?, ?, ?)"
                        );

                        $requeteRendezVous->execute([
                            $_SESSION['utilisateur_id'],
                            $idPrestationSelectionnee,
                            $dateSelectionnee,
                            $creneauSelectionne['heure_debut'],
                            'confirme'
                        ]);


                        /*
                            Le créneau devient indisponible.

                            disponible = 1 est ajouté dans
                            la condition pour vérifier qu'il
                            n'a pas été pris entre-temps.
                        */
                        $requeteMiseAJour = $pdo->prepare(
                            "UPDATE disponibilite
                             SET disponible = 0
                             WHERE id = ?
                             AND disponible = 1"
                        );

                        $requeteMiseAJour->execute([
                            $idDisponibiliteSelectionnee
                        ]);


                        /*
                            Le créneau doit obligatoirement
                            avoir été modifié.
                        */
                        if (
                            $requeteMiseAJour->rowCount() !== 1
                        ) {

                            throw new Exception(
                                "Ce créneau n'est plus disponible."
                            );
                        }


                        /*
                            Les deux opérations ont réussi :
                            validation définitive.
                        */
                        $pdo->commit();

                        $message =
                            "Rendez-vous confirmé.";

                    } catch (Exception $e) {

                        /*
                            Si une opération échoue,
                            on annule la transaction.
                        */
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }

                        $erreur =
    "Une erreur est survenue lors de la réservation.";
                    }
                }
            }
        }
    }


    /*
        Si une prestation et une date sont sélectionnées,
        on récupère les créneaux disponibles.
    */
    if (
        $idPrestationSelectionnee !== null
        && $dateSelectionnee !== null
        && !isset($_POST['confirmer_rdv'])
    ) {

        $requeteDisponibilites = $pdo->prepare(
            "SELECT
                id,
                date_disponibilite,
                heure_debut,
                heure_fin
             FROM disponibilite
             WHERE date_disponibilite = ?
             AND disponible = 1
             ORDER BY heure_debut ASC"
        );

        $requeteDisponibilites->execute([
            $dateSelectionnee
        ]);

        $creneauxDisponibles =
            $requeteDisponibilites->fetchAll(PDO::FETCH_ASSOC);
    }
}

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
        content="Réservez votre rendez-vous coiffure avec EasyRDV."
    >

    <title>Réserver | EasyRDV</title>

    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS EasyRDV -->
    <link
        rel="stylesheet"
        href="../assets/css/style.css?v=8"
    >

</head>


<body>


    <!-- =====================================================
         HEADER
    ====================================================== -->

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

                <a href="espace-client.php">
                    Mon espace
                </a>

                <a
                    href="reservation.php"
                    class="active"
                >
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



    <!-- =====================================================
         PAGE RÉSERVATION
    ====================================================== -->

    <main class="booking-page booking-page-v2">

    <div class="booking-decoration booking-decoration-one"></div>
    <div class="booking-decoration booking-decoration-two"></div>

    <section class="booking-shell">

        <!-- EN-TÊTE DE LA CARTE -->
        <div class="booking-v2-heading">

            <span class="booking-label">
                ✦ RÉSERVATION EN LIGNE
            </span>

            <h1>
                Prenez votre
                <span>rendez-vous.</span>
            </h1>

            <p>
                Choisissez votre prestation, votre date
                et le créneau qui vous convient.
            </p>

        </div>


        <?php if ($message !== null): ?>

            <!-- CONFIRMATION FINALE -->
            <section class="booking-v2-success">

                <div class="booking-success-icon">
                    ✓
                </div>

                <span class="booking-success-label">
                    RENDEZ-VOUS CONFIRMÉ
                </span>

                <h2>
                    C'est réservé !
                </h2>

                <p>
                    Votre rendez-vous a bien été enregistré.
                    Nous vous attendons au salon.
                </p>

                <div class="booking-success-details">

                    <div>
                        <span>Date</span>

                        <strong>
                            <?php
                            echo date(
                                'd/m/Y',
                                strtotime($dateSelectionnee)
                            );
                            ?>
                        </strong>
                    </div>

                    <div>
                        <span>Heure</span>

                        <strong>
                            <?php
                            echo substr(
                                $creneauSelectionne['heure_debut'],
                                0,
                                5
                            );
                            ?>
                        </strong>
                    </div>

                </div>

                <a
                    href="espace-client.php"
                    class="booking-main-button booking-success-button"
                >
                    Voir mes rendez-vous
                    <span>→</span>
                </a>

            </section>


        <?php else: ?>

            <!-- PROGRESSION -->
            <div class="booking-v2-progress">

                <div class="booking-v2-progress-item active">
                    <span>1</span>
                    <strong>Prestation</strong>
                </div>

                <div class="booking-v2-progress-line"></div>

                <div
                    class="booking-v2-progress-item
                    <?php
                    if (
                        $idPrestationSelectionnee !== null
                        && $dateSelectionnee !== null
                    ) {
                        echo 'active';
                    }
                    ?>"
                >
                    <span>2</span>
                    <strong>Date</strong>
                </div>

                <div class="booking-v2-progress-line"></div>

                <div
                    class="booking-v2-progress-item
                    <?php
                    if (
                        $idPrestationSelectionnee !== null
                        && $dateSelectionnee !== null
                    ) {
                        echo 'active';
                    }
                    ?>"
                >
                    <span>3</span>
                    <strong>Créneau</strong>
                </div>

                <div class="booking-v2-progress-line"></div>

                <div
                    class="booking-v2-progress-item
                    <?php
                    if ($creneauSelectionne) {
                        echo 'active';
                    }
                    ?>"
                >
                    <span>4</span>
                    <strong>Confirmation</strong>
                </div>

            </div>


            <?php if ($erreur !== null): ?>

                <div class="booking-v2-error">
                    <span>!</span>

                    <p>
                        <?php
                        echo htmlspecialchars($erreur);
                        ?>
                    </p>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="booking-v2-form"
            >

                <!-- CSRF -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $_SESSION['csrf_token']
                    );
                    ?>"
                >


                <!-- =========================================
                     01 - PRESTATION
                ========================================== -->

                <section class="booking-v2-section booking-v2-service-section">

                    <div class="booking-v2-section-heading">

                        <span class="booking-v2-number purple">
                            01
                        </span>

                        <div>
                            <h2>
                                Choisissez votre prestation
                            </h2>

                            <p>
                                Quel service souhaitez-vous réserver ?
                            </p>
                        </div>

                    </div>


                    <div class="booking-v2-services">

                        <?php foreach ($prestations as $prestation): ?>

                            <label class="booking-v2-service-card">

                                <input
                                    type="radio"
                                    name="prestation"
                                    value="<?php
                                    echo (int) $prestation['id'];
                                    ?>"
                                    required

                                    <?php
                                    if (
                                        $idPrestationSelectionnee
                                        === (int) $prestation['id']
                                    ) {
                                        echo 'checked';
                                    }
                                    ?>
                                >

                                <span class="booking-v2-service-check">
                                    ✓
                                </span>

                                <span class="booking-v2-service-icon">
                                    ✂
                                </span>

                                <strong class="booking-v2-service-name">
                                    <?php
                                    echo htmlspecialchars(
                                        $prestation['nom']
                                    );
                                    ?>
                                </strong>

                                <span class="booking-v2-service-description">
                                    <?php
                                    echo htmlspecialchars(
                                        $prestation['description']
                                    );
                                    ?>
                                </span>

                                <div class="booking-v2-service-info">

                                    <span>
                                        <?php
                                        echo (int)
                                            $prestation['duree_minutes'];
                                        ?>
                                        min
                                    </span>

                                    <strong>
                                        <?php
                                        echo number_format(
                                            $prestation['prix'],
                                            2,
                                            ',',
                                            ' '
                                        );
                                        ?>
                                        €
                                    </strong>

                                </div>

                            </label>

                        <?php endforeach; ?>

                    </div>

                </section>


                <!-- =========================================
                     02 - DATE
                ========================================== -->

                <section class="booking-v2-section booking-v2-date-section">

                    <div class="booking-v2-section-heading">

                        <span class="booking-v2-number mint">
                            02
                        </span>

                        <div>
                            <h2>
                                Choisissez une date
                            </h2>

                            <p>
                                Sélectionnez le jour qui vous convient.
                            </p>
                        </div>

                    </div>


                    <div class="booking-v2-date-row">

                        <div class="booking-v2-date-input">

                            <span>
                                ◫
                            </span>

                            <input
                                type="date"
                                name="date_rdv"
                                min="<?php echo date('Y-m-d'); ?>"
                                value="<?php
                                echo htmlspecialchars(
                                    $dateSelectionnee ?? ''
                                );
                                ?>"
                                required
                            >

                        </div>


                        <?php
                        if (
                            $idPrestationSelectionnee === null
                            || $dateSelectionnee === null
                        ):
                        ?>

                            <button
                                type="submit"
                                class="booking-main-button"
                            >
                                Voir les créneaux
                                <span>→</span>
                            </button>

                        <?php endif; ?>

                    </div>

                </section>


                <!-- =========================================
                     03 - CRÉNEAU
                ========================================== -->

                <?php
                if (
                    $idPrestationSelectionnee !== null
                    && $dateSelectionnee !== null
                    && $idDisponibiliteSelectionnee === null
                ):
                ?>

                    <section class="booking-v2-section booking-v2-slot-section">

                        <div class="booking-v2-section-heading">

                            <span class="booking-v2-number coral">
                                03
                            </span>

                            <div>
                                <h2>
                                    Choisissez votre créneau
                                </h2>

                                <p>
                                    Les horaires encore disponibles
                                    pour cette date.
                                </p>
                            </div>

                        </div>


                        <?php
                        if (count($creneauxDisponibles) > 0):
                        ?>

                            <div class="booking-v2-slots">

                                <?php
                                foreach (
                                    $creneauxDisponibles
                                    as $creneau
                                ):
                                ?>

                                    <label class="booking-v2-slot">

                                        <input
                                            type="radio"
                                            name="disponibilite"
                                            value="<?php
                                            echo (int) $creneau['id'];
                                            ?>"
                                            required
                                        >

                                        <strong>
                                            <?php
                                            echo substr(
                                                $creneau['heure_debut'],
                                                0,
                                                5
                                            );
                                            ?>
                                        </strong>

                                        <span>
                                            —
                                        </span>

                                        <span>
                                            <?php
                                            echo substr(
                                                $creneau['heure_fin'],
                                                0,
                                                5
                                            );
                                            ?>
                                        </span>

                                        <i>✓</i>

                                    </label>

                                <?php endforeach; ?>

                            </div>


                            <button
                                type="submit"
                                class="booking-main-button booking-v2-continue"
                            >
                                Continuer
                                <span>→</span>
                            </button>


                        <?php else: ?>

                            <div class="booking-v2-empty">

                                <span>◷</span>

                                <div>
                                    <strong>
                                        Aucun créneau disponible
                                    </strong>

                                    <p>
                                        Essayez simplement une autre date.
                                    </p>
                                </div>

                            </div>

                        <?php endif; ?>

                    </section>

                <?php endif; ?>


                <!-- =========================================
                     04 - CONFIRMATION
                ========================================== -->

                <?php if ($creneauSelectionne): ?>

                    <section class="booking-v2-section booking-v2-confirm-section">

                        <div class="booking-v2-section-heading">

                            <span class="booking-v2-number yellow">
                                04
                            </span>

                            <div>
                                <h2>
                                    Vérifiez votre rendez-vous
                                </h2>

                                <p>
                                    Tout est prêt. Il ne reste plus
                                    qu'à confirmer.
                                </p>
                            </div>

                        </div>


                        <div class="booking-v2-summary">

                            <div>

                                <span>
                                    Date
                                </span>

                                <strong>
                                    <?php
                                    echo date(
                                        'd/m/Y',
                                        strtotime(
                                            $dateSelectionnee
                                        )
                                    );
                                    ?>
                                </strong>

                            </div>


                            <div class="booking-v2-summary-divider"></div>


                            <div>

                                <span>
                                    Créneau
                                </span>

                                <strong>
                                    <?php
                                    echo substr(
                                        $creneauSelectionne[
                                            'heure_debut'
                                        ],
                                        0,
                                        5
                                    );
                                    ?>

                                    —

                                    <?php
                                    echo substr(
                                        $creneauSelectionne[
                                            'heure_fin'
                                        ],
                                        0,
                                        5
                                    );
                                    ?>
                                </strong>

                            </div>

                        </div>


                        <input
                            type="hidden"
                            name="disponibilite"
                            value="<?php
                            echo (int)
                                $idDisponibiliteSelectionnee;
                            ?>"
                        >


                        <button
                            type="submit"
                            name="confirmer_rdv"
                            class="booking-v2-confirm-button"
                        >

                            <span class="booking-v2-confirm-icon">
                                ✓
                            </span>

                            <span>
                                Confirmer le rendez-vous
                            </span>

                            <span>
                                →
                            </span>

                        </button>

                    </section>

                <?php endif; ?>

            </form>

        <?php endif; ?>

    </section>

</main>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer class="footer">

        <div class="footer-content">


            <div class="footer-brand">

                <a
                    href="../index.html"
                    class="brand footer-logo"
                >

                    <span class="brand-symbol">
                        ✦
                    </span>

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

                <a href="espace-client.php">
                    Mon espace
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