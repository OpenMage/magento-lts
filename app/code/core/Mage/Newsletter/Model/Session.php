<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */

/**
 * Newsletter session model
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('newsletter');
    }

    /**
     * @param string $message
     * @return $this
     */
    public function addError($message)
    {
        $this->setErrorMessage($message);
        return $this;
    }

    /**
     * @param string $message
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
