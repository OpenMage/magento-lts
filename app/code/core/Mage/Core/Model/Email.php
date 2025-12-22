<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
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
 * @package    Mage_Adminhtml
 *
 * @method getFromEmail()
 * @method getFromName()
 * @method string       getTemplate()
 * @method array|string getToEmail()
 * @method getToName()
 * @method string getType()
 * @method $this  setFromEmail(string $string)
 * @method $this  setFromName(string $string)
 * @method $this  setTemplate(string $string)
 * @method $this  setToEmail(array|string $string)
 * @method $this  setToName(string $string)
 * @method $this  setType(string $string)
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
     * @param  array|string $var
     * @param  null|string  $value
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

        $mail = new Zend_Mail('utf-8');
        $transport = new Varien_Object();

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        } else {
            $mail->setBodyText($this->getBody());
        }

        $mail->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());

        Mage::dispatchEvent('email_send_before', [
            'mail'      => $mail,
            'template'  => $this->getTemplate(),
            'transport' => $transport,
            'variables' => $this->getTemplateVars(),
        ]);

        if ($transport->getTransport()) {
            $mail->send($transport->getTransport());
        } else {
            $mail->send();
        }

        Mage::dispatchEvent('email_send_after', [
            'to'         => $this->getToEmail(),
            'subject'    => $this->getSubject(),
            'email_body' => $this->getBody(),
        ]);

        return $this;
    }
}
