<?php

use Tygh\Http;
use Tygh\Registry;
use Tygh\Payments\Processors\WayForPayLight as WayForPayLight;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION'))
{
    $data = array();
    switch ($mode)
    {
        case 'return':
            $data = $_REQUEST;
            break;
        case 'service':
            $json = file_get_contents('php://input');
            $data = json_decode($json, TRUE);
            break;
        default:
            return array(CONTROLLER_STATUS_NO_PAGE);
            break;
    }

    if (empty($data) || empty($data['orderReference']))
    {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
    $order_info = fn_get_order_info($data['orderReference']);
    $processor_data = fn_get_payment_method_data($order_info['payment_id']);
    $processor_params = $processor_data['processor_params'];
    $processor_params['merchantAccount'] = $processor_params['mode'] == 'test' ? 'test_merch_n1' : $processor_params['merchantAccount'];
    $processor_params['merchantSecretKey'] = $processor_params['mode'] == 'test' ? 'flk3409refn54t54t*FNJRET' : $processor_params['merchantSecretKey'];
    $WayForPay = new WayForPayLight($processor_params['merchantAccount'], $processor_params['merchantSecretKey']);

    if (empty($data['cardPan'])) $data['cardPan'] = '';
//    fn_print_die($data);
    $valid_signature = $WayForPay -> createSignature('CHECK_RESPONSE', $data);
    if ($data['merchantSignature'] !== $valid_signature)
    {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
    if (!empty($data['reasonCode']) && $data['reasonCode'] == 1132)
    {
        $company_data = fn_get_company_data($order_info['payment_method']['company_id']);
        $parsed_url = parse_url(Registry::get('config.current_location'));
        $storefront = $parsed_url['scheme'] == 'https' ? $company_data['secure_storefront'] : $company_data['storefront'];
        fn_set_notification('W', __('wayforpay.payment_failed'), __('wayforpay.response_code_message.' . $data['reasonCode'],
            array('[order_id]' => $order_info['order_id'], '[site]' => $storefront)));
        $data['transactionStatus'] = 'Created';
        $pp_response = $WayForPay -> getFieldsNameForOrder($data, $processor_params['order_status']);
        fn_finish_payment($order_info['order_id'], $pp_response);
        fn_order_placement_routines('route', $order_info['order_id']);
        exit;
    }
    $pp_response = $WayForPay -> getFieldsNameForOrder($data, $processor_params['order_status']);

    $force_notification = array();
    if ($order_info['status'] == $pp_response['order_status'])
    {
        $force_notification = array('C' => false, 'A' => false, 'V' => false);
    }
    if ($mode == 'return')
    {
        if ($order_info['status'] == STATUS_INCOMPLETED_ORDER)
        {
            fn_finish_payment($order_info['order_id'], $pp_response);
        }
        if (!empty($pp_response['reasonCode']) && $pp_response['reasonCode'] != '1100')
        {
            fn_set_notification('W', __('wayforpay.payment_failed'), __('wayforpay.response_code_message.' . $pp_response['reasonCode']));
            fn_order_placement_routines('save', $order_info['order_id'], $force_notification);
        } else { fn_order_placement_routines('route', $order_info['order_id'], $force_notification); }
    } elseif ($mode == 'service'){
        if ($order_info['status'] != STATUS_INCOMPLETED_ORDER)
        {
            if ($order_info['status'] != $pp_response['order_status'])
            {
                fn_change_order_status($order_info['order_id'], $pp_response['order_status']);
            }
            fn_update_order_payment_info($order_info['order_id'], $pp_response);
        }else{
            fn_finish_payment($order_info['order_id'], $pp_response);
        }

        $reply_to_response = array(
            'orderReference' => $pp_response['orderReference'],
            'status' => 'accept',
            'time' => $order_info['timestamp']
        );
        $reply_to_response['signature'] = $WayForPay -> createSignature('REPLY_TO_RESPONSE', $reply_to_response);
        echo json_encode($reply_to_response);
    }
    exit;
} else {
    $processor_params = $processor_data['processor_params'];
    $processor_params['merchantAccount'] = $processor_params['mode'] == 'test' ? 'test_merch_n1' : $processor_params['merchantAccount'];
    $processor_params['merchantSecretKey'] = $processor_params['mode'] == 'test' ? 'flk3409refn54t54t*FNJRET' : $processor_params['merchantSecretKey'];

    $order_reference = !empty($order_info['repaid']) ? ($order_info['order_id'] . '_' . $order_info['repaid']) : $order_info['order_id'];
    if ($processor_params['mode'] == 'test')
    {
//        $order_reference = $order_info['order_id'] . '_' . $order_info['repaid'] . '-' . time();
        $order_reference = $order_info['order_id'] . '_' . $order_info['repaid'];
        $order_info['total'] = '5';
    }
    $WayForPay = new WayForPayLight($processor_params['merchantAccount'], $processor_params['merchantSecretKey']);
    $wayforpay_products = fn_wayforpay_products_normalize($order_info['products'], $processor_params['currency']);
    $params = array
    (
        'merchantAccount' => $processor_params['merchantAccount'],
        'merchantTransactionSecureType' => 'AUTO',
        'merchantDomainName' => defined('HTTPS') ? Registry::get('config.https_host') : Registry::get('config.http_host'),
        'orderReference' => $order_reference,
        'orderDate' => (int) $order_info['timestamp'],
        'amount' => fn_format_price_by_currency($order_info['total'], CART_PRIMARY_CURRENCY, $processor_params['currency']),
        'currency' => 'UAH',
        'productName' => $wayforpay_products['productName'],
        'productCount' => $wayforpay_products['productCount'],
        'productPrice' => $wayforpay_products['productPrice'],
        'language' => CART_LANGUAGE,
        'returnUrl' => fn_url("payment_notification.return&payment=wayforpay", 'C', 'current'),
        'serviceUrl' => fn_url("payment_notification.service&payment=wayforpay", 'C', 'current'),
        'orderNo' => $order_info['order_id'],
        'orderLifetime' => !empty($processor_params['orderLifetime']) ? (int)$processor_params['orderLifetime'] : 3600,
        'orderTimeout' => !empty($processor_params['orderTimeout']) ? (int)$processor_params['orderTimeout'] : 3600,
        'transactionType' => 'PURCHASE',
        'merchantTransactionType' => 'SALE',
        'clientFirstName' => !empty($order_info['b_firstname']) ? $order_info['b_firstname'] : $order_info['s_firstname'],
        'clientLastName' => !empty($order_info['b_lastname']) ? $order_info['b_lastname'] : $order_info['s_lastname'],
        'clientEmail' => $order_info['email'],
        'clientPhone' => !empty($order_info['b_phone']) ? $order_info['b_phone'] : $order_info['s_phone']
    );
    $form = $WayForPay -> buildForm($params);
    echo(__('text_cc_processor_connection', array(
        '[processor]' => __("wayforpay")
    )));
    echo $form;
    echo <<<EOT
        <noscript><p>
EOT;
        echo(__('text_cc_javascript_disabled'));

        echo <<<EOT
        </p><p><input type="submit" name="btn" value="
EOT;
        echo(__('cc_button_submit'));
        echo <<<EOT
"></p>
        </noscript>
        </form>
        <script type="text/javascript">
            window.onload = function(){
                document.process.submit();
            };
        </script>
        </body>
    </html>
EOT;

    exit;
}