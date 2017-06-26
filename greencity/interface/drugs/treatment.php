<?php

 

$sanitize_all_escapes  = true;
$fake_register_globals = false;

 require_once("../globals.php");
 require_once("$srcdir/acl.inc");
 require_once("drugs.inc.php");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/formdata.inc.php");
 require_once("$srcdir/htmlspecialchars.inc.php");

 $alertmsg = '';
 $drug_id = $_REQUEST['drug'];
 $info_msg = "";
 $tmpl_line_no = 0;

 if (!acl_check('admin', 'drugs')) die(xlt('Not authorized'));


function bucks($amount) {
  if ($amount) {
    $amount = sprintf("%.2f", $amount);
    if ($amount != 0.00) return $amount;
  }
  return '';
}


?>
<html>
<head>
        
        <link rel="stylesheet" href="public/css/default.css" type="text/css">
        <link rel="stylesheet" href="datepicker/public/css/style.css" type="text/css">
		<link type="text/css" rel="stylesheet" href="datepicker/libraries/syntaxhighlighter/public/css/shCoreDefault.css">

<?php html_header_show(); ?>
<title><?php echo $drug_id ? xlt("Edit") : xlt("Add New"); echo ' ' . xlt('Record'); ?></title>
<link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>

<style>

 



input[class=rgt] { text-align:right }

td { font-size:10pt; }


input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
     text-align:right;
}


<?php if ($GLOBALS['sell_non_drug_products'] == 2) { ?>
.drugsonly { display:none; }
<?php } else { ?>
.drugsonly { }
<?php } ?>

<?php if (empty($GLOBALS['ippf_specific'])) { ?>
.ippfonly { display:none; }
<?php } else { ?>
.ippfonly { }
<?php } ?>




</style>



</head>

<body class="body_top">
<?php
// If we are saving, then save and close the window.
// First check for duplicates.




if (($_POST['form_save'] || $_POST['form_delete']) && !$alertmsg) {
  $new_drug = false;
 
   if ($_POST['form_save']) { // saving a new drug
 
			 
			
			 
			  
			  $j=0;
	foreach($_POST['com'] as $selected){
		
		
		 
		$date= $_POST['date'][$j];

		$hb= $_POST['hb'][$j];
		$pallor= $_POST['pallor'][$j];
		$weight= $_POST['weight'][$j];
		$bp= $_POST['bp'][$j];
		$oed= $_POST['oed'][$j];
		$PA= $_POST['PA'][$j];
		$pv= $_POST['pv'][$j];
		
		$exam= $_POST['exam'][$j];
		$advise= $_POST['advise'][$j];
		
	  
		
        if(empty($selected))
			continue;
	
	
	  
		
		 
		
			 $drug_id = sqlInsert("INSERT INTO form_vitals ( " .
    "date,complaint,bps,weight,pid,note" .
    
    ") VALUES ( " .
    "'" . $date       . "', " .
	 "'" . $selected       . "', " .
    "'" . $bp          . "', " .
    "'" . $weight          . "', " .
	  "'" . $_SESSION['pid']          . "', " .
	 "'" .$advise. "' " .
    
    
    ")");
	
	$j++;
		}  
		
	

	
  header('location:treatment.php');
	
  
	
  }
}


?>





<form method='post' name='theform' action=''>
<center>


	

 
 <br><br><br><br><br><br>
 
 <table  cellspacing="0" width='60%' border='1' align='left'   id="dataTable" style="border:1px solid black;width:40%;float:left"> 
 <tr>
  <th nowrape>S.No.</th>
  <th nowrap>Date</th>
  <th nowrap>Complaints</th>
  
   
   <th nowrap>Pallor</th>
    <th nowrap>Weight</th>
   <!--<th nowrap>New</br>Medicine</th>-->
   <th nowrap>BP</th>
   <th  nowrap>Oedema </br>Number</th>
 
  <th  nowrap>Examination Findings</th>
  <th  nowrap>Treatment And Advise </br>Type</th>
  
 </tr>
 
 
 
 <?php
 include_once('dbconnect.php');
 $pid=$_SESSION['pid'];
 $list = "SELECT * FROM `form_vitals` where pid='".$pid."'";
 $rid=mysqli_query($conn,$list);
 
  $num=mysqli_num_rows($rid);
 $j=1;
   while($result=mysqli_fetch_array($rid)) 
    
   {  
    
   ?>
	   <tr>
	   <td>
	    <?php echo $j; 
        $date = date_default_timezone_set('Asia/Kolkata');
         
		?>
	   </td>
	   
 

    <td><textarea name ='date1[]' rows="2" cols="15" value='' style="border:1px solid white; background-color:#DCDCDC;" readonly><?php echo $result['date'] ?></textarea></td>
  
  
    <td><textarea name='com1[]' rows="2" cols="25" style="border:1px solid white;background-color:#DCDCDC;" readonly></textarea></td>
  

  
  
  <td>
   <input type='text' size="10"  name='pallor1[]' maxlength='80' value=''  style="height: 50px; width:80px;background-color:#DCDCDC;border:1px solid white;"  readonly/>
  </td> 
   
  <td>
   <input type='text' size="10"  maxlength='80' name='weight1[]' value='<?php echo $result['weight']; ?>'  style="height: 50px; width:80px;background-color:#DCDCDC;border:1px solid white; " readonly />
  </td>
  
  <td>
   <input type='text' size="10"  maxlength='80' name='bp1[]' value='<?php echo $result['bps']; ?>' style="height: 50px; width:80px;background-color:#DCDCDC;border:1px solid white;" readonly/>
  </td>
  
   <td>
 <input type='text' size="10"  maxlength='80' name='ode1[]' style="height: 50px; width:80px; background-color:#DCDCDC;border:1px solid white;" readonly/>
 </td>
  


  
 
  
  
   <td><textarea name="exam1[]" rows="3" cols="25" style="border:1px solid black;background-color:#DCDCDC;border:1px solid white;" readonly></textarea></td>
  
  
   
  
    <td><textarea name="advise1[]" rows="3" cols="25" style="border:1px solid black;background-color:#DCDCDC;border:1px solid white;" readonly><?php echo $result['note']; ?></textarea></td>
  
	<td><a href="editrecord.php?id=<?php echo $result['id']; ?>" onclick="return confirm('Do You  Want To Edit This Record');">Edit</a></td>   
	   </tr>
	   
	   
<?php 
$j++;
  } 

 
 
 
 $i=$num+1;
  while($i<=$num+5) 
  {
  
     
?> 
 
 <tr>
 
   <td>
   <?php echo $i ?>
  </td> 
  
  
  <td><textarea name='date[]' rows="2" cols="15" value='' style="border:1px solid white;"><?php echo $today = date("Y-m-d H:i");?></textarea></td>
  
  
  
     <td><textarea name='com[]' rows="2" cols="25" value='' style="border:1px solid white;"></textarea></td>
  
     
  
  <td>
  <input type='text' size="10" name='pallor[]' maxlength='80' value=''  style="height: 50px; width:80px;border:1px solid white; "  />
  </td> 
  
  <td>
  <input type='text' size="10" name='weight[]' maxlength='80' value=''  style="height: 50px; width:80px;border:1px solid white; "  />
  </td>
  
  <td>
  <input type='text' size="10" name='bp[]' maxlength='80'   style="height: 50px; width:80px;border:1px solid white; "  />
  </td>
  
  <td>
 <input type='text' size="10" name='oed[]' maxlength='80'  style="height: 50px; width:80px;border:1px solid white; " />
 </td>
 
   <td><textarea name="exam[]" rows="3" cols="25"style="border:1px solid white;" ></textarea></td>
  
    <td><textarea name="advise[]" rows="3" cols="25" style="border:1px solid white;"></textarea></td>
  
  
  </tr>
  
  <?php
   

 $i++;  

         
}
 

?>
<tr>
<th colspan="9"> 
<input align="center" type='submit' name='form_save' value='<?php echo xla('Save'); ?>' />   </th>
</tr>




  
  

   </table>
   <table  cellspacing="0" width='40%' border='1' style="width:40%;float:left;margin-top:100px;">

<tr>
<td><b><u>Blood</u></b></td>     <td></td>     <td></td> <td></td><td></td><td></td><td></td>
</tr>


    
<tr>
  <td>Rubella<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td>Hb % igg<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td>BLD group (Rh)<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
  <td>RBS<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td>TSH<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
   <td>HIV<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
   <td>Urea<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
  
</tr>	


<tr>
   
  
 
   
   <td>V.D.R.L.<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
   <td>HBS Ag<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
  <td>Uric Acid<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
  <td>Creatinine<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td>Platelete Count<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
  <td>Hb A<sub>1</sub>C<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td></td>
</tr>

<tr>
   
  
   
  
  
</tr>


<tr>
<td><b><u>Urine</u></b></td>     <td></td>     <td></td> <td></td><td><b><u>Others</u></b></td><td></td><td></td>
</tr>

<tr>
   
  
  <td>Alb<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
  <td>Sug<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td>Mic<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td>BS,BP<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
   <td>TC<input type="text" name='' value='' size='2' style="border:1px solid white;"></td>
  <td>DC<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
  <td>ESR<input type="text" name='' value='' size='2' style="border:1px solid white;"></td> 
</tr>




 


 
</table>
 



</center>
</form>

       



 
		
		
		

  
   
    <style type="text/css">


.typeahead {
	background-color: #FFFFFF;
}
.typeahead:focus {
	border: 2px solid #0097CF;
}
.tt-query {
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
}
.tt-hint {
	color: #999999;
}
.tt-dropdown-menu {
	background-color: #FFFFFF;
	border: 1px solid rgba(0, 0, 0, 0.2);
	border-radius: 8px;
	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
	margin-top: 12px;
	padding: 8px 0;
	width: 422px;
}
.tt-suggestion {
	font-size: 24px;
	line-height: 24px;
	padding: 3px 20px;
}
.tt-suggestion.tt-is-under-cursor {
	background-color: #0097CF;
	color: #FFFFFF;
}
.tt-suggestion p {
	margin: 0;
}



</style>		
		
		



<div width="100%">		
<script language="JavaScript">



<?php
 if ($alertmsg) {
  echo "alert('" . htmlentities($alertmsg) . "');\n";
 }
?>
</script>
</div>
</body>
</html>
