<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer register form block
 *
 * @package    Mage_Customer
 *
 * @method $this setBackUrl(string $value)
 * @method $this setErrorUrl(string $value)
 * @method $this setShowAddressFields(bool $value)
 * @method $this setSuccessUrl(string $value)
 */
class Mage_Customer_Block_Form_Register extends Mage_Directory_Block_Data
{
    /**
     * Address instance with data
     *
     * @var null|Mage_Customer_Model_Address
     */
    protected $_address;

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Create New Customer Account'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = $this->helper('customer');
        return $helper->getRegisterPostUrl();
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            /** @var Mage_Customer_Helper_Data $helper */
            $helper = $this->helper('customer');
            $url = $helper->getLoginUrl();
        }

        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $formData = Mage::getSingleton('customer/session')->getCustomerFormData(true);
            $data = new Varien_Object();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }

            if (isset($data['region_id'])) {
                $data['region_id'] = (int) $data['region_id'];
            }

            if ($data->getDob()) {
                $dob = $data->getYear() . '-' . $data->getMonth() . '-' . $data->getDay();
                $data->setDob($dob);
            }

            $this->setData('form_data', $data);
        }

        return $data;
    }

    /**
     * Retrieve customer country identifier
     *
     * @return string
     */
    public function getCountryId()
    {
        $countryId = $this->getFormData()->getCountryId();
        if ($countryId) {
            return $countryId;
        }

        return parent::getCountryId();
    }

    /**
     * Retrieve customer region identifier
     *
     * @return null|int|string
     */
    public function getRegion()
    {
        if (($region = $this->getFormData()->getRegion()) !== false) {
            return $region;
        }

        if (($region = $this->getFormData()->getRegionId()) !== false) {
            return $region;
        }

        return null;
    }

    /**
     *  Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->isModuleOutputEnabled('Mage_Newsletter');
    }

    /**
     * Return customer address instance
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = Mage::getModel('customer/address');
        }

        return $this->_address;
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param  null|string $scope
     * @return $this
     */
    public function restoreSessionData(Mage_Customer_Model_Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data    = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }

    /**
     * Retrieve minimum length of customer password
     *
     * @return int
     */
    public function getMinPasswordLength()
    {
        return Mage::getModel('customer/customer')->getMinPasswordLength();
    }
}
