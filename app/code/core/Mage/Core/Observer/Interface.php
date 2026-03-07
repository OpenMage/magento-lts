<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Observer interface
 *
 * @category   Mage
 * @package    Mage_Core
 */
interface Mage_Core_Observer_Interface
{
    public function execute(Varien_Event_Observer $observer): void;
}
