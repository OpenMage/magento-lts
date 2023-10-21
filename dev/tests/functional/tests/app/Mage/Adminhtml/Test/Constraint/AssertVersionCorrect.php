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

namespace Mage\Adminhtml\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;


/**
 * Assert that created Store View can be found in Stores grid.
 */
class AssertVersionCorrect extends AbstractConstraint
{
    /**
     * Assert that created Store View can be found in Stores grid by name.
     *
     * @param Dashboard $dashboard
     * @return void
     */
    public function processAssert(Dashboard $dashboard)
    {
        $config = \Magento\Mtf\ObjectManagerFactory::getObjectManager()->get('Magento\Mtf\Config\GlobalConfig');
        $newVersion = $config->get('version/0/value');
        $dashboard->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $dashboard->getFooter()->findVersion($newVersion)->isVisible()
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Magento has benn upgraded successfully';
    }
}
