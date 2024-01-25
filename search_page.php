<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang tìm kiếm</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="./css/main.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Trang tìm kiếm</h3>
   <p> <a href="home.php">Trang chủ</a> / Tìm kiếm </p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="Tìm truyện..." class="box"  value=" <?php if(isset($_POST['submit'])) echo($_POST['search'])?>">
      <input type="submit" name="submit" value="Tìm kiếm" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">

   <div class="box-container">
      <?php
         if(isset($_POST['submit'])){
            $search_item = trim($_POST['search']);
            $select_products = mysqli_query($conn, "SELECT * FROM `books` WHERE name LIKE '%{$search_item}%' OR author LIKE '%{$search_item}%' AND books.quantity > 0") or die('query failed');
            if(mysqli_num_rows($select_products) > 0){
               while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
                  <form action="" method="post" class="box">
                     <img width="180px" height="207px" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                     <div class="name"><?php echo $fetch_products['name']; ?></div>
                     <div class="name"><?php echo $fetch_products['describes']; ?></div>
                     <div class="book-action">
                        <a href="book_detail.php?book_id=<?php echo $fetch_products['id'] ?>" class="view-book" >Xem thông tin sách</a>
                        <a href="book_borrow.php?book_id=<?php echo $fetch_products['id'] ?>" class="borrow-book" >Mượn sách</a>
                     </div>
                  </form>
      <?php
               }
            }else{
               echo '<p class="empty">Không tìm thấy kết quả phù hợp với yêu cầu tìm kiếm cảu bạn!</p>';
            }
         }else{
            echo '<p class="empty"">Hãy nhập tên sách hoặc tên tác giả bạn muốn tìm kiếm!</p>';
         }
      ?>
   </div>
  

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>