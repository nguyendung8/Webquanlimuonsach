<?php
   //đăng ksy tài khoản người dùng
   include 'config.php';

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
   <link rel="stylesheet" href="css/style.css">
   <style>
      body {
         font-family: 'Poppins', sans-serif;
         background-color: #f5f7fa;
         display: flex;
         align-items: center;
         justify-content: center;
         height: 100vh;
         margin: 0;
         overflow: hidden;
      }

      .register-container {
         position: relative;
         width: 100%;
         max-width: 1200px;
         display: flex;
         justify-content: center;
         align-items: center;
      }

      .form-register-container {
         background: white;
         padding: 40px;
         border-radius: 10px;
         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
         max-width: 400px;
         width: 100%;
         text-align: center;
         z-index: 1;
      }

      .form-register-container .title {
         font-size: 24px;
         font-weight: 600;
         margin-bottom: 10px;
         color: #3670EB;
      }

      .form-register-container .description {
         font-size: 14px;
         color: #666;
         margin-bottom: 30px;
      }

      .form-register-container h3 {
         margin-bottom: 20px;
         font-size: 20px;
         color: #333;
      }

      .form-register-container .box {
         width: 100%;
         padding: 12px;
         margin: 10px 0;
         border: 1px solid #ddd;
         border-radius: 5px;
         box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
         font-size: 14px;
      }

      .form-register-container .btn {
         width: 100%;
         padding: 12px;
         background-color: #3670EB;
         border: none;
         border-radius: 5px;
         color: white;
         font-size: 16px;
         cursor: pointer;
         transition: background 0.3s;
      }

      .form-register-container .btn:hover {
         background-color: #45b826;
      }

      .form-register-container p {
         margin: 15px 0 0;
         font-size: 16px;
      }

      .form-register-container p a {
         color: #3670EB;
         text-decoration: none;
         font-weight: 600;
      }

      .form-register-container p a:hover {
         text-decoration: underline;
      }

      .register-img {
         position: absolute;
         top: 50%;
         right: 0;
         width: 500px;
         max-width: 50%;
         transform: translateY(-50%);
         z-index: 0;
      }

      .message {
         background: #ffdddd;
         color: #d8000c;
         padding: 10px;
         border-left: 4px solid #d8000c;
         margin: 10px 0;
         position: relative;
         border-radius: 5px;
         font-size: 14px;
      }

      .message .fas {
         position: absolute;
         top: 10px;
         right: 10px;
         cursor: pointer;
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

   <div class="register-container">
      <div class="form-register-container">
         <h1 class="title">Tạo tài khoản của bạn</h1>
         <p class="description">Bắt đầu hành trình tìm kiếm cơ hội nghề nghiệp tuyệt vời với hồ sơ nổi bật</p>
         <h3>Đăng ký</h3>
         <form action="" method="post">
               <input type="text" name="name" placeholder="Nhập họ tên" required class="box">
               <input type="email" name="email" placeholder="Nhập email" required class="box">
               <input type="password" name="password" placeholder="Nhập mật khẩu" required class="box">
               <input type="password" name="cpassword" placeholder="Nhập lại mật khẩu" required class="box">
               <input type="submit" name="submit" value="Đăng ký ngay" class="btn">
         </form>

         <p>Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
      </div>
   </div>

</body>
</html>