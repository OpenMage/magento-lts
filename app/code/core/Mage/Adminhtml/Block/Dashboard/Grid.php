<?php
/**
 * Adminhtml dashboard grid
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
