<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   $sql_out_of_stock = "SELECT * FROM books WHERE quantity = 0";
   $result_stock = $conn->query($sql_out_of_stock);
   $out_of_stock = [];
    if ($result_stock->num_rows > 0) {
        while ($row = $result_stock->fetch_assoc()) {
            $out_of_stock[] = $row;
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thống kê</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="./css/admin_style.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .total_price {
            display:flex;
            align-items: center;
            gap: 10px;
        }
        .out_of_stock {
            margin-bottom: 30px;
        }
        h1 {
         color: #3670EB !important;
      }
    </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>
    <h1 style="margin-top: 25px;" class="title">Thống kê</h1>
   <div class="out_of_stock">
   <h1 class="statis_title">Thống kê sách đã hết trong kho</h1>
    <?php if (count($out_of_stock) > 0): ?>
      <div class="table-responsive card mt-2">
          <table style="width: 80% !important; margin: auto;" class="table table-bordered statistical_table">
              <tr>
                  <th>ID</th>
                  <th>Tên sách</th>
                  <th>Mô tả</th>
                  <th>Số lượng còn</th>
              </tr>
				<?php foreach ($out_of_stock as $item): ?>
					<tr>
						<td>
							<label style="width: auto"><?php echo $item['id']?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['name']; ?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['describes']; ?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['quantity']; ?></label>
						</td>
					</tr>
				<?php endforeach; ?>
          	</table>
      	</div>
    <?php else: ?>
        <p class="alert alert-danger">Danh sách trống</p>
    <?php endif; ?>
   </div>







<script src="js/admin_script.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>