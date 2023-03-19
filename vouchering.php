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

use paygw_voucher\voucher_form;

require_once __DIR__ . '/../../../config.php';
require_once './lib.php';


require_login();


$context = \context_system::instance(); 
$PAGE->set_context($context);
require_capability('paygw/voucher:voucher_creator', $context);

global $DB;

$id = required_param('id', PARAM_INT);
$r = $DB->get_record('paygw_vouchers', array('id' => $id), $fields = '*', $strictness = IGNORE_MISSING);

$PAGE->set_url('/payment/gateway/voucher/vouchering.php');
$PAGE->set_pagelayout('admin');

$pagetitle = 'Edit Voucher'; 

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$PAGE->navbar->add(get_string('pluginname', 'paygw_voucher'), $PAGE->url);

$post_url= new moodle_url($PAGE->url, array('sesskey'=>sesskey()));

$url = $CFG->wwwroot . '/payment/gateway/voucher/vouchers.php';
$params = array('id' => $id, 'reference' => $r->referencename, 'being' => $r->being, 'voucherid' => $itemid, 'active' => $r->active);

$mform = new voucher_form(null, ['id'=>$id]);
$mform->set_data($params);



if ($mform->is_cancelled()) {
    
    redirect($url, "Take you to page", 0);

} else if ($fromform = $mform->get_data()) {

    $v = Edit_Voucher($fromform->reference, $fromform->being, $id, $r->code, $fromform->active);
    redirect($url, "Voucher Edited", 1);

} else {
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}

