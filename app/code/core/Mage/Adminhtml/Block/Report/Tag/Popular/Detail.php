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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tag detail report blocks content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Tag_Popular_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_tag_popular_detail';
        $tag = Mage::getModel('tag/tag')->load($this->getRequest()->getParam('id'));
        $this->_headerText = Mage::helper('reports')->__('Tag "%s" details', $this->escapeHtml($tag->getName()));
        parent::__construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/popular/'));
        $this->_addBackButton();
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
