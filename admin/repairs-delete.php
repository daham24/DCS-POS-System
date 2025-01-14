<?php 

require '../config/function.php';

$paraResultId = checkParamId('id');

if(is_numeric($paraResultId)){

  $repairId = validate($paraResultId);

  $repairProduct = getById('repairs', $repairId);

  if($repairProduct['status'] == 200)
  {
    $repairsDeleteRes = delete('repairs', $repairId);
    if($repairsDeleteRes)
    {
      redirect('repairs.php', 'Repair Item Deleted Successfully!');
    }else{
      redirect('repairs.php', 'Something Went Wrong.');
    }

  }else{
    redirect('repairs.php', $repairProduct['message']);
  }
  

}else{
  redirect('repairs.php', 'Something Went Wrong.');
}


?>