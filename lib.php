<?php
/**
 * Library of functions and constants for module labrecord
 *
 * @package mod_labrecord
 * @copyright 2017 Abhinav Dayal <abhinav.dayal@vishnu.edu.in>
 */

defined('MOODLE_INTERNAL') || die();

function deleteAllAssociatedQuizzes($labrecordid) {
    global $DB;
    $select = 'labrecordid = ?';
    $params = array('labrecordid'=>$labrecordid);
    $DB->delete_records_select('labrecord_quizzes', $select, $params);
}

function setLabQuizzesInDB($labrecord) {
    global $DB;
    //print_object($labrecord);
    deleteAllAssociatedQuizzes($labrecord->id);
    $count = (int)$labrecord->total;
    $qids = array();
    for( $i = 0; $i < $count; $i++) {
        array_push($qids, 'quiz'.$i);
    }
    $quizzes = array($qids);
    $records = array();
    foreach($quizzes as $quiz) {
        $qid = $labrecord->{$quiz};
        if($qid > 0) {
            $record = new stdClass();
            $record->labrecordid = $labrecord->id;
            $record->quizid = $qid;
            array_push($records, $record);
        }
    }
    $DB->insert_records('labrecord_quizzes', $records);
}

function labrecord_add_instance($labrecord) {
    global $DB;
    $labrecord->timemodified = time();
    $labrecord->id = $DB->insert_Record('labrecord', $labrecord);
    setLabQuizzesInDB($labrecord);
    return $labrecord->id;
}

function labrecord_update_instance($labrecord) {
    global $DB;
    $labrecord->timemodified = time();
    $labrecord->id = $labrecord->instance;
    if(! $DB->update_record('labrecord', $labrecord)) {
        return false;
    }
    setLabQuizzesInDB($labrecord);
    return true;
}

function labrecord_delete_instance($id) {
    global $DB, $CFG;
    //require_once($CFG->dirroot.'/mod/labrecord/locallib.php');
    if (! $labrecord = $DB->get_record('labrecord', array('id'=>$id))) {
        return false;
    }
    $DB->delete_records('labrecord_quizzes', array('labrecordid'=>$id));
    $DB->delete_records('labrecord', array('id'=>$id));
    return true;
}

?>