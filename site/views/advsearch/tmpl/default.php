<?php
/**
 * @version     1.0.0
 * @package     com_advsearch
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      amol <amol_p@tekdi.net> - http://tekdi.net
 */

// no direct access
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScript(JURI::Base()."components/com_advsearch/assets/js/jquery-1.9.1.min.js");
$document->addScript(JURI::Base()."components/com_advsearch/assets/js/advsearch.js");
$document->addScript(JURI::base()."media/zoo/applications/blog/templates/osian/wallgallery/jquery.min.js");
//~ $document->addScript(JURI::base()."media/zoo/applications/blog/templates/osian/wallgallery/chosen.jquery.min.js");
$document->addScript(JURI::base()."components/com_advsearch/js/hackedchosen/chosenhacked.jquery.min.js");
$document->addScript(JURI::base()."components/com_advsearch/js/hackedchosen/chosen.jquery.min.js");

$document->addStyleSheet(JURI::Base()."components/com_advsearch/js/hackedchosen/chosen.min.css");
//~ $document->addStyleSheet(JURI::Base()."components/com_osian/style/chosen.min.css");
$document->addStyleSheet(JURI::Base()."components/com_advsearch/assets/css/style.css");



//~ $document->addScript(JURI::base()."components/com_advsearch/js/chosen/chosen.jquery.js");
//~ $document->addScript(JURI::base()."components/com_advsearch/js/chosen/chosen.proto.js");
//~ $document->addScript(JURI::base()."components/com_advsearch/js/chosen/docsupport/prism.js");

//~ $document->addStyleSheet(JURI::Base()."components/com_advsearch/js/chosen/chosen.css");



$document->addScriptDeclaration('
	var jq1102 = jQuery.noConflict();
	var rowminheigt			= 150;		//Minimum height for row
	var rowmaxheight		= 150;		//Maximum height for row
	var rowmaxwidth			= null;		//Minimum width for row
	var gutterwidthl1		= 20;		//Space between images
	var gutterwidthl2 		= 20; 	//Space between images on toggle
	var ajaxurl				= "index.php?option=com_osian&task=advsearch&view=grid";
	var animate_time		= 100;		//Time given to show newly appended images
	var total_images 		= 0;
	var go_to_top_speed 	= 500;
	var pagCount = 1;
'
);

$user = JFactory::getUser();

?>
<input type="hidden" name="checkuser" id="checkuser" value = "<?php echo $user->id;?>"/>

<div class="category-cover">
	<!--This is added for fixing message and buttons -->
	<div class="fixed"></div>
	<div class="category-left"></div>

	<div id="category-right" class="category-right" style="margin-top:30px;">

		<form id="searchform">

			<div class="main-body" align="left">

				<div class="cat-title">	<h1><?php echo JTEXT::_("COM_ADVSEARCH");?> </h1> </div>



					<div class="main-div">
						<?php
						// Show the classification select box here
						if($this->Classification) { 	?>

						<div class="clients-above">
								<div class="clients"> 	<?php echo $this->Classification; ?> </div>
						</div>
							<?php } ?>

						<div>
							<div class="ad-srch-button" >
								<input class="btn btn-default" type="submit" name="submit-advsearch" value="<?php echo JTEXT::_('COM_ADVANCED_SEARCH_SUBMIT');?>" class="submit" />
								<input class="btn btn-default" type="button" id="reset" name="reset" value="<?php echo JTEXT::_('COM_ADVANCED_SEARCH_RESET');?>" class="submit" />

							</div>
						</div>
							<div class="loadercover">
							<div class="loader_adv">	</div>
						</div>
						<div class="newcontainer"></div>
						<div style="clear:both"></div>
					</div>

					<div class="search" style="display:none;" >

						<span id="addfield"> </span>
						<!--div>
							<div class="ad-srch-button" >
								<input type="submit" name="submit-advsearch" value="<?php echo JTEXT::_('COM_ADVANCED_SEARCH_SUBMIT');?>" class="submit" />
								<input type="button" id="reset" name="reset" value="<?php echo JTEXT::_('COM_ADVANCED_SEARCH_RESET');?>" class="submit" />

							</div>
						</div-->
					</div>
				<div style="clear:both"></div>
			</div> <!-- main-body div close-->

		</div>
		</form>
		<!--script type="text/javascript" src="http://arrow.scrolltotop.com/arrow88.js"></script-->
		<?php

			$config = JFactory::getConfig();
			$debug_state = $config->get('debug');

			if ($debug_state == 0)
			{
				$document->addScript(JURI::Base()."components/com_advsearch/assets/js/arrow_top.min.js");
			}
			else
			{
				$document->addScript(JURI::Base()."components/com_advsearch/assets/js/arrow_top.js");
			}

		?>
		<noscript>Not seeing a <a href="http://www.scrolltotop.com/">Top</a></noscript>
	</div>
		<div class="clr"></div>
	</div>
