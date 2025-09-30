<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter subscribers grid website filter
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @var Mage_Core_Model_Resource_Website_Collection|null
     */
    protected $_websiteCollection = null;

    /**
     * @return array[]
     */
    protected function _getOptions()
    {
        $result = $this->getCollection()->toOptionArray();
        array_unshift($result, ['label' => null, 'value' => null]);
        return $result;
    }

    /**
     * @return Mage_Core_Model_Resource_Website_Collection
     * @throws Mage_Core_Exception
     */
    public function getCollection()
    {
        if (is_null($this->_websiteCollection)) {
            $this->_websiteCollection = Mage::getResourceModel('core/website_collection')
                ->load();
        }

        Mage::register('website_collection', $this->_websiteCollection);

        return $this->_websiteCollection;
    }

    /**
     * @return array|null
     * @throws Mage_Core_Exception
     */
    public function getCondition()
    {
        $id = $this->getValue();
        if (!$id) {
            return null;
        }

        $website = Mage::app()->getWebsite($id);

        return ['in' => $website->getStoresIds(true)];
    }
}
