<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

$this->deleteConfigData(Mage_Rss_Helper_Data::XML_PATH_RSS_ACTIVE);

$installer->endSetup();
