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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Core\Test\Block;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;

/**
 * Global messages block.
 */
class Messages extends Block
{
    /**
     * Success message selector.
     *
     * @var string
     */
    protected $successMessage = '.success-msg li';

    /**
     * Error message.
     *
     * @var string
     */
    protected $errorMessage = '.error-msg li';

    /**
     * Warning message selector.
     *
     * @var string
     */
    protected $warningMessage = '[data-ui-id$=message-warning]';

    /**
     * Message link selector.
     *
     * @var string
     */
    protected $messageLink = "//a[contains(.,'%s')]";

    /**
     * Notice message selector.
     *
     * @var string
     */
    protected $noticeMessage = '.notice-msg';

    /**
     * Wait for success message.
     *
     * @return bool
     */
    public function waitSuccessMessage()
    {
        return $this->waitForElementVisible($this->successMessage);
    }

    /**
     * Wait for error message.
     *
     * @return bool
     */
    public function waitErrorMessage()
    {
        return $this->waitForElementVisible($this->errorMessage);
    }

    /**
     * Get all success messages which are present on the page.
     *
     * @return string|array
     */
    public function getSuccessMessages()
    {
        $this->waitForElementVisible($this->successMessage);
        $elements = $this->_rootElement->getElements($this->successMessage);

        return $this->getTextFromElements($elements);
    }

    /**
     * Get all error messages which are present on the page.
     *
     * @return string|array
     */
    public function getErrorMessages()
    {
        $this->waitForElementVisible($this->errorMessage);
        $elements = $this->_rootElement->getElements($this->errorMessage);

        return $this->getTextFromElements($elements);
    }

    /**
     * Get text from specified elements.
     *
     * @param Element[] $elements
     * @return string|array
     */
    protected function getTextFromElements(array $elements)
    {
        $messages = [];
        /** Element $element */
        foreach ($elements as $key => $element) {
            $messages[$key] = $element->getText();
        }

        return count($messages) > 1 ? $messages : $messages[0];
    }

    /**
     * Check is visible messages
     *
     * @param string $messageType
     * @return bool
     */
    public function isVisibleMessage($messageType)
    {
        return $this->_rootElement
            ->find($this->{$messageType . 'Message'}, Locator::SELECTOR_CSS)
            ->isVisible();
    }

    /**
     * Get warning message which is present on the page
     *
     * @return string
     */
    public function getWarningMessages()
    {
        $this->waitForElementVisible($this->warningMessage);
        return $this->_rootElement->find($this->warningMessage)->getText();
    }

    /**
     * Click on link in the messages which are present on the page.
     *
     * @param string $messageType
     * @param string $linkText
     * @return void
     */
    public function clickLinkInMessages($messageType, $linkText)
    {
        if ($this->isVisibleMessage($messageType)) {
            $this->_rootElement
                ->find($this->{$messageType . 'Message'}, Locator::SELECTOR_CSS)
                ->find(sprintf($this->messageLink, $linkText), Locator::SELECTOR_XPATH)
                ->click();
        }
    }

    /**
     * Get notice message which is present on the page.
     *
     * @return string
     */
    public function getNoticeMessages()
    {
        $this->waitForElementVisible($this->noticeMessage);
        return $this->_rootElement->find($this->noticeMessage)->getText();
    }
}
