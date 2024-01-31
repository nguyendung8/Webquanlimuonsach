<?php

   include 'config.php';
   session_start();

   if(isset($user_id)){// session tồn tại => quay lại trang người dùng
      $user_id = $_SESSION['user_id']; //tạo session người dùng thường
      header('location:home.php');
   } else if(isset($admin_id)){// session tồn tại => quay lại trang quản lý
      $admin_id = $_SESSION['admin_id']; //tạo session admin
      header('location:admin_products.php');
   }

   if(isset($_POST['submit'])){//lấy thông tin đăng nhập từ form submit name='submit'

      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

      $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

      if(mysqli_num_rows($select_users) > 0){//kiểm tra tài khoản có tồn tại không

         $row = mysqli_fetch_assoc($select_users);
         $user_id = $row['id'];
         //kiểm tra quyền của tài khoản và đưa đến trang tương ứng
         if($row['user_type'] == 'admin'){

            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_id'] = $row['id'];
            header('location:admin_products.php');

         }elseif($row['user_type'] == 'user'){

            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];
            // Cập nhật login
            mysqli_query($conn, "UPDATE users SET is_logged_in = 1 WHERE id = $user_id") or die('query failed');
            header('location:home.php');

         }

      }else{
         $message[] = 'Email hoặc mật khẩu không chính xác!';
      }

   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng nhập</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .btn {
         background-color: #3670EB;
         margin-top: 48px;
         margin-left: 119px;
      }
      .form-container form {
         height: 415px;
         border: none;
      }
      .title {
         margin: 18px 0;
      }
      a {
         color: #3670EB !important;
      }
      a:hover {
         opacity: 0.8;
      }
      .box{
         background-color: #fff !important;
         border: 1px solid #8888886e !important;
         padding: 1.2rem 4.4rem !important;
      }
      .forget-btn {
         float: right;
         font-size: 16px;
         text-decoration: underline;
         padding: 10px 0;
      }
      .lock-icon {
         position: absolute;
         top: 23px;
         font-size: 23px;
         left: 11px;
      }
      .user-icon {
         position: absolute;
         top: 24px;
         font-size: 20px;
         left: 9px;
      }
      *::-webkit-scrollbar-thumb{
         background-color: #3670EB !important;
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3 class="title">Đăng nhập</h3>
      <div style="position: relative;">
         <i class="fa fa-user-circle-o user-icon" aria-hidden="true"></i>
         <input type="email" name="email" placeholder="Email" required class="box">
      </div>
      <div style="position: relative;">
         <i class="fa fa-lock lock-icon" aria-hidden="true"></i>
         <input type="password" name="password" placeholder="Mật khẩu" required class="box">
      </div>
      <a class="forget-btn" href="forget_password.php">Quên mật khẩu</a>
      <input type="submit" name="submit" value="Đăng nhập" class="btn">
      <p>Bạn chưa có tài khoản? <a style="text-decoration: none;" href="register.php">Đăng ký</a></p>
   </form>

</div>

</body>
</html>