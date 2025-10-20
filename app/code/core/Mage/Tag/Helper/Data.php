<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * @package    Mage_Tag
 */
class Mage_Tag_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Tag';

    /**
     * @return array
     */
    public function getStatusesArray()
    {
        return [
            Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('tag')->__('Disabled'),
            Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('tag')->__('Pending'),
            Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('tag')->__('Approved'),
        ];
    }

    /**
     * @return array
     */
    public function getStatusesOptionsArray()
    {
        return [
            [
                'label' => Mage::helper('tag')->__('Disabled'),
                'value' => Mage_Tag_Model_Tag::STATUS_DISABLED,
            ],
            [
                'label' => Mage::helper('tag')->__('Pending'),
                'value' => Mage_Tag_Model_Tag::STATUS_PENDING,
            ],
            [
                'label' => Mage::helper('tag')->__('Approved'),
                'value' => Mage_Tag_Model_Tag::STATUS_APPROVED,
            ],
        ];
    }

    /**
     * Check tags on the correctness of symbols and split string to array of tags
     *
     * @param string $tagNamesInString
     * @return array
     */
    public function extractTags($tagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
    }

    /**
     * Clear tag from the separating characters
     *
     * @return array
     */
    public function cleanTags(array $tagNamesArr)
    {
        foreach (array_keys($tagNamesArr) as $key) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if ($tagNamesArr[$key] == '') {
                unset($tagNamesArr[$key]);
            }
        }

        return $tagNamesArr;
    }
}
