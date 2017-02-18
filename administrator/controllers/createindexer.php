<?php
/**
 * @version     1.0.0
 * @package     com_advsearch
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      amol <amol_p@tekdi.net> - http://tekdi.net
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Createsearchindexer controller class.
 */
class AdvsearchControllerCreateindexer extends JControllerForm
{

    function __construct() {
		
        $this->view_list = 'createindexer';
        parent::__construct();
    }

    function saveIndexer(){
		//die('here');
		//print_r($_POST); die;
		
		$model	= $this->getModel('createindexer');
		$result =  $model->saveData();
		$link = JURI::Base()."index.php?option=com_advsearch&view=searchindexer";
		$msg = "Search Indexer saved successfully";
		$this->setRedirect($link, $msg);
		//die;
    
    }
    
      function Cancel(){
		
		//echo '1'; die;
		$link = JURI::Base()."index.php?option=com_advsearch&view=searchindexer";
		$this->setRedirect($link);
		//die;
      
    }
 
    function delete()
    {
		
		$model	= $this->getModel('createindexer');
		$result =  $model->deleteIndexer();
		$link = JURI::Base()."index.php?option=com_advsearch&view=searchindexer";
		$msg = "Search Indexer Removed successfully";
		$this->setRedirect($link, $msg);
		
	}
	function edit()
	{
		$post = JRequest::get('post');
		if($post['cid'][0])
		{
			$link = JURI::Base()."index.php?option=com_advsearch&view=createindexer&layout=edit&id=".$post['cid'][0];
			$this->setRedirect($link, $msg);
		}
	}
	function add()
	{
			$link = JURI::Base()."index.php?option=com_advsearch&view=createindexer";
			$this->setRedirect($link, $msg);
	}


}
