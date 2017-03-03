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

namespace Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\Client\Locator;

/**
 * Sample Form of downloadable product.
 */
class Samples extends Form
{
    /**
     * 'Add New Row' button for samples.
     *
     * @var string
     */
    protected $addNewSampleRow = 'button#add_sample_item';

    /**
     * Show Sample block button.
     *
     * @var string
     */
    protected $showSample = 'dt#dt-samples a';

    /**
     * Sample title block.
     *
     * @var string
     */
    protected $samplesTitle = '//input[@name="product[samples_title]"]';

    /**
     * Downloadable sample item block.
     *
     * @var string
     */
    protected $rowBlock = '//*[@id="sample_items_body"]/tr[%d]';

    /**
     * Get downloadable sample item block.
     *
     * @param int $index
     * @param ElementInterface|null $element
     * @return SampleRow
     */
    public function getRowBlock($index, ElementInterface $element = null)
    {
        $element = $element ?: $this->_rootElement;
        return $this->blockFactory->create(
            'Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\SampleRow',
            ['element' => $element->find(sprintf($this->rowBlock, ++$index), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Fill samples block.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return void
     */
    public function fillSamples(array $fields = null, Element $element = null)
    {
        $element = $element ?: $this->_rootElement;
        if (!$element->find($this->samplesTitle, Locator::SELECTOR_XPATH)->isVisible()) {
            $element->find($this->showSample)->click();
        }
        $mapping = $this->dataMapping(['title' => $fields['title']]);
        $this->_fill($mapping);
        foreach ($fields['downloadable']['sample'] as $index => $sample) {
            $element->find($this->addNewSampleRow)->click();
            $this->getRowBlock($index, $element)->fillSampleRow($sample);
        }
    }

    /**
     * Get data samples block.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataSamples(array $fields = null, Element $element = null)
    {
        $element = $element ?: $this->_rootElement;
        if (!$element->find($this->samplesTitle, Locator::SELECTOR_XPATH)->isVisible()) {
            $element->find($this->showSample)->click();
        }
        $mapping = $this->dataMapping(['title' => $fields['title']]);
        $result = $this->_getData($mapping);
        foreach ($fields['downloadable']['sample'] as $index => $sample) {
            $result['downloadable']['sample'][$index] = $this->getRowBlock($index, $element)
                ->getDataSampleRow($sample);
        }
        return $result;
    }
}
