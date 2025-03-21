<?php 

require '../config/function.php'; 


$paraResultId = checkParamId('id');


if (is_numeric($paraResultId)) {

  $returnId = validate($paraResultId);

  // Get the return item details
  $return = getById('returns', $returnId); 

  // Check if the return item exists
  if ($return['status'] == 200) {
    // Delete the return item from the 'returns' table
    $returnDeleteRes = delete('returns', $returnId);

    if ($returnDeleteRes) {
      // Redirect with success message if deletion was successful
      redirect('return-items-view.php', 'Return Item Deleted Successfully!');
    } else {
      // Redirect with error message if something went wrong
      redirect('return-items-view.php', 'Something Went Wrong.');
    }

  } else {
    // Redirect if return item not found
    redirect('return-items-view.php', $return['message']);
  }

} else {
  // Redirect if no valid ID is provided
  redirect('return-items-view.php', 'Invalid Return ID.');
}

?>
