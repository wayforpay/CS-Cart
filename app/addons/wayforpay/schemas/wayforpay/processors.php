<?php

$schema = array
(
    'wayforpay' => array
    (
        'processor' => 'wayforpay',
        'processor_script' => 'wayforpay.php',
//        'processor_template' => 'addons/wayforpay/views/orders/components/payments/wayforpay.tpl',
        'admin_template' => 'wayforpay.tpl',
        'callback' => 'Y',
        'type' => 'P',
        'position' => 9,
        'addon' => 'wayforpay',
    )
);

return $schema;