<?php // no direct access
defined('_JEXEC') or die('Restricted access');

// required files
require_once('modules/mod_stacks/helpers/stack_helper.php');
switch($params->get('content_provider', 'joomla')) {
	case 'k2':
		require_once('modules/mod_stacks/helpers/content_k2.php');
		$content_helper = new K2Helper;
		break;
	
	case 'joomla':
	default:
		require_once('modules/mod_stacks/helpers/content_joomla.php');
		$content_helper = new JoomlaHelper;
		break;
}

// add some fun to the params
$params->set('module_name', $params->get('module_name', $content_helper->generateUniqueCode("10")));

// pass params to helpers
$content_helper->construct($params);

// fetch content to display
$items = $content_helper->getItems();

// pass to template
if ($items) {
	$items = $content_helper->processItems();

	require(JModuleHelper::getLayoutPath('mod_stacks', $params->get('template', 'stack')));
}

//exit;

// no items to display
return;