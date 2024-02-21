<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['add_product'])){//thêm sách mới từ submit form name='add_product'

      $name = mysqli_real_escape_string($conn, $_POST['name']);
      $author = mysqli_real_escape_string($conn, $_POST['author']);
      $category = mysqli_real_escape_string($conn, $_POST['category']);
      $quantity = $_POST['quantity'];
      $describe = $_POST['describe'];
      $image = $_FILES['image']['name'];
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = 'uploaded_img/'.$image;

      $select_product_name = mysqli_query($conn, "SELECT name FROM `books` WHERE name = '$name'") or die('query failed');//truy vấn kiểm tra sách đã tồn tại chưa

      if(mysqli_num_rows($select_product_name) > 0){
         $message[] = 'Sách đã tồn tại.';
      }else{//chưa thì thêm mới
         $add_product_query = mysqli_query($conn, "INSERT INTO `books`(name, author, cate_id, quantity, describes, image) VALUES('$name', '$author', '$category', '$quantity', '$describe', '$image')") or die('query failed');
         if($add_product_query){
            if($image_size > 2000000){//kiểm tra kích thước ảnh
               $message[] = 'Kích tước ảnh quá lớn, hãy cập nhật lại ảnh!';
            }else{
               move_uploaded_file($image_tmp_name, $image_folder);//lưu file ảnh xuống
               $message[] = 'Thêm sách thành công!';
            }
         }else{
            $message[] = 'Thêm sách không thành công!';
         }
      }
   }

   if(isset($_GET['delete'])){//xóa sách từ onclick <a></a> href='delete'
      $delete_id = $_GET['delete'];
      $delete_image_query = mysqli_query($conn, "SELECT image FROM `books` WHERE id = '$delete_id'") or die('query failed');
      $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
      unlink('uploaded_img/'.$fetch_delete_image['image']);//xóa file ảnh của sách cần xóa
      mysqli_query($conn, "DELETE FROM `books` WHERE id = '$delete_id'") or die('query failed');
      header('location:admin_products.php');
   }

   if(isset($_POST['update_product'])){//cập nhật sách từ form submit name='update_product'

      $update_p_id = $_POST['update_p_id'];
      $update_name = $_POST['update_name'];
      $update_author = $_POST['update_author'];
      $update_category = $_POST['update_category'];
      $update_quantity = $_POST['update_quantity'];
      $update_describe = $_POST['update_describe'];

      mysqli_query($conn, "UPDATE `books` SET name = '$update_name', author = '$update_author', cate_id='$update_category', quantity='$update_quantity', describes='$update_describe' WHERE id = '$update_p_id'") or die('query failed');

      header('location:admin_products.php');

   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sách</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
         background-color: #3670EB !important;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">Quản lý Sách</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Thêm sách</h3>
      <input type="text" name="name" class="box" placeholder="Tên sách" required>
      <input type="text" name="author" class="box" placeholder="Tác giả" required>
      <select name="category" class="box">
         <?php
            $select_category= mysqli_query($conn, "SELECT * FROM `categories`") or die('Query failed');
            if(mysqli_num_rows($select_category)>0){
               while($fetch_category=mysqli_fetch_assoc($select_category)){
                  echo "<option value='" . $fetch_category['id'] . "'>".$fetch_category['cate_name']."</option>";
               }
            }
            else{
               echo "<option>Không có danh mục nào.</option>";
            }
         ?>
      </select>
      <input type="number" min="1" name="quantity" class="box" placeholder="Số lượng" required>
      <input type="text" name="describe" class="box" placeholder="Mô tả" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="Thêm" name="add_product" class="btn">
   </form>

</section>

<section class="show-products">

   <div class="box-container">

      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `books`") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
               <div class="box">
                  <img width="180px" height="207px" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_products['name']; ?></div>
                  <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Cập nhật</a>
                  <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Xóa sách này?');">Xóa</a>
               </div>
      <?php
            }
      }else{
         echo '<p class="empty">Không có truyện nào được thêm!</p>';
      }
      ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){//hiện form update từ onclick <a></a> href='update'
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `books` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                  <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                  <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Tên truyện">
                  <input type="text" name="update_author" value="<?php echo $fetch_update['author']; ?>" class="box" required placeholder="Tác giả">
                  <select name="update_category" class="box">
                     <?php
                        $cate_id = $fetch_update['cate_id'];
                        $select_category_name= mysqli_query($conn, "SELECT * FROM `categories` c WHERE c.id = $cate_id") or die('Truy vấn lỗi');
                        while($fetch_category_name=mysqli_fetch_assoc($select_category_name)){
                           echo"<option>".$fetch_category_name['cate_name']."</option>";
                        }
                     ?>
                     <?php
                      $cate_id = $fetch_update['cate_id'];
                      $select_category_name= mysqli_query($conn, "SELECT * FROM `categories` c WHERE c.id = $cate_id") or die('Truy vấn lỗi');
                      $fetch_category_name=mysqli_fetch_assoc($select_category_name);
                      $select_category= mysqli_query($conn, "SELECT * FROM `categories`") or die('Truy vấn lỗi');
                        while($fetch_category=mysqli_fetch_assoc($select_category)){
                           if($fetch_category['cate_name']!=$fetch_category_name['cate_name']){
                              echo"<option value='" . $fetch_category['id'] . "'>".$fetch_category['cate_name']."</option>";
                           }
                        }
                     ?>
                  </select>
                  <input type="number" name="update_quantity" value="<?php echo $fetch_update['quantity']; ?>" min="0" class="box" required placeholder="Số lượng truyện">
                  <input type="text" name="update_describe" value="<?php echo $fetch_update['describes']; ?>" class="box" required placeholder="Mô tả">
                  <input type="submit" value="update" name="update_product" class="btn">
                  <input type="reset" value="cancel" id="close-update" class="option-btn">
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