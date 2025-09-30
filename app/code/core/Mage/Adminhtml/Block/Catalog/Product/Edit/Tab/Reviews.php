<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Reviews products admin grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Reviews extends Mage_Adminhtml_Block_Review_Grid
{
    /**
     * Hide grid mass action elements
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Determine ajax url for grid refresh
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/reviews', ['_current' => true]);
    }
}
