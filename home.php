<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_GET['book_id'])) {
      $book_id = $_GET['book_id'];
   
      // Đếm số lượng sách hiện tại của user trong giỏ
      $count_cart = mysqli_query($conn, "SELECT COUNT(*) as total_books FROM `cart` WHERE user_id = '$user_id'");
      $row = mysqli_fetch_assoc($count_cart);
      $total_books = $row['total_books'];
   
      // Kiểm tra nếu đã đạt giới hạn 5 sách
      if($total_books >= 5) {
         $message[] = 'Bạn chỉ được mượn tối đa 5 sách!';
      } else {
         // Lấy thông tin sách từ bảng `books`
         $select_book = mysqli_query($conn, "SELECT * FROM `books` WHERE id = '$book_id' AND quantity > 0");
         if(mysqli_num_rows($select_book) > 0) {
            $book = mysqli_fetch_assoc($select_book);
            $book_name = $book['name'];
            $book_image = $book['image'];
   
            // Kiểm tra sách đã tồn tại trong giỏ của user chưa
            $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE book_id = '$book_id' AND user_id = '$user_id'");
            if(mysqli_num_rows($check_cart) > 0) {
               $message[] = 'Sách này đã có trong giỏ của bạn!';
            } else {
               // Thêm sách vào bảng `cart`
               mysqli_query($conn, "INSERT INTO `cart` (book_id, user_id, name, quantity, image) 
                                    VALUES ('$book_id', '$user_id', '$book_name', '1', '$book_image')") or die('query failed');
               $message[] = 'Sách đã được thêm vào giỏ của bạn!';
            }
         } else {
            $message[] = 'Mượn sách thất bại Sách này không tồn tại hoặc đã hết số lượng!!';
         }
      }
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang chủ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="./css/main.css">
   <style>
      .list-cate {
         text-align: center;
         font-size: 20px;
         display: flex;
         gap: 5px;
         justify-content: center;
         border: 1px solid #3670EB;
         align-items: center;
         padding: 12px 16px;
         width: fit-content;
         border-radius: 4px;
         margin: auto;
         margin-bottom: 20px;
         box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 10px 0px;
      }
      .slideshow-container {
         position: relative;
         max-width: 800px;
         margin: 0 auto;
         overflow: hidden; /* Để ẩn phần ngoài khung hình ảnh */
      }
      .slide {
         display: none;
         animation: fade 2s ease-in-out infinite; /* Sử dụng animation để thêm hiệu ứng lướt sang */
      }
      @keyframes fade {
         0%, 100% {
            opacity: 0;
         }
         25%, 75% {
            opacity: 1;
         }
      }
      .slide img {
         width: 100%;
         height: 485px;
         border-radius: 9px;
      }
      .home{
         min-height: 70vh;
         background:linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url(./images/home_bg.jpg) no-repeat;
         background-size: cover;
         background-position: center;
         display: flex;
         align-items: center;
         justify-content: center;
      }
      .box {
         border: none !important;
      }
      .cate_item {
         color: #3670EB !important;
         border-right: 1px solid;
         padding-right: 5px;
      }
      .cate_item:last-child {
         border-right: none;
      }
      *::-webkit-scrollbar-thumb{
         background-color: #3670EB !important;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="home">


</section>

<section class="products">

   <h1 class="title">Danh sách sách cho mượn</h1>
   <div class="list-cate">
      <?php  
         $select_categoriess = mysqli_query($conn, "SELECT * FROM `categories`") or die('query failed');
         if(mysqli_num_rows($select_categoriess) > 0){
            while($fetch_categoriess = mysqli_fetch_assoc($select_categoriess)){
      ?>
                  <a class="cate_item" href="?cate_id=<?php echo $fetch_categoriess['id']; ?> "><?php echo $fetch_categoriess['cate_name']; ?></a>
      <?php
            }
         }else{
            echo '<p class="empty">Chưa có danh mục nào!</p>';
         }
      ?>
   </div>
   <div class="box-container">
      <?php
      if(isset($_GET['cate_id'])) {
         $cate_id = $_GET['cate_id'];
      } else {
         $cate_id = 11;
      }
         $select_products = mysqli_query($conn, "SELECT b.* FROM books b JOIN categories c ON b.cate_id = c.id  WHERE cate_id = $cate_id AND b.quantity > 0") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
         <form style="height: -webkit-fill-available;" action="" method="post" class="box">
            <img width="180px" height="207px" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
            <div class="name"><?php echo $fetch_products['name']; ?></div>
            <div class="book-action">
               <a href="book_detail.php?book_id=<?php echo $fetch_products['id'] ?>" class="view-book" >Xem thông tin sách</a>
               <a href="home.php?book_id=<?php echo $fetch_products['id'] ?>" class="borrow-book" >Thêm vào giỏ</a>
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

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
<script src="./js/slide_show.js" ></script>

</body>
</html>