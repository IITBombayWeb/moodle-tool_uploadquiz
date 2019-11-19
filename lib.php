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
 * Libs, public API.
 *
 * @package    tool
 * @subpackage uploadquiz
 * @author     Prof. P Sunthar, Kashmira Nagwekar
 * @copyright  2019 IIT Bombay, India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
/**
 * This function extends the navigation with the course settings.
 *
 * @global stdClass       $CFG
 * @global core_renderer  $OUTPUT
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the tool
 * @param context         $context    The context of the course
 */
function tool_uploadquiz_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('mod/quiz:manage', $context)) {
        $url = new moodle_url('/admin/tool/uploadquiz/index.php', array('id'=>$course->id));
        $navigation->add('Upload quiz', $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/import', ''));
    }
}