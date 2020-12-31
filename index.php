<?php

// Global variables
include_once 'distance.php';
include_once 'coordinates.php';

global $distance;
global $coordinates;

/**
 * @param int $from
 * @param int $to
 * @param array $dist
 *
 * @return int
 */
function CalculateDistance($from, $to, $dist = []) {
    global $distance;
    if (empty($dist)) {
        $dist = $distance;
    }

    $from = (int) $from;
    $to = (int) $to;

    if ($from === $to) {
        return 0;
    }
    return (int) $dist[$from][$to];
}

/**
 * @param int $from
 * @param int $to
 * @param array $coo
 *
 * @return float|int
 */
function CalculateDistanceFromLocation($from, $to, $coo = []) {
    global $coordinates;
    if (empty($coo)) {
        $coo = $coordinates;
    }

    $from = (int) $from;
    $to = (int) $to;

    if ($from === $to) {
        return 0;
    }

    $diff_x = abs($coo[$from]['x'] - $coo[$to]['x']);
    $diff_y = abs($coo[$from]['y'] - $coo[$to]['y']);

    return round(hypot($diff_x, $diff_y));
}

/**
 * @param array $list
 */
function ValidateDistances($list) {
    foreach ($list as $k1 => $point1) {
        foreach ($list as $k2 => $point2) {
            if ($list[$point1][$point2] !== $list[$point2][$point1]) {
                print "Error on value \$distance[" . $point1 . "][" . $point2 . "]";
                exit;
            }
        }
    }
}

/**
 * @param array $array
 * @return string
 */
function ArrayToString($array) {
    return implode(",", $array);
}

/**
 * @param array $array
 * @return array
 */
function FixIntArray($array) {
    $fixed_array = [];
    foreach ($array as $key => $value) {
        $fixed_array[$value] = $value;
    }
    return $fixed_array;
}

/**
 * @param array $array
 * @return array
 */
function NormalizeArrayKeys($array) {
    $fixed_array = [];
    foreach ($array as $key => $value) {
        $fixed_array[] = $value;
    }
    return $fixed_array;
}

/**
 * @param array $array
 * @param array $result
 * @return array
 */
function GetMinFromIntegerArray($array, $result) {
    $min = min($array);
    foreach ($array as $key => $value) {
        if ($value === $min) {
            return $result[$key];
        }
    }
    return [];
}

/**
 * Calculate distance but do not pass from the same location more than once.
 *
 * @param int $start_node
 * @param int $end_node
 * @param array $locations
 *
 * @return array
 */
function GetRandomRoute($start_node, $end_node, $locations) {
    // Manipulate arrays
    $fixed_locations = FixIntArray($locations);
    unset($fixed_locations[$start_node], $fixed_locations[$end_node]);

    // Random order of keys without start and end Node
    $keys = array_keys($fixed_locations);
    shuffle($keys);
    $random = [];
    foreach ($keys as $key) {
        $random[$key] = $fixed_locations[$key];
    }
    array_unshift($random, $start_node);
    $random[] = $end_node;
    $random = NormalizeArrayKeys($random);

    // Start distance calculations
    $results = [
        'distance' => 0,
        'route' => "",
    ];
    foreach ($random as $key => $value) {
        $results['route'] .= $value;
        if (isset($random[$key + 1])) {
            $next_node = $random[$key + 1];
//            $results['distance'] += CalculateDistance($value, $next_node);
            $results['distance'] += CalculateDistanceFromLocation($value, $next_node);
            $results['route'] .= "-";
        }
    }

    return $results;
}

/**
 * @param int $start_node
 * @param int $end_node
 * @param array $list
 * @param int $loops
 *
 * @return array
 */
function GetMinDistance($start_node, $end_node, $list, $loops = 10000) {
    $output = [];
    $count = [];
    for ($i = 1; $i <= $loops; $i++) {
        // Get random calculations
        $output[$i] = GetRandomRoute($start_node, $end_node, $list);
        $count[$i] = $output[$i]['distance'];
    }

    /**
     * $results = [
     *   'distance' => 984,
     *   'route' => "1-3-4-2-5"
     * ];
     */

    return GetMinFromIntegerArray($count, $output);
}

/**
 * @param int $start_node
 * @param int $end_node
 * @param array $list
 * @param int $loops
 *
 * @return array
 */
function OptimizedGetMinDistance($start_node, $end_node, $list, $loops = 50000) {
    $get1 = GetMinDistance($start_node, $end_node, $list, $loops);
    // Reverse Lookup
    $get2 = GetMinDistance($end_node, $start_node, $list, $loops);

    if ($get1['distance'] === $get2['distance']) {
        return $get1;
    }
    return OptimizedGetMinDistance($start_node, $end_node, $list, $loops);
}

/**
 * @param string $list
 * @return string
 */
function DisplayRouteWithDistances($list) {
    $array = explode("-", $list);
    return GenerateTableOfDistances($array);
}

/**
 * @param string $list
 *
 * @return string
 */
function DisplayReverseRouteWithDistances($list) {
    $array = explode("-", $list);
    $reversed_array = array_reverse($array);
    return GenerateTableOfDistances($reversed_array);
}

/**
 * @param array $array
 *
 * @return string
 */
function GenerateTableOfDistances($array) {
    $output = "";
    foreach ($array as $key => $value) {
        if (isset($array[$key+1])) {
            $to = (int) $array[$key+1];
            $from = (int) $value;
            $calc = CalculateDistanceFromLocation($from, $to);
            $output .= $from . "-" . $to . ": " . $calc . "<br>";
        }
    }
    return $output;
}

// Final calculations
$all_values = [1,2,3,4,5,6,7,8,9,10];

$start = $_GET['start'];
$end = $_GET['end'];
$list = $_GET['list'];
$loops = $_GET['loops'];

if (!isset($start)) {
    $start = 1;
}

if (!isset($end)) {
    $end = 10;
    $list = $all_values;
    $loops = 10000;
}

if (!isset($list)) {
    $list_options = $all_values;
} else {
    foreach ($list as $option) {
        $list_options[$option] = $option;
    }
    if (!isset($list_options[$start])) {
        print_r("<p style='width:300px;border:1px solid;color:red;padding:10px;'><b>Please include the start value ".$start." on the list.</b></p>");
    }
    if (!isset($list_options[$end])) {
        print_r("<p style='width:300px;border:1px solid;color:red;padding:10px;'><b>Please include the end value ".$end." on the list.</b></p>");
    }
}

if (!isset($loops)) {
    $loops = 10000;
}


print "<html>";
print "<head>";
print "<style>";
print "
body {
  background: whitesmoke;
  padding: 20px;
  font-size: 18px;
  font-family: sans-serif;
}

select {
  height: 36px;
  margin-left: 10px;
  overflow: hidden;
}

select option {
  display: inline-block;
  width: 20px;
  height: 20px;
  text-align: center;
  margin: 2px;
  padding: 5px;
  cursor: pointer;
}

.button {
  background: #006fff;
  border: 2px solid #cacaca;
  padding: 10px 20px;
  color: #fff;
  font-weight: bold;
  border-radius: 5px;
}

li {
  margin-bottom: 10px;
}

.logo {
  height: 200px;
  with: auto;
  margin-bottom: 20px;
}
";
print "</style>";
print "</head>";
print "<body>";

// Print header
// print "<h1>The Faster Route Game</h1>";
print "<img class='logo' src='logo.svg' alt='logo' />";
print "<hr>";

$about = "";

// Print links
print "<ul>";
print "<li>Get Board <a href='board.png' target='_blank'>png</a>, <a href='board.svg' target='_blank'>svg</a></li>";
print "<li><a href='https://github.com/theodorosploumis/game-faster-route/blob/master/README.md' target='_blank'>About/Rules</a></li>";
print "</ul>";

print "<hr>";
print "<br><br>";

// Generate Form
$form = "<form>";
$form .= '<label for="start">Start (1 to 10):</label> ';
$form .= "<input type='number' value=".$start." id='start' name='start' min='1' max='10' step='1' maxlength='2'> ";
$form .= "<br><br>";

$form .= '<label for="end">End (1 to 10):</label> ';
$form .= "<input type='number' value=".$end." id='end' name='end' min='1' max='10' step='1' maxlength='2' > ";
$form .= "<br><br>";

$form .= '<label for="list">List (1 to 10, multiple):</label>';
$form .= "<select name='list[]' multiple='multiple' size='10'>";
foreach ($all_values as $k => $v) {
    if (isset($list_options[$v])) {
        $selected = "selected=selected";
    } else {
        $selected = "";
    }
    $form .= "<option value='".$v."' " . $selected . ">".$v."</option>";
}
$form .= "</select>";
$form .= "<br><br>";

$form .= '<label for="loops">Loops (10.000 to 1 million):</label> ';
$form .= "<input type='number' value=".$loops." id='loops' name='loops' min='10000' max='1000000' step='1000' maxlength='7'> ";
$form .= "<br><br>";
$form .= "<input type='submit' value='Get Route' class='button'>";
$form .= "</form>";

print $form;

print "<hr>";

// Single Calculation
$get_min1 = OptimizedGetMinDistance($start, $end, $list, $loops);

print "<h3>Options: Go from " . $start . " to " . $end . " through points " . ArrayToString($list_options) . "</h3>";
print "<h3>Fastest route: " . $get_min1['route'] . "</h3>";
print "<h3>Total distance: " . $get_min1['distance'] . "</h3>";
print "<h3>Analysis by points:</h3>";
print DisplayRouteWithDistances($get_min1['route']);

//foreach ($list as $k1 => $point1) {
//    foreach ($list as $k2 => $point2) {
//        if ($point1 !== $point2) {
//            $min = OptimizedGetMinDistance($point1, $point2, $list, $distance,20000);
//            print $min['route'] . ": " . $min['distance'] . "<br>";
////            print DisplayRouteWithDistances($min['route'], $distance);
//            print "<hr>";
//        }
//    }
//}

print "</body></html>";