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
 * Class containing data for deft response block.
 *
 * @package     block_deft
 * @copyright   2022 Daniel Thies <dethies@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_deft\output;

defined('MOODLE_INTERNAL') || die();

use cache;
use block_deft\comment;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use core_course\external\course_summary_exporter;
use block_deft\socket;
use block_deft\task;

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Class containing data for deft choice block.
 *
 * @copyright 2022 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    /**
     * Constructor.
     *
     * @param int $context The context of the block.
     * @param object $config block configuration
     */
    public function __construct($context, $config) {
        $this->context = $context;
        $this->config = $config;
        $this->socket = new socket($context);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $DB, $USER;

        $nocoursesurl = $output->image_url('courses', 'block_deft')->out();
        $config = get_config('block_deft');

        comment::init();
        $responses = array_map(function($option) {
            return ['option' => $option];
        }, array_filter($this->config->option ?? []));

        $cache = cache::make('block_deft', 'tasks');
        $tasks = $cache->get($this->context->instanceid);

        $tasklist = [];
        foreach ($tasks as $record) {
            switch ($record->type) {
                case 'choice':
                    $choice = new choice($this->context, $record);
                    $record->choice = $choice->export_for_template($output);
                    $record->html = $output->render_from_template('block_deft/choice', $record->choice);
                    break;
                case 'comments':
                    $comments = new comments($this->context, $record);
                    $record->comments = $comments->export_for_template($output);
                    $record->html = $output->render_from_template('block_deft/comments', $record->comments);
                    break;
                case 'text':
                    $text = new text($this->context, $record);
                    $record->text = $text->export_for_template($output);
                    $record->html = $output->render_from_template('block_deft/text', $record->text);
                    break;
            }
            $tasklist[] = $record;
        }

        $manageurl = new moodle_url('/blocks/deft/manage.php', ['id' => $this->context->instanceid]);

        return [
            'canuse' => has_capability('block/deft:manage', $this->context),
            'contextid' => $this->context->id,
            'uniqid' => uniqid(),
            'manageurl' => $manageurl->out(true),
            'tasks' => $tasklist,
            'throttle' => get_config('block_deft', 'throttle'),
            'token' => $this->socket->get_token(),
        ];
    }
}
