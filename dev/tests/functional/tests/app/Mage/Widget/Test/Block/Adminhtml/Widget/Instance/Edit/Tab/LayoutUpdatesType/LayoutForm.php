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

namespace Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Responds for filling layout form.
 */
class LayoutForm extends Form
{
    /**
     * Widget option chooser button.
     *
     * @var string
     */
    protected $chooser = '//*[@class="chooser_container"]//a[@class="widget-option-chooser"]/img';

    /**
     * Widget option apply button.
     *
     * @var string
     */
    protected $apply = '//*[@class="chooser_container"]//a[contains(@onclick,"hideEntityChooser")]/img';

    /**
     * Template block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Filling layout form.
     *
     * @param array $layoutFields
     * @param Element $element [optional]
     * @return void
     */
    public function fillForm(array $layoutFields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($layoutFields);
        foreach ($mapping as $key => $values) {
            $this->_fill([$key => $values], $element);
            $this->getTemplateBlock()->waitLoader();
        }
    }

    /**
     * Getting options data form on the product form.
     *
     * @param array $fields [optional]
     * @param Element $element [optional]
     * @return array
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        return $this->_getData($mapping, $element);
    }

    /**
     * Get template block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
