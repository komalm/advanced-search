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

require_once JPATH_COMPONENT.'/controller.php';

require_once(JPATH_SITE.DS.'components/com_osian/common.php');
/**
 * Createsearchindexer controller class.
 */
class AdvsearchControllerCreatesearchindexer extends AdvsearchController
{

	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @since	1.6
	 */
	public function edit()
	{
		$app			= JFactory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_advsearch.edit.createsearchindexer.id');
		$editId	= JFactory::getApplication()->input->getInt('id', null, 'array');

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_advsearch.edit.createsearchindexer.id', $editId);

		// Get the model.
		$model = $this->getModel('Createsearchindexer', 'AdvsearchModel');

		// Check out the item
		if ($editId) {
            $model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId) {
            $model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_advsearch&view=createsearchindexer&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model = $this->getModel('Createsearchindexer', 'AdvsearchModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_advsearch.edit.createsearchindexer.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_advsearch.edit.createsearchindexer.id');
			$this->setRedirect(JRoute::_('index.php?option=com_advsearch&view=createsearchindexer&layout=edit&id='.$id, false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_advsearch.edit.createsearchindexer.data', $data);

			// Redirect back to the edit screen.
			$id = (int)$app->getUserState('com_advsearch.edit.createsearchindexer.id');
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_advsearch&view=createsearchindexer&layout=edit&id='.$id, false));
			return false;
		}

            
        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }
        
        // Clear the profile id from the session.
        $app->setUserState('com_advsearch.edit.createsearchindexer.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('Item saved successfully'));
        $this->setRedirect(JRoute::_('index.php?option=com_advsearch&view=searchindexer', false));

		// Flush the data from the session.
		$app->setUserState('com_advsearch.edit.createsearchindexer.data', null);
	}
    
    
    function cancel() 
    {
        
		$app	= JFactory::getApplication();

		//Get the edit id (if any)
		$id = (int) $app->getUserState('com_advsearch.edit.createsearchindexer.id');
        if ($id) {
            //Redirect back to details
            $app->setUserState('com_advsearch.edit.createsearchindexer.id', null);
			$this->setRedirect(JRoute::_('index.php?option=com_advsearch&view=createsearchindexer&id='.$id, false));
        } else {
            //Redirect back to list
			$this->setRedirect(JRoute::_('index.php?option=com_advsearch&view=searchindexer', false));
        }
        
     
        
    }

	// Shows the actual results based on Advanced Search performed
    function showdata()
    {
		$app = App::getInstance('zoo');
		$app->loadHelper(array('zlfw'));
		$post = JRequest::get('post');
		$get = JRequest::get('get');
			
		// Store post & get values into the session so that we can get search parameters easily while pagination
		$session = JFactory::getSession();
		/*if(!$get['start'])
		$session->set('post', $post);*/
		
		//show captions save in cookies
		if($get['showCaptions']){
			setcookie("showCaptions", $get['showCaptions'], time()+3600, "/");
		}
			
		
		$model	= $this->getModel('createsearchindexer');
		$results =  $model->showdata();
		$count = 	$results['count'];
		
		//print_r($results); exit;
		
		//echo "<input type='hidden' name='advtotalcount' class='advtotalcount' value='".number_format($count)."' />";
		/*if($count < 1)
		{
			echo "<div class='totalresult'>".JTEXT::_("COM_ADVSEARCH_NO_RESULT_FOUND")."</div>";
			
			Jexit();
		}*/
		if(count($results) > 1)
		{ 
			$i=0;
			
			foreach($results as $key=>$val)
			{	
				$i++;
				
				$iitemName = $val->title;
				$itemid = $val->record_id; 
				$elements = $val->elements;
				$primary_cat = $val->primary_cat;
				$secondary_cat = $val->secondary_cat;
				//$image = $elements[$imgkey][0]['file'];
				$image = $val->image;
				$height = 130;
				$width1 = 130;
				$height1 = 130;
				$width = '';
				$id = $secondary_cat;
				
				// Square image 100x100
				
				$carr = array(15,16,17,18,19,20,22,23);
				$itemirn = "";				
				if( in_array($primary_cat,$carr))
				{	
					$parent_cat_name = CommonFunctions::category_prefix_pair($primary_cat);
					$cdname = CommonFunctions::getCatName($secondary_cat);
					
					$tmpl = strtolower($parent_cat_name).'.'.$cdname->name;
					
					$tpath = JPATH_SITE.DS.'media/zoo/applications/blog/templates/osian/renderer/rendercdt'.DS.$parent_cat_name.DS.$tmpl.'.phtml';
				
					if(JFile::exists($tpath))
					{ 
						require_once $tpath;
						$classname = 'DisplayPage'.$parent_cat_name.$cdname->name;
						$elements = json_decode($val->elements,true);

						$itemirn = $classname::getirn($elements);
					}
					
				}

				
				if(trim($image) != "")
					list($originalWidth, $originalHeight) = getimagesize(JPATH_SITE.DS.$image);
				
				if($originalHeight > 0){
					$ratio = $originalWidth / $originalHeight;

					if($ratio > JComponentHelper::getParams('com_osian')->get('maxratio')){
						$iw = $height * JComponentHelper::getParams('com_osian')->get('maxratio');
						$thmb_file = $app->zlfw->resizeImage(JPATH_SITE.DS.$image, $iw, $height, 0);
					}
					else{
						$thmb_file = $app->zlfw->resizeImage(JPATH_SITE.DS.$image, $width, $height, 1);
					}

					$tooltip_file = $app->zlfw->resizeImage(JPATH_SITE.DS.$image, $width1, $height1, 1);

					$item = $app->table->item->get($itemid);
					
					$link = $val->url.'&advcount='.$count;
					$link = JRoute::_($link);

					file_exists($image) ? $imgPath = JURI::root() . $app->path->relative($thmb_file) : $imgPath = JURI::base().'media/zoo/applications/blog/templates/osian/assets/images/images.jpg';
					
					if(trim($thmb_file) != ""){
						$imgSize = getimagesize($thmb_file);
						$sizeArray = explode('"', $imgSize[3]);
						$width_f = ($sizeArray[1]) ? $sizeArray[1] : 207;
						$height_f = ($sizeArray[3]) ? $sizeArray[3] : 300;
					}else{
						$imgPath = JURI::base().'media/zoo/applications/blog/templates/osian/assets/images/images.jpg';
						
						$imgSize = getimagesize($imgPath);
						$sizeArray = explode('"', $imgSize[3]);
						$width_f = ($sizeArray[1]) ? $sizeArray[1] : 207;
						$height_f = ($sizeArray[3]) ? $sizeArray[3] : 300;
					}
				}else{
					$item = $app->table->item->get($itemid);
					$link = $val->url.'&advcount='.$count;
					$link = JRoute::_($link);
					$imgPath = JURI::base().'media/zoo/applications/blog/templates/osian/assets/images/images.jpg';
					$imgSize = getimagesize($imgPath);
					$sizeArray = explode('"', $imgSize[3]);
					$width_f = ($sizeArray[1]) ? $sizeArray[1] : 207;
					$height_f = ($sizeArray[3]) ? $sizeArray[3] : 300;
				}
					
				$images[$i] = array("link" => $link, "itemName" => $itemirn, "imgPath" => $imgPath, "width" => $width_f,  "height" => $height_f, "itemid" => $itemid );
			
			} 
			//print_r($images);
			echo json_encode($images);
			exit();
			//echo '</div>';
			Jexit();
		}else{
			echo json_encode('nodata');
			Jexit();
		}
	} 
	// Show data ends
	
	function savesearch()
    {
		
		$post = JRequest::get('post');
		$get = JRequest::get('get');
			
		// Store post & get values into the session so that we can get search parameters easily while pagination
		$session = JFactory::getSession();
		if(!$get['start'])
		$session->set('post', $post);
		$session->set('get', $get);
		
		$result = "";
		$model	= $this->getModel('createsearchindexer');
		$result =  $model->savesearch();
		if($result == 1)
			echo JTEXT::_("COM_ADVSEARCH_SEARCH_ALREADY_EXISTS");
		if($result == 2)
			echo JTEXT::_("COM_ADVSEARCH_SEARCH_SAVED");
		Jexit();			
	} 
	
    
    
}
