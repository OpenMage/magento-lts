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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Connect\Test\TestCase;

use Mage\Admin\Test\Fixture\User;
use Mage\Connect\Test\Fixture\Connect;
use \Mage\Connect\Test\Page\ConnectManager;
use Mage\Connect\Test\Constraint\AssertChannelTextPresent;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\ObjectManagerFactory;

class UpgradeTest extends Injectable
{
    /* tags */
    const TEST_TYPE = 'upgrade';
    /* end tags */

    /**
     * Connect Manager page.
     *
     * @var ConnectManager
     */
    protected $connectManagerPage;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare configuration settings for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
        $config = \Magento\Mtf\ObjectManagerFactory::getObjectManager()->get('Magento\Mtf\Config\GlobalConfig');
        $adminCred['username'] = $config->get('application/0/backendLogin/0/value');
        $adminCred['password'] = $config->get('application/0/backendPassword/0/value');
        $newVersion['Mage_All_Latest'] = $config->get('version/0/value');
        $adminFixture = $this->fixtureFactory->createByCode('user', ['data' => $adminCred]);
        $connectFixture = $this->fixtureFactory->createByCode('connect', ['data' => $newVersion]);
        return ['adminUser' => $adminFixture, 'connect' => $connectFixture];
    }

    /**
     * Injection data.
     *
     * @param ConnectManager $connectManagerPage
     */
    public function __inject(
        ConnectManager $connectManagerPage
    ) {
        $this->connectManagerPage = $connectManagerPage;
    }

    /**
     * Upgrade Magento via Magento Connect Manager.
     *
     * @param AssertChannelTextPresent $assertChannelTextPresent
     * @param User $adminUser
     * @param Connect $connect
     * @return array
     */
    public function test(AssertChannelTextPresent $assertChannelTextPresent, User $adminUser, Connect $connect)
    {
        $this->connectManagerPage->open();
        $this->connectManagerPage->getConnectLogin()->loginToConnectManager($adminUser);
        $assertChannelTextPresent->processAssert($this->connectManagerPage);
        $this->connectManagerPage->getConnectContent()->checkForUpgrades();
        $this->connectManagerPage->getConnectContent()->selectPackages($connect);
        $this->connectManagerPage->getConnectContent()->commitChanges();
    }
}
