<?php
/**
 * Adminhtml dashboard order amounts diagram
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Tab_Amounts extends Mage_Adminhtml_Block_Dashboard_Graph
{
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
        $this->setDataHelperName('adminhtml/dashboard_order');
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $this->getDataHelper()->setParam('website', $this->getRequest()->getParam('website'));
        $this->getDataHelper()->setParam('group', $this->getRequest()->getParam('group'));

        $this->setDataRows('revenue');
        $this->_axisMaps = [
            'x' => 'range',
            'y' => 'revenue',
        ];

        parent::_prepareData();
    }
}
