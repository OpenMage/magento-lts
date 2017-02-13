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

namespace Mage\Adminhtml\Test\Block\Page;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Header block.
 */
class Footer extends Block
{
    /**
     * @var string
     */
    protected $versionPath = '//div[@class="footer" and contains(.,"Magento ver. ';

    /**
     * @param $currentVersion
     * @return string
     */
    protected function getVersionLocator($currentVersion)
    {
        return $this->versionPath . $currentVersion . '")]';
    }

    /**
     *
     */
    public function findVersion($currentVersion)
    {
       return $this->_rootElement->find($this->getVersionLocator($currentVersion), Locator::SELECTOR_XPATH);
    }
}
