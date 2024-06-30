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

$id = $_POST['id'];
$password = $_POST['password'];

$query = "SELECT * FROM `user_data` WHERE `Email Id` = '$id'";
$result = $conn->query($query);

echo "<br>";

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $storedPassword = $row['Password'];

    echo "<br>";

    if (password_verify($password, $storedPassword)) {

        set_time_limit(600);
        
        $python_output = exec("python realtime.py");

        $output_array = explode("\n", $python_output);

        $output = $output_array[0];

        if ($output != "Error") 
        {
            echo "<script>
                Swal.fire({
                    title: 'Login Successful',
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
                        title: 'Login Failed',
                        text: 'Face Emotion Capture Error',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        var redirectUrl = 'index.html';
                        window.location.href = redirectUrl;
                    });
                </script>";

        }
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Login Failed',
                    text: 'Invalid Email Id or Password',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = 'index.html';
                });
             </script>";
    }
} else {
    echo "<script>
            Swal.fire({
                title: 'Login Failed',
                text: 'Invalid ID or Password',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = 'index.html';
            });
         </script>";
}

$conn->close();
?>