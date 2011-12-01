<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$readmoretext	= $params->get('readmoretext', true);
?>

<tr>
	<td colspan="2">
		<h1><?php echo $params->get('title'); ?></h1>
	</td>
</tr>

<?php foreach ($items as $item) { ?>
<!-- ARTICLE -->
<tr>
	<td rowspan="2" class="image">
		<?php
		if (isset($item->image)) {
			$item->image = '<img src="'.$item->image['path'].'"  />';
		} else {
			$item->image = null;
		}	
		?>
		<?php echo $item->image; ?>
	</td>
	<td class="title">
		<b><?php echo $item->title; ?></b>
	</td>
</tr>
<tr>
	<td valign="top" class="text">
		<?php echo $item->introtext .' '.'<a href="'.$item->link.'" class="readmore">'.$readmoretext.'</a>'; ?>
		
	</td>
</tr>
<!-- /ARTICLE -->
<?php }
exit;