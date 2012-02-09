<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = &JFactory::getDocument();

$module_name	= ltrim($params->get('module_name'));
$display_title 	= $params->get('display_title', true);
$display_text 	= $params->get('display_text', true);
$link_text 		= $params->get('link_text', true);
$link_title 	= $params->get('link_title', true);
$read_more		= $params->get('display_read_more', true);

?>

<div id="<?php echo $module_name; ?>">
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
				if ($display_title) { 
					// link title
					if ($link_title) {
						$item->title = '<a class="titleLink" href="'.$item->link.'">'.$item->title.'</a>';
					}
					
					$item->title = '<div class="title">'.$item->title.'</div>';
				} else {
					$item->title = null;
				}
				
				// text
				if ($display_text) { 
					// link title
					if ($link_text) {
						$item->introtext = '<a class="bodyLink" href="'.$item->link.'">'.$item->introtext.'</a>';
					}
					
					$item->introtext = '<div class="text">'.$item->introtext.'</div>';
				} else {
					$item->introtext = null;
				}
				
				if ($read_more) {
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

