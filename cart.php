<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['update_cart'])){//cập nhật giỏ hàng từ form submit name='update_cart'
      $cart_id = $_POST['cart_id'];
      $cart_quantity = $_POST['cart_quantity'];
      mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
      $message[] = 'Giỏ đã được cập nhật!';
   }

   if(isset($_GET['delete'])){//xóa sách khỏi giỏ hàng từ onclick href='delete'
      $delete_id = $_GET['delete'];
      mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('query failed');
      header('location:cart.php');
   }

   if(isset($_GET['delete_all'])){//xóa tất cả sách khỏi giỏ hàng của người dùng từ onclick href='delete_all'
      mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      header('location:cart.php');
   }

   if(isset($_POST['submit'])) { // Người dùng click mượn
      // Lấy ra tất cả sách mà người dùng đã thêm vào giỏ
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      $cart_items = mysqli_fetch_all($select_cart, MYSQLI_ASSOC);
      $placed_on = date('d-m-Y');
      $count_cart = mysqli_num_rows($select_cart);
      if(!empty($count_cart)) {

         mysqli_query($conn, "INSERT INTO `borrows` (user_id, placed_on) VALUES ('$user_id', '$placed_on')") or die('query failed');

         // Lấy ra borrow_id vừa tạo
         $borrow_id = mysqli_insert_id($conn);

         foreach ($cart_items as $item) {
            $book_id = $item['book_id'];
            $quantity = $item['quantity'];
               mysqli_query($conn, "INSERT INTO `borrow_book` (quantity, book_id, borrow_id) VALUES ('$quantity', '$book_id', '$borrow_id')") or die('query failed');
         }
         // Xóa các sách khỏi giỏ hàng
         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
         $message[] = 'Mượn sách thành công!';
      } else {
         $message[] = 'Giỏ của bạn trống!';
      }
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Giỏ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .head {
         background: url(./images/home-about.jpg) no-repeat;
         background-size: cover;
         background-position: center;
      }
      .del-btn {
         height: 2.5rem !important;
         width: 2.5rem !important;
         line-height: 2.5rem !important;
      }
      .borrow-btn {
         background-color: #0900ff;
         color: #fff;
      }
      .borrow-btn:hover {
         background-color: #0900ff !important;
         opacity: 0.7;
         color: #fff !important;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="shopping-cart">

   <h1 class="title">Sách đã được thêm vào giỏ</h1>

   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');//lấy ra giỏ hàng tương ứng với id người dùng
         $count_cart = mysqli_num_rows($select_cart);
         if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){ 
               $name_product = $fetch_cart['name'];
               $select_quantity = mysqli_query($conn, "SELECT * FROM `books` WHERE name='$name_product'");
               $fetch_quantity = mysqli_fetch_assoc($select_quantity); 
      ?>
               <div style="height: -webkit-fill-available;" class="box">
                  <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="del-btn fas fa-times" onclick="return confirm('Xóa sách này khỏi giỏ?');"></a>
                  <img width="207px" style="height: 224px !important" src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_cart['name']; ?></div>
                  <form action="" method="post">
                     <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                     <input type="number" min="1" max="<?=$fetch_quantity['quantity']?>" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                     <input type="submit" name="update_cart" value="Cập nhật" class="option-btn">
                  </form>
               </div>
      <?php
            }
         }else{
            echo '<p class="empty">Giỏ của bạn trống!</p>';
         }
      ?>
   </div>

   <div style="margin-top: 2rem; text-align:center;">
      <a href="cart.php?delete_all" class="delete-btn" onclick="return confirm('Xóa tất cả giỏ?');">Xóa tất cả</a>
   </div>

   <form method="post" class="cart-total">
      <div class="flex">
         <a href="home.php" class="option-btn">Tiếp tục chọn sách</a>
         <input type="submit" name="submit" class="btn borrow-btn" value="Mượn">
      </div>
   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>