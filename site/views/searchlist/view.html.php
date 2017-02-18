<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1
 * @license    GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 *
 * @package    osianHelloWorld
 */

class advsearchViewsearchlist extends JView
{
  
 
	function display($tpl = null){
		// global $mainframe;
	  
	  // YOUR CUSTOM CODE HERE
    
    // $this->assignRef('items', $items);
		//die('in');
		
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

   $document =& JFactory::getDocument();
		// Get some data from the models
		$state		= $this->get('State');
		$item		= $this->get('Item');
		
		/*$greeting = $this->get( 'Greeting' );
	  $this->assignRef( 'greeting',	$greeting->greeting );
	  $this->assignRef( 'id',		$greeting->id );
	  $this->assignRef( 'content',	$greeting->content );*/
	  
	                $state= &$this->getModel();
		$item=$state->getData();

		$this->item=$item;
		//print_r($this->item);
               // die("view.html");
		parent::display($tpl);

	}
}
