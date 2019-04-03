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

namespace Mage\Adminhtml\Test\Block\Widget;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Block\Form;

/**
 * Is used to represent any tab on the page.
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Tab extends Form
{
    /**
     * Field with Mage error.
     *
     * @var string
     */
    protected $mageErrorField = '//*[contains(@class,"field ")][.//*[@class="mage-error"]]';

    /**
     * Fields label with mage error.
     *
     * @var string
     */
    protected $mageErrorLabel = './label';

    /**
     * Mage error text.
     *
     * @var string
     */
    protected $mageErrorText = './/*[@class="mage-error"]';

    /**
     * Notice message css selector.
     *
     * @var string
     */
    protected $noticeMassage = '.validation-advice';

    /**
     * Label for notice message.
     *
     * @var string
     */
    protected $noticeLabel = './../../td[@class="label"]/label';

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $data = $this->dataMapping($fields);
        $this->_fill($data, $element);

        return $this;
    }

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $data = $this->dataMapping($fields);
        return $this->_getData($data, $element);
    }

    /**
     * Update data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return void
     */
    public function updateFormTab(array $fields, Element $element = null)
    {
        $this->fillFormTab($fields, $element);
    }

    /**
     * Get array of label => js error text.
     *
     * @return array
     */
    public function getJsErrors()
    {
        $data = [];
        $elements = $this->_rootElement->getElements($this->mageErrorField, Locator::SELECTOR_XPATH);
        foreach ($elements as $element) {
            $error = $element->find($this->mageErrorText, Locator::SELECTOR_XPATH);
            if ($error->isVisible()) {
                $label = $element->find($this->mageErrorLabel, Locator::SELECTOR_XPATH)->getText();
                $data[$label] = $error->getText();
            }
        }
        return $data;
    }

    /**
     * Get require notice messages.
     *
     * @return array
     */
    public function getRequireNoticeMessages()
    {
        $messages = [];
        $elements = $this->_rootElement->getElements($this->noticeMassage);
        foreach ($elements as $element) {
            $error = $element->find($this->noticeLabel, Locator::SELECTOR_XPATH);
            if ($error->isVisible()) {
                $label = $this->getNoticeLabel($element);
                $messages[$label] = $element->getText();
            }
        }

        return $messages;
    }

    /**
     * Get label for notice message.
     *
     * @param Element $element
     * @return string
     */
    protected function getNoticeLabel(Element $element)
    {
        $noticeLabel = str_replace(' ', '', strtolower($element->find($this->noticeLabel, Locator::SELECTOR_XPATH)->getText()));
        return str_replace('*', '', $noticeLabel);
    }
}
