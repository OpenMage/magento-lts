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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Observer
{
    const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';
    const IMPORT_ENABLE = 'currency/import/enabled';
    const IMPORT_SERVICE = 'currency/import/service';

    const XML_PATH_ERROR_TEMPLATE = 'currency/import/error_email_template';
    const XML_PATH_ERROR_IDENTITY = 'currency/import/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT = 'currency/import/error_email';

    public function scheduledUpdateCurrencyRates($schedule)
    {
        $importWarnings = array();
        if(!Mage::getStoreConfig(self::IMPORT_ENABLE) || !Mage::getStoreConfig(self::CRON_STRING_PATH)) {
            return;
        }

        $service = Mage::getStoreConfig(self::IMPORT_SERVICE);
        if( !$service ) {
            $importWarnings[] = Mage::helper('directory')->__('FATAL ERROR:') . ' ' . Mage::helper('directory')->__('Invalid Import Service specified.');
        }

        try {
            $importModel = Mage::getModel(Mage::getConfig()->getNode('global/currency/import/services/' . $service . '/model')->asArray());
        } catch (Exception $e) {
            $importWarnings[] = Mage::helper('directory')->__('FATAL ERROR:') . ' ' . Mage::throwException(Mage::helper('directory')->__('Unable to initialize the import model.'));
        }

        $rates = $importModel->fetchRates();
        $errors = $importModel->getMessages();

        if( sizeof($errors) > 0 ) {
            foreach ($errors as $error) {
                $importWarnings[] = Mage::helper('directory')->__('WARNING:') . ' ' . $error;
            }
        }

        if (sizeof($importWarnings) == 0) {
            Mage::getModel('directory/currency')->saveRates($rates);
        }
        else {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate = Mage::getModel('core/email_template');
            $mailTemplate->setDesignConfig(array(
                'area'  => 'frontend',
            ))->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                null,
                array(
                    'warnings'    => join("\n", $importWarnings),
                )
            );

            $translate->setTranslateInline(true);
        }
    }
}
