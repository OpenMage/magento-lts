<?php

class Mage_Adminhtml_Model_Extension_Local_Collection extends Mage_Adminhtml_Model_Extension_Collection_Abstract
{
    protected function _fetchPackages()
    {
        // fetch installed packages
        $pear = Varien_Pear::getInstance();
        $pear->run('list', array('allchannels'=>1));
        $output = $pear->getOutput();

        // load available packages into array
        $packages = array();
        foreach ($output as $i=>$channelRoot) {
            $channel = $channelRoot['output'];
            if (!isset($channel['headline'])) {
                continue;
            }
            foreach ($channel['data'] as $j=>$pkg) {
                $packages[] = array(
                    'id'=>$channel['channel'].'|'.$pkg[0],
                    'channel'=>$channel['channel'],
                    'name'=>$pkg[0],
                    'version'=>$pkg[1],
                    'stability'=>$pkg[2],
                );
            }
        }

        return $packages;
    }
}