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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
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
    protected $_toplinks = array();

    function __construct()
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
     * @param int $position
     * @param string $beforeText
     * @param string $afterText
     * @return Mage_Page_Block_Html_Toplinks
     */
    public function addLink($liParams, $aParams, $innerText, $position='', $beforeText='', $afterText='')
    {
        $params = '';
        if (!empty($liParams) && is_array($liParams)) {
            foreach ($liParams as $key=>$value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($liParams)) {
            $params .= ' ' . $liParams;
        }
        $toplinkInfo['liParams'] = $params;
        $params = '';
        if (!empty($aParams) && is_array($aParams)) {
            foreach ($aParams as $key=>$value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($aParams)) {
            $params .= ' ' . $aParams;
        }
        $toplinkInfo['aParams'] = $params;
        $toplinkInfo['innerText'] = $innerText;
        $toplinkInfo['beforeText'] = $beforeText;
        $toplinkInfo['afterText'] = $afterText;
        $this->_prepareArray($toplinkInfo, array('liParams', 'aParams', 'innerText', 'beforeText', 'afterText', 'first', 'last'));
        if (is_numeric($position)) {
            array_splice($this->_toplinks, $position, 0, array($toplinkInfo));
        } else {
            $this->_toplinks[] = $toplinkInfo;
        }
        return $this;
    }

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
