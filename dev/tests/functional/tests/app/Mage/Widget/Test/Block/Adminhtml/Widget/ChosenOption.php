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
