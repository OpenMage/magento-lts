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

namespace Mage\Adminhtml\Test\Constraint;

use Mage\Adminhtml\Test\Fixture\Store;
use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Mage\Adminhtml\Test\Fixture\Website;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that product is present on custom website.
 */
class AssertProductIsPresentOnCustomWebsite extends AbstractConstraint
{
    /**
     * Website fixture.
     *
     * @var Website
     */
    protected $website;

    /**
     * Path to magento root.
     *
     * @var string
     */
    protected $magentoRoot;

    /**
     * Path to website folder.
     *
     * @var string
     */
    protected $websiteFolder;

    /**
     * Base dir path.
     *
     * @var string
     */
    protected $baseDir;

    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that product is present on custom website.
     *
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductView $catalogProductView
     * @param Store $storeView
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CatalogProductView $catalogProductView,
        Store $storeView,
        Browser $browser
    ) {
        /** @var StoreGroup $store */
        $store = $storeView->getDataFieldConfig('group_id')['source']->getStoreGroup();
        $this->website = $store->getDataFieldConfig('website_id')['source']->getWebsite();
        $this->setupPaths();
        $this->createWebsiteFolder();
        $this->placeFiles();
        $this->enableWebsiteConfiguration($fixtureFactory);

        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataset' => 'default', 'data' => ['website_ids' => ['websites' => [$this->website]]]]
        );
        $product->persist();

        $code = $this->website->getCode();
        $productUrl = $_ENV['app_frontend_url'] . "websites/$code/" . $product->getUrlKey() . ".html";
        $browser->open(str_replace("index.php/", "", $productUrl));

        \PHPUnit_Framework_Assert::assertTrue(
            $catalogProductView->getViewBlock()->isVisible(),
            "Searched product is not visible."
        );
    }

    /**
     * Setup paths for assert.
     *
     * @throws \Exception
     * @return void
     */
    protected function setupPaths()
    {
        $code = $this->website->getCode();
        $this->magentoRoot = $this->resolveMagentoRoot();
        $this->websiteFolder = $this->magentoRoot . DIRECTORY_SEPARATOR . "websites" . DIRECTORY_SEPARATOR . $code;
    }

    /**
     * Resolve magento root path.
     *
     * @return string
     */
    protected function resolveMagentoRoot()
    {
        $realPath = realpath(MTF_BP . '/../../../');
        preg_match('@instance-\d@', $_ENV['app_frontend_url'], $matches);
        return isset($matches[0]) ? preg_replace('@instance-\d@', $matches[0], $realPath) : $realPath;
    }

    /**
     * Create Website folder in magento root.
     *
     * @return void
     */
    protected function createWebsiteFolder()
    {
        $oldMask = umask(0);
        if (!is_dir($this->magentoRoot . DIRECTORY_SEPARATOR . 'websites')) {

            mkdir($this->magentoRoot . DIRECTORY_SEPARATOR . 'websites', 0777);
        }
        mkdir($this->websiteFolder, 0777);
        umask($oldMask);
    }

    /**
     * Place files in created folder in magento root dir.
     *
     * @return void
     */
    protected function placeFiles()
    {
        $htaccessFile = file_get_contents($this->magentoRoot . DIRECTORY_SEPARATOR . '.htaccess');
        file_put_contents($this->websiteFolder . DIRECTORY_SEPARATOR . ".htaccess", $htaccessFile);
        $indexPhpFile = file_get_contents($this->magentoRoot . DIRECTORY_SEPARATOR . 'index.php');

        $replace = ["getcwd()", "(\$mageRunCode, \$mageRunType)"];
        $replacement = ["'{$this->magentoRoot}'", "('{$this->website->getCode()}', 'website')"];
        $indexPhpFile = str_replace($replace, $replacement, $indexPhpFile);

        file_put_contents($this->websiteFolder . DIRECTORY_SEPARATOR . "index.php", $indexPhpFile);
    }

    /**
     * Enable website configuration.
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    protected function enableWebsiteConfiguration(FixtureFactory $fixtureFactory)
    {
        $code = $this->website->getCode();
        $scope = "web/website/$code/";
        $data = [
            $scope . 'secure/base_link_url' => [
                'scope' => $scope,
                'value' => "{{secure_base_url}}websites/$code/"
            ],
            $scope . 'unsecure/base_link_url' => [
                'scope' => $scope,
                'value' => "{{unsecure_base_url}}websites/$code/"
            ]
        ];

        $fixture = $fixtureFactory->createByCode('customConfigData', ['data' => $data]);
        $fixture->persist();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Product is present on custom website.";
    }
}
