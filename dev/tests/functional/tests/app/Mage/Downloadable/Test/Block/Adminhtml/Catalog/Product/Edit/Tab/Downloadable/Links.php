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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\Client\Locator;

/**
 * Link form of downloadable product.
 */
class Links extends Form
{
    /**
     * Show Links block button.
     *
     * @var string
     */
    protected $showLinks = '#dt-links a';

    /**
     * Add New Row for links button.
     *
     * @var string
     */
    protected $addNewLinkRow = 'button#add_link_item';

    /**
     * Downloadable link item block.
     *
     * @var string
     */
    protected $rowBlock = '//*[@id="link_items_body"]/tr[%d]';

    /**
     * Downloadable link title block.
     *
     * @var string
     */
    protected $title = "#downloadable_links_title";

    /**
     * Get downloadable link item block.
     *
     * @param int $index
     * @param ElementInterface|null $element
     * @return LinkRow
     */
    public function getRowBlock($index, ElementInterface $element = null)
    {
        $element = $element ?: $this->_rootElement;
        return $this->blockFactory->create(
            'Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\LinkRow',
            ['element' => $element->find(sprintf($this->rowBlock, ++$index), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Fill links block.
     *
     * @param array $fields
     * @param Element|null $element
     * @return void
     */
    public function fillLinks(array $fields, Element $element = null)
    {
        $element = $element ?: $this->_rootElement;
        if (!$element->find($this->title)->isVisible()) {
            $element->find($this->showLinks)->click();
        }
        $mapping = $this->dataMapping(
            ['title' => $fields['title'], 'links_purchased_separately' => $fields['links_purchased_separately']]
        );
        $this->_fill($mapping);
        foreach ($fields['downloadable']['link'] as $index => $link) {
            $rowBlock = $this->getRowBlock($index, $element);
            if (!$rowBlock->isVisible()) {
                $element->find($this->addNewLinkRow)->click();
            }
            $rowBlock->fillLinkRow($link);
        }
    }

    /**
     * Get data links block.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataLinks(array $fields = null, Element $element = null)
    {
        $element = $element ?: $this->_rootElement;
        if (!$element->find($this->title)->isVisible()) {
            $element->find($this->showLinks)->click();
        }
        $mapping = $this->dataMapping(
            ['title' => $fields['title'], 'links_purchased_separately' => $fields['links_purchased_separately']]
        );
        $newFields = $this->_getData($mapping);
        foreach ($fields['downloadable']['link'] as $index => $link) {
            $newFields['downloadable']['link'][$index] = $this->getRowBlock($index, $element)
                ->getDataLinkRow($link);
        }
        return $newFields;
    }

    /**
     * Delete all links and clear title.
     *
     * @return void
     */
    public function clearDownloadableData()
    {
        $this->_rootElement->find($this->title)->setValue('');
        $index = 1;
        while ($this->_rootElement->find(sprintf($this->rowBlock, $index), Locator::SELECTOR_XPATH)->isVisible()) {
            $rowBlock = $this->getRowBlock($index - 1);
            $rowBlock->clickDeleteButton();
            ++$index;
        }
    }
}
