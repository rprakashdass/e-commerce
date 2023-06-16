<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}


// CREATE TABLE `tracking` (
//    `user_id` int(100) NOT NULL,
//    `name` varchar(20) NOT NULL,
//    `email` varchar(50) NOT NULL,
//    `method` varchar(50) NOT NULL,
//    `address` varchar(500) NOT NULL,
//    `total_products` varchar(1000) NOT NULL,
//    `total_price` int(100) NOT NULL,
//    `placed_on` date NOT NULL DEFAULT current_timestamp(),
//    `Delivery info` varchar(20) NOT NULL
//  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 



if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $delivery_status = $_POST['Delivery_info'];
   $delivery_status = filter_var($delivery_status, FILTER_SANITIZE_STRING);
   $update_track = $conn->prepare("UPDATE `tracking` SET Delivery_info = ? WHERE id = ?");
   $update_track->execute([$delivery_status, $order_id]);
   $message[] = 'payment status updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_track = $conn->prepare("DELETE FROM `tracking` WHERE id = ?");
   $delete_track->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Arriving Soon...</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="orders">

<h1 class="heading">Arriving Soon</h1>

<div class="box-container">

   <?php
      $select_track = $conn->prepare("SELECT * FROM `tracking`");
      $select_track->execute();
      if($select_track->rowCount() > 0){
         while($fetch_track = $select_track->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> placed on : <span><?= $fetch_track['placed_on']; ?></span> </p>
      <p> name : <span><?= $fetch_track['name']; ?></span> </p>
      <p> number : <span><?= $fetch_track['number']; ?></span> </p>
      <p> address : <span><?= $fetch_track['address']; ?></span> </p>
      <p> total products : <span><?= $fetch_track['total_products']; ?></span> </p>
      <p> total price : <span>₹<?= $fetch_track['total_price']; ?>/-</span> </p>
      <p> Delivery status : <span><?= $fetch_track['Delivery_info']; ?>/-</span> </p>
      <form action="" method="post">
         <input type="hidden" name="order_id" value="<?= $fetch_track['id']; ?>">
         <select name="delivery_status" class="select">
            <option selected disabled><?= $fetch_track['delivery_status']; ?></option>
            <option value="pending">pending</option>
            <option value="completed">completed</option>
         </select>
        <div class="flex-btn">
         <input type="submit" value="update" class="option-btn" name="update_payment">
         <a href="placed_orders.php?delete=<?= $fetch_track['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">delete</a>
        </div>
      </form>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
   ?>

</div>

</section>

</section>












<script src="../js/admin_script.js"></script>
   
</body>
</html>