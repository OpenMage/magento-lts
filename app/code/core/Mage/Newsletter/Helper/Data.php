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
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Newsletter base observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve subsription confirmation url
     *
     * @param   Mage_Newsletter_Model_Subscriber $subscriber
     * @return  string
     */
    public function getConfirmationUrl($subscriber)
    {
        $params = array(
            'id'    => $subscriber->getId(),
            'code'  => $subscriber->getCode()
        );
        return $this->_getUrl('newsletter/subscriber/confirm', $params);
    }
    
    /**
     * Retrieve unsubsription url
     *
     * @param   Mage_Newsletter_Model_Subscriber $subscriber
     * @return  string
     */
    public function getUnsubscribeUrl($subscriber)
    {
        $params = array(
            'id'    => $subscriber->getId(),
            'code'  => $subscriber->getCode()
        );
        return $this->_getUrl('newsletter/subscriber/unsubscribe', $params);
    }
}
