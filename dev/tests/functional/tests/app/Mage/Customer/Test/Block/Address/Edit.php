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

namespace Mage\Customer\Test\Block\Address;

use Magento\Mtf\Block\Form;
use Mage\Customer\Test\Fixture\Customer;

/**
 * Address edit form on checkout with multishipping pages.
 */
class Edit extends Form
{
    /**
     * Selector for save button.
     *
     * @var string
     */
    protected $save = '[type="submit"]';

    /**
     * Click on save button.
     *
     * @return void
     */
    public function save()
    {
        $this->_rootElement->find($this->save)->click();
    }
}
