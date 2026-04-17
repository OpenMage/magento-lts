<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter Data Helper
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_TEMPLATE_FILTER = 'global/newsletter/tempate_filter';

    protected $_moduleName = 'Mage_Newsletter';

    /**
     * Retrieve subsription confirmation url
     *
     * @param  Mage_Newsletter_Model_Subscriber $subscriber
     * @return string
     */
    public function getConfirmationUrl($subscriber)
    {
        return Mage::getModel('core/url')
            ->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/confirm', [
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true,
            ]);
    }

    /**
     * Retrieve unsubsription url
     *
     * @param  Mage_Newsletter_Model_Subscriber $subscriber
     * @return string
     */
    public function getUnsubscribeUrl($subscriber)
    {
        return Mage::getModel('core/url')
            ->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/unsubscribe', [
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true,
            ]);
    }

    /**
     * Retrieve Template processor for Newsletter template
     *
     * @return false|Mage_Core_Model_Abstract|Varien_Filter_Template
     */
    public function getTemplateProcessor()
    {
        $model = (string) Mage::getConfig()->getNode(self::XML_PATH_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }
}
