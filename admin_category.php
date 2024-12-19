<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['add_category'])){//Thêm loại sách vào danh mục từ submit có name='add_category'

      $name = mysqli_real_escape_string($conn, $_POST['cate_name']);

      $select_category_name = mysqli_query($conn, "SELECT cate_name FROM `categories` WHERE cate_name = '$name'") or die('query failed');//truy vấn để kiểm tra loại sách đã tồn tại chưa

      if(mysqli_num_rows($select_category_name) > 0){// tồn tại rồi thì thông báo
         $message[] = 'Danh mục đã tồn tại.';
      }else{
         $add_category_query = mysqli_query($conn, "INSERT INTO `categories`(cate_name) VALUES('$name')") or die('query failed');

         if($add_category_query){
         $message[] = 'Thêm danh mục thành công!';
         }else{
            $message[] = 'Không thể thêm danh mục này!';
         }
      }
   }

   if(isset($_GET['delete'])){//Xóa loại sách từ onclick <a></a> có href='delete'
      $delete_id = $_GET['delete'];
      $delete_category_query = mysqli_query($conn, "DELETE FROM `categories` WHERE id = '$delete_id'") or die('query failed');

      if($delete_category_query){
         $message[] = 'Xóa danh mục thành công!';
         }else{
            $message[] = 'Không thể xóa danh mục này!';
         }
      header('location:admin_category.php');
   }

   if(isset($_POST['update_category'])){//Cập nhật loại sách vào danh mục từ submit có name='update_category'

      $update_p_id = $_POST['update_p_id'];
      $update_name = $_POST['update_name'];

      mysqli_query($conn, "UPDATE `categories` SET cate_name = '$update_name' WHERE id = '$update_p_id'") or die('query failed');

      header('location:admin_category.php');

   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Danh mục sách</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      .add-products form {
         border: none !important;
         box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
      }
      .box {
         border: 1px solid #3670EB !important;
         background-color: #fff !important;
      }
      h1, h3 {
         color: #3670EB !important;
      }
      .btn {
         background-color: #3670EB;
      }
      *::-webkit-scrollbar-thumb{
         background-color: unset !important;
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
      i {
         font-size: 15px;
         margin-right: 3px;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">Danh mục sách</h1>
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Thêm danh mục sách</h3>
      <input type="text" name="cate_name" class="box" placeholder="Nhập tên danh mục" required>
      <input type="submit" value="Thêm" name="add_category" class="btn">
   </form>

</section>

<section class="show-products">
   <div class="container">
      <h3 class="text-center my-4">Danh sách Danh mục Sách</h3>
      <div class="table-responsive">
         <table class="table table-bordered table-striped text-center">
            <thead class="table-primary">
               <tr>
                  <th>ID</th>
                  <th>Tên danh mục</th>
                  <th>Thao tác</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  $select_categorys = mysqli_query($conn, "SELECT * FROM `categories`") or die('query failed');
                  if(mysqli_num_rows($select_categorys) > 0){
                     while($fetch_categorys = mysqli_fetch_assoc($select_categorys)){
               ?>
               <tr>
                  <td><?php echo $fetch_categorys['id']; ?></td>
                  <td><?php echo $fetch_categorys['cate_name']; ?></td>
                  <td>
                     <a style="margin-right: 5px;" href="admin_category.php?update=<?php echo $fetch_categorys['id']; ?>" class="new-btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Cập nhật
                     </a>
                     <a href="admin_category.php?delete=<?php echo $fetch_categorys['id']; ?>" class="new-btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?');">
                        <i class="fas fa-trash"></i> Xóa
                     </a>
                  </td>
               </tr>
               <?php
                     }
                  }else{
                     echo '<tr><td colspan="3" class="text-center">Không có thể loại sách nào được thêm!</td></tr>';
                  }
               ?>
            </tbody>
         </table>
      </div>
   </div>
</section>


<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){//Hiện form cập nhật thông tin loại sách từ <a></a> có href='update'
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `categories` WHERE id = '$update_id'") or die('query failed');//lấy ra thông tin loại sách cần cập nhật
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                  <input type="text" name="update_name" value="<?php echo $fetch_update['cate_name']; ?>" class="box" required placeholder="Tên">
                  <input type="submit" value="Cập nhật" name="update_category" class="btn"> <!-- submit form cập nhật -->
                  <input type="reset" value="Hủy"  onclick="window.location.href = 'admin_category.php'" class="option-btn">
               </form>
   <?php
            }
         }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>