<?php

declare(strict_types=1);

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions orphaned resource block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_OrphanedResource extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'permissions_orphanedResource';
        $this->_headerText = Mage::helper('adminhtml')->__('Orphaned Role Resources');
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _toHtml(): string
    {
        Mage::dispatchEvent('permissions_orphanedresource_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
