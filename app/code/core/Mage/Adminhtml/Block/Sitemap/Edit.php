<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Sitemap edit form container
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sitemap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init container
     */
    public function __construct()
    {
        $this->_objectId = 'sitemap_id';
        $this->_controller = 'sitemap';

        parent::__construct();

        $this->_addPreparedButton(
            id: 'generate',
            label: Mage::helper('adminhtml')->__('Save & Generate'),
            class: 'add generate',
            onClick: "$('generate').value=1; editForm.submit();",
        );
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        if (Mage::registry('sitemap_sitemap')->getId()) {
            return Mage::helper('sitemap')->__('Edit Sitemap');
        }

        return Mage::helper('sitemap')->__('New Sitemap');
    }
}
