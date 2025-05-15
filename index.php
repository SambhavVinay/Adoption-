<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: intro.php");
    exit();
}

$isAdmin = $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pet Adoption Management</title>
    <link rel="stylesheet" href="styles/index.css" />
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code&family=Lato&family=Montserrat&family=Poppins&family=Playfair+Display&family=Roboto&display=swap" rel="stylesheet" />
</head>
<body>

    <header>
        <h1>Welcome to Pet Adoption Management System</h1>
    </header>

    <div class="container">
        <div class="heading"><h2>MANAGE THE FOLLOWING TABLES</h2></div>
        <div class="inst" align="center">
            <p>Click on the links below to manage the different sections of the pet adoption database:</p>
        </div>

        <div class="link-grid">
            <?php if ($isAdmin): ?>
                <a href="pets.php" class="table-link">Manage Pets</a>
                <a href="foster_homes.php" class="table-link">Manage Foster Homes</a>
                <a href="shelters.php" class="table-link">Manage Shelters</a>
                <a href="adopters.php" class="table-link">Manage Adopters</a>
                <a href="employees.php" class="table-link">Manage Employees</a>
                <a href="medical_records.php" class="table-link">Manage Medical Records</a>
                <a href="supplies.php" class="table-link">Manage Supplies</a>
                <a href="adoptions.php" class="table-link">Manage Adoptions</a>
            <?php else: ?>
                <a href="pets.php" class="table-link">Pets Available</a>
                <a href="foster_homes.php" class="table-link">Foster Homes</a>
                <a href="shelters.php" class="table-link">See Shelters</a>
                <a href="medical_records.php" class="table-link">Medical Records</a>
                <a href="adopters.php" class="table-link">Add New Adopter</a>
                
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
