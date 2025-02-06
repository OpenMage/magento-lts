<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Page
 */

/**
 * Abstract container block with header
 *
 * @category   Mage
 * @package    Mage_Page
 */
class Mage_Page_Block_Template_Container extends Mage_Core_Block_Template
{
    /**
     * Set default template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('page/template/container.phtml');
    }
}
