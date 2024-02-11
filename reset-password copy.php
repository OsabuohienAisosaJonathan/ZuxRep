<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else{
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


        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/misc.css">
        <link rel="stylesheet" href="css/flexslider.css">
        <link rel="stylesheet" href="css/testimonials-slider.css">

        <!-- Signup & Login css -->
        <link rel="stylesheet" href="css/main.css">

        
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
                                <span>Home / <a href="login.html">Passord Reset</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main">
                <!-- Log in  Form -->
                <section class="sign-in">
                    <div class="container">
                        <div class="signin-content">
                            <!-- <div class="signin-image">
                                <figure><img src="images/signin-image.jpg" alt="sing up image"></figure>
                                <a href="#" class="signup-image-link">Create an account</a>
                            </div> -->
        
                            <div class="signin-form">
                                <h2 class="form-title">Reset your Account</h2>
                                <?php 
                                    if(!empty($login_err)){
                                        echo '<div class="alert alert-danger">' . $login_err . '</div>';
                                    }        
                                ?>
                                <form method="POST" class="register-form" id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="form-group">
                                        <label for="new_password"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                        <input type="password" name="new_password" id="your_pass" placeholder="Your New Password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>
                                        <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                        <input type="password" name="confirm_password" id="your_pass" placeholder="confirm Password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"/>
                                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" name="remember-me" id="remember-me" class="agree-term" />
                                        <label for="remember-me" class="label-agree-term"><span><span></span></span>Remember me</label>
                                    </div>
                                    <div class="form-group form-button">
                                        <input type="submit" name="signin" id="signin" class="form-submit" value="submit"/>
                                        <a class="btn btn-link ml-2" href="welcome.php">Cancel</a>
                                    </div>
                                </form>
        
                                <div class="sign-up">
                                    <a href="sign-up.php">Don't have an account? Sign up now</a>
                                </div>
                                <!-- <div class="social-login">
                                    <span class="social-label">Or login with</span>
                                    <ul class="socials">
                                        <li><a href="#"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                                        <li><a href="#"><i class="display-flex-center zmdi zmdi-twitter"></i></a></li>
                                        <li><a href="#"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
                                    </ul>
                                </div> -->
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
                        	<span>Copyright © 2024 <a href="#">Ghost</a> 
                            | Design: <a rel="nofollow" href="#" target="_parent"><span class="blue">Ghost</span><span class="green">Inc</span></a></span>
                        </p>
                    </div>
                    
                </div>
            </footer>

    
        <script src="js/vendor/jquery-1.11.0.min.js"></script>
        <script src="js/vendor/jquery.gmap3.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script src="js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="js/mainLS.js"></script>
    </body>
</html>