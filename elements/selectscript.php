<?php // no direct access to this file
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');

/**
 * Renders a multiple item select element
 *
 */
class JElementSelectScript extends JElement
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
                
                
 
                // Render the HTML SELECT list.
                // return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
        		return "<script>function jSelectArticle(id, title, object) {
        					console.log(id);
        					console.log(title);
        					console.log(object);
        					
        					var string = id + ' : ' + title;
        					
        					document.getElementById(\"contentId\").value = string;
        					document.getElementById('sbox-window').close();
        				}</script>";
        }
}