<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; //tạo session người dùng thường

if(!isset($user_id)){ // session không tồn tại => quay lại trang đăng nhập
   header('location:login.php');
}

// Kiểm tra nếu người dùng nhấn nút "Trả sách"
if (isset($_POST['return_book'])) {
   $borrow_id = $_POST['borrow_id'];

   // Bước 1: Lấy danh sách sách và số lượng từ bảng borrow_book
   $select_books = "SELECT book_id, quantity FROM borrow_book WHERE borrow_id = '$borrow_id'";
   $result_books = mysqli_query($conn, $select_books);

   if (mysqli_num_rows($result_books) > 0) {
      while ($book = mysqli_fetch_assoc($result_books)) {
         $book_id = $book['book_id'];
         $quantity = $book['quantity'];

         // Bước 2: Cộng số lượng sách vào kho (bảng books)
         $update_stock = "UPDATE books SET quantity = quantity + $quantity WHERE id = '$book_id'";
         mysqli_query($conn, $update_stock) or die('Lỗi cập nhật số lượng sách vào kho');
      }
   }

   $pay_date = date('Y-m-d');
   // Bước 3: Cập nhật trạng thái phiếu mượn thành "Đã trả" (borrow_status = 2)
   $update_status = "UPDATE borrows SET borrow_status = 2, pay_day = '$pay_date' WHERE id = '$borrow_id' AND user_id = '$user_id'";
   mysqli_query($conn, $update_status) or die('Lỗi cập nhật trạng thái phiếu mượn');

   $messege[] = 'Sách đã được trả thành công!';
   // Chuyển hướng lại trang danh sách phiếu mượn để cập nhật trạng thái
   header('Location: borrows.php');
   exit();
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
         width: 100%;
      }
      .borrow-box p {
         padding: 4px 0;
      }
      *::-webkit-scrollbar-thumb {
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
         $sql = "SELECT borrows.id AS borrow_id, borrows.placed_on, borrows.pay_day, borrows.borrow_deadline, borrows.borrow_status, books.id AS book_id, borrow_book.quantity, books.name, books.author, books.image
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
                  'borrow_deadline' => $row['borrow_deadline'],
                  'borrow_status' => $row['borrow_status'],
                  'pay_day' => $row['pay_day'],
                  'books' => []
               ];
            }
            $borrows[$borrow_id]['books'][] = [
               'book_id' => $row['book_id'],
               'name' => $row['name'],
               'author' => $row['author'],
               'image' => $row['image'],
               'quantity' => $row['quantity']
            ];
         }

         if (!empty($borrows)) {
            foreach ($borrows as $borrow_id => $borrow) {
      ?>
      <div class="borrow-box">
         <p> ID phiếu mượn: <span><?php echo $borrow_id; ?></span> </p>
         <p>Ngày mượn: <span><?php echo date("d-m-Y", strtotime($borrow['placed_on'])); ?></span></p>
         <p>Ngày hẹn trả: <span><?php echo date("d-m-Y", strtotime($borrow['borrow_deadline'])); ?></span></p>

         <?php
            foreach ($borrow['books'] as $book) {
               echo "<p> Tên sách: " . htmlspecialchars($book['name']) .  "<br> Số lượng: " . $book['quantity'] . "</p>";
            }
         ?>

         <p>
            Ngày trả thực tế:
            <span style="color:<?php if($borrow['borrow_status'] == 2){ echo 'green'; }else{ echo 'red'; } ?>;">
               <?php
                  if ($borrow['borrow_status'] == 2) {
                     echo date("d-m-Y", strtotime($borrow['pay_day']));
                  } else {
                     echo 'Chưa trả';
                  }
               ?>
            </span>
         </p>

         <p> Trạng thái: 
         <span style="color:<?php if($borrow['borrow_status'] == 1){ echo 'green'; }else if($borrow['borrow_status'] == 2){ echo 'orange'; }else{ echo '#0022ff'; } ?>;">
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
            // Hiển thị nút "Trả sách" nếu phiếu mượn chưa được trả và có trạng thái "Đã duyệt" hoặc "Chờ xử lý"
            if ($borrow['borrow_status'] == 1 || $borrow['borrow_status'] == 0) {
         ?>
         <form method="POST">
            <input type="hidden" name="borrow_id" value="<?php echo $borrow_id; ?>">
            <button type="submit" name="return_book" class="btn" style="background-color: red; color: white;">Trả sách</button>
         </form>
         <?php
            }
         ?>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">Chưa có sách được mượn!</p>';
         }
      ?>

   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
