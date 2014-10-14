<?php
/**
 * Documentation
 *
 * @author h.woltersdorf
 */

require_once __DIR__ . '/../vendor/autoload.php';

$tmd = new \hollodotme\TreeMDown\TreeMDown( __DIR__ . '/Utilities' );
$tmd->setProjectName( 'Utilities' );
$tmd->setShortDescription( '' );
$tmd->hideEmptyFolders();
$tmd->enablePrettyNames();
$tmd->hideFilenameSuffix();

$tmd->display();
