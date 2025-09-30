<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Attribute Block with additional js scripts in template
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Block_Adminhtml_Attribute_Edit_Js extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('eav/attribute/edit/js.phtml');
    }
}
