<?php
include('../config/dbCon.php'); 

if (isset($_GET['category_id'])) {
    $categoryId = $_GET['category_id'];

    // Fetch subcategories based on the selected category
    $query = "SELECT * FROM sub_categories WHERE category_id = $categoryId";
    $result = mysqli_query($conn, $query);

    $subcategories = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subcategories[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
    }

    // Return subcategories as JSON
    header('Content-Type: application/json');
    echo json_encode($subcategories);
} else {
    echo json_encode([]);
}
?>