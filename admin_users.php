<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }


   if(isset($_GET['delete'])){//xóa người dùng từ onclick href='delete'
      $delete_id = $_GET['delete'];
      mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
      header('location:admin_users.php');
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Người dùng</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      h1, h3 {
         color: #3670EB !important;
      }
      .total-view {
         text-align: center;
         margin-bottom: 20px;
      }
      .users .box-container .box p span {
         color: #3670EB !important;
      }
      .box {
         border: 1px solid #3670EB !important;
         background-color: #fff !important;
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
      i  {
         font-size: 15px;
         margin-right: 3px;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">
   <div class="container">
      <h1 class="title text-center my-4">Tài Khoản Sinh Viên</h1>
      <div class="table-responsive">
         <table class="table table-bordered table-striped text-center">
            <thead class="table-primary">
               <tr>
                  <th>ID</th>
                  <th>Email</th>
                  <th>Tên Sinh Viên</th>
                  <th>Thao Tác</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
                  if(mysqli_num_rows($select_users) > 0){
                     while($fetch_users = mysqli_fetch_assoc($select_users)){
               ?>
               <tr>
                  <td><?php echo $fetch_users['id']; ?></td>
                  <td><?php echo htmlspecialchars($fetch_users['email']); ?></td>
                  <td><?php echo htmlspecialchars($fetch_users['name']); ?></td>
                  <td>
                     <?php if($fetch_users['user_type'] == 'admin'){ ?>
                        <button class="btn btn-secondary btn-sm" disabled>Không thể xóa Admin</button>
                     <?php } else { ?>
                        <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" 
                           onclick="return confirm('Xóa người dùng này?');" 
                           class="new-btn btn-danger btn-sm">
                           <i class="fas fa-trash-alt"></i> Xóa
                        </a>
                     <?php } ?>
                  </td>
               </tr>
               <?php
                     }
                  } else {
                     echo '<tr><td colspan="4" class="text-center">Không có tài khoản sinh viên nào!</td></tr>';
                  }
               ?>
            </tbody>
         </table>
      </div>
   </div>
</section>


<script src="js/admin_script.js"></script>

</body>
</html>