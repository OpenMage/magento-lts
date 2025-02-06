<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Page
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Page
 *
 * @deprecated after 1.4.0.1
 */
class Mage_Page_Block_Html_Toplinks extends Mage_Core_Block_Template
{
    /**
     * Array of toplinks
     *
     * array(
     *  [$index] => array(
     *                  ['liParams']
     *                  ['aParams']
     *                  ['innerText']
     *                  ['beforeText']
     *                  ['afterText']
     *                  ['first']
     *                  ['last']
     *              )
     * )
     *
     * @var array
     */
    protected $_toplinks = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/html/top.links.phtml');
    }

    /**
     * Add link
     *
     * @param string|array $liParams
     * @param string|array $aParams
     * @param string $innerText
     * @param string|int $position
     * @param string $beforeText
     * @param string $afterText
     * @return $this
     */
    public function addLink($liParams, $aParams, $innerText, $position = '', $beforeText = '', $afterText = '')
    {
        $params = '';
        if (!empty($liParams) && is_array($liParams)) {
            foreach ($liParams as $key => $value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($liParams)) {
            $params .= ' ' . $liParams;
        }
        $toplinkInfo['liParams'] = $params;
        $params = '';
        if (!empty($aParams) && is_array($aParams)) {
            foreach ($aParams as $key => $value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($aParams)) {
            $params .= ' ' . $aParams;
        }
        $toplinkInfo['aParams'] = $params;
        $toplinkInfo['innerText'] = $innerText;
        $toplinkInfo['beforeText'] = $beforeText;
        $toplinkInfo['afterText'] = $afterText;
        $this->_prepareArray($toplinkInfo, ['liParams', 'aParams', 'innerText', 'beforeText', 'afterText', 'first', 'last']);
        if (is_numeric($position)) {
            array_splice($this->_toplinks, $position, 0, [$toplinkInfo]);
        } else {
            $this->_toplinks[] = $toplinkInfo;
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (is_array($this->_toplinks) && $this->_toplinks) {
            reset($this->_toplinks);
            $this->_toplinks[key($this->_toplinks)]['first'] = true;
            end($this->_toplinks);
            $this->_toplinks[key($this->_toplinks)]['last'] = true;
        }
        $this->assign('toplinks', $this->_toplinks);
        return parent::_toHtml();
    }
}
