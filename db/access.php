<?php
$capabilities = array(

    'paygw/voucher:voucher_creator' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        ),
        'clonepermissionsfrom' => 'moodle/site:config',
    )
);
