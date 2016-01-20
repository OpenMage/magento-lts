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

namespace Mage\Install\Test\TestCase;

use Mage\Admin\Test\Fixture\User;
use Mage\Install\Test\Constraint\AssertWelcomeWizardTextPresent;
use Mage\Install\Test\Constraint\AssertAgreementTextPresent;
use Mage\Install\Test\Constraint\AssertSuccessDeploy;
use Mage\Install\Test\Page\DownloaderWelcome;
use Mage\Install\Test\Page\DownloaderValidation;
use Mage\Install\Test\Page\DownloaderDeploy;
use Mage\Install\Test\Page\DownloaderDeployEnd;
use Mage\Install\Test\Page\Downloader;
use Mage\Install\Test\Fixture\Install;
use Magento\Mtf\Fixture\FixtureFactory;
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
class DownloaderTest extends InstallTest
{
    /* tags */
    const TEST_TYPE = 'install';
    /* end tags */

    /**
     * Install page.
     *
     * @var DownloaderWelcome
     */
    protected $downloaderWelcome;

    /**
     * DownloaderValidation page.
     *
     * @var DownloaderValidation
     */
    protected $downloaderValidation;

    /**
     * DownloaderDeploy page.
     *
     * @var DownloaderDeploy
     */
    protected $downloaderDeploy;

    /**
     * Downloader page.
     *
     * @var Downloader
     */
    protected  $downloaderDownloader;

    /**
     * Downloader page.
     *
     * @var DownloaderDeployEnd
     */
    protected  $downloaderDeployEnd;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Fixture factory.
     *
     * @var AssertAgreementTextPresent
     */
    protected $assertLicense;

    /**
     * Fixture factory.
     *
     * @var AssertSuccessDeploy
     */
    protected $assertSuccessDeploy;

    /**
     * Injection data.
     *
     * @param DownloaderWelcome $downloaderWelcome
     * @param DownloaderValidation $downloaderValidation
     * @param DownloaderDeploy $downloaderDeploy
     * @param Downloader $downloaderDownloader
     * @param AssertAgreementTextPresent $assertLicense
     * @param AssertSuccessDeploy $assertSuccessDeploy
     * @param DownloaderDeployEnd $downloaderDeployEnd
     * @internal param InstallWizardLocale $installWizardLocale
     * @internal param InstallWizardConfig $installWizardConfig
     * @internal param InstallWizardAdministrator $installWizardAdministrator
     */
    public function __inject(
        DownloaderWelcome $downloaderWelcome,
        DownloaderValidation $downloaderValidation,
        DownloaderDeploy $downloaderDeploy,
        Downloader $downloaderDownloader,
        AssertAgreementTextPresent $assertLicense,
        AssertSuccessDeploy $assertSuccessDeploy,
        DownloaderDeployEnd $downloaderDeployEnd
    ) {
        $this->downloaderWelcome = $downloaderWelcome;
        $this->downloaderValidation = $downloaderValidation;
        $this->downloaderDeploy = $downloaderDeploy;
        $this->downloaderDownloader = $downloaderDownloader;
        $this->assertLicense = $assertLicense;
        $this->assertSuccessDeploy = $assertSuccessDeploy;
        $this->downloaderDeployEnd = $downloaderDeployEnd;
    }

    /**
     * Install Magento via web interface.
     *
     * @param AssertWelcomeWizardTextPresent $assertWelcomeWizardTextPresent
     * @param array $configData
     * @return array
     */
    public function test(AssertWelcomeWizardTextPresent $assertWelcomeWizardTextPresent, $configData)
    {
        // Steps:
        $this->downloaderWelcome->open();
        // Verify license agreement.
        $assertWelcomeWizardTextPresent->processAssert($this->downloaderWelcome);
        $this->downloaderWelcome->getContinueDownloadBlock()->continueValidation();
        $this->downloaderValidation->getContinueDownloadBlock()->continueDeploy();
        $this->downloaderDeploy->getContinueDownloadBlock()->continueDeploy();
        $this->assertSuccessDeploy->processAssert($this->downloaderDeployEnd);
        $this->downloaderDeploy->getContinueDownloadBlock()->continueDownload();
        $this->downloaderDownloader->getContinueDownloadBlock()->startDownload();
        $this->downloaderDownloader->getMessagesBlock()->getSuccessMessages();
        parent::test($this->assertLicense, $configData);
    }

    /**
     * Prepare install fixture for test.
     *
     * @param array $configData
     * @param array $install
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
