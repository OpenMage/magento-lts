<?php 

function getInput($minLength = '1')
{
	$handle = fopen ("php://stdin","r");
	$line = fgets($handle);
	if(strlen(trim($line)) < $minLength){
		echo "error - ABORTING!\n";
		exit;
	}	
	fclose($handle);

	return trim($line);
	
}

echo "Please input new customer email: ";
$email_address = getInput('5');

echo "Please input increment ID: ";
$incrementID = getInput('9');


/****************************************/

require_once 'public_html/app/Mage.php';
umask(0); 
Mage::init();

if(isset($email_address) && isset($incrementID))
{
	try {
		$order = Mage::getModel('sales/order')->loadByIncrementId($incrementID);
		$order->setCustomerEmail($email_address)->save();
	} catch (Exception $e) {
			 echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
} else {
	echo "insufficient data provided \n";
}
