<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Backend model for saving certificate file in case of using certificate based authentication
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Backend_Cert extends Mage_Core_Model_Config_Data
{
    /**
     * Process additional data before save config
     *
     * @return $this
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            Mage::getModel('paypal/cert')->loadByWebsite($this->getScopeId())->delete();
        }

        if (!isset($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])) {
            return $this;
        }

        $tmpPath = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
        if ($tmpPath && file_exists($tmpPath)) {
            if (!filesize($tmpPath)) {
                Mage::throwException(Mage::helper('paypal')->__('PayPal certificate file is empty.'));
            }

            $this->setValue($_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']);
            $content = Mage::helper('core')->encrypt(file_get_contents($tmpPath));
            Mage::getModel('paypal/cert')->loadByWebsite($this->getScopeId())
                ->setContent($content)
                ->save();
        }

        return $this;
    }

    /**
     * Process object after delete data
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        Mage::getModel('paypal/cert')->loadByWebsite($this->getScopeId())->delete();
        return $this;
    }
}
