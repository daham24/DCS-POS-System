<?php
include('../config/dbCon.php');
include('../config/function.php');


if(isset($_POST['saveRepair']))
{
    // Validate and sanitize inputs
    $item_name = validate($_POST['item_name']);
    $customer_id = validate($_POST['customer_id']);
    $description = validate($_POST['description']);
    $physical_condition = isset($_POST['physical_condition']) ? implode(', ', $_POST['physical_condition']) : '';
    $received_items = isset($_POST['received_items']) ? implode(', ', $_POST['received_items']) : '';

    // Check required fields
    if($item_name != '' && $customer_id != '' && $description != '')
    {
        // Prepare data for insertion
        $data = [
            'item_name' => $item_name,
            'customer_id' => $customer_id,
            'description' => $description,
            'physical_condition' => $physical_condition,
            'received_items' => $received_items
        ];

        // Insert data into repairs table
        $result = insert('repairs', $data);

        // Check result and redirect
        if($result)
        {
            redirect('repairs.php', 'Repair Item Created Successfully!');
        }
        else
        {
            redirect('repair-create.php', 'Something Went Wrong. Please try again.');
        }
    }
    else
    {
        redirect('repair-create.php', 'Please fill all required fields.');
    }
}
else
{
    redirect('repair-create.php', 'Invalid Request.');
}

?>