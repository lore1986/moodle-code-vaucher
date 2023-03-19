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
 * Settings for the voucher payment gateway
 *
 * @package   paygw_voucher
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once __DIR__ . '/../../../config.php';
require_once './lib.php';


require_login();

global $DB;

$context = \context_system::instance(); 
$PAGE->set_context($context);

require_capability('paygw/voucher:voucher_creator', $context);

$id = required_param('id', PARAM_INT);


$PAGE->set_url('/payment/gateway/voucher/deletevoucher.php');

$PAGE->set_pagelayout('redirect');

$url = $CFG->wwwroot . '/payment/gateway/voucher/vouchers.php';

if($id != 0)
{
    $DB->delete_records(
        'paygw_vouchers', ['id' => $id]);
    redirect($url, "Voucher Deleted", 1);
}else
{
    redirect($url, "Cannot delete voucher. Id not valid", 1);
}