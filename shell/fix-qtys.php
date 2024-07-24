<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Shell
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

// TEST IT BEFORE RUN IN YOUR PRODUCTION ENVIRONMENT!
// See https://github.com/OpenMage/magento-lts/pull/2395
chdir(dirname(__DIR__, 1));
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (PHP_SAPI != 'cli') {
    exit(0);
}
if (is_file('app/bootstrap.php')) {
    require_once('app/bootstrap.php');
}

require_once('app/Mage.php');
Mage::app('admin')->setUseSessionInUrl(false);
Mage::app()->addEventArea('crontab');
Mage::setIsDeveloperMode(true);

echo 'start: ', date('c'), "\n";


// nice -n 15 su - www-data -s /bin/bash -c 'php /var/www/.../fix-qtys.php'
$runNumb = (int) end($argv);

ini_set('memory_limit', '1G'); // memory limit
exec('nproc', $core);
$core  = max(1, (int) trim(implode($core))); // number of cpu core
$limit = 1000; // number of orders per thread
$stop  = Mage::getBaseDir('var').'/fixqtys.stop';


if (is_file($stop)) {
    echo 'Stop file is here: ',$stop,' - Exiting now.',"\n";
    exit(-1);
}

if (empty($runNumb)) {
    echo 'Starting process in 25 seconds...',"\n";
    echo 'You can stop it at any moment by creating the stop file: '.$stop,"\n";
    file_put_contents($stop, 'checking');
    sleep(25);
    unlink($stop);
    echo 'Go!',"\n";

    $core = max(1, $core - 5);
    $pids = [];
    $cmds = [];

    $runNumb = ceil(Mage::getResourceModel('sales/order_collection')->getSize() / $limit);
    while ($runNumb > 0) {
        $cmds[] = PHP_BINARY.' '.getcwd().'/shell/'.basename($argv[0]).' '.$runNumb;
        $runNumb--;
    }

    while (!empty($cmds)) {
        if (is_file($stop)) {
            break;
        }

        $pids[] = exec($cmd = array_shift($cmds).' >/dev/null 2>&1 & echo $!');
        echo $cmd,"\n";
        while (count($pids) >= $core) {
            sleep(10);
            foreach ($pids as $key => $pid) {
                if (!file_exists('/proc/'.$pid)) {
                    unset($pids[$key]);
                } else {
                    clearstatcache('/proc/'.$pid);
                }
            }
        }
    }

    while (count($pids) > 0) {
        sleep(10);
        foreach ($pids as $key => $pid) {
            if (!file_exists('/proc/'.$pid)) {
                unset($pids[$key]);
            } else {
                clearstatcache('/proc/'.$pid);
            }
        }
    }
} else {
    $cron = Mage::getModel('cron/schedule');
    $cron->setData('job_code', 'manual_fixqtys_'.$runNumb);
    $cron->setData('created_at', date('Y-m-d H:i:s'));
    $cron->setData('scheduled_at', date('Y-m-d H:i:s'));
    $cron->setData('executed_at', date('Y-m-d H:i:s'));
    $cron->setData('status', 'running');
    $cron->save();

    $count   = 0;
    $results = ['success' => [], 'error' => []];
    $orders  = Mage::getResourceModel('sales/order_collection')
        ->setOrder('entity_id', 'asc')
        ->setPageSize($limit)
        ->setCurPage($runNumb);

    foreach ($orders as $order) {
        if (is_file($stop)) {
            $results['error'][] = ($runNumb > 0) ? 'Interrupted by '.$stop.' file (thread#'.$runNumb.').' : 'Interrupted by '.$stop.' file.';
            break;
        }

        try {
            $changes   = false;
            $invoices  = $order->getInvoiceCollection();
            $refunds   = $order->getCreditmemosCollection();
            $shipments = $order->getShipmentsCollection();

            // items of order
            foreach ($order->getAllItems() as $item) {
                $parentItem = $item->getParentItem();
                if (is_object($parentItem)) {
                    if ($parentItem->getData('qty_invoiced') == $parentItem->getData('qty_ordered')) {
                        // parent item full invoiced
                        $item->setData('qty_invoiced', $item->getData('qty_ordered'));
                    } elseif ($parentItem->getData('qty_invoiced') > 0) {
                        $productOptions = $item->getProductOptions();
                        if (isset($productOptions['bundle_selection_attributes'])) {
                            $bundleSelectionAttrs = unserialize($productOptions['bundle_selection_attributes'], ['allowed_classes' => false]);
                            if ($bundleSelectionAttrs) {
                                $item->setData('qty_invoiced', $parentItem->getData('qty_invoiced') * $bundleSelectionAttrs['qty']);
                            }
                        } elseif ($parentItem->getData('product_type') == 'configurable') {
                            $item->setData('qty_invoiced', $parentItem->getData('qty_invoiced'));
                        }
                    }

                    if ($parentItem->getData('qty_refunded') == $parentItem->getData('qty_ordered')) {
                        // parent item full refunded
                        $item->setData('qty_refunded', $item->getData('qty_ordered'));
                    } elseif ($parentItem->getData('qty_refunded') > 0) {
                        $productOptions = $item->getProductOptions();
                        if (isset($productOptions['bundle_selection_attributes'])) {
                            $bundleSelectionAttrs = unserialize($productOptions['bundle_selection_attributes'], ['allowed_classes' => false]);
                            if ($bundleSelectionAttrs) {
                                $item->setData('qty_refunded', $parentItem->getData('qty_refunded') * $bundleSelectionAttrs['qty']);
                            }
                        } elseif ($parentItem->getData('product_type') == 'configurable') {
                            $item->setData('qty_refunded', $parentItem->getData('qty_refunded'));
                        }
                    }

                    if ($parentItem->getData('qty_shipped') == $parentItem->getData('qty_ordered')) {
                        // parent item full shipped
                        if (in_array($item->getData('product_type'), ['downloadable', 'virtual'])) {
                            $item->setData('qty_shipped', 0);
                        } else {
                            $item->setData('qty_shipped', $item->getData('qty_ordered'));
                        }
                    } elseif ($parentItem->getData('qty_shipped') > 0) {
                        $productOptions = $item->getProductOptions();
                        if (in_array($item->getData('product_type'), ['downloadable', 'virtual'])) {
                            $item->setData('qty_shipped', 0);
                        } elseif (isset($productOptions['bundle_selection_attributes'])) {
                            $bundleSelectionAttrs = unserialize($productOptions['bundle_selection_attributes'], ['allowed_classes' => false]);
                            if ($bundleSelectionAttrs) {
                                $item->setData('qty_shipped', $parentItem->getData('qty_shipped') * $bundleSelectionAttrs['qty']);
                            }
                        } elseif ($parentItem->getData('product_type') == 'configurable') {
                            $item->setData('qty_shipped', $parentItem->getData('qty_shipped'));
                        }
                    }

                    if ($item->hasDataChanges()) {
                        $changes = true;
                        $item->save();
                    }
                }
            }

            // items of invoices
            $nb = count($invoices);
            foreach ($invoices as $invoice) {
                foreach ($invoice->getAllItems() as $item) {
                    $orderItem = $item->getOrderItem();
                    if ($nb == 1) {
                        $item->setData('qty', $orderItem->getData('qty_invoiced'));
                    }
                    if ($item->hasDataChanges()) {
                        $changes = true;
                        $item->save();
                    }
                }
            }

            // items of creditmemos
            $nb = count($refunds);
            foreach ($refunds as $refund) {
                foreach ($refund->getAllItems() as $item) {
                    $orderItem = $item->getOrderItem();
                    if ($nb == 1) {
                        $item->setData('qty', $orderItem->getData('qty_refunded'));
                    }
                    if ($item->hasDataChanges()) {
                        $changes = true;
                        $item->save();
                    }
                }
            }

            // items of shipments
            $nb = count($shipments);
            foreach ($shipments as $shipment) {
                foreach ($shipment->getAllItems() as $item) {
                    $orderItem  = $item->getOrderItem();
                    $parentItem = $orderItem->getParentItem();

                    if ($nb == 1) {
                        $item->setData('qty', $orderItem->getData('qty_shipped'));
                    }

                    if (is_object($parentItem) && ($parentItem->getData('product_type') == 'configurable')) {
                        $changes = true;
                        $item->delete();
                    } elseif ($item->hasDataChanges()) {
                        $changes = true;
                        $item->save();
                    }
                }
            }

            if ($changes) {
                $results['success'][] = $order->getId();
            }
        } catch (Throwable $t) {
            $results['error'][] = $order->getId().'#'.$t->getMessage();
        }

        if ((++$count % 200) == 0) {
            saveCron($cron, $results, false);
        }
    }

    saveCron($cron, $results, true);
}

function saveCron(object $cron, array $results, bool $end)
{
    if (!empty($results['success']) || !empty($results['error'])) {
        $textok = trim(str_replace(['    ', ' => Array', "\n\n"], [' ', '', "\n"], preg_replace('#\s+[()]#', '', print_r($results['success'], true))));
        $textko = trim(str_replace(['    ', ' => Array', "\n\n"], [' ', '', "\n"], preg_replace('#\s+[()]#', '', print_r($results['error'], true))));

        if ($end) {
            $cron->setData('finished_at', date('Y-m-d H:i:s'));
            $cron->setData('status', empty($results['error']) ? 'success' : 'error');
        }

        $cron->setData('messages', 'memory: '.((int) (memory_get_peak_usage(true) / 1024 / 1024)).'M (max: '.ini_get('memory_limit').')'."\n".'success: '.$textok."\n".'error: '.$textko);
        $cron->save();
    } elseif ($end) {
        // should never happen
        $cron->setData('finished_at', date('Y-m-d H:i:s'));
        $cron->setData('status', 'error');
        $cron->setData('messages', 'nothing to do');
        $cron->save();
    }
}


exit(0);
