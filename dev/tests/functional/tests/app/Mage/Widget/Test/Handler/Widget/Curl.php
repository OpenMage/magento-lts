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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Widget\Test\Handler\Widget;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating widgetInstance/frontendApp.
 */
class Curl extends AbstractCurl
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'package_theme' => [
            'Magento Blank' => 2,
        ],
        'type' => [
            'CMS Page Link' => 'cms_page_link',
        ],
        'block' => [
            'Main Content Area' => 'content',
            'Sidebar Additional' => 'sidebar.additional',
            'Sidebar Main' => 'sidebar.main',
        ],
        'page_group' => [
            'All Pages' => 'all_pages',
            'Specified Page' => 'pages',
            'Page Layouts' => 'page_layouts',
            'Non-Anchor Categories' => 'notanchor_categories',
        ],
        'template' => [
            'CMS Page Link Block Template' => 'widget/link/link_block.phtml',
        ],
        'layout_handle' => [
            'Shopping Cart' => 'checkout_cart_index',
        ],
    ];

    /**
     * Widget Instance Template.
     *
     * @var string
     */
    protected $widgetInstanceTemplate = '';

    /**
     * Post request for creating widget instance.
     *
     * @param FixtureInterface $fixture [optional]
     * @throws \Exception
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'widget_instance/save/type/'
            . $data['type'] . '/package/' . $this->prepareTheme($data['package_theme']);

        if (isset($data['page_id'])) {
            $data['parameters']['page_id'] = $data['page_id'][0];
            unset($data['page_id']);
        }
        if ($fixture->hasData('store_ids')) {
            $stores = $fixture->getDataFieldConfig('store_ids')['source']->getStores();
            foreach ($stores as $store) {
                $data['store_ids'][] = $store->getStoreId();
            }
        }
        $data['parameters']['unique_id'] = isset($data['parameters']['unique_id']) ? uniqid() : '';
        unset($data['type']);
        unset($data['package_theme']);
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'class="success-msg"')) {
            throw new \Exception("Widget instance creation by curl handler was not successful! Response: $response");
        }
        $id = $this->getWidgetId($response);

        return ['id' => $id];
    }

    /**
     * Return saved widget id.
     *
     * @param string $response
     * @return int|null
     * @throws \Exception
     */
    protected function getWidgetId($response)
    {
        preg_match_all('~tr title=[^\s]*\/instance_id\/(\d+)~', $response, $matches);
        if (empty($matches[1])) {
            throw new \Exception('Cannot find Widget id');
        }

        return max($matches[1]);
    }

    /**
     * Prepare theme.
     *
     * @param string $theme
     * @return string
     */
    protected function prepareTheme($theme)
    {
        $data = explode('/', $theme);

        return $data[0] . '/theme/' . $data[1];
    }

    /**
     * Prepare data for create widget.
     *
     * @param FixtureInterface $widget
     * @return array
     */
    protected function prepareData(FixtureInterface $widget)
    {
        $data = $this->replaceMappingData($widget->getData());

        return $this->prepareWidgetInstance($data);
    }

    /**
     * Prepare Widget Instance data.
     *
     * @param array $data
     * @throws \Exception
     * @return array
     */
    protected function prepareWidgetInstance($data)
    {
        foreach ($data['widget_instance'] as $key => $widgetInstance) {
            $pageGroup = $widgetInstance['page_group'];

            if (!isset($widgetInstance[$pageGroup]['page_id'])) {
                $widgetInstance[$pageGroup]['page_id'] = 0;
            }
            $method = 'prepare' . str_replace(' ', '', ucwords(str_replace('_', ' ', $pageGroup))) . 'Group';
            if (!method_exists(__CLASS__, $method)) {
                throw new \Exception('Method for prepare page group "' . $method . '" is not exist.');
            }
            $widgetInstance[$pageGroup] = $this->$method($widgetInstance[$pageGroup]);
            $data['widget_instance'][$key] = $widgetInstance;
        }

        return $data;
    }

    /**
     * Prepare All Page Group.
     *
     * @param array $widgetInstancePageGroup
     * @return array
     */
    protected function prepareAllPagesGroup(array $widgetInstancePageGroup)
    {
        $widgetInstancePageGroup['layout_handle'] = 'default';
        $widgetInstancePageGroup['for'] = 'all';
        if (!isset($widgetInstancePageGroup['template'])) {
            $widgetInstancePageGroup['template'] = $this->widgetInstanceTemplate;
        }

        return $widgetInstancePageGroup;
    }

    /**
     * Prepare Non-Anchor Categories Page Group.
     *
     * @param array $widgetInstancePageGroup
     * @return array
     */
    protected function prepareNotanchorCategoriesGroup(array $widgetInstancePageGroup)
    {
        $widgetInstancePageGroup['is_anchor_only'] = 0;
        $widgetInstancePageGroup['for'] = 'all';
        $widgetInstancePageGroup['layout_handle'] = 'catalog_category_view_type_default';

        return $widgetInstancePageGroup;
    }

    /**
     * Prepare Specified Page Group.
     *
     * @param array $widgetInstancePageGroup
     * @return array
     */
    protected function preparePagesGroup(array $widgetInstancePageGroup)
    {
        $widgetInstancePageGroup['for'] = 'all';

        return $widgetInstancePageGroup;
    }
}
