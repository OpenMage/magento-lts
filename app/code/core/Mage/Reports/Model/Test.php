<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Model  for flex reports
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Test extends Varien_Object
{
    /**
     * @return false|string
     */
    public function getUsersCountries()
    {
        return file_get_contents(Mage::getModuleDir('etc', 'Mage_Reports') . DS . 'flexTestDataCountries.xml');
    }

    /**
     * @param int $countryId
     * @return string
     */
    public function getUsersCities($countryId)
    {
        $dom = new DOMDocument();
        $dom -> preserveWhiteSpace = false;
        $dom -> load(Mage::getModuleDir('etc', 'Mage_Reports') . DS . 'flexTestDataCities.xml');

        $root = $dom -> documentElement;
        $rows = $root -> getElementsByTagName('row');

        $childsToRemove = [];
        for ($i = 0; $i < $rows -> length; $i++) {
            for ($j = 0; $j < $rows -> item($i) -> childNodes -> length; $j++) {
                if ($rows -> item($i) -> childNodes -> item($j) -> nodeType == XML_ELEMENT_NODE
                        &&
                    $rows -> item($i) -> childNodes -> item($j) -> nodeName == 'countryId'
                        &&
                    $rows -> item($i) -> childNodes -> item($j) -> nodeValue != $countryId
                ) {
                    $childsToRemove[] = $rows -> item($i);
                }
            }
        }

        foreach ($childsToRemove as $child) {
            $root -> removeChild($child);
        }

        return $dom -> saveXML();
    }

    /**
     * @return false|string
     */
    public function getTimelineData()
    {
        return file_get_contents(Mage::getModuleDir('etc', 'Mage_Reports') . DS . 'flexTestDataTimeline.xml');
    }

    /**
     * @return string
     */
    public function getAllLinearExample()
    {
        $session = Mage::getModel('review/session');

        $startPoint = time() - 24 * 60 * 60;

        $allData = [];
        $countOfStartData = 12;
        for ($i = 1; $i <= $countOfStartData; $i++) {
            $allData[] = ['time' => date('Y-m-d H:i', $startPoint), 'value' => random_int(1, 100)];
            $startPoint += 30 * 60;
        }

        $allData[] = ['time' => date('Y-m-d H:i', $startPoint + (90 * 60))];

        $session -> setData('startPoint', $startPoint);

        return $this -> returnAsDataSource($allData);
    }

    /**
     * @return string
     */
    public function getNewLinearData()
    {
        $session = Mage::getModel('review/session');

        $startPoint = $session->getData('startPoint');

        $reset = 12;

        $newData  = [
            ['time' => date('Y-m-d H:i', $startPoint), 'value' => random_int(1, 100)],
        ];

        $startPoint += 30 * 60;
        $newData[]  = ['time' => date('Y-m-d H:i', $startPoint + (90 * 60))];

        $session->setData('startPoint', $startPoint);

        return $this->returnAsDataSource($newData, $reset);
    }

    /**
     * @param array $array
     * @param int $reset
     * @return string
     */
    private function returnAsDataSource(&$array, $reset = 0)
    {
        $dom = new DOMDocument();
        $dom -> preserveWhiteSpace = false;
        $dom -> loadXML('<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n<dataSource></dataSource>");
        $root = $dom ->documentElement;
        if ($reset) {
            $resetItem = $dom -> createElement('reset');
            $resetItem -> nodeValue = $reset;
            $root->appendChild($resetItem);
        }
        foreach ($array as $item) {
            $row = $dom->createElement('row');
            foreach ($item as $key => $val) {
                $valItem = $dom->createElement($key);
                $valItem->nodeValue = $val;
                $row->appendChild($valItem);
            }

            $root->appendChild($row);
        }

        return $dom->saveXML();
    }
}
