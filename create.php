<?php

//pdo is a php extension to interact with database
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//initialize variables globally
$errors = [];
$title = '';
$price = '';
$description = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //set variables to event target value of user input fields
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    //special php date function - has many formatting options 
    $date = date('Y-m-d H:i:s');



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
        $imagePath = '';
        //if image is not falsy AND has a tmp_name(filesystem creates a temp file), create a path in the images directory with a random string name and move the file into it
        if ($image && $image['tmp_name']) {

            //concatenation in php is wild
            //initialize pathname to random string and image name
            $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
            //create the directory
            mkdir(dirname($imagePath));
            //move file from the temp path to the unique image path
            move_uploaded_file($image['tmp_name'], $imagePath);
        }


        //prepared statement will protect against sql injection
        $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date)
    VALUES (:title, :image, :description, :price, :date)");
        //bind the data
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':date', $date);
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
    <h1>Add Product</h1>
    <!-- if errors is not empty, loop through the errors and display unique error for each missing input item -->
    <?php if (!empty($errors)) : ?>
        <div class="aler alert-danger"><?php foreach ($errors as $error) : ?>
                <div><?php echo $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Image</label><br>
            <input type="file" name="image">
        </div>
        <div class="form-group">
            <label>Product Title</label>
            <!-- variables is defined globally as empty string and value gets updated when post request is made. storing variable name as value of input field make it so that if the form is submitted with an error, the user won't have to type their information in again  -->
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