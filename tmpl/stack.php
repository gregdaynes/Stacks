<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = &JFactory::getDocument();

$moduleId = ltrim($params->get('modulename'));
$showtitle = $params->get('showtitle', true);
$showtext = $params->get('showtext', true);
$linktext = $params->get('linktext', true);
$linktitle = $params->get('linktitle', true);
$clearfix = $params->get('clearfix', 'clear');
$readmore		= $params->get('readmore', true);
$readmoretext	= $params->get('readmoretext', true);

if ($params->get('customcss') == 0)
{
	$css = '
	#'.$moduleId.' {
		width: 100%;
		float: left;
		clear: left;
	}
	
	#'.$moduleId.' ul {
		padding-left: 0;
	}
	
	#'.$moduleId.' li {
		list-style: none !important;
		display: block;
		clear: left;
		float: left;
		width: 100%;
	}
	
	#'.$moduleId.' h2 {
		margin-top: 0;
		margin-bottom: 0;
	}
	
	#'.$moduleId.' .img,
	#'.$moduleId.' .wrapper {
		clear: right;
		float: right;
	}

	#'.$moduleId.' div a {
		float: right;
	}
	
	.clear {
		display: block;
		float: none;
		clear: both;
	}
	';
	$document->addStyleDeclaration($css);
}

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
	<ul>
		<?php
			foreach( $items as $i=>$item )
			{
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
						$item->title = '<a class="titleLink" href="'.$item->link.'">'.$item->title.'</a>';
					}
					
					$item->title = '<h2>'.$item->title.'</h2>';
				} else {
					$item->title = null;
				}
				
				// text
				if ($showtext) { 
					// link title
					if ($linktext) {
						$item->introtext = '<a class="bodyLink" href="'.$item->link.'">'.$item->introtext.'</a>';
					}
					
					$item->introtext = '<span class="text">'.$item->introtext.'</span>';
				} else {
					$item->introtext = null;
				}
				
				if ($readmore) {
					$item->readmore = '<a href="'.$item->link.'" class="readmore">'.$readmoretext.'</a>';
				} else {
					$item->readmore = null;
				}
				
				?>
				<li>
					<?php echo $item->image; ?>
					<?php echo $item->title; ?>
					<?php echo $item->introtext; ?>
					<?php echo $item->readmore; ?>
				</li>
				<?php
			}
		?>
	</ul>
</div>

<div class="<?php echo $clearfix; ?>"></div>