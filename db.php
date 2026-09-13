<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name   = "dbquery";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

$sql_db = "CREATE DATABASE IF NOT EXISTS $db_name";
if (!$conn->query($sql_db)) {
    die("Gagal buat database: " . $conn->error);
}

$conn->select_db($db_name);

$tables = [
    "users" => "CREATE TABLE IF NOT EXISTS users (
        userID INT(10) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(20) NOT NULL UNIQUE,
        email VARCHAR(50) NOT NULL UNIQUE, 
        password VARCHAR(255) NOT NULL,
        gender VARCHAR(6) NOT NULL,
        dob DATE NOT NULL,
        role VARCHAR(6) NOT NULL
    )",
    
    "vendors" => "CREATE TABLE IF NOT EXISTS vendors (
        vendorID INT(10) AUTO_INCREMENT PRIMARY KEY,
        vendorName VARCHAR(20) NOT NULL,
        location VARCHAR(100) NOT NULL
    )",

    "products" => "CREATE TABLE IF NOT EXISTS products (
        productID INT(10) AUTO_INCREMENT PRIMARY KEY,
        productName VARCHAR(30) NOT NULL,
        description VARCHAR(255) NOT NULL,
        price FLOAT NOT NULL,
        image VARCHAR(100) NOT NULL,
        vendorID INT(10) NOT NULL,
        FOREIGN KEY (vendorID) REFERENCES vendors(vendorID) ON DELETE RESTRICT
    )",

    "transactions" => "CREATE TABLE IF NOT EXISTS transactions (
        transactionID INT(10) AUTO_INCREMENT PRIMARY KEY,
        userID INT(10) NOT NULL,
        totalPrice FLOAT NOT NULL,
        transactionDate DATE NOT NULL,
        FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
    )",

    "transaction_details" => "CREATE TABLE IF NOT EXISTS transaction_details (
        detailID INT(10) AUTO_INCREMENT PRIMARY KEY,
        transactionID INT(10) NOT NULL,
        productID INT(10) NOT NULL,
        quantity INT(5) NOT NULL,
        subtotal INT(10) NOT NULL,
        FOREIGN KEY (transactionID) REFERENCES transactions(transactionID) ON DELETE CASCADE,
        FOREIGN KEY (productID) REFERENCES products(productID) ON DELETE CASCADE
    )",

    "cart" => "CREATE TABLE IF NOT EXISTS cart (
        cartID INT(10) AUTO_INCREMENT PRIMARY KEY,
        userID INT(10) NOT NULL,
        productID INT(10) NOT NULL,
        quantity INT(5) NOT NULL,
        FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE,
        FOREIGN KEY (productID) REFERENCES products(productID) ON DELETE CASCADE
    )"
];

foreach ($tables as $name => $query) {
    if (!$conn->query($query)) {
        die("Gagal buat tabel $name: " . $conn->error);
    }
}


$checkAdmin = $conn->query("SELECT * FROM users WHERE role = 'Admin' LIMIT 1");
if ($checkAdmin->num_rows == 0) {
    $passwordAdmin = password_hash('admin123', PASSWORD_DEFAULT);

    $sqlAdmin = "INSERT IGNORE INTO users (userID, username, email, password, gender, dob, role) 
                 VALUES (1, 'Admin01', 'admin@gmail.com', '$passwordAdmin', 'Male', '2000-01-01', 'Admin')";
    $conn->query($sqlAdmin);
}

?>