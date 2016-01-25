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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tab for Content Management
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Content
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * List of static CMS pages
     *
     * @var array
     */
    protected $_pages;

    /**
     * Class constructor
     * Setting view option
     */
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
        $this->setTemplate('/xmlconnect/edit/tab/content.phtml');
        $this->_pages = Mage::getResourceModel('xmlconnect/cms_page_collection')->toOptionIdArray();
    }

    /**
     * Add page input to fieldset
     *
     * @deprecated will delete in the next version
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @return null
     */
    protected function _addPage($fieldset, $fieldPrefix)
    {
        $element = $fieldset->addField($fieldPrefix, 'page', array('name' => $fieldPrefix));
        $element->initFields(array('name' => $fieldPrefix, 'values' => $this->_pages));
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $this->_prepareButtons();
        return parent::_prepareForm();
    }

    /**
     * Prepare add and delete buttons for content tab
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Content
     */
    protected function _prepareButtons()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label'     => $this->__('Add Page'),
            'onclick'   => 'cmsPageActionHelper.insertPage(); return false;',
            'class'     => 'add'
        ))->setName('add_page_item_button');

        $this->setChild('add_button', $addButton);

        $deleteButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label'     => $this->__('Delete'),
            'onclick'   => "$(\'config_data[{{deleteId}}][tr]\').remove(); return false;",
            'class'     => 'delete'
        ))->setName('add_page_item_button');

        $this->setChild('delete_button', $deleteButton);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Content');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return (bool) !Mage::getSingleton('adminhtml/session')->getNewApplication();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return cms page list
     *
     * @return array
     */
    public function getPages()
    {
        return $this->_pages;
    }

    /**
     * Return saved static page list
     *
     * @return array
     */
    public function getStaticPageList()
    {
        return Mage::getSingleton('xmlconnect/configuration')->getDeviceStaticPages();
    }
}
