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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Extensions_Custom_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Default collection limit
     *
     * @var int
     */
    protected $_defaultLimit = 200;

    /**
     * Initialize collection and grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('extensions_custom_edit_grid');
        $this->setUseAjax(true);
        $this->setCollection(Mage::getModel('adminhtml/extension_collection'));
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Adminhtml_Block_Extensions_Custom_Edit_Tab_Grid
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('folder', array(
            'header'  => Mage::helper('adminhtml')->__('Folder'),
            'index'   => 'folder',
            'width'   => 100,
            'type'    => 'options',
            'options' => $this->getCollection()->collectFolders()
        ));

        $this->addColumn('package', array(
            'header' => Mage::helper('adminhtml')->__('Package'),
            'index'  => 'package',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Self URL getter
     *
     * @return string
     */
    public function getCurrentUrl($params=array())
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Row URL getter
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/load') . '?id=' . urlencode($row->getFilenameId());
    }
}
