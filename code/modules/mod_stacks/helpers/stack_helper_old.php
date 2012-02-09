<?php defined('_JEXEC') or die();

class StackHelper extends JModuleHelper
{
	
	var $_catid = null; // array
	var $_item = null; // int
	var $_mode = null; // int // 0 - item | 1 - categories
	var $_params = null; // object
	
	public function getItems(&$params)
	{
		// load up some parameters
		$db = &JFactory::getDBO();
		$this->_params = $params;
		$this->_catid = $params->get('category_id', null);
		$this->_item = $params->get('item', 0);
		
		/**
		 * mode check
		 * have to do this like this, can't deselect item in module parameters
		 *
		 * check if category param is empty
		 */
		switch($this->_catid)
		{
			case null:
				$this->_mode = 0;
				break;
				
			default:
				$this->_mode = 1;
		}
	
		/*
		 * Query
		 */
		
		// buildQuery
		$db->setQuery($this->_buildQuery());
		//echo $db->explain();
		
		// fetch item(s)
		$items = $db->loadObjectList();
		
		// no items		
		if (!$items) {
			return;
		}
		
		// format and create item objects
		return $this->_processItems($items);
		
		exit;
	}
	
	private function _buildQuery()
	{	
		// setup some vars
		$params = $this->_params;
		$db = &JFactory::getDBO();
		
		$itemCount = $params->get('itemcount', 10); // number of items
	
		// does not matter how many items we want if we only select 1
		if ($this->_mode == 0) {
			$itemCount = 1;
		}
		
		$query = $db->getQuery(true);
		$query = 'SELECT items.* '
			    .'FROM #__k2_items as items '
			    . $this->_buildQueryJoins()
			  	. $this->_buildQueryWhere()
			  	. $this->_buildQueryOrder()
			    .' LIMIT '. $itemCount
			    .' OFFSET '. $params->get('offset', 0)
			    ;
		
		return $query;
	}
	
	private function _buildQueryJoins()
	{
		return;
	}
	
	private function _buildQueryWhere()
	{	
		$params = $this->_params;
		$db	  = &JFactory::getDBO();
		$date = &JFactory::getDate();
		$null_date  = $db->getNullDate();
		$now		= $date->toMySQL();

		switch($this->_mode) {
			case 0:
				$where[] = 'items.id = '.$this->_item;
				break;
			
			case 1:
			default:
				$where[] = '(items.catid = '.implode(' OR items.catid = ', $this->_catid).')';
		}
		
		$where[] = 'items.published = 1'; // published items only
		$where[] = '(items.publish_up = "'.$null_date.'" OR items.publish_up <= "'.$now.'")';
		$where[] = '(items.publish_up = "'.$null_date.'" OR items.publish_up <= "'.$now.'")';
		$where[] = '(items.publish_down = "'.$null_date.'" OR items.publish_down >= "'.$now.'")';
		
		return (string) ' WHERE '.implode(' AND ', $where);
	}
	
	private function _buildQueryOrder()
	{
		$params = $this->_params;
		$order = ' ORDER BY '.$params->get( 'order', 'items.publish_up DESC' );
		
		return (string) $order;
	}
	
	private function _processItems($items)
	{
		// params
		$params 		 = $this->_params;
		$moduleId		 = $params->get('modulename');
		$itemid			 = $params->get('itemid', false);
		$lengthtitle	 = $params->get('lengthtitle', 60);
		$lengthtext		 = $params->get('lengthtext', 250);
		$trunctitle		 = $params->get('trunctitle', '&hellip;');
		$trunctext		 = $params->get('trunctext', '&hellip;');
		$image			 = $params->get('image', 1);
				
		foreach($items as $index=>$item)
		{
			if ($itemid) {
				$itemid = '&amp;Itemid='.$itemid;
			}
		
			// link
			$items[$index]->link = 'index.php?option=com_k2&amp;view=item&amp;id='.$item->id.$itemid;
			
			
			if ($image == 1) {
				$imgParams['path'] = null;
				
				
				// parse introtext for image
				$image_path = $this->_getImagePath($item->introtext);
				
				if ($image_path) {
					$image_path = $this->_imageProcessor($image_path[0]);
					$items[$index]->image = $image_path;
				}
			}
			
						// title
			$title = $item->title;
			$title = $this->_stripHtmlTags($title);
			$title = $this->_truncate($title, $lengthtitle, $trunctitle);
			$items[$index]->title = $title;
						
			// text
			$introtext = $this->_stripHtmlTags($item->introtext);
			$items[$index]->introtext = $this->_truncate($introtext, $lengthtext, $trunctext);
			
			// Alias
			if ($item->alias == '') {
				$item[$index]->alias = JFilterOutput::stringURLSafe($item->title);
			} else {
				$items[$index]->alias = $item->alias;
			}
		}
		
		return $items;
	}
	
	/**
	 * imageProcessor
	 */
	private function _imageProcessor($path)
	{
		// no file, no work
		if (!file_exists($path)) {
			return null;
		}
		
		jimport('joomla.filesystem.file');
		
		$filepath = pathinfo($path);
		$filename = $filepath['filename'];
		$extension = $filepath['extension'];
		$cache_path = JPATH_CACHE."/mod_stacks/";
		
		// create cache folder
		if (!JFolder::exists($cache_path)) {
			JFolder::create($cache_path);
		}
		
		// image dimensions
		$imageWidth	 = $this->_params->get('imagewidth');
		$imageHeight = $this->_params->get('imageheight');
		
		// modify filename with dimensions added
		$filename = $filename.'-'.$imageWidth.'x'.$imageHeight.'.'.$extension;
		
		// check cache for file
		if (JFile::exists($cache_path.$filename)) {
			return "cache/mod_stacks/".$filename;
		}
		
		// import image library
		jimport('joomla.image.image');
		
		$imageProperties = JImage::getImageFileProperties($path);
		
		// image dimensions wrong
		if ($imageWidth !== $imageProperties->width || $imageHeight !== $imageProperties->height) 
		{
		
			// center cropping
			$x = true; $y = false;
			if ($imageWidth < $imageHeight) {
				$x = false;
				$y = true;
			}
			
			// width is largest = cropping Height
			if ($x) {
				$top = floor(($imageWidth - $imageHeight) / 2);
				$left = 0;
			} else {
				$top = 0;
				$left = floor(($imageHeight - $imageWidth) / 2);
			}
		
			$image = new JImage;
			$image->loadFile($path);
			$image->resize($imageWidth, $imageHeight, false, 3);
			$image->crop($imageWidth, $imageHeight, $left, $top, false);
			$image->toFile($cache_path.$filename, IMAGETYPE_JPEG, array('quality' => 70));
			
			return "cache/mod_stacks/".$filename;
		} else {
			
			// image dimensions correct
			// copy to cache
			JFile::copy($path, $cache_path.$filename);
			
			return "cache/mod_stacks/".$filename;
		}
		
		return null;
		
	}
	
	/**
	 * getImagePath
	 *
	 * regex find image path inside string
	 */
	private function _getImagePath($string)
	{
		$string = preg_match('/<img[^>]+>/i', $string, $match);
		$string = $match;
		$string = preg_replace( '/.*<img.*src="(.*?)".*?>.*/', '\1', $string );	
		
		return $string;
	}
	
	private function _stripHtmlTags($text)
	{
		$text = preg_replace(
			array(
				// Remove invisible content
				'@<head[^>]*?>.*?</head>@siu',
				'@<style[^>]*?>.*?</style>@siu',
				'@<script[^>]*?.*?</script>@siu',
				'@<object[^>]*?.*?</object>@siu',
				'@<embed[^>]*?.*?</embed>@siu',
				'@<applet[^>]*?.*?</applet>@siu',
				'@<noframes[^>]*?.*?</noframes>@siu',
				'@<noscript[^>]*?.*?</noscript>@siu',
				'@<noembed[^>]*?.*?</noembed>@siu',
	
				// Add line breaks before & after blocks
				'@<((br)|(hr))@iu',
				'@</?((address)|(blockquote)|(center)|(del))@iu',
				'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
				'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
				'@</?((table)|(th)|(td)|(caption))@iu',
				'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
				'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
				'@</?((frameset)|(frame)|(iframe))@iu',
			),
			array(
				' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
				"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
				"\n\$0", "\n\$0",
			),
			$text );
	
		// Remove all remaining tags and comments and return.
		return strip_tags( $text );
	}
	
	/**
	 * truncate
	 *
	 * trim string to nearest word matching character length
	 */
	protected function _truncate($text, $length, $trunc)
	{
		if (strlen($text) > $length) {
			$length -= strlen($trunc);
			$text = strrev(strstr(strrev(substr($text, 0, $length)), ' '));
			$text = trim($text).$trunc;
		}
		
		return $text;
	}
	
	public function generateUniqueCode($length = "")
	{	
		$code = md5(uniqid(rand(), true));
		if ($length != "") return substr('e'.$code, 0, $length-1);
		else return 'e'.$code;
	}
	
	
}