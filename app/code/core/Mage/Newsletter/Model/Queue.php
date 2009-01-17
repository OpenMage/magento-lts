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
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Subscribers collection
     * @var Varien_Data_Collection_Db
     */
    protected $_subscribersCollection = null;

    protected $_saveTemplateFlag = false;

    protected $_saveStoresFlag = false;

    protected $_stores = false;

    const STATUS_NEVER = 0;
    const STATUS_SENDING = 1;
    const STATUS_CANCEL = 2;
    const STATUS_SENT = 3;
    const STATUS_PAUSE = 4;


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
     * @param Varien_Object $data
     * @return Mage_Newsletter_Model_Queue
     */
    public function addTemplateData( $data )
    {
        if ($data->getTemplateId()) {
            $this->setTemplate(Mage::getModel('newsletter/template')
                                    ->load($data->getTemplateId()));
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






        foreach($collection->getItems() as $item) {

            $this->getTemplate()->send($item, array('subscriber'=>$item), null, $this);

        }

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
}
