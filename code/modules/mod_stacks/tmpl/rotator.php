<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = &JFactory::getDocument();

$moduleId = ltrim($params->get('module_name'));
$autoplay = $params->get('auto_start', 1);
if ($autoplay == 0) {
	$autoplay = 'false';
} else {
	$autoplay = 'true';
}

$arrows			= $params->get('display_arrows');
$tabs			= $params->get('display_tabs');
$displaytime	= $params->get('display_time');
$animated		= $params->get('animation_enabled');
$transtime		= $params->get('transition_time');
$transition		= $params->get('transition');
$linktext		= $params->get('link_text', false);
$linktitle		= $params->get('link_title', true);
$showtitle		= $params->get('display_title', true);
$showtext		= $params->get('display_text', true);
$readmore		= $params->get('display_read_more', true);

if ($params->get('moduleId')) {
	$randVar = ltrim($params->get('module_name'));
} else {
	$randVar = 'id'.$content_helper->generateUniqueCode("5");
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
	#'.$moduleId.' .mask { height: 200px; width: 100%; overflow: hidden; }
	#'.$moduleId.' .mask li { width: 100%; background-color: white; }
	#'.$moduleId.' .buttons li { float: left; '.$tabStyle.' }
	#'.$moduleId.' .img, #'.$moduleId.' .wrapper { float:right; }
	#'.$moduleId.' .active { font-weight: bold; }
	';
	$document->addStyleDeclaration( $css );
}


	JHTML::_('behavior.mootools');
	JHTML::script('fx.elements.js', 'media/mod_oapj/');
	JHTML::script('loop.js', 'media/mod_oapj/');
	JHTML::script('slideshow.js', 'media/mod_oapj/');
	
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

?>

<div id="<?php echo $moduleId; ?>">
	<div class="mask">
		<ul id="b<?php echo $moduleId; ?>">		
			<?php foreach($items as $i=>$item) {
				// image
				
				
				if (isset($item->image)) {
					$item->image = '<img src="'.$item->image.'" style="clear: right; float: right;" />';
				} else {
					$item->image = null;
				}
				
				// title
				if ($showtitle) { 
					// link title
					if ($linktitle) {
						$item->title = '<a href="'.$item->link.'">'.$item->title.'</a>';
					}
					
					$item->title_html = '<div class="title">'.$item->title.'</div>';
				} else {
					$item->title_html = null;
				}
				
				// text
				if ($showtext) { 
					// link title
					if ($linktext) {
						$item->introtext = '<a class="bodyLink" href="'.$item->link.'">'.$item->introtext.'</a>';
					}
					
					$item->introtext_html = '<div class="text">'.$item->introtext.'</div>';
				} else {
					$item->introtext_html = null;
				}
				
				if ($readmore) {
					$item->readmore = '<a href="'.$item->link.'" class="readmore">'.JText::_('READ_MORE').'</a>';
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
			<div class="clearfix"></div>
		</ul>
	<?php } ?>
	<div class="clearfix"></div>
</div>