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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Install\Test\TestCase;

use Mage\Admin\Test\Fixture\User;
use Mage\Install\Test\Constraint\AssertAgreementTextPresent;
use Mage\Install\Test\Page\Install as InstallPage;
use Mage\Install\Test\Fixture\Install;
use Mage\Install\Test\Page\InstallWizardAdministrator;
use Mage\Install\Test\Page\InstallWizardConfig;
use Mage\Install\Test\Page\InstallWizardLocale;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\ObjectManagerFactory;

/**
 * PLEASE ADD NECESSARY INFO BEFORE RUNNING TEST TO ../dev/tests/functional/etc/config.xml
 *
 * Preconditions:
 * 1. Uninstall Magento.
 *
 * Steps:
 * 1. Open Magento install page.
 * 2. Check license agreement text.
 * 3. Accept license and click continue button.
 * 4. Fill DB configuration settings according to dataSet and click continue button.
 * 5. Fill admin user info and click continue button.
 * 6. Perform assertions.
 *
 * @group Installer_and_Upgrade/Downgrade_(PS)
 * @ZephyrId MPERF-7483
 */
class InstallTest extends Injectable
{
    /* tags */
    const TEST_TYPE = 'install';
    /* end tags */

    /**
     * Install page.
     *
     * @var InstallPage
     */
    protected $installPage;

    /**
     * InstallWizardLocale page.
     *
     * @var InstallWizardLocale
     */
    protected $installWizardLocale;

    /**
     * InstallWizardConfig page.
     *
     * @var InstallWizardConfig
     */
    protected $installWizardConfig;

    /**
     * InstallWizardAdministrator page.
     *
     * @var InstallWizardAdministrator
     */
    protected $installWizardAdministrator;

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
        $config = ObjectManagerFactory::getObjectManager()->get('Magento\Mtf\Config\DataInterface');
        // Prepare config data
        $configData['db_host'] = $config->get('install/0/host/0/value');
        $configData['db_user'] = $config->get('install/0/user/0/value');
        $configData['db_pass'] = $config->get('install/0/password/0/value');
        $configData['db_name'] = $config->get('install/0/dbName/0/value');
        $configData['unsecure_base_url'] = $config->get('install/0/baseUrl/0/value');

        return ['configData' => $configData];
    }

    /**
     * Injection data.
     *
     * @param InstallPage $installPage
     * @param InstallWizardLocale $installWizardLocale
     * @param InstallWizardConfig $installWizardConfig
     * @param InstallWizardAdministrator $installWizardAdministrator
     * @return void
     */
    public function __inject(
        InstallPage $installPage,
        InstallWizardLocale $installWizardLocale,
        InstallWizardConfig $installWizardConfig,
        InstallWizardAdministrator $installWizardAdministrator
    ) {
        $this->installPage = $installPage;
        $this->installWizardLocale = $installWizardLocale;
        $this->installWizardConfig = $installWizardConfig;
        $this->installWizardAdministrator = $installWizardAdministrator;
    }

    /**
     * Install Magento via web interface.
     *
     * @param AssertAgreementTextPresent $assertLicense
     * @param array $configData
     * @param array $install [optional]
     * @param array $installLocale [optional]
     * @return array
     */
    public function test(
        AssertAgreementTextPresent $assertLicense,
        array $configData,
        array $install = [],
        array $installLocale = []
    )
    {
        // Preconditions:
        $installConfig = $this->prepareInstallFixture($configData, $install);
        if (isset($install['use_rewrites'])) {
            $user = $this->fixtureFactory->createByCode('user', ['dataset' => 'admin_install_admin']);
        } else {
            $user = $this->fixtureFactory->createByCode('user', ['dataset' => 'admin_for_installation']);
        }
        // Steps:
        $this->installPage->open();
        $this->installPage->getLicenseBlock()->acceptLicenseAgreement();
        // Verify license agreement.
        $assertLicense->processAssert($this->installPage);
        $this->installPage->getContinueBlock()->continueInstallation();
        if (!empty($installLocale)) {
            $locale = $this->prepareInstallLocaleFixture($installLocale);
            $this->installWizardLocale->getLocalizationForm()->fill($locale);
        }
        $this->installWizardLocale->getContinueBlock()->continueInstallation();
        $this->installWizardConfig->getConfigurationForm()->fill($installConfig);
        $this->installWizardConfig->getContinueBlock()->continueInstallation();
        $this->installWizardAdministrator->getPersonalInformationForm()->fill($user);
        $this->installWizardAdministrator->getContinueBlock()->continueInstallation();

        return ['user' => $user];
    }

    /**
     * Prepare install locale fixture for test.
     *
     * @param array $install
     * @return Install
     */
    protected function prepareInstallLocaleFixture(array $install)
    {
        return $this->fixtureFactory->createByCode('installLocale', ['data' => $install]);
    }

    /**
     * Prepare install fixture for test.
     *
     * @param array $configData
     * @return Install
     */
    protected function prepareInstallFixture(array $configData, array $install)
    {
        $dataConfig = array_merge($install, $configData);
        $dataConfig['unsecure_base_url'] = str_replace('index.php/', '', $dataConfig['unsecure_base_url']);
        $dataConfig['unsecure_base_url'] = isset($dataConfig['use_secure'])
            ? str_replace('http', 'https', $dataConfig['unsecure_base_url'])
            : $dataConfig['unsecure_base_url'];
        return $this->fixtureFactory->createByCode('install', ['data' => $dataConfig]);
    }

}
