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

$id = $_GET['id'];
$emotion = $_GET['emotion'];

echo "<br>";

$python_output = exec("python realtime.py");

$output_array = explode("\n", $python_output);

$output = $output_array[0];

if ($output != "Error") 
{
     echo "<script>
                Swal.fire({
                    title: 'Face Emotion Capture Successful',
                    text: 'Redirecting...',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    var redirectUrl = 'products.php?id=$id&emotion=$output';
                    window.location.href = redirectUrl;
                });
            </script>";
}

else
{
     echo "<script>
                Swal.fire({
                    title: 'Face Emotion Capture Failed',
                    text: 'Redirecting...',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(function() {
                    var redirectUrl = 'products.php?id=$id&emotion=$emotion';
                    window.location.href = redirectUrl;
                });
            </script>";
}

$conn->close();
?>