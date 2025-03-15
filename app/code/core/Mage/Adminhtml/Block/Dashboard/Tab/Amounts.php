<?php

/**
 * Adminhtml dashboard order amounts diagram
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Tab_Amounts extends Mage_Adminhtml_Block_Dashboard_Graph
{
    protected $_axisMaps = [
        'x' => 'range',
        'y' => 'revenue',
    ];

    /**
     * Initialize object
     */
    public function __construct()
    {
        $this->setHtmlId('amounts');
        parent::__construct();
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->setDataRows('revenue');
        parent::_prepareData();
    }
}
