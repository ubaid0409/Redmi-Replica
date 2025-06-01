<!DOCTYPE html>
<html>
<head>
    <title>PHP Form with Alerts</title>
    <style>
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
            width: 400px;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error   { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    </style>
</head>
<body>

<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "user_data";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo '<div class="alert error">Connection failed: ' . $conn->connect_error . '</div>';
    exit();
}

$email = $_POST['email'];
$pass = $_POST['password'];

if (!empty($email) && !empty($pass)) {
    $checkStmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['password'] === $pass) {
            echo '<div class="alert warning">This data is already in the database.</div>';
        } else {
            echo '<div class="alert error">Wrong password for this email.</div>';
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $pass);

        if ($stmt->execute()) {
            echo '<div class="alert success">Data saved successfully!</div>';
        } else {
            echo '<div class="alert error">Error: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    }

    $checkStmt->close();
} else {
    echo '<div class="alert error">Please fill in all fields.</div>';
}

$conn->close();
?>

</body>
</html>
