<?php

require_once '../config/database.php';

$message = '';
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $prenom = trim($_POST['firstname']);
    $nom = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['phone']);
    $motDePasse = $_POST['password'];
    $confirmationMotDePasse = $_POST['password_confirmation'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "L'adresse e-mail n'est pas valide.";

    } elseif ($motDePasse !== $confirmationMotDePasse) {

        $message = "Les mots de passe ne correspondent pas.";

    } else {

        $requete = $pdo->prepare(
            "SELECT id FROM utilisateur WHERE email = ?"
        );

        $requete->execute([$email]);

        $utilisateurExistant = $requete->fetch();

        if ($utilisateurExistant) {

            $message = "Cette adresse e-mail est déjà utilisée.";

        } else {

            $motDePasseHash = password_hash(
                $motDePasse,
                PASSWORD_DEFAULT
            );

            $requete = $pdo->prepare(
                "INSERT INTO utilisateur
                (prenom, nom, email, telephone, mot_de_passe)
                VALUES (?, ?, ?, ?, ?)"
            );

            $requete->execute([
                $prenom,
                $nom,
                $email,
                $telephone,
                $motDePasseHash
            ]);

            $message = "Votre compte a été créé avec succès.";
            $succes = true;
        }
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
        content="Créez votre compte EasyRDV pour réserver vos rendez-vous coiffure."
    >

    <title>Créer un compte | EasyRDV</title>

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
        href="../assets/css/style.css?v=4"
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

                <a href="../index.html#fonctionnement">
                    EasyRDV
                </a>

                <a href="connexion.php">
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
         INSCRIPTION
    ====================================================== -->

    <main class="auth-page register-page">

        <div class="auth-decoration auth-decoration-one"></div>
        <div class="auth-decoration auth-decoration-two"></div>


        <section class="auth-layout register-layout">


            <!-- =============================================
                 PRÉSENTATION
            ============================================== -->

            <div class="auth-presentation">

                <span class="auth-label">
                    ✦ REJOIGNEZ EASYRDV
                </span>


                <h1>
                    Votre prochain rendez-vous
                    <span>commence ici.</span>
                </h1>


                <p class="auth-description">
                    Créez gratuitement votre compte pour réserver
                    vos rendez-vous et les gérer depuis votre
                    espace personnel.
                </p>


                <div class="auth-benefits">


                    <div class="auth-benefit">

                        <div class="auth-benefit-icon purple">
                            1
                        </div>

                        <div>
                            <strong>
                                Créez votre compte
                            </strong>

                            <span>
                                Quelques informations suffisent.
                            </span>
                        </div>

                    </div>


                    <div class="auth-benefit">

                        <div class="auth-benefit-icon mint">
                            2
                        </div>

                        <div>
                            <strong>
                                Choisissez votre prestation
                            </strong>

                            <span>
                                Coupe, barbe ou formule complète.
                            </span>
                        </div>

                    </div>


                    <div class="auth-benefit">

                        <div class="auth-benefit-icon coral">
                            3
                        </div>

                        <div>
                            <strong>
                                Réservez votre créneau
                            </strong>

                            <span>
                                Retrouvez ensuite votre rendez-vous dans votre espace.
                            </span>
                        </div>

                    </div>

                </div>

            </div>



            <!-- =============================================
                 CARTE INSCRIPTION
            ============================================== -->

            <div class="auth-card register-card">


                <div class="auth-card-top">

                    <div class="auth-card-icon">
                        ✦
                    </div>

                    <span>
                        EasyRDV
                    </span>

                </div>


                <h2>
                    Créer un compte
                </h2>


                <p class="auth-card-intro">
                    Renseignez vos informations pour créer
                    votre espace personnel.
                </p>



                <!-- MESSAGE -->

                <?php if ($message !== ''): ?>

                    <div
                        class="<?php echo $succes ? 'auth-message auth-success' : 'auth-message'; ?>"
                    >

                        <span>
                            <?php echo $succes ? '✓' : '!'; ?>
                        </span>

                        <p>
                            <?php echo htmlspecialchars($message); ?>
                        </p>

                    </div>

                <?php endif; ?>



                <!-- FORMULAIRE -->

                <form
                    class="auth-form register-form-grid"
                    method="POST"
                >


                    <!-- PRÉNOM -->

                    <div class="auth-form-group">

                        <label for="firstname">
                            Prénom
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                ✦
                            </span>

                            <input
                                type="text"
                                id="firstname"
                                name="firstname"
                                placeholder="Votre prénom"
                                autocomplete="given-name"
                                minlength="2"
                                maxlength="50"
                                required
                            >

                        </div>

                    </div>



                    <!-- NOM -->

                    <div class="auth-form-group">

                        <label for="lastname">
                            Nom
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                ✦
                            </span>

                            <input
                                type="text"
                                id="lastname"
                                name="lastname"
                                placeholder="Votre nom"
                                autocomplete="family-name"
                                minlength="2"
                                maxlength="50"
                                required
                            >

                        </div>

                    </div>



                    <!-- EMAIL -->

                    <div class="auth-form-group register-full">

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



                    <!-- TÉLÉPHONE -->

                    <div class="auth-form-group register-full">

                        <label for="phone">
                            Téléphone
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                #
                            </span>

                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                placeholder="06 00 00 00 00"
                                autocomplete="tel"
                                maxlength="20"
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
                                placeholder="8 caractères minimum"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                        </div>

                    </div>



                    <!-- CONFIRMATION -->

                    <div class="auth-form-group">

                        <label for="password-confirmation">
                            Confirmer
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
                                •
                            </span>

                            <input
                                type="password"
                                id="password-confirmation"
                                name="password_confirmation"
                                placeholder="Confirmez"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                        </div>

                    </div>



                    <!-- BOUTON -->

                    <button
                        type="submit"
                        class="auth-submit register-full"
                    >

                        <span>
                            Créer mon compte
                        </span>

                        <span class="auth-submit-arrow">
                            →
                        </span>

                    </button>

                </form>



                <!-- CONNEXION -->

                <div class="auth-register">

                    <span>
                        Vous avez déjà un compte ?
                    </span>

                    <a href="connexion.php">
                        Se connecter
                    </a>

                </div>



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