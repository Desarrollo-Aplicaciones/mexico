<?php
// A module to let you do simple A/B split testing.
// By Pete Warden ( http://petewarden.typepad.com ) – freely reusable with no restrictions

// An array to keep track of the choices that have been made, so we can log them
$g_ab_choices = array();

function should_ab($testname, $userid=null) {
    // If no user identifier is supplied, fall back to the client IP address
    if (empty($userid))
        $userid = $_SERVER['REMOTE_ADDR'];
   
    global $g_ab_choices;
    if (isset($g_ab_choices[$testname]))
        return $g_ab_choices[$testname];
       
    $key = $testname.$userid;
    $keycrc = crc32($key);
   
    $result = (($keycrc&1)==1);
   
    $g_ab_choices[$testname] = $result;
   
    return $result;
}

function get_ab_choices()
{
    global $g_ab_choices;
    return $g_ab_choices;
}
?>