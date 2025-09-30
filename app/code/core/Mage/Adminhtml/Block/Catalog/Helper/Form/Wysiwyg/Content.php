<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Textarea attribute WYSIWYG content
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg_Content extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form.
     * Adding editor field to render
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(['id' => 'wysiwyg_edit_form', 'action' => $this->getData('action'), 'method' => 'post']);

        $config['document_base_url']     = $this->getData('store_media_url');
        $config['store_id']              = $this->getData('store_id');
        $config['add_variables']         = false;
        $config['add_widgets']           = false;
        $config['add_directives']        = true;
        $config['use_container']         = true;
        $config['container_class']       = 'hor-scroll';

        $form->addField($this->getData('editor_element_id'), 'editor', [
            'name'      => 'content',
            'style'     => 'height:460px',
            'required'  => true,
            'force_load' => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig($config),
        ]);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
