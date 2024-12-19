<?php
   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; // Tạo session người dùng thường

   if (!isset($user_id)) { // Session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   // Xóa sách khỏi giỏ hàng
   if (isset($_GET['delete'])) {
      $delete_id = $_GET['delete'];
      mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id' AND user_id = '$user_id'") or die('query failed');
      header('location:cart.php');
   }

   // Xóa tất cả sách trong giỏ hàng
   if (isset($_GET['clear'])) {
      mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      header('location:cart.php');
   }

    // Xử lý khi người dùng xác nhận mượn sách
    if (isset($_POST['borrow_books'])) {
        $borrow_deadline = $_POST['borrow_deadline'];
        $placed_on = date("Y-m-d");
  
        // Thêm phiếu mượn vào bảng borrows
        $insert_borrow = mysqli_query($conn, "INSERT INTO `borrows` (user_id, placed_on, borrow_deadline) VALUES ('$user_id', '$placed_on', '$borrow_deadline')") or die('query failed');
        $borrow_id = mysqli_insert_id($conn); // Lấy id của phiếu mượn vừa tạo
  
        // Thêm sách vào bảng borrow_book
        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
           $book_id = $fetch_cart['book_id'];
           $quantity = $fetch_cart['quantity'];
  
           // Kiểm tra số lượng sách còn lại trong kho
           $book_query = mysqli_query($conn, "SELECT quantity FROM `books` WHERE id = '$book_id'") or die('query failed');
           $fetch_book = mysqli_fetch_assoc($book_query);
           $available_quantity = $fetch_book['quantity'];
  
           if ($quantity <= $available_quantity) {
              // Cập nhật bảng borrow_book
              mysqli_query($conn, "INSERT INTO `borrow_book` (book_id, borrow_id, quantity) VALUES ('$book_id', '$borrow_id', '$quantity')") or die('query failed');
  
              // Giảm số lượng sách trong bảng books
            //   mysqli_query($conn, "UPDATE `books` SET quantity = quantity - '$quantity' WHERE id = '$book_id'") or die('query failed');
           } else {
              $message[] = 'Số lượng sách không đủ để mượn!';
              exit;
           }
        }
  
        // Xóa sách khỏi giỏ hàng sau khi mượn
        mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        $message[] = 'Mượn sách thành công!';
        header('location:cart.php');
     }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Giỏ Hàng</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/main.css">
   <style>
    .cart-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    a {
        text-decoration: none !important;
    }
      .table {
         width: 100%;
         border-collapse: collapse;
      }
      .table th, .table td {
         border: 1px solid #ddd;
         padding: 10px;
         text-align: center;
      }
      .table th {
         background-color: #3670EB;
         color: #fff;
      }
      .table img {
         width: 70px;
         height: 100px;
      }
      .action-btn:hover {
        color: #fff !important;
        background-color: #D93A2C;
      }
      .clear-btn {
         display: inline-block;
         margin: 10px 0;
         background-color: #FF4136;
         color: white;
         padding: 8px 15px;
         text-decoration: none;
         border-radius: 5px;
      }
      .clear-btn:hover {
         background-color: #D93A2C;
         color: #fff !important;
      }
      th {
           font-size: 20px;
            text-align: center;
      }
      td {
         font-size: 18px;
         padding: 1.5rem 0.5rem !important;
         text-align: center;
      }
      .new-btn {
         padding: 10px 13px; 
         text-decoration: none; 
         font-size: 18px;
         margin-bottom: 7px;
         border-radius: 4px;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="cart">
   <h1 class="title">Giỏ Hàng Của Bạn</h1>

   <div class="table-container">
      <table class="table">
         <thead>
            <tr>
               <th>ID</th>
               <th>Hình Ảnh</th>
               <th>Tên Sách</th>
               <th>Số Lượng</th>
               <th>Thao Tác</th>
            </tr>
         </thead>
         <tbody>
            <?php
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               if (mysqli_num_rows($select_cart) > 0) {
                  while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            ?>
            <tr>
               <td><?php echo $fetch_cart['id']; ?></td>
               <td><img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="Book"></td>
               <td><?php echo htmlspecialchars($fetch_cart['name']); ?></td>
               <td><?php echo $fetch_cart['quantity']; ?></td>
               <td>
                  <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="new-btn btn-danger action-btn" onclick="return confirm('Xóa sách này khỏi giỏ hàng?');">
                     Xóa
                  </a>
               </td>
            </tr>
            <?php
                  }
               } else {
                  echo '<tr><td colspan="5">Giỏ hàng của bạn đang trống!</td></tr>';
               }
            ?>
         </tbody>
      </table>
   </div>

    

   <!-- Modal nhập thông tin phiếu mượn -->
   <div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title fs-2" id="borrowModalLabel">Phiếu Mượn Sách</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form action="cart.php" method="post">
                  <div class="mb-3">
                     <label for="borrow_deadline" class="form- fs-3">Ngày hẹn trả</label>
                     <input type="date" class="form-control fs-3" id="borrow_deadline" name="borrow_deadline" required>
                  </div>
                  <button type="submit" name="borrow_books" class="fs-4 new-btn btn-primary">Xác Nhận Mượn</button>
               </form>
            </div>
         </div>
      </div>
   </div>

   <!-- Nút mượn sách -->
   <div class="cart-btn">
      <button class="new-btn btn-success btn-success" data-bs-toggle="modal" data-bs-target="#borrowModal">Mượn Sách</button>
      <a href="cart.php?clear" class="fs-2 clear-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa tất cả sách trong giỏ hàng không?');">Xóa tất cả</a>
   </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
