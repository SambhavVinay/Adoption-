<?php
session_start();

$admin_email = "admin@gmail.com";
$admin_password = "Rvu$1234";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["role"] == "admin") {
        $email = $_POST["email"];
        $password = $_POST["password"];

        if ($email === $admin_email && $password === $admin_password) {
            $_SESSION['admin'] = true;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid admin credentials.";
        }
    } elseif ($_POST["role"] == "user") {
        $_SESSION['admin'] = false;
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Choose Role - Pet Adoption</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #c3e0e5, #a2d2ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .box {
            background-color: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            text-align: center;
            width: 300px;
        }

        input, button {
            width: 90%;
            margin: 10px 0;
            padding: 10px;
            font-size: 14px;
        }

        .admin-fields {
            display: none;
            flex-direction: column;
            align-items: center;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        h2 {
            margin-bottom: 20px;
        }
    </style>
    <script>
    function toggleFields() {
        const role = document.querySelector('input[name="role"]:checked').value;
        const adminFields = document.getElementById("adminFields");
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');

        if (role === "admin") {
            adminFields.style.display = "flex";
            emailInput.required = true;
            passwordInput.required = true;
        } else {
            adminFields.style.display = "none";
            emailInput.required = false;
            passwordInput.required = false;
        }
    }
</script>

</head>
<body>
    <form method="POST" class="box">
        <h2>Select Role</h2>

        <label><input type="radio" name="role" value="admin" onclick="toggleFields()" required> Admin</label><br>
        <label><input type="radio" name="role" value="user" onclick="toggleFields()"> User</label>

        <div id="adminFields" class="admin-fields">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Root Password" required>
        </div>

        <button type="submit">Continue</button>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
    </form>
</body>
</html>
