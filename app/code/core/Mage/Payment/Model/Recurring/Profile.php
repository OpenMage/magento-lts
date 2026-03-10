<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

use Carbon\Carbon;

/**
 * Recurring payment profile
 * Extends from Mage_Core_Abstract for a reason: to make descendants have its own resource
 *
 * @package    Mage_Payment
 *
 * @method float  getBillingAmount()
 * @method string getCurrencyCode()
 * @method int    getInternalReferenceId()
 * @method string getMethodCode()
 * @method int    getPeriodFrequency()
 * @method int    getPeriodUnit()
 * @method string getScheduleDescription()
 * @method bool   getStartDateIsEditable()
 * @method string getStartDatetime()
 * @method int    getStoreId()
 * @method float  getTrialBillingAmount()
 * @method int    getTrialPeriodFrequency()
 * @method int    getTrialPeriodMaxCycles()
 * @method int    getTrialPeriodUnit()
 * @method bool   hasScheduleDescription()
 * @method $this  setImportedStartDatetime(string $value)
 * @method $this  setMethodCode(string $value)
 * @method string setScheduleDescription(string $value)
 * @method $this  setStartDatetime(string $value)
 */
class Mage_Payment_Model_Recurring_Profile extends Mage_Core_Model_Abstract
{
    /**
     * Constants for passing data through catalog
     *
     * @var string
     */
    public const BUY_REQUEST_START_DATETIME = 'recurring_profile_start_datetime';

    public const PRODUCT_OPTIONS_KEY = 'recurring_profile_options';

    /**
     * Period units
     *
     * @var string
     */
    public const PERIOD_UNIT_DAY = 'day';

    public const PERIOD_UNIT_WEEK = 'week';

    public const PERIOD_UNIT_SEMI_MONTH = 'semi_month';

    public const PERIOD_UNIT_MONTH = 'month';

    public const PERIOD_UNIT_YEAR = 'year';

    /**
     * Errors collected during validation
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * @var Mage_Payment_Model_Method_Abstract
     */
    protected $_methodInstance = null;

    /**
     * Locale instance used for importing/exporting data
     *
     * @var Mage_Core_Model_Locale
     */
    protected $_locale = null;

    /**
     * Store instance used by locale or method instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Payment methods reference
     *
     * @var array
     */
    protected $_paymentMethods = [];

    /**
     * Check whether the object data is valid
     * Returns true if valid.
     *
     * @return bool
     */
    public function isValid()
    {
        $this->_filterValues();
        $this->_errors = [];

        // start date, order ref ID, schedule description
        if (!$this->getStartDatetime()) {
            $this->_errors['start_datetime'][] = Mage::helper('payment')->__('Start date is undefined.');
        } elseif (!Zend_Date::isDate($this->getStartDatetime(), Varien_Date::DATETIME_INTERNAL_FORMAT)) {
            $this->_errors['start_datetime'][] = Mage::helper('payment')->__('Start date has invalid format.');
        }

        if (!$this->getScheduleDescription()) {
            $this->_errors['schedule_description'][] = Mage::helper('payment')->__('Schedule description must be not empty.');
        }

        // period unit and frequency
        if (!$this->getPeriodUnit() || !in_array($this->getPeriodUnit(), $this->getAllPeriodUnits(false), true)) {
            $this->_errors['period_unit'][] = Mage::helper('payment')->__('Billing period unit is not defined or wrong.');
        }

        if ($this->getPeriodFrequency() && !$this->_validatePeriodFrequency('period_unit', 'period_frequency')) {
            $this->_errors['period_frequency'][] = Mage::helper('payment')->__('Period frequency is wrong.');
        }

        // trial period unit, trial frequency, trial period max cycles, trial billing amount
        if ($this->getTrialPeriodUnit()) {
            if (!in_array($this->getTrialPeriodUnit(), $this->getAllPeriodUnits(false), true)) {
                $this->_errors['trial_period_unit'][] = Mage::helper('payment')->__('Trial billing period unit is wrong.');
            }

            if (!$this->getTrialPeriodFrequency() || !$this->_validatePeriodFrequency('trial_period_unit', 'trial_period_frequency')) {
                $this->_errors['trial_period_frequency'][] = Mage::helper('payment')->__('Trial period frequency is wrong.');
            }

            if (!$this->getTrialPeriodMaxCycles()) {
                $this->_errors['trial_period_max_cycles'][] = Mage::helper('payment')->__('Trial period max cycles is wrong.');
            }

            if (!$this->getTrialBillingAmount()) {
                $this->_errors['trial_billing_amount'][] = Mage::helper('payment')->__('Trial billing amount is wrong.');
            }
        }

        // billing and other amounts
        if (!$this->getBillingAmount() || 0 >= $this->getBillingAmount()) {
            $this->_errors['billing_amount'][] = Mage::helper('payment')->__('Wrong or empty billing amount specified.');
        }

        foreach (['trial_billing_abount', 'shipping_amount', 'tax_amount', 'init_amount'] as $key) {
            if ($this->hasData($key) && 0 >= $this->getData($key)) {
                $this->_errors[$key][] = Mage::helper('payment')->__('Wrong %s specified.', $this->getFieldLabel($key));
            }
        }

        // currency code
        if (!$this->getCurrencyCode()) {
            $this->_errors['currency_code'][] = Mage::helper('payment')->__('Currency code is undefined.');
        }

        // payment method
        if (!$this->_methodInstance || !$this->getMethodCode()) {
            $this->_errors['method_code'][] = Mage::helper('payment')->__('Payment method code is undefined.');
        }

        if ($this->_methodInstance) {
            try {
                $this->_methodInstance->validateRecurringProfile($this);
            } catch (Mage_Core_Exception $e) {
                $this->_errors['payment_method'][] = $e->getMessage();
            }
        }

        return empty($this->_errors);
    }

    /**
     * Getter for errors that may appear after validation
     *
     * @param  bool                $isGrouped
     * @param  bool                $asMessage
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getValidationErrors($isGrouped = true, $asMessage = false)
    {
        if ($isGrouped && $this->_errors) {
            $result = [];
            foreach ($this->_errors as $row) {
                $result[] = implode(' ', $row);
            }

            if ($asMessage) {
                return Mage::throwException(
                    Mage::helper('payment')->__("Payment profile is invalid:\n%s", implode("\n", $result)),
                );
            }

            return $result;
        }

        return $this->_errors;
    }

    /**
     * Setter for payment method instance
     *
     * @return $this
     * @throws Exception
     */
    public function setMethodInstance(Mage_Payment_Model_Method_Abstract $object)
    {
        if ($object instanceof Mage_Payment_Model_Recurring_Profile_MethodInterface) {
            $this->_methodInstance = $object;
        } else {
            throw new Exception('Invalid payment method instance for use in recurring profile.');
        }

        return $this;
    }

    /**
     * Collect needed information from buy request
     * Then filter data
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function importBuyRequest(Varien_Object $buyRequest)
    {
        $startDate = $buyRequest->getData(self::BUY_REQUEST_START_DATETIME);
        if ($startDate) {
            $this->_ensureLocaleAndStore();
            $dateFormat = $this->_locale->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $localeCode = $this->_locale->getLocaleCode();
            if (!Zend_Date::isDate($startDate, $dateFormat, $localeCode)) {
                Mage::throwException(Mage::helper('payment')->__('Recurring profile start date has invalid format.'));
            }

            $utcTime = $this->_locale->utcDate($this->_store, $startDate, true, $dateFormat)
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            $this->setStartDatetime($utcTime)->setImportedStartDatetime($startDate);
        }

        return $this->_filterValues();
    }

    /**
     * Import product recurring profile information
     * Returns false if it cannot be imported
     *
     * @return $this|false
     */
    public function importProduct(Mage_Catalog_Model_Product $product)
    {
        if ($product->isRecurring() && is_array($product->getRecurringProfile())) {
            // import recurring profile data
            $this->addData($product->getRecurringProfile());

            // automatically set product name if there is no schedule description
            if (!$this->hasScheduleDescription()) {
                $this->setScheduleDescription($product->getName());
            }

            // collect start datetime from the product options
            $options = $product->getCustomOption(self::PRODUCT_OPTIONS_KEY);
            if ($options) {
                $options = unserialize($options->getValue(), ['allowed_classes' => false]);
                if (is_array($options)) {
                    if (isset($options['start_datetime'])) {
                        $startDatetime = new Zend_Date($options['start_datetime'], Varien_Date::DATETIME_INTERNAL_FORMAT);
                        $this->setNearestStartDatetime($startDatetime);
                    }
                }
            }

            return $this->_filterValues();
        }

        return false;
    }

    /**
     * Render available schedule information
     *
     * @return Varien_Object[]
     */
    public function exportScheduleInfo()
    {
        $result = [
            new Varien_Object([
                'title'    => Mage::helper('payment')->__('Billing Period'),
                'schedule' => $this->_renderSchedule('period_unit', 'period_frequency', 'period_max_cycles'),
            ]),
        ];
        $trial = $this->_renderSchedule('trial_period_unit', 'trial_period_frequency', 'trial_period_max_cycles');
        if ($trial) {
            $result[] = new Varien_Object([
                'title'    => Mage::helper('payment')->__('Trial Period'),
                'schedule' => $trial,
            ]);
        }

        return $result;
    }

    /**
     * Determine nearest possible profile start date
     *
     * @return $this
     * @throws Zend_Date_Exception
     */
    public function setNearestStartDatetime(?Zend_Date $minAllowed = null)
    {
        // TODO: implement proper logic with invoking payment method instance
        $date = $minAllowed;
        if (!$date || $date->getTimestamp() < Carbon::now()->getTimestamp()) {
            $date = new Zend_Date(Carbon::now()->getTimestamp());
        }

        $this->setStartDatetime($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        return $this;
    }

    /**
     * Convert the start datetime (if set) to proper locale/timezone and return
     *
     * @param  bool             $asString
     * @return string|Zend_Date
     */
    public function exportStartDatetime($asString = true)
    {
        $datetime = $this->getStartDatetime();
        if (!$datetime || !$this->_locale || !$this->_store) {
            return;
        }

        $date = $this->_locale->storeDate($this->_store, Carbon::parse($datetime)->getTimestamp(), true);
        if ($asString) {
            return $date->toString($this->_locale->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        }

        return $date;
    }

    /**
     * Locale instance setter
     *
     * @return $this
     */
    public function setLocale(Mage_Core_Model_Locale $locale)
    {
        $this->_locale = $locale;
        return $this;
    }

    /**
     * Store instance setter
     *
     * @return $this
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Getter for available period units
     *
     * @param  bool  $withLabels
     * @return array
     */
    public function getAllPeriodUnits($withLabels = true)
    {
        $units = [
            self::PERIOD_UNIT_DAY,
            self::PERIOD_UNIT_WEEK,
            self::PERIOD_UNIT_SEMI_MONTH,
            self::PERIOD_UNIT_MONTH,
            self::PERIOD_UNIT_YEAR,
        ];

        if ($withLabels) {
            $result = [];
            foreach ($units as $unit) {
                $result[$unit] = $this->getPeriodUnitLabel($unit);
            }

            return $result;
        }

        return $units;
    }

    /**
     * Render label for specified period unit
     *
     * @param  string $unit
     * @return string
     */
    public function getPeriodUnitLabel($unit)
    {
        return match ($unit) {
            self::PERIOD_UNIT_DAY => Mage::helper('payment')->__('Day'),
            self::PERIOD_UNIT_WEEK => Mage::helper('payment')->__('Week'),
            self::PERIOD_UNIT_SEMI_MONTH => Mage::helper('payment')->__('Two Weeks'),
            self::PERIOD_UNIT_MONTH => Mage::helper('payment')->__('Month'),
            self::PERIOD_UNIT_YEAR => Mage::helper('payment')->__('Year'),
            default => $unit,
        };
    }

    /**
     * Getter for field label
     *
     * @param  string      $field
     * @return null|string
     */
    public function getFieldLabel($field)
    {
        return match ($field) {
            'subscriber_name' => Mage::helper('payment')->__('Subscriber Name'),
            'start_datetime' => Mage::helper('payment')->__('Start Date'),
            'internal_reference_id' => Mage::helper('payment')->__('Internal Reference ID'),
            'schedule_description' => Mage::helper('payment')->__('Schedule Description'),
            'suspension_threshold' => Mage::helper('payment')->__('Maximum Payment Failures'),
            'bill_failed_later' => Mage::helper('payment')->__('Auto Bill on Next Cycle'),
            'period_unit' => Mage::helper('payment')->__('Billing Period Unit'),
            'period_frequency' => Mage::helper('payment')->__('Billing Frequency'),
            'period_max_cycles' => Mage::helper('payment')->__('Maximum Billing Cycles'),
            'billing_amount' => Mage::helper('payment')->__('Billing Amount'),
            'trial_period_unit' => Mage::helper('payment')->__('Trial Billing Period Unit'),
            'trial_period_frequency' => Mage::helper('payment')->__('Trial Billing Frequency'),
            'trial_period_max_cycles' => Mage::helper('payment')->__('Maximum Trial Billing Cycles'),
            'trial_billing_amount' => Mage::helper('payment')->__('Trial Billing Amount'),
            'currency_code' => Mage::helper('payment')->__('Currency'),
            'shipping_amount' => Mage::helper('payment')->__('Shipping Amount'),
            'tax_amount' => Mage::helper('payment')->__('Tax Amount'),
            'init_amount' => Mage::helper('payment')->__('Initial Fee'),
            'init_may_fail' => Mage::helper('payment')->__('Allow Initial Fee Failure'),
            'method_code' => Mage::helper('payment')->__('Payment Method'),
            'reference_id' => Mage::helper('payment')->__('Payment Reference ID'),
            default => null,
        };
    }

    /**
     * Getter for field comments
     *
     * @param  string      $field
     * @return null|string
     */
    public function getFieldComment($field)
    {
        return match ($field) {
            'subscriber_name' => Mage::helper('payment')->__('Full name of the person receiving the product or service paid for by the recurring payment.'),
            'start_datetime' => Mage::helper('payment')->__('The date when billing for the profile begins.'),
            'schedule_description' => Mage::helper('payment')->__('Short description of the recurring payment. By default equals to the product name.'),
            'suspension_threshold' => Mage::helper('payment')->__('The number of scheduled payments that can fail before the profile is automatically suspended.'),
            'bill_failed_later' => Mage::helper('payment')->__('Automatically bill the outstanding balance amount in the next billing cycle (if there were failed payments).'),
            'period_unit' => Mage::helper('payment')->__('Unit for billing during the subscription period.'),
            'period_frequency' => Mage::helper('payment')->__('Number of billing periods that make up one billing cycle.'),
            'period_max_cycles' => Mage::helper('payment')->__('The number of billing cycles for payment period.'),
            'init_amount' => Mage::helper('payment')->__('Initial non-recurring payment amount due immediately upon profile creation.'),
            'init_may_fail' => Mage::helper('payment')->__('Whether to suspend the payment profile if the initial fee fails or add it to the outstanding balance.'),
            default => null,
        };
    }

    /**
     * Transform some specific data for output
     *
     * @param  string $key
     * @return mixed
     */
    public function renderData($key)
    {
        $value = $this->_getData($key);
        switch ($key) {
            case 'period_unit':
                return $this->getPeriodUnitLabel($value);
            case 'method_code':
                if (!$this->_paymentMethods) {
                    $this->_paymentMethods = Mage::helper('payment')->getPaymentMethodList(false);
                }

                if (isset($this->_paymentMethods[$value])) {
                    return $this->_paymentMethods[$value];
                }

                break;
            case 'start_datetime':
                return $this->exportStartDatetime(true);
        }

        return $value;
    }

    /**
     * Filter self data to make sure it can be validated properly
     *
     * @return $this
     */
    protected function _filterValues()
    {
        // determine payment method/code
        if ($this->_methodInstance) {
            $this->setMethodCode($this->_methodInstance->getCode());
        } elseif ($this->getMethodCode()) {
            $this->getMethodInstance();
        }

        // unset redundant values, if empty
        foreach (['schedule_description',
            'suspension_threshold', 'bill_failed_later', 'period_frequency', 'period_max_cycles', 'reference_id',
            'trial_period_unit', 'trial_period_frequency', 'trial_period_max_cycles', 'init_may_fail'] as $key
        ) {
            if ($this->hasData($key) && (!$this->getData($key) || $this->getData($key) == '0')) {
                $this->unsetData($key);
            }
        }

        // cast amounts
        foreach ([
            'billing_amount', 'trial_billing_amount', 'shipping_amount', 'tax_amount', 'init_amount'] as $key
        ) {
            if ($this->hasData($key)) {
                if (!$this->getData($key) || $this->getData($key) == 0) {
                    $this->unsetData($key);
                } else {
                    $this->setData($key, sprintf('%.4F', $this->getData($key)));
                }
            }
        }

        // automatically determine start date, if not set
        if ($this->getStartDatetime()) {
            $date = new Zend_Date($this->getStartDatetime(), Varien_Date::DATETIME_INTERNAL_FORMAT);
            $this->setNearestStartDatetime($date);
        } else {
            $this->setNearestStartDatetime();
        }

        return $this;
    }

    /**
     * Check that locale and store instances are set
     *
     * @throws Exception
     */
    protected function _ensureLocaleAndStore()
    {
        if (!$this->_locale || !$this->_store) {
            throw new Exception('Locale and store instances must be set for this operation.');
        }
    }

    /**
     * Return payment method instance
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    protected function getMethodInstance()
    {
        if (!$this->_methodInstance) {
            $this->setMethodInstance(Mage::helper('payment')->getMethodInstance($this->getMethodCode()));
        }

        $this->_methodInstance->setStore($this->getStoreId());
        return $this->_methodInstance;
    }

    /**
     * Check accordance of the unit and frequency
     *
     * @param  string $unitKey
     * @param  string $frequencyKey
     * @return bool
     */
    protected function _validatePeriodFrequency($unitKey, $frequencyKey)
    {
        if ($this->getData($unitKey) == self::PERIOD_UNIT_SEMI_MONTH && $this->getData($frequencyKey) != 1) {
            return false;
        }

        return true;
    }

    /**
     * Perform full validation before saving
     *
     * @throws Mage_Core_Exception
     */
    protected function _validateBeforeSave()
    {
        if (!$this->isValid()) {
            Mage::throwException($this->getValidationErrors(true, true));
        }

        if (!$this->getInternalReferenceId()) {
            Mage::throwException(
                Mage::helper('payment')->__('An internal reference ID is required to save the payment profile.'),
            );
        }
    }

    /**
     * Validate before saving
     *
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        $this->_validateBeforeSave();
        return parent::_beforeSave();
    }

    /**
     * Generate explanations for specified schedule parameters
     *
     * TODO: utilize Zend_Translate_Plural or similar stuff to render proper declensions with numerals.
     *
     * @param  string $periodKey
     * @param  string $frequencyKey
     * @param  string $cyclesKey
     * @return array
     */
    protected function _renderSchedule($periodKey, $frequencyKey, $cyclesKey)
    {
        $result = [];

        $period = $this->_getData($periodKey);
        $frequency = (int) $this->_getData($frequencyKey);
        if (!$period || !$frequency) {
            return $result;
        }

        if (self::PERIOD_UNIT_SEMI_MONTH == $period) {
            $frequency = '';
        }

        $result[] = Mage::helper('payment')->__('%s %s cycle.', $frequency, $this->getPeriodUnitLabel($period));

        $cycles = (int) $this->_getData($cyclesKey);
        if ($cycles) {
            $result[] = Mage::helper('payment')->__('Repeats %s time(s).', $cycles);
        } else {
            $result[] = Mage::helper('payment')->__('Repeats until suspended or canceled.');
        }

        return $result;
    }
}
