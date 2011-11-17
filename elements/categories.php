<?php
/**
 * @version	$Id: categories.php 325 2010-11-23 05:37:22Z jevolve $
 * @package	jEvolve.SlideStack
 * @copyright  Copyright (C) 2010 jEvolve, LLC. All rights reserved.
 * @license	GNU General Public License
 */

// no direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Renders a multiple item select element
 *
 */
class JElementCategories extends JElement
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        var    $_name = 'Categories';
 
        function fetchElement($name, $value, &$node, $control_name)
        {
                // Base name of the HTML control.
                $ctrl  = $control_name .'['. $name .']';
 
                // Construct an array of the HTML OPTION statements.
                $options = array ();
                
                $db = & JFactory::getDBO();
                $query = 'SELECT cat.id AS id, cat.title AS title, sec.title AS section FROM #__categories AS cat JOIN #__sections AS sec ON sec.id = cat.section ORDER BY sec.title, cat.title ASC';
                $db->setQuery($query);
                $categories = $db->loadObjectList();
                                
                foreach ($categories as $option)
                {
                        $val = $option->id;
                        $text = $option->section .' / '. $option->title;
                        $options[] = JHTML::_('select.option', $val, JText::_($text));
                }
 
                // Construct the various argument calls that are supported.
                $attribs       = ' ';
                if ($v = $node->attributes( 'size' )) {
                        $attribs       .= 'size="'.$v.'"';
                }
                if ($v = $node->attributes( 'class' )) {
                        $attribs       .= 'class="'.$v.'"';
                } else {
                        $attribs       .= 'class="inputbox"';
                }
                if ($m = $node->attributes( 'multiple' ))
                {
                        $attribs       .= ' multiple="multiple"';
                        $ctrl          .= '[]';
                }
 
                // Render the HTML SELECT list.
                return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
        }
}