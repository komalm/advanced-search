<?php defined('_JEXEC') or die('Restricted access'); 

//print_r($this->item);
$data=$this->item;

//print_r($data);
//die();
//echo $this->searchlist;

//echo"$arr";

//echo $hello; 

echo"<br><br>";
//echo"$arr1";
echo"<br><br>";

echo'<br></br>';
//print_r($arr);
//echo"$arr";

?>

<table border='3'>
<tr>
    <th>Search Name</th>
    <th>Run</th>
      <th>Delete</th>
</tr>

<?php
foreach($data as $li)
{
echo"<tr>";
echo"<td>";
//echo"<br>";
echo"$li->name";

echo"</td>";
echo"<td>";
$id=$li->id;
//echo"$id";
//$query = "DELETE FROM `paai_advanced_search_saved_searches` WHERE id=$li->id";
echo"<a href='http://google.com'>Run </a>";
//echo"<a href="."index.php?option=com_advsearch&view=searchlist&task=run&id=$li->id>Delete </a>";
?>

<?echo"</td>";
echo"<td>";
echo"<a href="."index.php?option=com_advsearch&view=searchlist&task=delete&id=$li->id>Delete </a>";
//
//echo"<br>";
//echo"$li->address";
echo"</td>";
echo"</tr>";
}
die("");
?>

</table>


