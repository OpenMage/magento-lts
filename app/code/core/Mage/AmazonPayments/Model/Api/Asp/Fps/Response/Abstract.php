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
 * @package     Mage_AmazonPayments
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AmazonPayments FPS base response Model
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract extends Varien_Object
{
    /*
     * Response statuses
     */
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_FAILURE = 'Failure';
    const STATUS_PENDING = 'Pending';
    const STATUS_RESERVED = 'Reserved';
    const STATUS_SUCCESS = 'Success';
    const STATUS_ERROR = 'Error';
    
    /**
     * Init object 
     *
     * @param Varien_Simplexml_Element $responseBody
     * @return Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract
     */
    public function init($responseBody)
    {
        if (!$this->parse($responseBody)) {
            return false;
        }
        return $this;
    }
    
    /**
     * Parse response body
     *
     * @param Varien_Simplexml_Element $responseBody
     * @return bool
     */
    protected function parse($responseBody)
    {
        $responseId = (string)$responseBody->ResponseMetadata->RequestId;
        if($responseId == '') {
           return false;   
        }
        $this->setData('Id', $responseId);
        
        if (!$status = $this->getData('Status')) {
            return false;
        }
        if ($status != self::STATUS_CANCELLED &&
            $status != self::STATUS_FAILURE &&
            $status != self::STATUS_PENDING &&
            $status != self::STATUS_RESERVED &&
            $status != self::STATUS_SUCCESS) {
              return false;       
        }
        return true;
    }

    /**
     * Return status of respons
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('Status');        
    }
    
    /**
     * Return response ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getData('Id');        
    }
    
}
