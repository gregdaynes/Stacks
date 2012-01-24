<?php // no direct access to this file
//defined('_JEXEC') or die('Restricted access');
//
//JHTML::_('behavior.mootools');
//JHTML::_('behavior.modal');
//
///**
// * Renders a multiple item select element
// *
// */
//class JElementHTMLSelect extends JElement
//{
//        /**
//        * Element name
//        *
//        * @access       protected
//        * @var          string
//        */
//        var    $_name = 'select';
// 
//        function fetchElement($name, $value, &$node, $control_name)
//        {
                // Base name of the HTML control.
//                $ctrl  = $control_name .'['. $name .']';
// 
                // Construct an array of the HTML OPTION statements.
//                $options = array ();
//                
                // Construct the various argument calls that are supported.
//                $attribs       = ' ';
//                if ($v = $node->attributes( 'size' )) {
//                        $attribs       .= 'size="'.$v.'"';
//                }
//                if ($v = $node->attributes( 'class' )) {
//                        $attribs       .= 'class="'.$v.'"';
//                } else {
//                        $attribs       .= 'class="inputbox"';
//                }
//                if ($m = $node->attributes( 'multiple' ))
//                {
//                        $attribs       .= ' multiple="multiple"';
//                        $ctrl          .= '[]';
//                }
// 
                // Render the HTML SELECT list.
                // return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
//        		return "<input type=\"text\" id=\"select\" id=\"select\" /><div class=\"button2-left\"><div class=\"blank\"><a class='modal' title='Select an Article' href='index.php?option=com_content&task=element&tmpl=component&object=id' rel='{handler: \"iframe\", size: {x: 650, y: 375}}'>Select</a></div></div>";
//        }
//}