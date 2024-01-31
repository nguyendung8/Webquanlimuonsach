<?php
   //nhúng vào các trang quản trị
   if(isset($message)){
      foreach($message as $message){//in ra thông báo trên cùng khi biến message được gán giá trị từ các trang quản trị
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>';
      }
   }
?>

<header class="header">

   <div style="padding: 0 2rem !important;" class="flex">

      <a href="admin_page.php" class="logo"><img width="110px" height="80px" src="./images/logo.png"></a>

      <nav class="navbar">
         <a href="admin_products.php">Sách</a>
         <a href="admin_category.php">Danh mục sách</a>
         <!-- <a href="admin_borrows.php">Phiếu mượn</a> -->
         <!-- <a href="admin_users.php">Người dùng</a></a> -->
         <!-- <a href="admin_contacts.php">Tin nhắn</a> -->
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div style="color: #3670EB !important;" id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="account-box">
         <p><span><?php echo $_SESSION['admin_name']; ?></span></p>
         <a href="logout.php" class="delete-btn">Đăng xuất</a>
      </div>

   </div>

</header>