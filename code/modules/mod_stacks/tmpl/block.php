<?php defined('_JEXEC') or die('Restricted access'); 

$display_title = $params->get('display_title', true);
$display_text = $params->get('display_text', true);
$link_text = $params->get('link_text', true);
$link_title = $params->get('link_title', true);
$read_more = $params->get('display_read_more', true); ?>

<?php foreach ($items as $item) : 

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
		
		$item->title = '<span class="title">'.$item->title.'</span>';
	} else {
		$item->title = null;
	}
	
	// text
	if ($display_text) { 
		// link title
		if ($link_text) {
			$item->introtext = '<a class="bodyLink" href="'.$item->link.'">'.$item->introtext.'</a>';
		}
		
		$item->introtext = $item->introtext;
	} else {
		$item->introtext = null;
	}
	
	if ($read_more) {
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