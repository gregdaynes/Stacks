<?php
/**
 * @version   $Id: helper.php 567 2011-01-20 17:50:07Z media $
 * @package   jEvolve.SlideStack
 * @copyright Copyright (C) 2010 jEvolve, LLC. All rights reserved.
 * @license   GNU General Public License
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

class SlideStackHelper extends JModuleHelper
{
	var $db		   = null;
	var $catid	   = null;
	var $frontpage = null;
	var $itemcount = null;
	var $offset    = null;
	var $params	   = null;
	var $items	   = null;
	
	public function getItems(&$params)
	{
		// system stuff to load
		$this->db		 = &JFactory::getDBO();
		$this->params	 = $params;
		// module params
		$this->catid	 = $params->get('categories'); // categories to look at
		$this->frontpage = $params->get('frontpage', false); // frontpage items only
		$itemcount = $params->get('itemcount', 5 ); // Number of items
		$offset	 = $params->get('offset', 0 ); // offset number of items returned
		
		// render query
		$this->db->setQuery($this->_buildQuery(), $offset, $itemcount);
		$this->items = $this->db->loadObjectList();
				
		if (!$this->items) {
			return;
		}
		
		return $this->_constructItems();
	}
	
	private function _buildQuery()
	{
		$query = 'SELECT content.id'
			   .', content.title'
			   .', content.alias'
			   .', content.introtext'
			   .', content.catid'
			   .' FROM #__content as content'
			   .  $this->_buildQueryJoins()
			   .  $this->_buildQueryWhere()
			   .  $this->_buildQueryOrder()
			   ;
		
		return $query;
	}
	
	private function _buildQueryJoins()
	{
		if ($this->frontpage) {
			return ' LEFT JOIN #__content_frontpage AS frontpage ON frontpage.content_id = content.id ';
		}
	
		return;
	}
	
	private function _buildQueryWhere()
	{
		$null_date  = $this->db->getNullDate();
		$date 		= &JFactory::getDate();
		$now		= $date->toMySQL();
		$this->user	= &JFactory::getUser();
		$this->aid	= $this->user->get('aid');
		
		// setup categories
		if (!$this->frontpage) { // not frontpage only items
			if (is_array($this->catid)) {
				$this->catid = implode(' OR catid = ', $this->catid); // ## OR catid = ##
			}
			
			// final catid string
			$where[] = '(catid = ' . $this->catid . ')';
		} else {
			// frontpage only
			$where[] = 'frontpage.content_id = content.id';
		}
		
		$where[] = 'content.state = 1'; // published items only
		$where[] = 'content.access <= '. intval($this->aid); // access
		$where[] = '(content.publish_up = "'.$null_date.'" OR content.publish_up <= "'.$now.'")';
		$where[] = '(content.publish_up = "'.$null_date.'" OR content.publish_up <= "'.$now.'")';
		$where[] = '(content.publish_down = "'.$null_date.'" OR content.publish_down >= "'.$now.'")';
		
		return (string) ' WHERE '.implode(' AND ', $where);
	}
	
	private function _buildQueryOrder()
	{
		$order = ' ORDER BY '.$this->params->get( 'order', 'content.publish_up DESC' );
		
		return (string) $order;
	}
	
	private function _constructItems()
	{		
		// params
		$params 		 = $this->params;
		$moduleId		 = $params->get('modulename');
		$itemid			 = $params->get('itemid', -1);
		$lengthtitle	 = $params->get('lengthtitle', 60);
		$lengthtext		 = $params->get('lengthtext', 250);
		$trunctitle		 = $params->get('trunctitle', '&hellip;');
		$trunctext		 = $params->get('trunctext', '&hellip;');
		$image			 = $params->get('image', true);
		$imagewidth		 = $params->get('imagewidth', 100);
		$imageheight	 = $params->get('imageheight', 100);
		$imagedimensions = array("width"=>$imagewidth, "height"=>$imageheight);
		$imagecache		 = $params->get('image_cache', 1);
		$crop_mode		 = $params->get('crop_mode', 'adaptive');
		
		if ($imagecache == 1) {
			$imagecache = true;
		} else {
			$imagecache = false;
		}
		
		// use current item id if none specified
		if ($itemid == -1) {
			$itemid = JRequest::getVar('Itemid');
		}
				
		$items = $this->items;
		
		$evl = new evolveLibrary;
		$imgLib = $evl->loadHelper('Image');
		$imgParams = array(
			'width'   => $imagewidth,
			'height'  => $imageheight,
			'quality' => 70,
			'resizer' => $crop_mode,   // js, basic, adaptive, percentage, crop
			'cache'   => $imagecache,
			'cache_folder' => 'mod_slidestack',
			'unique_id' => $moduleId,
			'resize_up' => true
		);
		
		
		foreach($items as $index=>$item)
		{
			// link
			$items[$index]->link = 'index.php?option=com_content&amp;view=article&amp;id='.$item->id.'&amp;catid='.$item->catid.'&amp;Itemid='.$itemid;
			
			// Image
			if ($image == 1) {
				$imgParams['path'] = null;
				
				
				// parse introtext for image
				$image_path = $evl->getImagePath($item->introtext);
								
				// if image is found
				if ($image_path) {
					$imgParams['path'] = $image_path[0];
					$items[$index]->image = $imgLib->getThumbnail($imgParams);
				}
			}
			
			// title
			$title = $item->title;
			$title = $evl->stripHtmlTags($title);
			$title = $evl->truncate($title, $lengthtitle, $trunctitle);
			$items[$index]->title = $title;
						
			// text
			$introtext = $evl->stripHtmlTags($item->introtext);
			$items[$index]->introtext = $evl->truncate($introtext, $lengthtext, $trunctext);
			
			// Alias
			if ($item->alias == '') {
				$item[$index]->alias = JFilterOutput::stringURLSafe($item->title);
			} else {
				$items[$index]->alias = $item->alias;
			}
		}
		
		return $items;
	}
}