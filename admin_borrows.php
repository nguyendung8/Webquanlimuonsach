<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   };
   
   // Click duyệt
   if(isset($_POST['confirmed'])) {
      $book_id = $_POST['book_id'];
      
      // Lấy thông tin sách
      $sql = "SELECT * FROM books WHERE id = $book_id";
      $result = $conn->query($sql);
      $bookItem = $result->fetch_assoc();
      $book_current_quantity = $bookItem['quantity'];

      $book_quantity = $_POST['book_quantity'];
      $borrow_id = $_POST['borrow_id'];
      if($book_current_quantity >= $book_quantity) {
         mysqli_query($conn, "UPDATE books SET quantity = quantity - $book_quantity WHERE id = $book_id;") or die('query failed');
         mysqli_query($conn1, "UPDATE borrows SET borrows.is_confirmed = 1 WHERE borrows.id = $borrow_id;") or die('query failed');
         $message[] = 'Duyệt sách thành công!';
      } else {
         $message[] = 'Số lượng sách này ở trong kho hiện tại không đủ, hãy nhập thêm sách!';
      }
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Phiếu mượn</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      .box {
         border: 1px solid #3670EB !important;
         background-color: #fff !important;
      }
      h1, h3 {
         color: #3670EB !important;
      }
      .confirm-btn {
         margin-top: 16px;
         padding: 7px 16px;
         border-radius: 4px;
         font-size: 18px;
         color: #fff;
         cursor: pointer;
      }
      .confirm-btn:hover {
         opacity: 0.8;
      }
      .orders .box-container .box p span {
         color: #3670EB !important;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="orders">

   <h1 class="title">Phiếu mượn</h1>
   <div class="box-container">
      <?php
         $select_orders = mysqli_query($conn, "SELECT * FROM `borrows`") or die('query failed');
         if(mysqli_num_rows($select_orders) > 0){
            while($fetch_borrows = mysqli_fetch_assoc($select_orders)){
      ?>
               <div style="text-align: center;" class="box">
                  <p> User id : <span><?php echo $fetch_borrows['user_id']; ?></span> </p>
                  <p> Tên : <span><?php echo $fetch_borrows['user_name']; ?></span> </p>
                  <p> Email : <span><?php echo $fetch_borrows['email']; ?></span> </p>
                  <p> Số điện thoại : <span><?php echo $fetch_borrows['phone']; ?></span> </p>
                  <p> Tên sách : <span><?php echo $fetch_borrows['book_name']; ?></span> </p>
                  <p> Số lượng mượn : <span><?php echo $fetch_borrows['borrow_quantity']; ?> quyển</span> </p>
                  <img width="180px" height="207px" src="uploaded_img/<?php echo $fetch_borrows['book_img']; ?>" alt="">
                  <p style="margin-top: 10px;"> Trạng thái  : 
                     <span style="color:<?php if($fetch_borrows['is_confirmed'] == 1){ echo 'green !important'; }else if($fetch_borrows['is_confirmed'] == '0'){ echo 'red !important'; }else{ echo 'orange !important'; } ?>;">
                        <?php if ($fetch_borrows['is_confirmed'] == 1) {
                              echo 'Đã duyệt';
                           } else {
                              echo 'Chờ xử lý';
                           }
                        ?>
                     </span> 
                  </p>
                  <form action="" method="post">
                     <input type="hidden" name="book_id" value="<?php echo $fetch_borrows['book_id'] ?>">
                     <input type="hidden" name="book_quantity" value="<?php echo $fetch_borrows['borrow_quantity'] ?>">
                     <input type="hidden" name="borrow_id" value="<?php echo $fetch_borrows['id'] ?>">
                     <input style="background:<?php if($fetch_borrows['is_confirmed'] == 1){ echo '#12c811c7'; } else{ echo 'red'; } ?>;" class="confirm-btn" type="submit" value=" <?php if ($fetch_borrows['is_confirmed'] == 1) {echo 'Đã duyệt'; } else { echo 'Duyệt';}  ?>" name="confirmed" <?php if($fetch_borrows['is_confirmed'] == 1) echo 'disabled' ?> >
                  </form>
               </div>
      <?php
            }
         }else{
            echo '<p class="empty">Không có đơn đặt hàng nào!</p>';
         }
      ?>
   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>