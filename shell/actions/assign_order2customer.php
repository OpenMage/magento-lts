<?php 

require_once 'public_html/app/Mage.php';
umask(0); 
Mage::init();


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

function getStoreViewList()
{
    $_store = array();
    foreach (Mage::app()->getWebsites() as $website) {
        foreach ($website->getGroups() as $group) {
            $stores = $group->getStores();
            foreach ($stores as $store) {
                $_store[] = array("id" => $store->getWebsiteId(), "name" => $store->getName());
            }
        }
    }
	
    return $_store;
}

// Print stores list
$allStoresArray = getStoreViewList();
foreach($allStoresArray as $store)
{
	echo "[". $store['id'] . "] " . $store['name'] . "\r\n";
}


echo "Please input Website ID: ";
$websiteID = getInput();

echo "Please input customer email: ";
$customeremailid = getInput('5');


/****************************************/

if(isset($customeremailid) && isset($websiteID))
{
	$toCustomer = Mage::getModel('customer/customer')
		->setWebsiteId($websiteID)
		->loadByEmail($customeremailid);


	$orders = Mage::getModel('sales/order')->getCollection()
		->addAttributeToFilter('store_id',$websiteID)
		->addAttributeToFilter('customer_is_guest', array('nin' => array(0)))
		->addAttributeToFilter('customer_email', $customeremailid);

		
	foreach($orders as $order)
	{
		try {
			$orderbyid = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);

			$orderbyid->setCustomerId($toCustomer->getId());
			$orderbyid->setCustomerFirstname($toCustomer->getFirstname());
			$orderbyid->setCustomerLastname($toCustomer->getLastname());
			$orderbyid->setCustomerEmail($toCustomer->getEmail());
			$orderbyid->setCustomerIsGuest(0);
			$orderbyid->save();
			
			echo $order['increment_id'] . " saved!\n";
			
		} catch (Exception $e) {
			 echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
}
