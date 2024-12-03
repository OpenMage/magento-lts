<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter Data Helper
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_TEMPLATE_FILTER = 'global/newsletter/tempate_filter';

    protected $_moduleName = 'Mage_Newsletter';

    /**
     * Retrieve subsription confirmation url
     *
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @return string
     */
    public function getConfirmationUrl($subscriber)
    {
        return Mage::getModel('core/url')
            ->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/confirm', [
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true
            ]);
    }

    /**
     * Retrieve unsubsription url
     *
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @return string
     */
    public function getUnsubscribeUrl($subscriber)
    {
        return Mage::getModel('core/url')
            ->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/unsubscribe', [
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true
            ]);
    }

    /**
     * Retrieve Template processor for Newsletter template
     *
     * @return false|Mage_Core_Model_Abstract|Varien_Filter_Template
     */
    public function getTemplateProcessor()
    {
        $model = (string)Mage::getConfig()->getNode(self::XML_PATH_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }
}
