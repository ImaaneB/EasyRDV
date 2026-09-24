<?php

session_start();

require_once '../config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $motDePasse = $_POST['password'];

    $requete = $pdo->prepare(
        "SELECT * FROM utilisateur WHERE email = ?"
    );

    $requete->execute([$email]);

    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    if (
        $utilisateur &&
        password_verify(
            $motDePasse,
            $utilisateur['mot_de_passe']
        )
    ) {

        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['prenom'] = $utilisateur['prenom'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['role'] = $utilisateur['role'];

        // Redirection selon le rôle
        if ($utilisateur['role'] === 'admin') {

            header('Location: admin.php');
            exit;

        } else {

            header('Location: espace-client.php');
            exit;
        }

    } else {

        $message = "Adresse e-mail ou mot de passe incorrect.";
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
        content="Connectez-vous à votre compte EasyRDV."
    >

    <title>Connexion | EasyRDV</title>

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
        href="../assets/css/style.css?v=3"
    >

</head>


<body>


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <header class="site-header">

        <nav class="main-nav">

            <!-- Logo -->
            <a href="../index.html" class="brand">

                <span class="brand-symbol">✦</span>

                <span class="brand-name">
                    <span>Easy</span><strong>RDV</strong>
                </span>

                <span class="brand-dot"></span>

            </a>


            <!-- Navigation -->
            <div class="nav-links">

                <a href="../index.html">
                    Accueil
                </a>

                <a href="../index.html#prestations">
                    Prestations
                </a>

                <a href="../index.html#fonctionnement">
                    EasyRDV
                </a>

                <a
                    href="connexion.php"
                    class="active"
                >
                    Connexion
                </a>

                <a
                    href="reservation.php"
                    class="nav-reservation"
                >
                    Réserver
                    <span>↗</span>
                </a>

            </div>

        </nav>

    </header>



    <!-- =====================================================
         CONNEXION
    ====================================================== -->

    <main class="auth-page">


        <!-- Décorations -->
        <div class="auth-decoration auth-decoration-one"></div>
        <div class="auth-decoration auth-decoration-two"></div>


        <section class="auth-layout">


            <!-- =============================================
                 PARTIE GAUCHE
            ============================================== -->

            <div class="auth-presentation">

                <span class="auth-label">
                    ✦ ESPACE CLIENT
                </span>


                <h1>
                    Heureux de vous
                    <span>revoir.</span>
                </h1>


                <p class="auth-description">
                    Connectez-vous pour réserver votre prochain
                    rendez-vous et retrouver facilement toutes
                    vos réservations.
                </p>


                <!-- Petits avantages -->

                <div class="auth-benefits">


                    <div class="auth-benefit">

                        <div class="auth-benefit-icon purple">
                            ✓
                        </div>

                        <div>
                            <strong>
                                Réservez simplement
                            </strong>

                            <span>
                                Choisissez votre créneau en quelques clics.
                            </span>
                        </div>

                    </div>


                    <div class="auth-benefit">

                        <div class="auth-benefit-icon mint">
                            ◷
                        </div>

                        <div>
                            <strong>
                                Gérez vos rendez-vous
                            </strong>

                            <span>
                                Consultez et annulez vos réservations.
                            </span>
                        </div>

                    </div>


                    <div class="auth-benefit">

                        <div class="auth-benefit-icon coral">
                            ✦
                        </div>

                        <div>
                            <strong>
                                Votre espace personnel
                            </strong>

                            <span>
                                Retrouvez toutes vos informations au même endroit.
                            </span>
                        </div>

                    </div>

                </div>

            </div>



            <!-- =============================================
                 FORMULAIRE
            ============================================== -->

            <div class="auth-card">


                <div class="auth-card-top">

                    <div class="auth-card-icon">
                        ✦
                    </div>

                    <span>
                        EasyRDV
                    </span>

                </div>


                <h2>
                    Connexion
                </h2>


                <p class="auth-card-intro">
                    Entrez vos identifiants pour accéder
                    à votre espace.
                </p>



                <!-- MESSAGE D'ERREUR -->

                <?php if ($message !== ''): ?>

                    <div class="auth-message">

                        <span>!</span>

                        <p>
                            <?php echo htmlspecialchars($message); ?>
                        </p>

                    </div>

                <?php endif; ?>



                <!-- FORMULAIRE -->

                <form
                    class="auth-form"
                    method="POST"
                >


                    <!-- EMAIL -->

                    <div class="auth-form-group">

                        <label for="email">
                            Adresse e-mail
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                @
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="exemple@email.com"
                                autocomplete="email"
                                maxlength="150"
                                required
                            >

                        </div>

                    </div>



                    <!-- MOT DE PASSE -->

                    <div class="auth-form-group">

                        <label for="password">
                            Mot de passe
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                •
                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Votre mot de passe"
                                autocomplete="current-password"
                                minlength="8"
                                required
                            >

                        </div>

                    </div>



                    <!-- BOUTON -->

                    <button
                        type="submit"
                        class="auth-submit"
                    >
                        <span>
                            Se connecter
                        </span>

                        <span class="auth-submit-arrow">
                            →
                        </span>
                    </button>

                </form>



                <!-- INSCRIPTION -->

                <div class="auth-register">

                    <span>
                        Vous n'avez pas encore de compte ?
                    </span>

                    <a href="inscription.php">
                        Créer un compte
                    </a>

                </div>


                <!-- Petite décoration -->

                <div class="auth-card-dots">

                    <i></i>
                    <i></i>
                    <i></i>

                </div>

            </div>

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

                <a href="connexion.php">
                    Connexion
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