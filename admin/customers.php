<?php include('includes/header.php');?>

<div class="container-fluid px-4">

  <div class="card mt-4 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Customers</h4>
        <!-- Search Form -->
        <form class="d-flex" method="GET" action="">
          <input 
            type="text" 
            name="search" 
            class="form-control me-2" 
            placeholder="Search by phone number..." 
            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
          >
          <button type="submit" class="btn btn-dark">Search</button>
        </form>
        <a href="customers-create.php" class="btn btn-primary">Add Customer</a>
      </div>
      <div class="card-body">
        <?php alertMessage(); ?> 

        <?php
          // Fetch customers based on search
          $searchQuery = isset($_GET['search']) ? validate($_GET['search']) : '';

          $query = "SELECT * FROM customers WHERE 1=1";

          if($searchQuery) {
            $query .= " AND phone LIKE '%$searchQuery%'";
          }

          $customers = mysqli_query($conn, $query);

          if(!$customers){
            echo '<h4>Something Went Wrong!</h4>';
            return false;
          }
          if(mysqli_num_rows($customers) > 0)
          {
        ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email Id</th>
                <th>Phone</th>
                <th>Status</th> <!-- New column for is_ban -->
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              
                <?php foreach($customers as $item) : ?>
                <tr>
                  <td><?= $item['id']?></td>
                  <td><?= $item['name']?></td>  
                  <td><?= $item['email']?></td>  
                  <td><?= $item['phone']?></td>           
                  <td>
                    <?php
                      if($item['status'] == 1){
                        echo '<span class="badge bg-danger">Hidden</span>';
                      }else{
                        echo '<span class="badge bg-primary">Visible</span>';
                      }                    
                    ?>
                  </td>
                  <td>
                    <a href="customers-edit.php?id=<?=$item['id']?>" class="btn btn-success btn-sm">Edit</a>
                    <a 
                        href="customers-delete.php?id=<?=$item['id']?>" 
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this customer?')"
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

<?php include('includes/footer.php');?>