<?php

require 'authentication.php'; // admin authentication check 

// auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
}

// check admin
$user_role = $_SESSION['user_role'];


if(isset($_GET['delete_task'])){
  $action_id = $_GET['task_id'];
  
  $sql = "DELETE FROM task_info WHERE task_id = :id";
  $sent_po = "task-info.php";
  $obj_admin->delete_data_by_this_method($sql,$action_id,$sent_po);
}

if(isset($_POST['add_task_post'])){
    $obj_admin->add_new_task($_POST);
}

$page_name="Task_Info";
include("include/sidebar.php");
// include('ems_header.php');


?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog add-category-modal">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h2 class="modal-title text-center">اضافة مهمة</h2>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <form role="form" action="" method="post" autocomplete="off">
                <div class="form-horizontal">
                  <div class="form-group">
                   
                    <div class="col-sm-7">
                      <input type="text" placeholder="عنوان المهمة" id="task_title" name="task_title" list="expense" class="form-control" id="default" required>
                    </div>
                    <label class="control-label col-sm-5">عنوان المهمة</label>
                  </div>
                  <div class="form-group">
                    
                    <div class="col-sm-7">
                      <textarea name="task_description" id="task_description" placeholder="وصف المهمة" class="form-control" rows="5" cols="5"></textarea>
                    </div>
                    <label class="control-label col-sm-5">وصف المهمة </label>ٍ
                  </div>
                  <div class="form-group">
                    <div class="col-sm-7">
                      <input type="text" name="t_start_time" id="t_start_time" class="form-control">
                    </div>
                    <label class="control-label col-sm-5">تاريخ البداية</label>

                  </div>
                  <div class="form-group">
                    <div class="col-sm-7">
                      <input type="text" name="t_end_time" id="t_end_time" class="form-control">
                    </div>
                    <label class="control-label col-sm-5">تاريخ النهاية</label>

                  </div>
                  <div class="form-group">

                    <div class="col-sm-7">
                      <?php 
                        $sql = "SELECT user_id, fullname FROM tbl_admin WHERE user_role = 2";
                        $info = $obj_admin->manage_all_info($sql);   
                        ?>
                      <select class="form-control" name="assign_to" id="aassign_to" required>
                        <option value="">حدد الموظف</option>
                        
                        <?php while($row = $info->fetch(PDO::FETCH_ASSOC)){ ?>
                          <option value="<?php echo $row['user_id']; ?>"><?php echo $row['fullname']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                      
                    <label class="control-label col-sm-5">مسندة الى</label>

                  </div>
                  
                  
                    <form id="form" action="" method="post" enctype="multipart/form-data" class="form-control">
                        <div class="form-group">
                             <input name="file[]" multiple="" class="form-control" type="file" />
                        </div>
                        <!--<div class="form-group">
                             <button id="confirm" class="btn btn-primary btn-sm" name="upload">Upload</button>
                        </div>-->
                    </form>

                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-3">
                      <button type="submit" name="add_task_post" class="btn btn-success-custom">اضافة مهمة</button>
                    </div>
                    <div class="col-sm-3">
                      <button type="submit" class="btn btn-danger-custom" data-dismiss="modal">الغاء</button>
                    </div>
                  </div>
                </div>
              </form> 
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>





    <div class="row">
      <div class="col-md-12">
        <div class="well well-custom">
          <div class="gap"></div>
          <div class="row">
            <div class="col-md-8">
              <div class="btn-group">
                <?php if($user_role == 1 or $user_role == 2){ ?>
                <div class="btn-group">
                  <button class="btn btn-warning btn-menu" data-toggle="modal" data-target="#myModal">اضافة مهمة جديدة</button>
                </div>
              <?php } ?>

              </div>

            </div>

            
          </div>
          <center ><h3>قسم ادارة المهام</h3></center>
          <div class="gap"></div>

          <div class="gap"></div>

          <div class="table-responsive">
            <table class="table table-codensed table-custom">
              <thead>
                <tr>
                  <th>خيارات</th>
                  <th>الحالة</th>
                  <th> وقت النهاية </th>
                  <th>وقت البداية </th>
                  <th>مسندة الى</th>
                  <th>عنوان المهمة</th>
                  <th>تسلسل</th>
                  
                </tr>
              </thead>
              <tbody>

              <?php 
                if($user_role == 1 ){
                  $sql = "SELECT a.*, b.fullname 
                        FROM task_info a
                        INNER JOIN tbl_admin b ON(a.t_user_id = b.user_id)
                        ORDER BY a.task_id DESC";
                }else{
                  $sql = "SELECT a.*, b.fullname 
                  FROM task_info a
                  INNER JOIN tbl_admin b ON(a.t_user_id = b.user_id)
                  WHERE a.t_user_id = $user_id
                  ORDER BY a.task_id DESC";
                } 
                
                  $info = $obj_admin->manage_all_info($sql);
                  $serial  = 1;
                  $num_row = $info->rowCount();
                  if($num_row==0){
                    echo '<tr><td colspan="7">لا توجد مهام</td></tr>';
                  }
                      while( $row = $info->fetch(PDO::FETCH_ASSOC) ){
              ?>
                 <td><a title="تعديل"  href="edit-task.php?task_id=<?php echo $row['task_id'];?>"><span class="glyphicon glyphicon-edit"></span></a>&nbsp;&nbsp;
                  <a title="عرض" href="task-details.php?task_id=<?php echo $row['task_id']; ?>"><span class="glyphicon glyphicon-folder-open"></span></a>&nbsp;&nbsp;
                  <?php if($user_role == 1){ ?>
                  <a title="حذف" href="?delete_task=delete_task&task_id=<?php echo $row['task_id']; ?>" onclick=" return check_delete();"><span class="glyphicon glyphicon-trash"></span></a></td>
                <?php } ?>
                <td>
                    <?php  if($row['status'] == 1){
                        echo "جاري العمل <span style='color:#d4ab3a;' class=' glyphicon glyphicon-refresh' >";
                    }elseif($row['status'] == 2){
                       echo "مكتملة <span style='color:#00af16;' class=' glyphicon glyphicon-ok' >";
                    }else{
                      echo "لا يوجد اجراء 
                      <span style='color:#d00909;' class=' glyphicon glyphicon-remove' >";
                    } ?>
                    
                  </td>
                <td><?php echo $row['t_end_time']; ?></td>
                <td><?php echo $row['t_start_time']; ?></td>
                <td><?php echo $row['fullname']; ?></td>
                <td><?php echo $row['t_title']; ?></td>
                <td><?php echo $serial; $serial++; ?></td>
                </tr>
                
                <?php } ?>
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


<?php

include("include/footer.php");



?>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script type="text/javascript">
  flatpickr('#t_start_time', {
    enableTime: true
  });

  flatpickr('#t_end_time', {
    enableTime: true
  });

</script>
