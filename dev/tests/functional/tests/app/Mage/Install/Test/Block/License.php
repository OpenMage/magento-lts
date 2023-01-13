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

namespace Mage\Install\Test\Block;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Block\Block;

/**
 * License block.
 */
class License extends Block
{
    /**
     * License text.
     *
     * @var string
     */
    protected $license = 'h4';

    /**
     * License agreements checkbox selector.
     *
     * @var string
     */
    protected $agree = '#agree';

    /**
     * Get license text.
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->_rootElement->find($this->license)->getText();
    }

    /**
     * Accept license agreements.
     *
     * @return void
     */
    public function acceptLicenseAgreement()
    {
        $this->_rootElement->find($this->agree, Locator::SELECTOR_CSS, 'checkbox')->setValue('Yes');
    }
}
