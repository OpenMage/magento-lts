<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Catalog_Index_Kill_Flag extends Mage_Core_Model_Flag
{
    protected $_flagCode = 'catalogindex_kill';

    /**
     * @return bool
     */
    public function checkIsThisProcess()
    {
        return ($this->getFlagData() == getmypid());
    }
}
