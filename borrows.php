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
   <title>Danh sách phiếu mượn</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .borrow-container {
         display: flex;
         gap: 10px;
         flex-wrap: wrap;
      }
      .borrow-box {
         font-size: 19px;
         border: 2px solid #eee;
         border-radius: 4px;
         padding: 12px;
         box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
      }
      .borrow-box p {
         padding: 4px 0;
      }
      *::-webkit-scrollbar-thumb{
         background-color: #3670EB !important;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Danh sách phiếu mượn của bạn</h1>

   <div class="borrow-container">

      <?php
         $order_query = mysqli_query($conn, "SELECT * FROM `borrows` WHERE user_id = '$user_id'") or die('query failed');
         if(mysqli_num_rows($order_query) > 0){
            while($fetch_borrows = mysqli_fetch_assoc($order_query)){
      ?>
      <div class="borrow-box">
         <p> Tên : <span><?php echo $fetch_borrows['user_name']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_borrows['email']; ?></span> </p>
         <p> Số điện thoại : <span><?php echo $fetch_borrows['phone']; ?></span> </p>
         <p> Tên sách : <span><?php echo $fetch_borrows['book_name']; ?></span> </p>
         <img width="180px" height="207px" src="uploaded_img/<?php echo $fetch_borrows['book_img']; ?>" alt="">
         <p> Số lượng mượn : <span><?php echo $fetch_borrows['borrow_quantity']; ?> quyển</span> </p>
         <p> Trạng thái  : 
            <span style="color:<?php if($fetch_borrows['is_confirmed'] == 1){ echo 'green'; }else if($fetch_borrows['is_confirmed'] == '0'){ echo 'red'; }else{ echo 'orange'; } ?>;">
               <?php if ($fetch_borrows['is_confirmed'] == 1) {
                     echo 'Đã duyệt';
                  } else {
                     echo 'Chờ xử lý';
                  }
               ?>
            </span> 
         </p>
         </div>
      <?php
       }
      }else{
         echo '<p class="empty">Chưa có đơn hàng được đặt!</p>';
      }
      ?>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>