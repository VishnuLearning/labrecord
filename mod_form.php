<?php
/**
 * Forms for updating/adding instance
 *
 * @package mod_labrecord
 * @copyright 2017 Abhinav Dayal <abhinav.dayal@vishnu.edu.in>
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
 
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/mod/labrecord/lib.php');
 
class mod_labrecord_mod_form extends moodleform_mod {
 
    function definition() {
        global $CFG, $DB, $COURSE;
 
        $mform =& $this->_form;
 
        $mform->addElement('html', '<h3>Select Quizzes to beincluded in lab record below</h3>');
 
        $quizzes = $DB->get_records_sql('SELECT id, name, preferredbehaviour as type from {quiz} WHERE course = ?', array($COURSE->id));
        $count = 0;
        foreach ($quizzes as $quiz) {
            $id = $quiz->id;
            $name = $quiz->name;
            $type = $quiz->type;
            $mform->addElement('advcheckbox','quiz'.$count,$count, $name, array('group'=>$type), array(0, (int)$id));
            $count = $count + 1;
        }
        $mform->addElement('hidden', 'total', $count);
 
        $this->standard_coursemodule_elements();
 
        $this->add_action_buttons();
    }
}



?>