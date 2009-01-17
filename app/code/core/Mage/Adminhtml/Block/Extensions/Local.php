<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage installed extensions
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Extensions_Local extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'extensions_local';
        $this->_headerText = Mage::helper('adminhtml')->__('Manage Installed Extensions');

        parent::__construct();

        $this->_removeButton('add');

        $this->_addButton('upgrade_all', array(
            'label'=>$this->__("Upgrade all available packages"),
            'onclick'=>"setLocation('".$this->getUrl('*/extensions_local/upgradeAll')."')",
            'class'=>'add',
        ));

        $this->_addButton('file', array(
            'label'=>$this->__("Install Package File"),
            'onclick'=>"setLocation('".$this->getUrl('*/extensions_file')."')",
            'class'=>'add',
        ));

        $this->_addButton('remote', array(
            'label'=>$this->__("Browse Available Extensions"),
            'onclick'=>"setLocation('".$this->getUrl('*/extensions_remote')."')",
            'class'=>'add',
        ));
    }
}
