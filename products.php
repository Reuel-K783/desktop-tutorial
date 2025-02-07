<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pencil"; // Update this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (name, description, price, image, stock) 
                VALUES ('$name', '$description', '$price', '$image', '$stock')";

        if ($conn->query($sql) === TRUE) {
            echo "New product added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$conn->close();
?>

<form action="products.php" method="post" enctype="multipart/form-data">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name"><br>
    <label for="description">Description:</label><br>
    <textarea id="description" name="description"></textarea><br>
    <label for="price">Price:</label><br>
    <input type="text" id="price" name="price"><br>
    <label for="stock">Stock:</label><br>
    <input type="text" id="stock" name="stock"><br>
    <label for="image">Image:</label><br>
    <input type="file" id="image" name="image"><br><br>
    <input type="submit" value="Add Product">
</form>
