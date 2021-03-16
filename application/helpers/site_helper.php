
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//datetime only
function site_datetimeformat($formatdate)
{
	$CI =& get_instance();
	if(empty($formatdate)) {
		return $formatdate;
	}

	if($formatdate == '0000-00-00 00:00:00' ||  $formatdate == '0000-00-00') {
		return '';
	}

	//for date orderimport columns
	if(isValiddate_format((string)$formatdate, 'm/d/Y')) {
		return $formatdate;
	}


	return !empty($formatdate) && $formatdate != '0000-00-00 00:00:00' && $formatdate != '0000-00-00' ? date($CI->config->item('date_format'),strtotime($formatdate)) : '';
}

//date only
function site_dateformat($formatdate)
{
	$CI =& get_instance();
	if(empty($formatdate)) {
		return $formatdate;
	}

	if($formatdate == '0000-00-00' ||  $formatdate == '0000-00-00') {
		return '';
	}

	//for date orderimport columns
	if(isValiddate_format((string)$formatdate, 'm/d/Y')) {
		return $formatdate;
	}


	return !empty($formatdate) && $formatdate != '0000-00-00' && $formatdate != '0000-00-00' ? date('m/d/Y',strtotime($formatdate)) : '';
}

/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
/** @date Tuesday 17 March 2020 **/
/** @description Calculate DueDateTime using SLA for partcular workflow **/
function calculate_workflowduedatetime($OrderUID,$WorkflowModuleUID)
{
	$CI =& get_instance();
	$PriorityTime = $CI->Common_Model->get_workflowprioritytime($OrderUID,$WorkflowModuleUID);
	if(empty($PriorityTime)) {
		return false;
	}
	return !empty($PriorityTime) ? date('Y-m-d H:i:s', strtotime('+' . $PriorityTime . ' Hours')) : NULL;
}

//date only
function site_datetimeaginginhours($datetime)
{
	if (!empty($datetime) && $datetime != '0000-00-00 00:00:00' && $datetime != '0000-00-00') {
		$hour1 = 0; $hour2 = 0;
		$datetimeObj1 = new DateTime($datetime);
		$datetimeObj2 = new DateTime('now');
		$interval = $datetimeObj1->diff($datetimeObj2);

		if($interval->format('%a') > 0){
			$hour1 = $interval->format('%a')*24;
		}
		if($interval->format('%h') > 0){
			$hour2 = $interval->format('%h');
		}

		return ($hour1 + $hour2);
	} else {
		return '';
	}
}

//date only
function site_datetimeaging($datetime)
{
	$CI =& get_instance();

	return !empty($datetime) && $datetime != '0000-00-00 00:00:00' && $datetime != '0000-00-00' ?  modifiedtimespan(strtotime($datetime), time()) : '';
}

if ( ! function_exists('modifiedtimespan'))
{
	/**
	 * Modified Timespan
	 *
	 * Returns a span of seconds in this format:
	 *	10 days 14 hours 36 minutes 47 seconds
	 *
	 * @param	int	a number of seconds
	 * @param	int	Unix timestamp
	 * @param	int	a number of display units
	 * @return	string
	 */
	function modifiedtimespan($seconds = 1, $time = '')
	{
		$CI =& get_instance();
		$CI->lang->load('date');

		is_numeric($seconds) OR $seconds = 1;
		is_numeric($time) OR $time = time();

		$seconds = ($time <= $seconds) ? 1 : $time - $seconds;

		$days = floor($seconds / 86400);

		if (empty($str) && $days > 0)
		{

			$str = $days;

			$seconds -= $days * 86400;
		}

		if(empty($str)) {
			$str = '0';
		}

		return $str;
	}
}


/**
*Function change hour to days
*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
*@since Thursday 02 April 2020
*/
function hourstodays($hours)
{
	$hid = 24; // Hours in a day - could be 24, 8, etc
	$days = round($hours/$hid);

	if( $days <= 0 )
	{
		echo ($hours != "") ? "$hours Hours" : NULL;
	}
	else
	{
		echo ($days != "") ? "$days Days" : NULL;
	}
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function alphabetsonly($string)
{
	return  preg_replace( '/[\W]/', '', $string);
}

function alphanumericonly($string)
{
	return  preg_replace( '/[^A-Za-z0-9?!]/', '', $string);
}

function isValiddate_format(string $date, string $format = 'Y-m-d'): bool
{
	$dateObj = DateTime::createFromFormat($format, $date);
	return $dateObj && $dateObj->format($format) == $date;
}


/**
*Function Calculate DueDate Based on working hours
*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
*@since Saturday 27 June 2020 IST
*/

//check function is defined
if(!function_exists('get_businesshourtat_duedate')) {
	function get_businesshourtat_duedate($Queue,$StartDateTime) {
		$CI =& get_instance();
		$CI->load->database();

		$SkipWeekends = true;
		$dayStart =  '00:00:00';
		$dayEnd = '24:00:00';

		if($Queue->SkipWeekend == 0) {
			$SkipWeekends = false;
		}
		
		if($Queue->IsBusinessHours == 1) {
			$dayStart =  !empty($Queue->dayStart) ? $Queue->dayStart : '00:00:00';
			$dayEnd =  !empty($Queue->dayEnd) ? $Queue->dayEnd : '24:00:00';
		}

		$holidays = [];

		$holidayarray = $CI->Common_Model->GetHolidays();
		if(!empty($holidayarray)) {
			$holidays = array_column($holidayarray, 'HolidayDate');
		}

		$DateTimeobj = calculate_WorkingHours($StartDateTime, (int)$Queue->FollowupDuration, $dayStart, $dayEnd,$holidays,$SkipWeekends);
		return $DateTimeobj->format('Y-m-d H:i:s');
	}
}

/**
*Function Calculate DueDate Based on working hours
*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
*@since Monday 13 April 2020
*/

//check function is defined
if(!function_exists('calculate_WorkingHours')) {
	function calculate_WorkingHours($givenDate, $addtime, $dayStart, $dayEnd,$holidays, $SkipWeekends) {
		//Break the working day start and end times into hours, minuets
		$dayStart = explode(':', $dayStart);
		$dayEnd = explode(':', $dayEnd);

		//fetch customer delay



		//Create required datetime objects and hours interval
		$datetime = new DateTime($givenDate);
		$datetime->modify("-1 second");
		$startofday = clone $datetime;
		$startofday->setTime($dayStart[0], $dayStart[1]); //set start of working day time
		$endofday = clone $datetime;
		$endofday->setTime($dayEnd[0], $dayEnd[1]); //set end of working day time


		$interval = 'PT'.$addtime.'H';

		//if initial date is before the start of working day
		if($datetime < $startofday) {
			//reset to start of working hours
			$datetime = $startofday;
		}  		

		//Add hours onto initial given date
		$datetime->add(new DateInterval($interval));


		//if initial date + hours is after the end of working day
		if($datetime > $endofday)
		{	
			//get the difference between the initial date + interval and the end of working day in seconds
			$seconds = $datetime->getTimestamp()- $endofday->getTimestamp();
			//Loop to next day
			while(true)
			{

				if(in_array($endofday->format('Y-m-d'), $holidays))
				{
					$endofday->add(new DateInterval('PT24H'));//Loop to next day by adding 24hrs
					continue;
				}

				if(in_array($endofday->format('l'), array('Sunday','Saturday')) && $SkipWeekends)
				{
					$endofday->add(new DateInterval('PT24H'));//Loop to next day by adding 24hrs
					continue;
				}

				$endofday->add(new DateInterval('PT24H'));//Loop to next day by adding 24hrs
				$nextDay = $endofday->setTime($dayStart[0], $dayStart[1]);//Set day to working day start time
				//If the next day is on a weekend and the week day only param is true continue to add days
				if(in_array($nextDay->format('Y-m-d'), $holidays))
				{
					continue;
				}
				else if(in_array($nextDay->format('l'), array('Sunday','Saturday')) && $SkipWeekends)
				{
					continue;
				}
				else //If not a weekend
				{
					$tmpDate = clone $nextDay;
					$tmpDate->setTime($dayEnd[0], $dayEnd[1]);//clone the next day and set time to working day end time
					$nextDay->add(new DateInterval('PT'.$seconds.'S')); //add the seconds onto the next day

					//if the next day time is later than the end of the working day continue loop
					if($nextDay > $tmpDate)
					{
						$seconds = $nextDay->getTimestamp()-$tmpDate->getTimestamp();
						$endofday = clone $tmpDate;
						$endofday->setTime($dayStart[0], $dayStart[1]);

					}
					else //else return the new date.
					{
						return $endofday;


					}
				}
			}
		}

		return $datetime;
	}
}

/**
* Function Menu Name Generation 
* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
* @since Thursday 16 July 2020.
*/
if(!function_exists('initial_name_generator')) {

	function initial_name_generator(string $name) : string
	{
		$words = explode(' ', $name);
		if (count($words) >= 2) {
			return strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
		}
		return makeInitialsFromSingleWord($name);
	}

}

/**
* Make initials from a word with no spaces
*
* @param string $name
* @return string
* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
* @since Thursday 16 July 2020.
*/
if(!function_exists('makeInitialsFromSingleWord')) {

	function makeInitialsFromSingleWord(string $name) : string
	{
		preg_match_all('#([A-Z]+)#', $name, $capitals);
		if (count($capitals[1]) >= 2) {
			return substr(implode('', $capitals[1]), 0, 2);
		}
		return strtoupper(substr($name, 0, 2));
	}

}

/**
* Function get remaining days left
* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
* @since Thursday 16 July 2020.
*/
function get_remainingdaysleft($futuredate, $conditions = [])
{
	$futurestrtime = strtotime($futuredate);
	$now = time();

	//check $futuredate is past date 
	if ($futurestrtime < $now && !isset($conditions['Ignore'])) {
		return 'E';
	}

	//$futuredate is future date 
	$timeleft = $futurestrtime-$now;
	$daysleft = round((($timeleft/24)/60)/60);

	if($daysleft >= $conditions['ExpiryOrdersAging']) {
		return '';
	}

	if($daysleft == 0 && !isset($conditions['Ignore'])) {
		return 'ET';
	} elseif ($daysleft == 0 && isset($conditions['Ignore'])) {
		return '0';
	}

	return $daysleft;
}

/**
* Function get remaining days saturday & sunday
* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
* @since Thursday 16 July 2020.
*/
function get_weekremainingdaysleft($monthtext)
{
	$lastmonth = date("M", strtotime("last month"));
	$currentmonth = date("M", strtotime("m"));
	$montharray = explode(',', $monthtext);
	if( in_array( $lastmonth ,$montharray ) && !in_array( $currentmonth ,$montharray ) )
	{
		return 'E';
	}

	return false;
}

/**
* Function check date has time
* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
* @since Thursday 30 July 2020.
*/
function isTime($time) {
	if (preg_match("/^([1-2][0-3]|[01]?[1-9]):([0-5]?[0-9]):([0-5]?[0-9])$/", $time))
		return true;
	return false;
}

/**
* Function User & Role Permissions for logged user
* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
* @since Thursday 30 July 2020.
*/

function get_user_permissions()
{
	$CI =& get_instance();
	$UserID = $CI->session->userdata('UserUID');
	$CI->db->select('mUsers.RoleUID,mRole.RoleTypeUID,mUsers.UserName,mUsers.Avatar,mUsers.ProfileColor,mUsers.ProfileBackground,mUsers.SidebarBackground,mUsers.SidebarActive,mUsers.SidebarBackgroundActive,mRole.IsAssigned,mRole.OrderQueue,mRole.IsReverseEnabled,mRole.IsSelfAssignEnabled,mRole.IsLockExpirationRestricted')->from('mRole')->join('mUsers', 'mUsers.RoleUID = mRole.RoleUID')->where('mUsers.UserUID', $UserID);
	return $CI->db->get()->row();
}


/**
* Function User & Role Permissions for logged user
* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
* @since Monday 10 August 2020.
*/

function submissionqueueconditions()
{
	//submissions queue condition
	$CI =& get_instance();
	$workupcompletequeues = $CI->config->item('RaiseSubmissionsParkingQueue');
	$DependentWorkflowUIDQuery = FALSE;
	$workupcompletequeuessql = FALSE;
	$len = count($workupcompletequeues);


	foreach ($workupcompletequeues as $workflowqueuename => $workupcompletequeueUID) {

		//subqueues complete check
		$workupcompletequeuessql .= " OR EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = suborder.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = suborder.CustomerUID AND mQueues.WorkflowModuleUID = ".$CI->config->item('Workflows')['Workup']." AND mQueues.QueueUID = ".$workupcompletequeueUID." )";

	}

	//workup completed workflow
	$DependentWorkflowUIDQuery .= " AND (EXISTS(SELECT TOA_TW.OrderUID FROM tOrderAssignments TOA_TW WHERE TOA_TW.OrderUID = suborder.OrderUID AND TOA_TW.WorkflowModuleUID = ".$CI->config->item('Workflows')['Workup']." AND TOA_TW.WorkflowStatus = 5) ".$workupcompletequeuessql." ) ";

	
	//gatekeeping completed workflow
	$DependentWorkflowUIDQuery .= " AND EXISTS (SELECT TOA_GK.OrderUID FROM tOrderAssignments TOA_GK where TOA_GK.OrderUID = suborder.OrderUID AND TOA_GK.WorkflowModuleUID = ".$CI->config->item('Workflows')['GateKeeping']." AND TOA_GK.WorkflowStatus = 5)";

	return $DependentWorkflowUIDQuery;

}

/**
  *Function check table exists
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Friday 14 August 2020.
  */
/**
 * Determine if a particular table exists
 *
 * @param	string	$table_name
 * @return	bool
 */
function is_mysql_table_exists($table_name)
{
	$CI =& get_instance();
	$sql = 'SHOW TABLES FROM `'.$CI->db->database.'`';
	$tables = $CI->db->query($sql)->result_array();
	$tables = array_map('current', $tables);
	return in_array($CI->db->protect_identifiers($table_name, TRUE, FALSE, FALSE), $tables);
}


/**
*Function convert percentage
*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
*@since Thursday 22 October 2020
*/
function percent($num_amount,$num_total){
	return number_format(($num_amount / $num_total) * 100, 2);
}