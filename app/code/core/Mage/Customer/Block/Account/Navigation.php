<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer account navigation sidebar
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Block_Account_Navigation extends Mage_Core_Block_Template
{
    /**
     * @var array
     */
    protected $_links = [];

    /**
     * @var false|string
     */
    protected $_activeLink = false;

    /**
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return $this
     */
    public function addLink($name, $path, $label, $urlParams = [])
    {
        $this->_links[$name] = new Varien_Object([
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
        ]);
        return $this;
    }

    /**
     * Remove a link
     *
     * @param string $name Name of the link
     * @return $this
     */
    public function removeLink($name)
    {
        if (isset($this->_links[$name])) {
            unset($this->_links[$name]);
        }

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * @param Varien_Object $link
     * @return bool
     */
    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->getAction()->getFullActionName('/');
        }

        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (count(explode('/', $path))) {
            case 1:
                $path .= '/index';
                // no break

            case 2:
                $path .= '/index';
        }

        return $path;
    }
}
