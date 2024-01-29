<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }
   $book_id = $_GET['book_id'];

   // Lấy ra thông tin sách
   $sql = "SELECT * FROM books WHERE id = $book_id";
   $result = $conn->query($sql);
   $bookItem = $result->fetch_assoc();

   // Lấy ra thông tin user
   $sql1 = "SELECT * FROM users WHERE id = $user_id";
   $result1 = $conn->query($sql1);
   $user = $result1->fetch_assoc();

   // Lúc click vào nút mượn
   if(isset($_POST['submit'])) {
      $userName = $user['name'];
      $userId = $user_id;
      $book_name = $bookItem['name'];
      $book_img = $bookItem['image'];
      $book_quantity = $_POST['quantity'];
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $phone = mysqli_real_escape_string($conn, $_POST['phone']);
      $placed_on = date('d-m-Y');

      mysqli_query($conn, "INSERT INTO `borrows`(user_id, book_id, book_name, borrow_quantity, book_img, user_name, email, phone, placed_on) VALUES('$userId', '$book_id', '$book_name','$book_quantity', '$book_img', '$userName', '$email', '$phone', '$placed_on')") or die('query failed');
      $message[] = 'Mượn sách thành công!';
      // header('location:home.php');
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mượn sách</title>

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
         width: 180px;
      }
      .bookdetail-author {
         margin-top: 19px;
         font-size: 20px;
      }
      .bookdetail-desc {
         margin-top: 20px;
         font-size: 16px;
      }
      .form-item {
         margin-bottom: 5px;
      }
      .form-item label {
         font-size: 16px;
      }
      .form-item input {
         font-size: 16px;
         border: 1px solid #999191 !important;
         padding: 7px 18px;
         margin-top: 4px;
         width: 225px;
         border-radius: 3px;
      }
      .borrow-input {
         margin-top: 10px;
      }
      .borrow-btn {
         margin-top: 21px;
         padding: 8px;
         border-radius: 4px;
         background: #1ed224;
         color: #fff;
         font-size: 20px;
         cursor: pointer;
      }
      .borrow-btn:hover {
         opacity: 0.8;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>


<section class="view-book">
   <?php if ($bookItem) : ?>
         <!-- Modal View Detail Book -->
      <form class="modal" method="post">
         <div class="modal-container">
            <h3 class="bookdetail-title">Mượn sách <?php echo($bookItem['name']) ?></h3>
            <div>
               <img class="bookdetail-img" src="uploaded_img/<?php echo $bookItem['image']; ?>" alt="">
            </div>
            <p class="bookdetail-author">
               Tác giả: 
               <?php echo ($bookItem['author']) ?>
            </p>
            <p class="bookdetail-desc">
               Mô tả: 
               <?php echo($bookItem['describes'])  ?>
            </p>
               <div class="borrow-input">
                  <div class="form-item">
                     <label for="">Số lượng mượn: </label>
                     <input style="" type="number" max="<?php echo $bookItem['quantity']; ?>" min="<?=($bookItem['quantity']>0) ? 1:0 ?>" name="quantity" placeholder="Nhập số lượng mượn" required>
                  </div>
               </div>
               <div style="margin-left: 74px;" class="form-item">
                  <label for="">Email: </label>
                  <input type="email" name="email" id="" placeholder="Nhập email" required>
               </div>
               <div  style="margin-left: 17px;" class="form-item">
                  <label for="">Số điện thoại: </label>
                  <input type="text" min="10" max="10" name="phone" id="" placeholder="Nhập số điện thoại" required>
               </div>
            <input class="borrow-btn" name="submit" type="submit" value="Mượn sách">
         </div>
      </form>
   <?php else : ?>
      <p style="font-size: 20px; text-align: center;">Không mượn được sách này</p>
   <?php endif; ?>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>