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
 * Plugin version and other meta-data are defined here.
 *
 * @package   paygw_voucher
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once __DIR__ . '/../../../config.php';
require_once './lib.php';

use paygw_voucher\voucher_form;

require_login();

global $DB;

$context = \context_system::instance(); 
$PAGE->set_context($context);
require_capability('paygw/voucher:voucher_creator', $context);


$PAGE->set_url('/payment/gateway/voucher/vouchercreate.php');

$PAGE->set_pagelayout('admin');
$PAGE->set_heading($SITE->fullname);

$pagetitle = 'Create Voucher';
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$PAGE->navbar->add(get_string('pluginname', 'paygw_voucher'), $PAGE->url);


$post_url= new moodle_url($PAGE->url, array('sesskey'=>sesskey()));
$url = $CFG->wwwroot . '/payment/gateway/voucher/vouchers.php';


$mform = new voucher_form();
if ($mform->is_cancelled()) {
    redirect($url, "Back to vouchers page.", 0);

} else if ($fromform = $mform->get_data()) {
    $v = Gen_Check_Voucher($fromform);
    redirect($url, 'Voucher ' . $v->code . ' for client ' . $fromform->reference . " Created", 2);

} else {
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
