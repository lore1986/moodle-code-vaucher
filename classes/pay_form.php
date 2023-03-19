<?php
// This file is part of the voucher paymnts module for Moodle - http://moodle.org/
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
 * Contains form to apply for PAYNL services through Sebsoft
 *
 * File         pay_form.php
 * Encoding     UTF-8
 *
 * @package paygw_voucher
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_voucher;
defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';


class pay_form extends \moodleform
{

    public function definition()
    {
        $mform = $this->_form;
        $mform->setDisableShortforms(true);
        $mform->addElement('hidden', 'confirm');
        $mform->setDefault('confirm', 1);
        $mform->setType('confirm', PARAM_INT);
        $mform->addElement('hidden', 'component');
        $mform->setType('component', PARAM_TEXT);

        $mform->addElement('hidden', 'paymentarea');
        $mform->setType('paymentarea', PARAM_TEXT);

        $mform->addElement('hidden', 'itemid');
        $mform->setType('itemid', PARAM_INT);

        $mform->addElement('hidden', 'description');
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('text', 'voucher_code', get_string('insert_voucher_code', 'paygw_voucher'));
        $mform->setType('voucher_code', PARAM_TEXT);

        $mform->addElement('submit', 'submitbutton', get_string('start_process', 'paygw_voucher'));
    }
    public function validation($data, $files)
    {
        global $DB;
        $errors = parent::validation($data, $files);
        return $errors;
    }

}
