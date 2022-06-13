<?php
// Includeer DB connectie file
require_once "../config/DB.php";

// Definieer variabelen en initialiseer met lege waarden
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Formuliergegevens verwerken wanneer formulier wordt ingediend
if($_SERVER["REQUEST_METHOD"] == "POST"){
    echo "<pre>".print_r($_POST, true)."</pre>";


    // Validate gebruikersnaam
    if(empty(trim($_POST["username"]))){
        $username_err = "Voer een gebruikersnaam in.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Gebruikersnaam mag alleen letters, cijfers en underscores bevatten.";
    } else{
        // Prepare een SELECT statement
        $sql = "SELECT id FROM users WHERE username = :username";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Parameters instellen
            $param_username = trim($_POST["username"]);

            // Poging om de prepared statement uit te voeren
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Deze gebruikersnaam is al in gebruik.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oeps! Er is iets misgegaan. Probeer het later opnieuw.";
            }

            // Sluit statement
            unset($stmt);
        }
    }

    // Valideer wachtwoord
    if(empty(trim($_POST["password"]))){
        $password_err = "Aub voer uw wachtwoord in.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Wachtwoord moet minimaal 6 tekens bevatten.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Valideer bevestigd wachtwoord
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Bevestig wachtwoord nogmaals.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Wachtwoord komt niet overeen.";
        }
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

        if($stmt = $pdo->prepare($sql)){

            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Stuur door naar login pagina
                header("location: login.php");
            } else{
                echo "Oeps! Er is iets misgegaan. Probeer het later opnieuw.";
            }

            // Sluit statement
            unset($stmt);
        }
    }

    // Sluit verbinding
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <style>
        body {
            font: 14px sans-serif;
        }
        .wrapper {
            width: 360px;
            padding: 20px;
            margin: 100px auto;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Meld u aan <i class="fas fa-user-plus"></i></h2>
    <p>Vul dit formulier in om een account aan te maken.</p>
    <form action="" method="post">
        <div class="form-group">
            <label>Gebruikersnaam</label>
            <input required type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>Wachtwoord</label>
            <input required type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <label>Bevestig wachtwoord</label>
            <input required type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Verzenden">
            <input type="reset" class="btn btn-secondary ml-2" value="Annuleer">
        </div>
        <p>Heb je een account? <a href="login.php">Log hier in</a>.</p>
    </form>
</div>
</body>
</html>