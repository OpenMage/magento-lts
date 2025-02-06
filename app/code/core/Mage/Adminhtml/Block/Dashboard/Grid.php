<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Setting default for every grid on dashboard
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/grid.phtml');
        $this->setDefaultLimit(5);
    }
}
