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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;

/**
 * Assert that after Store View save successful message appears.
 */
class AssertStoreSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Success store view create message.
     */
    const SUCCESS_MESSAGE = 'The store view has been saved';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after Store View has been created.
     *
     * @param StoreIndex $storeIndex
     * @param string|null $savedMessage
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, $savedMessage = null)
    {
        $expectedMessage = ($savedMessage === null) ? self::SUCCESS_MESSAGE : $savedMessage;
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedMessage,
            $storeIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store View success create message is present.';
    }
}
