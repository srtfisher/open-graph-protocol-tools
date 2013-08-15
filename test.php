<?php
/**
 * Test Example of the Open Graph Fetcher
 *
 * @package opengraph
 */
include 'vendor/autoload.php';

$fetch = new OpenGraph\Fetcher('http://www.cnn.com/2013/08/15/opinion/bergen-zawahiri-egypt/index.html?hpt=hp_t1');
var_dump($fetch->getKeys());