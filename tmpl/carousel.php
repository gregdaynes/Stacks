<?php
/**
 * @version	$Id: carousel.php 777 2011-05-16 22:37:16Z media $
 * @package	jEvolve.SlideStack
 * @copyright  Copyright (C) 2010 jEvolve, LLC. All rights reserved.
 * @license	GNU General Public License
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = &JFactory::getDocument();
$evl = new evolveLibrary;

$moduleId = ltrim($params->get('modulename'));
$autoplay = $params->get('autoscroll', 0);
if ($autoplay == 0) {
	$autoplay = 'false';
} else {
	$autoplay = 'true';
}

$arrows			= $params->get('arrows');
$tabs			= $params->get('tabs');
$displaytime	= $params->get('displaytime');
$animated		= $params->get('animated');
$transtime		= $params->get('transitiontime');
$transition		= $params->get('transitiontype');
$linktext		= $params->get('linktext', false);
$linktitle		= $params->get('linktitle', true);
$showtitle		= $params->get('showtitle', true);
$showtext		= $params->get('showtext', true);
$readmore		= $params->get('readmore', true);
$readmoretext	= $params->get('readmoretext', true);
$height			= $params->get('slideheight', 100);

if ($params->get('moduleId')) {
	$randVar = ltrim($params->get('moduleId'));
} else {
	$randVar = 'id'.$evl->generateUniqueCode("5");
}

if (!$params->get('customcss'))
{
	// tabs should clear with titles
	$tabStyle = null;
	if ($tabs == 1) {
		$tabStyle = 'clear: both;';
	}
	$css = '
	#'.$moduleId.' ul, #'.$moduleId.' li { list-style: none !important; padding: 0; margin: 0;}
	#'.$moduleId.' .mask { height: '.(int) $height.'px; width: 100%; overflow: hidden; }
	#'.$moduleId.' .mask li { width: 100%; background-color: white; }
	#'.$moduleId.' .buttons li { float: left; '.$tabStyle.' }
	#'.$moduleId.' .img, #'.$moduleId.' .wrapper { float:right; }
	#'.$moduleId.' .active { font-weight: bold; }
	';
	$document->addStyleDeclaration( $css );
}


// include javascript
//if ($animated == 1 && $evl->getMootoolsVersion() == '1.2' ) {
	JHTML::_('behavior.mootools');
	JHTML::script('fx.elements.js', 'media/evolve_library/');
	JHTML::script('loop.js', 'media/evolve_library/');
	JHTML::script('slideshow.js', 'media/evolve_library/');
	
	$prevnext = '';
	if ($arrows == 1 && count($items) > 1) {
		$prevnext = '
		// Prev / Next Buttons
		'.$moduleId.'Controls.getElement(\'.prev\').setStyle(\'display\', \'\').addEvent(\'click\', function() { '.$randVar.'.showPrevious(); });
		'.$moduleId.'Controls.getElement(\'.next\').setStyle(\'display\', \'\').addEvent(\'click\', function() { '.$randVar.'.showNext(); });
		';
	}
	
	$buttons = '';
	if ($tabs >= 1) {
		$buttons = '
		// Buttons
		'.$moduleId.'Buttons.each(function(element, index){
		    element.addEvents({
		        click: function(){
		            '.$randVar.'.show(index);
		        }
		    });
			element.getElement(\'a\').removeProperty(\'href\');
		});
		
		// Active Button
		'.$randVar.'.addEvent(\'show\', function(slideData) {
			'.$moduleId.'Buttons[slideData.previous.index].removeClass(\'active\');
			'.$moduleId.'Buttons[slideData.next.index].addClass(\'active\');
		});
		';
	}
	
	$script = 'window.addEvent(\'load\', function(){
		// Elements / vars
		var '.$moduleId.'Container = $(\''.$moduleId.'\');
		var '.$moduleId.'Controls = '.$moduleId.'Container.getElement(\'.buttons\');
		var '.$moduleId.'Buttons = '.$moduleId.'Controls.getElements(\'li.index li\');
		var manualPaused = false;
		
		// Instance
		var '.$randVar.' = new SlideShow(
			'.$moduleId.'Container.getElement(\'.mask ul\'), {
			delay: '.($displaytime * 1000).',
			autoplay: '.$autoplay.',
			transition: \''.$transition.'\',
			duration: '.($transtime * 1000).'
		});
		
		// hover menu fix
		'.$randVar.'.addEvent(\'showComplete\', function(slideData) {
			slideData.next.element.setStyle(\'z-index\', 0);
		});
		
		'.$buttons.$prevnext.'
				
		'.$moduleId.'Container.addEvent(\'mouseenter\', function() { '.$randVar.'.pause(); });
		'.$moduleId.'Container.addEvent(\'mouseleave\', function() { if (!manualPaused) '.$randVar.'.play(); });
	});
		';
	$document->addScriptDeclaration( $script );
//} else {
//	$log = $evl->loadHelper('log');
// $log->simpleLog('mootools 1.2 not enabled', 'mod_slidestack');
//}

if ($params->get('crop_mode') == 'js') {
	if ($params->get('resizerjs') == 1) {
		for($i=0, $n=false; $i <= count($items); $i++) {
			if ($n !== false) {
				break;
			}
			
			if (isset($items[$i]->image['class'])) {
				$n = $items[$i]->image['class'];
			}
		}
		
		$pWidth = $params->get('imagewidth', 100);
		$pHeight = $params->get('imageheight', 100);
		
		// resizer css to add
		$css = '
			.'.$n.' { 
				width: '.$pWidth.'px;
				height: '.$pHeight.'px;
			}
		';
		$document->addStyleDeclaration($css);
		
		// resizer js to add
		$js = "window.addEvent('load', function() {
				var images = $(document).getElements('[class~=".$n."]');
				
					images.each(function(el, i) {
						wrapper = new Element('span', {
							'class': 'wrapper',
							'styles': {
								'display': 'block',
								'width': ".$pWidth.",
								'height': ".$pHeight.",
								'overflow': 'hidden',
								'position': 'relative',
							}
						});
						
						el.removeClass('".$n."');
						
						wrapper.wraps(el);
						
						size = el.getSize();
						
						if (size.x > size.y) { // wide
							el.setStyle('height', ".$pHeight.");	
						} else { // tall
							el.setStyle('width', ".$pWidth.");
						}
						
						size = el.getSize();
						
						el.setStyles({
							'margin-left': -(size.x / 2),
							'position': 'absolute',
							'left': '50%'
						});
					});
				});";
		$document->addScriptDeclaration($js);
		
	}
}

?>

<div id="<?php echo $moduleId; ?>">
	<div class="mask">
		<ul id="b<?php echo $moduleId; ?>">		
			<?php foreach($items as $i=>$item) {
				// image
				if (isset($item->image)) {
					if (isset($item->image['class'])) {
						$class = 'img '.$item->image['class'];
					} else {
						$class = 'img';
					}
					$item->image = '<img src="'.$item->image['path'].'" class="'.$class.'" style="clear: right; float: right;" />';
				} else {
					$item->image = null;
				}
				
				// title
				if ($showtitle) { 
					// link title
					if ($linktitle) {
						$item->title_html = '<a class="titleLink" href="'.$item->link.'">'.$item->title.'</a>';
					}
					
					$item->title_html = '<h3>'.$item->title_html.'</h3>';
				} else {
					$item->title_html = null;
				}
				
				// text
				if ($showtext) { 
					// link title
					if ($linktext) {
						$item->introtext_html = '<a class="bodyLink" href="'.$item->link.'">'.$item->introtext.'</a>';
					}
					
					$item->introtext_html = '<span class="text">'.$item->introtext_html.'</span>';
				} else {
					$item->introtext_html = null;
				}
				
				if ($readmore) {
					$item->readmore = '<a href="'.$item->link.'" class="readmore">'.$readmoretext.'</a>';
				} else {
					$item->readmore = null;
				}
			?>
				<li class="item" id="<?php echo $moduleId.'-'.$item->alias; ?>">
					<?php echo $item->image; ?>
					<?php echo $item->title_html; ?>
					<?php echo $item->introtext_html; ?>
					<?php echo $item->readmore; ?>
				</li>
				<?php
			}
			?>
		</ul>
	</div>

	<?php 
	if ($tabs >= 1) {
		$tabNum = 1;
		?>
		<ul class="buttons">
			<?php if ($arrows == 1 && count($items) > 1) { ?>
				<li class="prev" style="display: none;">&lt;</li>
			<?php
			}
			?>
			<li class="index">
				<ul>
				<?php
				foreach ($items as $i=>$item)
				{
					($i == 0) ? $active = ' class="active"' : $active = null;
					
					if ($tabs == 1) { ?><li<?php echo $active; ?>><a href="#<?php echo $moduleId.'-'.$item->alias .'">'.$item->title; ?></a></li><?php }
					else if ($tabs == 2) { ?><li<?php echo $active; ?>><a href="#<?php echo $moduleId.'-'.$item->alias .'">'.$tabNum; ?></a></li><?php }
					else if ($tabs == 3) { ?><li<?php echo $active; ?>><a href="#<?php echo $moduleId.'-'.$item->alias; ?>">&bull;</a></li><?php }
					$tabNum ++;
				}
				?>
				</ul>
			</li>
			<?php if ($arrows == 1 && count($items) > 1) { ?>
				<li class="next" style="display: none;">&gt;</li>
			<?php } ?>
		</ul>
	<?php } ?>
	<div class="clear"></div>
</div>