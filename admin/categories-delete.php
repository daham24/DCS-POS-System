<?php 

require '../config/function.php';

$paraResultId = checkParamId('id');

if (is_numeric($paraResultId)) {

    $categoryId = validate($paraResultId);

    // Fetch the category by ID
    $category = getById('categories', $categoryId);

    if ($category['status'] == 200) {
        // Fetch all subcategories related to this category
        $subCategories = getByColumn('sub_categories', 'category_id', $categoryId);

        if ($subCategories['status'] == 200 && !empty($subCategories['data'])) {
            // Delete all subcategories
            foreach ($subCategories['data'] as $subCategory) {
                $subCategoryDeleteRes = delete('sub_categories', $subCategory['id']);
                if (!$subCategoryDeleteRes) {
                    redirect('categories.php', 'Failed to delete subcategories. Please try again.');
                }
            }
        }

        // Delete the main category
        $categoryDeleteRes = delete('categories', $categoryId);
        if ($categoryDeleteRes) {
            redirect('categories.php', 'Category and its subcategories deleted successfully!');
        } else {
            redirect('categories.php', 'Something Went Wrong.');
        }

    } else {
        redirect('categories.php', $category['message']);
    }

} else {
    redirect('categories.php', 'Something Went Wrong.');
}

?>