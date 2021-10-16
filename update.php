<?php

//pdo is a php extension to interact with database
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//id now coming from get request
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

//get specific product
$statement = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$statement->bindValue(':id', $id);
$statement->execute();
//fetch product as associative array
$product = $statement->fetch(PDO::FETCH_ASSOC);



$errors = [];

//get variables from associative array that has been fetched above
$title = $product['title'];
$price = $product['price'];
$description = $product['description'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //set variables to event target value of user input fields
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    if (!$title) {
        $errors[] = 'Product title is required';
    }
    if (!$price) {
        $errors[] = 'Product price is required';
    }


    //if there is not a directory named images, one will be created on form submission
    if (!is_dir('images')) {
        mkdir('images');
    }

    //if there are no errors, send the data to the database
    if (empty($errors)) {

        $image = $_FILES['image'] ?? null;
        $imagePath = $product['image'];




        //if image is not falsy AND has a tmp_name(filesystem creates a temp file), create a path in the images directory with a random string name and move the file into it
        if ($image && $image['tmp_name']) {

            if ($product['image']) {
                unlink($product['image']);
            }

            //concatenation in php is wild
            //initialize pathname to random string and image name
            $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
            //create the directory
            mkdir(dirname($imagePath));
            //move file from the temp path to the unique image path
            move_uploaded_file($image['tmp_name'], $imagePath);
        }


        // VERY IMPORTANT FOR UPDATE TO USE WHERE KEYWORD TO PREVENT UPDATING ALL TABLE ITEMS
        $statement = $pdo->prepare(
            "UPDATE products SET title = :title,
         image = :image,
        description = :description, price = :price WHERE id = :id"
        );
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
        //send the data
        $statement->execute();
        //returns user to the index page after form submission
        header('Location: index.php');
    }
}

function randomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }

    return $str;
}




?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="app.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Guitar Pedals CRUD</title>
</head>

<body>

    <p><a href="index.php" class="btn btn-secondary">Go Back to Products</a></p>

    <h1>Edit Product: <b><?php echo $product['title'] ?></b></h1>
    <!-- if errors is not empty, loop through the errors and display unique error for each missing input item -->
    <?php if (!empty($errors)) : ?>
        <div class="aler alert-danger"><?php foreach ($errors as $error) : ?>
                <div><?php echo $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <?php if ($product['image']) : ?>
            <img class="update-image" src="<?php echo $product['image'] ?>" />
        <?php endif; ?>

        <div class="form-group">
            <label>Product Image</label><br>
            <input type="file" name="image">
        </div>
        <div class="form-group">
            <label>Product Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo $title ?>">
        </div>
        <div class="form-group">
            <label>Product Description</label>
            <textarea class="form-control" name="description"><?php echo $description ?></textarea>
        </div>
        <div class="form-group">
            <label>Product Price</label>
            <input type="number" name="price" step="0.01" value="<?php echo $price ?>" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>


</body>

</html>