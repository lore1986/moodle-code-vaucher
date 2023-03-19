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


defined('MOODLE_INTERNAL') || die();


function Gen_Check_Voucher($fromform)
{
    global $DB;

    $voucher = new \stdClass();
    $voucher->referencename = preg_replace('/\s+/', '', strtoupper($fromform->reference));
    $voucher->being = $fromform->being;
    $voucher->availability = $fromform->being;
    $voucher->active = $fromform->active;

    $date = strtoupper(substr(date("M"), 0, 2) . rand(1,100));
    $rand = chr(rand(65,90)) . chr(rand(65,90)) . rand(1,10);
    $code = strtoupper(substr($fromform->reference, 0, 2));
    $code = $code . $date . $rand;    
    $voucher->code = $code;

    if($DB->count_records('paygw_vouchers', array('code' => $voucher->code)) == 0)
    {
        $voucher->id = $DB->insert_record('paygw_vouchers', $voucher);
    }else
    {
        return Gen_Check_Voucher($fromform->reference, $fromform->being);
    }

    return $voucher;
}

function Prepare_Vouchers_page()
{
    global $DB, $CFG;

    $sqlVouchers = 'SELECT * FROM {paygw_vouchers}';
    $vouchers = $DB->get_records_sql($sqlVouchers);

    $trow = array();
    //referencename code active being availability 

    foreach ($vouchers as $r) {

        $myR = array();
        $myR['vname'] = $r->referencename;
        $myR['vcode'] = $r->code;

        $u = $CFG->wwwroot . '/payment/gateway/voucher/img/';

        if($r->active && $r->availability > 0){
            $myR['vactive'] = $u . 'yes.png';
        }else
        {
            $myR['vactive'] = $u . 'no.png';
        }
        
        $myR['vbeing'] = $r->being;
        $myR['vavailability'] = $r->availability;
        $myR['vleft'] = $r->being - $r->availability;
        $myR['vurl'] = $CFG->wwwroot . '/payment/gateway/voucher/vouchering.php?id=' .  $r->id;
        $myR['vdel'] = $CFG->wwwroot . '/payment/gateway/voucher/deletevoucher.php?id=' .  $r->id;

        $trow[] = $myR;
    }

    return $trow;
}

function Edit_Voucher($reference, $being, $id, $code, $active)
{
    global $DB;

    $voucher = new \stdClass();
    $voucher->id = $id;
    $voucher->referencename = strtoupper($reference);
    $voucher->being = $being;
    $voucher->availability = $being;
    $voucher->code = $code;
    $voucher->active = $active;

    $voucher = $DB->update_record('paygw_vouchers', $voucher, false);

    return $voucher;
}

function voucher_approve_payment($code)
{
    global $DB;

    $output  = 1; 

    if($DB->count_records('paygw_vouchers', ['code' => $code]))
    {
        $vExist = $DB->get_record('paygw_vouchers', ['code' => $code, 'active' => 1]);
        
        if($vExist->availability == 0)
        {
            $output = "Tutti gli accessi con questo codice sono gia' stati usati. Molte grazie.";
        }else
        {
            $vExist->availability -= 1;
            $DB->update_record('paygw_vouchers', $vExist, false);
        }
    }
    else
    {
        $output = "Codice non valido. Perfavore ricontrollare di aver inserito correttamente il codice.";
    }

    
    
    return $output;
}