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

namespace Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples;
use Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links;

/**
 * Product downloadable tab.
 */
class Downloadable extends Tab
{
    /**
     * 'Add New Row' button.
     *
     * @var string
     */
    protected $addNewRow = '[data-action=add-link]';

    /**
     * Downloadable block.
     *
     * @var string
     */
    protected $downloadableBlock = 'dl#tab_content_downloadableInfo';

    /**
     * Get Downloadable block.
     *
     * @param string $type
     * @param Element $element
     * @return Samples|Links
     */
    public function getDownloadableBlock($type, Element $element = null)
    {
        $element = $element ?: $this->_rootElement;
        return $this->blockFactory->create(
            'Mage\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\\' . $type,
            ['element' => $element->find($this->downloadableBlock)]
        );
    }

    /**
     * Get data to fields on downloadable tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $resultFields = [];
        if (isset($fields['downloadable_sample']['value'])) {
            $resultFields['downloadable_sample'] = $this->getDownloadableBlock('Samples')->getDataSamples(
                $fields['downloadable_sample']['value']
            );
        }
        if (isset($fields['downloadable_links']['value'])) {
            $resultFields['downloadable_links'] = $this->getDownloadableBlock('Links')->getDataLinks(
                $fields['downloadable_links']['value']
            );
        }

        return $resultFields;
    }

    /**
     * Fill downloadable information.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['downloadable_sample']['value'])) {
            $this->getDownloadableBlock('Samples')->fillSamples($fields['downloadable_sample']['value']);
        }

        if (isset($fields['downloadable_links']['value'])) {
            $this->getDownloadableBlock('Links')->fillLinks($fields['downloadable_links']['value']);
        }

        return $this;
    }
}
