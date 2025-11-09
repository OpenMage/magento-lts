<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

use Mage_Adminhtml_Block_System_Config_Form as Form;

/**
 * CMS Page Helper
 *
 * @package    Mage_Cms
 */
class Mage_Cms_Helper_Page extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_NO_ROUTE_PAGE        = 'web/default/cms_no_route';

    public const XML_PATH_NO_COOKIES_PAGE      = 'web/default/cms_no_cookies';

    public const XML_PATH_HOME_PAGE            = 'web/default/cms_home_page';

    protected $_moduleName = 'Mage_Cms';

    /**
     * Renders CMS page on front end
     *
     * Call from controller action
     *
     * @param string $pageId
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function renderPage(Mage_Core_Controller_Front_Action $action, $pageId = null)
    {
        return $this->_renderPage($action, $pageId);
    }

    /**
     * Renders CMS page
     *
     * @param string $pageId
     * @param bool $renderLayout
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _renderPage(Mage_Core_Controller_Varien_Action $action, $pageId = null, $renderLayout = true)
    {
        $page = Mage::getSingleton('cms/page');
        if (!is_null($pageId) && $pageId !== $page->getId()) {
            $delimiterPosition = strrpos($pageId, '|');
            if ($delimiterPosition) {
                $pageId = substr($pageId, 0, $delimiterPosition);
            }

            $page->setStoreId(Mage::app()->getStore()->getId());
            if (!$page->load($pageId)) {
                return false;
            }
        }

        if (!$page->getId()) {
            return false;
        }

        $inRange = Mage::app()->getLocale()
            ->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo());

        if ($page->getCustomTheme()) {
            if ($inRange) {
                [$package, $theme] = explode('/', $page->getCustomTheme());
                Mage::getSingleton('core/design_package')
                    ->setPackageName($package)
                    ->setTheme($theme);
            }
        }

        $action->getLayout()->getUpdate()
            ->addHandle('default')
            ->addHandle('cms_page');

        $action->addActionLayoutHandles();
        if ($page->getRootTemplate()) {
            $handle = ($page->getCustomRootTemplate()
                        && $page->getCustomRootTemplate() != 'empty'
                        && $inRange) ? $page->getCustomRootTemplate() : $page->getRootTemplate();
            $action->getLayout()->helper('page/layout')->applyHandle($handle);
        }

        Mage::dispatchEvent('cms_page_render', ['page' => $page, 'controller_action' => $action]);

        $action->loadLayoutUpdates();
        $layoutUpdate = ($page->getCustomLayoutUpdateXml() && $inRange)
            ? $page->getCustomLayoutUpdateXml() : $page->getLayoutUpdateXml();
        $action->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        $action->generateLayoutXml()->generateLayoutBlocks();

        $contentHeadingBlock = $action->getLayout()->getBlock('page_content_heading');
        if ($contentHeadingBlock) {
            $contentHeading = $this->escapeHtml($page->getContentHeading());
            $contentHeadingBlock->setContentHeading($contentHeading);
        }

        if ($page->getRootTemplate()) {
            $action->getLayout()->helper('page/layout')
                ->applyTemplate($page->getRootTemplate());
        }

        /* @TODO: Move catalog and checkout storage types to appropriate modules */
        $messageBlock = $action->getLayout()->getMessagesBlock();
        foreach (['catalog/session', 'checkout/session', 'customer/session'] as $storageType) {
            $storage = Mage::getSingleton($storageType);
            if ($storage) {
                $messageBlock->addStorageType($storageType);
                $messageBlock->addMessages($storage->getMessages(true));
            }
        }

        if ($renderLayout) {
            $action->renderLayout();
        }

        return true;
    }

    /**
     * Renders CMS Page with more flexibility then original renderPage function.
     * Allows to use also backend action as first parameter.
     * Also takes third parameter which allows not run renderLayout method.
     *
     * @param string $pageId
     * @param bool $renderLayout
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function renderPageExtended(Mage_Core_Controller_Varien_Action $action, $pageId = null, $renderLayout = true)
    {
        return $this->_renderPage($action, $pageId, $renderLayout);
    }

    /**
     * Retrieve page direct URL
     *
     * @param string $pageId
     * @return null|string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getPageUrl($pageId = null)
    {
        $page = Mage::getModel('cms/page');
        if (!is_null($pageId) && $pageId !== $page->getId()) {
            $page->setStoreId(Mage::app()->getStore()->getId());
            if (!$page->load($pageId)) {
                return null;
            }
        }

        if (!$page->getId()) {
            return null;
        }

        return Mage::getUrl(null, ['_direct' => $page->getIdentifier()]);
    }

    public static function getUsedInStoreConfigPaths(?array $paths = []): array
    {
        $searchPaths = [
            self::XML_PATH_NO_ROUTE_PAGE,
            self::XML_PATH_NO_COOKIES_PAGE,
            self::XML_PATH_HOME_PAGE,
        ];

        if (is_array($paths) && $paths !== []) {
            $searchPaths = array_merge($searchPaths, $paths);
        }

        if (is_null($paths)) {
            $searchPaths = [];
        }

        return $searchPaths;
    }

    /**
     * @param self::XML_PATH_* $path
     */
    public static function getConfigLabelFromConfigPath(string $path): string
    {
        return match ($path) {
            self::XML_PATH_NO_ROUTE_PAGE => Mage::helper('cms')->__('No Route Page'),
            self::XML_PATH_NO_COOKIES_PAGE => Mage::helper('cms')->__('No Cookies Page'),
            self::XML_PATH_HOME_PAGE => Mage::helper('cms')->__('Home Page'),
            default => $path,
        };
    }

    /**
     * @param Form::SCOPE_* $scope
     * @throws Mage_Core_Exception
     */
    public static function getScopeInfoFromConfigScope(string $scope, string $scopeId): string
    {
        return match ($scope) {
            Form::SCOPE_ENV => Mage::helper('cms')->__('Environment Config'),
            Form::SCOPE_DEFAULT => Mage::helper('cms')->__('Default Config'),
            Form::SCOPE_WEBSITES => Mage::app()->getWebsite($scopeId)->getName(),
            Form::SCOPE_STORES => sprintf(
                '%s - %s',
                Mage::app()->getStore($scopeId)->getGroup()->getName(),
                Mage::app()->getStore($scopeId)->getName(),
            ),
        };
    }

    /**
     * @throws Mage_Core_Exception
     */
    public static function getValidateConfigErrorMessage(Mage_Core_Model_Resource_Db_Collection_Abstract $isUsedInConfig): string
    {
        $messages = [];

        $data = $isUsedInConfig->getData();
        foreach ($data as $key => $item) {
            $path = $item['path'];
            unset($item['config_id'], $item['path'], $item['updated_at'], $item['value']);
            $data[$path][] = $item;
            unset($data[$key], $key, $path);
        }

        foreach ($data as $path => $items) {
            $scopes = [];
            foreach ($items as $item) {
                $scopes[] = self::getScopeInfoFromConfigScope($item['scope'], $item['scope_id']);
            }

            $messages[] = sprintf(
                '%s (%s)',
                self::getConfigLabelFromConfigPath($path),
                implode(', ', $scopes),
            );
        }

        unset($data, $path, $items, $item, $scopes);

        return implode(', ', $messages);
    }
}
