<?php

namespace AppBundle\Service;

class CalendarUtils {

  function getNameOfMonth($m) {
    $months = array('','January','February','March','April','May','June','July','August','September','October','November','December');
    return $months[$m];
  }

  function getDaysOfWeek() {
    return array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
  }

  function getDaysOfWeekShort() {
    return array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
  }

  function isLeapYear($d) {
    return (date("L", $d) === 1) ? true : false;
  }

  function getDaysInMonth($d) {
    return date("t", $d);
  }

  function getNextMonthYear($m, $y) {
    if ($m === 12) {
      return array('month' => 1, 'year' => $y+1);
    }
    return array('month' => $m+1, 'year' => $y);
  }

  function getPrevMonthYear($m, $y) {
    if ($m === 1) {
      return array('month' => 12, 'year' => $y-1);
    }
    return array('month' => $m-1, 'year' => $y);
  }

  function getCalendarMonthArray($m, $y) {
    $weeks = array();
    $d = strtotime("$y-$m-01");
    $begin_cell = date("w", $d);
    $end_day = $this->getDaysInMonth($d);
    $curr_day=1;
    $cell_ptr=0;

    while ($curr_day <= $end_day) {
      $days = array();
      for ($j=0; $j<7; $j++) {
        if (($cell_ptr < $begin_cell) || ($curr_day > $end_day)) {
          array_push($days, null);
        }
        else {
          array_push($days,$curr_day);
          $curr_day++;
        }
        $cell_ptr++;
      }
      array_push($weeks,$days);
    }
    return ($weeks);
  }

  function filterThisMonthsEvents($events, $m, $y) {
    return array_filter($events, function($event) use($m, $y){
      $month = intval($event->getDueDate()->format("m"));
      $year = intval($event->getDueDate()->format("Y"));
      return (($month === $m) && ($year === $y));
    });
  }

  function formatEvents($events) {
    $formatted_events = array();
    foreach ($events as $event) {
      $day = intval($event->getDueDate()->format("j"));
      $time = $event->getDueDate()->format("g:ia");
      $time = str_replace("m","",$time);
      $name = $event->getName();
      $id = intval($event->getId());
      $formatted_events[$day][] = array('name' => $name, 'time'=> $time, 'id' => $id);
    }
    return $formatted_events;
  }

  function parseDateTime($dt) {
    return array(
      'monthNum' => intval($dt->format('m')),
      'yearNum' =>  intval($dt->format('Y')),
      'dayNum' => intval($dt->format('j')),
      );
  }

}
?>
