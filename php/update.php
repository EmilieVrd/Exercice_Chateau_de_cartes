<?php
// Include config file
require_once "connect.php";

// Define variables and initialize with empty values
$username = $mail = $password = "";
$username_err = $mail_err = $password_err = "";

// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
// Get hidden input value
$id = $_POST["id"];

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
        // Prepare an update statement
        $sql = "UPDATE users SET username=:username, mail=:mail, password=:password WHERE id=:id";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_name);
            $stmt->bindParam(":mail", $param_address);
            $stmt->bindParam(":password", $param_salary);
            $stmt->bindParam(":id", $param_id);

            // Set parameters
            $param_name = $username;
            $param_address = $mail;
            $param_salary = password_hash($password, PASSWORD_DEFAULT);
            $param_id = $id;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
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
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM users WHERE id = :id";
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":id", $param_id);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Retrieve individual field value
                    $name = $row["username"];
                    $address = $row["mail"];
                    $salary = $row["password"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }

            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);

        // Close connection
        unset($pdo);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mise à jour enregistrement</title>
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
                    <h2>Mettre à jour l'enregistrement</h2>
                </div>
                <p>Editez les champs afin de mettre à jour les informations du client.</p>
                <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                        <span class="help-block"><?php echo $username_err;?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($mail_err)) ? 'has-error' : ''; ?>">
                        <label>Addresse email</label>
                        <input type="email" name="mail" class="form-control" value=" <?php echo $mail; ?>">
                        <span class="help-block"><?php echo $mail_err;?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Mot de passe</label>
                        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                        <span class="help-block"><?php echo $password_err;?></span>
                    </div>
                    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                    <input type="submit" class="btn btn-primary" value="Mettre à jour">
                    <a href="welcome.php" class="btn btn-default">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>