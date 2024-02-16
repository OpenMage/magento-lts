<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Widget\Test\Block\Adminhtml\Widget;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;

/**
 * Widget Chosen Option.
 */
class ChosenOption extends SimpleElement
{
    /**
     * Select page button selector.
     *
     * @var string
     */
    protected $selectButton = '//ancestor::body//button[contains(@class,"btn-chooser")]';

    /**
     * Select block selector.
     *
     * @var string
     */
    protected $selectBlock = "//ancestor::body/div[@id='widget-chooser']";

    /**
     * Entity chooser block class mapping.
     *
     * @var array
     */
    protected $chooserClasses = [
        'page' => '\Mage\Adminhtml\Test\Block\Cms\Page\Widget\Chooser',
    ];

    /**
     * Select widget options.
     *
     * @param array $value
     * @return void
     */
    public function setValue($value)
    {
        $this->clickSelectButton();
        if (isset($value['filter_url_key'])) {
            $grid = $this->getClassBlock($this->chooserClasses['page']);
            $grid->searchAndOpen(['chooser_identifier' => $value['filter_url_key']]);
        }
    }

    /**
     * Clicking to select button.
     *
     * @return void
     */
    protected function clickSelectButton()
    {
        $this->find($this->selectButton, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get block by class.
     *
     * @param string $class
     * @return mixed
     */
    protected function getClassBlock($class)
    {
        return \Magento\Mtf\ObjectManager::getInstance()->create(
            $class,
            ['element' => $this->driver->find($this->selectBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
