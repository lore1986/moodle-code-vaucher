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

use core_payment\helper;
use paygw_voucher\pay_form;

require_once __DIR__ . '/../../../config.php';
require_once './lib.php';

require_login();


global $DB;
$context = context_system::instance(); // Because we "have no scope".
$PAGE->set_context($context);

$component = required_param('component', PARAM_COMPONENT);
$paymentarea = required_param('paymentarea', PARAM_AREA);
$itemid = required_param('itemid', PARAM_INT);
$description = required_param('description', PARAM_TEXT);


$params = [
    'component' => $component,
    'paymentarea' => $paymentarea,
    'itemid' => $itemid,
    'description' => $description,
];


$mform = new pay_form(
null,
array('
    confirm' => 1, 
    'component' => $component, 
    'paymentarea' => $paymentarea, 
    'itemid' => $itemid, 
    'description' => $description
));

$mform->set_data($params);
$dataform = $mform->get_data();

$confirm = 0;

if ($dataform != null) {
    $component = $dataform->component;
    $paymentarea = $dataform->paymentarea;
    $itemid = $dataform->itemid;
    $description = $dataform->description;
    $confirm = $dataform->confirm;
}


$PAGE->set_url('/payment/gateway/voucher/pay.php', $params);
$PAGE->set_pagelayout('report');

$PAGE->set_title($description);
$PAGE->set_heading($description);

$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'voucher');
$payable = helper::get_payable($component, $paymentarea, $itemid);

$currency = $payable->get_currency();
$voucher_entry = null;


// Add surcharge if there is any.
$surcharge = helper::get_gateway_surcharge('voucher');
$amount = helper::get_rounded_cost($payable->get_amount(), $currency, $surcharge);

$post_url= new moodle_url($PAGE->url, array('sesskey'=>sesskey()));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('gatewayname', 'paygw_voucher'), 4);

$mform->display();

if ($fromform = $mform->get_data()) {

    $t = voucher_approve_payment($fromform->voucher_code);
    
    if($t === 1)
    {
        global $COURSE;
        
        $button_b = '';
        $url = '';
        

        if($paymentarea === 'fee')
        {
            $courseid = $DB->get_field('enrol', 'courseid', ['enrol' => 'fee', 'id' => $itemid]);
            
            $url = course_get_url($courseid);

            $button_b = '<a class="btn btn-success" href="' . $url . '"> Back to course </a>';
            
        }else if($paymentarea === 'cmfee')
        {
            $coursemodule = $DB->get_record('course_modules', ['id' => $itemid]);
            $url = "http://localhost/course/view.php?id=" . $coursemodule->course;
            $button_b = '<a class="btn btn-success" href="http://localhost/course/view.php?id=' . $coursemodule->course . '"> Back to course </a>';

        }else
        {
            $button_b = 'Cannot take back to course, go manually. Payment area not implemented yet';
        }
        
        
        global $USER;

        $transaction = $DB->start_delegated_transaction();

        $accountid = $payable->get_account_id();

        $paymentid = helper::save_payment(
            $payable->get_account_id(),
            $component,
            $paymentarea,
            $itemid,
            $USER->id,
            $amount,
            $payable->get_currency(),
            'voucher'
        );


        
        $d = array();
        $d[0] = ['idcourse' => $url];
        $PAGE->requires->js_call_amd('paygw_voucher/voucher_js', 'processmodal', $d);
        
        $time = new DateTime("now", core_date::get_user_timezone_object());
        $timestamp = $time->getTimestamp();

        $ins_payed = new \stdClass();
        $ins_payed->paymentid = $paymentid;
        $ins_payed->paymentaccountid = $accountid;
        $ins_payed->description = $description;
        $ins_payed->timecreated = $timestamp;

        $DB->insert_record('paygw_voucher_transactions', $ins_payed);


        helper::deliver_order($component, $paymentarea, $itemid, $paymentid, (int) $USER->id);
        $transaction->allow_commit();


    }else
    {
        echo ('<div class="row">
                    <div class="col-12">
                        <p style="font-size=20"><span style="font-decoration:bold; color:red;">Attenzione nessun acquisto completato: </span>' . $t . '</p>
                    </div>
                </div>');
    }
} 


echo $OUTPUT->footer();
