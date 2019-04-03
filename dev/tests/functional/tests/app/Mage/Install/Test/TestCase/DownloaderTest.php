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

namespace Mage\Install\Test\TestCase;

use Mage\Install\Test\Constraint\AssertWelcomeWizardTextPresent;
use Mage\Install\Test\Constraint\AssertAgreementTextPresent;
use Mage\Install\Test\Constraint\AssertSuccessDeploy;
use Mage\Install\Test\Page\DownloaderWelcome;
use Mage\Install\Test\Page\DownloaderValidation;
use Mage\Install\Test\Page\DownloaderDeploy;
use Mage\Install\Test\Page\DownloaderDeployEnd;
use Mage\Install\Test\Page\Downloader;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * PLEASE ADD NECESSARY INFO BEFORE RUNNING TEST TO ../dev/tests/functional/etc/config.xml
 *
 * Steps:
 * 1. Open downloader.php page.
 * 2. Download downloader files
 *
 * @group Installer_and_Upgrade/Downgrade_(PS)
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
     * @param AssertSuccessDeploy $assertSuccessDeploy
     * @param DownloaderDeployEnd $downloaderDeployEnd
     */
    public function __inject(
        DownloaderWelcome $downloaderWelcome,
        DownloaderValidation $downloaderValidation,
        DownloaderDeploy $downloaderDeploy,
        Downloader $downloaderDownloader,
        AssertSuccessDeploy $assertSuccessDeploy,
        DownloaderDeployEnd $downloaderDeployEnd
    ) {
        $this->downloaderWelcome = $downloaderWelcome;
        $this->downloaderValidation = $downloaderValidation;
        $this->downloaderDeploy = $downloaderDeploy;
        $this->downloaderDownloader = $downloaderDownloader;
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

    }
}
