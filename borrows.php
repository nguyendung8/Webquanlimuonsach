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
         $sql = "SELECT borrows.id AS borrow_id, borrows.placed_on, borrows.is_confirmed, books.id AS book_id, borrow_book.quantity, books.name
         FROM borrows
         JOIN borrow_book ON borrows.id = borrow_book.borrow_id
         JOIN books ON borrow_book.book_id = books.id
         WHERE borrows.user_id = '$user_id'
         ORDER BY borrows.id DESC";
         $result = mysqli_query($conn, $sql);
         $borrows = [];
         while ($row = mysqli_fetch_assoc($result)) {
            $borrow_id = $row['borrow_id'];
            if (!isset($borrows[$borrow_id])) {
               $borrows[$borrow_id] = [
                     'placed_on' => $row['placed_on'],
                     'is_confirmed' => $row['is_confirmed'],
                     'quantity' => $row['quantity'],
                     'books' => []
               ];
            }
            $borrows[$borrow_id]['books'][] = [
               'book_id' => $row['book_id'],
               'name' => $row['name'],
            ];
         }

         if (!empty($borrows)) {
            foreach ($borrows as $borrow_id => $borrow) {
      ?>
      <div style="height: -webkit-fill-available;" class="borrow-box">
         <p> ID phiếu mượn : <span><?php echo $borrow_id; ?></span> </p>
         <?php
            foreach ($borrow['books'] as $book) {
               echo " Tên sách: " . $book['name']  . " - " . "Số lượng: " . $borrow['quantity']  . "<br>";
            }
         ?>
         <p>Ngày mượn: <span><?php echo $borrow['placed_on']; ?></span></p>
         <p> Trạng thái  : 
            <span style="color:<?php if($borrow['is_confirmed'] == 1){ echo 'green'; }else if($borrow['is_confirmed'] == '0'){ echo 'red'; }else{ echo 'orange'; } ?>;">
               <?php if ($borrow['is_confirmed'] == 1) {
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
         echo '<p class="empty">Chưa có sách được mượn!</p>';
      }
      ?>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>