<?php
// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
    require_once "connect.php";

    // Prepare a select statement
    $sql = "SELECT * FROM users WHERE id = :id";

    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $param_id);

        // Set parameters
        $param_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Retrieve individual field value
                $name = $row["username"];
                $address = $row["mail"];
                $salary = $row["created_at"];
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
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
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vue enregistrement</title>
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
                    <h1>Détails enregistrement</h1>
                </div>
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <p class="form-control-static"><?php echo $row["username"]; ?></p>
                </div>
                <div class="form-group">
                    <label>Addresse email</label>
                    <p class="form-control-static"><?php echo $row["mail"]; ?></p>
                </div>
                <div class="form-group">
                    <label>Date d'inscription</label>
                    <p class="form-control-static"><?php echo $row["created_at"]; ?></p>
                </div>
                <p><a href="welcome.php" class="btn btn-primary">Retour</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>