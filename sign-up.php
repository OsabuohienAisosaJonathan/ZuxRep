<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email address.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);

            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ZuxRep</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <!-- Add the following line to include the favicon -->
    <link rel="icon" type="image/png" href="images/favicon/favicon-16x16.png">
    <!--Login & Signup Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">


    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>


    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/misc.css">
    <link rel="stylesheet" href="css/flexslider.css">
    <link rel="stylesheet" href="css/testimonials-slider.css">

    <script src="js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>
</head>

<body>
    <header>
        <!-- <div id="top-header">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="home-account">
                                    <a href="index.html">Home</a>
                                    <a href="#">My account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
        <div id="main-header">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="logo">
                            <a href="#"><img src="images/logo.png" title="ZuxRep" alt="ZuxRep website" width="50px"></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main-menu">
                            <ul>
                                <li><a href="index.html">Home</a></li>
                                <li><a href="about-us.html">About</a></li>
                                <li><a href="products.html">Products</a></li>
                                <li><a href="contact-us.html">Contact</a></li>
                                <li><a href="sign-up.html">GET ACCESS</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- <div class="col-md-3">
                                <div class="search-box">  
                                    <form name="search_form" method="get" class="search_form">
                                        <input id="search" type="text" />
                                        <input type="submit" id="search-button" />
                                    </form>
                                </div>
                            </div> -->
                </div>
            </div>
        </div>
    </header>



    <div id="heading">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading-content">
                        <h2>ACCESS YOUR ACCOUNT</h2>
                        <span>Home / <a href="sign-up.html">Sign Up</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main">

        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Register your Account</h2>
                        <form method="POST" class="register-form" id="register-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="username" id="name" placeholder="Your Name" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                <span class="invalid-feedback"><?php echo $username_err; ?> </span>
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Your Email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                <span class="invalid-feedback"><?php echo $email_err; ?> </span>
                            </div>
                            <div class="form-group">
                                <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="pass" placeholder="Password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?> </span>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="confirm_password" id="re_pass" placeholder="Repeat your password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $confirm_password_err; ?> </span>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" />
                                <label for="agree-term" class="label-agree-term"><span><span></span></span>I agree all statements in <a href="#" class="term-service">Terms of service</a></label>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="submit" />
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="images/signup-image.jpg" alt="sing up image"></figure>
                        <a href="login.php" class="signup-image-link">I am already a member</a>
                    </div>
                </div>
            </div>
        </section>
    </div>




    <footer>
        <div class="container">
            <div class="top-footer">
                <div class="row">
                    <div class="col-md-9">
                        <div class="subscribe-form">
                            <span>Get in touch with us</span>
                            <form method="get" class="subscribeForm">
                                <input id="subscribe" type="text" />
                                <input type="submit" id="submitButton" />
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="social-bottom">
                            <span>Follow us:</span>
                            <ul>
                                <li><a href="#" class="fa fa-facebook"></a></li>
                                <li><a href="#" class="fa fa-twitter"></a></li>
                                <li><a href="#" class="fa fa-rss"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-footer">
                <div class="row">
                    <div class="col-md-3">
                        <div class="about">
                            <h4 class="footer-title">About ZuxRep</h4>
                            <p>ZuxRep is a free <span class="blue">receipt generating website with easy receipt template with customizable features.
                                    <br><br>Credit goes to <a rel="nofollow" href="http://unsplash.com">Ghost</a>.</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="shop-list">
                            <h4 class="footer-title">Upcoming Categories</h4>
                            <ul>
                                <li><a href="#"><i class="fa fa-angle-right"></i>classic</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i>professional</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i>boutique</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i>Local</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i>Basic</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i>Fancy</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="more-info">
                            <h4 class="footer-title">More info</h4>
                            <p>ZuxRep is a demo version mainly created for the purpose of presenting it as a school project.</p>
                            <ul>
                                <li><i class="fa fa-phone"></i>+234-8156-885306</li>
                                <li><i class="fa fa-globe"></i>PMB 1154 Ugbowo Lagos road, Benin city, Nigeria</li>
                                <li><i class="fa fa-envelope"></i><a href="#">zux@anthascil.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-footer">
                <p>
                    <span>Copyright © 2024 <a href="#">Ghost</a> | Design: <a rel="nofollow" href="#" target="_parent"><span class="blue">Ghost</span><span class="green">Inc</span></a></span>
                </p>
            </div>

        </div>
    </footer>


    <script src="js/vendor/jquery-1.11.0.min.js"></script>
    <script src="js/vendor/jquery.gmap3.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/mainLS.js"></script>
</body>

</html>
