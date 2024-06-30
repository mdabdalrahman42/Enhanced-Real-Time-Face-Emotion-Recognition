<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "facial_emotions";

echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
echo '<link rel="stylesheet" href="static/css/nav.css"/>';
echo '<link rel="stylesheet" href="static/css/form.css"/>';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$emotion = $_GET['emotion'];

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$confirmPassword = $_POST['confirm_password'];

$sql = "UPDATE user_data SET 
            `First Name` = '$firstName', 
            `Last Name` = '$lastName', 
            `Phone` = '$phone', 
            `Gender` = '$gender', 
            `dob` = '$dob', 
            `Password` = '$password' 
        WHERE `Email Id` = '$email'";

echo "<br>";

if ($conn->query($sql) === TRUE) {
    echo "<script>
            Swal.fire({
            title: 'Update Successful',
            text: 'Redirecting...',
            icon: 'success',
            confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = 'products.php?id=$email&emotion=$emotion';
            });
        </script>";

} else {
    echo "<script>
            Swal.fire({
            title: 'Update failed',
            text: 'User already exists',
            icon: 'error',
            confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = 'products.php?id=$email&emotion=$emotion';
            });
        </script>";
}

$conn->close();
?>