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
 * This file contains the definition for the library class for automatic extension plugin
 *
 * @package    assignsubmission_automaticextension
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

 require_once($CFG->dirroot . '/mod/assign/submissionplugin.php');

/**
 * Library class for automatic extension plugin extending submission plugin base class
 *
 * @package    assignsubmission_automaticextension
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_automaticextension extends assign_submission_plugin {

    /**
     * Get the name of the automatic extension plugin
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'assignsubmission_automaticextension');
    }

    /**
     * Display request extension button
     *
     * @param stdClass $submission
     * @param bool $showviewlink
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $PAGE, $USER;

        // Never show a link to view full submission.
        $showviewlink = false;

        list ($course, $cm) = get_course_and_cm_from_instance($submission->assignment, 'assign');
        $context = context_module::instance($cm->id);

        $assign = new \assign($context, $cm, $course);
        $userid = $USER->id;
        $automaticextension = new assignsubmission_automaticextension\automaticextension($assign, $userid);
        $canrequestextension = $automaticextension->can_request_extension();
        if (!$canrequestextension) {
            return '';
        }

        $renderer = $PAGE->get_renderer('assignsubmission_automaticextension');
        return $renderer->render_request_button($cm->id);
    }

    /**
     * Always return true because the automatic extension is not part of the submission form.
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        return true;
    }

    /**
     * The automatic extension plugin has no submission component so should not be counted
     * when determining whether to show the edit submission link.
     * @return boolean
     */
    public function allow_submissions() {
        return false;
    }

    /**
     * Return if the student meets the conditions for requesting an extension.
     *
     * @return bool
     */
    public function is_enabled() {
        global $USER;

        $userid = $USER->id;
        $automaticextension = new assignsubmission_automaticextension\automaticextension($this->assignment, $userid);
        return $automaticextension->can_request_extension();
    }

    /**
     * Automatically hide the setting for the submission plugin.
     *
     * @return bool
     */
    public function is_configurable() {
        return false;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of settings
     * @since Moodle 3.2
     */
    public function get_config_for_external() {
        return (array) $this->get_config();
    }
}
