<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter session model
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('newsletter');
    }

    /**
     * @param  string $message
     * @return $this
     */
    public function addError($message)
    {
        $this->setErrorMessage($message);
        return $this;
    }

    /**
     * @param  string $message
     * @return $this
     */
    public function addSuccess($message)
    {
        $this->setSuccessMessage($message);
        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        $message = $this->getErrorMessage();
        $this->unsErrorMessage();
        return $message;
    }

    /**
     * @return string
     */
    public function getSuccess()
    {
        $message = $this->getSuccessMessage();
        $this->unsSuccessMessage();
        return $message;
    }
}
