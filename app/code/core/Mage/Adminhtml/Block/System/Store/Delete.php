<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Store / store view / website delete form container
 *
 * @package    Mage_Adminhtml
 *
 * @method string getStoreTypeTitle()
 */
class Mage_Adminhtml_Block_System_Store_Delete extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_objectId = 'item_id';
        $this->_mode = 'delete';
        $this->_controller = 'system_store';

        parent::__construct();

        $this->_removeButton(self::BUTTON_TYPE_SAVE);
        $this->_removeButton(self::BUTTON_TYPE_RESET);

        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'area', 'footer');
        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'onclick', 'editForm.submit();');

        $this->_addButton(self::BUTTON_TYPE_CANCEL, [
            'label'     => Mage::helper('adminhtml')->__('Cancel'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getBackUrl()),
        ], 2, 100, 'footer');
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__(
            "Delete %s '%s'",
            $this->getStoreTypeTitle(),
            $this->escapeHtml($this->getChild('form')->getDataObject()->getName()),
        );
    }

    /**
     * Set store type title
     *
     * @param  string $title
     * @return $this
     */
    public function setStoreTypeTitle($title)
    {
        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'label', Mage::helper('adminhtml')->__('Delete %s', $title));
        return $this->setData('store_type_title', $title);
    }

    /**
     * Set back URL for "Cancel" and "Back" buttons
     *
     * @param  string $url
     * @return $this
     */
    public function setBackUrl($url)
    {
        $this->setData('back_url', $url);
        $this->_updateButton(self::BUTTON_TYPE_CANCEL, 'onclick', Mage::helper('core/js')->getSetLocationJs($url));
        $this->_updateButton(self::BUTTON_TYPE_BACK, 'onclick', Mage::helper('core/js')->getSetLocationJs($url));
        return $this;
    }
}
