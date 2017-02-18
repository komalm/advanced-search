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

//~ require_once(JPATH_SITE . '/components/com_osian/common.php');

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/advsearch.php';

class AdvsearchController extends JControllerLegacy
{

	/*
	 *  Added by Amol
	 *  This returns the search fields on repective search indexer.
	 *
	 * */
	function get_attributes()
	{
		$input 	=	JFactory::getApplication()->input;
		$type 	= 	$input->get('type', '', 'STRING');
		if($type)
		{
			// Calling advsearch getAttributes method to get the search parameters.
			$model	= $this->getModel('advsearch');
			$result =  $model->getAttributes($type);
			if($result)
				echo $result;
			else
				echo JText::_('COM_ADVANCED_SEARCH_SELECT_INDEXER_FIELDS_FIRST');
				Jexit();
		}
		else
			echo JText::_('COM_ADVANCED_SEARCH_SELECT_INDEXER_FIRST');
		Jexit();
	}


	function get_fields()
	{
		$model	= $this->getModel('advsearch');
		echo $Fields =  $model->getFields();
		Jexit();
	}


	function getFormfield()
	{
		$field_key = JRequest::getVar('field');
		$model	= $this->getModel('advsearch');
		echo $Fields =  $model->getFieldType();
		Jexit();
	}

 	function delete()
 	{

   		$db = JFactory::getDBO();
		$id = JRequest::getVar('id');
		$query = "DELETE FROM `paai_advanced_search_saved_searches` WHERE id=$id";
		$db->setQuery($query);
		$db->loadObjectList();
		$message="deleted sucessfully";
		$this->setRedirect(JRoute::_('index.php?option=com_advsearch&view=searchlist'),$message);

	}

	function cronjob()
	{
		$model	= $this->getModel('searchindexer');

		$result =  $model->cronjob();
	}

	function getRelatedFieldOptions()
	{
		$model	= $this->getModel('advsearch');
		echo json_encode($model->getRelatedFieldOptions());
		Jexit();
	}

}
