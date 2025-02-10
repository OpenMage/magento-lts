<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Catalog_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_catalog_form');
        $this->setTitle(Mage::helper('catalogrule')->__('Rule Information'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']);
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
