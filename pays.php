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
   <title>Thanh toán</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .contact form {
         border: none !important;
         box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
      }
      .contact .box {
         border: 1px solid #3670EB !important;
      }
      h3 {
         color: #3670EB !important;
      }
      .btn {
         background-color: #3670EB;
      }
      *::-webkit-scrollbar-thumb{
         background-color: #3670EB !important;
      }
      select option {
         padding: 4px 0;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section style="height: 354px;" class="contact">

   <form action="" method="post">
      <h3>Thanh toán</h3>
      <select style="padding: 13px 85px; font-size: 16px;" name="" id="">
         <option value="">Chọn phương thức thanh toán</option>
         <option value="">Momo</option>
         <option value="">QR Code</option>
         <option value="">Tiền mặt</option>
      </select>
   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>