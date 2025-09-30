<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales widget search form for orders and returns block
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Widget_Guest_Form extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function isEnable()
    {
        return !(Mage::getSingleton('customer/session')->isLoggedIn());
    }

    /**
     * Select element for choosing registry type
     *
     * @return array
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setData([
                'id'    => 'quick_search_type_id',
                'class' => 'select guest-select',
            ])
            ->setName('oar_type')
            ->setOptions($this->_getFormOptions())
            ->setExtraParams('onchange="showIdentifyBlock(this.value);"');
        return $select->getHtml();
    }

    /**
     * Get Form Options for Guest
     *
     * @return array
     */
    protected function _getFormOptions()
    {
        $options = $this->getData('identifymeby_options');
        if (is_null($options)) {
            $options = [];
            $options[] = [
                'value' => 'email',
                'label' => 'Email Address',
            ];
            $options[] = [
                'value' => 'zip',
                'label' => 'ZIP Code',
            ];
            $this->setData('identifymeby_options', $options);
        }

        return $options;
    }

    /**
     * Return quick search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('sales/guest/view', ['_secure' => $this->_isSecure()]);
    }
}
