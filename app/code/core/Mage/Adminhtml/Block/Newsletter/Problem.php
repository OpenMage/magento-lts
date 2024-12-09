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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter problem block template.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Problem extends Mage_Adminhtml_Block_Template
{
    public const BLOCK_GRID = 'grid';

    public const BUTTON_DELETE      = 'deleteButton';
    public const BUTTON_UNSUBSCRIBE = 'unsubscribeButton';

    protected $_template = 'newsletter/problem/list.phtml';

    public function __construct()
    {
        parent::__construct();

        Mage::getResourceSingleton('newsletter/problem_collection')
            ->addSubscriberInfo()
            ->addQueueInfo();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            self::BLOCK_GRID,
            $this->getLayout()->createBlock('adminhtml/newsletter_problem_grid', 'newsletter.problem.grid')
        );

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_DELETE, $this->getButtonDeleteBlock());
        $this->setChild(self::BUTTON_UNSUBSCRIBE, $this->getButtonUnsubscribeBlock());
    }

    public function getButtonDeleteBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_DELETE, 'del.button')
            ->setLabel(Mage::helper('newsletter')->__('Delete Selected Problems'))
            ->setOnClick('problemController.deleteSelected();');
    }

    public function getButtonUnsubscribeBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock('unsubscribe.button')
            ->setLabel(Mage::helper('newsletter')->__('Unsubscribe Selected'))
            ->setOnClick('problemController.unsubscribe();');
    }

    public function getUnsubscribeButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_UNSUBSCRIBE);
    }

    public function getShowButtons()
    {
        return  Mage::getResourceSingleton('newsletter/problem_collection')->getSize() > 0;
    }
}
