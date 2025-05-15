<?php
session_start();

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

$host = "localhost";
$user = "root";
$password = "";
$dbname = "pet_adoption";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only allow admin to process add/update/delete
if ($isAdmin) {
    // Add new pet
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $species = $_POST['species'];
        $breed = $_POST['breed'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $adoption_status = $_POST['adoption_status'];
        $shelter_id = $_POST['shelter_id'];

        $sql = "INSERT INTO Pets (name, species, breed, age, gender, adoption_status, shelter_id)
                VALUES ('$name', '$species', '$breed', '$age', '$gender', '$adoption_status', '$shelter_id')";
        $conn->query($sql);
        header("Location: pets.php");
        exit();
    }

    // Update pet
    if (isset($_POST['update'])) {
        $pet_id = $_POST['pet_id'];
        $name = $_POST['name'];
        $species = $_POST['species'];
        $breed = $_POST['breed'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $adoption_status = $_POST['adoption_status'];
        $shelter_id = $_POST['shelter_id'];

        $sql = "UPDATE Pets SET name='$name', species='$species', breed='$breed', age='$age', gender='$gender', adoption_status='$adoption_status', shelter_id='$shelter_id' WHERE pet_id='$pet_id'";
        $conn->query($sql);
        header("Location: pets.php");
        exit();
    }

    // Delete pet
    if (isset($_GET['delete'])) {
        $pet_id = $_GET['delete'];
        $sql = "DELETE FROM Pets WHERE pet_id='$pet_id'";
        $conn->query($sql);
        header("Location: pets.php");
        exit();
    }
}

// Fetch only non-adopted pets for both admin and user
$sql = "SELECT * FROM Pets WHERE adoption_status != 'Adopted'";
$result = $conn->query($sql);

// For editing: load pet data if edit param present
$edit_pet = null;
if ($isAdmin && isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM Pets WHERE pet_id='$edit_id'");
    if ($res && $res->num_rows > 0) {
        $edit_pet = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Pets</title>
    <link rel="stylesheet" href="styles/pets.css" />
</head>
<body>

<h1>Manage Pets</h1>

<?php if ($isAdmin): ?>
    <div class="form-container">
        <h2><?= $edit_pet ? "Edit Pet" : "Add New Pet" ?></h2>
        <form method="POST" action="pets.php">
            <?php if ($edit_pet): ?>
                <input type="hidden" name="pet_id" value="<?= $edit_pet['pet_id'] ?>">
            <?php endif; ?>
            <input type="text" name="name" placeholder="Name" required value="<?= $edit_pet['name'] ?? '' ?>">
            <input type="text" name="species" placeholder="Species" required value="<?= $edit_pet['species'] ?? '' ?>">
            <input type="text" name="breed" placeholder="Breed" required value="<?= $edit_pet['breed'] ?? '' ?>">
            <input type="number" name="age" placeholder="Age" required min="0" value="<?= $edit_pet['age'] ?? '' ?>">
            <input type="text" name="gender" placeholder="Gender" required value="<?= $edit_pet['gender'] ?? '' ?>">
            <input type="text" name="adoption_status" placeholder="Adoption Status" required value="<?= $edit_pet['adoption_status'] ?? '' ?>">
            <input type="number" name="shelter_id" placeholder="Shelter ID" required value="<?= $edit_pet['shelter_id'] ?? '' ?>">

            <?php if ($edit_pet): ?>
                <button type="submit" name="update">Update Pet</button>
                <a href="pets.php">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add">Add Pet</button>
            <?php endif; ?>
        </form>
    </div>
<?php else: ?>
    <p><em>You are viewing pets. Only admins can add or edit pets.</em></p>
<?php endif; ?>

<h2>Pet List</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Pet ID</th>
        <th>Name</th>
        <th>Species</th>
        <th>Breed</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Adoption Status</th>
        <th>Shelter ID</th>
        <?php if ($isAdmin): ?>
            <th>Actions</th>
        <?php endif; ?>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['pet_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['species']) ?></td>
                <td><?= htmlspecialchars($row['breed']) ?></td>
                <td><?= $row['age'] ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td><?= htmlspecialchars($row['adoption_status']) ?></td>
                <td><?= $row['shelter_id'] ?></td>
                <?php if ($isAdmin): ?>
                    <td>
                        <a href="pets.php?edit=<?= $row['pet_id'] ?>">Edit</a> |
                        <a href="pets.php?delete=<?= $row['pet_id'] ?>" onclick="return confirm('Are you sure you want to delete this pet?')">Delete</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="<?= $isAdmin ? 9 : 8 ?>">No pets found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
