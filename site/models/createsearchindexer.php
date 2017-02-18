<?php
/**
 * @version     1.0.0
 * @package     com_advsearch
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      amol <amol_p@tekdi.net> - http://tekdi.net
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once(JPATH_SITE.DS.'components'.DS.'com_osian'.DS.'common.php');
/**
 * Advsearch model.
 */
class AdvsearchModelCreatesearchindexer extends JModelForm
{
    
    var $_item = null;
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('com_advsearch');

		// Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_advsearch.edit.createsearchindexer.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_advsearch.edit.createsearchindexer.id', $id);
        }
		$this->setState('createsearchindexer.id', $id);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}
        

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('createsearchindexer.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			} elseif ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}
    
	public function getTable($type = 'Createsearchindexer', $prefix = 'AdvsearchTable', $config = array())
	{   
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
        return JTable::getInstance($type, $prefix, $config);
	}     

    
	/**
	 * Method to check in an item.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int)$this->getState('createsearchindexer.id');

		if ($id) {
            
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
		}

		return true;
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int)$this->getState('createsearchindexer.id');

		if ($id) {
            
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
		}

		return true;
	}    
    
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML 
     * 
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_advsearch.createsearchindexer', 'createsearchindexer', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		$data = $this->getData(); 
        
        return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data)
	{
		$id = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('createsearchindexer.id');
        $user = JFactory::getUser();

        if($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'createsearchindexer.'.$id);
        } else {
            //Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_advsearch');
        }

        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

		$table = $this->getTable();
        if ($table->save($data) === true) {
            return $id;
        } else {
            return false;
        }
        
	}
	
	function showdata()
	{
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);	
		
		$session = JFactory::getSession();
		
		$post = Jrequest::get('get');
		$get = JRequest::get('get');
		
		$page = JRequest::getCmd('page');
		$pitem = JComponentHelper::getParams("com_osian")->get("records");

		$start = $page - 1;
		$end = $pitem;
		$start = ($start > 0) ? $start * $end : $start;	
		
		
		
		//echo "start: ".$start . "end: ". $end;
		
		//// Query limit variables code ends
		
		$range = $post['range'];
		$attributes = $post['attributes'];
		$values = $post['value'];
		$i=0;
		
		if($post['classification'])
		{
			//$adv_search_table_name = CommonFunctions::get_config_type_name($post['classification']);
			$wnnkey = CommonFunctions::getwnnkey('#__advanced_search_zoo_'.$post['classification']);
			
			$search_mode = JComponentHelper::getParams("com_advsearch")->get("search_mode");
			if($search_mode)
				$adv_search_table_name = $post['classification'];
			else
				$adv_search_table_name = $post['classification'].TEMP_TABLE_SUFFIX;
			
			$search_table = '#__advanced_search_zoo_'.$adv_search_table_name; 	// Table Name
		
		
			$search_table = $db->nameQuote('#__advanced_search_zoo_'.$adv_search_table_name).' as aszb'; 	// Table Name
			$search_table .= ' LEFT JOIN '.$db->nameQuote('#__zoo_item').' as ze ON aszb.record_id = ze.id';
		
		}
		
		// Column names of data to retrive
		$fields_arr = array('record_id','title','url','image', 'primary_cat', 'secondary_cat', $db->nameQuote($wnnkey).' AS wnn');
		$fields = implode(",aszb.", $fields_arr);
		$fields .= ", ze.elements";
		
			
		foreach($attributes as $key=>$value)
		{
			$Field_Values[$value] = $values[$key];	
		}
		
		if(!empty($post))
		{
			foreach($post['text'] as $key=>$val)
			{
				$mysession_arry[$key] = $val;
				$session->set('field'.$key, $val);
				$str = preg_replace('/_*/','',$val);
				$str = trim($str);
 				if($i==0 && ( empty($post['range']) || empty($post['singleselect']) || empty($post['multiselect']) ) )
				{
					if($str)
					{
						
						$query->select($fields);
						$query->from($search_table);
						if($post['searchtype'])
							{
								$Rlike_String = "[[:<:]]".$str."[[:>:]]";
								$query->where($db->nameQuote($key)." RLIKE '$Rlike_String'");
							}
						else
							$query->where($db->nameQuote($key)." LIKE '%$str%'");
						
						$save_search_ele[$key] = $val; 
						
					}
				}
				else
				{
					if($str)
					{
						$mysession_arry[$key] = $val;
						if($post['searchtype'])
						{
							
							$Rlike_String = "[[:<:]]".$str."[[:>:]]";
							$rlike_query = $db->nameQuote($key)." RLIKE '$Rlike_String'";
							$and .= " AND $rlike_query";
						}
						else
							$and .= " AND ".$db->nameQuote($key)." LIKE '%$str%'"; 
						
						$save_search_ele[$key] = $val; 
					}
				}
				$i++;
			}

			foreach($post['singleselect'] as $key=>$val)
			{ 

				$mysession_arry[$key] = $val;
				if($i==0 && ( empty($post['range']) || empty($post['text']) || empty($post['multiselect']) ) )
				{
					
					if($val)
					{
						$query->select($fields);
						$query->from($search_table);
						$query->where($db->nameQuote($key)." = '$val'");
						$save_search_ele[$key] = $val; 

					}
				}
				else
				{
					if($val)
					{
						$and .= " AND ".$db->nameQuote($key)." = '$val'"; 	
						$save_search_ele[$key] = $val; 
					}
				}
				$i++;
			}

			foreach($post['range'] as $key=>$val)
			{ 
				
				$val[1] = trim($val[1]);
				$val[2] = trim($val[2]);
				$mysession_arry[$key] = $val;
				
				if($i==0 && ( empty($post['text']) || empty($post['singleselect']) || empty($post['multiselect']) ) )
				{
					
					if($val)
					{
						$query->select($fields);
						$query->from($search_table);
						$query->where($db->nameQuote($key)." BETWEEN $val[1] AND $val[2]");
						$save_search_ele[$key] = array($val[1], $val[1]); 

					}
				}
				else
				{
					if($val)
					{
						$and .= " AND ".$db->nameQuote($key)." BETWEEN $val[1] AND $val[2]";			
						$save_search_ele[$key] = array($val[1], $val[1]); 
					}
				}
				$i++;
			}
			
			foreach($post['multiselect'] as $key=>$value)
			{

				$mysession_arry[$key] = $val;

				$multi = "'".implode("','", $post['multiselect'][$key])."'";
				
				if( $i == 0 && ( empty($post['text']) || empty($post['singleselect']) || empty($post['range']) ) )
				{
					$query->select($fields);
					$query->from($search_table);
					$query->where($db->nameQuote($key)." IN (".$multi.")");
				}
				else
				{
						$and .= " AND ".$db->nameQuote($key)." IN (".$multi.")"; 
				}
				$i++;
			}
			
			$session->set('My_query', $query.$and); 
			
			foreach($post['attributes'] as $k=>$v)
			{
				$mysession_new_array[$v] = $mysession_arry[$v];	
			}
			
			$session->set('Adv_Fields', $mysession_new_array); 
	
		
		
		}
		if($post['classification'])
			$session->set('classification', $post['classification']); 
		
		if($post['formsubmit'])
			$session->set('searchtype', $post['searchtype']);

		$myquery = $session->get('My_query'); 
		
		//echo $myquery; exit();
		
		$limit = " LIMIT $start, $end";
		$Final_Query = $myquery.' ORDER BY record_id ASC'.$limit;
		//die;
		$db->setQuery($Final_Query);
		$results = $db->loadObjectList();
		
		if($page == 1){
			
			$db->setQuery($myquery.' ORDER BY record_id ASC');
			$count_records = $db->loadObjectList();
			$actual_count = count($count_records);
			setcookie("searchresultcount", "",  time()-60*60*24, "/");
			setcookie("searchresultcount", $actual_count,  time()+60*60*24, "/");
		}
		
		foreach($count_records as $k=>$v)
		{
			$Resultant_Ids[$k] = $v->record_id;
		}
	
		$session->set('Resultant_Ids', $Resultant_Ids);
		return $results;

				
	}
	
	
	function savesearch()
	{
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);	
		
		// Get the values from session 
		$session = JFactory::getSession();
		$post = $session->get('post');
		$get = $session->get('get');
	
		$range = $post['range'];
		$attributes = $post['attributes'];
		$values = $post['value'];
		$i=0;
		
		//if($get['classification'])
		//$search_table = '#__advanced_search_zoo_'.$gets['classification']; 	// Table Name
		
		if($post['classification'])
		$search_table = '#__advanced_search_zoo_'.$post['classification']; 	// Table Name

		// Column names of data to retrive
		$fields_arr = array('title','url','image');
		$fields = implode(",", $fields_arr);
		
			
		foreach($attributes as $key=>$value)
		{
			$Field_Values[$value] = $values[$key];	
		}
		
		foreach($post['text'] as $key=>$val)
		{

			$str = preg_replace('/_*/','',$val);

			if($i==0 && ( empty($post['range']) || empty($post['singleselect']) || empty($post['multiselect']) ) )
			{
				if($str)
					$save_search_ele[$key] = $val; 
			}
			else
			{
				if($str)
					$save_search_ele[$key] = $val; 
			}
			$i++;
		}
		
		foreach($post['singleselect'] as $key=>$val)
		{ 
			if($i==0 && ( empty($post['range']) || empty($post['text']) || empty($post['multiselect']) ) )
			{
				
				if($val)
					$save_search_ele[$key] = $val; 
			}
			else
			{
				if($val)
					$save_search_ele[$key] = $val; 
			}
			$i++;
		}

		//  For Range 
		foreach($post['range'] as $key=>$val)
		{ 
			
			if($i==0 && ( empty($post['text']) || empty($post['singleselect']) || empty($post['multiselect']) ) )
			{
				
				if($val)
					$save_search_ele[$key] = array($val[1], $val[2]); 
			}
			else
			{
				if($val)
					$save_search_ele[$key] = array($val[1], $val[2]); 
			}
			$i++;
		}
		
		
		
		// For Multiselect 
		foreach($post['multiselect'] as $key=>$value)
		{

			$multi = "'".implode("','", $post['multiselect'][$key])."'";
			
			if( $i == 0 && ( empty($post['text']) || empty($post['singleselect']) || empty($post['range']) ) )
				$save_search_ele[$key] = array($multi); 			
			else
				$save_search_ele[$key] = array($multi); 
			$i++;
		}
	
		// Save Search Code Starts
		if($post['savesearch'] == "on" && $post['searchname'])
		{	
			
			$db = JFactory::getDBO();
			$user = JFactory::getUser();
			$query = $db->getQuery(true);	
			$date =& JFactory::getDate();

			// check whether search name exits
			$name_query = "SELECT name FROM #__advanced_search_saved_searches WHERE 
						   userid = $user->id 
						   AND name = ".$db->Quote($post['searchname']);
			$db->setQuery($name_query);
			$search_name = $db->loadResult();
			
			if($search_name)
			{
					return 1;
			}
			else
			{
				
				$attributes = $post['attributes'];
				$values = $post['value'];
				$i=0;
				
				//if($get['classification'])
				//$search_table = '#__advanced_search_zoo_'.$gets['classification']; 	// Table Name
				
				if($post['classification'])
				$search_table = '#__advanced_search_zoo_'.$post['classification']; 	// Table Name

				$str = "test data";
				//$arr = json_encode($array);
				$classification = $post['classification'];
				$save_search_ele_json = json_encode($save_search_ele); 
				
				$query_client = "SELECT client FROM #__advanced_search_index WHERE ".$db->nameQuote('content_type')." = '$classification'";	
				$db->setQuery($query_client);
				$client = $db->loadResult();
				
				$Search_Data = new stdClass;
				$Search_Data->userid = $user->id; 				// Logged in user id
				$Search_Data->client = $client; 				// Advanced Search client name
				$Search_Data->type = $classification;			// Advanced Search Clients type
				$Search_Data->name = $post['searchname'];   	// Search name
				$Search_Data->fieldval = $save_search_ele_json; // Logged in userid 
				$Search_Data->date = $date->toFormat();
				
				
				$db->insertObject('#__advanced_search_saved_searches', $Search_Data, 'id');	
				
				//$message = $post['searchname']."Search saved";
				return 2;
			}
		}
		// Save Search Code ends	

				
	}
	
	
	
	    
    
}
