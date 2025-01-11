<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering buttons
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Attribute_Buttons extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('api2/attribute/buttons.phtml');
    }

    /**
     * Prepare global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $buttons = [
            'backButton'    => [
                'label'     => $this->__('Back'),
                'onclick'   => sprintf("window.location.href='%s';", $this->getUrl('*/*/')),
                'class'     => 'back',
            ],
            'saveButton'    => [
                'label'     => $this->__('Save'),
                'onclick'   => 'form.submit(); return false;',
                'class'     => 'save',
            ],
        ];

        foreach ($buttons as $name => $data) {
            $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData($data);
            $this->setChild($name, $button);
        }

        return parent::_prepareLayout();
    }

    /**
     * Get back button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    /**
     * Get reset button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    /**
     * Get save button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    /**
     * Get block caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->__('Edit');
    }
}
