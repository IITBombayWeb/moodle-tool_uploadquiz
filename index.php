<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * New quiz creation script from a comma separated file
 *
 * @package    tool
 * @subpackage uploadquiz
 * @author     Prof. P Sunthar, Kashmira Nagwekar
 * @copyright  2019 IIT Bombay, India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// Reusing code from /admin/tool/uloaduser/index.php for UI for CSV file upload.
require('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
// require_once($CFG->dirroot.'/user/profile/lib.php');
// require_once($CFG->dirroot.'/user/lib.php');
// require_once($CFG->dirroot.'/group/lib.php');
// require_once($CFG->dirroot.'/cohort/lib.php');
// require_once('locallib.php');
require_once($CFG->dirroot.'/admin/tool/uploadquiz/uploadquiz_form.php');
require_once($CFG->dirroot . '/course/modlib.php');

$iid         = optional_param('iid', '', PARAM_INT);
// $previewrows = optional_param('previewrows', 10, PARAM_INT);

// core_php_time_limit::raise(60*60); // 1 hour should be enough
// raise_memory_limit(MEMORY_HUGE);

// require_login();
// admin_externalpage_setup('tooluploaduser');
// require_capability('moodle/site:uploadusers', context_system::instance());

$courseid   = required_param('id', PARAM_INT);
$course     = get_course($courseid);
// $quiz       = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
require_login($course);

// require_login($course, false, $cm);
// $cmid               = required_param('cmid', PARAM_INT);
// list($course, $cm)  = get_course_and_cm_from_cmid($cmid, 'quiz');
// $context            = context_module::instance($cm->id);
// $url                = new moodle_url('/mod/quiz/importsettings.php', array('id'=>$cmid));

$context = context_course::instance($courseid);
// Check the user has the required capabilities to access this plugin.
require_capability('mod/quiz:manage', $context);

// Page setup.
$url = new moodle_url('/admin/tool/uploadquiz/index.php', array('id'=>$courseid));
$pluginname = get_string('pluginname', 'tool_uploadquiz');

$PAGE->set_url($url);
$PAGE->set_pagelayout('incourse');
$PAGE->set_context($context);
$PAGE->set_title($course->shortname . ': ' . $pluginname);
$PAGE->set_heading($course->fullname . ': ' . $pluginname);

/*
$struserrenamed             = get_string('userrenamed', 'tool_uploaduser');
$strusernotrenamedexists    = get_string('usernotrenamedexists', 'error');
$strusernotrenamedmissing   = get_string('usernotrenamedmissing', 'error');
$strusernotrenamedoff       = get_string('usernotrenamedoff', 'error');
$strusernotrenamedadmin     = get_string('usernotrenamedadmin', 'error');

$struserupdated             = get_string('useraccountupdated', 'tool_uploaduser');
$strusernotupdated          = get_string('usernotupdatederror', 'error');
$strusernotupdatednotexists = get_string('usernotupdatednotexists', 'error');
$strusernotupdatedadmin     = get_string('usernotupdatedadmin', 'error');

$struseruptodate            = get_string('useraccountuptodate', 'tool_uploaduser');

$struseradded               = get_string('newuser');
$strusernotadded            = get_string('usernotaddedregistered', 'error');
$strusernotaddederror       = get_string('usernotaddederror', 'error');

$struserdeleted             = get_string('userdeleted', 'tool_uploaduser');
$strusernotdeletederror     = get_string('usernotdeletederror', 'error');
$strusernotdeletedmissing   = get_string('usernotdeletedmissing', 'error');
$strusernotdeletedoff       = get_string('usernotdeletedoff', 'error');
$strusernotdeletedadmin     = get_string('usernotdeletedadmin', 'error');

$strcannotassignrole        = get_string('cannotassignrole', 'error');

$struserauthunsupported     = get_string('userauthunsupported', 'error');
$stremailduplicate          = get_string('useremailduplicate', 'error');

$strinvalidpasswordpolicy   = get_string('invalidpasswordpolicy', 'error');
$errorstr                   = get_string('error');

$stryes                     = get_string('yes');
$strno                      = get_string('no');
$stryesnooptions = array(0=>$strno, 1=>$stryes);

$returnurl = new moodle_url('/admin/tool/uploaduser/index.php');
$bulknurl  = new moodle_url('/admin/user/user_bulk.php');

$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// array of all valid fields for validation
$STD_FIELDS = array('id', 'username', 'email',
        'city', 'country', 'lang', 'timezone', 'mailformat',
        'maildisplay', 'maildigest', 'htmleditor', 'autosubscribe',
        'institution', 'department', 'idnumber', 'skype',
        'msn', 'aim', 'yahoo', 'icq', 'phone1', 'phone2', 'address',
        'url', 'description', 'descriptionformat', 'password',
        'auth',        // watch out when changing auth type or using external auth plugins!
        'oldusername', // use when renaming users - this is the original username
        'suspended',   // 1 means suspend user account, 0 means activate user account, nothing means keep as is for existing users
        'deleted',     // 1 means delete user
        'mnethostid',  // Can not be used for adding, updating or deleting of users - only for enrolments, groups, cohorts and suspending.
        'interests',
    );
// Include all name fields.
$STD_FIELDS = array_merge($STD_FIELDS, get_all_user_name_fields());

$PRF_FIELDS = array();

if ($proffields = $DB->get_records('user_info_field')) {
    foreach ($proffields as $key => $proffield) {
        $profilefieldname = 'profile_field_'.$proffield->shortname;
        $PRF_FIELDS[] = $profilefieldname;
        // Re-index $proffields with key as shortname. This will be
        // used while checking if profile data is key and needs to be converted (eg. menu profile field)
        $proffields[$profilefieldname] = $proffield;
        unset($proffields[$key]);
    }
}
*/
// if (empty($iid)) {================================================
// /*
    // Working code - Displays file upload UI.
    $mform1 = new quiz_uploadquiz_form1();
    $formdata = array ('id' => $courseid);
    $mform1->set_data($formdata);

    if ($formdata = $mform1->get_data()) {
        // Currently, upload one random file into the upload file section
        // and keep defaults for other settigs
        // Click on 'Import quiz settings' button
        // A notice appears reagarding 'itemid' field in 'introeditor' for $formobj
        // Click on 'Continue'
        // Takes you to the newly created quiz view page
        // Creates a quiz in course with id 2, at the end of section with id 0 (first section in the course)
        //===========================================================
        /*
        $iid = csv_import_reader::get_new_iid('uploaduser');
        $cir = new csv_import_reader($iid, 'uploaduser');

        $content = $mform1->get_file_content('userfile');

        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
        $csvloaderror = $cir->get_error();
        unset($content);

        if (!is_null($csvloaderror)) {
            print_error('csvloaderror', '', $returnurl, $csvloaderror);
        }
        // test if columns ok
        $filecolumns = uu_validate_user_upload_columns($cir, $STD_FIELDS, $PRF_FIELDS, $returnurl);
        // continue to form2
        // */

        //===========================================================
        // /*
        // $fromform object as in /course/modedit.php.
        // Adding new quiz activity in a course opens up /course/modedit.php.
        // Quiz administration -> Edit settings link also opens up /course/modedit.php.
        // Uses /mod/quiz/mod_form.php

        // Currently, creating a object for new quiz with static data.
        // These settings are to be uploaded and read from CSV file.
        $formobj = new stdClass();
        $formobj->name = "New Quiz";
        $formobj->introeditor = Array
        (
                'text' => '<p>Quiz description goes here.<br></p>',
                'format' => 1,
//                 'itemid' => 104237544,
                );
        $formobj->showdescription = 1;
        $formobj->timeopen = 1572564600;
        $formobj->timeclose = 1574692200;
        $formobj->timelimit = 2700;
        $formobj->overduehandling = 'autosubmit';
        $formobj->graceperiod = 0;
        $formobj->gradecat = 1;
        $formobj->gradepass = 0;
        $formobj->grade = 10;
        $formobj->attempts = 1;
        $formobj->grademethod = 1;
        $formobj->questionsperpage = 1;
        $formobj->navmethod = 'free';
        $formobj->shuffleanswers = 1;
        $formobj->preferredbehaviour = 'deferredfeedback';
        $formobj->canredoquestions = 0;
        $formobj->attemptonlast = 0;
        $formobj->enableclear = 1;
        $formobj->overallfeedbackimmediately = 1;
        $formobj->overallfeedbackopen = 1;
        $formobj->showuserpicture = 0;
        $formobj->decimalpoints = 2;
        $formobj->questiondecimalpoints = -1;
        $formobj->showblocks = 0;
        $formobj->quizpassword = '';
        $formobj->subnet = '10.196.0.0/16; 10.102.6.33; 10.150.1.0/24';
        $formobj->delay1 = 0;
        $formobj->delay2 = 0;
        $formobj->browsersecurity = 'safebrowser';
        $formobj->hbmonrequired = 1;
        $formobj->hbmonmode = 1;
        $formobj->nodehost = '10.102.1.115';
        $formobj->nodeport = 3000;
        $formobj->logs = 3;
        $formobj->odrequired = 0;
        $formobj->safeexambrowser_allowedkeys = 'ecbb8884ec0f09c429786b5a211778217801a30ad124bc5d793d0f942d2768a8 a8273eb7393427db448fd7ba77476a46d7d111844d24eefd57b81fedcf5351c9 bd9ce6681c1564a9e8b0fee6d5381d1ebe0b7bc68e228c698c2e43532af17b18';
        $formobj->boundary_repeats = 1;
        $formobj->feedbacktext = null;
//         $formobj->feedbacktext = array
//         (   [0] => array
//                 (
//                         'text' => '<p></p><p></p><p>If you have other sections to attempt:</p><p></p><ol><li>Click on&nbsp;<a href="http://exams.iitb.ac.in/my/">Dashboard</a>&nbsp;(on the top left or under your name dropdown top right)</li><li>Navigate to PCAT course</li><li>Choose the section</li></ol><p>If you have completed all the sections you can quit the browser by clicking here:</p><h5><a href="http://exams.iitb.ac.in/quitseb">Quit Safe Exam Browser</a></h5><br><p></p>',
//                         'format' => 1,
//                         'itemid' => 956519334,
//                 ),

//                 [1] => array
//                 (
//                         'text' => '',
//                         'format' => 1,
//                         'itemid' => 403800424,
//                 )
//         );
        $formobj->feedbackboundaries = Array ();
        $formobj->visible = 1;
        $formobj->visibleoncoursepage = 1;
        $formobj->cmidnumber = '';
        $formobj->groupmode = 0;
        $formobj->groupingid = 0;
        $formobj->availabilityconditionsjson = '{"op":"&","c":[],"showc":[]}';
        $formobj->completionunlocked = 1;
        $formobj->completion = 2;
        $formobj->completionview = 1;
        $formobj->completionpass = 0;
        $formobj->completionattemptsexhausted = 0;
        $formobj->completionexpected = 0;
        $formobj->tags = Array ();
        $formobj->course = 2;
        $formobj->coursemodule = 0;
        $formobj->section = 0;
        $formobj->module = 16;
        $formobj->modulename = 'quiz';
        $formobj->instance = 0;
        $formobj->add = 'quiz';
        $formobj->update = 0;
        $formobj->return = 0;
        $formobj->sr = 0;
        $formobj->competency_rule = 0;
        $formobj->submitbutton = 'Save changes';

        // Data required for new quiz creation in /course/modedit.php.
        $fromform = $formobj;
        $module = new stdClass();
        $module->name = 'quiz';

        // Code from /course/modedit.php.----------------------------
        $fromform = add_moduleinfo($fromform, $course);

        if (isset($fromform->submitbutton)) {
            $url = new moodle_url("/mod/$module->name/view.php", array('id' => $fromform->coursemodule, 'forceview' => 1));
            if (empty($fromform->showgradingmanagement)) {
                redirect($url);
            } else {
                redirect($fromform->gradingman->get_management_url($url));
            }
        } else {
            redirect(course_get_url($course, $cw->section, array('sr' => $sectionreturn)));
        }
        //     exit;
        //-----------------------------------------------------------
    } else {
        echo $OUTPUT->header();
        // echo $OUTPUT->heading(format_string($course->shortname, true, array('context' => $context)));
        // echo $OUTPUT->heading_with_help(get_string('uploadusers', 'tool_uploaduser'), 'uploadusers', 'tool_uploaduser');

        echo $OUTPUT->notification(get_string('versionone_note', 'tool_uploadquiz'), 'info');
        $mform1->display();
        echo $OUTPUT->footer();
        die;

    }
//     */
//===================================================================
// } else {
//     $cir = new csv_import_reader($iid, 'uploaduser');
//     $filecolumns = uu_validate_user_upload_columns($cir, $STD_FIELDS, $PRF_FIELDS, $returnurl);
// }