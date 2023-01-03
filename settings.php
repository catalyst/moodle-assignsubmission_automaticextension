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
 * Automatic extension settings.
 *
 * @package    local_automaticextension
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_automaticextension', get_string('pluginname', 'local_automaticextension'));
    $ADMIN->add('localplugins', $settings);

    $name = new lang_string('settings:conditions', 'local_automaticextension');
    $description = new lang_string('settings:conditions_help', 'local_automaticextension');
    $settings->add(new admin_setting_confightmleditor('local_automaticextension/conditions', $name, $description, '', PARAM_RAW));

    $name = new lang_string('settings:maximumrequests', 'local_automaticextension');
    $description = new lang_string('settings:maximumrequests_help', 'local_automaticextension');
    $settings->add(new admin_setting_configtext('local_automaticextension/maximumrequests', $name, $description, 1, PARAM_INT));

    $name = new lang_string('settings:extensionlength', 'local_automaticextension');
    $description = new lang_string('settings:extensionlength_help', 'local_automaticextension');
    $settings->add(new admin_setting_configtext('local_automaticextension/extensionlength', $name, $description, 24, PARAM_INT));
}
