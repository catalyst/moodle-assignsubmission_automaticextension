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
 * Plugin upgrade code
 *
 * @package    assignsubmission_automaticextension
 * @author     Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @copyright  2025, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade assignsubmission_automaticextension.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_assignsubmission_automaticextension_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023010600) {
        // Define table assignsubmission_automaticextension to be created.
        $table = new xmldb_table('assign_automaticextension');

        // Adding fields to table assignsubmission_automaticextension.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('assignid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timerequested', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('extensionduedate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table assignsubmission_automaticextension.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for assignsubmission_automaticextension.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Backfill requests with data from the previous year.
        $task = new \assignsubmission_automaticextension\task\backfill_extensions_task();
        $task->set_custom_data([
            'backfillfrom' => time() - YEARSECS,
            'backfillto' => time(),
        ]);
        core\task\manager::queue_adhoc_task($task, true);

        // Automaticextension savepoint reached.
        upgrade_plugin_savepoint(true, 2023010600, 'assignsubmission', 'automaticextension');
    }

    return true;
}
