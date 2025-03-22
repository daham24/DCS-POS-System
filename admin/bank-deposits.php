<?php 
include('includes/header.php'); 
?>



<div class="container-fluid px-4 mt-4 mb-3">

    <?php
    // Display message if exists
    if (isset($_SESSION['message'])) {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['message'] . '</div>';
      unset($_SESSION['message']);
    } 
    ?>

    <!-- Add Bank and Account Modal -->
    <div class="modal fade" id="addBankAccountModal" tabindex="-1" aria-labelledby="addBankAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBankAccountModalLabel">Add Bank and Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add Bank Form -->
                    <form method="POST" action="bank-deposit-process.php">
                        <h5>Add Bank</h5>
                        <div class="mb-3">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" required>
                        </div>
                        <button type="submit" name="add_bank" class="btn btn-primary mt-2">Add Bank</button>
                    </form>
                    
                    <!-- Add Account Form -->
                    <form method="POST" action="bank-deposit-process.php">
                        <h5 class="mt-4">Add Account</h5>
                        <div class="mb-3">
                            <label for="bank_id">Select Bank</label>
                            <select name="bank_id" class="form-select" required>
                                <option value="">Select a Bank</option>
                                <?php
                                    // Fetch banks
                                    $banks = mysqli_query($conn, "SELECT * FROM banks");
                                    while ($bank = mysqli_fetch_assoc($banks)) {
                                        echo "<option value='{$bank['id']}'>{$bank['name']}</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="account_number">Account Number</label>
                            <input type="text" name="account_number" class="form-control" required>
                        </div>
                        <button type="submit" name="add_account" class="btn btn-primary mt-2">Add Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Card for Adding Daily Deposit -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Add Daily Deposit</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankAccountModal">Add Bank/Account</button>
        </div>
        <div class="card-body">
            <form method="POST" action="bank-deposit-process.php">
                <!-- Bank Selection -->
                <div class="mb-3">
                    <label for="bank_id">Select Bank</label>
                    <select name="bank_id" class="form-select" id="bank_id" required>
                        <option value="">Select a Bank</option>
                        <?php
                            // Fetch banks
                            $banks = mysqli_query($conn, "SELECT * FROM banks");
                            while ($bank = mysqli_fetch_assoc($banks)) {
                                echo "<option value='{$bank['id']}'>{$bank['name']}</option>";
                            }
                        ?>
                    </select>
                </div>

                <!-- Account Selection (Dynamically Loaded) -->
                <div class="mb-3">
                    <label for="account_id">Select Account</label>
                    <select name="account_id" class="form-select" id="account_id" required>
                        <option value="">Select a Bank First</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="deposit_amount">Deposit Amount</label>
                    <input type="number" name="deposit_amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="deposit_date">Deposit Date</label>
                    <input type="date" name="deposit_date" class="form-control" required>
                </div>
                <button type="submit" name="add_deposit" class="btn btn-primary mt-2">Add Deposit</button>
            </form>
        </div>
    </div>

    <!-- Table to Display Deposits -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">All Deposits</h4>
        </div>
        <div class="card-body">
            <?php
                // Query to fetch deposits
                $query = "SELECT dd.*, ba.account_number, b.name AS bank_name 
                          FROM daily_deposits dd
                          JOIN bank_accounts ba ON dd.bank_account_id = ba.id
                          JOIN banks b ON ba.bank_id = b.id
                          ORDER BY dd.deposit_date DESC";
                $deposits = mysqli_query($conn, $query);
            ?>
            <table class="table table-striped table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>Deposit Amount</th>
                        <th>Deposit Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($deposit = mysqli_fetch_assoc($deposits)) : ?>
                        <tr>
                            <td><?= $deposit['bank_name']; ?></td>
                            <td><?= $deposit['account_number']; ?></td>
                            <td><?= $deposit['deposit_amount']; ?></td>
                            <td><?= $deposit['deposit_date']; ?></td>
                            <td>
                                <a href="bank-deposit-edit.php?id=<?= $deposit['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="bank-deposit-delete.php?id=<?= $deposit['id']; ?>" class="btn btn-danger btn-sm delete-btn" data-id="<?= $deposit['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- JavaScript to Load Accounts Based on Selected Bank -->
<script>
document.getElementById('bank_id').addEventListener('change', function() {
    const bankId = this.value;
    
    // Get accounts related to selected bank
    if (bankId) {
        fetch('bank-deposit-process.php?bank_id=' + bankId)
            .then(response => response.json())
            .then(data => {
                const accountSelect = document.getElementById('account_id');
                accountSelect.innerHTML = '<option value="">Select an Account</option>';
                
                // Populate account options
                data.forEach(account => {
                    const option = document.createElement('option');
                    option.value = account.id;
                    option.textContent = account.account_number;
                    accountSelect.appendChild(option);
                });
            });
    } else {
        // Clear account options if no bank selected
        document.getElementById('account_id').innerHTML = '<option value="">Select a Bank First</option>';
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert for delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default link behavior

            const deleteUrl = this.getAttribute('href'); // Get the URL for the delete action

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


