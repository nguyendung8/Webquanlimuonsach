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
   <title>Sách mới nhất</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="./css/main.css">
   <style>
      .img-new {
        position: absolute;
        left: 10px;
        top: 5px;
      }
      .box {
         border: none !important;
      }
      *::-webkit-scrollbar-thumb{
         background-color: #3670EB !important;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>
<section class="products">

   <h1 class="title">Danh sách sách mới nhất</h1>
   <div class="box-container">
      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM books WHERE quantity > 0 ORDER BY id DESC LIMIT 20") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
         <form action="" method="post" class="box">
            <img class="img-new" width="50px" height="50px" src="./images/new.png" alt="">
            <img width="180px" height="207px" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
            <div class="name"><?php echo $fetch_products['name']; ?></div>
            <div class="book-action">
               <a href="book_detail.php?book_id=<?php echo $fetch_products['id'] ?>" class="view-book" >Xem thông tin sách</a>
               <a href="book_borrow.php?book_id=<?php echo $fetch_products['id'] ?>" class="borrow-book" >Mượn sách</a>
            </div>
         </form>
      <?php
            }
         }else{
            echo '<p class="empty">Chưa có sách để cho mượn!</p>';
         }
      ?>
   </div>

</section>

<!-- <section class="home-contact">

   <div class="content">
      <h3>Bạn có thắc mắc?</h3>
      <p>Hãy để lại những điều bạn còn thắc mắc, băn khoăn hay muốn chia sẻ thêm về những quyển sách cho chúng mình tại đây để chúng mình có thể giải đáp giúp bạn</p>
      <a href="contact.php" class="white-btn">Liên hệ</a>
   </div>

</section> -->

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
<script src="./js/slide_show.js" ></script>

</body>
</html>