<?php
session_start();

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

include 'db.php';

// Only allow admin to process add/update/delete
if ($isAdmin) {
    // Add Medical Record
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['record_id'])) {
        $pet_id = $_POST['pet_id'];
        $checkup_date = $_POST['checkup_date'];
        $vaccinations = $_POST['vaccinations'];
        $medical_notes = $_POST['medical_notes'];
        $vet_name = $_POST['vet_name'];

        $sql = "INSERT INTO Medical_Records (pet_id, checkup_date, vaccinations, medical_notes, vet_name) 
                VALUES ('$pet_id', '$checkup_date', '$vaccinations', '$medical_notes', '$vet_name')";
        $conn->query($sql);
        header("Location: medical_records.php");
        exit();
    }

    // Update Medical Record
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['record_id'])) {
        $record_id = $_POST['record_id'];
        $pet_id = $_POST['pet_id'];
        $checkup_date = $_POST['checkup_date'];
        $vaccinations = $_POST['vaccinations'];
        $medical_notes = $_POST['medical_notes'];
        $vet_name = $_POST['vet_name'];

        $sql = "UPDATE Medical_Records SET pet_id='$pet_id', checkup_date='$checkup_date', vaccinations='$vaccinations', 
                medical_notes='$medical_notes', vet_name='$vet_name' WHERE record_id='$record_id'";
        $conn->query($sql);
        header("Location: medical_records.php");
        exit();
    }

    // Delete Medical Record
    if (isset($_GET['delete'])) {
        $record_id = $_GET['delete'];
        $sql = "DELETE FROM Medical_Records WHERE record_id = $record_id";
        $conn->query($sql);
        header("Location: medical_records.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Records Management</title>
    <link rel="stylesheet" href="styles/medical_records.css">
</head>
<body>
<div class="container">

    <!-- Form Container -->
    <div class="form-container">
        <?php if ($isAdmin): ?>
            <h2><?= isset($_GET['edit']) ? "Update Medical Record" : "Add Medical Record" ?></h2>
            <form method="POST">
                <?php
                if (isset($_GET['edit'])) {
                    $record_id = $_GET['edit'];
                    $sql = "SELECT * FROM Medical_Records WHERE record_id = $record_id";
                    $result = $conn->query($sql);
                    $record = $result->fetch_assoc();
                    ?>
                    <input type="hidden" name="record_id" value="<?= $record['record_id'] ?>">
                    <input type="number" name="pet_id" value="<?= $record['pet_id'] ?>" placeholder="Pet ID" required>
                    <input type="date" name="checkup_date" value="<?= $record['checkup_date'] ?>" required>
                    <input type="text" name="vaccinations" value="<?= htmlspecialchars($record['vaccinations']) ?>" placeholder="Vaccinations">
                    <input type="text" name="medical_notes" value="<?= htmlspecialchars($record['medical_notes']) ?>" placeholder="Medical Notes">
                    <input type="text" name="vet_name" value="<?= htmlspecialchars($record['vet_name']) ?>" placeholder="Vet Name" required>
                    <input type="submit" value="Update Record">
                    <?php
                } else {
                    ?>
                    <input type="number" name="pet_id" placeholder="Pet ID" required>
                    <input type="date" name="checkup_date" required>
                    <input type="text" name="vaccinations" placeholder="Vaccinations">
                    <input type="text" name="medical_notes" placeholder="Medical Notes">
                    <input type="text" name="vet_name" placeholder="Vet Name" required>
                    <input type="submit" value="Add Record">
                    <?php
                }
                ?>
            </form>
        <?php else: ?>
            <p><em>You are viewing medical records. Only admins can add or edit records.</em></p>
        <?php endif; ?>
    </div>

    <!-- Records Display -->
    <div class="medical-list-container">
        <?php
        $sql = "SELECT * FROM Medical_Records";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='medical-card'>";
                echo "<div class='record-id'>Record ID: " . $row["record_id"] . "</div>";
                echo "<p><strong>Pet ID:</strong> " . $row["pet_id"] . "</p>";
                echo "<p><strong>Checkup Date:</strong> " . $row["checkup_date"] . "</p>";
                echo "<p><strong>Vaccinations:</strong> " . htmlspecialchars($row["vaccinations"]) . "</p>";
                echo "<p><strong>Notes:</strong> " . htmlspecialchars($row["medical_notes"]) . "</p>";
                echo "<p><strong>Vet:</strong> " . htmlspecialchars($row["vet_name"]) . "</p>";
                if ($isAdmin) {
                    echo "<div class='action-buttons'>";
                    echo "<a class='edit-btn' href='medical_records.php?edit=" . $row["record_id"] . "'>Edit</a> ";
                    echo "<a class='delete-btn' href='medical_records.php?delete=" . $row["record_id"] . "' onclick=\"return confirm('Delete this record?')\">Delete</a>";
                    echo "</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No medical records found.</p>";
        }
        ?>
    </div>
</div>

<footer> Medical Records Management </footer>
</body>
</html>
