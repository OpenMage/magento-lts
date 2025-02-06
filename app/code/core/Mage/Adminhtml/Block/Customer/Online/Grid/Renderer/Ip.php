<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers online grid block item renderer by ip.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function render(Varien_Object $row)
    {
        /**
         * The output of the "inet_ntop" function was disabled to prevent an error throwing
         * in case when the database value is not an ipv6 or an ipv4 binary representation (ex. NULL).
         */
        return @inet_ntop($row->getData($this->getColumn()->getIndex()));
    }
}
