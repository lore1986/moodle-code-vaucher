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

defined('MOODLE_INTERNAL') || die();


if ($ADMIN->fulltree) {
    \core_payment\helper::add_common_gateway_settings($settings, 'paygw_voucher');
}

$settings->add(new \paygw_voucher\admin_setting_link('paygw/createvoucher',
    get_string('createvoucher', 'paygw_voucher'), get_string('createvoucherdesc', 'paygw_voucher'),
    get_string('createvoucher', 'paygw_voucher'), new moodle_url('/payment/gateway/voucher/vouchercreate.php'), 'paygw/voucher:voucher_creator'));

$settings->add(new \paygw_voucher\admin_setting_link('paygw/managevoucher',
    get_string('managevoucher', 'paygw_voucher'), get_string('managevoucherdesc', 'paygw_voucher'),
    get_string('managevoucher', 'paygw_voucher'), new moodle_url('/payment/gateway/voucher/vouchers.php'), 'paygw/voucher:voucher_creator'));

$settings->add(new \paygw_voucher\admin_setting_link('paygw/vouchersreport',
    get_string('vouchersreport', 'paygw_voucher'), get_string('vouchersreportdesc', 'paygw_voucher'),
    get_string('vouchersreport', 'paygw_voucher'), new moodle_url('/payment/gateway/voucher/vouchersreport.php'), 'paygw/voucher:voucher_creator'));


