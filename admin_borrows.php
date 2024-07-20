<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   };
   
   // Click duyệt
   if(isset($_POST['confirmed'])) {
      $borrow_id = $_POST['borrow_id'];

      // Truy vấn để lấy thông tin sách trong phiếu mượn
      $sql = "SELECT book_id, quantity FROM borrow_book WHERE borrow_id = '$borrow_id'";
      $result = mysqli_query($conn, $sql) or die('query failed');

      while ($row = mysqli_fetch_assoc($result)) {
         $book_id = $row['book_id'];
         $borrowed_quantity = $row['quantity'];
 
         // Cập nhật số lượng sách trong bảng books
         $update_sql = "UPDATE books SET quantity = quantity - $borrowed_quantity WHERE id = '$book_id'";
         mysqli_query($conn, $update_sql) or die('query failed');

         // Cập nhật trạng thái phiếu mượn
         $update_sql = "UPDATE borrows SET borrow_status = 1 WHERE id = '$borrow_id'";
         mysqli_query($conn, $update_sql) or die('query failed');
      }
   
      $message[] = 'Duyệt sách thành công!';
   }

   // Click trả sách
   if (isset($_POST['returned'])) {
      $borrow_id = $_POST['borrow_id'];
      $pay_day = date('d-m-Y');
  
      // Truy vấn để lấy thông tin sách trong phiếu mượn
      $sql = "SELECT book_id, quantity FROM borrow_book WHERE borrow_id = '$borrow_id'";
      $result = mysqli_query($conn, $sql) or die('query failed');
  
      while ($row = mysqli_fetch_assoc($result)) {
          $book_id = $row['book_id'];
          $borrowed_quantity = $row['quantity'];
  
          // Cập nhật số lượng sách trong bảng books
          $update_sql = "UPDATE books SET quantity = quantity + $borrowed_quantity WHERE id = '$book_id'";
          mysqli_query($conn, $update_sql) or die('query failed');
      }
  
      // Cập nhật trạng thái phiếu mượn là đã trả
      $update_borrow_sql = "UPDATE borrows SET borrow_status = 2, pay_day = '$pay_day' WHERE id = '$borrow_id'";
      mysqli_query($conn, $update_borrow_sql) or die('query failed');
  
      $message[] = 'Sách đã được trả thành công!';
   }

   // Click xóa phiếu mượn
   if (isset($_POST['delete-borrow'])) {
      $borrow_id = $_POST['borrow_id'];
      // Xóa phiếu mượn
      $delete_borrow_sql = "DELETE FROM borrows WHERE id = '$borrow_id'";
      mysqli_query($conn, $delete_borrow_sql) or die('query failed');
  
      $message[] = 'Xóa phiếu mượn thành công!';
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
         $sql = "SELECT borrows.user_id, borrows.id AS borrow_id, borrows.placed_on, borrows.pay_day, books.id AS book_id, books.name, borrow_book.quantity, borrows.borrow_status
         FROM borrows
         JOIN borrow_book ON borrows.id = borrow_book.borrow_id
         JOIN books ON borrow_book.book_id = books.id
         ORDER BY borrows.placed_on DESC";
         $result = mysqli_query($conn, $sql) or die('query failed');
         $borrows = [];
         while ($row = mysqli_fetch_assoc($result)) {
            $borrow_id = $row['borrow_id'];
            if (!isset($borrows[$borrow_id])) {
               $borrows[$borrow_id] = [
                     'placed_on' => $row['placed_on'],
                     'user_id' => $row['user_id'],
                     'quantity' => $row['quantity'],
                     'borrow_status' => $row['borrow_status'],
                     'pay_day' => $row['pay_day'],
                     'books' => []
               ];
            }
            $borrows[$borrow_id]['books'][] = [
               'book_id' => $row['book_id'],
               'name' => $row['name'],
            ];
         }
         if(!empty($borrows)){
            foreach ($borrows as $borrow_id => $borrow) {
               ?>
               <div style="text-align: center; height: -webkit-fill-available;" class="box">
                  <p> Phiếu mượn ID: : <span><?php echo $borrow_id; ?></span> </p>
                  <?php
                  $user_id = $borrow['user_id'];
                     $fetch_user = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id") or die('query failed');
                     $user = mysqli_fetch_assoc($fetch_user);
                  ?>
                  <p> MSSV : <span><?php echo $user['mssv']; ?></span> </p>
                  <p> Tên SV: <span><?php echo $user['name']; ?></span></p>
                  <?php
                     foreach ($borrow['books'] as $book) {
                        echo "<p>" . " Tên sách: " . $book['name']  . " - " . "Số lượng: " . $borrow['quantity'] . "</p>"  . "<br>";
                     }
                  ?>
                  <p>Ngày mượn: <span><?php echo $borrow['placed_on']; ?></span></p>
                  <?php
                     if($borrow['borrow_status'] == 2){
                  ?>
                  <p>Ngày trả: <span><?php echo $borrow['pay_day']; ?></span> </p>
                  <?php
                     }
                  ?>
                  <p style="margin-top: 10px;"> Trạng thái  : 
                     <span style="color:<?php if($borrow['borrow_status'] == 1){ echo 'green !important'; }else if($borrow['borrow_status'] == '2'){ echo 'orange !important'; }else{ echo '#0022ff !important'; } ?>;">
                        <?php 
                           if ($borrow['borrow_status'] == 1) {
                              echo 'Đã duyệt';
                           } else if($borrow['borrow_status'] == 2) {
                              echo 'Đã trả';
                           } else {
                              echo 'Chờ xử lý';
                           }
                        ?>
                     </span> 
                  </p>
                     <?php
                     if($borrow['borrow_status'] == 0) {            
                     ?>
                        <form action="" method="post">
                           <input type="hidden" name="borrow_id" value="<?php echo $borrow_id ?>">
                           <input style= "background: #0022ff" class="confirm-btn" type="submit" value=" <?php echo 'Duyệt';  ?>" name="confirmed" >
                        </form>
                     <?php
                        } else if($borrow['borrow_status'] == 1) {
                     ?>
                        <form action="" method="post">
                           <input type="hidden" name="borrow_id" value="<?php echo $borrow_id ?>">
                           <input style= "background: #12c811c7" class="confirm-btn" type="submit" value=" <?php echo 'Trả sách';  ?>" name="returned" >
                        </form>
                     <?php
                        } else  {
                     ?>
                        <form action="" method="post">
                           <input type="hidden" name="borrow_id" value="<?php echo $borrow_id ?>">
                           <input style= "background: red " class="confirm-btn" type="submit" value=" <?php echo 'Xóa phiếu';  ?>" name="delete-borrow" >
                        </form>
                     <?php
                        }
                     ?>
               </div>
      <?php
            }
         }else{
            echo '<p class="empty">Không có phiếu mượn nào!</p>';
         }
      ?>
   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>