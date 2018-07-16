<?php
/**
 * Capability definitions for this module
 *
 * @package mod_labrecord
 * @copyright 2017 Abhinav Dayal <abhinav.dayal@vishnu.edu.in>
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'mod/labrecord:addinstance' => array(
        'riskbitmask'  => RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),
 
    'mod/labrecord:view' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,            
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        )
    ),
);
?>