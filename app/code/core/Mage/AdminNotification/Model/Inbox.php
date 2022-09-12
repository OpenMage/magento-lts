<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdminNotification Inbox model
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_AdminNotification_Model_Resource_Inbox _getResource()
 * @method Mage_AdminNotification_Model_Resource_Inbox getResource()
 * @method Mage_AdminNotification_Model_Resource_Inbox_Collection getCollection()
 * @method string getDateAdded()
 * @method $this setDateAdded(string $value)
 * @method string getDescription()
 * @method $this setDescription(string $value)
 * @method int getIsRead()
 * @method $this setIsRead(int $value)
 * @method int getIsRemove()
 * @method $this setIsRemove(int $value)
 * @method int getSeverity()
 * @method $this setSeverity(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getUrl()
 * @method $this setUrl(string $value)
 */
class Mage_AdminNotification_Model_Inbox extends Mage_Core_Model_Abstract
{
    const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR    = 2;
    const SEVERITY_MINOR    = 3;
    const SEVERITY_NOTICE   = 4;

    protected function _construct()
    {
        $this->_init('adminnotification/inbox');
    }

    /**
     * Retrieve Severity collection array
     *
     * @param int|null $severity
     * @return array|string|null
     */
    public function getSeverities($severity = null)
    {
        $severities = [
            self::SEVERITY_CRITICAL => Mage::helper('adminnotification')->__('critical'),
            self::SEVERITY_MAJOR    => Mage::helper('adminnotification')->__('major'),
            self::SEVERITY_MINOR    => Mage::helper('adminnotification')->__('minor'),
            self::SEVERITY_NOTICE   => Mage::helper('adminnotification')->__('notice'),
        ];

        if (!is_null($severity)) {
            if (isset($severities[$severity])) {
                return $severities[$severity];
            }
            return null;
        }

        return $severities;
    }

    /**
     * Retrieve Latest Notice
     *
     * @return $this
     */
    public function loadLatestNotice()
    {
        $this->setData([]);
        $this->getResource()->loadLatestNotice($this);
        return $this;
    }

    /**
     * Retrieve notice statuses
     *
     * @return array
     */
    public function getNoticeStatus()
    {
        return $this->getResource()->getNoticeStatus($this);
    }

    /**
     * Parse and save new data
     *
     * @param array $data
     * @return $this
     */
    public function parse(array $data)
    {
        return $this->getResource()->parse($this, $data);
    }

    /**
     * Add new message
     *
     * @param int $severity
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function add($severity, $title, $description, $url = '', $isInternal = true)
    {
        if (!$this->getSeverities($severity)) {
            Mage::throwException(Mage::helper('adminnotification')->__('Wrong message type'));
        }
        if (is_array($description)) {
            $description = '<ul><li>' . implode('</li><li>', $description) . '</li></ul>';
        }
        $date = date('Y-m-d H:i:s');
        $this->parse([[
            'severity'    => $severity,
            'date_added'  => $date,
            'title'       => $title,
            'description' => $description,
            'url'         => $url,
            'internal'    => $isInternal
        ]]);
        return $this;
    }

    /**
     * Add critical severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addCritical($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_CRITICAL, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add major severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addMajor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MAJOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add minor severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addMinor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MINOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add notice
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addNotice($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_NOTICE, $title, $description, $url, $isInternal);
        return $this;
    }
}
