<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">

  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Edit Repair Item
        <a href="repairs.php" class="btn btn-primary float-end">Back</a>
      </h4>
    </div>
    <div class="card-body">

      <?php alertMessage(); ?>

      <form action="code.php" method="POST">

        <?php 
          $paramValue = checkParamId('id');
          if (!is_numeric($paramValue)) {
              echo '<h5 class="text-danger">Invalid Repair ID: ' . htmlspecialchars($paramValue) . '</h5>';
              return false;
          }

          $repair = getById('repairs', $paramValue);
          if ($repair['status'] == 200) {
            $repairData = $repair['data'];


            // Pre-fill arrays for checkboxes
            $physicalConditions = explode(', ', $repairData['physical_condition'] ?? '');
            $receivedItems = explode(', ', $repairData['received_items'] ?? '');

        ?>

          <input type="hidden" name="repairId" value="<?= htmlspecialchars($repairData['id']); ?>" />
          <div class="row">

            <!-- Item Name -->
            <div class="col-md-12 mb-3">
              <label for="item_name">Item Name *</label>
              <input type="text" id="item_name" name="item_name" required 
                     value="<?= htmlspecialchars($repairData['item_name']); ?>" class="form-control" />
            </div>

            <!-- Customer Dropdown -->
            <div class="mb-3">
                <label for="customer_id" class="form-label">Select Customer</label>
                <select id="customer_id" name="customer_id" class="form-select" required>
                    <option value="">-- Select Customer --</option>
                    <?php
                    $customers = getAll('customers');
                    foreach ($customers as $customer) {
                        $selected = ($repairData['customer_id'] == $customer['id']) ? 'selected' : '';
                        echo "<option value='{$customer['id']}' $selected>
                                {$customer['name']} ({$customer['email']}, {$customer['phone']})
                              </option>";
                    }
                    ?>
                </select>
            </div>

             <!-- Physical Condition -->
             <div class="col-md-12 mb-3">
              <label for="physical_condition" class="form-label">Physical Condition</label>
              <div id="physical_condition">
                <?php
                $conditions = [
                  "Water logged", "No power", "Signal issues", "Charging issues", 
                  "Display Damage", "Mic issues", "Speaker issues", "Battery issues", 
                  "Volume key issues", "Software issues", "Camera issues", "Other"
                ];
                foreach ($conditions as $condition) {
                  $checked = in_array($condition, $physicalConditions) ? 'checked' : '';
                  echo "<div><input type='checkbox' name='physical_condition[]' value='$condition' $checked> $condition</div>";
                }
                ?>
              </div>
            </div>

            <!-- Received Items -->
            <div class="col-md-12 mb-3">
              <label for="received_items" class="form-label">Received Items</label>
              <div id="received_items">
                <?php
                $items = [
                  "Battery", "Charger", "Hands free", "Data cable", 
                  "Memory card", "Handset", "Sim", "Other"
                ];
                foreach ($items as $item) {
                  $checked = in_array($item, $receivedItems) ? 'checked' : '';
                  echo "<div><input type='checkbox' name='received_items[]' value='$item' $checked> $item</div>";
                }
                ?>
              </div>
            </div>

            <!-- Description -->
            <div class="col-md-12 mb-3">
              <label for="description">Description (Issue) *</label>
              <textarea id="description" name="description" required class="form-control"><?= htmlspecialchars($repairData['description']); ?></textarea>
            </div>

           

            <!-- Status Checkbox -->
            <div class="col-md-6 mb-3">
              <label for="status">Status (Unchecked = Pending, Checked = Completed)</label>
              <br>
              <input type="checkbox" id="status" name="status" 
                     <?= $repairData['status'] == true ? 'checked' : ''; ?> 
                     style="width: 30px; height: 30px;">
            </div>

            <!-- Submit Button -->
            <div class="col-md-6 mb-3 text-end">
              <br>
              <button type="submit" name="updateRepair" class="btn btn-primary">Update</button>
            </div>

          </div>

        <?php
          } else {
              echo '<h5 class="text-danger">' . htmlspecialchars($repair['message']) . '</h5>';
              return false;
          }
        ?>

      </form>
    </div>
  </div>

</div>

<?php include('includes/footer.php'); ?>
