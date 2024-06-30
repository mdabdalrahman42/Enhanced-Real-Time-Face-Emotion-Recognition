<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "facial_emotions";

echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
echo '<link rel="stylesheet" href="static/css/nav.css"/>';
echo '<link rel="stylesheet" href="static/css/form.css"/>';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) 
{
  die("Connection failed: " . $conn->connect_error);
}

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];
$password = password_hash($_POST['password'],PASSWORD_DEFAULT);
$confirmPassword = $_POST['confirm_password'];

$checkExistingQuery = "SELECT * FROM `user_data` WHERE `Email Id`='$email';";
$resultExisting = $conn->query($checkExistingQuery);

echo "<br>";

if ($resultExisting->num_rows > 0) 
{
  echo "<script>
          Swal.fire({
            title: 'Registration failed',
            text: 'User already exists',
            icon: 'error',
            confirmButtonText: 'OK'
          }).then(function() {
              window.location.href = 'index.html';
          });
        </script>";
}

else 
{
  $insertQuery = "INSERT INTO `user_data` VALUES ('$firstName', '$lastName', '$email', '$phone', '$gender', '$dob', '$password')";

  if ($conn->query($insertQuery) === TRUE) 
  {
    echo "<script>
            Swal.fire({
              title: 'Registration Successful',
              text: 'Redirecting...',
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = 'index.html';
            });
          </script>";
  }
}

$conn->close();
?>