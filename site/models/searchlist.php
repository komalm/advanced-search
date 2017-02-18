<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );
//echo"hi";
class advsearchModelsearchlist extends JModel
{
 //function generate(){
    //$items = JRequest::getVar('data',array(), 'post', 'array');
    //print_r($items);
//}
function getData(){

$user =& JFactory::getUser();


$db=& JFactory::getDBO();
//$q1="desc `paai_advanced_search_saved_searches`";
  $q1="SELECT * FROM `paai_advanced_search_saved_searches` ORDER By name ASC";
 $db->setQuery($q1);
$arr=$db->loadObjectList(); 

//$object = (object)$arr;
//print_r($arr);
echo"<br><br><br>";
return $arr;

//return "hi vijay";
//die("model file");
    
}
}

//id
?>



