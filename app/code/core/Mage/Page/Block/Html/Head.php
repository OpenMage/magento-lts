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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @return Mage_Page_Block_Html_Head
     */
    public function addCss($name, $params = "")
    {
        $this->addItem('skin_css', $name, $params);
        return $this;
    }

    /**
     * Add JavaScript file to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @return Mage_Page_Block_Html_Head
     */
    public function addJs($name, $params = "")
    {
        $this->addItem('js', $name, $params);
        return $this;
    }

    /**
     * Add CSS file for Internet Explorer only to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @return Mage_Page_Block_Html_Head
     */
    public function addCssIe($name, $params = "")
    {
        $this->addItem('skin_css', $name, $params, 'IE');
        return $this;
    }

    /**
     * Add JavaScript file for Internet Explorer only to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @return Mage_Page_Block_Html_Head
     */
    public function addJsIe($name, $params = "")
    {
        $this->addItem('js', $name, $params, 'IE');
        return $this;
    }

    /**
     * Add Link element to HEAD entity
     *
     * @param string $rel forward link types
     * @param string $href URI for linked resource
     * @return Mage_Page_Block_Html_Head
     */
    public function addLinkRel($rel, $href)
    {
        $this->addItem('link_rel', $rel, $href);
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
     * @return Mage_Page_Block_Html_Head
     */
    public function addItem($type, $name, $params=null, $if=null, $cond=null)
    {
        if ($type==='skin_css' && empty($params)) {
            $params = 'media="all"';
        }
        $this->_data['items'][$type.'/'.$name] = array(
            'type'   => $type,
            'name'   => $name,
            'params' => $params,
            'if'     => $if,
            'cond'   => $cond,
       );
        return $this;
    }

    /**
     * Remove Item from HEAD entity
     *
     * @param string $type
     * @param string $name
     * @return Mage_Page_Block_Html_Head
     */
    public function removeItem($type, $name)
    {
        unset($this->_data['items'][$type.'/'.$name]);
        return $this;
    }

    /**
     * Retrieve HEAD fragment HTML with CSS/JS/RSS definations
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        $lines  = array();
        $baseJs = Mage::getBaseUrl('js');
        $html   = '';

        $script     = '<script type="text/javascript" src="%s" %s></script>';
        $stylesheet = '<link rel="stylesheet" type="text/css" href="%s" %s />';
        $alternate  = '<link rel="alternate" type="%s" href="%s" %s />';
        $linkRel    = '<link rel="%s" href="%s" />';

        foreach ($this->_data['items'] as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond'])) {
                continue;
            }
            $if = !empty($item['if']) ? $item['if'] : '';
            switch ($item['type']) {
                case 'js':
                    #$lines[$if]['other'][] = sprintf($script, $baseJs.$item['name'], $item['params']);
                    $lines[$if]['script'][] = $item['name'];
                    break;

                case 'js_css':
                    //proxying css will require real-time prepending path to all image urls, should we do it?
                    $lines[$if]['other'][] = sprintf($stylesheet, $baseJs.$item['name'], $item['params']);
                    #$lines[$if]['stylesheet'][] = $item['name'];
                    break;

                case 'skin_js':
                    $lines[$if]['other'][] = sprintf($script, $this->getSkinUrl($item['name']), $item['params']);
                    break;

                case 'skin_css':
                    $lines[$if]['other'][] = sprintf($stylesheet, $this->getSkinUrl($item['name']), $item['params']);
                    break;

                case 'rss':
                    $lines[$if]['other'][] = sprintf($alternate, 'application/rss+xml'/*'text/xml' for IE?*/, $item['name'], $item['params']);
                    break;

                case 'link_rel':
                    $lines[$if]['other'][] = sprintf($linkRel, $item['name'], $item['params']);
                    break;
            }
        }

        foreach ($lines as $if=>$items) {
            if (!empty($if)) {
                $html .= '<!--[if '.$if.']>'."\n";
            }
            if (!empty($items['script'])) {
                $scriptItems = array();
                if (Mage::getStoreConfigFlag('dev/js/merge_files')) {
                    $scriptItems = $this->getChunkedItems($items['script'], 'index.php?c=auto&amp;f=');
                } else {
                    $scriptItems = $items['script'];
                }
                foreach ($scriptItems as $item) {
                    $html .= sprintf($script, $baseJs.$item, '') . "\n";
                }
//                foreach (array_chunk($items['script'], 15) as $chunk) {
//                    $html .= sprintf($script, $baseJs.'index.php/x.js?f='.join(',',$chunk), '')."\n";
//                }
            }
            if (!empty($items['stylesheet'])) {
                foreach ($this->getChunkedItems($items['stylesheet'], $baseJs.'index.php?c=auto&amp;f=') as $item) {
                    $html .= sprintf($stylesheet, $item, '')."\n";
                }
//                foreach (array_chunk($items['stylesheet'], 15) as $chunk) {
//                    $html .= sprintf($stylesheet, $baseJs.'index.php/x.css?f='.join(',',$chunk), '')."\n";
//                }
            }
            if (!empty($items['other'])) {
                $html .= join("\n", $items['other'])."\n";
            }
            if (!empty($if)) {
                $html .= '<![endif]-->'."\n";
            }
        }

        return $html;
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
        $chunks = array();
        $chunk  = $prefix;
        foreach ($items as $item) {
            if (strlen($chunk.','.$item)>$maxLen) {
                $chunks[] = $chunk;
                $chunk = $prefix;
            }
            $chunk .= ','.$item;
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
            $this->_data['content_type'] = $this->getMediaType().'; charset='.$this->getCharset();
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
     * @return Mage_Page_Block_Html_Head
     */
    public function setTitle($title)
    {
        $this->_data['title'] = Mage::getStoreConfig('design/head/title_prefix').' '.$title
            .' '.Mage::getStoreConfig('design/head/title_suffix');
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
        return htmlspecialchars(html_entity_decode($this->_data['title'], ENT_QUOTES, 'UTF-8'));
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
     * Get miscellanious scripts/styles to be included in head before head closing tag
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
}
