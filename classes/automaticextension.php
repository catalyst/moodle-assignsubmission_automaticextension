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
 * This file contains the automatic extension class.
 *
 * @package    local_automaticextension
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_automaticextension;

use assign;

defined('MOODLE_INTERNAL') || die();

/**
 * The automatic extension class.
 *
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class automaticextension {

    /**
     * The assign object.
     *
     * @var object $assign.
     */
    private $assign = null;

    /**
     * The assign due date.
     *
     * @var integer $duedate.
     */
    private $duedate = 0;

    /**
     * The extension due date.
     *
     * @var integer $extensionduedate.
     */
    private $extensionduedate = 0;

    /**
     * The user id.
     *
     * @var integer $userid.
     */
    private $userid = null;

    /**
     * The maximum number of requests that can be made.
     *
     * @var integer $maximumrequests.
     */
    private $maximumrequests = 0;

    /**
     * The automatic extension length in seconds.
     *
     * @var integer $extensionlength.
     */
    private $extensionlength = 0;

    /**
     * The maximum automatic extension length in seconds.
     *
     * @var integer $maximumextensionlength.
     */
    private $maximumextensionlength = 0;

    /**
     * Class constructor.
     *
     * @param object $assign the assign object
     * @param integer $userid the user id
     */
    public function __construct(assign $assign, $userid) {
        $this->assign = $assign;
        $this->userid = $userid;
        $this->duedate = $assign->get_instance()->duedate;
        $flags = $assign->get_user_flags($userid, false);
        if ($flags) {
            $this->extensionduedate = $flags->extensionduedate;
        }
        $config = get_config('local_automaticextension');
        if ($config) {
            $this->maximumrequests        = $config->maximumrequests;
            $this->extensionlength        = $config->extensionlength * 3600;
            $this->maximumextensionlength = $this->maximumrequests * $this->extensionlength;
        }
    }

    /**
     * Apply the automatic extension.
     *
     * @param object $assign the assign object
     * @param integer $userid the user to apply the extension to
     * @return boolean if extension was applied successfully
     */
    public function apply_extension() {
        $newduedate = $this->duedate + $this->extensionlength;
        if ($this->extensionduedate > 0) {
            $newduedate = $this->extensionduedate + $this->extensionlength;
        }

        if (!$this->assign->save_user_extension($this->userid, $newduedate)) {
            return false;
        }

        $this->extensionduedate = $newduedate;

        $eventdata = [
            'context' => $this->assign->get_context(),
            'objectid' => $this->assign->get_instance()->id,
            'other' => [
                'extensionduedate' => $this->extensionduedate,
            ],
        ];
        event\automatic_extension_applied::create($eventdata)->trigger();

        return true;
    }

    /**
     * Check if the user is able to request an extension.
     *
     * @param integer $duedate the due date
     * @param integer $extensionduedate the extension due date
     * @return boolean
     */
    public function can_request_extension() {
        $now = time();

        if ($this->extensionlength > 0) {
            $withinduedate = max($this->duedate, $this->extensionduedate) > $now;
            $withinmaximumrequests = ($this->duedate + $this->maximumextensionlength) > $this->extensionduedate;
            if ($withinduedate && $withinmaximumrequests) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the extension due date in human readable format.
     *
     * @return string
     */
    public function get_user_extension_due_date() {
        return userdate($this->extensionduedate, get_string('strftimedaydatetime', 'langconfig'));
    }
}
