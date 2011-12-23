<?php // no direct access
defined('_JEXEC') or die('Restricted access');

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
require_once('modules/mod_stacks/helpers/helper.php');

$ss = new SlideStackHelper;

$params->set('modulename', ltrim($params->get('modulename', $evl->generateUniqueCode("10"))));

// fetch items to display
if ($items = $ss->getItems($params)) {

	// pass to template
	require(JModuleHelper::getLayoutPath('mod_stacks', $params->get('layout', 'stack')));
}

// no items to display
return;