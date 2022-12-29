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
 * Automatic extension request page.
 *
 * @package    local_automaticextension
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot . "/local/automaticextension/lib.php");
require_once($CFG->dirroot . "/mod/assign/locallib.php");

$cmid = required_param('cmid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'assign');

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/assign:view', $context);

$returnurl = new \moodle_url('/mod/assign/view.php', array('cmid' => $cmid));

$assign = new \assign($context, $cm, $course);
$instance = $assign->get_instance();
$canview = $assign->can_view_submission($USER->id);
$canedit = $assign->submissions_open($USER->id) && $assign->is_any_submission_plugin_enabled();
$duedate = $instance->duedate;
$extensionduedate = null;
if ($flags) {
    $extensionduedate = $flags->extensionduedate;
}
$canrequestextension = local_automaticextension_can_request_extension($duedate, $extensionduedate);
if (!$canview || !$canedit || !$canrequestextension) {
    \core\notification::warning(get_string('unabletorequest', 'local_automaticextension'));
    redirect($returnurl);
}

$pageurl = new \moodle_url('/local/automaticextension/request.php', array('cmid' => $cmid));
$title = get_string('extensionrequest', 'local_automaticextension');
$PAGE->set_url($pageurl);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

// Print page HTML.
echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_automaticextension');
echo $renderer->render_request_page($assign);

echo $OUTPUT->footer();
