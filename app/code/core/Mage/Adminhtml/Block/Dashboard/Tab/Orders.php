<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard orders diagram
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Tab_Orders extends Mage_Adminhtml_Block_Dashboard_Graph
{
    protected $_axisMaps = [
        'x' => 'range',
        'y' => 'quantity',
    ];

    public function __construct()
    {
        $this->setHtmlId('orders');
        parent::__construct();
    }

    /**
     * Prepare chart data
     *
     * @return void
     * @throws Exception
     */
    protected function _prepareData()
    {
        $this->setDataRows('quantity');
        parent::_prepareData();
    }
}
