<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }
   $book_id = $_GET['book_id'];

   $sql = "SELECT * FROM books WHERE id = $book_id";
   $result = $conn->query($sql);
   $bookItem = $result->fetch_assoc()


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Xem thông tin sách</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .view-book {
         padding: 15px;
      }
      .modal{
         width: 500px;
         margin: auto;
         border: 2px solid #eee;
         padding-bottom: 27px;
         box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
         border-radius: 5px;
      }
      .modal-container{
         background-color:#fff;
         text-align: center;
      }
      .bookdetail-title {
         font-size: 21px;
         padding-top: 10px;
         color: #3670EB !important;
      }
      .bookdetail-img {
         margin-top: 18px;
         width: 230px;
      }
      .bookdetail-author {
         margin-top: 19px;
         font-size: 20px;
      }
      .bookdetail-desc {
         margin-top: 20px;
         font-size: 16px;
         margin-bottom: 20px;
      }
      .borrow-book:hover { 
         opacity: 0.9;
      }
      .borrow-book {
         padding: 5px 10px;
         background-image: linear-gradient(to right, #ff9800, #F7695D);
         border-radius: 4px;
         cursor: pointer; 
         font-size: 18px;
         color: #fff;
         font-weight: 700;
      }
      .head {
         background: url(./images/home_bg.jpg) no-repeat;
         background-size: cover;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="view-book">
   <?php if ($bookItem) : ?>
         <!-- Modal View Detail Book -->
      <div class="modal">
         <div class="modal-container">
            <h3 class="bookdetail-title">Xem thông tin sách <?php echo($bookItem['name']) ?></h3>
            <div>
               <img class="bookdetail-img" src="uploaded_img/<?php echo $bookItem['image']; ?>" alt="">
            </div>
            <p class="bookdetail-author">
               Tác giả: 
               <?php echo ($bookItem['author']) ?>
            </p>
            <p class="bookdetail-author">
               Số lượng còn: 
               <?php echo ($bookItem['quantity']) ?> quyển
            </p>
            <p class="bookdetail-desc">
               Mô tả: 
               <?php echo($bookItem['describes'])  ?>
            </p>
            <a href="book_borrow.php?book_id=<?php echo $bookItem['id'] ?>" class="borrow-book" >Mượn sách</a>
         </div>
      </div>
   <?php else : ?>
      <p style="font-size: 20px; text-align: center;">Không xem được chi tiết sách này</p>
   <?php endif; ?>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>