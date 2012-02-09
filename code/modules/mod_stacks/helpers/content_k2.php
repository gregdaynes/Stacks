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
		$this->_catid	= $params->get('k2_category_id', array(0));
		
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
		$itemCount = $params->get('itemcount', 10); // number of items
		
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
		
		$where[] = '(items.catid = '.implode(' OR items.catid = ', $this->_catid).')';	
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
}