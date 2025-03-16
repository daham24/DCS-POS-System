<?php 

require '../config/function.php';

$paraResultId = checkParamId('id');

if(is_numeric($paraResultId)){

  $supplierId = validate($paraResultId);

  $supplier = getById('suppliers', $supplierId);

  if($supplier['status'] == 200)
  {
    $supplierDeleteRes = delete('suppliers', $supplierId);
    if($supplierDeleteRes)
    {
      redirect('suppliers.php', 'Supplier Deleted Successfully!');
    }else{
      redirect('suppliers.php', 'Something Went Wrong.');
    }

  }else{
    redirect('suppliers.php', $supplier['message']);
  }
  

}else{
  redirect('suppliers.php', 'Something Went Wrong.');
}


?>