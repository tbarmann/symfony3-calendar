<?php

require './CalendarUtils.php';

$myClass = new CalendarUtils;

$weeks = $myClass->getCalendarMonthArray(2, 2017);

print_r($weeks);

?>