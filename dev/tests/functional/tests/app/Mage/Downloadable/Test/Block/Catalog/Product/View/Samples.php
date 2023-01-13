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

namespace Mage\Downloadable\Test\Block\Catalog\Product\View;

use Magento\Mtf\Block\Block;

/**
 * Downloadable samples blocks on frontend.
 */
class Samples extends Block
{
    /**
     * Title selector for samples block.
     *
     * @var string
     */
    protected $titleBlock = '.item-options dt';

    /**
     * Title selector item sample link.
     *
     * @var string
     */
    protected $linkTitle = '.item-options dd a';

    /**
     * Get title for Samples block.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_rootElement->find($this->titleBlock)->getText();
    }

    /**
     * Get sample links.
     *
     * @return array
     */
    public function getLinks()
    {
        $links = $this->_rootElement->getElements($this->linkTitle);
        $linksData = [];

        foreach ($links as $link) {
            $linksData[] = [
                'title' => $link->getText(),
            ];
        }

        return $linksData;
    }
}
