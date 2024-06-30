<?php
$servername = "localhost";
$username = "root";
$password = "12345678";

$conn = new mysqli($servername, $username, $password);

mysqli_options($conn, MYSQLI_OPT_LOCAL_INFILE, true);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

$query = "SHOW DATABASES LIKE 'facial_emotions'";
$result = $conn->query($query);

if ($result->num_rows == 0) 
{
    $createDBQuery = "CREATE DATABASE facial_emotions";
    $conn->query($createDBQuery);
    $conn->select_db("facial_emotions");
} 

else 
{
    $conn->select_db("facial_emotions");
    
    $checkUserDataQuery = "SHOW TABLES LIKE 'user_data'";
    $resultUserData = $conn->query($checkUserDataQuery);

    $checkProductDataQuery = "SHOW TABLES LIKE 'product_data'";
    $resultProductData = $conn->query($checkProductDataQuery);
    
    if ($resultUserData->num_rows == 1 ) 
    {
        echo "user_data table already exists<br>";
    } 
    
    else 
    {
        create1($conn);
    }

    if ($resultProductData->num_rows == 1 ) 
    {
        echo "product_data table already exists<br>";

        $selectQuery = "SELECT * FROM product_data";
        $ProductData = $conn->query($selectQuery);

        if ($ProductData->num_rows >= 1) {
            
            $deleteQuery = "DELETE FROM product_data";
            $conn->query($deleteQuery);
        
        }

        data($conn);
    } 
    
    else 
    {
        create2($conn);
    }
}

function create1(mysqli $conn) 
{
    $createUserDataQuery = "CREATE TABLE `user_data` (
        `First Name` VARCHAR(50) NOT NULL,
        `Last Name` VARCHAR(50) NOT NULL,
        `Email Id` VARCHAR(100) NOT NULL PRIMARY KEY,
        `Phone` VARCHAR(20) NOT NULL,
        `Gender` VARCHAR(10) NOT NULL,
        `Dob` DATE NOT NULL,
        `Password` VARCHAR(255) NOT NULL
    )";
    $conn->query($createUserDataQuery);
    echo "user_data table created successfully<br>";
}

function create2(mysqli $conn) 
{
    $createProductDataQuery = "CREATE TABLE `product_data` (
        `Product Id` INTEGER NOT NULL,
        `Product Name` VARCHAR(100) NOT NULL,
        `Color` VARCHAR(100) NOT NULL,
        `Material` VARCHAR(100) NOT NULL,
        `Net Quantity` VARCHAR(100) NOT NULL,
        `Brand Name` VARCHAR(100) NOT NULL,
        `Emotion` VARCHAR(20) NOT NULL,
        `Price` VARCHAR(100) NOT NULL,
        /*`Gender` VARCHAR(20) NOT NULL,*/
        /*`Age Group` VARCHAR(20) NOT NULL,*/
        `Description` VARCHAR(1)
    )";
    
    $conn->query($createProductDataQuery);
    echo "product_data table created successfully<br>";

    data($conn);
}

function data(mysqli $conn) 
{
    $csv_file = 'static/data/Products.csv';
    
    $load_data_query = "LOAD DATA LOCAL INFILE '$csv_file'
                        INTO TABLE product_data
                        FIELDS TERMINATED BY ','
                        ENCLOSED BY '\"'
                        LINES TERMINATED BY '\n'
                        IGNORE 1 LINES";
    
    if ($conn->query($load_data_query) === TRUE) 
    {
        echo "Products data imported successfully<br>";
    } 
    
    else 
    {
        echo "Error importing data: " . $conn->error;
    }
}

$conn->close();
?>