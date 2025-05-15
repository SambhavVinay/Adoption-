<?php
session_start();

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Include the database connection file
include 'db.php';

// Only allow admin to process add/update/delete
if ($isAdmin && $_SERVER["REQUEST_METHOD"] == "POST") {
    $foster_name = $_POST['foster_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $capacity = $_POST['capacity'];
    $current_pets = $_POST['current_pets'];
    $shelter_id = $_POST['shelter_id'];

    // Check if the shelter_id exists in the 'shelters' table
    $sql_check_shelter = "SELECT * FROM shelters WHERE shelter_id = ?";
    $stmt_check_shelter = $conn->prepare($sql_check_shelter);
    $stmt_check_shelter->bind_param("i", $shelter_id);
    $stmt_check_shelter->execute();
    $result_check_shelter = $stmt_check_shelter->get_result();

    if ($result_check_shelter->num_rows > 0) {
        // Shelter exists, proceed with insert or update
        if (!isset($_POST['foster_id'])) {
            // Insert new foster home
            $sql = "INSERT INTO Foster_Homes (foster_name, phone, address, capacity, current_pets, shelter_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiis", $foster_name, $phone, $address, $capacity, $current_pets, $shelter_id);
        } else {
            // Update existing foster home
            $foster_id = $_POST['foster_id'];
            $sql = "UPDATE Foster_Homes SET foster_name = ?, phone = ?, address = ?, capacity = ?, current_pets = ?, shelter_id = ? 
                    WHERE foster_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiisi", $foster_name, $phone, $address, $capacity, $current_pets, $shelter_id, $foster_id);
        }

        if ($stmt->execute()) {
            header("Location: foster_homes.php");
            exit();
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p>Error: Shelter ID does not exist in the shelters table.</p>";
    }
}

// Check for delete request - only admin allowed
if ($isAdmin && isset($_GET['delete'])) {
    $foster_id = $_GET['delete'];
    $sql = "DELETE FROM Foster_Homes WHERE foster_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $foster_id);
    if ($stmt->execute()) {
        header("Location: foster_homes.php");
        exit();
    } else {
        echo "<p>Error deleting foster home: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Foster Homes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles/foster_homes.css" />
</head>
<body>
    <div class="container">
        <div class="form-container">
            <?php if ($isAdmin): ?>
                <?php
                // Display form for editing or adding foster homes
                if (isset($_GET['edit'])) {
                    $foster_id = $_GET['edit'];
                    $sql = "SELECT * FROM Foster_Homes WHERE foster_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $foster_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $foster = $result->fetch_assoc();
                    ?>
                    <h2>Update Foster Home</h2>
                    <form action="foster_homes.php" method="POST">
                        <input type="hidden" name="foster_id" value="<?= $foster['foster_id'] ?>">
                        <input type="text" name="foster_name" value="<?= htmlspecialchars($foster['foster_name']) ?>" placeholder="Foster Name" required><br>
                        <input type="text" name="phone" value="<?= htmlspecialchars($foster['phone']) ?>" placeholder="Phone" required><br>
                        <input type="text" name="address" value="<?= htmlspecialchars($foster['address']) ?>" placeholder="Address" required><br>
                        <input type="number" name="capacity" value="<?= $foster['capacity'] ?>" placeholder="Capacity" required><br>
                        <input type="number" name="current_pets" value="<?= $foster['current_pets'] ?>" placeholder="Current Pets" required><br>
                        <input type="number" name="shelter_id" value="<?= $foster['shelter_id'] ?>" placeholder="Shelter ID" required><br>
                        <input type="submit" value="Update Foster Home">
                    </form>
                    <hr>
                <?php
                } else {
                    ?>
                    <h2>Add a New Foster Home</h2>
                    <form action="foster_homes.php" method="POST">
                        <input type="text" name="foster_name" placeholder="Foster Name" required><br>
                        <input type="text" name="phone" placeholder="Phone" required><br>
                        <input type="text" name="address" placeholder="Address" required><br>
                        <input type="number" name="capacity" placeholder="Capacity" required><br>
                        <input type="number" name="current_pets" placeholder="Current Pets" required><br>
                        <input type="number" name="shelter_id" placeholder="Shelter ID" required><br>
                        <input type="submit" value="Add Foster Home">
                    </form>
                    <hr>
                <?php
                }
                ?>
            <?php else: ?>
                <p><em>You are viewing foster homes. Only admins can add or edit foster homes.</em></p>
            <?php endif; ?>
        </div>

        <div class="foster-home-list-container">
            <?php
            // Display all foster homes as cards
            $sql = "SELECT * FROM Foster_Homes";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='foster-home-card'>";
                    echo "<div class='foster-home-id'>ID: " . $row["foster_id"] . "</div>";
                    echo "<p><strong>Foster Name:</strong> " . htmlspecialchars($row["foster_name"]) . "</p>";
                    echo "<p><strong>Phone:</strong> " . htmlspecialchars($row["phone"]) . "</p>";
                    echo "<p><strong>Address:</strong> " . htmlspecialchars($row["address"]) . "</p>";
                    echo "<p><strong>Capacity:</strong> " . $row["capacity"] . "</p>";
                    echo "<p><strong>Current Pets:</strong> " . $row["current_pets"] . "</p>";
                    echo "<p><strong>Shelter ID:</strong> " . $row["shelter_id"] . "</p>";
                    if ($isAdmin) {
                        echo "<div class='action-buttons'>
                                <a href='foster_homes.php?edit=" . $row["foster_id"] . "' class='edit-btn'>Edit</a>
                                <a href='foster_homes.php?delete=" . $row["foster_id"] . "' onclick=\"return confirm('Delete this foster home?')\" class='delete-btn'>Delete</a>
                              </div>";
                    }
                    echo "</div><br><hr>";
                }
            } else {
                echo "No foster homes found.";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>Foster Homes Management</p>
    </footer>
</body>
</html>
