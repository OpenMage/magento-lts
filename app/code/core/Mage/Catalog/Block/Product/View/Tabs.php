<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
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
