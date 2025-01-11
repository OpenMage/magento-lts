<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Grid widget massaction block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method $this setFormFieldName(string $value)
 * @method $this setHideFormElement(bool $value) Hide Form element to prevent IE errors
 * @method bool getHideFormElement()
 */
abstract class Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract extends Mage_Adminhtml_Block_Widget
{
    /**
     * Current massactions
     * @var string
     */
    public const ASSIGN_GROUP              = 'assign_group';
    public const ATTRIBUTES                = 'attributes';
    public const CANCEL_ORDER              = 'cancel_order';
    public const CHANGE_MODE               = 'change_mode';
    public const ENABLE                    = 'enable';
    public const DELETE                    = 'delete';
    public const DISABLE                   = 'disable';
    public const HOLD_ORDER                = 'hold_order';
    public const MARK_AS_READ              = 'mark_as_read';
    public const NEWSLETTER_SUBSCRIBE      = 'newsletter_subscribe';
    public const NEWSLETTER_UNSUBSCRIBE    = 'newsletter_unsubscribe';
    public const PDF_CREDITMEMOS_ORDER     = 'pdfcreditmemos_order';
    public const PDF_DOCS_ORDER            = 'pdfdocs_order';
    public const PDF_INVOICE_ORDER         = 'pdfinvoices_order';
    public const PDF_SHIPMENTS_ORDER       = 'pdfshipments_order';
    public const PRINT_SHIPMENT_LABEL      = 'print_shipping_label';
    public const REFRESH                   = 'refresh';
    public const REFRESH_LIFETIME          = 'refresh_lifetime';
    public const REFRESH_RECENT            = 'refresh_recent';
    public const REINDEX                   = 'reindex';
    public const REMOVE                    = 'remove';
    public const STATUS                    = 'status';
    public const UNHOLD_ORDER              = 'unhold_order';
    public const UNSUBSCRIBE               = 'unsubscribe';
    public const UPDATE_STATUS             = 'update_status';

    /**
     * @var string[]
     */
    protected static $needsConfirm = [
        self::CANCEL_ORDER,
        self::HOLD_ORDER,
        self::UNHOLD_ORDER,
        self::DELETE,
        self::REMOVE,
    ];

    /**
     * Massaction items
     *
     * @var array
     */
    protected $_items = [];

    /**
     * Sets Massaction template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/grid/massaction.phtml');
        $this->setErrorText(Mage::helper('catalog')->jsQuoteEscape(Mage::helper('catalog')->__('Please select items.')));
    }

    /**
     * Add new massaction item
     *
     * $item = array(
     *      'label'    => string,
     *      'complete' => string, // Only for ajax enabled grid (optional)
     *      'url'      => string,
     *      'confirm'  => string, // text of confirmation of this action (optional)
     *      'additional' => string|array|Mage_Core_Block_Abstract // (optional)
     * );
     *
     * @param string $itemId
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
     */
    public function addItem($itemId, array $item)
    {
        if (is_string($itemId) && $this->isConfirmMassAction($itemId) && !isset($item['confirm'])) {
            $item['confirm'] = Mage::helper('core')->__('Are you sure?');
        }

        $this->_items[$itemId] =  $this->getLayout()->createBlock('adminhtml/widget_grid_massaction_item')
            ->setData($item)
            ->setMassaction($this)
            ->setId($itemId);

        if ($this->_items[$itemId]->getAdditional()) {
            $this->_items[$itemId]->setAdditionalActionBlock($this->_items[$itemId]->getAdditional());
            $this->_items[$itemId]->unsAdditional();
        }

        return $this;
    }

    /**
     * Retrieve massaction item with id $itemId
     *
     * @param string $itemId
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Item|null
     */
    public function getItem($itemId)
    {
        return $this->_items[$itemId] ?? null;
    }

    /**
     * Retrieve massaction items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Retrieve massaction items JSON
     *
     * @return string
     */
    public function getItemsJson()
    {
        $result = [];
        foreach ($this->getItems() as $itemId => $item) {
            $result[$itemId] = $item->toArray();
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * Retrieve massaction items count
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->_items);
    }

    /**
     * Checks are massactions available
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getCount() > 0 && $this->getParentBlock()->getMassactionIdField();
    }

    /**
     * Retrieve global form field name for all massaction items
     *
     * @return string
     */
    public function getFormFieldName()
    {
        return ($this->getData('form_field_name') ? $this->getData('form_field_name') : 'massaction');
    }

    /**
     * Retrieve form field name for internal use. Based on $this->getFormFieldName()
     *
     * @return string
     */
    public function getFormFieldNameInternal()
    {
        return  'internal_' . $this->getFormFieldName();
    }

    /**
     * Retrieve massaction block js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrieve grid block js object name
     *
     * @return string
     */
    public function getGridJsObjectName()
    {
        return $this->getParentBlock()->getJsObjectName();
    }

    /**
     * Retrieve JSON string of selected checkboxes
     *
     * @return string
     */
    public function getSelectedJson()
    {
        if ($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            $selected = explode(',', $this->quoteEscape($selected));
            return implode(',', $selected);
        } else {
            return '';
        }
    }

    /**
     * Retrieve array of selected checkboxes
     *
     * @return array
     */
    public function getSelected()
    {
        if ($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            return explode(',', $this->quoteEscape($selected));
        } else {
            return [];
        }
    }

    /**
     * Retrieve apply button html
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        return $this->getButtonHtml($this->__('Submit'), $this->getJsObjectName() . '.apply()');
    }

    /**
     * @return string
     */
    public function getJavaScript()
    {
        return " var {$this->getJsObjectName()} = new varienGridMassaction('{$this->getHtmlId()}', "
                . "{$this->getGridJsObjectName()}, '{$this->getSelectedJson()}'"
                . ", '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');"
                . "{$this->getJsObjectName()}.setItems({$this->getItemsJson()}); "
                . "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');"
                . ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '')
                . ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '')
                . "{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';";
    }

    /**
     * @return string
     */
    public function getGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }

        $gridIds = $this->getParentBlock()->getCollection()->getAllIds();

        if (!empty($gridIds)) {
            return implode(',', $gridIds);
        }
        return '';
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getParentBlock()->getHtmlId() . '_massaction';
    }

    /**
     * Remove existing massaction item by its id
     *
     * @param string $itemId
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
     */
    public function removeItem($itemId)
    {
        if (isset($this->_items[$itemId])) {
            unset($this->_items[$itemId]);
        }

        return $this;
    }

    /**
     * Retrieve select all functionality flag check
     *
     * @return bool
     */
    public function getUseSelectAll()
    {
        return $this->_getData('use_select_all') === null || $this->_getData('use_select_all');
    }

    /**
     * Retrieve select all functionality flag check
     *
     * @param bool $flag
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
     */
    public function setUseSelectAll($flag)
    {
        $this->setData('use_select_all', (bool) $flag);
        return $this;
    }

    /**
     * Group items for optgroups
     */
    public function getGroupedItems(): array
    {
        $groupedItems = [
            'default' => [],
        ];

        foreach ($this->getItems() as $item) {
            if ($item->getData('group')) {
                $groupedItems['grouped'][$item->getData('group')][$item->getId()] = $item;
            } else {
                $groupedItems['default'][$item->getId()] = $item;
            }
        }

        return $groupedItems;
    }

    protected function isConfirmMassAction(string $itemId): bool
    {
        return in_array($itemId, static::$needsConfirm);
    }
}
