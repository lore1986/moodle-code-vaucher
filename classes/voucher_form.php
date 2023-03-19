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
 *
 * File         voucher_form.php
 * Encoding     UTF-8
 *
 * @package paygw_voucher
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace paygw_voucher;

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';

class voucher_form  extends \moodleform
{

    public function definition()
    {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('advcheckbox', 'active', get_string('active_checkbox', 'paygw_voucher'), 
        get_string('active_checkbox_text', 'paygw_voucher'), array('group' => 1), array(0, 1));
        $mform->setDefault('active', 1);

        $mform->addElement('text', 'reference', get_string('voucher_name', 'paygw_voucher'), 'maxlength="100"');
        $mform->setType('reference', PARAM_TEXT);
        $mform->addRule('reference', get_string('error_being', 'paygw_voucher'), 'required', '', 'client', false, false);
        

        $mform->addElement('text', 'being', get_string('time_to_be_used', 'paygw_voucher'), 'maxlength="3"');
        $mform->setType('being', PARAM_INT);
        $mform->setDefault('being', 1);
        $mform->addRule('being', get_string('error_being', 'paygw_voucher'), 'numeric', '', 'client', false, false);

        $this->add_action_buttons();
    }

}
