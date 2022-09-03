<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recurring profiles listing
 *
 * @method $this setBackUrl(string $value)
 * @method $this setGridColumns(Varien_Object[] $profiles)
 * @method $this setGridElements(Varien_Object[] $profiles)
 */
class Mage_Sales_Block_Recurring_Profiles extends Mage_Core_Block_Template
{
    /**
     * Profiles collection
     *
     * @var Mage_Sales_Model_Resource_Recurring_Profile_Collection
     */
    protected $_profiles = null;

    /**
     * Prepare profiles collection and render it as grid information
     */
    public function prepareProfilesGrid()
    {
        $this->_prepareProfiles(['reference_id', 'state', 'created_at', 'updated_at', 'method_code']);

        $pager = $this->getLayout()->createBlock('page/html_pager')
            ->setCollection($this->_profiles)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);

        /** @var Mage_Sales_Model_Recurring_Profile $profile */
        $profile = Mage::getModel('sales/recurring_profile');

        $this->setGridColumns([
            new Varien_Object([
                'index' => 'reference_id',
                'title' => $profile->getFieldLabel('reference_id'),
                'is_nobr' => true,
                'width' => 1,
            ]),
            new Varien_Object([
                'index' => 'state',
                'title' => $profile->getFieldLabel('state'),
            ]),
            new Varien_Object([
                'index' => 'created_at',
                'title' => $profile->getFieldLabel('created_at'),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            ]),
            new Varien_Object([
                'index' => 'updated_at',
                'title' => $profile->getFieldLabel('updated_at'),
                'is_nobr' => true,
                'width' => 1,
            ]),
            new Varien_Object([
                'index' => 'method_code',
                'title' => $profile->getFieldLabel('method_code'),
                'is_nobr' => true,
                'width' => 1,
            ]),
        ]);

        $profiles = [];
        $store = Mage::app()->getStore();
        $locale = Mage::app()->getLocale();
        foreach ($this->_profiles as $profile) {
            $profile->setStore($store)->setLocale($locale);
            $profiles[] = new Varien_Object([
                'reference_id' => $profile->getReferenceId(),
                'reference_id_link_url' => $this->getUrl('sales/recurring_profile/view/', ['profile' => $profile->getId()]),
                'state'       => $profile->renderData('state'),
                'created_at'  => $this->formatDate($profile->getData('created_at'), 'medium', true),
                'updated_at'  => $profile->getData('updated_at') ? $this->formatDate($profile->getData('updated_at'), 'short', true) : '',
                'method_code' => $profile->renderData('method_code'),
            ]);
        }
        if ($profiles) {
            $this->setGridElements($profiles);
        }
        $orders = [];
    }

    /**
     * Instantiate profiles collection
     *
     * @param string|array $fields
     */
    protected function _prepareProfiles($fields = '*')
    {
        $this->_profiles = Mage::getModel('sales/recurring_profile')->getCollection()
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
            ->addFieldToSelect($fields)
            ->setOrder('profile_id', 'desc')
        ;
    }

    /**
     * Set back Url
     *
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_beforeToHtml();
    }
}
