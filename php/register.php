<?php
// Include config file
require_once "connect.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $mail = "";
$username_err = $password_err = $confirm_password_err = $mail_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Veuillez renseigner un nom d'utilisateur";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Ce nom est déjà pris.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Mince, quelque chose a foiré, avez-vous essayé de redémarrer votre modem ?";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Veuillez renseigner un mot de passe.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Les mots de passe doivent avoir au moins 6 caractères.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Veuillez confirmer le mot de passe.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Les deu mots de passe ne correspondent pas.";
        }
    }
    
	
	    // Validate email
    if(empty(trim($_POST["mail"]))){
        $mail_err = "Veuillez renseigner une adresse mail";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE mail = :mail";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":mail", $param_mail, PDO::PARAM_STR);
            
            // Set parameters
            $param_mail = trim($_POST["mail"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $mail_err = "Cette adresse email est déjà utilisée.";
                } else{
                    $mail = trim($_POST["mail"]);
                }
            } else{
                echo "Mince, quelque chose a foiré, avez-vous essayé de redémarrer votre modem ?";
            }

            // Close statement
            unset($stmt);
        }
    }
	
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, mail) VALUES (:username, :password, :mail)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":mail", $param_mail, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
			$param_mail = $mail;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Mince, quelque chose a foiré, avez-vous essayé de redémarrer votre modem ?";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Inscription</h2>
        <p>Remplissez les champs afin de créer un compte.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirmer mot de passe</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
			<div class="form-group <?php echo (!empty($mail_err)) ? 'has-error' : ''; ?>">
                <label>Entrez une adresse e-mail</label>
                <input type="email" name="mail" class="form-control" value="<?php echo $mail_err; ?>">
                <span class="help-block"><?php echo $mail_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Envoyer">
                <input type="reset" class="btn btn-default" value="Réinitialiser">
            </div>
            <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici.</a>.</p>
        </form>
    </div>    
</body>
</html>