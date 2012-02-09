<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = &JFactory::getDocument();

$moduleId 		= ltrim($params->get('modulename'));
$showtitle 		= $params->get('showtitle', true);
$showtext 		= $params->get('showtext', true);
$linktext 		= $params->get('linktext', true);
$linktitle 		= $params->get('linktitle', true);
$clearfix		= $params->get('clearfix', 'clear');
$readmore		= $params->get('readmore', true);

?>

<div id="<?php echo $moduleId; ?>">
	<ul>
		<?php
			foreach( $items as $i=>$item )
			{
				// image
				if (isset($item->image)) {
					$item->image = '<img src="'.$item->image.'" />';
				} else {
					$item->image = null;
				}
				
				// title
				if ($showtitle) { 
					// link title
					if ($linktitle) {
						$item->title = '<a class="titleLink" href="'.$item->link.'">'.$item->title.'</a>';
					}
					
					$item->title = '<div class="title">'.$item->title.'</div>';
				} else {
					$item->title = null;
				}
				
				// text
				if ($showtext) { 
					// link title
					if ($linktext) {
						$item->introtext = '<a class="bodyLink" href="'.$item->link.'">'.$item->introtext.'</a>';
					}
					
					$item->introtext = '<div class="text">'.$item->introtext.'</div>';
				} else {
					$item->introtext = null;
				}
				
				if ($readmore) {
					$item->readmore = '<a href="'.$item->link.'" class="readmore">'.JText::_('READ_MORE').'</a>';
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