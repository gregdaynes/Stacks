<?php
/**
 * @version   $Id: mod_slidestack.php 561 2011-01-19 23:39:03Z jevolve $
 * @package   jEvolve.SlideStack
 * @copyright Copyright (C) 2010 jEvolve, LLC. All rights reserved.
 * @license   GNU General Public License
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

// check evolve_library is installed
if (!file_exists('plugins/system/evolvelibrary.php')) {
	// can't do much without it
	jimport('joomla.error.log');
	
	$errorLog = 'slidestack-error_log-'.date('Y-m-d').'.log.php';
	
    $options = array(
        'format' => "{DATE}\t{TIME}\t{COMMENT}"
    );
    // Create the instance of the log file in case we use it later
    $log = &JLog::getInstance($errorLog, $options);
    $log->addEntry(array('comment' => 'evolve library not loading'));
	
	return;
}

// load evolve_library
require_once('plugins/system/evolvelibrary.php');

// new class
$evl = new evolveLibrary;

// check evolve_library is enabled
if (!$evl->enabled()) {
	// can't do much without it
	JError::raiseWarning(500, JText::_('JEVOLVE_LIBRARY_DISABLED'));
	return;
}

// load slide logic
require_once('modules/mod_slidestack/helpers/helper.php');
$ss = new SlideStackHelper;

$params->set('modulename', ltrim($params->get('modulename', $evl->generateUniqueCode("10"))));

// fetch items to display
if ($items = $ss->getItems($params)) {

	// pass to template
	require(JModuleHelper::getLayoutPath('mod_slidestack', $params->get('layout', 'stack')));
}

// no items to display
return;