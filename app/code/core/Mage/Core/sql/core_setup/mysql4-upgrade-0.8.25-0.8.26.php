<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

$currentVersion = Mage::getVersion();
if (version_compare($currentVersion, '1.3.9') < 0) {
    echo 'Exiting ...';
    exit();
}
