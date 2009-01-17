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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Extensions_Local_Edit_Tab_Depends
    extends Mage_Adminhtml_Block_Extensions_Local_Edit_Tab_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('extensions/local/depends.phtml');
    }

    public function getDepends()
    {
        foreach ($this->getPkg()->getDependencies() as $role=>$types) {
            if (!('required'===$role || 'optional'===$role)) {
                continue;
            }
            foreach ($types as $type=>$depends) {
                if (!('php'===$type || 'package'===$type || 'extension'===$type)) {
                    continue;
                }
                if (isset($depends[0])) {
                    foreach ($depends as $dep) {
                        $this->_addDep($deps, $role, $type, $dep);
                    }
                } else {
                    if ('php'===$type) {
                        $depends['name'] = 'PHP';
                    }
                    $this->_addDep($deps, $role, $type, $depends);
                }
            }
        }

        return $deps;
    }

    protected function _addDep(&$deps, $role, $type, $dep)
    {
        $deps[] = array(
            'type'=>$type,
            'role'=>isset($dep['conflicts']) ? 'conflicts' : $role,
            'name'=>$dep['name'],
            'channel'=>isset($dep['channel']) ? $dep['channel'] : null,
            'min'=>isset($dep['min']) ? $dep['min'] : null,
            'max'=>isset($dep['max']) ? $dep['max'] : null,
            'recommended'=>isset($dep['recommended']) ? $dep['recommended'] : null,
            'exclude'=>isset($dep['exclude']) ? print_r($dep['exclude'], 1) : null,
        );
    }

    public function getGroups()
    {
        $groups = array();
        foreach ($this->getData('pkg/dependencies/groups') as $group) {
            $groups[] = array(
                'name'=>$group['name'],
                'hint'=>$group['hint'],
            );
        }
        return $groups;
    }
}
