<?php
/**
 * @file
 * Example file for getting file type info from getRemoteFileInfo.
 */

include_once 'getRemoteFileInfo.php';

$url = 'http://www.nd.gov/gis/apps/NDHUB.TraumaCenters.csv';

$file_info = new dkanDataset\getRemoteFileInfo($url, 'test', TRUE, '/tmp');
var_dump($file_info->getType());
var_dump($file_info->getName());
