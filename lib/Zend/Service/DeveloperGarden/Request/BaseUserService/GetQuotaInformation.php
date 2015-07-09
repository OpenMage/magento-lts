<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Marco Kaiser
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_DeveloperGarden_Request_BaseUserService_GetQuotaInformation
{
    /**
     * string module id
     *
     * @var string
     */
    public $moduleId = null;

    /**
     * constructor give them the module id
     *
     * @param string $moduleId
     * @return Zend_Service_DeveloperGarden_Request_BaseUserService
     */
    public function __construct($moduleId = null)
    {
        $this->setModuleId($moduleId);
    }

    /**
     * sets a new moduleId
     *
     * @param integer $moduleId
     * @return Zend_Service_DeveloperGarden_Request_BaseUserService
     */
    public function setModuleId($moduleId = null)
    {
        $this->moduleId = $moduleId;
        return $this;
    }

    /**
     * returns the moduleId
     *
     * @return string
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }
}
