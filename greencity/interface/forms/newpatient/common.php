<?php
/**
 * Common script for the encounter form (new and view) scripts.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://www.open-emr.org
 */

require_once("$srcdir/options.inc.php");

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14",
  "15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
$thisyear = date("Y");
$years = array($thisyear-1, $thisyear, $thisyear+1, $thisyear+2);

if ($viewmode) {
  $id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : '';
  $result = sqlQuery("SELECT * FROM form_encounter WHERE id = ?", array($id));
  $encounter = $result['encounter'];
  if ($result['sensitivity'] && !acl_check('sensitivities', $result['sensitivity'])) {
    echo "<body>\n<html>\n";
    echo "<p>" . xlt('You are not authorized to see this visit.') . "</p>\n";
    echo "</body>\n</html>\n";
    exit();
  }
}

// Sort comparison for sensitivities by their order attribute.
function sensitivity_compare($a, $b) {
  return ($a[2] < $b[2]) ? -1 : 1;
}

// get issues
$ires = sqlStatement("SELECT id, type, title, begdate FROM lists WHERE " .
  "pid = ? AND enddate IS NULL " .
  "ORDER BY type, begdate", array($pid));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php html_header_show();?>
<title><?php echo xlt('Patient Visit'); ?></title>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/js/jAlert-master/src/jAlert-v3.css" />
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.treeview-1.4.1/jquery.treeview.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
 <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.7.2.min.js"></script> 
<script src="<?php echo $GLOBALS['webroot'] ?>/library/js/jAlert-master/src/jAlert-v3.js"></script>
<script src="<?php echo $GLOBALS['webroot'] ?>/library/js/jAlert-master/src/jAlert-functions.js"> //optional!!</script>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" media="screen" />
<!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.4.3.min.js"></script>-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/overlib_mini.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>

<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/ajax/facility_ajax_jav.inc.php"); ?>
<script language="JavaScript">

 var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

 // Process click on issue title.
 function newissue() {
  dlgopen('../../patient_file/summary/add_edit_issue.php', '_blank', 800, 600);
  return false;
 }

 // callback from add_edit_issue.php:
 function refreshIssue(issue, title) {
  var s = document.forms[0]['issues[]'];
  s.options[s.options.length] = new Option(title, issue, true, true);
 }

 function saveClicked() {
  var f = document.forms[0];

<?php if (!$GLOBALS['athletic_team']) { ?>
  var category = document.forms[0].pc_catid.value;
  if ( category == '_blank' ) {
   alert("<?php echo xls('You must select a visit category'); ?>");
   return;
  }
<?php } ?>

  top.restoreSession();
  f.submit();
 }

$(document).ready(function(){
  enable_big_modals();
});
function bill_loc(){
var pid=<?php echo attr($pid);?>;
var dte=document.getElementById('form_date').value;
var facility=document.forms[0].facility_id.value;
ajax_bill_loc(pid,dte,facility);
}

// Handler for Cancel clicked when creating a new encounter.
// Show demographics or encounters list depending on what frame we're in.
function cancelClicked() {
 if (window.name == 'RBot') {
  parent.left_nav.setRadio(window.name, 'ens');
  parent.left_nav.loadFrame('ens1', window.name, 'patient_file/history/encounters.php');
 }
 else {
  parent.left_nav.setRadio(window.name, 'dem');
  parent.left_nav.loadFrame('dem1', window.name, 'patient_file/summary/demographics.php');
 }
 return false;
}
function getRatePlan(plan)
{
	var plan= document.getElementById('rateplan').value;
	if(plan=="TPA Insurance")
	{
		document.getElementById('instpa').style.display = '';
	}
	else
	{
		document.getElementById('instpa').style.display = 'none';
	}
	
}
</script>
</head>

<?php if ($viewmode) { ?>
<body class="body_top">
<?php } else { ?>
<body class="body_top" onload="javascript:document.new_encounter.reason.focus();">
<?php } ?>

<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<form method='post' action="<?php echo $rootdir ?>/forms/newpatient/save.php" name='new_encounter'
 <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?>>

<div style = 'float:left'>
<?php if ($viewmode) { ?>
<input type=hidden name='mode' value='update'>
<input type=hidden name='id' value='<?php echo (isset($_GET["id"])) ? attr($_GET["id"]) : '' ?>'>
<span class=title><?php echo xlt('Patient Visit Form'); ?></span>
<?php } else { ?>
<input type='hidden' name='mode' value='new'>
<h1 style="margin: 0;
    font-size: 24px;">
        <?php echo xlt('New Visit Form'); ?>
        
      </h1>
<?php } ?>
</div>

<table width='96%'>

 <tr>
  <td width='33%' nowrap class='bold' style="display: none"><?php echo xlt('Consultation Brief Description'); ?>:</td>
  <td width='34%' rowspan='2' align='center' valign='center' class='text'>
   <table>

    <tr<?php if ($GLOBALS['athletic_team']) echo " style='visibility:hidden;'"; ?>>
     <td class='bold' nowrap><?php echo xlt('Visit Category:'); ?></td>
     <td class='text'>
      <select name='pc_catid' id='pc_catid'>
	<option value='_blank'>-- <?php echo xlt('Select One'); ?> --</option>
<?php
 $cres = sqlStatement("SELECT pc_catid, pc_catname " .
  "FROM openemr_postcalendar_categories ORDER BY pc_catname");
 while ($crow = sqlFetchArray($cres)) {
  $catid = $crow['pc_catid'];
  if ($catid < 9 && $catid != 5) continue;
  echo "       <option value='" . attr($catid) . "'";
  if($result['pc_catid']=='')
  {
	  if($crow['pc_catid'] == 10) echo "selected";
  }else
  {
  if ($viewmode && $crow['pc_catid'] == $result['pc_catid']) echo " selected";
  }
  echo ">" . text(xl_appt_category($crow['pc_catname'])) . "</option>\n";
 }
?>
      </select>
     </td>
    </tr>
	


	<tr style="visibility:hidden">
     <td class='bold' nowrap><?php echo xlt('Package:'); ?></td>
	 <td class='text'>
  
<?php


  $ures = sqlStatement("select * from procedure_type where parent=283");
   echo "<select name='package' style='width:100%' />  <option value='0'></option>";
    while ($urow = sqlFetchArray($ures)) {
      echo "    <option value='" . attr($urow['name']) . "'";
     if ($urow['id'] == $defaultProvider);
      echo ">" . text($urow['name']);
    
      echo "</option>\n";
    }
    echo "</select>";
?>
     </td>
	</tr>
	
	
	
	<tr style="visibility:hidden;position:absolute;opacity:0">
     <td class='bold' nowrap><?php echo xlt('Rate Plan:'); ?></td>
	 <td class='text'>
  
<?php
//get default insurance data
	$getdefins = sqlStatement("select provider,name from insurance_data a, insurance_companies b where pid='$pid' and a.provider = b.id");
	$getins = sqlFetchArray($getdefins);
  $ures = sqlStatement("select * from list_options where list_id='RatePlan' ");
   echo "<select name='rateplan' id='rateplan' style='width:100%' onChange='getRatePlan(this.value)' />";
   
    while ($urow = sqlFetchArray($ures)) {
	  /*if($getins['provider']>0  & $urow['title']=="TPA Insurance") { 
	  echo "<option selected value='" . attr($urow['title']) . "'";
      echo ">" . text($urow['title']);
	  echo "</option>\n";
	  }	*/
	/*else
	{*/
      echo "    <option value='" . attr($urow['title']) . "'";
      //if ($urow['id'] == $defaultProvider) echo " selected";
      echo ">" . text($urow['title']);
    
      echo "</option>\n";
	/*}*/
	}		
    echo "</select>";
?>
     </td>
	</tr>
	

   <tr style='display:none' id='instpa'>
     <td class='bold' nowrap><?php echo xlt('TPA:'); ?></td>
	 <td class='text'>
  
<?php

	$ures = sqlStatement("select distinct name, id from insurance_companies ");
	echo "<select name='instpa'/>";
	 if($getins['provider']>0){
     echo "<option value='".attr($getins['provider'])."'";
	 echo ">" . attr($getins['name']);
	 echo "</option>";
	 }
	 else
	 {
		  echo "<option value='0'>";
		  echo "</option>";
		 
	 }
    while ($urow = sqlFetchArray($ures)) {
		if ($getins['provider']!=$urow['id']){
      echo "    <option value='" . attr($urow['id']) . "'";
      //if ($urow['id'] == $defaultProvider) echo " selected";
      echo ">" . text($urow['name']);
    
      echo "</option>\n";
		}
    }
    echo "</select>";
	?>
	</td>
	</tr>

	
	<tr>
     <td class='bold' nowrap><?php echo xlt('Doctor:'); ?></td>
     <td class='text'>
  
<?php
  $ures = sqlStatement("SELECT id, username, fname, lname FROM users WHERE " .
  "authorized != 0 AND active = 1 ORDER BY lname, fname");
   echo "<select name='form_provider'  onchange='getval(this);' style='width:100%' /><option>Choose A Doctor</option>";
    while ($urow = sqlFetchArray($ures)) {
      echo "    <option value='" . attr($urow['id']) . "'";
      if ($urow['id'] == $defaultProvider) echo " selected";
      echo ">" . "Dr. ".text($urow['fname']);
      if ($urow['lname']) echo " " . text($urow['lname']);
      echo "</option>\n";
    }
    echo "</select>";
?>
     </td>
    </tr>

    <tr  style="visibility:hidden;position:absolute;opacity:0">
     <td class='bold' nowrap><?php echo xlt('Facility:'); ?></td>
     <td class='text'>
      <select name='facility_id' onChange="bill_loc()">
<?php

if ($viewmode) {
  $def_facility = $result['facility_id'];
} else {
  $dres = sqlStatement("select facility_id from users where username = ?", array($_SESSION['authUser']));
  $drow = sqlFetchArray($dres);
  $def_facility = $drow['facility_id'];
}
$fres = sqlStatement("select * from facility where service_location != 0 order by name");
if ($fres) {
  $fresult = array();
  for ($iter = 0; $frow = sqlFetchArray($fres); $iter++)
    $fresult[$iter] = $frow;
  foreach($fresult as $iter) {
?>
       <option value="<?php echo attr($iter['id']); ?>" <?php if ($def_facility == $iter['id']) echo "selected";?>><?php echo text($iter['name']); ?></option>
<?php
  }
 }
?>
      </select>
     </td>
    </tr>
	<tr  style="visibility:hidden;position:absolute;opacity:0">
		<td class='bold' nowrap><?php echo xlt('Billing Facility'); ?>:</td>
		<td class='text'>
			<div id="ajaxdiv">
			<?php
			billing_facility('billing_facility',$result['billing_facility']);
			?>
			</div>
		</td>
     </tr>
    <tr  style="visibility:hidden;position:absolute;opacity:0">
<?php
 $sensitivities = acl_get_sensitivities();
 if ($sensitivities && count($sensitivities)) {
  usort($sensitivities, "sensitivity_compare");
?>
     <td class='bold' nowrap><?php echo xlt('Sensitivity:'); ?></td>
     <td class='text'>
      <select name='form_sensitivity'>
<?php
  foreach ($sensitivities as $value) {
   // Omit sensitivities to which this user does not have access.
   if (acl_check('sensitivities', $value[1])) {
    echo "       <option value='" . attr($value[1]) . "'";
    if ($viewmode && $result['sensitivity'] == $value[1]) echo " selected";
    echo ">" . xlt($value[3]) . "</option>\n";
   }
  }
  echo "       <option value=''";
  if ($viewmode && !$result['sensitivity']) echo " selected";
  echo ">" . xlt('None'). "</option>\n";
?>
      </select>
     </td>
<?php
 } else {
?>
     <td colspan='2'><!-- sensitivities not used --></td>
<?php
 }
?>
    </tr>

    <tr<?php if (!$GLOBALS['gbl_visit_referral_source']) echo " style='visibility:hidden;'"; ?>>
     <td class='bold' nowrap><?php echo xlt('Referred By'); ?>:</td>
     <td class='text' id="select_dr">

<?php
  $ures = sqlStatement("SELECT id,doctor_name FROM referral_doctor");
   echo "<select name='form_referral_source' id='form_referral_source' style='width:100%' /><option>Choose referral doctor</option>";
    while ($urow = sqlFetchArray($ures)) {
      echo "    <option value='" . text($urow['doctor_name']) . "'";
      if ($urow['id'] == $defaultProvider) echo " selected";
      echo ">".text($urow['doctor_name']);
      echo "</option>\n";
    }
    echo "</select>";
?>
     </td>

	 <td class='text' id="input_dr" style="display:none">
	 <input type="text" name="refsource" style="width: 100%">
	 </td>
	 <td><a href="#" id="toggle_doc" title="Doctor Not listed? Add Doctor"><i class="fa fa-plus-circle"></i></a><td>
    </tr>

    <tr style="visibility:hidden;position:absolute;opacity:0">
     <td class='bold' nowrap><?php echo xlt('Date of Service:'); ?></td>
     <td class='text' nowrap>
      <input type='text' size='10' name='form_date' id='form_date' <?php echo $disabled ?>
       value='<?php echo $viewmode ? substr($result['date'], 0, 10) : date('Y-m-d'); ?>'
       title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
        <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
        id='img_form_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
        title='<?php echo xla('Click here to choose a date'); ?>'>
     </td>
    </tr>

    <tr<?php if ($GLOBALS['ippf_specific'] || $GLOBALS['athletic_team']) echo " style='visibility:hidden;'"; ?>   style="visibility:hidden">
     <td class='bold' nowrap><?php echo xlt('Onset/hosp. date:'); ?></td>
     <td class='text' nowrap><!-- default is blank so that while generating claim the date is blank. -->
      <input type='text' size='10' name='form_onset_date' id='form_onset_date'
       value='<?php echo $viewmode && $result['onset_date']!='0000-00-00 00:00:00' ? substr($result['onset_date'], 0, 10) : ''; ?>' 
       title='<?php echo xla('yyyy-mm-dd Date of onset or hospitalization'); ?>'
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
        <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
        id='img_form_onset_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
        title='<?php echo xla('Click here to choose a date'); ?>'>
     </td>
    </tr>

    <tr style="visibility:hidden;position:absolute;opacity:0">
     <td class='text' colspan='2' style='padding-top:1em'>
<?php if ($GLOBALS['athletic_team']) { ?>
      <p><i>Click [Add Issue] to add a new issue if:<br />
      New injury likely to miss &gt; 1 day<br />
      New significant illness/medical<br />
      New allergy - only if nil exist</i></p>
<?php } ?>
     </td>
    </tr>
	    <tr class="docspecific" style="display:none">
     <td class='bold' nowrap><?php echo xlt('Clinical Features:'); ?></td>
     <td class='text'>
      <input type="text" placeholder="temperature in °F(Ex: 98)" name="temp"> °F
	  <input type="text" placeholder="Weight in Kgs (Ex: 65)" name="weight"> Kgs
	  <input type="text" placeholder="Height in inches (Ex: 6.5)" name="height"> Inches
	  
     </td>
    </tr>
    <tr class="docspecific" style="display:none">
	 <td class='bold' nowrap><?php echo xlt('Review after:'); ?></td>
     <td class='text' >
<input type="text" name="review_after" placeholder="Enter in multiples of Days"> Days
     </td>
    </tr>
   </table>

  </td>

  <td class='bold' width='33%' style="display:none" nowrap >
    <div style='float:left'>
   <?php echo xlt('Issues (Injuries/Medical/Allergy)'); ?>
    </div>
    <div style='float:left;margin-left:8px;margin-top:-3px'>
<?php if ($GLOBALS['athletic_team']) { // they want the old-style popup window ?>
      <a href="#" class="css_button_small link_submit"
       onclick="return newissue()"><span><?php echo xlt('Add'); ?></span></a>
<?php } else { ?>
      <a href="../../patient_file/summary/add_edit_issue.php" class="css_button_small link_submit iframe"
       onclick="top.restoreSession()"><span><?php echo xlt('Add'); ?></span></a>
<?php } ?>
    </div>
  </td>
 </tr>

 <tr>
  <td class='text' valign='top'style="visibility:hidden;position:absolute;opacity:0">
   <textarea name='reason' cols='40' rows='12' wrap='virtual' style='width:96%'
    ><?php echo $viewmode ? text($result['reason']) : text($GLOBALS['default_chief_complaint']); ?></textarea>
  </td>
  <td class='text' valign='top' style="display:none">
   <select multiple name='issues[]' size='8' style='width:100%'
    title='<?php echo xla('Hold down [Ctrl] for multiple selections or to unselect'); ?>'>
<?php
while ($irow = sqlFetchArray($ires)) {
  $list_id = $irow['id'];
  $tcode = $irow['type'];
  if ($ISSUE_TYPES[$tcode]) $tcode = $ISSUE_TYPES[$tcode][2];
  echo "    <option value='" . attr($list_id) . "'";
  if ($viewmode) {
    $perow = sqlQuery("SELECT count(*) AS count FROM issue_encounter WHERE " .
      "pid = ? AND encounter = ? AND list_id = ?", array($pid,$encounter,$list_id));
    if ($perow['count']) echo " selected";
  }
  else {
    // For new encounters the invoker may pass an issue ID.
    if (!empty($_REQUEST['issue']) && $_REQUEST['issue'] == $list_id) echo " selected";
  }
  echo ">" . text($tcode) . ": " . text($irow['begdate']) . " " .
    text(substr($irow['title'], 0, 40)) . "</option>\n";
}
?>
   </select>

   <p><i><?php echo xlt('To link this encounter/consult to an existing issue, click the '
   . 'desired issue above to highlight it and then click [Save]. '
   . 'Hold down [Ctrl] button to select multiple issues.'); ?></i></p>

  </td>
 </tr>

</table>
<div>
    <div style = 'float:left; margin-left:8px;margin-top:-3px'>
      <a href="javascript:saveClicked();" class="css_button link_submit"><span><?php echo xlt('Save'); ?></span></a>
      <?php if ($viewmode || !isset($_GET["autoloaded"]) || $_GET["autoloaded"] != "1") { ?>
    </div>

    <div style = 'float:left; margin-top:-3px'>
  <?php if ($GLOBALS['concurrent_layout']) { ?>
      <a href="<?php echo "$rootdir/patient_file/encounter/encounter_top.php"; ?>"
        class="css_button link_submit" onClick="top.restoreSession()"><span><?php echo xlt('Cancel'); ?></span></a>
  <?php } else { ?>
      <a href="<?php echo "$rootdir/patient_file/encounter/patient_encounter.php"; ?>"
        class="css_button link_submit" target='Main' onClick="top.restoreSession()">
      <span><?php echo xlt('Cancel'); ?>]</span></a>
  <?php } // end not concurrent layout ?>
  <?php } else if ($GLOBALS['concurrent_layout']) { // not $viewmode ?>
      <a href="" class="css_button link_submit" onClick="return cancelClicked()">
      <span><?php echo xlt('Cancel'); ?></span></a>
  <?php } // end not $viewmode ?>
    </div>
 </div>

</form>

</body>
<script type="text/javascript">
$( document ).ready(function() {
$('#toggle_doc').on('click', function() {

	$(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');
	$('#select_dr, #input_dr').toggle();

   });

   });
</script>
<script language="javascript">
/* required for popup calendar */
Calendar.setup({inputField:"form_date", ifFormat:"%Y-%m-%d", button:"img_form_date"});
Calendar.setup({inputField:"form_onset_date", ifFormat:"%Y-%m-%d", button:"img_form_onset_date"});
<?php
if (!$viewmode) { ?>
 function duplicateVisit(enc, datestr) {
     $.jAlert({'type': 'confirm', 'confirmQuestion': 'A visit already exists for this patient today. Click NO to open it, or YES to proceed with creating a new one.','!onConfirm': function(){
       
  }, 'onDeny': function(){
    top.restoreSession();
            parent.left_nav.setEncounter(datestr, enc, window.name);
            parent.left_nav.setRadio(window.name, 'enc');
            parent.left_nav.loadFrame('enc2', window.name, 'patient_file/encounter/encounter_top.php?set_encounter=' + enc);
            return;     
  } });
   /* if (!confirm('<?php echo xl("A visit already exists for this patient today. Click Cancel to open it, or OK to proceed with creating a new one.") ?>')) {
            // User pressed the cancel button, so re-direct to today's encounter
            top.restoreSession();
            parent.left_nav.setEncounter(datestr, enc, window.name);
            parent.left_nav.setRadio(window.name, 'enc');
            parent.left_nav.loadFrame('enc2', window.name, 'patient_file/encounter/encounter_top.php?set_encounter=' + enc);
            return;
        }*/
        // otherwise just continue normally
    }    
<?php

  // Search for an encounter from today
  $erow = sqlQuery("SELECT fe.encounter, fe.date " .
    "FROM form_encounter AS fe, forms AS f WHERE " .
    "fe.pid = ? " . 
    " AND fe.date >= ? " . 
    " AND fe.date <= ? " .
    " AND " .
    "f.formdir = 'newpatient' AND f.form_id = fe.id AND f.deleted = 0 " .
    "ORDER BY fe.encounter DESC LIMIT 1",array($pid,date('Y-m-d 00:00:00'),date('Y-m-d 23:59:59')));

  if (!empty($erow['encounter'])) {
    // If there is an encounter from today then present the duplicate visit dialog
    echo "duplicateVisit('" . $erow['encounter'] . "', '" .
      oeFormatShortDate(substr($erow['date'], 0, 10)) . "');\n";
  }
}
?>
</script>
<script type="text/javascript">
function getval(sel)
{
    if(sel.value == 13)   {
	 $(".docspecific").css("display","block");
	} else{
                $(".docspecific").css("display","none");
            }
}
$(document).ready(function(){
function getval(doc){
	alert(doc);
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if(optionValue){
                $(".box").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else{
                $(".box").hide();
            }
        });
    }
});
</script>
</html>
