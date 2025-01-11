<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product information tabs
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_View_Tabs extends Mage_Core_Block_Template
{
    protected $_tabs = [];

    /**
     * Add tab to the container
     *
     * @param string $alias
     * @param string $title
     * @param string $block
     * @param string $template
     * @return false|void
     */
    public function addTab($alias, $title, $block, $template)
    {
        if (!$title || !$block || !$template) {
            return false;
        }

        $this->_tabs[] = [
            'alias' => $alias,
            'title' => $title,
        ];

        $this->setChild(
            $alias,
            $this->getLayout()->createBlock($block, $alias)
                ->setTemplate($template),
        );
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        return $this->_tabs;
    }
}
