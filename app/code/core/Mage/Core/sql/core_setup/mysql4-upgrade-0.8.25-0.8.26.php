<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

$currentVersion = Mage::getVersion();
if (version_compare($currentVersion, '1.3.9') < 0) {
    echo 'Exiting ...';
    exit();
}
