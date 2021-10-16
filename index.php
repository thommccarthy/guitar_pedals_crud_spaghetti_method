<?php

//new instance of PDO (php data object)
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
//PDO error handling - !!read docs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$search = $_GET['search'] ?? '';
if ($search) {
  //Select all from 'products' table and order by date
  $statement = $pdo->prepare('SELECT * FROM products WHERE title LIKE :title ORDER BY create_date DESC');
  $statement->bindValue(':title', "%$search%");
} else {

  //Select all from 'products' table and order by date
  $statement = $pdo->prepare('SELECT * FROM products ORDER BY create_date DESC');
}



//send the request
$statement->execute();
// fetch data as associative array
$products = $statement->fetchAll(PDO::FETCH_ASSOC);

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
  <h1>Guitar Pedals CRUD</h1>


  <p>
    <a href="create.php" class="btn btn-success">Create Product</a>
  </p>

  <form>
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Search for products" name="search" value="<?php echo $search ?>">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </div>
  </form>

  <!-- table -->
  <table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Image</th>
        <th scope="col">Title</th>
        <th scope="col">Price</th>
        <th scope="col">Create Date</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- loop through data and add each item to the table -->
      <?php foreach ($products as $i => $product) :  ?>
        <tr>
          <th scope="row"><?php echo $i + 1 ?></th>
          <td><img class="thumb-image" src="<?php echo $product['image'] ?>" alt=""></td>
          <td><?php echo $product['title'] ?></td>
          <td><?php echo $product['price'] ?></td>
          <td><?php echo $product['create_date'] ?></td>
          <td>
            <a href="update.php?id=<?php echo $product['id'] ?>" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
            <form method="post" action="delete.php" style="display: inline-block">
              <input type="hidden" name='id' value="<?php echo $product['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>

    </tbody>
  </table>
</body>

</html>