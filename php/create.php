<?php
// Include config file
require_once "connect.php";

// Define variables and initialize with empty values
$username = $mail = $password = "";
$username_err = $mail_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["username"]);
    if(empty($input_name)){
        $username_err = "Veuillez renseigner un nom d'utilisateur.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $username_err = "Veuillez renseigner un nom d'utilisateur valide.";
    } else{
        $username = $input_name;
    }

    // Validate mail address
    $input_mail = trim($_POST["mail"]);
    if(empty($input_mail)){
        $mail_err = "Veuillez entrer une adresse email.";
    } else{
        $mail = $input_mail;
    }

    // Validate & hash password
    $input_password = trim($_POST["password"]);
    if(empty($input_password)){
        $password_err = "Veuillez renseigner un mot de passe.";
    }  else{
        $password = $input_password;
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($mail_err) && empty($password_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, mail, password) VALUES (:username, :mail, :password)";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_name);
            $stmt->bindParam(":mail", $param_address);
            $stmt->bindParam(":password", $param_salary);

            // Set parameters
            $param_name = $username;
            $param_address = $mail;
            $param_salary = password_hash($password, PASSWORD_DEFAULT);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: welcome.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un enregistrement </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h2>Créer un enregistrement</h2>
                </div>
                <p>Pour ajouter un nouveau client, remplissez les champs ci-ddesous.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                        <span class="help-block"><?php echo $username_err;?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($mail_err)) ? 'has-error' : ''; ?>">
                        <label>Adresse email</label>
                        <input type="email" name="mail" class="form-control" value=" <?php echo $mail; ?>">
                        <span class="help-block"><?php echo $mail_err;?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Mot de passe</label>
                        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                        <span class="help-block"><?php echo $password_err;?></span>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <a href="index.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>