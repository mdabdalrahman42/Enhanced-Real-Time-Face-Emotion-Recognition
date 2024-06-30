<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$database = "facial_emotions";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$emotion = $_GET['emotion'];

$emotions = ['Happy', 'Neutral', 'Surprise', 'Disgust', 'Angry', 'Sad',  'Fear'];

$emojis = ['<svg class="emo" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-emoji-smile-fill" viewBox="0 0 16 16">
<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8"/>
</svg>', '<svg class="emo" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-emoji-neutral-fill" viewBox="0 0 16 16">
<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m-3 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8"/>
</svg>', '<svg class="emo" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-emoji-surprise-fill" viewBox="0 0 16 16">
<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M7 5.5C7 4.672 6.552 4 6 4s-1 .672-1 1.5S5.448 7 6 7s1-.672 1-1.5m4 0c0-.828-.448-1.5-1-1.5s-1 .672-1 1.5S9.448 7 10 7s1-.672 1-1.5M8 13a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
</svg>', '<svg class="emo" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-emoji-astonished-fill" viewBox="0 0 16 16">
<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.884-3.978a2.1 2.1 0 0 1 .53.332.5.5 0 0 0 .708-.708h-.001v-.001a2 2 0 0 0-.237-.197 3 3 0 0 0-.606-.345 3 3 0 0 0-2.168-.077.5.5 0 1 0 .316.948 2 2 0 0 1 1.458.048m-4.774-.048a.5.5 0 0 0 .316-.948 3 3 0 0 0-2.167.077 3.1 3.1 0 0 0-.773.478q-.036.03-.07.064l-.002.001a.5.5 0 1 0 .728.689 2 2 0 0 1 .51-.313 2 2 0 0 1 1.458-.048M7 6.5C7 5.672 6.552 5 6 5s-1 .672-1 1.5S5.448 8 6 8s1-.672 1-1.5m4 0c0-.828-.448-1.5-1-1.5s-1 .672-1 1.5S9.448 8 10 8s1-.672 1-1.5m-5.247 4.746c-.383.478.08 1.06.687.98q1.56-.202 3.12 0c.606.08 1.07-.502.687-.98C9.747 10.623 8.998 10 8 10s-1.747.623-2.247 1.246"/>
</svg>', '<svg class="emo" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-emoji-angry-fill" viewBox="0 0 16 16">
<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M4.053 4.276a.5.5 0 0 1 .67-.223l2 1a.5.5 0 0 1 .166.76c.071.206.111.44.111.687C7 7.328 6.552 8 6 8s-1-.672-1-1.5c0-.408.109-.778.285-1.049l-1.009-.504a.5.5 0 0 1-.223-.67zm.232 8.157a.5.5 0 0 1-.183-.683A4.5 4.5 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 1 1-.866.5A3.5 3.5 0 0 0 8 10.5a3.5 3.5 0 0 0-3.032 1.75.5.5 0 0 1-.683.183M10 8c-.552 0-1-.672-1-1.5 0-.247.04-.48.11-.686a.502.502 0 0 1 .166-.761l2-1a.5.5 0 1 1 .448.894l-1.009.504c.176.27.285.64.285 1.049 0 .828-.448 1.5-1 1.5"/>
</svg>', '<svg class="emo" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-emoji-frown-fill" viewBox="0 0 16 16">
<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m-2.715 5.933a.5.5 0 0 1-.183-.683A4.5 4.5 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 0 1-.866.5A3.5 3.5 0 0 0 8 10.5a3.5 3.5 0 0 0-3.032 1.75.5.5 0 0 1-.683.183M10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8"/>
</svg>', '<svg class="emo" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-emoji-tear-fill" viewBox="0 0 16 16">
<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M9.5 3.5a.5.5 0 0 0 .5.5c.838 0 1.65.416 2.053 1.224a.5.5 0 1 0 .894-.448C12.351 3.584 11.162 3 10 3a.5.5 0 0 0-.5.5M7 6.5C7 5.672 6.552 5 6 5s-1 .672-1 1.5S5.448 8 6 8s1-.672 1-1.5M4.5 13c.828 0 1.5-.746 1.5-1.667 0-.706-.882-2.29-1.294-2.99a.238.238 0 0 0-.412 0C3.882 9.044 3 10.628 3 11.334 3 12.253 3.672 13 4.5 13M8 11.197c.916 0 1.607.408 2.25.826.212.138.424-.069.282-.277-.564-.83-1.558-2.049-2.532-2.049-.53 0-1.066.361-1.536.824q.126.27.232.535.069.174.135.373A3.1 3.1 0 0 1 8 11.197M10 8c.552 0 1-.672 1-1.5S10.552 5 10 5s-1 .672-1 1.5S9.448 8 10 8M6.5 3c-1.162 0-2.35.584-2.947 1.776a.5.5 0 1 0 .894.448C4.851 4.416 5.662 4 6.5 4a.5.5 0 0 0 0-1"/>
</svg>'];

for ($i = 0; $i < 7; $i++)
{
    if ($emotion ==  $emotions[$i]) {
        $emoji = $emojis[$i];
    }
}

$sql = "SELECT * FROM user_data WHERE `Email Id` = '$id'";

$result = $conn->query($sql);

$user = $result->fetch_assoc();

$a = "";
$b = "";
$c = "";

if ($user["Gender"] == "Male") {
    $a = "checked";
}

else if ($user["Gender"] == "Female") {
    $b = "checked";
}

else {
    $c = "checked";
}


echo '

<html>

<head>

    <title>Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <link rel="stylesheet" href="static/css/nav.css">

    <link rel="stylesheet" href="static/css/products.css">

    <link rel="stylesheet" href="static/css/form.css">

    <style>

        .navbar-nav .prof {
            margin-left: 20px !important;
        }

        .navbar-nav .emotion {
            margin-left: 34px !important;
        }

        .start {
            margin-left: 25px;
        }

        @media (max-width: 991.98px) {
            .start {
                margin-left: 0px;
            }
        }

        @media screen and (max-width: 991.98px) {
            .navbar-nav .prof {
                margin-left: 0px !important;
            }
        
            .navbar-nav .emotion {
                margin-left: 0px !important;
            }
        }
        @media screen and (max-width: 991.98px) {
            .navbar-nav .prof {
                margin-top: 10px !important;
                margin-bottom: 10px !important;
            }
            .navbar-nav .emotion {
                margin-top: 10px !important;
                margin-bottom: 10px !important;
            }
        }
        

    </style>

    <script>
        function validateForm() {
            document.getElementById("error_message").innerHTML = "";
            var firstName = document.getElementById("first_name").value;
            var lastName = document.getElementById("last_name").value;
            var email = document.getElementById("email").value;
            var phone = document.getElementById("phone").value;
            var gender = document.querySelector("input[name='."'gender'".']:checked");
            var dob = document.getElementById("dob").value;
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var nameReg = /^[A-Za-z][A-Za-z ]*[A-Za-z]$/;
            if (!nameReg.test(firstName)) {
                document.getElementById("error_message").innerHTML = "First Name should be of correct format!!!";
                return false;
            }
            if (!nameReg.test(lastName)) {
                document.getElementById("error_message").innerHTML = "Last Name should be of correct format!!!";
                return false;
            }
            var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailReg.test(email)) {
                document.getElementById("error_message").innerHTML = "Email Address should be of correct format!!!";
                return false;
            }
            if (phone.length != 10) {
                document.getElementById("error_message").innerHTML = "Phone Number should be of length 10!!!";
                return false;
            }
            if (dob === "") {
                document.getElementById("error_message").innerHTML = "Please select a Birth Date!!!";
                return false;
            }
            if (!gender) {
                document.getElementById("error_message").innerHTML = "Please select a gender!!!";
                return false;
            }
            if (password === "") {
                document.getElementById("error_message").innerHTML = "Please enter a password!!!";
                return false;
            }
            if (password.length < 8 || password.length > 12) {
                document.getElementById("error_message").innerHTML = "Password should be of length 8 to 12!!!";
                return false;
            }
            if (confirmPassword === "") {
                document.getElementById("error_message").innerHTML = "Please confirm your password!!!";
                return false;
            }
            if (password !== confirmPassword) {
                document.getElementById("error_message").innerHTML = "Password and confirm password do not match";
                return false;
            }
            return true;
        }
    </script>

</head>

<body>

    <div class="container-fluid">

        <div class="row mb-5">

            <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
                <div class="container-fluid">
                    <img class="logo" src="static/images/logo.png" alt="logo">
                    <a class="navbar-brand" href="#"><i>Emokart</i></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <form class="d-flex search" role="search">
                        <input class="search-box me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn" type="submit"><svg class="symbol" xmlns="http://www.w3.org/2000/svg"
                                height="24" fill="white" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg></button>
                    </form>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mb-2 mb-lg-0 start">
                            <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="products.php?id='. $id .'&emotion='. $emotion .'">Products</a>

                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="cart.php?id='. $id .'&emotion='. $emotion .'">Container<svg
                                        class="cart" xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        fill="white" class="bi bi-cart-check-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708" />
                                    </svg></a>
                            </li>
                            <li class="nav-item current prof" style="margin-left: 5px;">
                                <a class="nav-link text-primary" aria-current="page" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&emotion=' . $emotion . '">Profile<svg
                                        class="profile" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="rgb(13, 110, 253)" class="bi bi-person-circle" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                        <path fill-rule="evenodd"
                                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                    </svg></a>
                            </li>
                            <li class="nav-item current emotion" style="margin-left: 15px;">
                                <a class="nav-link text-primary" aria-current="page" href="emotion.php?id='. $id .'&emotion='. $emotion .'">'. $emotion . $emoji .'</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="index.html">Log Out</a>
                            </li>
                        </ul>

                    </div>
                </div>
            </nav>

        </div>

        <div class="container-fluid mb-5 mt-3" style="min-height: 82vh;">
            <div class="row justify-content-center align-items-center" style="min-height: 82vh;">
                <div class="col-xl-3 col-lg-2 col-md-2 col-sm-1">

                </div>
                <div class="col-xl-6 col-lg-8 col-md-8 col-sm-10 col">
                    <div class="container">
                        <div class="row">
                            <div class="col text-center">
                                <header>Profile Update</header>
                            </div>
                        </div>
                        <form onsubmit="return validateForm()" method="POST" action="profile_update.php?emotion='. $emotion .'" class="form">
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="input-box">
                                        <label>First Name<span class="req"> *</span></label>
                                        <input type="text" id="first_name" value="'. $user["First Name"] .'"
                                            name="first_name" title="Eg: '."'John'".', '."'Shah Rukh'".'" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-box">
                                        <label>Last Name<span class="req"> *</span></label>
                                        <input type="text"  value="'. $user["Last Name"] .'" id="last_name" name="last_name"
                                            title="Eg: '."'John'".', '."'Shah Rukh'".'" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-box">
                                        <label>Email Id<span class="req"> *</span></label>
                                        <input type="text" value="'. $user["Email Id"] .'" id="email" name="email"
                                            title="Eg: '."'user@gmail.com'".', '."'user@yahoo.com'".'" readonly style="background-color: #e6efff; color: #333;"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                    <div class="input-box">
                                        <label>Phone Number<span class="req"> *</span></label>
                                        <input type="text" value="'. $user["Phone"] .'" id="phone" name="phone"
                                            title="Should be of length 10" />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-box">
                                        <label>Birth Date<span class="req"> *</span></label>
                                        <input class="form-select" value="'. $user["Dob"] .'" type="date" id="dob" name="dob" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label class="gender">Gender<span class="req"> *</span></label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-3 col-12">
                                    <input type="radio" id="male" name="gender" value="Male" '. $a .'/>
                                    <label for="check-male">Male</label>
                                </div>
                                <div class="col-sm-3 col-12">
                                    <input type="radio" id="female" name="gender" value="Female" '. $b .'/>
                                    <label for="check-female">Female</label>
                                </div>
                                <div class="col-sm-3 col-12">
                                    <input type="radio" id="others" name="gender" value="Others" '. $c .'/>
                                    <label for="check-other">Others</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                    <div class="input-box">
                                        <label>Change Password<span class="req"> *</span></label>
                                        <input type="password" placeholder="Enter New Password" id="password"
                                            name="password" title="Length of 8 to 12 characters"/>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-box">
                                        <label>Confirm Password<span class="req"> *</span></label>
                                        <input type="password" placeholder="Re-enter New Password" id="confirm_password"
                                            name="confirm_password" title="Should match the password" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col text-center">
                                    <div id="error_message" class="error text-danger"></div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-center">
                                    <button type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-2 col-md-2 col-sm-1">

                </div>
            </div>


        </div>

</body>

</html>';

$conn->close();

?>