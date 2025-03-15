<?php

/**
 * Html page block
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */
class Mage_Page_Block_Html_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Array of breadcrumbs
     *
     * array(
     *  [$index] => array(
     *                  ['label']
     *                  ['title']
     *                  ['link']
     *                  ['first']
     *                  ['last']
     *              )
     * )
     *
     * @var array
     */
    protected $_crumbs = null;

    /**
     * Cache key info
     *
     * @var null|array
     */
    protected $_cacheKeyInfo = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/html/breadcrumbs.phtml');
    }

    /**
     * @param string $crumbName
     * @param array $crumbInfo
     * @param string|bool $after
     * @return $this
     */
    public function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        $this->_prepareArray($crumbInfo, ['label', 'title', 'link', 'first', 'last', 'readonly']);
        if ((!isset($this->_crumbs[$crumbName])) || (!$this->_crumbs[$crumbName]['readonly'])) {
            if ($after && isset($this->_crumbs[$after])) {
                $offset = array_search($after, array_keys($this->_crumbs), true) + 1;
                $this->_crumbs = array_slice($this->_crumbs, 0, $offset, true) + [$crumbName => $crumbInfo] + array_slice($this->_crumbs, $offset, null, true);
            } else {
                $this->_crumbs[$crumbName] = $crumbInfo;
            }
        }
        return $this;
    }

    /**
     * @param string $crumbName
     * @param array $crumbInfo
     * @param bool $before
     */
    public function addCrumbBefore($crumbName, $crumbInfo, $before = false)
    {
        if ($before && isset($this->_crumbs[$before])) {
            $keys   = array_keys($this->_crumbs);
            $offset = array_search($before, $keys, true);
            # add before first
            if (!$offset) {
                $this->_prepareArray($crumbInfo, ['label', 'title', 'link', 'first', 'last', 'readonly']);
                $this->_crumbs = [$crumbName => $crumbInfo] + $this->_crumbs;
            } else {
                $this->addCrumb($crumbName, $crumbInfo, $keys[$offset - 1]);
            }
        } else {
            $this->addCrumb($crumbName, $crumbInfo);
        }
    }

    /**
     * @param string $crumbName
     */
    public function removeCrumb($crumbName)
    {
        if (isset($this->_crumbs[$crumbName])) {
            unset($this->_crumbs[$crumbName]);
        }
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if ($this->_cacheKeyInfo === null) {
            $this->_cacheKeyInfo = parent::getCacheKeyInfo() + [
                'crumbs' => base64_encode(serialize($this->_crumbs)),
                'name'   => $this->getNameInLayout(),
            ];
        }

        return $this->_cacheKeyInfo;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (is_array($this->_crumbs)) {
            $this->_crumbs[array_key_first($this->_crumbs)]['first'] = true;
            $this->_crumbs[array_key_last($this->_crumbs)]['last'] = true;
        }
        $this->assign('crumbs', $this->_crumbs);
        return parent::_toHtml();
    }
}
