<?php defined('_JEXEC') or die();

class StackHelper extends JModuleHelper
{
	var $_params = null; // object
	var $_items = null;
	
	// set params
	public function construct($params)
	{
		$this->_params = $params;
	}
	
	public function processItems()
	{
		$items = $this->_items;
		
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
	 * generateUniqueCode
	 */
	public function generateUniqueCode($length = "")
	{	
		$code = md5(uniqid(rand(), true));
		if ($length != "") return substr('e'.$code, 0, $length-1);
		else return 'e'.$code;
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
		//	return "cache/mod_stacks/".$filename;
		}
		
		// import image library
		jimport('joomla.image.image');
		
		$imageProperties = JImage::getImageFileProperties($path);
		
		$image = new JImage;
		$image->loadFile($path);
		$image->resize($imageWidth, $imageHeight, false, 3);
				
		$horizontal_crop = $this->_params->get('horizontal_crop', 'left');
		$vertical_crop	 = $this->_params->get('vertical_crop', 'top');
		
		switch($horizontal_crop) {
			case 'left':
				$crop_h = 0;
				break;
				
			case 'center':
				$crop_h = floor(($image->getWidth() - $imageWidth) / 2);
				break;
				
			case 'right':
				$crop_h = $image->getWidth() - $imageWidth;
				break;
		}
		
		switch($vertical_crop) {
			case 'top':
				$crop_v = 0;
				break;
				
			case 'middle':
				$crop_v = floor(($image->getHeight() - $imageHeight) / 2);
				break;
				
			case 'bottom':
				$crop_v = $image->getHeight() - $imageHeight;
				break;
		}
		
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
		
		$image->crop($imageWidth, $imageHeight, $crop_h, $crop_v, false);
		$image->toFile($cache_path.$filename, IMAGETYPE_JPEG, array('quality' => 70));
		
		return "cache/mod_stacks/".$filename;
		
		
		return null;
		
	}	
}