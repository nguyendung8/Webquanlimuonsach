<?php
   //đăng ký tài khoản người dùng
   include 'config.php';
   session_start();

   if(isset($user_id)){// session tồn tại => quay lại trang người dùng
      $user_id = $_SESSION['user_id']; //tạo session người dùng thường
      header('location:home.php');
   }else if(isset($admin_id)){// session tồn tại => quay lại trang quản lý
      $admin_id = $_SESSION['admin_id']; //tạo session admin
      header('location:admin_products.php');
   }

   if(isset($_POST['submit'])){

      $name = mysqli_real_escape_string($conn, $_POST['name']);
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
      $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
      // $user_type = $_POST['user_type'];
      $user_type = 'user';

      $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

      if(mysqli_num_rows($select_users) > 0){//kiểm tra email đã tồn tại chưa
         $message[] = 'Tài khoản đã tồn tại!';
      }else{//chưa thì kiểm tra mật khẩu xác nhận và tạo tài khoản
         if($pass != $cpass){
            $message[] = 'Mật khẩu không khớp!';
         }else{
            mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$cpass', '$user_type')") or die('query failed');
            $message[] = 'Đăng ký thành công!';
            header('location:login.php');
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
   <title>Đăng ký</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .btn {
         background-color: #3670EB;
      }
      .form-container form {
         height: 500px;
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
      <h3 class="title">Đăng ký</h3>
      <div style="position: relative;">
         <i class="fa fa-user-circle-o user-icon" aria-hidden="true"></i>
         <input type="text" name="name" placeholder="Nhập họ tên" required class="box">
      </div>
      <div style="position: relative;">
      <i class="fa fa-envelope-o user-icon" aria-hidden="true"></i>
         <input type="email" name="email" placeholder="Nhập email" required class="box">
      </div>
      <div style="position: relative;">
         <i class="fa fa-lock lock-icon" aria-hidden="true"></i>
         <input type="password" name="password" placeholder="Nhập mật khẩu" required class="box">
      </div>
      <div style="position: relative;">
         <i class="fa fa-lock lock-icon" aria-hidden="true"></i>
         <input type="password" name="cpassword" placeholder="Nhập lại mật khẩu" required class="box">
      </div>
      <input type="submit" name="submit" value="Đăng ký" class="btn">
      <p>Bạn đã có tài khoản? <a style="text-decoration: none;" href="login.php">Đăng nhập</a></p>
   </form>

</div>

</body>
</html>