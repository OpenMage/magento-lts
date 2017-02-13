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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Widget;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Block\Form;

/**
 * Is used to represent any form with tabs on the page.
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FormTabs extends Form
{
    /**
     * Tabs array.
     *
     * @var array
     */
    protected $tabs = [];

    /**
     * Fields which aren't assigned to any tab.
     *
     * @var array
     */
    protected $unassignedFields = [];

    /**
     * Initialize block.
     *
     * @return void
     */
    protected function init()
    {
        $this->tabs = $this->getFormMapping();
    }

    /**
     * Get path for form *.xml file with mapping
     *
     * @return string
     */
    protected function getFormMapping()
    {
        $result = [];
        $paths = $this->getPaths();
        foreach ($paths as $path) {
            $content = $this->mapper->read($path);
            if (is_array($content)) {
                $result = array_replace_recursive($result, $content);
            }
        }

        return $result;
    }

    /**
     * Get xml files paths for merge.
     *
     * @return array
     */
    protected function getPaths()
    {
        $realPath = str_replace('\\', '/', get_class($this)) . '.xml';
        $paths = glob(MTF_TESTS_PATH . preg_replace('/Mage\/\w+/', '*/*', $realPath));
        if (strpos($realPath, 'Adminhtml') !== false) {
            $paths = array_merge(
                $paths,
                glob(MTF_TESTS_PATH . preg_replace('/Mage\/(\w+)(\/.*Block\/)/', '*/*$2$1/', $realPath)),
                glob(MTF_TESTS_PATH . preg_replace('@.*Adminhtml/(.*)@', '*/Adminhtml/Test/Block/$1', $realPath)),
                glob(MTF_TESTS_PATH . preg_replace('/.*Adminhtml\/(.*)/', '*/*/Test/Block/*/$1', $realPath))
            );
        }
        return array_reverse($paths);
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
        return $this->fillTabs($tabs, $element);
    }

    /**
     * Fill specified form with tabs.
     *
     * @param array $tabs
     * @param Element|null $element
     * @return FormTabs
     */
    protected function fillTabs(array $tabs, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($tabs as $tabName => $tabFields) {
            $tabElement = $this->getTabElement($tabName);
            $this->openTab($tabName);
            $tabElement->fillFormTab($tabFields, $context);
        }
        if (!empty($this->unassignedFields)) {
            $this->fillMissedFields();
        }

        return $this;
    }

    /**
     * Fill fields which weren't found on filled tabs.
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function fillMissedFields()
    {
        foreach ($this->tabs as $tabName => $tabData) {
            $tabElement = $this->getTabElement($tabName);
            if ($this->openTab($tabName)) {
                $mapping = $tabElement->dataMapping($this->unassignedFields);
                foreach ($mapping as $fieldName => $data) {
                    $element = $tabElement->_rootElement->find($data['selector'], $data['strategy'], $data['input']);
                    if ($element->isVisible()) {
                        $element->setValue($data['value']);
                        unset($this->unassignedFields[$fieldName]);
                    }
                }
                if (empty($this->unassignedFields)) {
                    break;
                }
            }
        }

        if (!empty($this->unassignedFields)) {
            throw new \Exception(
                'Could not find all elements on the tabs: ' . implode(', ', array_keys($this->unassignedFields))
            );
        }
    }

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

        if (null === $fixture) {
            foreach ($this->tabs as $tabName => $tab) {
                $this->openTab($tabName);
                $tabData = $this->getTabElement($tabName)->getDataFormTab();
                $data = array_merge($data, $tabData);
            }
        } else {
            $tabsFields = $fixture->hasData() ? $this->getFieldsByTabs($fixture) : [];
            foreach ($tabsFields as $tabName => $fields) {
                $this->openTab($tabName);
                $tabData = $this->getTabElement($tabName)->getDataFormTab($fields, $this->_rootElement);
                $data = array_merge($data, $tabData);
            }
        }

        return $data;
    }

    /**
     * Update form with tabs.
     *
     * @param FixtureInterface $fixture
     * @return FormTabs
     */
    public function update(FixtureInterface $fixture)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        foreach ($tabs as $tab => $tabFields) {
            $this->openTab($tab)->updateFormTab($tabFields, $this->_rootElement);
        }
        return $this;
    }

    /**
     * Create data array for filling tabs.
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function getFieldsByTabs(FixtureInterface $fixture)
    {
        return $this->getFixtureFieldsByTabs($fixture);
    }

    /**
     * Create data array for filling tabs (new fixture specification).
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    private function getFixtureFieldsByTabs(FixtureInterface $fixture)
    {
        $tabs = [];

        $data = $fixture->getData();
        foreach ($data as $field => $value) {
            $attributes = $fixture->getDataFieldConfig($field);
            $attributes['value'] = $value;
            if (array_key_exists('group', $attributes) && $attributes['group'] != 'null') {
                $tabs[$attributes['group']][$field] = $attributes;
            } elseif (!array_key_exists('group', $attributes) && ($field != 'attribute_id')) {
                $this->unassignedFields[$field] = $attributes;
            }
        }
        return $tabs;
    }

    /**
     * Get tab element.
     *
     * @param string $tabName
     * @return Tab
     * @throws \Exception
     */
    public function getTabElement($tabName)
    {
        $tabClass = $this->tabs[$tabName]['class'];
        /** @var Tab $tabElement */
        $tabElement = $this->blockFactory->create($tabClass, ['element' => $this->_rootElement]);
        if (!$tabElement instanceof Tab) {
            throw new \Exception('Wrong Tab Class.');
        }
        $tabElement->setWrapper(isset($this->tabs[$tabName]['wrapper']) ? $this->tabs[$tabName]['wrapper'] : '');
        $tabElement->setMapping(isset($this->tabs[$tabName]['fields']) ? (array)$this->tabs[$tabName]['fields'] : []);

        return $tabElement;
    }

    /**
     * Open tab.
     *
     * @param string $tabName
     * @return Tab
     */
    public function openTab($tabName)
    {
        $selector = $this->tabs[$tabName]['selector'];
        $strategy = isset($this->tabs[$tabName]['strategy'])
            ? $this->tabs[$tabName]['strategy']
            : Locator::SELECTOR_CSS;
        $tab = $this->_rootElement->find($selector, $strategy);
        $tab->click();

        return $this;
    }
}
