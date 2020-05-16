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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\Set;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\ObjectManager;

/**
 * Attribute Set Main block.
 */
class Main extends Block
{
    /**
     * Attribute label locator.
     *
     * @var string
     */
    protected $attributeLabel = ".//*[contains(@id,'tree-div2')]//li[@class='x-tree-node']/div/a/span[text()='%s']";

    /**
     * Add group button locator.
     *
     * @var string
     */
    protected $addGroupButton = '[data-ui-id="adminhtml-catalog-product-set-edit-add-group-button"]';

    /**
     * Attribute css locator for block with tabs.
     *
     * @var string
     */
    protected $attributeLocator = ".//*[contains(@id,'tree-div1')]//li[@class='x-tree-node']/div/a/span[text()='%s']";

    /**
     * Css locator for attribute set name.
     *
     * @var string
     */
    protected $attributeSetName = '#attribute_set_name';

    /**
     * Attribute Groups.
     *
     * @var string
     */
    protected $groups = './/li[@class="x-tree-node" and .//span[text()="%s"]]/ul/li[last()]';

    /**
     * Attribute that will be added to the group.
     *
     * @var string
     */
    protected $attribute = './/*[contains(@class,"x-tree-root-node")]//div[.//span[text()="%s"]]/img[2]';

    /**
     * Move Attribute to Attribute Group.
     *
     * @param array $attributeData
     * @param string $attributeGroup [optional]
     * @return void
     */
    public function moveAttribute(array $attributeData, $attributeGroup = 'General')
    {
        $attribute = isset($attributeData['attribute_code'])
            ? $attributeData['attribute_code']
            : strtolower($attributeData['frontend_label']);

        $this->_rootElement->find(sprintf($this->attribute, 'color'), Locator::SELECTOR_XPATH)->click();
        $attributeLocator = sprintf($this->attribute, $attribute);
        $attribute = $this->_rootElement->find($attributeLocator, Locator::SELECTOR_XPATH);
        $attribute->click();
        $target = $this->_rootElement->find(sprintf($this->groups, $attributeGroup), Locator::SELECTOR_XPATH);
        $this->browser->refresh();
        $attribute->dragAndDrop($target);
    }

    /**
     * Get AttributeSet name from product_set edit page.
     *
     * @return string
     */
    public function getAttributeSetName()
    {
        return $this->_rootElement->find($this->attributeSetName, Locator::SELECTOR_CSS)->getValue();
    }

    /**
     * Checks present Product Attribute on product_set Groups.
     *
     * @param string $attributeLabel
     * @return bool
     */
    public function checkProductAttribute($attributeLabel)
    {
        $attributeLabelLocator = sprintf($this->attributeLocator, $attributeLabel);
        return $this->_rootElement->find($attributeLabelLocator, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Checks present Unassigned Product Attribute.
     *
     * @param string $attributeLabel
     * @return bool
     */
    public function checkUnassignedProductAttribute($attributeLabel)
    {
        $attributeLabelLocator = sprintf($this->attributeLabel, $attributeLabel);

        return $this->_rootElement->find($attributeLabelLocator, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Add attribute set group to Attribute Set.
     *
     * @param string $groupName
     * @return void
     */
    public function addAttributeSetGroup($groupName)
    {
        $this->_rootElement->find($this->addGroupButton)->click();
        $this->browser->setAlertText($groupName);
        $this->browser->acceptAlert();
    }
}
