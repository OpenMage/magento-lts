<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 *
 * @method $this setCanLoadCalendarJs(bool $value)
 * @method $this setDescription(string $value)
 * @method $this setKeywords(string $value)
 * @method $this setCanLoadTinyMce(bool $value)
 */
class Mage_Page_Block_Html_Head extends Mage_Core_Block_Template
{
    /**
     * Initialize template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('page/html/head.phtml');
    }

    /**
     * Add CSS file to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @param string $referenceName
     * @param bool $before
     * @return $this
     */
    public function addCss($name, $params = '', $referenceName = '*', $before = null)
    {
        $this->addItem('skin_css', $name, $params, null, null, $referenceName, $before);
        return $this;
    }

    /**
     * Add JavaScript file to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @param string $referenceName
     * @param bool $before
     * @return $this
     */
    public function addJs($name, $params = '', $referenceName = '*', $before = null)
    {
        $this->addItem('js', $name, $params, null, null, $referenceName, $before);
        return $this;
    }

    /**
     * Add CSS file for Internet Explorer only to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @param string $referenceName
     * @param bool $before
     * @return $this
     * @deprecated
     */
    public function addCssIe($name, $params = '', $referenceName = '*', $before = null)
    {
        return $this;
    }

    /**
     * Add JavaScript file for Internet Explorer only to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @param string $referenceName
     * @param bool $before
     * @return $this
     * @deprecated
     */
    public function addJsIe($name, $params = '', $referenceName = '*', $before = null)
    {
        return $this;
    }

    /**
     * Add Link element to HEAD entity
     *
     * @param string $rel forward link types
     * @param string $href URI for linked resource
     * @return $this
     */
    public function addLinkRel($rel, $href)
    {
        $this->addItem('link_rel', $href, 'rel="' . $rel . '"');
        return $this;
    }

    /**
     * Add HEAD Item
     *
     * Allowed types:
     *  - js
     *  - js_css
     *  - skin_js
     *  - skin_css
     *  - rss
     *
     * @param string $type
     * @param string $name
     * @param string $params
     * @param string $if
     * @param string $cond
     * @param string $referenceName name of the item to insert the element before. If name is not found, insert at the end, * has special meaning (before all / before all)
     * @param string|bool $before If true insert before the $referenceName instead of after
     * @return $this
     */
    public function addItem($type, $name, $params = null, $if = null, $cond = null, $referenceName = '*', $before = false)
    {
        // allow skipping of parameters in the layout XML files via empty-string
        if ($params === '') {
            $params = null;
        }
        if ($if === '') {
            $if = null;
        }
        if ($cond === '') {
            $cond = null;
        }

        if ($type === 'skin_css' && empty($params)) {
            $params = 'media="all"';
        }
        $this->_data['items'][$type . '/' . $name] = [
            'type' => $type,
            'name' => $name,
            'params' => $params,
            'if' => $if,
            'cond' => $cond,
        ];

        // that is the standard behaviour
        if ($referenceName === '*' && $before === false) {
            return $this;
        }

        $this->_sortItems($referenceName, $before, $type);

        return $this;
    }

    /**
     * Remove Item from HEAD entity
     *
     * @param string $type
     * @param string $name
     * @return $this
     */
    public function removeItem($type, $name)
    {
        unset($this->_data['items'][$type . '/' . $name]);
        return $this;
    }

    /**
     * Get HEAD HTML with CSS/JS/RSS definitions
     * (actually it also renders other elements, TODO: fix it up or rename this method)
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        // separate items by types
        $lines  = [];
        foreach ($this->_data['items'] as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            $if     = !empty($item['if']) ? $item['if'] : '';
            $params = !empty($item['params']) ? $item['params'] : '';
            switch ($item['type']) {
                case 'js':        // js/*.js
                case 'skin_js':   // skin/*/*.js
                case 'js_css':    // js/*.css
                case 'skin_css':  // skin/*/*.css
                    $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                    break;
                default:
                    $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                    break;
            }
        }

        // prepare HTML
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        $html   = '';
        foreach ($lines as $if => $items) {
            if (empty($items)) {
                continue;
            }
            if (!empty($if)) {
                // @deprecated
                continue;
            }

            // static and skin css
            $html .= $this->_prepareStaticAndSkinElements(
                '<link rel="stylesheet" href="%s"%s >' . PHP_EOL,
                empty($items['js_css']) ? [] : $items['js_css'],
                empty($items['skin_css']) ? [] : $items['skin_css'],
                $shouldMergeCss ? [Mage::getDesign(), 'getMergedCssUrl'] : null,
            );

            // static and skin javascripts
            $html .= $this->_prepareStaticAndSkinElements(
                '<script src="%s"%s></script>' . PHP_EOL,
                empty($items['js']) ? [] : $items['js'],
                empty($items['skin_js']) ? [] : $items['skin_js'],
                $shouldMergeJs ? [Mage::getDesign(), 'getMergedJsUrl'] : null,
            );

            // other stuff
            if (!empty($items['other'])) {
                $html .= $this->_prepareOtherHtmlHeadElements($items['other']) . PHP_EOL;
            }
        }
        return $html;
    }

    /**
     * Merge static and skin files of the same format into 1 set of HEAD directives or even into 1 directive
     *
     * Will attempt to merge into 1 directive, if merging callback is provided. In this case it will generate
     * filenames, rather than render urls.
     * The merger callback is responsible for checking whether files exist, merging them and giving result URL
     *
     * @param string $format - HTML element format for sprintf('<element src="%s"%s>', $src, $params)
     * @param array $staticItems - array of relative names of static items to be grabbed from js/ folder
     * @param array $skinItems - array of relative names of skin items to be found in skins according to design config
     * @param callable $mergeCallback
     * @return string
     */
    protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems, $mergeCallback = null)
    {
        $designPackage = Mage::getDesign();
        $baseJsUrl = Mage::getBaseUrl('js');
        $items = [];
        if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
        }

        // get static files from the js folder, no need in lookups
        foreach ($staticItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? Mage::getBaseDir() . DS . 'js' . DS . $name : $baseJsUrl . $name;
            }
        }

        // lookup each file basing on current theme configuration
        foreach ($skinItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $designPackage->getFilename($name, ['_type' => 'skin'])
                    : $designPackage->getSkinUrl($name, []);
            }
        }

        $html = '';
        foreach ($items as $params => $rows) {
            // attempt to merge
            $mergedUrl = false;
            if ($mergeCallback) {
                $mergedUrl = call_user_func($mergeCallback, $rows);
            }
            // render elements
            $params = trim($params);
            $params = $params ? ' ' . $params : '';
            if ($mergedUrl) {
                $html .= sprintf($format, $mergedUrl, $params);
            } else {
                foreach ($rows as $src) {
                    $html .= sprintf($format, $src, $params);
                }
            }
        }
        return $html;
    }

    /**
     * Classify HTML head item and queue it into "lines" array
     *
     * @see self::getCssJsHtml()
     * @param array $lines
     * @param string $itemIf
     * @param string $itemType
     * @param string $itemParams
     * @param string $itemName
     * @param array $itemThe
     */
    protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams, $itemName, $itemThe)
    {
        $params = $itemParams ? ' ' . $itemParams : '';
        $href   = $itemName;
        switch ($itemType) {
            case 'rss':
                $lines[$itemIf]['other'][] = sprintf(
                    '<link href="%s"%s rel="alternate" type="application/rss+xml">',
                    $href,
                    $params,
                );
                break;
            case 'link_rel':
                $lines[$itemIf]['other'][] = sprintf('<link%s href="%s">', $params, $href);
                break;
        }
    }

    /**
     * Render arbitrary HTML head items
     *
     * @see self::getCssJsHtml()
     * @param array $items
     * @return string
     */
    protected function _prepareOtherHtmlHeadElements($items)
    {
        return implode(PHP_EOL, $items);
    }

    /**
     * Retrieve Chunked Items
     *
     * @param array $items
     * @param string $prefix
     * @param int $maxLen
     * @return array
     */
    public function getChunkedItems($items, $prefix = '', $maxLen = 450)
    {
        $chunks = [];
        $chunk  = $prefix;
        foreach ($items as $item) {
            if (strlen($chunk . ',' . $item) > $maxLen) {
                $chunks[] = $chunk;
                $chunk = $prefix;
            }
            $chunk .= ',' . $item;
        }
        $chunks[] = $chunk;
        return $chunks;
    }

    /**
     * Retrieve Content Type
     *
     * @return string
     */
    public function getContentType()
    {
        if (empty($this->_data['content_type'])) {
            $this->_data['content_type'] = $this->getMediaType() . '; charset=' . $this->getCharset();
        }
        return $this->_data['content_type'];
    }

    /**
     * Retrieve Media Type
     *
     * @return string
     */
    public function getMediaType()
    {
        if (empty($this->_data['media_type'])) {
            $this->_data['media_type'] = Mage::getStoreConfig('design/head/default_media_type');
        }
        return $this->_data['media_type'];
    }

    /**
     * Retrieve Charset
     *
     * @return string
     */
    public function getCharset()
    {
        if (empty($this->_data['charset'])) {
            $this->_data['charset'] = Mage::getStoreConfig('design/head/default_charset');
        }
        return $this->_data['charset'];
    }

    /**
     * Set title element text
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->_data['title'] = Mage::getStoreConfig('design/head/title_prefix') . ' ' . $title
            . ' ' . Mage::getStoreConfig('design/head/title_suffix');
        return $this;
    }

    /**
     * Retrieve title element text (encoded)
     *
     * @return string
     */
    public function getTitle()
    {
        if (empty($this->_data['title'])) {
            $this->_data['title'] = $this->getDefaultTitle();
        }
        return htmlspecialchars(html_entity_decode(trim($this->_data['title']), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Retrieve default title text
     *
     * @return string
     */
    public function getDefaultTitle()
    {
        return Mage::getStoreConfig('design/head/default_title');
    }

    /**
     * Retrieve content for description tag
     *
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->_data['description'])) {
            $this->_data['description'] = Mage::getStoreConfig('design/head/default_description');
        }
        return $this->_data['description'];
    }

    /**
     * Retrieve content for keyvords tag
     *
     * @return string
     */
    public function getKeywords()
    {
        if (empty($this->_data['keywords'])) {
            $this->_data['keywords'] = Mage::getStoreConfig('design/head/default_keywords');
        }
        return $this->_data['keywords'];
    }

    /**
     * Retrieve URL to robots file
     *
     * @return string
     */
    public function getRobots()
    {
        if (empty($this->_data['robots'])) {
            $this->_data['robots'] = Mage::getStoreConfig('design/head/default_robots');
        }
        return $this->_data['robots'];
    }

    /**
     * Get miscellaneous scripts/styles to be included in head before head closing tag
     *
     * @return string
     */
    public function getIncludes()
    {
        if (empty($this->_data['includes'])) {
            $this->_data['includes'] = Mage::getStoreConfig('design/head/includes');
        }
        return $this->_data['includes'];
    }

    /**
     * Getter for path to Favicon
     *
     * @return string
     */
    public function getFaviconFile()
    {
        if (empty($this->_data['favicon_file'])) {
            $this->_data['favicon_file'] = $this->_getFaviconFile();
        }
        return $this->_data['favicon_file'];
    }

    /**
     * Retrieve path to Favicon
     *
     * @return string
     */
    protected function _getFaviconFile()
    {
        $folderName = Mage_Adminhtml_Model_System_Config_Backend_Image_Favicon::UPLOAD_DIR;
        $storeConfig = Mage::getStoreConfig('design/head/shortcut_icon');
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        if (!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            $url = $faviconFile;
        } else {
            $url = $this->getSkinUrl('favicon.ico');
        }
        return $url;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename
     * @return bool
     */
    protected function _isFile($filename)
    {
        if (Mage::helper('core/file_storage_database')->checkDbUsage() && !is_file($filename)) {
            Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }
        return is_file($filename);
    }

    /**
     * @param string $referenceName
     * @param string|bool $before
     * @param string $type
     */
    protected function _sortItems($referenceName, $before, $type)
    {
        // Convert string values ("true"/"false") to bool
        $before = filter_var($before, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $items = $this->_data['items'];
        $newKey = array_key_last($items);
        $newVal = array_pop($items);

        $newItems = [];

        if ($referenceName === '*' && $before === true) {
            $newItems[$newKey] = $newVal;
        }

        $referenceName = $type . '/' . $referenceName;
        foreach ($items as $key => $value) {
            if ($key === $referenceName && $before === true) {
                $newItems[$newKey] = $newVal;
            }

            $newItems[$key] = $value;

            if ($key === $referenceName && $before === false) {
                $newItems[$newKey] = $newVal;
            }
        }

        // replace items only if the reference was found (otherwise insert as last item)
        if (isset($newItems[$newKey])) {
            $this->_data['items'] = $newItems;
        }
    }
}
