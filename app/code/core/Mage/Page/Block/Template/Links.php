<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * Simple links list block
 *
 * @package    Mage_Page
 */
class Mage_Page_Block_Template_Links extends Mage_Core_Block_Template
{
    /**
     * All links
     *
     * @var array
     */
    protected $_links = [];

    /**
     * Cache key info
     *
     * @var null|array
     */
    protected $_cacheKeyInfo = null;

    /**
     * Set default template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('page/template/links.phtml');
    }

    /**
     * Get all links
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * Add link to the list
     *
     * @param string $label
     * @param string $url
     * @param string $title
     * @param bool $prepare
     * @param array $urlParams
     * @param int $position
     * @param string|array $liParams
     * @param string|array $aParams
     * @param string $beforeText
     * @param string $afterText
     * @return $this
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function addLink(
        $label,
        $url = '',
        $title = '',
        $prepare = false,
        $urlParams = [],
        $position = null,
        $liParams = null,
        $aParams = null,
        $beforeText = '',
        $afterText = ''
    ) {
        if (is_null($label) || $label === false) {
            return $this;
        }
        $link = new Varien_Object([
            'label'         => $label,
            'url'           => ($prepare ? $this->getUrl($url, (is_array($urlParams) ? $urlParams : [])) : $url),
            'title'         => $title,
            'li_params'     => $this->_prepareParams($liParams),
            'a_params'      => $this->_prepareParams($aParams),
            'before_text'   => $beforeText,
            'after_text'    => $afterText,
        ]);

        $this->_addIntoPosition($link, $position);

        return $this;
    }

    /**
     * Add link into collection
     *
     * @param Varien_Object $link
     * @param int $position
     * @return $this
     */
    protected function _addIntoPosition($link, $position)
    {
        $this->_links[$this->_getNewPosition($position)] = $link;

        if ((int) $position > 0) {
            ksort($this->_links);
        }

        return $this;
    }

    /**
     * Add block to link list
     *
     * @param string $blockName
     * @return $this
     */
    public function addLinkBlock($blockName)
    {
        $block = $this->getLayout()->getBlock($blockName);
        if ($block) {
            $position = (int) $block->getPosition();
            $this->_addIntoPosition($block, $position);
        }
        return $this;
    }

    /**
     * Remove Link block by blockName
     *
     * @param string $blockName
     * @return $this
     */
    public function removeLinkBlock($blockName)
    {
        foreach ($this->_links as $key => $link) {
            if ($link instanceof Mage_Core_Block_Abstract && $link->getNameInLayout() == $blockName) {
                unset($this->_links[$key]);
            }
        }
        return $this;
    }

    /**
     * Removes link by url
     *
     * @param string $url
     * @return $this
     */
    public function removeLinkByUrl($url)
    {
        foreach ($this->_links as $k => $v) {
            if ($v->getUrl() == $url) {
                unset($this->_links[$k]);
            }
        }

        return $this;
    }

    /**
     * Get cache key informative items
     * Provide string array key to share specific info item with FPC placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if (is_null($this->_cacheKeyInfo)) {
            $links = [];
            if (!empty($this->_links)) {
                foreach ($this->_links as $position => $link) {
                    if ($link instanceof Varien_Object) {
                        $links[$position] = $link->getData();
                    }
                }
            }
            $this->_cacheKeyInfo = parent::getCacheKeyInfo() + [
                'links' => base64_encode(serialize($links)),
                'name' => $this->getNameInLayout(),
            ];
        }

        return $this->_cacheKeyInfo;
    }

    /**
     * Prepare tag attributes
     *
     * @param string|array $params
     * @return string
     */
    protected function _prepareParams($params)
    {
        if (is_string($params)) {
            return $params;
        } elseif (is_array($params)) {
            $result = '';
            foreach ($params as $key => $value) {
                $result .= ' ' . $key . '="' . addslashes($value) . '"';
            }
            return $result;
        }
        return '';
    }

    /**
     * Set first/last
     *
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        if (!empty($this->_links)) {
            $this->_links[array_key_first($this->_links)]->setIsFirst(true);
            $this->_links[array_key_last($this->_links)]->setIsLast(true);
        }
        return parent::_beforeToHtml();
    }

    /**
     * Return new link position in list
     *
     * @param int $position
     * @return int
     */
    protected function _getNewPosition($position = 0)
    {
        if ((int) $position > 0) {
            while (isset($this->_links[$position])) {
                $position++;
            }
        } else {
            $position = 0;
            foreach (array_keys($this->_links) as $k) {
                $position = $k;
            }
            $position += 10;
        }
        return $position;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->addModelTags(Mage::getSingleton('customer/session')->getCustomer());
        }

        return parent::getCacheTags();
    }
}
