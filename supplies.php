<?php
include 'db.php';

// Add Supply
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['supply_id'])) {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    $shelter_id = $_POST['shelter_id'];

    $sql = "INSERT INTO Supplies (item_name, quantity, category, shelter_id) 
            VALUES ('$item_name', '$quantity', '$category', '$shelter_id')";
    $conn->query($sql);
    header("Location: supplies.php");
    exit();
}

// Update Supply
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supply_id'])) {
    $supply_id = $_POST['supply_id'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    $shelter_id = $_POST['shelter_id'];

    $sql = "UPDATE Supplies SET item_name='$item_name', quantity='$quantity', category='$category', 
            shelter_id='$shelter_id' WHERE supply_id='$supply_id'";
    $conn->query($sql);
    header("Location: supplies.php");
    exit();
}

// Delete Supply
if (isset($_GET['delete'])) {
    $supply_id = $_GET['delete'];
    $sql = "DELETE FROM Supplies WHERE supply_id = $supply_id";
    $conn->query($sql);
    header("Location: supplies.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Management</title>
    <link rel="stylesheet" href="styles/supplies.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <?php
        if (isset($_GET['edit'])) {
            $supply_id = $_GET['edit'];
            $sql = "SELECT * FROM Supplies WHERE supply_id = $supply_id";
            $result = $conn->query($sql);
            $supply = $result->fetch_assoc();
            echo "<h2>Update Supply</h2>
            <form method='POST'>
                <input type='hidden' name='supply_id' value='{$supply['supply_id']}'>
                <input type='text' name='item_name' value='{$supply['item_name']}' placeholder='Item Name' required><br>
                <input type='number' name='quantity' value='{$supply['quantity']}' placeholder='Quantity' required><br>
                <input type='text' name='category' value='{$supply['category']}' placeholder='Category' required><br>
                <input type='number' name='shelter_id' value='{$supply['shelter_id']}' placeholder='Shelter ID' required><br>
                <input type='submit' value='Update Supply'>
            </form><hr>";
        } else {
            echo "<h2>Add Supply</h2>
            <form method='POST'>
                <input type='text' name='item_name' placeholder='Item Name' required><br>
                <input type='number' name='quantity' placeholder='Quantity' required><br>
                <input type='text' name='category' placeholder='Category' required><br>
                <input type='number' name='shelter_id' placeholder='Shelter ID' required><br>
                <input type='submit' value='Add Supply'>
            </form><hr>";
        }
        ?>
    </div>

    <div class="supply-list-container">
        <?php
        $sql = "SELECT * FROM Supplies";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='supply-card'>
                    <div class='supply-id'>ID: " . $row["supply_id"] . "</div>
                    <p><strong>Item Name:</strong> " . $row["item_name"] . "</p>
                    <p><strong>Quantity:</strong> " . $row["quantity"] . "</p>
                    <p><strong>Category:</strong> " . $row["category"] . "</p>
                    <p><strong>Shelter ID:</strong> " . $row["shelter_id"] . "</p>
                    <div class='action-buttons'>
                        <a href='supplies.php?edit=" . $row["supply_id"] . "' class='edit-btn'>Edit</a>
                        <a href='supplies.php?delete=" . $row["supply_id"] . "' class='delete-btn' onclick=\"return confirm('Delete this supply?')\">Delete</a>
                    </div>
                </div>";
            }
        } else {
            echo "No supplies found.";
        }
        ?>
    </div>
</div>

<footer>
    <p>Supply Management</p>
</footer>

</body>
</html>
