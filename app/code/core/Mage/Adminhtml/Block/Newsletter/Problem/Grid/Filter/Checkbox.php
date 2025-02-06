<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter subscribers grid filter checkbox
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Problem_Grid_Filter_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getCondition()
    {
        return [];
    }

    public function getHtml()
    {
        return '<input type="checkbox" onclick="problemController.checkCheckboxes(this)"/>';
    }
}
