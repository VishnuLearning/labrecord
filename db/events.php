<?php
/**
 * Labrecord Event Handler definition
 *
 * @package mod_labrecord
 * @copyright 2017 Abhinav Dayal <abhinav.dayal@vishnu.edu.in>
 */

defined('MOODLE_INTERNAL') || die();

// List of observers
$observers = array(
    array(
        'eventname' => '\core\event\course_content_deleted',
        'callback'  => 'mod_labrecord_observer::course_content_deleted',
    ),
);

?>