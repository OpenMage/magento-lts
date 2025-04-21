<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Recurring profile view
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Recurring_Profile_View extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Sales_Model_Recurring_Profile
     */
    protected $_profile = null;

    /**
     * Whether the block should be used to render $_info
     *
     * @var bool
     */
    protected $_shouldRenderInfo = false;

    /**
     * Information to be rendered
     *
     * @var array
     */
    protected $_info = [];

    /**
     * Related orders collection
     *
     * @var Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_relatedOrders = null;

    /**
     * Prepare main view data
     */
    public function prepareViewData()
    {
        $this->addData([
            'reference_id' => $this->_profile->getReferenceId(),
            'can_cancel'   => $this->_profile->canCancel(),
            'cancel_url'   => $this->getUrl('*/*/updateState', ['profile' => $this->_profile->getId(), 'action' => 'cancel']),
            'can_suspend'  => $this->_profile->canSuspend(),
            'suspend_url'  => $this->getUrl('*/*/updateState', ['profile' => $this->_profile->getId(), 'action' => 'suspend']),
            'can_activate' => $this->_profile->canActivate(),
            'activate_url' => $this->getUrl('*/*/updateState', ['profile' => $this->_profile->getId(), 'action' => 'activate']),
            'can_update'   => $this->_profile->canFetchUpdate(),
            'update_url'   => $this->getUrl('*/*/updateProfile', ['profile' => $this->_profile->getId()]),
            'back_url'     => $this->getUrl('*/*/'),
            'confirmation_message' => Mage::helper('sales')->__('Are you sure you want to do this?'),
        ]);
    }

    /**
     * Getter for rendered info, if any
     *
     * @return array
     */
    public function getRenderedInfo()
    {
        return $this->_info;
    }

    /**
     * Prepare profile main reference info
     */
    public function prepareReferenceInfo()
    {
        $this->_shouldRenderInfo = true;

        foreach (['method_code', 'reference_id', 'schedule_description', 'state'] as $key) {
            $this->_addInfo([
                'label' => $this->_profile->getFieldLabel($key),
                'value' => $this->_profile->renderData($key),
            ]);
        }
    }

    /**
     * Prepare profile order item info
     */
    public function prepareItemInfo()
    {
        $this->_shouldRenderInfo = true;
        $key = 'order_item_info';

        foreach (['name' => Mage::helper('catalog')->__('Product Name'),
            'sku'  => Mage::helper('catalog')->__('SKU'),
            'qty'  => Mage::helper('catalog')->__('Quantity'),
        ] as $itemKey => $label
        ) {
            $value = $this->_profile->getInfoValue($key, $itemKey);
            if ($value) {
                $this->_addInfo(['label' => $label, 'value' => $value,]);
            }
        }

        $request = $this->_profile->getInfoValue($key, 'info_buyRequest');
        if (empty($request)) {
            return;
        }

        $request = unserialize($request);
        if (empty($request['options'])) {
            return;
        }

        $options = Mage::getModel('catalog/product_option')->getCollection()
            ->addIdsToFilter(array_keys($request['options']))
            ->addTitleToResult($this->_profile->getInfoValue($key, 'store_id'))
            ->addValuesToResult();

        $productMock = Mage::getModel('catalog/product');
        $quoteItemOptionMock = Mage::getModel('sales/quote_item_option');
        foreach ($options as $option) {
            $quoteItemOptionMock->setId($option->getId());

            $group = $option->groupFactory($option->getType())
                ->setOption($option)
                ->setRequest(new Varien_Object($request))
                ->setProduct($productMock)
                ->setUseQuotePath(true)
                ->setQuoteItemOption($quoteItemOptionMock)
                ->validateUserValue($request['options']);

            $skipHtmlEscaping = false;
            if ($option->getType() == 'file') {
                $skipHtmlEscaping = true;

                $downloadParams = [
                    'id'  => $this->_profile->getId(),
                    'option_id' => $option->getId(),
                    'key' => $request['options'][$option->getId()]['secret_key'],
                ];
                $group->setCustomOptionDownloadUrl('sales/download/downloadProfileCustomOption')
                    ->setCustomOptionUrlParams($downloadParams);
            }

            $optionValue = $group->prepareForCart();

            $this->_addInfo([
                'label' => $option->getTitle(),
                'value' => $group->getFormattedOptionValue($optionValue),
                'skip_html_escaping' => $skipHtmlEscaping,
            ]);
        }
    }

    /**
     * Prepare profile schedule info
     */
    public function prepareScheduleInfo()
    {
        $this->_shouldRenderInfo = true;

        foreach (['start_datetime', 'suspension_threshold'] as $key) {
            $this->_addInfo([
                'label' => $this->_profile->getFieldLabel($key),
                'value' => $this->_profile->renderData($key),
            ]);
        }

        foreach ($this->_profile->exportScheduleInfo() as $i) {
            $this->_addInfo([
                'label' => $i->getTitle(),
                'value' => $i->getSchedule(),
            ]);
        }
    }

    /**
     * Prepare profile payments info
     */
    public function prepareFeesInfo()
    {
        $this->_shouldRenderInfo = true;

        $this->_addInfo([
            'label' => $this->_profile->getFieldLabel('currency_code'),
            'value' => $this->_profile->getCurrencyCode(),
        ]);
        foreach ([
            'init_amount',
            'trial_billing_amount',
            'billing_amount',
            'tax_amount',
            'shipping_amount',
        ] as $key
        ) {
            $value = $this->_profile->getData($key);
            if ($value) {
                $this->_addInfo([
                    'label' => $this->_profile->getFieldLabel($key),
                    'value' => Mage::helper('core')->formatCurrency($value, false),
                    'is_amount' => true,
                ]);
            }
        }
    }

    /**
     * Prepare profile address (billing or shipping) info
     */
    public function prepareAddressInfo()
    {
        $this->_shouldRenderInfo = true;

        if ($this->getAddressType() == 'shipping') {
            if ($this->_profile->getInfoValue('order_item_info', 'is_virtual') == '1') {
                $this->getParentBlock()->unsetChild('sales.recurring.profile.view.shipping');
                return;
            }
            $key = 'shipping_address_info';
        } else {
            $key = 'billing_address_info';
        }
        $this->setIsAddress(true);
        $address = Mage::getModel('sales/order_address', $this->_profile->getData($key));
        $this->_addInfo([
            'value' => preg_replace('/\\n{2,}/', "\n", $address->getFormated()),
        ]);
    }

    /**
     * Render related orders grid information
     */
    public function prepareRelatedOrdersFrontendGrid()
    {
        $this->_prepareRelatedOrders([
            'increment_id',
            'created_at',
            'customer_firstname',
            'customer_middlename',
            'customer_lastname',
            'base_grand_total',
            'status',
        ]);
        $this->_relatedOrders->addFieldToFilter('state', [
            'in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates(),
        ]);

        $pager = $this->getLayout()->createBlock('page/html_pager')
            ->setCollection($this->_relatedOrders)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);

        $this->setGridColumns([
            new Varien_Object([
                'index' => 'increment_id',
                'title' => $this->__('Order #'),
                'is_nobr' => true,
                'width' => 1,
            ]),
            new Varien_Object([
                'index' => 'created_at',
                'title' => $this->__('Date'),
                'is_nobr' => true,
                'width' => 1,
            ]),
            new Varien_Object([
                'index' => 'customer_name',
                'title' => $this->__('Customer Name'),
            ]),
            new Varien_Object([
                'index' => 'base_grand_total',
                'title' => $this->__('Order Total'),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            ]),
            new Varien_Object([
                'index' => 'status',
                'title' => $this->__('Order Status'),
                'is_nobr' => true,
                'width' => 1,
            ]),
        ]);

        $orders = [];
        foreach ($this->_relatedOrders as $order) {
            $orders[] = new Varien_Object([
                'increment_id' => $order->getIncrementId(),
                'created_at' => $this->formatDate($order->getCreatedAt()),
                'customer_name' => $order->getCustomerName(),
                'base_grand_total' => Mage::helper('core')->formatCurrency($order->getBaseGrandTotal(), false),
                'status' => $order->getStatusLabel(),
                'increment_id_link_url' => $this->getUrl('sales/order/view/', ['order_id' => $order->getId()]),
            ]);
        }
        if ($orders) {
            $this->setGridElements($orders);
        }
    }

    /**
     * Get rendered row value
     *
     * @return string
     */
    public function renderRowValue(Varien_Object $row)
    {
        $value = $row->getValue();
        if ($value === null) {
            return '';
        }
        if (is_array($value)) {
            $value = implode("\n", $value);
        }
        if (!$row->getSkipHtmlEscaping()) {
            $value = $this->escapeHtml($value);
        }
        return nl2br($value);
    }

    /**
     * Prepare related orders collection
     *
     * @param array|string $fieldsToSelect
     */
    protected function _prepareRelatedOrders($fieldsToSelect = '*')
    {
        if ($this->_relatedOrders === null) {
            $this->_relatedOrders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect($fieldsToSelect)
                ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
                ->addRecurringProfilesFilter($this->_profile->getId())
                ->setOrder('entity_id', 'desc');
        }
    }

    /**
     * Add specified data to the $_info
     *
     * @param string $key = null
     */
    protected function _addInfo(array $data, $key = null)
    {
        $object = new Varien_Object($data);
        if ($key) {
            $this->_info[$key] = $object;
        } else {
            $this->_info[] = $object;
        }
    }

    /**
     * Get current profile from registry and assign store/locale information to it
     */
    protected function _prepareLayout()
    {
        $this->_profile = Mage::registry('current_recurring_profile')
            ->setStore(Mage::app()->getStore())
            ->setLocale(Mage::app()->getLocale())
        ;
        return parent::_prepareLayout();
    }

    /**
     * Render self only if needed, also render info tabs group if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_profile || $this->_shouldRenderInfo && !$this->_info) {
            return '';
        }

        if ($this->hasShouldPrepareInfoTabs()) {
            foreach ($this->getChildGroup('info_tabs') as $block) {
                $block->setViewUrl(
                    $this->getUrl("*/*/{$block->getViewAction()}", ['profile' => $this->_profile->getId()]),
                );
            }
        }

        return parent::_toHtml();
    }
}
