<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rss
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$this->deleteConfigData(Mage_Rss_Helper_Data::XML_PATH_RSS_ACTIVE);

$installer->endSetup();
