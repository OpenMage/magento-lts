<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product information tabs
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_View_Tabs extends Mage_Core_Block_Template
{
    protected $_tabs = [];

    /**
     * Add tab to the container
     *
     * @param  string     $alias
     * @param  string     $title
     * @param  string     $block
     * @param  string     $template
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
