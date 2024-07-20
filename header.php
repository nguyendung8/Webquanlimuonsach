<?php
   //nhúng vào các trang bán hàng
   if(isset($message)){//hiển thị thông báo sau khi thao tác với biến message được gán giá trị
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>';//đóng thẻ này
      }
   }
?>
<style>
   .changepw-btn {
      border-radius: 4px;
      font-size: 20px;
      background: #3670EB;
      color: #fff;
      padding: 7px 12px;
   }
   .changepw-btn:hover {
      opacity: 0.7;
   }
</style>
<header class="header">

   <div class="header-2">
      <div style="padding: 0 2rem;" class="flex">
      <a href="home.php" class="logo"><img width="120px" height="100px" src="./images/logotv.png"></a>
         <nav class="navbar">
            <a href="home.php">Trang chủ</a>
            <a href="list_new_books.php">Sách mới nhất</a>
            <a href="contact.php">Liên hệ</a>
            <a href="borrows.php">Đã mượn</a>
            <a href="pays.php">Thanh toán</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="search_page.php" class="fas fa-search"></a>
            <div style="color: #3670EB !important;" id="user-btn" class="fas fa-user"></div>
            <?php
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $cart_rows_number = mysqli_num_rows($select_cart);
            ?>
            <a href="cart.php"> <i class="fas fa-shopping-cart"></i> <span>(<?php echo $cart_rows_number; ?>)</span> </a>
         </div>

         <div style="z-index: 1000;" class="user-box">
            <p><span style="color: #3670EB !important;"><?php echo $_SESSION['user_name']; ?></span></p>
            <a href="change_password.php" class="changepw-btn">Đổi mật khẩu</a>
            <a style="margin-top: 13px;" href="logout.php" class="delete-btn">Đăng xuất</a>
         </div>
      </div>
   </div>

</header>