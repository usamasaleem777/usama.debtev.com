<?php


use Psr\Log\NullLogger;

function getUserFullName($user_id)
{
    //Get Fullname of user from user_id by adding first and last names from admin_users table
    $query = "SELECT first_name, last_name FROM admin_users WHERE user_id = '$user_id'";
    $result = DB::queryFirstRow($query);
    $full_name = $result['first_name'] . ' ' . $result['last_name'];
    return $full_name;
}

function ShowRoleName($role_id)
{
    //Get Role name from role_id by adding role_name from roles table
    $query = "SELECT role_desc FROM user_roles WHERE role_id = '$role_id'";
    $result = DB::queryFirstField($query);
    return $result;
}


function ShowManagerName($user_id)
{
    $full_name = '';
    //Get Manager name from user_id by geting manager_id from admin_users tabbel and then getting  first and last names of manager from admin_users table
    $query = "SELECT manager_id FROM admin_users WHERE user_id = '$user_id'";
    $result = DB::queryFirstField($query);
    // if manager_id is null then return N/A else get first and last names of manager from admin_users table
    if ($result == null) {
        $full_name = 'N/A';
    } else {
        $query = "SELECT first_name, last_name FROM admin_users WHERE user_id = '$result'";
        $result = DB::queryFirstRow($query);
        $full_name = $result['first_name'] . ' ' . $result['last_name'];
    }
    return $full_name;

}

function getCompanyName($company_id)
{
    //get company_name from companies table
    $company_name = DB::queryFirstField("SELECT company_name FROM companies WHERE company_id = $company_id");
    return $company_name;
}

function getCompanyEmailByCompanyId($company_id)
{
    //get company email from companies table
    $company_email = DB::queryFirstField("SELECT email FROM companies WHERE company_id = $company_id");
    return $company_email;
}

function getUserRoleID($user_id)
{
    $role_id = DB::queryFirstField("SELECT role_id FROM users WHERE user_id = $user_id");
    return $role_id;
}

function get_company_user_count($company_id)
{

    $user_count = DB::queryFirstField("SELECT COUNT(*) from admin_users where company_id =$company_id");

    return $user_count;
}



function getManagerByAgent($agentId)
{
    $manager_name = "--";
    if ($agentId <> "" and $agentId > 1) {
        $managerId = DB::queryFirstRow("SELECT manager_id FROM admin_users WHERE user_id = $agentId");
        if (isset($managerID)) {
            $manager = DB::queryFirstRow("SELECT * FROM admin_users WHERE user_id = '" . $managerId['manager_id'] . "'");
            $manager_name = $manager['first_name'] . " " . $manager['last_name'];
        }
    }

    return $manager_name;
}


function ShowAgentName($agent_id)
{

    $agent_name = "--";
    if ($agent_id <> "" and $agent_id > 1) {
        $agent = DB::queryFirstRow("SELECT first_name,last_name FROM admin_users WHERE user_id = $agent_id");
        // we need to account for the situation if agent_id does not exisit in admin_users table
        if (empty($agent)) {
            $agent_name = "--";
        } else {
            $agent_name = $agent['first_name'] . " " . $agent['last_name'];
        }
    }

    return $agent_name;
}


function get_company_targets_for_month($companyID, $date = NULL)
{
    if (is_null($date)) {
        $date = getDateTime(0, "MonthYear");
    } else {
        $date = getDateTime($date, "MonthYear");
    }
    $check_target = DB::queryFirstField("SELECT COUNT(*) FROM company_targets WHERE company_id = $companyID AND target_month_year =  '" . $date . "'  ");

    if ($check_target > 0) {

        $target = DB::queryFirstRow("SELECT * FROM company_targets WHERE company_id = $companyID AND target_month_year =  '" . $date . "'  ");
    } else {


        $target = DB::queryFirstRow("SELECT * FROM company_targets WHERE company_id = $companyID ORDER BY target_id DESC LIMIT 1");
    }
    if (isset($target['target_amount'])) {
        $target_amount = $target['target_amount'];
    } else {
        $target_amount = -1;
    }
    if (isset($target['target_deals'])) {
        $target_deals = $target['target_deals'];
    } else {
        $target_deals = -1;
    }

    if (isset($target['deals_avg'])) {

        $deal_avg = $target['deals_avg'];
    } else {
        $deal_avg = -1;
    }
    if (isset($target['deals_avg'])) {
        if (is_null($target['deals_avg']) || $target['deals_avg'] == 0) {
            $deal_avg = round0dp($target_amount / $target_deals);
        }
    }





    return array(
        'target_amount' => $target_amount,
        'target_deals' => $target_deals,
        'deals_avg' => $deal_avg
    );
}


function get_active_agents_list($companyID, $date = NULL, $back_office_query = NULL)
{

    if (is_null($date)) {

        $date = getDateTime(0, "mySQL");
    } else {

        $date = getDateTime($date, "mySQL");
    }


    $query = "SELECT  agent_id , COUNT(*) AS deals FROM sales_intake as s WHERE LEFT(date_signed,7) = LEFT('$date',7) AND company_id = $companyID AND STATUS=0 $back_office_query GROUP BY agent_id ORDER BY deals DESC ";
    //echo $query;
    $agents = DB::queryFirstColumn($query);

    return $agents;
}


function workingDays($date = null)
{
    if ($date == null) {
        $date = date('Y-m-d');
    }
    $date = strtotime($date);
    $month = date('m', $date);
    $year = date('Y', $date);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $workingDaysInMonth = 0;
    $workingDaysPassed = 0;
    $workingDaysRemaining = 0;
    $holidays = DB::queryFirstColumn('SELECT date FROM company_holidays WHERE company_id = ' . $_SESSION['company_id'] . ' ORDER BY date ASC');
    //print_r($holidays);
    for ($i = 1; $i <= $daysInMonth; $i++) {
        $currentDate = strtotime("$year-$month-$i");
        if (date('N', $currentDate) < 6 && !in_array(date('Y-m-d', $currentDate), $holidays)) {
            $workingDaysInMonth++;
            if ($currentDate <= $date) {
                $workingDaysPassed++;
            } elseif ($currentDate > $date) {
                $workingDaysRemaining++;
            }
        }
    }

    return array(
        'workingDaysInMonth' => $workingDaysInMonth,
        'workingDaysPassed' => $workingDaysPassed,
        'workingDaysRemaining' => $workingDaysRemaining
    );
}


// function getCurrentMonthGraphData()
// {
// 	$first_date = date('y-m-d', strtotime('first day of this month'));
// 	$last_date = date('y-m-d', strtotime('last day of this month'));
// 	$query = "SELECT DAY(date_signed )as ds,sum(debt_amount) as amount,count(brp_no) as total_deals  FROM sales_intake m WHERE m.date_signed
//     BETWEEN '".$first_date."' AND '".$last_date."' AND status = 0 group by date_signed";
// 	$result = DB::Query($query);

// 	$dates = array();
// 	$RevenueAmount = array();
// 	$dealsCount = array();
// 	if (count($result) > 0) {
// 		foreach ($result as $data) {
// 			$dates[]=  "'" . $data['ds']. date('M')."'";
// 			$RevenueAmount[]=   $data['amount'];
// 			$dealsCount[]=   $data['total_deals'];
// 		}
// 		$datesStr = implode(',',$dates);
// 		$RevenueAmountStr = implode(',',$RevenueAmount);
// 		$dealsCount = implode(',',$dealsCount);

// 		return array('dates'=>$datesStr,'revenue'=>$RevenueAmountStr,'dealscount'=>$dealsCount );
// 	}
// 	return 0;
// }



/********************/

/********************/


/******************Monthly Graph Work Start*******************/

function getCurrentMonthRevenueGraphData()
{
    $first_date = date('y-m-d', strtotime('first day of this month'));
    $last_date = date('y-m-d', strtotime('last day of this month'));
    $query = "SELECT DAY(date_signed )as ds,sum(debt_amount) as amount FROM sales_intake m WHERE m.date_signed
    BETWEEN '" . $first_date . "' AND '" . $last_date . "' AND  company_id = " . $_SESSION['company_id'] . " AND status IN (0,2) GROUP BY date_signed";


    $result = DB::Query($query);
    $monthNoOfDays = date('t');
    $finalCancelRevenueStr = getDailyCancelRevenue(); // get daily Cancel revenue


    $tempCounter = 1;

    $aDates = array();
    $aRevenueAmount = array();
    $dealsCount = array();
    $aFinalDates = array();
    $aFinalRevenue = array();
    $aFinalDealsCount = array();

    if (count($result) > 0) {
        foreach ($result as $data) {

            $aDates[$data['ds']] = $data['ds'];
            $aRevenueAmount[$data['ds']] = (int) $data['amount'];
        }

        if (count($aDates) > 0) {
            $monthDays = array();
            for ($counter = 1; $counter <= $monthNoOfDays; $counter++) {
                if (in_array($counter, $aDates)) {
                    $aFinalRevenue[$tempCounter] = $aRevenueAmount[$counter];
                } else {
                    $aFinalRevenue[$tempCounter] = 0;
                }
                //$monthDays[$counter] = "'".$counter.' '.date('M')."'";

                $monthDays[$counter] = "'" . date('D', strtotime(date('Y') . '-' . date('m') . '-' . $counter)) . ' ' . $counter . "'";
                $tempCounter++;
            }
            $revenueAmountStr = implode(',', $aFinalRevenue);
            $currentMonthStr = implode(',', $monthDays);


            return array(
                'revenue' => $revenueAmountStr,
                'currentmonthdays' => $currentMonthStr,
                'cancel_revenue' => $finalCancelRevenueStr
            );
        }
    }

    return 0;
}


// cancel revenue
function getDailyCancelRevenue()
{
    $first_date = date('y-m-d', strtotime('first day of this month'));
    $last_date = date('y-m-d', strtotime('last day of this month'));
    $query = "SELECT DAY(date_signed) AS ds,sum(cancel_amount) AS total_cancel_amount FROM sales_intake m 
        WHERE m.date_signed BETWEEN '" . $first_date . "' AND '" . $last_date . "'  AND  company_id = " . $_SESSION['company_id'] . " 
        AND status IN (2) GROUP BY date_signed  ORDER BY date_signed ASC";


    $result = DB::Query($query);
    $monthNoOfDays = date('t');
    $tempCounter = 1;
    if (count($result) > 0) {
        foreach ($result as $data) {

            $aDates[$data['ds']] = $data['ds'];
            $aTotalCancelAmount[$data['ds']] = (int) $data['total_cancel_amount'];
        }
        if (count($aDates) > 0) {

            for ($counter = 1; $counter <= $monthNoOfDays; $counter++) {
                if (in_array($counter, $aDates)) {
                    $aFinalCancelRevenue[$tempCounter] = $aTotalCancelAmount[$counter];
                } else {
                    $aFinalCancelRevenue[$tempCounter] = 0;
                }
                $tempCounter++;
            }

            return $finalCancelRevenueStr = implode(',', $aFinalCancelRevenue);
        }
    }

    return 0;
}

// get month deals
function getCurrentMonthDealsGraphData()
{

    $first_date = date('y-m-d', strtotime('first day of this month'));
    $last_date = date('y-m-d', strtotime('last day of this month'));
    $query = "SELECT DAY(date_signed )as ds,count(sale_id) as total_deals FROM sales_intake m WHERE m.date_signed
    BETWEEN '" . $first_date . "' AND '" . $last_date . "' AND  company_id = " . $_SESSION['company_id'] . " AND status IN (0,2) GROUP BY date_signed";




    $result = DB::Query($query);
    $monthNoOfDays = date('t');
    $finalCancelRevenueStr = getDailyCancelDeals(); // get daily Cancel revenue
    // echo "<pre>";
    //   print_r($finalCancelRevenueStr);
    // echo "</pre>";
    // die();
    // lq();
    // die();

    $tempCounter = 1;

    $aDates = array();

    $dealsCount = array();

    $aFinalDeals = array();
    $aFinalDealsCount = array();

    if (count($result) > 0) {
        foreach ($result as $data) {

            $aDates[$data['ds']] = $data['ds'];
            $aDeals[$data['ds']] = (int) $data['total_deals'];
        }

        if (count($aDates) > 0) {
            $monthDays = array();
            for ($counter = 1; $counter <= $monthNoOfDays; $counter++) {
                if (in_array($counter, $aDates)) {
                    $aFinalDeals[$tempCounter] = $aDeals[$counter];
                } else {
                    $aFinalDeals[$tempCounter] = 0;
                }

                // $monthDays[$counter] = "'".$counter.' '.date('M')."'";

                $monthDays[$counter] = "'" . date('D', strtotime(date('Y') . '-' . date('m') . '-' . $counter)) . ' ' . $counter . "'";


                $tempCounter++;
            }

            $dealsStr = implode(',', $aFinalDeals);
            $currentMonthStr = implode(',', $monthDays);

            return array(
                'deals' => $dealsStr,
                'currentmonthdays' => $currentMonthStr,
                'cancel_deals' => $finalCancelRevenueStr
            );
        }
    }

    return 0;
}

// get month deals
function getMonthsRangeDealsGraphData($first_date, $last_date)
{

    //$first_date = date('y-m-d', strtotime('first day of this month'));
    //$last_date = date('y-m-d', strtotime('last day of this month'));
    $query = "SELECT DAY(date_signed )as ds,count(sale_id) as total_deals FROM sales_intake m WHERE m.date_signed
    BETWEEN '" . $first_date . "' AND '" . $last_date . "' AND  company_id = " . $_SESSION['company_id'] . " AND status IN (0,2) GROUP BY date_signed";




    $result = DB::Query($query);
    $monthNoOfDays = date('t');
    $finalCancelRevenueStr = getDailyCancelDeals(); // get daily Cancel revenue
    // echo "<pre>";
    //   print_r($finalCancelRevenueStr);
    // echo "</pre>";
    // die();
    // lq();
    // die();

    $tempCounter = 1;

    $aDates = array();

    $dealsCount = array();

    $aFinalDeals = array();
    $aFinalDealsCount = array();

    if (count($result) > 0) {
        foreach ($result as $data) {

            $aDates[$data['ds']] = $data['ds'];
            $aDeals[$data['ds']] = (int) $data['total_deals'];
        }

        if (count($aDates) > 0) {
            $monthDays = array();
            for ($counter = 1; $counter <= $monthNoOfDays; $counter++) {
                if (in_array($counter, $aDates)) {
                    $aFinalDeals[$tempCounter] = $aDeals[$counter];
                } else {
                    $aFinalDeals[$tempCounter] = 0;
                }

                // $monthDays[$counter] = "'".$counter.' '.date('M')."'";

                $monthDays[$counter] = "'" . date('D', strtotime(date('Y') . '-' . date('m') . '-' . $counter)) . ' ' . $counter . "'";


                $tempCounter++;
            }

            $dealsStr = implode(',', $aFinalDeals);
            $currentMonthStr = implode(',', $monthDays);

            return array(
                'deals' => $dealsStr,
                'currentmonthdays' => $currentMonthStr,
                'cancel_deals' => $finalCancelRevenueStr
            );
        }
    }

    return 0;
}


// for get daily cancel deals
function getDailyCancelDeals()
{
    $first_date = date('y-m-d', strtotime('first day of this month'));
    $last_date = date('y-m-d', strtotime('last day of this month'));
    $query = "SELECT DAY(date_signed) AS ds,count(sale_id) as total_cancel_deals  FROM sales_intake m 

      WHERE m.date_signed BETWEEN '" . $first_date . "' AND '" . $last_date . "' AND   company_id = " . $_SESSION['company_id'] . " AND status IN (2)   
      GROUP BY date_signed  ORDER BY date_signed ASC";





    //die();

    $result = DB::Query($query);
    $monthNoOfDays = date('t');
    $tempCounter = 1;
    $aFinalCancelDeals = array();
    $aTotalCancelDeals = array();
    if (count($result) > 0) {
        foreach ($result as $data) {

            $aDates[$data['ds']] = $data['ds'];
            $aTotalCancelDeals[$data['ds']] = (int) $data['total_cancel_deals'];
        }
        if (count($aDates) > 0) {

            for ($counter = 1; $counter <= $monthNoOfDays; $counter++) {
                if (in_array($counter, $aDates)) {
                    $aFinalCancelDeals[$tempCounter] = $aTotalCancelDeals[$counter];
                } else {
                    $aFinalCancelDeals[$tempCounter] = 0;
                }
                $tempCounter++;
            }

            return $finalCancelDealsStr = implode(',', $aFinalCancelDeals);
        }
    }

    return 0;
}
/******************Monthly Graph Work End*******************/
/******************Monthly Range Graph Work Start*******************/

function getMonthlyRevenueData($monthYear) //calculate revenue for a month
{
    // Convert the string to a DateTime object
    $dateTime = DateTime::createFromFormat('Y-m', $monthYear);

    // Get the first day of the month
    $from = $dateTime->format('Y-m-01');

    // Get the last day of the month
    $to = $dateTime->format('Y-m-t');

    $query = "SELECT sum(debt_amount) as amount FROM sales_intake m WHERE m.date_signed
    BETWEEN '" . $from . "' AND '" . $to . "' AND  company_id = " . $_SESSION['company_id'] . " AND status IN (0,2)";


    $result = DB::Query($query);


    return $result[0]['amount'];
}

function getAllMonthlyRevenueDataGraph() //calculate revenue for a month
{
    $query = "SELECT MIN(date_signed) as minimum_date FROM sales_intake where company_id = " . $_SESSION['company_id'];
    $date = DB::Query($query);

    $monthsDifference = calculateMonthsDifference($date[0]['minimum_date']);
    // print_r($monthsDifference);die();


    // Save all month's name in an array from minimum date of sales_intake table and display as Year-Month
    $month_array = array();
    for ($i = 0; $i < $monthsDifference + 1; $i++) {

        $month_array[] = date("Y-m", strtotime(date('Y-m-01') . " -$i months"));
    }

    foreach ($month_array as $month) {
        $amount = getMonthlyRevenueData($month);
        if (!isset($amount) || $amount == '') {
            $amount_array[] = 0;
        } else {
            $amount_array[] = getMonthlyRevenueData($month);
        }
        $converted_months_array[] = date('F Y', strtotime($month . '-01'));
    }

    $amount_array = array_reverse($amount_array);
    $converted_months_array = array_reverse($converted_months_array);
    // return $converted_months_array;

    // Add quotes around each element
    $quotedMonths = array_map(function ($element) {
        return "'" . $element . "'";
    }, $converted_months_array);

    $revenueAmountStr = implode(',', $amount_array);
    $currentMonthStr = implode(',', $quotedMonths);


    return array(
        'revenue' => $revenueAmountStr,
        'currentmonths' => $currentMonthStr
    );
}


function getMonthlyDealData($monthYear) //calculate revenue for a month
{
    // Convert the string to a DateTime object
    $dateTime = DateTime::createFromFormat('Y-m', $monthYear);

    // Get the first day of the month
    $from = $dateTime->format('Y-m-01');

    // Get the last day of the month
    $to = $dateTime->format('Y-m-t');

    $query = "SELECT count(sale_id) as total_deals FROM sales_intake m WHERE m.date_signed
    BETWEEN '" . $from . "' AND '" . $to . "' AND  company_id = " . $_SESSION['company_id'] . " AND status IN (0,2)";


    $result = DB::Query($query);


    return $result[0]['total_deals'];
}

function getAllMonthlyDealDataGraph() //calculate revenue for a month
{
    $query = "SELECT MIN(date_signed) as minimum_date FROM sales_intake where company_id = " . $_SESSION['company_id'];
    $date = DB::Query($query);

    $monthsDifference = calculateMonthsDifference($date[0]['minimum_date']);
    // print_r($monthsDifference);die();


    // Save all month's name in an array from minimum date of sales_intake table and display as Year-Month
    $month_array = array();
    for ($i = 0; $i < $monthsDifference + 1; $i++) {

        $month_array[] = date("Y-m", strtotime(date('Y-m-01') . " -$i months"));
    }

    foreach ($month_array as $month) {
        $deals = getMonthlyDealData($month);
        if (!isset($deals) || $deals == '') {
            $amount_array[] = 0;
        } else {
            $deal_array[] = getMonthlyDealData($month);
        }
        $converted_months_array[] = date('F Y', strtotime($month . '-01'));
    }

    $deal_array = array_reverse($deal_array);
    $converted_months_array = array_reverse($converted_months_array);

    // return $converted_months_array;

    // Add quotes around each element
    $quotedMonths = array_map(function ($element) {
        return "'" . $element . "'";
    }, $converted_months_array);

    // Add quotes around each element
    $quotedDeals = array_map(function ($element) {
        return "'" . $element . "'";
    }, $deal_array);

    $dealStr = implode(',', $quotedDeals);
    $currentMonthStr = implode(',', $quotedMonths);


    return array(
        'deals' => $dealStr,
        'currentmonths' => $currentMonthStr
    );
}

function getMonthsRangeRevenueGraphData($from, $to) //updated
{
    // $first_date = date('y-m-d', strtotime('first day of this month'));
    //$last_date = date('y-m-d', strtotime('last day of this month'));
    $query = "SELECT DAY(date_signed )as ds,sum(debt_amount) as amount FROM sales_intake m WHERE m.date_signed
    BETWEEN '" . $from . "' AND '" . $to . "' AND  company_id = " . $_SESSION['company_id'] . " AND status IN (0,2) GROUP BY date_signed";


    $result = DB::Query($query);
    $monthNoOfDays = date('t');
    $finalCancelRevenueStr = getDailyCancelRevenue(); // get daily Cancel revenue


    $tempCounter = 1;

    $aDates = array();
    $aRevenueAmount = array();
    $dealsCount = array();
    $aFinalDates = array();
    $aFinalRevenue = array();
    $aFinalDealsCount = array();

    if (count($result) > 0) {
        foreach ($result as $data) {

            $aDates[$data['ds']] = $data['ds'];
            $aRevenueAmount[$data['ds']] = (int) $data['amount'];
        }

        if (count($aDates) > 0) {
            $monthDays = array();
            for ($counter = 1; $counter <= $monthNoOfDays; $counter++) {
                if (in_array($counter, $aDates)) {
                    $aFinalRevenue[$tempCounter] = $aRevenueAmount[$counter];
                } else {
                    $aFinalRevenue[$tempCounter] = 0;
                }
                //$monthDays[$counter] = "'".$counter.' '.date('M')."'";

                $monthDays[$counter] = "'" . date('D', strtotime(date('Y') . '-' . date('m') . '-' . $counter)) . ' ' . $counter . "'";
                $tempCounter++;
            }
            $revenueAmountStr = implode(',', $aFinalRevenue);
            $currentMonthStr = implode(',', $monthDays);


            return array(
                'revenue' => $revenueAmountStr,
                'currentmonthdays' => $currentMonthStr,
                'cancel_revenue' => $finalCancelRevenueStr
            );
        }
    }

    return 0;
}
/******************Monthly Graph Work End*******************/



/**********************Weekly Graph Work Start*****************************/
function getCurrentWeeklyRevenueGraphData()
{
    // Get the timestamp for last Monday
    $monday = strtotime("last monday");
    // If today is Monday, get the next Monday
    $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
    // Get the timestamp for the upcoming Sunday
    $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");

    // Format the start and end dates of the week
    $this_week_sd = date("Y-m-d", $monday);
    $this_week_ed = date("Y-m-d", $sunday);

    // Initialize arrays
    $aDates = array();
    $aRevenueAmount = array();
    $weekDays = array();
    $aFinalRevenue = array();

    // Construct the query string
    $query = "SELECT DAY(date_signed) as ds, sum(debt_amount) as amount FROM sales_intake m WHERE m.date_signed
    BETWEEN '" . $this_week_sd . "' AND '" . $this_week_ed . "' AND company_id =" . $_SESSION['company_id'] . " AND status = 0 GROUP BY date_signed";

    // Execute the query
    $result = DB::Query($query);

    // Get daily Cancel revenue
    $finalCancelRevenueStr = getCurrentWeeklyCancelRevenue();

    // Initialize string variables
    $revenueAmountStr = $weekdaysStr = '0,0,0,0,0,0,0';

    if (count($result) > 0) {
        foreach ($result as $data) {
            $aDates[$data['ds']] = $data['ds'];
            $aRevenueAmount[$data['ds']] = (int) $data['amount'];
        }

        // Create DateTime objects for the start and end dates
        $startDateTime = new DateTime($this_week_sd);
        $endDateTime = new DateTime($this_week_ed);

        // Create a DateInterval object for one day
        $oneDayInterval = new DateInterval('P1D');

        // Initialize the counter
        $tempCounter = (int) $startDateTime->format('d');

        // Iterate through the days of the week using the DateTime and DateInterval objects
        for ($currentDateTime = clone $startDateTime; $currentDateTime <= $endDateTime; $currentDateTime->add($oneDayInterval)) {
            $counter = (int) $currentDateTime->format('d');

            if (isset($aRevenueAmount[$counter])) {
                $aFinalRevenue[$tempCounter] = $aRevenueAmount[$counter];
            } else {
                $aFinalRevenue[$tempCounter] = 0;
            }

            $weekDays[$counter] = "'" . $currentDateTime->format('D') . "'";
            $tempCounter++;
        }

        $revenueAmountStr = implode(',', $aFinalRevenue);
        $weekdaysStr = implode(',', $weekDays);
    }

    // Return the result as an array
    return array('revenue' => $revenueAmountStr, 'cancel_revenue' => $finalCancelRevenueStr, 'week_days' => $weekdaysStr);
}

function getCurrentWeeklyCancelRevenue()
{
    // Similar modifications should be made to this function as well
    // Get the timestamp for last Monday
    $monday = strtotime("last monday");
    // If today is Monday, get the next Monday
    $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
    // Get the timestamp for the upcoming Sunday
    $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");

    // Format the start and end dates of the week
    $this_week_sd = date("Y-m-d", $monday);
    $this_week_ed = date("Y-m-d", $sunday);

    // Construct the query string
    $query = "SELECT DAY(date_signed) AS ds, sum(cancel_amount) AS total_cancel_amount FROM sales_intake m 
    WHERE m.date_signed BETWEEN '" . $this_week_sd . "' AND '" . $this_week_ed . "' AND company_id =" . $_SESSION['company_id'] . " AND status IN (2) GROUP BY date_signed ORDER BY date_signed ASC";

    // Execute the query
    $result = DB::Query($query);

    // Initialize string variables
    $finalCancelRevenueStr = '0,0,0,0,0,0,0';

    if (count($result) > 0) {
        foreach ($result as $data) {
            $aDates[$data['ds']] = $data['ds'];
            $aTotalCancelAmount[$data['ds']] = (int) $data['total_cancel_amount'];
        }

        // Create DateTime objects for the start and end dates
        $startDateTime = new DateTime($this_week_sd);
        $endDateTime = new DateTime($this_week_ed);

        // Create a DateInterval object for one day
        $oneDayInterval = new DateInterval('P1D');

        // Initialize the counter
        $tempCounter = (int) $startDateTime->format('d');

        // Iterate through the days of the week using the DateTime and DateInterval objects
        for ($currentDateTime = clone $startDateTime; $currentDateTime <= $endDateTime; $currentDateTime->add($oneDayInterval)) {
            $counter = (int) $currentDateTime->format('d');

            if (isset($aTotalCancelAmount[$counter])) {
                $aFinalCancelRevenue[$tempCounter] = $aTotalCancelAmount[$counter];
            } else {
                $aFinalCancelRevenue[$tempCounter] = 0;
            }

            $tempCounter++;
        }

        $finalCancelRevenueStr = implode(',', $aFinalCancelRevenue);
    }

    // Return the final cancel revenue string
    return $finalCancelRevenueStr;
}


/********Weekly Deals**********/

function getCurrentWeeklyDealsGraphData()
{
    // Get the timestamp for last Monday
    $monday = strtotime("last monday");
    // If today is Monday, get the next Monday
    $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
    // Get the timestamp for the upcoming Sunday
    $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");

    // Format the start and end dates of the week
    $this_week_sd = date("Y-m-d", $monday);
    $this_week_ed = date("Y-m-d", $sunday);

    // Initialize arrays
    $aDates = array();
    $aDeals = array();
    $weekDays = array();
    $aFinalDeals = array();

    // Construct the query string
    $query = "SELECT DAY(date_signed) as ds, count(sale_id) as total_deals FROM sales_intake m WHERE m.date_signed
        BETWEEN '" . $this_week_sd . "' AND '" . $this_week_ed . "' AND company_id =" . $_SESSION['company_id'] . " AND status =0 GROUP BY date_signed";

    // Execute the query
    $result = DB::Query($query);

    // Initialize string variables
    $dealsStr = $weekdaysStr = '0,0,0,0,0,0,0';

    // Get daily Cancel revenue
    $finalCancelRevenueStr = getWeeklyCancelDeals();

    if (count($result) > 0) {
        foreach ($result as $data) {
            $aDates[$data['ds']] = $data['ds'];
            $aDeals[$data['ds']] = (int) $data['total_deals'];
        }

        // Create DateTime objects for the start and end dates
        $startDateTime = new DateTime($this_week_sd);
        $endDateTime = new DateTime($this_week_ed);

        // Create a DateInterval object for one day
        $oneDayInterval = new DateInterval('P1D');

        // Initialize the counter
        $tempCounter = (int) $startDateTime->format('d');

        // Iterate through the days of the week using the DateTime and DateInterval objects
        for ($currentDateTime = clone $startDateTime; $currentDateTime <= $endDateTime; $currentDateTime->add($oneDayInterval)) {
            $counter = (int) $currentDateTime->format('d');

            if (isset($aDeals[$counter])) {
                $aFinalDeals[$tempCounter] = $aDeals[$counter];
            } else {
                $aFinalDeals[$tempCounter] = 0;
            }

            $weekDays[$counter] = "'" . $currentDateTime->format('D') . "'";
            $tempCounter++;
        }

        $dealsStr = implode(',', $aFinalDeals);
        $weekdaysStr = implode(',', $weekDays);
    }

    // Return the result as an array
    return array('deals' => $dealsStr, 'week_days' => $weekdaysStr, 'cancel_deals' => $finalCancelRevenueStr);
}

// weekly cancel deals
function getWeeklyCancelDeals()
{

    $monday = strtotime("last monday");
    $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
    $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
    $this_week_sd = date("Y-m-d", $monday);
    $this_week_ed = date("Y-m-d", $sunday);

    $query = "SELECT DAY(date_signed) AS ds,count(sale_id) as total_cancel_deals  FROM sales_intake m 
       WHERE m.date_signed BETWEEN '" . $this_week_sd . "' AND '" . $this_week_ed . "'  AND company_id =" . $_SESSION['company_id'] . "
         AND status IN (2) GROUP BY date_signed  ORDER BY date_signed ASC";

    $result = DB::Query($query);
    $finalCancelDealsStr = '0,0,0,0,0,0,0';
    $startCounter = (int) date('d', strtotime($this_week_sd));
    $endCounter = (int) date('d', strtotime($this_week_ed));

    $aFinalCancelDeals = array();
    $aTotalCancelDeals = array();
    if (count($result) > 0) {
        foreach ($result as $data) {

            $aDates[$data['ds']] = $data['ds'];
            $aTotalCancelDeals[$data['ds']] = (int) $data['total_cancel_deals'];
        }
        $tempCounter = $startCounter;

        if (count($aDates) > 0) {

            for ($counter = $startCounter; $counter <= $endCounter; $counter++) {
                if (in_array($counter, $aDates)) {
                    $aFinalCancelDeals[$tempCounter] = $aTotalCancelDeals[$counter];
                } else {
                    $aFinalCancelDeals[$tempCounter] = 0;
                }
                $tempCounter++;
            }

            return $finalCancelDealsStr = implode(',', $aFinalCancelDeals);
        }
    }

    return $finalCancelDealsStr;
}

/**********************Weekly Graph Work End*****************************/


/******Send Notifications Start*****29-08-2023****/
/*|-------------------------------**/
// call sendNotification(

// array(

//     'receiver_id'=>90,
//     'type'=>1,
//     'subject'=>'Test Subject',
//     'message'=>'Some Message'
//   )

//)
/*|-------------------------------**/


function sendNotification($aParam)
{

    if (empty($_SESSION['user_id']) or !$_SESSION['user_id']) {
        return false;
    }

    if (is_Array($aParam) & count($aParam) > 0) {

        $aData = array(
            'sender_id' => $_SESSION['user_id'],
            'receiver_id' => $aParam['receiver_id'],
            'type' => $aParam['type'],
            'subject' => $aParam['subject'],
            'message' => $aParam['message'],
        );
        /* 
                if( DB::insert(TBL_NOTIFICATION, $aData) > 0)
                {
                    return true;
                }
              */
        return false;
    }
}


function getCompanyRevenueToday()
{

    $current_date = Date('Y-m-d');
    // Assuming DB::Query is your database access method
    // and it returns an array of results
    $query1 = "SELECT SUM(debt_amount) AS revenue_today FROM sales_intake WHERE date_signed='" . $current_date . "' AND company_id = '" . $_SESSION['company_id'] . "' AND status = 0";
    $revenueToday = DB::queryFirstField($query1);

    // Check if there are results and return the revenue for today (or 0 if no data found)
    if (!empty($revenueToday)) {
        return $revenueToday;
    } else {
        return 0;
    }
}

function getRevenueNeededForThisMonth()
{
    // current month 
    $query1 = "SELECT sum(target_amount) AS target_amount_current_month FROM `company_targets` WHERE  target_month_year = '" . DATE('M-Y') . "' ";
    $result = DB::Query($query1);

    // current revnue month 
    $query2 = "SELECT sum(debt_amount) AS revenue_month  FROM `sales_intake` WHERE MONTH(date_signed)  = MONTH(now()) ";
    $result2 = DB::Query($query2);
    return (int) ($result[0]['target_amount_current_month'] - (int) $result2[0]['revenue_month']);
}

function getMonthlyCancelPercentage($company_id)
{

    // get monthly cancel percentage by company id 
    // get monthly cancel amount by company id
    // get monthly deals amount by company id

    $monthly_cancel_percentage = 0;
    $monthly_cancel_amount = 0;
    $monthly_deals_amount = 0;

    $query1 = "SELECT SUM(cancel_amount) AS monthly_cancel_amount FROM sales_intake WHERE MONTH(date_signed)  = MONTH(now()) AND YEAR(date_signed) = YEAR(NOW())  AND company_id = '" . $company_id . "' AND status = 2";
    // echo $query1;
    // die( );
    $monthly_cancel_amount = DB::queryFirstField($query1);
    $query2 = "SELECT SUM(debt_amount) AS monthly_deals_amount FROM sales_intake WHERE MONTH(date_signed)  = MONTH(now()) AND YEAR(date_signed) = YEAR(NOW()) AND company_id = '" . $company_id . "' AND status = 0";
    $monthly_deals_amount = DB::queryFirstField($query2);
    if ($monthly_deals_amount > 0) {
        $monthly_cancel_percentage = ($monthly_cancel_amount / $monthly_deals_amount) * 100;
    } else {
        $monthly_cancel_percentage = 0;
    }

    return $monthly_cancel_percentage;
}
// Get Avg Days to first draft for current month

function get_avg_days_to_draft_current_month($company_id)
{
    $query = "SELECT AVG(DATEDIFF(first_draft_date,date_signed)) AS avg_days_to_draft_current_month FROM sales_intake WHERE MONTH(date_signed)  = MONTH(now()) AND YEAR(date_signed) = YEAR(NOW()) AND company_id = '" . $company_id . "' AND status = 0";
    $avg_days_to_draft_current_month = DB::queryFirstField($query);
    return round($avg_days_to_draft_current_month ?? 0, 2);
}

function getCurrentMonthDebtAmount($company_id)
{

    // current revnue month 
    $query = "SELECT SUM(debt_amount) AS revenue_today FROM sales_intake WHERE status = 0 AND company_id = " . $company_id . " 
    AND MONTH(date_signed)  = MONTH(now())";
    $currentMonthRev = DB::queryFirstField($query);
    return $currentMonthRev;
}

/* 
Write a function to caculate Average daily debt amount for current month
*/
function getAverageDailyDebtAmount($company_id)
{

    $daysInMonth = workingDays()['workingDaysInMonth'];
    $currentMonthDebtAmount = getCurrentMonthDebtAmount($company_id);
    $averageDailyDebtAmount = $currentMonthDebtAmount / $daysInMonth;

    return $averageDailyDebtAmount;
}

// write average deal amount for current month
function getAverageDealAmount($company_id)
{
    $currentMonthDebtAmount = getCurrentMonthDebtAmount($company_id);
    $currentMonthDeals = getMonthlyAdminDeals($company_id);
    $averageDealAmount = $currentMonthDebtAmount / $currentMonthDeals;

    return $averageDealAmount;
}

function getDailyRevenueNeededForThisMonth($company_id)
{
    $remaingdaysInMonth = workingDays()['workingDaysRemaining'];

    // Check if remaining days in month is zero to avoid division by zero error
    if ($remaingdaysInMonth == 0) {
        // Return a value or message indicating that calculation cannot be performed
        return $dailyRevenueNeeded = (getMonthlyCompanyTargetsSum($company_id) - getMonthlyAdminGrossSale($company_id));
    }

    $dailyRevenueNeeded = (getMonthlyCompanyTargetsSum($company_id) - getMonthlyAdminGrossSale($company_id)) / $remaingdaysInMonth;

    return $dailyRevenueNeeded;
}

function getComapanyTodayDealCount($company_id)
{
    $query = " SELECT count(sale_id) AS today_deals_count FROM sales_intake WHERE DATE(date_signed) = CURDATE() AND
    company_id = '" . $company_id . "' 
    AND status = 0";
    $today_deals_count = DB::queryFirstField($query);
    return $today_deals_count;
}



/* 
Fix the getTodayAverageDealAmount function to resolve division by zero error when today deals are zero    
*/
function getTodayAverageDealAmount($company_id)
{
    $todayDebtAmount = getCompanyRevenueToday($company_id);  // zero 

    $revenueData = getCompanyCurrentDateRevenue($company_id);
    // $todayDeals = getComapanyTodayDealCount($company_id); 

    // Check if $revenueData is an array and 'today_deals' key exists
    $todayDeals = (is_array($revenueData) && isset($revenueData['today_deals'])) ? $revenueData['today_deals'] : 0;

    // Check if $todayDeals is zero
    if ($todayDeals == 0) {
        return 0;  // Return 0 or any other default value
    }

    $todayAverageDealAmount = $todayDebtAmount / $todayDeals;

    return $todayAverageDealAmount;
}

// daily cancel amount
function getDailyAdminCancelsAmount($company_id) // new
{
    $date = date('Y-m-d');
    $query = "SELECT sum(cancel_amount) AS daily_cancel_amount  FROM sales_intake m WHERE m.date_signed = '" . $date . "' 
        AND company_id = $company_id AND cancel_amount != '' 
        AND status IN (2)";
    $result = DB::Query($query);
    return (($result[0]['daily_cancel_amount']) ? $result[0]['daily_cancel_amount'] : 0);
}

// weekly cancel amount
function getWeeklyAdminCancelsAmount($company_id)
{

    $monday = strtotime("last monday");
    $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
    $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
    $this_week_sd = date("Y-m-d", $monday);
    $this_week_ed = date("Y-m-d", $sunday);
    $query = "SELECT sum(cancel_amount) AS weekly_cancel_amount  FROM sales_intake m 
        WHERE m.date_signed BETWEEN '" . $this_week_sd . "' AND '" . $this_week_ed . "' AND company_id = $company_id AND cancel_amount != '' 
        AND status IN (2)";

    $result = DB::Query($query);
    return (($result[0]['weekly_cancel_amount']) ? $result[0]['weekly_cancel_amount'] : 0);
}

// monthly cancel amount
function getMonthlyAdminCancelAmount($company_id)
{
    $first_date = date('y-m-d', strtotime('first day of this month'));
    $last_date = date('y-m-d', strtotime('last day of this month'));
    $query = "SELECT sum(cancel_amount) AS monthly_cancel_amount FROM sales_intake m WHERE m.date_signed BETWEEN '" . $first_date . "' AND '" . $last_date . "'
        AND company_id = $company_id  AND status IN (2)";

    $result = DB::Query($query);
    return (($result[0]['monthly_cancel_amount']) ? $result[0]['monthly_cancel_amount'] : 0);
}
// search filter cancel amount
function getBySearchAdminCancelAmount($first_date, $last_date, $company_id)
{
    $query = "SELECT sum(cancel_amount) AS monthly_cancel_amount_search FROM sales_intake m WHERE m.date_signed 
        BETWEEN '" . $first_date . "' AND '" . $last_date . "'
        AND company_id = $company_id  AND status IN (2)";

    $result = DB::Query($query);
    return (($result[0]['monthly_cancel_amount_search']) ? $result[0]['monthly_cancel_amount_search'] : 0);
}


function getAgentUserIdByName($fullName, $companyId)
{

    /**
     * Fetch the user_id for a given agent's name and company_id.
     *
     * @param string $fullName Full name of the agent.
     * @param int $companyId The ID of the company the agent is associated with.
     *
     * @return int Returns the user_id if found, otherwise returns -1.
     */
    // Trim the full name and split it into components
    $names = preg_split('/\s+/', trim($fullName));
    $lastName = array_pop($names);
    $firstName = implode(" ", $names);

    // Fetch the user_id based on the provided name and company_id
    $result = DB::queryFirstRow("SELECT user_id FROM admin_users WHERE first_name=%s AND last_name=%s AND company_id=%d", $firstName, $lastName, $companyId);

    // Return the user_id if found
    if ($result) {
        return $result['user_id'];
    } else {
        return -1; // Indicates that no matching agent was found
    }
}


function processAchievementLevel($level)
{

    /**
     * Processes the given achievement level, ensuring it's within a valid range.
     * 
     * @param string $level The achievement level as a string, which can be with or without a '%' sign.
     *                      Example values: "1.50%", "1.50 %", "1.50"
     * 
     * @return float The processed achievement level as a float. If the provided level is outside the 
     *               range 0.50 to 2.00, the function returns the default value of 0.75.
     */


    // Trim any leading or trailing whitespaces from the input string
    $level = trim($level);

    // Remove percentage signs and any surrounding whitespaces. 
    // This accounts for formats like "1.50 %", "1.50% ", " 1.50%", etc.
    $cleanedLevel = preg_replace('/\s*%\s*/', '', $level);

    // Convert the cleaned string to a float for further processing
    $floatLevel = floatval($cleanedLevel);

    // Check if the value is within the valid range (0.50 to 2.00)
    // If not, default to 0.75
    if ($floatLevel < 0.50 || $floatLevel > 2.00) {
        return 0.75;
    }

    // Return the processed achievement level
    return $floatLevel;
}

function saveLtNotification($live_transfer_id, $company_id, $created_by_user_id, $created_by_role_id, $display_to_role_id, $class, $title, $message)
{
    // Insert data into notifications table
    DB::insert("notifications", array(
        'company_id' => $company_id,
        'is_read' => 0,
        'live_transfer_id' => $live_transfer_id,
        'user_id' => $created_by_user_id,
        'role_id' => $created_by_role_id,
        'display_to_role_id' => $display_to_role_id,
        'class' => $class,
        'title' => $title,
        'message' => $message,
    ));
}

function saveQaNotification($qa_call_track_id, $company_id, $display_to_user_id, $created_by_role_id, $display_to_role_id, $class, $title, $message)
{
    // Insert data into notifications table
    DB::insert("notifications", array(
        'company_id' => $company_id,
        'is_read' => 0,
        'qa_call_track_id' => $qa_call_track_id,
        'user_id' => $display_to_user_id,
        'role_id' => $created_by_role_id,
        'display_to_role_id' => $display_to_role_id,
        'class' => $class,
        'title' => $title,
        'message' => $message,
    ));
}

function saveSfNotification($fresh_lead_id, $company_id, $display_to_user_id, $created_by_role_id, $display_to_role_id, $class, $title, $message)
{
    // Insert data into notifications table
    DB::insert("notifications", array(
        'company_id' => $company_id,
        'is_read' => 0,
        'fresh_lead_id' => $fresh_lead_id,
        'user_id' => $display_to_user_id,
        'role_id' => $created_by_role_id,
        'display_to_role_id' => $display_to_role_id,
        'class' => $class,
        'title' => $title,
        'message' => $message,
    ));
}

function notifications($status, $sale_id)
{
    // Status switch
    switch ($status) {
        case "0":
            $notificationType = "Active";
            $class = "bg-primary";
            break;
        case "1":
            $notificationType = "Pending";
            $class = "bg-warning";
            break;
        case "2":
            $notificationType = "Cancelled";
            $class = "bg-danger";
            break;
        default:
            $statusVal = "Active";
            $class = "bg-secondary";
    }

    // Retrieve session variables
    $company_id = $_SESSION['company_id'];
    $company_name = $_SESSION['company_name'];
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $role_id = $_SESSION['role_id'];

    // Notification title and message
    $notificationTitle = getBrpNo($sale_id) . " status is  '" . $notificationType . "'";

    // Check if the status is 'Cancelled'
    if ($status == '2') {
        // Fetch client name and debt amount from the sales intake table
        $clientName = getClientNameFromSales($sale_id);
        $debtAmount = getDebtAmountFromSales($sale_id);

        // Fetch user_id and split_id from the sales_intake table
        $user_split_ids_result = DB::query("SELECT agent_id, split_with_id FROM sales_intake WHERE sale_id = %s", $sale_id);

        // Check if a result was found
        if (!empty($user_split_ids_result)) {
            // Extract user_id and split_id from the result
            $user_id1 = $user_split_ids_result[0]['agent_id'];
            $split_id = $user_split_ids_result[0]['split_with_id'];

            // Customize message for agent when the deal is cancelled
            $messageTitle = "You have received a cancellation";
            $message = getBrpNo($sale_id) . " under '" . $company_name . "' has been cancelled. $" . $debtAmount . " | " . $clientName . ". This deal has been cancelled.";

            // Inserting notification into the database for the primary agent
            DB::insert("notifications", array(
                'title' => $messageTitle,
                'company_id' => $company_id,
                'is_read' => 0,
                'deal_id' => $sale_id,
                'user_id' => $user_id1,
                'created_by' => $user_name,
                'role_id' => $role_id,
                'class' => $class,
                'message' => $message,
            ));

            // Check if there is a split_id and send a message to the split_id as well
            if (!empty($split_id)) {
                // Customize message for the split agent when the deal is cancelled
                $splitMessage = getBrpNo($sale_id) . " under '" . $company_name . "' has been cancelled. $" . $debtAmount . " | " . $clientName . ". This deal has been cancelled.";

                // Inserting notification into the database for the split agent
                DB::insert("notifications", array(
                    'title' => $messageTitle,
                    'company_id' => $company_id,
                    'is_read' => 0,
                    'deal_id' => $sale_id,
                    'user_id' => $split_id, // Update this to the appropriate role for split agents
                    'created_by' => $user_name,
                    'role_id' => $role_id,
                    'class' => $class,
                    'message' => $splitMessage,
                ));
            }
        }
    }


    // Continue with the usual code execution
    // Default message for other roles or when the status is not 'Cancelled'
    $message = getBrpNo($sale_id) . " under '" . $company_name . "' has been set to '" . $notificationType . "' status by " . $user_name . ". <a href=\"index.php?route=modules/dataentry/editsalesintake&sale_id=" . $sale_id . "\"><strong>Click here</strong></a> to check the detail";

    // Inserting notification into the database
    DB::insert("notifications", array(
        'title' => $notificationTitle,
        'company_id' => $company_id,
        'is_read' => 0,
        'deal_id' => $sale_id,
        'user_id' => $user_id,
        'created_by' => $user_name,
        'role_id' => $role_id,
        'class' => $class,
        'message' => $message,
    ));
}



// Function to get debt amount from sales intake table
function getDebtAmountFromSales($sale_id)
{
    // Replace 'DB' with your actual database class
    $result = DB::query("SELECT debt_amount FROM sales_intake WHERE sale_id = %i", $sale_id);

    if ($result && isset($result[0]['debt_amount'])) {
        return $result[0]['debt_amount'];
    }

    // Return a default value or handle the absence of data based on your requirements
    return 0;
}

// Function to get client name from sales intake table
function getClientNameFromSales($sale_id)
{
    // Replace 'DB' with your actual database class
    $result = DB::query("SELECT first_name, last_name FROM sales_intake WHERE sale_id = %i", $sale_id);

    if ($result && isset($result[0]['first_name'], $result[0]['last_name'])) {
        $firstName = $result[0]['first_name'];
        $lastName = $result[0]['last_name'];

        // Concatenate first and last names
        $clientName = $firstName . " " . $lastName;

        return $clientName;
    }

    // Return a default value or handle the absence of data based on your requirements
    return "Unknown Client";
}




function timeAgo($timestamp)
{
    $datetime1 = new DateTime("now");
    $datetime2 = date_create($timestamp);
    $diff = date_diff($datetime1, $datetime2);
    $timemsg = '';
    if ($diff->y > 0) {
        $timemsg = $diff->y . ' year' . ($diff->y > 1 ? "'s" : '');
    } else if ($diff->m > 0) {
        $timemsg = $diff->m . ' month' . ($diff->m > 1 ? "'s" : '');
    } else if ($diff->d > 0) {
        $timemsg = $diff->d . ' day' . ($diff->d > 1 ? "'s" : '');
    } else if ($diff->h > 0) {
        $timemsg = $diff->h . ' hour' . ($diff->h > 1 ? "'s" : '');
    } else if ($diff->i > 0) {
        $timemsg = $diff->i . ' minute' . ($diff->i > 1 ? "'s" : '');
    } else if ($diff->s > 0) {
        $timemsg = $diff->s . ' second' . ($diff->s > 1 ? "'s" : '');
    }

    $timemsg = $timemsg . ' ago';
    return $timemsg;
}

function get_active_agents_for_month($month, $company_id, $back_office_query = "")
{
    // Use MeekroDB to get count of active agents for the month
    // All agents who have atleast one deal in the month in sales_intake table.
    // sales_intake.agent_id is the agent_id
    // sales_intake.sale_id is the sale_id
    // sales_intake.status is the status
    // sales_intake.date_signed is the date that the deal was signed
    // sales_intake.company_id is the company_id
    // $month is the month for which we want to get the count of active agents
    // $company_id is the company_id for which we want to get the count of active agents
    // $active_agents is the count of active agents for the month
    // $sql is the SQL query to get the count of active agents for the month
    // $params is the array of parameters for the SQL query
    // $active_agents is the count of active agents for the month
    $month = date("Y-m-d", strtotime($month));
    $sql = "SELECT COUNT(DISTINCT agent_id) AS active_agents FROM sales_intake as s WHERE MONTH(date_signed) = MONTH('$month') AND YEAR(date_signed) = YEAR('$month') AND company_id = $company_id " . $back_office_query;
    // echo $sql; // die( );
    $params = array();
    $active_agents = DB::queryFirstField($sql, $params);
    return $active_agents;
}
function get_gross_sales_for_month($month, $company_id, $back_office_query = "")
{
    // Use MeekroDB to get count of gross sales for the month
    // sales_intake.sale_id is the sale_id
    // sales_intake.status is the status
    // sales_intake.date_signed is the date that the deal was signed
    // sales_intake.company_id is the company_id
    // $month is the month for which we want to get the count of gross sales
    // $company_id is the company_id for which we want to get the count of gross sales
    // $gross_sales is the count of gross sales for the month
    // $sql is the SQL query to get the count of gross sales for the month
    // $params is the array of parameters for the SQL query
    // $gross_sales is the count of gross sales for the month
    $month = date("Y-m-d", strtotime($month));
    $sql = "SELECT COUNT(sale_id) AS gross_sales FROM sales_intake as s WHERE MONTH(date_signed) = MONTH('$month') AND YEAR(date_signed) = YEAR('$month') AND status = 0 AND company_id = $company_id " . $back_office_query;
    //echo $sql; // die( );
    $params = array();
    $gross_sales = DB::queryFirstField($sql, $params);
    return $gross_sales;
}

function get_gross_cancels_for_month($month, $company_id, $back_office_query = "")
{
    // Use MeekroDB to get count of gross cancels for the month
    // sales_intake.sale_id is the sale_id
    // sales_intake.status is the status
    // sales_intake.date_signed is the date that the deal was signed
    // sales_intake.company_id is the company_id
    // $month is the month for which we want to get the count of gross cancels
    // $company_id is the company_id for which we want to get the count of gross cancels
    // $gross_cancels is the count of gross cancels for the month
    // $sql is the SQL query to get the count of gross cancels for the month
    // $params is the array of parameters for the SQL query
    // $gross_cancels is the count of gross cancels for the month
    $month = date("Y-m-d", strtotime($month));
    $sql = "SELECT COUNT(sale_id) AS gross_cancels FROM sales_intake as s WHERE MONTH(date_signed) = MONTH('$month') AND YEAR(date_signed) = YEAR('$month') AND status = 2 AND company_id = $company_id " . $back_office_query;
    //echo $sql; // die( );
    $params = array();
    $gross_cancels = DB::queryFirstField($sql, $params);
    return $gross_cancels;
}



function get_net_sales_for_month($month, $company_id, $back_office_query = "")
{
    // Use MeekroDB to get sum of net sales for the month
    // sales_intake.sale_id is the sale_id
    // sales_intake.status is the status
    // sales_intake.debt_amount is the debt_amount
    // sales_intake.date_signed is the date that the deal was signed
    // sales_intake.company_id is the company_id
    // $month is the month for which we want to get the sum of net sales
    // $company_id is the company_id for which we want to get the sum of net sales
    // $net_sales is the sum of net sales for the month
    // $sql is the SQL query to get the sum of net sales for the month
    // $params is the array of parameters for the SQL query
    // $net_sales is the sum of net sales for the month
    $month = date("Y-m-d", strtotime($month));
    $sql = "SELECT SUM(debt_amount) AS net_sales FROM sales_intake as s WHERE MONTH(date_signed) = MONTH('$month') AND YEAR(date_signed) = YEAR('$month') AND status = 0 AND company_id = $company_id " . $back_office_query;
    //echo $sql; // die( );
    $params = array();
    $net_sales = DB::queryFirstField($sql, $params);
    return $net_sales;
}

function get_net_cancels_for_month($month, $company_id, $back_office_query = "")
{
    // Use MeekroDB to get sum of net cancels for the month
    // sales_intake.sale_id is the sale_id
    // sales_intake.status is the status
    // sales_intake.debt_amount is the debt_amount
    // sales_intake.date_signed is the date that the deal was signed
    // sales_intake.company_id is the company_id
    // $month is the month for which we want to get the sum of net cancels
    // $company_id is the company_id for which we want to get the sum of net cancels
    // $net_cancels is the sum of net cancels for the month
    // $sql is the SQL query to get the sum of net cancels for the month
    // $params is the array of parameters for the SQL query
    // $net_cancels is the sum of net cancels for the month
    $month = date("Y-m-d", strtotime($month));
    $sql = "SELECT SUM(debt_amount) AS net_cancels FROM sales_intake as s WHERE MONTH(date_signed) = MONTH('$month') AND YEAR(date_signed) = YEAR('$month') AND status = 2 AND company_id = $company_id " . $back_office_query;
    //echo $sql; // die( );
    $params = array();
    $net_cancels = DB::queryFirstField($sql, $params);
    return $net_cancels;
}

function checkdealStatus($status)
{
    switch ($status) {
        case "0": // this project uses 0 for active status
            $return = "Active";
            break;
        case "1":
            $return = "Pending";
            break;
        case "2":
            $return = "Cancelled";
            break;
        default:
            $return = "Other";
    }
    return $return;
}

function calculateMaxValue($value)
{
    // Calculate the result
    $result = 100 - $value;

    // Take the maximum between 0 and the result
    return max(0, $result);
}

function save_monthly_reports($month)
{
    // Prepare Select Query
    $query = "SELECT 
        s.company_id,
        s.agent_id,
        COUNT(s.sale_id) AS gross_cnt, 
        COUNT(CASE WHEN s.status = 2 THEN s.sale_id END) AS cancels_cnt,
        COUNT(s.sale_id) - COUNT(CASE WHEN s.status = 2 THEN s.sale_id END) AS net_cnt,
        SUM(s.debt_amount) AS total_amount, 
        SUM(s.cancel_amount) AS total_cancel_sales,
        SUM(s.cancel_amount) / SUM(s.debt_amount) AS cxl_percent,
        SUM(s.debt_amount) - SUM(s.cancel_amount) AS net_sales,
        SUM(s.debt_amount) / COUNT(s.sale_id) AS avg_month_deal,
        SUM(DATEDIFF(s.first_draft_date, s.date_signed)) / COUNT(s.sale_id) AS avg_days_to_first_draft,
        'all' AS back_office
    FROM 
        sales_intake s 
    JOIN 
        admin_users a ON a.user_id = s.agent_id 
    WHERE 
        s.status IN (0, 2) AND 
        MONTH(s.date_signed) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND
        YEAR(s.date_signed) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
    GROUP BY 
        s.agent_id";

    // Execute the select query
    $results = DB::query($query);

    // Check if there are results
    if (count($results) > 0) {
        // Iterate over each row
        foreach ($results as $row) {
            // Construct the insert query
            DB::insert('saved_reports', array(
                'month' => $month,
                'company_id' => $row['company_id'],
                'agent_id' => $row['agent_id'],
                'gross_cnt' => $row['gross_cnt'],
                'cxls_cnt' => $row['cancels_cnt'],
                'net_cnt' => $row['net_cnt'],
                'gross_sales' => $row['total_amount'],
                'cxls' => $row['total_cancel_sales'],
                'cxl_percent' => $row['cxl_percent'],
                'net_sales' => $row['net_sales'],
                'avg_month_deal' => $row['avg_month_deal'],
                'avg_days' => $row['avg_days_to_first_draft'],
                'back_office' => 'all'
            ));
        }
    }
}

// Function to check if role_id is allowed
function isRoleAllowed($allowed_roles, $role_id)
{
    return in_array($role_id, $allowed_roles);
}

function get_signature($user_id)
{

    $qry = DB::Query("SELECT * FROM admin_users WHERE user_id = %i", $user_id);

    if ($qry === false) {
        // Handle query execution failure
        echo "Error executing query: " . DB::error();
    } elseif (empty($qry)) {
        // Handle empty result set
        echo "No user details found for user ID: $user_id";
    } else {

        $user_detail = $qry[0];

        if (!empty($user_detail['signature'])) {	// if signature exists
            return $user_detail['signature'];
        } else {									// if signature not exists

            if ($user_detail['company_id'] == 5) {
                ob_start(); // Start output buffering
                ?>

                <!-- Company 5 -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="main01"
                    style=" font-family: Arial, sans-serif; font-size: 14px ;border-collapse: collapse; margin: 0; border:none; padding:0;">
                    <tbody>
                        <tr>
                            <td height="5" style="font-size:1px; line-height:1px;border:none;">
                                <style>
                                    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,600&display=swap');

                                    .main01,
                                    .tableClass {
                                        margin: 0px 0px 0px 0px !important;

                                        padding: 0px 0px 0px 0px !important;
                                    }
                                </style>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="border:none;">
                                <table width="400" border="0" cellspacing="0" cellpadding="0" align="left"
                                    style="max-width:400px;border-collapse: collapse; margin: 0; border:none; padding:0;">
                                    <tbody>
                                        <tr>
                                            <td valign="top" align="left" style="border:none;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                                    style="border-collapse: collapse; margin: 0; border:none; padding:0;">
                                                    <tbody>
                                                        <tr>
                                                            <td valign="bottom" width="110" align="center"
                                                                style="line-height:1px; margin:0px 0px 0px 0px;border:none;">
                                                                <p style="line-height:1px; margin:0px 0px 12px 0px; padding:0px;"><img
                                                                        src="<?php echo $user_detail['user_avatar_url']; ?>" alt="head"
                                                                        width="110" height="110"
                                                                        style="width:110px; height:110px; border:none;"></p>
                                                                <p style="line-height:1px; margin:0px 0px 0px 0px; padding:0px;"><a
                                                                        href="tel:<?php echo $user_detail['user_phone']; ?>"><img
                                                                            src="https://www.uscreditsolutions.com/signatures/btn1.png"
                                                                            alt="head" width="110" height="22"
                                                                            style="width:110px; height:22px; border:none;"></a></p>
                                                            </td>
                                                            <td width="15" style="font-size:1px; line-height:1px;border:none;"
                                                                valign="top">&nbsp;</td>
                                                            <td valign="middle" align="left" style="border:none;">
                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="2" valign="top" align="left"
                                                                                style="font-family:'Open Sans', Arial, Gotham, Helvetica, sans-serif; font-size:13pt; line-height:110%; color:#2c2c2c; text-align:left; border:none; ">
                                                                                <p
                                                                                    style="line-height:1px; margin:0px 0px 0px 0px; padding:0px;">
                                                                                    <img src="https://www.uscreditsolutions.com/signatures/ab-signature.png"
                                                                                        alt="sign" width="130" height="36"
                                                                                        style="width:130px; height:36px; border:none;">
                                                                                </p>
                                                                                <strong><?php echo $user_detail['first_name'] . " " . $user_detail['last_name']; ?></strong>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2" height="10"
                                                                                style="font-size:1px; line-height:1px;border:none;">
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2" valign="top" align="left"
                                                                                style="font-family:'Open Sans', Arial, Gotham, Helvetica, sans-serif; font-size:10pt; line-height:130%; color:#2c2c2c; text-align:left; border:none; ">
                                                                                <?php echo $user_detail['designation']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2" height="8"
                                                                                style="font-size:0pt; line-height:1px;border:none;">
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="bottom"
                                                                                style="padding-left:10px; border-left:2px solid #d30124">
                                                                                <table width="100%" border="0" cellspacing="0"
                                                                                    cellpadding="0"
                                                                                    style="text-align:left;border-collapse: collapse; margin: 0;">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td width="20"
                                                                                                style="line-height:1px;border:none;">
                                                                                                <img src="https://www.uscreditsolutions.com/signatures/call.png"
                                                                                                    alt="icon" width="12" height="12"
                                                                                                    style="border:none; width:12px; height:12px;">
                                                                                            </td>
                                                                                            <td valign="top" align="left"
                                                                                                style="font-family:'Open Sans', Arial, Gotham, Helvetica, sans-serif; font-size:10pt; line-height:100%; color:#000000; text-align:left; font-weight:normal;border:none;">
                                                                                                <a href="tel:<?php echo $user_detail['user_phone']; ?>"
                                                                                                    style="color:#000000; text-decoration:none !important; text-decoration-color: #FFFFFF; font-size:10pt;"><span
                                                                                                        style="color:rgb(0,0,0);"><?php echo $user_detail['user_phone']; ?></span></a>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td colspan="2" height="5"
                                                                                                style="font-size:0pt; line-height:1px;border:none;">
                                                                                            </td>
                                                                                        </tr>

                                                                                        <tr>
                                                                                            <td width="20"
                                                                                                style="line-height:1px;border:none;">
                                                                                                <img src="https://www.uscreditsolutions.com/signatures/email.png"
                                                                                                    alt="icon" width="12" height="12"
                                                                                                    style="border:none; width:12px; height:12px;">
                                                                                            </td>
                                                                                            <td valign="top" align="left"
                                                                                                style="font-family:'Open Sans', Arial, Gotham, Helvetica, sans-serif; font-size:10pt; line-height:100%; color:#000000; text-align:left; font-weight:normal;border:none;">
                                                                                                <a href="mailto:<?php echo $user_detail['user_email']; ?>"
                                                                                                    style="color:#000000; text-decoration:none !important; text-decoration-color: #FFFFFF; font-size:10pt;"><span
                                                                                                        style="color:rgb(0,0,0);"><?php echo $user_detail['user_email']; ?></span></a>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td colspan="2" height="5"
                                                                                                style="font-size:0pt; line-height:1px;border:none;">
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td width="20"
                                                                                                style="line-height:1px;border:none;">
                                                                                                <img src="https://www.uscreditsolutions.com/signatures/web.png"
                                                                                                    alt="icon" width="12" height="12"
                                                                                                    style="border:none; width:12px; height:12px;">
                                                                                            </td>
                                                                                            <td valign="top" align="left"
                                                                                                style="font-family:'Open Sans', Arial, Gotham, Helvetica, sans-serif; font-size:10pt; line-height:100%; color:#000000; text-align:left; font-weight:normal;border:none;">
                                                                                                <a href="http://www.uscreditsolutions.com/"
                                                                                                    target="_blank"
                                                                                                    style="color:#000000; text-decoration:none !important; text-decoration-color: #FFFFFF; font-size:10pt;"><span
                                                                                                        style="color:rgb(0,0,0);">www.uscreditsolutions.com</span></a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>

                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="8" style="font-size:0pt; line-height:1px;border:none;"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd;">
                                                <p style="line-height:0px; margin:0px 0px 0px 0px; padding:0px;"><a
                                                        href="https://uscreditsolutions.com/" target="_blank"><img
                                                            src="https://www.uscreditsolutions.com/signatures/banner.png" alt="banner"
                                                            width="400" height="70" style="width:400px; height:70px; border:none;"></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p style="font-family: Arial, sans-serif; font-size: 14px;">
                    This e-mail, along with any attachments, is covered by the Electronic Communication Privacy Act, 18 U.S.C. Section
                    2510-2521 and is legally privileged. The information contained herein is confidential information intended only for
                    the use of the individual or entity named above. If the reader of this message is not the intended recipient, or if
                    an attachment is made in error, the reader is hereby notified that any dissemination, distribution or copying of
                    this communication is strictly prohibited. If you have received this transmission in error, please notify the above
                    person by telephone immediately, or by return e-mail and delete/trash the original message from your system. Thank
                    you for your cooperation.
                </p>

                <?php
                $signature_html = ob_get_clean(); // Get the output buffer contents and clean the buffer
            } elseif ($user_detail['company_id'] == 3) {
                ob_start(); // Start output buffering
                ?>

                <!-- Company 3 -->
                <table style="font-family: Arial, sans-serif; font-size: 14px;" width="510" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="90" style="border-right:2px solid #000000" valign="top">
                            <p style="font-size:14px;line-height:20px;color:#000000;margin:0px 0px 0px 0px">
                                <a href="https://www.americasfirstfinancial.com/" target="new"><img
                                        src="<?php echo $user_detail['user_avatar_url']; ?>" width="80" height="111"
                                        style="width:80px;height:111px"></a>
                            </p>
                        </td>
                        <td style="padding-left:10px">
                            <p style="font-size:14px;line-height:20px;color:#000000;margin:0px 0px 0px 0px">
                                <b
                                    style="font-size:18px;color:#bf202f"><?php echo $user_detail['first_name'] . " " . $user_detail['last_name']; ?></b><br><b><?php echo $user_detail['designation']; ?></b>
                            </p>
                            <p style="font-size:14px;line-height:23px;color:#000000;margin:5px 0px 0px 0px">
                                <img src="https://www.americasfirstfinancial.com/signatures/Phone-call.png" width="12" height="12"
                                    style="width:12px;height:12px"> <a href="tel:<?php echo $user_detail['user_phone']; ?>" target="new"
                                    style="text-decoration:none;color:#000000"><?php echo $user_detail['user_phone']; ?></a><br>
                                <img src="https://www.americasfirstfinancial.com/signatures/Envelope.png" width="12" height="12"
                                    style="width:12px;height:12px"> <a href="mailto:<?php echo $user_detail['user_email']; ?>"
                                    target="new"
                                    style="text-decoration:none;color:#000000"><?php echo $user_detail['user_email']; ?></a><br>
                                <img src="https://www.americasfirstfinancial.com/signatures/Web.png" width="12" height="12"
                                    style="width:12px;height:12px"> <a href="https://www.americasfirstfinancial.com/" target="new"
                                    style="text-decoration:none;color:#000000">www.americasfirstfinancial.com </a>
                            </p>
                        </td>
                        <td style="padding-left:5px">
                            <p style="font-size:14px;line-height:20px;color:#000000;margin:0px 0px 0px 0px">
                                <a href="https://www.americasfirstfinancial.com/" target="new"><img
                                        src="https://americasfirstfinancial.com/signatures/americas-first-finanical-logo.png"
                                        width="130" height="82" style="width:130px;height:82px"></a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <img src="https://www.americasfirstfinancial.com/signatures/bbb.png" width="308" height="94">
                        </td>
                    </tr>
                </table>

                <p style="font-family: Arial, sans-serif; font-size: 14px;">
                    This e-mail, along with any attachments, is covered by the Electronic Communication Privacy Act, 18 U.S.C. Section
                    2510-2521 and is legally privileged. The information contained herein is confidential information intended only for
                    the use of the individual or entity named above. If the reader of this message is not the intended recipient, or if
                    an attachment is made in error, the reader is hereby notified that any dissemination, distribution or copying of
                    this communication is strictly prohibited. If you have received this transmission in error, please notify the above
                    person by telephone immediately, or by return e-mail and delete/trash the original message from your system. Thank
                    you for your cooperation.
                </p>

                <?php
                $signature_html = ob_get_clean(); // Get the output buffer contents and clean the buffer
            } elseif ($user_detail['company_id'] == 2) {
                ob_start(); // Start output buffering
                ?>

                <!-- Company 2 -->
                <table style="font-family: Arial, sans-serif; font-size: 14px;" border="0" cellpadding="0" cellspacing="0"
                    style="width: 100%;">
                    <tr>
                        <td align="center" style="width: 90px; border:none; border-right: 2px solid #000000;">
                            <a href="https://liberty1financial.com/" style="text-decoration: none;">
                                <img src="<?php echo $user_detail['user_avatar_url']; ?>" width="80" height="111"
                                    alt="Liberty1 Financial" style="display: block;" />
                            </a>
                        </td>
                        <td colspan="3" width="280" style="padding-left: 10px;border:none;">
                            <p style="font-size: 18px; color: #389647; margin: 0 0 5px 0; font-weight: bold;">
                                <?php echo $user_detail['first_name'] . " " . $user_detail['last_name']; ?></p>
                            <p style="font-size: 14px; color: #000000; margin: 0 0 5px 0; font-weight: bold;">
                                <?php echo $user_detail['designation']; ?></p>
                            <p style="font-size: 14px; color: #000000; margin: 0;">
                                <img src="https://liberty1financial.com/signatures/Phone-call.png" width="12" height="12"
                                    alt="Phone Icon" style="display: inline-block; vertical-align: middle;" />
                                <a href="tel:<?php echo $user_detail['user_phone']; ?>" target="_blank"
                                    style="text-decoration: none; color: #000000;"><?php echo $user_detail['user_phone']; ?></a>
                                <br />
                                <img src="https://liberty1financial.com/signatures/Envelope.png" width="12" height="12" alt="Email Icon"
                                    style="display: inline-block; vertical-align: middle;" />
                                <a href="mailto:<?php echo $user_detail['user_email']; ?>" target="_blank"
                                    style="text-decoration: none; color: #000000;"><?php echo $user_detail['user_email']; ?></a>
                                <br <img src="https://liberty1financial.com/signatures/Web.png" width="12" height="12" alt="Web Icon"
                                    style="display: inline-block; vertical-align: middle;" />
                                <a href="https://www.liberty1financial.com/" target="_blank"
                                    style="text-decoration: none; color: #000000;">www.liberty1financial.com</a>
                            </p>
                        </td>
                        <td style="padding-left: 5px;border:none;">
                            <p style="font-size: 14px; line-height: 20px; color: #000000; margin: 0 0 15px 0;border:none;">
                                <a style="text-decoration: none;" href="https://www.liberty1financial.com/" target="_blank">
                                    <img src="https://liberty1financial.com/signatures/Liberty1Financial-logo.png" width="130"
                                        height="82" alt="Liberty1 Financial Logo" style="width: 130px; height: 82px; display: block;">
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border:none;">
                            <!-- <a href="https://www.bbb.org/us/ca/irvine/profile/financial-consultants/liberty1-financial-1126-1000072942/#sealclick" target="_blank" rel="nofollow"> -->
                            <img src="https://liberty1financial.com/signatures/bbb-new.png" width="124" height="93" alt="BBB Logo">
                            <!-- </a> -->
                            <img src="https://liberty1financial.com/signatures/veteran-owned-biz.png" width="94" height="93"
                                alt="Veteran Owned">
                            <a style="text-decoration: none;"
                                href="https://www.inc.com/liberty1financial/lenders-on-a-mission-when-your-success-secret-is-empowering-consumers.html"
                                target="_blank"><img src="https://liberty1financial.com/mail/liberty-7/inc-r-1.png" height="93"
                                    alt=""></a>
                            <a style="text-decoration: none;" href="https://www.inc.com/inc5000/2024" target="_blank"><img
                                    src="https://liberty1financial.com/mail/liberty-7/inc-r-2.png" height="93" alt=""></a>
                        </td>
                    </tr>
                </table>

                <p style="font-family: Arial, sans-serif; font-size: 14px;">
                    This e-mail, along with any attachments, is covered by the Electronic Communication Privacy Act, 18 U.S.C. Section
                    2510-2521 and is legally privileged. The information contained herein is confidential information intended only for
                    the use of the individual or entity named above. If the reader of this message is not the intended recipient, or if
                    an attachment is made in error, the reader is hereby notified that any dissemination, distribution or copying of
                    this communication is strictly prohibited. If you have received this transmission in error, please notify the above
                    person by telephone immediately, or by return e-mail and delete/trash the original message from your system. Thank
                    you for your cooperation.
                </p>

                <?php
                $signature_html = ob_get_clean(); // Get the output buffer contents and clean the buffer
            } else {	// Unlisted Company
                $signature_html = "";
            }

            return $signature_html;
        }
    }
}

function abbreviateCompanyName($name)
{
    $words = explode(' ', $name);
    $abbreviation = '';
    foreach ($words as $index => $word) {
        if ($index === 0 && ctype_alnum($word)) {
            $abbreviation .= $word;
        } elseif (!empty($word) && ctype_alpha($word[0])) {
            $abbreviation .= strtoupper($word[0]);
        }
    }

    return strtoupper($abbreviation);
}
function getUserSFID($agentEmail)
{
    // Check if the provided email is empty or null
    if (empty($agentEmail)) {
        return null;
    }

    try {
        // Prepare the query to prevent SQL injection
        $query = "SELECT sf_user_id FROM admin_users WHERE user_email = %s";

        // Execute the query and fetch the result
        $agent_sf_id = DB::queryFirstField($query, $agentEmail);

        // Return the result, or null if no result was found
        return $agent_sf_id ?: null;
    } catch (Exception $e) {
        // Log the exception (optional) and return null
        error_log("Error fetching SFID for user: " . $e->getMessage());
        return null;
    }
}

// Function to generate API token
function generateAndStoreApiToken($userId, $expiryHours = 24)
{
    // Generate a random secure token (use bin2hex for a readable string)
    $token = bin2hex(random_bytes(32));

    // Calculate expiration time (optional)
    $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours"));

    // Store token in database
    DB::insert('api_tokens', [
        'user_id' => $userId,
        'token' => password_hash($token, PASSWORD_BCRYPT), // Secure storage
        'expires_at' => $expiresAt
    ]);

    return $token; // Return the unhashed token for user usage
}

// Function to validate API token
function validateApiToken($authHeader)
{
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return false; // if "Bearer" format match not found  invalid
    }

    $token = $matches[1]; // Bearer token extract

    // Token verify in database
    $row = DB::queryFirstRow("SELECT * FROM api_tokens WHERE token IS NOT NULL");

    if (!$row) {
        return false; // Token not found
    }

    // Hashed token verify
    if (!password_verify($token, $row['token'])) {
        return false;
    }

    // Expiration check
    if ($row['expires_at'] && strtotime($row['expires_at']) < time()) {
        return false; // Token expired
    }

    return $row['user_id'];
    return true; // Valid token return true
}

// Multiple languages
// Default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en'; // Default English
}

// Function to get translation
function lang($key)
{
    $lang = $_SESSION['lang'];
    $translations = include "lang/{$lang}.php";
    return $translations[$key] ?? $key; // Agar translation na mile to key return ho
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendHRNotificationEmail($applicantData, $applicant_id)
{
    $phpmailer = new PHPMailer(true);

    try {

        $positions = DB::query("
        SELECT positions.position_name 
        FROM applicants 
        LEFT JOIN positions ON FIND_IN_SET(positions.id, applicants.position)
        WHERE applicants.id = %i
    ", $applicant_id);

        // Extract position names
        $positionNames = array_column($positions, 'position_name');
        $applicantPosition = implode(', ', array_filter($positionNames));

        // Update applicant data with position names
        $applicantData['position'] = $applicantPosition;

        // Server settings
        // Looking to send emails in production? Check out our Email API/SMTP product!
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        // $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = 'username';
        $phpmailer->Password = 'password';

        // Recipients
        $phpmailer->setFrom('youremail@gmail.com', 'Job Portal');
        $phpmailer->addAddress('email@gmail.com', 'HR Department');

        // Content
        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'New Job Application Received';

        // Prepare the HTML table for applicant data
        $phpmailer->Body =
            "
        <html>
        <head>
    <style>
    /* Base Mobile First Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 15px;
        color: #444;
        line-height: 1.5;
        min-width: 320px; /* Minimum mobile width */
    }
    
    .container {
        width: 100%;
        max-width: 100%;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
        box-sizing: border-box;
    }

    h3 {
        color: #fe5500;
        margin: 0 0 1.2rem 0;
        font-size: 1.6rem;
        font-weight: 600;
        line-height: 1.3;
    }

    .info-table {
        width: 100%;
        margin: 25px 0;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .info-table tr {
        border-bottom: 1px solid #eee;
        display: block;
        margin-bottom: 15px;
    }

    .info-table td {
        display: block;
        padding: 12px 0;
        font-size: 1rem;
        word-break: break-word;
    }

    .label {
        color: #666;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 0.95rem;
    }

    .value {
        color: #333;
        font-size: 1rem;
    }

    .cta-link {
        display: block;
        width: 100%;
        background-color: #fe5500;
        color: #fff !important;
        padding: 14px 20px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        margin: 25px 0;
        transition: all 0.3s ease;
    }

    .cta-link:hover {
        background-color: #d94600;
        transform: translateY(-1px);
    }

    .footer {
        margin-top: 30px;
        font-size: 0.9rem;
        color: #666;
        border-top: 1px solid #eee;
        padding-top: 20px;
        text-align: center;
    }

    /* Small Tablets (600px and up) */
    @media (min-width: 600px) {
        .container {
            padding: 25px;
            max-width: 90%;
        }
        
        .info-table td {
            padding: 14px 0;
        }
        
        .cta-link {
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    }

    /* Large Tablets/Small Laptops (768px and up) */
    @media (min-width: 768px) {
        body {
            padding: 25px;
        }
        
        .container {
            padding: 35px;
            max-width: 700px;
        }

        .info-table tr {
            display: table-row;
            margin-bottom: 0;
        }

        .info-table td {
            display: table-cell;
            padding: 16px 0;
            vertical-align: top;
        }

        .label {
            width: 30%;
            min-width: 150px;
            padding-right: 25px;
            white-space: normal;
        }

        .cta-link {
            width: auto;
            display: inline-block;
            padding: 14px 35px;
        }
    }

    /* Laptops/Desktops (992px and up) */
    @media (min-width: 992px) {
        .container {
            padding: 40px;
            max-width: 800px;
        }

        h3 {
            font-size: 1.8rem;
        }

        .info-table td {
            padding: 18px 0;
            font-size: 1.05rem;
        }

        .label {
            font-size: 1rem;
        }
    }

    /* Large Desktops (1200px and up) */
    @media (min-width: 1200px) {
        .container {
            max-width: 900px;
            padding: 45px;
        }
        
        .info-table {
            margin: 30px 0;
        }
    }
</style>
        </head>
        <body>
            <div class='container'>
                <h3>Dear HR,</h3>
                <p>We are pleased to inform you that a new job application has been submitted. Please find the applicant's details below:</p>
<table class='info-table'>
    <tr>
        <td>
            <span class='label'>Applied For:</span>
            <span class='value'>{$applicantData['position']}</span>
        </td>
        <td>
            <span class='label'>Name:</span>
            <span class='value'>{$applicantData['name']}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class='label'>Email:</span>
            <span class='value'>{$applicantData['email']}</span>
        </td>
        <td>
            <span class='label'>Phone:</span>
            <span class='value'>{$applicantData['phone']}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class='label'>City:</span>
            <span class='value'>{$applicantData['city']}</span>
        </td>
        <td>
            <span class='label'>Skills:</span>
            <span class='value'>{$applicantData['skills']}</span>
        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <a href='http://localhost/craftHiring/index.php?route=modules/applicants/view_applicant_new&id={$applicant_id}' class='cta-link' class='cta-link'>
                View Full Application
            </a>
        </td>
    </tr>
</table>

                <p>Please review the application at your earliest convenience.</p>

                <div class='footer'>
                  <strong>Best regards</strong>
                </div>
            </div>
        </body>
        </html>";

        $phpmailer->send();
        // Optional: echo "Notification sent to HR!";
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$phpmailer->ErrorInfo}");
    }
}


    // File handling function
    function saveLicenseFile($file, $applicantId, $side, $uploadDir)
    {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type for {$side} license");
        }

        // Generate safe filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = sprintf(
            "%s_%s_%s.%s",
            $applicantId,
            $side,
            bin2hex(random_bytes(4)),
            $ext
        );

        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to save {$side} license file");
        }

        return $filename;
    }


    function showAlertRedirect($icon, $title, $text, $redirectUrl)
    {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text'
            }).then(() => {
                window.location.href = '$redirectUrl';
            });
        </script>";
        exit();
    }


function sendReportApprovalEmail($reportId) {
    // Fetch report details
    $report = DB::queryFirstRow("
        SELECT dwr.*, j.job_title, u.name AS manager_name
        FROM daily_work_reports dwr
        LEFT JOIN job j ON dwr.job_id = j.id
        LEFT JOIN users u ON dwr.foreman_id = u.user_id
        WHERE dwr.id = %i
    ", $reportId);

    if (!$report) return false;

    $adminEmail = 'admin@example.com'; // ACTUAL ADMIN EMAIL HERE
    $reportDate = date('F j, Y', strtotime($report['report_date']));
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings - USE ACTUAL SMTP CREDENTIALS
        $mail->isSMTP();
        $mail->Host = 'smtp.yourprovider.com';  // REAL SMTP SERVER
        $mail->SMTPAuth = true;
        $mail->Port = 587;                     // COMMON PORT: 587 (TLS) or 465 (SSL)
        $mail->Username = 'your@email.com';    // ACTUAL EMAIL
        $mail->Password = 'yourpassword';      // ACTUAL PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Or ENCRYPTION_SMTPS for SSL
        
        // Enable debugging for troubleshooting
        $mail->SMTPDebug = 2; // Level 2 for client/server communication
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP (level $level): $str");
        };

        // Recipients
        $mail->setFrom('reports@yourcompany.com', 'Daily Report System');
        $mail->addAddress($adminEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Daily Report Approved - {$report['job_title']} - $reportDate";

        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; }
                    .header { background-color: #fd7e14; padding: 20px; color: white; }
                    .content { padding: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Daily Work Report Approved</h2>
                    </div>
                    <div class='content'>
                        <p>A daily work report has been approved by the manager:</p>
                        
                        <p><strong>Job Site:</strong> {$report['job_title']}</p>
                        <p><strong>Report Date:</strong> $reportDate</p>
                        <p><strong>Approved by:</strong> {$report['manager_name']}</p>
                        <p><strong>Total Head Count:</strong> {$report['craft_count']} workers</p>
                        
                        <p>You can review the full report in the admin panel.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        // Plain text alternative
        $mail->AltBody = "Daily Report Approved\n\n"
                       . "Job Site: {$report['job_title']}\n"
                       . "Report Date: $reportDate\n"
                       . "Approved by: {$report['manager_name']}\n"
                       . "Total Head Count: {$report['craft_count']} workers\n\n"
                       . "View in admin panel";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}