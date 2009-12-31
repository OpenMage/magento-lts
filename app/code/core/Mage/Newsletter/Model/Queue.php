<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter queue model.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Newsletter Template object
     *
     * @var Mage_Newsletter_Model_Template
     */
    protected $_template;

    /**
     * Subscribers collection
     * @var Varien_Data_Collection_Db
     */
    protected $_subscribersCollection = null;

    protected $_saveTemplateFlag = false;

    protected $_saveStoresFlag = false;

    protected $_stores = false;

    /**
     * Design changes object instance
     * @var Varien_Object
     */
    protected $_designConfig;

    const STATUS_NEVER = 0;
    const STATUS_SENDING = 1;
    const STATUS_CANCEL = 2;
    const STATUS_SENT = 3;
    const STATUS_PAUSE = 4;

    /**
     * Default design area for emulation
     */
    const DEFAULT_DESIGN_AREA = 'frontend';

    protected function _construct()
    {
        $this->_init('newsletter/queue');
    }

    /**
     * Returns subscribers collection for this queue
     *
     * @return Varien_Data_Collection_Db
     */
    public function getSubscribersCollection()
    {
        if (is_null($this->_subscribersCollection)) {
            $this->_subscribersCollection = Mage::getResourceModel('newsletter/subscriber_collection')
                ->useQueue($this);
        }

        return $this->_subscribersCollection;
    }

    /**
     * Add template data to queue.
     *
     * @deprecated
     * @param Varien_Object $data
     * @return Mage_Newsletter_Model_Queue
     */
    public function addTemplateData( $data )
    {
        $template = $this->getTemplate();
        if ($data->getTemplateId() && $data->getTemplateId() != $template->getId()) {
            $template->load($data->getTemplateId());
        }

        return $this;
    }

    /**
     * Send messages to subscribers for this queue
     *
     * @param   int     $count
     * @param   array   $additionalVariables
     */
    public function sendPerSubscriber($count=20, array $additionalVariables=array())
    {
        if($this->getQueueStatus()!=self::STATUS_SENDING && ($this->getQueueStatus()!=self::STATUS_NEVER && $this->getQueueStartAt()) ) {
            return $this;
        }

        if($this->getSubscribersCollection()->getSize()==0) {
            return $this;
        }

        $collection = $this->getSubscribersCollection()
            ->useOnlyUnsent()
            ->showCustomerInfo()
            ->setPageSize($count)
            ->setCurPage(1)
            ->load();

        if(!$this->getTemplate()) {
            $this->addTemplateData($this);
            if(!$this->getTemplate()->isPreprocessed()) {
                $this->getTemplate()->preproccess();
            }
        }

        // save current design settings
        $currentDesignConfig = clone $this->_getDesignConfig();
        foreach($collection->getItems() as $item) {
            if ($this->_getDesignConfig()->getStore() != $item->getStoreId()) {
                $this->_setDesignConfig(array('area' => self::DEFAULT_DESIGN_AREA, 'store' => $item->getStoreId()));
                $this->_applyDesignConfig();
            }
            $this->getTemplate()->send($item, array('subscriber'=>$item), null, $this);
        }

        // restore previous design settings
        $this->_setDesignConfig($currentDesignConfig->getData());
        $this->_applyDesignConfig();

        if(count($collection->getItems()) < $count-1 || count($collection->getItems()) == 0) {
            $this->setQueueFinishAt(now());
            $this->setQueueStatus(self::STATUS_SENT);
            $this->save();
        }
        return $this;
    }

    public function getDataForSave() {
        $data = array();
        $data['template_id'] = $this->getTemplateId();
        $data['queue_status'] = $this->getQueueStatus();
        $data['queue_start_at'] = $this->getQueueStartAt();
        $data['queue_finish_at'] = $this->getQueueFinishAt();
        return $data;
    }

    public function addSubscribersToQueue(array $subscriberIds)
    {
        $this->_getResource()->addSubscribersToQueue($this, $subscriberIds);
        return $this;
    }

    public function setSaveTemplateFlag($value)
    {
        $this->_saveTemplateFlag = (boolean)$value;
        return $this;
    }

    public function getSaveTemplateFlag()
    {
        return $this->_saveTemplateFlag;
    }

    public function setSaveStoresFlag($value)
    {
        $this->_saveStoresFlag = (boolean)$value;
        return $this;
    }

    public function getSaveStoresFlag()
    {
        return $this->_saveStoresFlag;
    }

    public function setStores(array $storesIds)
    {
        $this->setSaveStoresFlag(true);
        $this->_stores = $storesIds;
        return $this;
    }

    public function getStores()
    {
        if(!$this->_stores) {
            $this->_stores = $this->_getResource()->getStores($this);
        }

        return $this->_stores;
    }

    /**
     * Retrieve Newsletter Template object
     *
     * @return Mage_Newsletter_Model_Template
     */
    public function getTemplate()
    {
        if (is_null($this->_template)) {
            $this->_template = Mage::getModel('newsletter/template')
                ->load($this->getTemplateId());
        }
        return $this->_template;
    }

    /**
     * Setter for design changes
     *
     * @param   array $config
     * @return  Mage_Newsletter_Model_Queue
     */
    protected function _setDesignConfig(array $config)
    {
        $this->_getDesignConfig()->setData($config);
        return $this;
    }

    /**
     * Getter for design changes
     *
     * @return Varien_Object
     */
    protected function _getDesignConfig()
    {
        if(is_null($this->_designConfig)) {

            $store = is_object(Mage::getDesign()->getStore())
                ? Mage::getDesign()->getStore()->getId()
                : Mage::getDesign()->getStore();

            $this->_designConfig = new Varien_Object(array(
                'area' => Mage::getDesign()->getArea(),
                'store' => $store
            ));
        }
        return $this->_designConfig;
    }

    protected function _applyDesignConfig()
    {
        $designConfig = $this->_getDesignConfig();

        $design = Mage::getDesign();
        $designConfig->setOldArea($design->getArea())
            ->setOldStore($design->getStore());

        if ($designConfig->hasData('area')) {
            Mage::getDesign()->setArea($designConfig->getArea());
        }

        if ($designConfig->hasData('store')) {
            $store = $designConfig->getStore();
            Mage::app()->setCurrentStore($store);

            $locale = new Zend_Locale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store));
            Mage::app()->getLocale()->setLocale($locale);
            Mage::app()->getLocale()->setLocaleCode($locale->toString());
            if ($designConfig->hasData('area')) {
                Mage::getSingleton('core/translate')->setLocale($locale)
                    ->init($designConfig->getArea(), true);
            }

            $design->setStore($store);
            $design->setTheme('');
            $design->setPackageName('');
        }

        return $this;
    }
}
