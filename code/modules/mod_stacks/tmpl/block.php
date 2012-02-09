<?php defined('_JEXEC') or die('Restricted access'); 

$showtitle = $params->get('showtitle', true);
$showtext = $params->get('showtext', true);
$linktext = $params->get('linktext', true);
$linktitle = $params->get('linktitle', true);
$readmore = $params->get('readmore', true); ?>

<?php foreach ($items as $item) : 

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
		
		$item->title = '<span class="title">'.$item->title.'</span>';
	} else {
		$item->title = null;
	}
	
	// text
	if ($showtext) { 
		// link title
		if ($linktext) {
			$item->introtext = '<a class="bodyLink" href="'.$item->link.'">'.$item->introtext.'</a>';
		}
		
		$item->introtext = $item->introtext;
	} else {
		$item->introtext = null;
	}
	
	if ($readmore) {
		$item->readmore = '<a href="'.$item->link.'" class="readmore">'.JText::_('READ_MORE').'</a>';
	} else {
		$item->readmore = null;
	}

?>

<!-- ARTICLE -->
<tr>
	<td rowspan="2" class="image">
		<?php echo $item->image; ?>
	</td>
	<td class="title">
		<b><?php echo $item->title; ?></b>
	</td>
</tr>
<tr>
	<td valign="top" class="text">
		<?php echo '<spann class="text">'.$item->introtext.' '. $item->readmore.'</span>'; ?>
	</td>
</tr>
<!-- /ARTICLE -->
<?php endforeach; ?>