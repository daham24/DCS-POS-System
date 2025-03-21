<?php include('includes/header.php'); ?>
<div class="ccontainer-fluid px-4 mt-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4>Add Repair Item</h4>
        </div>
        <div class="card-body">
            <form action="repairs-process.php" method="POST">
                <div class="mb-3">
                    <label for="item_name" class="form-label">Item Name</label>
                    <input type="text" id="item_name" name="item_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Select Customer</label>
                    <select id="customer_id" name="customer_id" class="form-select" required>
                        <option value="">-- Select Customer --</option>
                        <?php
                        $customers = getAll('customers');
                        foreach ($customers as $customer) {
                            echo "<option value='{$customer['id']}'>{$customer['name']} ({$customer['email']}, {$customer['phone']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="physical_condition" class="form-label">Physical Condition</label>
                    <div id="physical_condition">
                        <div><input type="checkbox" name="physical_condition[]" value="Water logged"> Water logged</div>
                        <div><input type="checkbox" name="physical_condition[]" value="No power"> No power</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Signal issues"> Signal issues</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Charging issues"> Charging issues</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Display Damage"> Display Damage</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Mic issues"> Mic issues</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Speaker issues"> Speaker issues</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Battery issues"> Battery issues</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Volume key issues"> Volume key issues</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Software issues"> Software issues</div>
                        <div><input type="checkbox" name="physical_condition[]" value="Camera issues"> Camera issues</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="received_items" class="form-label">Received Items</label>
                    <div id="received_items">
                        <div><input type="checkbox" name="received_items[]" value="Battery"> Battery</div>
                        <div><input type="checkbox" name="received_items[]" value="Charger"> Charger</div>
                        <div><input type="checkbox" name="received_items[]" value="Hands free"> Hands free</div>
                        <div><input type="checkbox" name="received_items[]" value="Data cable"> Data cable</div>
                        <div><input type="checkbox" name="received_items[]" value="Memory card"> Memory card</div>
                        <div><input type="checkbox" name="received_items[]" value="Handset"> Handset</div>
                        <div><input type="checkbox" name="received_items[]" value="Sim"> Sim</div>
                        <div><input type="checkbox" name="received_items[]" value="Sim Tary"> Sim Tray</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Additional Info</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="saveRepair" class="btn btn-primary">Save Repair</button>
            </form>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>