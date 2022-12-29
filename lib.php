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
 * Automatic extension library.
 *
 * @package    local_automaticextension
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This is the custom hook that creates and returns the request extention button html.
 *
 * @param assign_submission_status $status
 * @return string
 */
function local_automaticextension_get_request_button(assign_submission_status $status) {
    global $PAGE;

    $canrequestextension = local_automaticextension_can_request_extension($status->duedate, $status->extensionduedate);
    if (!$canrequestextension) {
        return '';
    }

    $renderer = $PAGE->get_renderer('local_automaticextension');
    return $renderer->render_request_button($status->coursemoduleid);
}

/**
 * Check if the use is able to request an extension.
 *
 * @param integer $duedate the due date
 * @param integer $extensionduedate the extension due date
 * @return boolean
 */
function local_automaticextension_can_request_extension($duedate, $extensionduedate) {
    $config = get_config('local_automaticextension');
    $now = time();

    if ($config->extensionlength > 0) {
        if ($duedate > $now) {
            return true;
        } else if ($config->maximumrequests > 1 && $extensionduedate > 0) {
            $maximumextensionlength = $config->maximumrequests * $config->extensionlength * 3600;
            $maximumextension = $duedate + $maximumextensionlength;
            if ($maximumextension > $extensionduedate) {
                return true;
            }
        }
    }

    return false;
}
