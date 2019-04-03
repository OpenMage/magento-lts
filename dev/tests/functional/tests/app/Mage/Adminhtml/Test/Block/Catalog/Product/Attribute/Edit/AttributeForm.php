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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\Edit;

use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Adminhtml\Test\Block\Widget\FormTabs;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Catalog Product Attribute form.
 */
class AttributeForm extends FormTabs
{
    /**
     * Delete button css selector.
     *
     * @var string
     */
    protected $deleteButton = '.scalable.delete';

    /**
     * Get data of the tabs.
     *
     * @param FixtureInterface|null $fixture
     * @param Element|null $element
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData(FixtureInterface $fixture = null, Element $element = null)
    {
        $data = [];
        if ($fixture === null) {
            foreach ($this->tabs as $tabName => $tab) {
                $this->openTab($tabName);
                $tabData = $this->getTabElement($tabName)->getDataFormTab();
                $data = array_merge($data, $tabData);
            }
        } else {
            $tabsFields = $this->getFieldsByTabs($fixture);
            foreach ($tabsFields as $tabName => $fields) {
                $this->openTab($tabName);
                $tabData = $this->getTabElement($tabName)->getDataFormTab($fields);
                $data = array_merge($data, $tabData);
            }
        }
        return $data;
    }

    /**
     * Fill form with tabs.
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);

        $this->openTab('attribute-properties');
        $tabElement = $this->getTabElement('attribute-properties');
        $tabElement->fillFormTab($tabs['attribute-properties'], $element);
        unset($tabs['attribute-properties']);

        return $this->fillTabs($tabs, $element);
    }

    /**
     * Get options ids.
     *
     * @return array
     */
    public function getOptionsIds()
    {
        $this->openTab('manage-options');
        return $this->getTabElement('manage-options')->getOptionsIds();
    }

    /**
     * Get attribute id.
     *
     * @return int
     */
    public function getAttributeId()
    {
        $attributeId = $this->_rootElement->find($this->deleteButton)->getAttribute('onclick');
        preg_match('`attribute_id/(\d+)`', $attributeId, $matches);
        return $matches[1];
    }
}
