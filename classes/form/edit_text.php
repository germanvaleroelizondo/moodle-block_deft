<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Edit form for text task
 *
 * @package     block_deft
 * @copyright   2022 Daniel Thies <dethies@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_deft\form;

use context;
use context_user;
use core_form\dynamic_form;
use moodle_exception;
use moodle_url;
use block_deft\task;

/**
 * Edit form for text task
 *
 * @package     block_deft
 * @copyright   2022 Daniel Thies <dethies@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_text extends edit_task {

    /** @var {string} $type Type of task */
    protected $type = 'text';

    /**
     * Form definition
     */
    public function definition() {
        global $CFG, $USER;

        $mform = $this->_form;
        parent::definition();

        $mform->addElement('text', 'content', get_string('content', 'page'));
        $mform->setType('content', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'addcomments', '', get_string('addcomments', 'block_deft'));
        $mform->setType('addcomments', PARAM_BOOL);
        $mform->setDefault('addcomments', get_config('block_deft', 'addcomments'));
    }
}
