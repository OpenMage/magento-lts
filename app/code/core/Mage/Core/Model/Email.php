<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Possible data fields:
 *
 * - subject
 * - to
 * - from
 * - body
 * - template (file name)
 * - module (for template)
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method getFromEmail()
 * @method $this setFromEmail(string $string)
 * @method getFromName()
 * @method $this setFromName(string $string)
 * @method string getTemplate()
 * @method $this setTemplate(string $string)
 * @method string|array getToEmail()
 * @method $this setToEmail(string|array $string)
 * @method getToName()
 * @method $this setToName(string $string)
 * @method string getType()
 * @method $this setType(string $string)
 */
class Mage_Core_Model_Email extends Varien_Object
{
    protected $_tplVars = [];

    /**
     * @var Mage_Core_Block_Template
     */
    protected $_block;

    public function __construct()
    {
        // TODO: move to config
        $this->setFromName('Magento');
        $this->setFromEmail('magento@varien.com');
        $this->setType('text');
    }

    /**
     * @param string|array $var
     * @param string|null $value
     * @return $this
     */
    public function setTemplateVar($var, $value = null)
    {
        if (is_array($var)) {
            foreach ($var as $index => $value) {
                $this->_tplVars[$index] = $value;
            }
        } else {
            $this->_tplVars[$var] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateVars()
    {
        return $this->_tplVars;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        $body = $this->getData('body');
        if (empty($body) && $this->getTemplate()) {
            $this->_block = Mage::getModel('core/layout')->createBlock('core/template', 'email')
                ->setArea('frontend')
                ->setTemplate($this->getTemplate());
            foreach ($this->getTemplateVars() as $var => $value) {
                $this->_block->assign($var, $value);
            }
            $this->_block->assign('_type', strtolower($this->getType()))
                ->assign('_section', 'body');
            $body = $this->_block->toHtml();
        }
        return $body;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        $subject = $this->getData('subject');
        if (empty($subject) && $this->_block) {
            $this->_block->assign('_section', 'subject');
            $subject = $this->_block->toHtml();
        }
        return $subject;
    }

    /**
     * @return $this
     * @throws Zend_Mail_Exception
     */
    public function send()
    {
        if (Mage::getStoreConfigFlag('system/smtp/disable')) {
            return $this;
        }

        $mail = new Zend_Mail();

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        } else {
            $mail->setBodyText($this->getBody());
        }

        $mail->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());
        $mail->send();

        return $this;
    }
}
