<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml system templates page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Email_Template extends Mage_Adminhtml_Block_Template
{
    public const BLOCK_GRID = 'grid';

    protected $_template = 'system/email/template/list.phtml';

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            self::BLOCK_GRID,
            $this->getLayout()->createBlock('adminhtml/system_email_template_grid', 'email.template.grid')
        );

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_ADD, $this->getButtonAddBlock());
    }

    public function getButtonAddBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonAddBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Add New Template'))
            ->setOnClick("window.location='" . $this->getCreateUrl() . "'");
    }

    /**
     * Get URL for create new email template
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * Get transactional emails page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Transactional Emails');
    }
}
