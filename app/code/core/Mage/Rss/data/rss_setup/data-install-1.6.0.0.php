<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;

$this->deleteConfigData(Mage_Rss_Helper_Data::XML_PATH_RSS_ACTIVE);

$installer->endSetup();
