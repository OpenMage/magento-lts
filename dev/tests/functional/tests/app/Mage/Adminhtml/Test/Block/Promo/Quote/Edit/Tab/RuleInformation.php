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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Promo\Quote\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;

/**
 * Backend sales rule information tab.
 */
class RuleInformation extends Tab
{
    /**
     * Web site field.
     *
     * @var string
     */
    protected $websiteField = '#rule_website_ids';

    /**
     * Fill data to labels fields on labels tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        parent::fillFormTab($fields, $element);
        $webSiteField = $this->_rootElement->find($this->websiteField, Locator::SELECTOR_CSS, 'multiselect');
        if ($webSiteField->isVisible()) {
            $webSiteFieldValue = $webSiteField->getValue();
            if (empty($webSiteFieldValue)) {
                $webSiteField->setValue('Main Website');
            }
        }

        return $this;
    }
}
