<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory module observer
 *
 * @category   Mage
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Observer
{
    public const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';
    public const IMPORT_ENABLE    = 'currency/import/enabled';
    public const IMPORT_SERVICE   = 'currency/import/service';

    public const XML_PATH_ERROR_TEMPLATE  = 'currency/import/error_email_template';
    public const XML_PATH_ERROR_IDENTITY  = 'currency/import/error_email_identity';
    public const XML_PATH_ERROR_RECIPIENT = 'currency/import/error_email';

    /**
     * @param Mage_Cron_Model_Schedule|null $cron
     * @throws Mage_Core_Exception
     */
    public function scheduledUpdateCurrencyRates($cron = null)
    {
        if (!Mage::getStoreConfig(self::IMPORT_ENABLE) || !Mage::getStoreConfig(self::CRON_STRING_PATH)) {
            return;
        }

        $errors  = [];
        $service = (string)Mage::getStoreConfig(self::IMPORT_SERVICE);
        $importModel = null;

        if ($service) {
            try {
                /** @var Mage_Directory_Model_Currency_Import_Abstract $importModel */
                $importModel = Mage::getModel(Mage::getConfig()->getNode('global/currency/import/services/' . $service . '/model')->asArray());
            } catch (Exception $e) {
                $errors[] = 'FATAL ERROR: Unable to initialize the import model (' . $e->getMessage() . ').';
            }
        } else {
            $errors[] = 'FATAL ERROR: Invalid Import Service specified (' . $service . ').';
        }

        if (is_object($importModel)) {
            $rates  = $importModel->fetchRates();
            $errors = $importModel->getMessages();
        }

        if (isset($rates) && !count($errors)) {
            Mage::getModel('directory/currency')->saveRates($rates);
        } else {
            $errors = 'An error occured while importing currency rates, no rates updated.' . "\n- " . implode("\n- ", $errors);

            Mage::logException(new Exception($errors));
            if (is_object($cron)) {
                /** @var Mage_Cron_Model_Schedule $cron */
                $cron->setMessages($errors);
                $cron->setIsError(true);
            }

            /** @var Mage_Core_Model_Translate $translate */
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            /** @var Mage_Core_Model_Email_Template $mailTemplate */
            $mailTemplate = Mage::getModel('core/email_template');
            $mailTemplate->setDesignConfig([
                'area'  => 'frontend',
            ])->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                null,
                [
                    'warnings' => $errors,
                ]
            );

            $translate->setTranslateInline(true);
        }
    }
}
