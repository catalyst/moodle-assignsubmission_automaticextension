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

namespace assignsubmission_automaticextension\task;

use core\task\adhoc_task;
use assignsubmission_automaticextension\automaticextension;

/**
 * Backfill automatic extension task
 *
 * @package    assignsubmission_automaticextension
 * @author     Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @copyright  2025, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backfill_extensions_task extends adhoc_task {

    /**
     * Perform the requested operation.
     *
     * @return void
     */
    public function execute(): void {
        global $DB;

        $data = $this->get_custom_data();
        if (empty($data) || !isset($data->backfillfrom)) {
            return;
        }

        automaticextension::backfill_requests($data->backfillfrom, $data->backfillto ?? null);
    }
}
