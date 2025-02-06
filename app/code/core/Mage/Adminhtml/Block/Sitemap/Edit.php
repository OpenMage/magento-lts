<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Sitemap edit form container
 *
 * @category   Mage
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

        $this->_addButton('generate', [
            'label'   => Mage::helper('adminhtml')->__('Save & Generate'),
            'onclick' => "$('generate').value=1; editForm.submit();",
            'class'   => 'add',
        ]);
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('sitemap_sitemap')->getId()) {
            return Mage::helper('sitemap')->__('Edit Sitemap');
        }
        return Mage::helper('sitemap')->__('New Sitemap');
    }
}
