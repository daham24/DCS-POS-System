<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Admins/Staff
          <a href="admins-create.php" class="btn btn-primary float-end">Add Admin/Staff</a>
        </h4>
      </div>
      <div class="card-body">
        <?php alertMessage(); ?> 

        <?php
              $admins = getAll('admins');
              if(!$admins){
                echo '<h4>Something Went Wrong!</h4>';
                return false;
              }
              if(mysqli_num_rows($admins) > 0)
              {
              ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th> <!-- New column for is_ban -->
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              
                <?php foreach($admins as $adminItem) : ?>
                <tr>
                  <td><?= $adminItem['name']?></td>
                  <td><?= $adminItem['email']?></td>
                  <td>
                    <?php if($adminItem['is_ban'] == 1): ?>
                      <span class="badge bg-danger">Inactive</span> <!-- Display for banned -->
                    <?php else: ?>
                      <span class="badge bg-success">Active</span> <!-- Display for active -->
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="admins-edit.php?id=<?=$adminItem['id']?>" class="btn btn-success btn-sm">Edit</a>
                    <a 
                        href="admins-delete.php?id=<?= $adminItem['id'] ?>" 
                        class="btn btn-danger btn-sm delete-btn" 
                        data-delete-url="admins-delete.php?id=<?= $adminItem['id'] ?>"
                    >
                        Delete
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
    
            </tbody>
          </table>
        </div>
        <?php 
              }
              else
              {
                ?>
                  <h4 class="mb-0">No Record Found</h4>
                <?php
              }
              ?>
      </div>
  </div>
  
</div>

<?php include('includes/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert for delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default link behavior

            const deleteUrl = this.getAttribute('data-delete-url');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl; // Redirect to delete URL
                }
            });
        });
    });
});
</script>