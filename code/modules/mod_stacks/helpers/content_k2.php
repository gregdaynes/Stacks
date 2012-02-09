<?php defined('_JEXEC') or die();

class k2Helper extends StackHelper
{
	var $_catid	= null;
	var $_db	= null;
	
	/**
	 *
	 */
	public function getItems()
	{	
		// load vars
		$db				= &JFactory::getDBO();
		$params			= $this->_params;
		$this->_db		= $db;
		$this->_catid	= $params->get('k2_categories', array(0));
		
		if (!is_array($this->_catid)) {
			$this->_catid = array($this->_catid);
		}
		
		// buildQuery
		$db->setQuery($this->_buildQuery());
		//echo $db->explain();
		
		//exit;
		$this->_items = $db->loadObjectList();
		
		// no items		
		if (!$this->_items) {
			return false;
		}
		
		
		return true;
	}
	
	private function _buildQuery()
	{	
		// setup some vars
		$params	= $this->_params;
		$db		= $this->_db;
		$itemCount = $params->get('item_count', 10); // number of items
		
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
		$params		= $this->_params;
		$db			= $this->_db;
		$date		= &JFactory::getDate();
		$null_date  = $db->getNullDate();
		$now		= $date->toMySQL();
		
		if ($params->get('featured_content_only', 0)) {
			$where[] = 'items.featured = 1';
		} else {
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
		$order = ' ORDER BY items.'.$params->get( 'order', 'publish_up' ).' '.$params->get('order_direction', 'DESC');
		
		return (string) $order;
	}
}