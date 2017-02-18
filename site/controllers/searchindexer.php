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

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Searchindexer list controller class.
 */
class AdvsearchControllerSearchindexer extends AdvsearchController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	
	function getattributes()
	{
		
		$model	= $this->getModel('advsearch');
		echo $result =  $model->getAttributes();
		Jexit();
	}
}
