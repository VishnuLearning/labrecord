<?php
/**
 * Prints the overview of all the quizzes included in lab record
 *
 * @package mod_labrecord
 * @copyright 2017 Abhinav Dayal <abhinav.dayal@vishnu.edu.in>
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);           // Course ID
 
// Ensure that the course specified is valid
if (!$course = $DB->get_record('course', array('id'=> $id))) {
    print_error('Course ID is incorrect');
}
require_login($course);
$PAGE->set_pagelayout('incourse');

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$table->head  = array ('S. No', 'Lab Record');
$table->align = array ('left', 'left');

$labrecords = $DB->get_records_sql('SELECT id, name from {labrecord} WHERE course = ?', array($course->id));
$count = 1;
foreach($labrecords as $labrecord) {
    $table->data[] = array($count, $labrecord->name);
    $count++;
}

echo html_writer::table($table);
echo $OUTPUT->footer();
?>