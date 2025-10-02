<?php
//Returns Date given in Selected Format
function getDateTime($time = 0, $form = "dtLong") {
	Switch($form) {
		case "dtVLong":
		$strform = "D, jS F, Y g:i:s A (\G\M\T O)";
		break;
		case "dtLong":
		$strform = "D, jS F, Y g:i A";
		break;
		case "dtShort":
		$strform = "jS M, Y g:i A";
		break;
		case "dtMin":
		$strform = "j-n-y G:i";
		break;
		case "dLong":
		$strform = "D, jS F Y";
		break;	
		case "dShort":
		$strform = "j-M-Y";
		break;
		case "dMin":
		$strform = "j-n-y";
		case "dOnly":
		$strform = "Y-n-j";
		break;
		case "tLong":
		$strform = "G:i:s (\G\M\T O)";
		break;
		case "tShort":
		$strform = "G:i";
		break;
		case "mySQL":
		$strform = "Y-m-d H:i:s";
		break;
		case "MonthYear":
		$strform = "F-Y";
		break;
		default:
		$strform = "j-M-Y g:ia";	
	}
	if ($time == 0 ){	
	$formated_time = date($strform);
	} else {
	$time = strtotime($time);	
	$formated_time  = date($strform, $time);
	}
	return $formated_time;
}

function get_current_week_start_end_date(){
    $monday = strtotime("last monday");
    $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
    $sunday = strtotime(date("Y-m-d",$monday)." +5 days");
    $this_week_start = date("Y-m-d",$monday);
    $this_week_end = date("Y-m-d",$sunday);
    $currentWeek = array('start_week'=>$this_week_start,'end_week'=>$this_week_end);
    return $currentWeek;
}
function getDatesFromRange($start, $end, $format = 'Y-m-d') {
    $array = array();
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach($period as $date) { 
        $array[] = $date->format($format); 
    }

    return $array;
}
function time_elapsed_string($ptime, $full = false) {
	date_default_timezone_set("Asia/Karachi");
    // Past time as MySQL DATETIME value
//	echo $ptime;
    //$ptime = date("Y-m-d H:i:s",strtotime($ptime));//strtotime($ptime);die(1);
	 $ptime = strtotime($ptime);

    // Current time as MySQL DATETIME value
$csqltime = date('Y-m-d H:i:s');
	
    // Current time as Unix timestamp
    $ctime = strtotime($csqltime); 

    // Elapsed time
    $etime = $ctime - $ptime;
	//$etime = $ptime - $ctime;

    // If no elapsed time, return 0
    if ($etime < 1){
        return '0 seconds';
    }

    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                // 1  =>  'second'
    );

    $a_plural = array( 'year'   => 'Y',
                       'month'  => 'M',
                       'day'    => 'D',
                       'hour'   => 'hrs',
                       'minute' => 'min',
                      // 'second' => 'Sec'
    );
$estring = '';
    foreach ($a as $secs => $str){
        // Divide elapsed time by seconds
		
        $d = $etime / $secs;
        if ($d >= 1){
            // Round to the next lowest integer 
            $r = floor($d);
            // Calculate time to remove from elapsed time
            $rtime = $r * $secs;
            // Recalculate and store elapsed time for next loop
            if(($etime - $rtime)  < 0){
                $etime -= ($r - 1) * $secs;
            }
            else{
                $etime -= $rtime;
            }
            // Create string to return
            $estring = $estring . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ';
        }
    }
    return $estring . ' ago';
}

// To Prevent SQL injection
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

function get_domain($url)
{
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  return false;
}


function get_host() {
    if ($host = $_SERVER['HTTP_X_FORWARDED_HOST'])
    {
        $elements = explode(',', $host);
		
        $host = trim(end($elements));
    }
    else
    {
        if (!$host = $_SERVER['HTTP_HOST'])
        {
            if (!$host = $_SERVER['SERVER_NAME'])
            {
                $host = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
            }
        }
    }
	
    // Remove port number from host
    $host = preg_replace('/:\d+$/', '', $host);
	
    return trim($host);
}
function round2dp($number){
		return number_format((float)$number, 2, '.', ',');
	}
function round0dp($number){
		return number_format((float)$number, 0, '.', ',');
	}
function col_index($string , $line){
	
		$found = 0;
		$i = 0;
		while ($found == 0 && $i < count ($line))
		{
			if ($line[$i] == $string)
			{
					$found = 1;
			}
			else
			{
				$i = $i + 1;
			}
		}
		if ($found ==0) return -1;
		else return $i;
		
	}

function parseTree($root, $arr) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach($arr as $child => $parent) {
            # A direct child is found
            if($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($arr[$child]);
                # Append the child into result array and parse it's children
                $return[] = array(
                    'name' => $child,
                    'children' => parseTree($child, $arr)
                );
            }
        }
        return empty($return) ? null : $return;    
    }
    
function printTree($arr) {
        if(!is_null($arr) && count($arr) > 0) {
            echo '<ul>';
            foreach($arr as $node) {
                echo "<li>".  $node['sect_name'] . "";
                if (array_key_exists('children', $node)) {
                    printTree($node['children']);
                }
                echo '</li>';
            }
            echo '</ul>';
        }
    }

function is_serialized( $data ) {
        
        if (!is_string($data)) {
            return false;
        }
        $data = trim( $data );
        if ('N;' == $data) {
            return true;
        }
        if (!preg_match('/^([adObis]):/', $data, $badions)) {
            return false;
        }
        switch ( $badions[1] ) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                    return true;
                }
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                    return true;
                }
                break;
        }
        return false;
    }
    
    function displayPaginationBelow($per_page,$page,$sql,$page_url){
        DB::query($sql);
        $total = DB::count();
        $adjacents = "2";
        
        $page = ($page == 0 ? 1 : $page);
        $start = ($page - 1) * $per_page;
        
        $prev = $page - 1;
        $next = $page + 1;
        $setLastpage = ceil($total/$per_page);
        $lpm1 = $setLastpage - 1;
        
        $setPaginate = "";
        if($setLastpage > 1)
        {
            $setPaginate .= "<ul class='setPaginate'>";
            $setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
            if ($setLastpage < 7 + ($adjacents * 2))
            {
                for ($counter = 1; $counter <= $setLastpage; $counter++)
                {
                    if ($counter == $page)
                        $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
                        else
                            $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
                }
            }
            elseif($setLastpage > 5 + ($adjacents * 2))
            {
                if($page < 1 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
                            else
                                $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
                    }
                    $setPaginate.= "<li class='dot'>...</li>";
                    $setPaginate.= "<li><a href='{$page_url}page=$lpm1'>$lpm1</a></li>";
                    $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>$setLastpage</a></li>";
                }
                elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $setPaginate.= "<li><a href='{$page_url}page=1'>1</a></li>";
                    $setPaginate.= "<li><a href='{$page_url}page=2'>2</a></li>";
                    $setPaginate.= "<li class='dot'>...</li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
                            else
                                $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
                    }
                    $setPaginate.= "<li class='dot'>..</li>";
                    $setPaginate.= "<li><a href='{$page_url}page=$lpm1'>$lpm1</a></li>";
                    $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>$setLastpage</a></li>";
                }
                else
                {
                    $setPaginate.= "<li><a href='{$page_url}page=1'>1</a></li>";
                    $setPaginate.= "<li><a href='{$page_url}page=2'>2</a></li>";
                    $setPaginate.= "<li class='dot'>..</li>";
                    for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
                    {
                        if ($counter == $page)
                            $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
                            else
                                $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
                    }
                }
            }
            
            if ($page < $counter - 1){
                $setPaginate.= "<li><a href='{$page_url}page=$next'>Next</a></li>";
                $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>Last</a></li>";
            }else{
                $setPaginate.= "<li><a class='current_page'>Next</a></li>";
                $setPaginate.= "<li><a class='current_page'>Last</a></li>";
            }
            
            $setPaginate.= "</ul>\n";
        }
        
        
        return $setPaginate;
    }




function make_bitly_url($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
{
    //create the URL
    $bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
    
    //get the url
    //could also use cURL here
    $response = file_get_contents($bitly);
    
    //parse depending on desired format
    if(strtolower($format) == 'json')
    {
        $json = @json_decode($response,true);
        return $json['results'][$url]['shortUrl'];
    }
    else //xml
    {
        $xml = simplexml_load_string($response);
        return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
    }
}

function get_tick($value){
		if ($value == 1) {
	  	 	$symbol = '<span class="glyphicon glyphicon-ok text-center"></span>';
		} else {
	    	$symbol = '<span class="glyphicon glyphicon-remove"></span>';
		}
	return $symbol;
}


function getAPI( $url, $data){
   $url = sprintf("%s?%s", $url, http_build_query($data));
   echo $url."<br>";
   // EXECUTE:
   $result = file_get_contents($url);
   //print_r(curl_getinfo($curl));
   if(!$result){die("Connection Failure");}
 
   return $result;
}
function callAPI($method, $url, $data){
   $curl = curl_init();
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data) 
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }
   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
 	
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
   curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
   curl_setopt($curl, CURLOPT_ENCODING, '');
   // EXECUTE:
   $result = curl_exec($curl);
   //print_r(curl_getinfo($curl));
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}

function truncate($string,$length=100,$append="&hellip;") {
  $string = trim($string);

  if(strlen($string) > $length) {
    $string = wordwrap($string, $length);
    $string = explode("\n", $string, 2);
    $string = $string[0] . $append;
  }

  return $string;
}
function ShowYesNo($pass_value)
{

	if ($pass_value==1)
		return "YES";
	elseif ($pass_value==0)
		return "NO";
	elseif ($pass_value==-1)
		return "UNKNOWN";
}
function ShowTickCross($pass_value)
{

	if ($pass_value==1)
		return " <i class='fa fa-check-square' ></i> ";
	elseif ($pass_value==0)
		return "<i class='fa fa-times-circle' ></i>";
	elseif ($pass_value==-1)
		return "<i class='fa fa-question-circle' ></i>";
}
function as_money($number, $decimal = 0) {
    if (is_null($number)) {
        $number = 0;
    }
    // Ensure $number is a float
    $number = (float)$number;
    
    $output = number_format($number, $decimal, ".", ",");
    if ($number < 0) {
        $output = "(" . number_format(abs($number), $decimal, ".", ",") . ")";
    }
    return $output;
}

function strToDecimal($str)
{
	
	// Remove dollar sign and comma and leading spaces
	$str = trim($str);
	if ($str <> '') {
		$str = str_replace(['$', ','], '', $str);
		// Convert to decimal number with two digits
		$number =  (float) $str ;
		return $number;
	} else {
		return $str;
	}
}
/**
 * Converts a string representing a month and year to a YYYY-MM format.
 *
 * @param string $monthYear A string representing a month and year in various formats.
 * @return string The month and year in YYYY-MM format.
 */
function convertToYYYYMM($monthYear) {
    // List of potential formats we want to recognize
    $formats = [
        "F Y",      // August 2023
        "M Y",      // Aug 2023
        "m/Y",      // 08/2023
        "m-Y",      // 08-2023
        "Y-m",      // 2023-08
        "Y/m",      // 2023/08
        "m.Y",      // 08.2023
        "F, Y",     // August, 2023
        "M, Y",     // Aug, 2023
        "F'y",      // August'23
        "M-y",      // Aug-23
        "M'y"       // Aug'23
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $monthYear);
        if ($date) {
            return $date->format('Y-m');
        }
    }
    
    // If no match, default to last month and current year
    $currentDate = new DateTime();
    $currentDate->modify('-1 month');
    return $currentDate->format('Y-m');
}

//get first date of the current month

function getFirstDateofCurrentMonth(){
    $first_date = date('m/01/Y');
    return $first_date;
}
// get last date of the current month
function getLastDateofCurrentMonth(){
    $last_date = date('m/t/Y');
    return $last_date;
}

// get current date of month
function getCurrentDate(){
    $current_date = date('m/d/Y');
    return $current_date;
}
// get short text of a string 
function shorten_text($string, $wordsreturned)
        {
            $retval = $string;  //  Just in case of a problem
            $array = explode(" ", $string);
            /*  Already short enough, return the whole thing*/
            if (count($array)<=$wordsreturned)
            {
                $retval = $string;
            }
            /*  Need to chop of some words*/
            else
            {
                array_splice($array, $wordsreturned);
                $retval = implode(" ", $array)." ...";
            }
            return $retval;
        }              

        function sanitize($value) {
            if (is_array($value)) {
                // If $value is an array, apply sanitize function recursively to each element
                return array_map('sanitize', $value);
            } else if (is_string($value)) {
                // If $value is a string, sanitize it using mb_convert_encoding
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            } else {
                // If $value is neither an array nor a string, return it as is (or handle as needed)
                return $value;
            }
        }

// To get light colors
function hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if (empty($color))
		return $default;

	//Sanitize $color if "#" is provided
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}

	//Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		list($red, $green, $blue) = [$color[0].$color[1], $color[2].$color[3], $color[4].$color[5]];
	} elseif ( strlen( $color ) == 3 ) {
		list($red, $green, $blue) = [$color[0].$color[0], $color[1].$color[1], $color[2].$color[2]];
	} else {
		return $default;
	}

	//Convert HEX to DEC
	$red = hexdec($red);
	$green = hexdec($green);
	$blue = hexdec($blue);

	//Check if opacity is set(rgba or rgb)
	if($opacity !== false){
		return 'rgba('.$red.','.$green.','.$blue.','.$opacity.')';
	} else {
		return 'rgb('.$red.','.$green.','.$blue.')';
	}
}


// Function to get full state name from abbreviation
function getStateFullName($abbr) {
    // Associative array mapping state abbreviations to full names
    $states = [
        "AL" => "Alabama",
        "AK" => "Alaska",
        "AZ" => "Arizona",
        "AR" => "Arkansas",
        "CA" => "California",
        "CO" => "Colorado",
        "CT" => "Connecticut",
        "DE" => "Delaware",
        "FL" => "Florida",
        "GA" => "Georgia",
        "HI" => "Hawaii",
        "ID" => "Idaho",
        "IL" => "Illinois",
        "IN" => "Indiana",
        "IA" => "Iowa",
        "KS" => "Kansas",
        "KY" => "Kentucky",
        "LA" => "Louisiana",
        "ME" => "Maine",
        "MD" => "Maryland",
        "MA" => "Massachusetts",
        "MI" => "Michigan",
        "MN" => "Minnesota",
        "MS" => "Mississippi",
        "MO" => "Missouri",
        "MT" => "Montana",
        "NE" => "Nebraska",
        "NV" => "Nevada",
        "NH" => "New Hampshire",
        "NJ" => "New Jersey",
        "NM" => "New Mexico",
        "NY" => "New York",
        "NC" => "North Carolina",
        "ND" => "North Dakota",
        "OH" => "Ohio",
        "OK" => "Oklahoma",
        "OR" => "Oregon",
        "PA" => "Pennsylvania",
        "RI" => "Rhode Island",
        "SC" => "South Carolina",
        "SD" => "South Dakota",
        "TN" => "Tennessee",
        "TX" => "Texas",
        "UT" => "Utah",
        "VT" => "Vermont",
        "VA" => "Virginia",
        "WA" => "Washington",
        "WV" => "West Virginia",
        "WI" => "Wisconsin",
        "WY" => "Wyoming",
        "DC" => "Washington DC"
    ];

    // Return the full state name or null if abbreviation not found
    return $states[strtoupper($abbr)] ?? null;
}

 
//calculate difference of months
function calculateMonthsDifference($specificDate)
{
    $currentDate = new DateTime();
    $givenDate = new DateTime($specificDate);

    $interval = $currentDate->diff($givenDate);
    $monthsDifference = $interval->y * 12 + $interval->m;

    return $monthsDifference;
}
