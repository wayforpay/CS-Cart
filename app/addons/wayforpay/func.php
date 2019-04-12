<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;

function fn_wayforpay_install()
{
    $processors = fn_get_schema('wayforpay', 'processors', 'php', true);

    if (!empty($processors))
    {
        foreach ($processors as $processor_name => $processor_data)
        {
            $processor_id = db_get_field
            (
                'SELECT processor_id FROM ?:payment_processors WHERE admin_template = ?s',
                $processor_data['admin_template']
            );

            if (empty($processor_id))
            {
                $processor_id = db_query
                (
                    'INSERT INTO ?:payment_processors ?e',
                    $processor_data
                );
            } else {
                db_query
                (
                    'UPDATE ?:payment_processors SET ?u WHERE processor_id = ?i',
                    $processor_data, $processor_id
                );
            }
        }
    }
}

function fn_wayforpay_uninstall()
{
    $processors = fn_get_schema('wayforpay', 'processors');

    foreach ($processors as $processor_name => $processor_data)
    {
        $processor_id = db_get_field
        (
            "SELECT processor_id FROM ?:payment_processors WHERE admin_template = ?s",
            $processor_data['admin_template']
        );

        if (!empty($processor_id))
        {
            db_query
            (
                "DELETE FROM ?:payments WHERE processor_id = ?i",
                $processor_id
            );
            db_query
            (
                "DELETE FROM ?:payment_processors WHERE processor_id = ?i",
                $processor_id
            );
        }
    }
}

function fn_wayforpay_get_currencies()
{
    $currencies = Registry::get('currencies');
    return $currencies;
}

function fn_wayforpay_products_normalize($products, $currency_code)
{
    $wayforpay_products = array();
    if (!empty($products) && is_array($products))
    {
        $productName = array();
        $productPrice = array();
        $productCount = array();
        foreach ($products as $key => $product)
        {
            $productName[] = !empty($product['product_code']) ? $product['product'] . ' (' . $product['product_code'] . ')' : $product['product'];
            $productPrice[] = fn_format_price_by_currency($product['base_price'], CART_PRIMARY_CURRENCY, $currency_code);
            $productCount[] = $product['amount'];
        }
        $wayforpay_products['productName'] = $productName;
        $wayforpay_products['productPrice'] = $productPrice;
        $wayforpay_products['productCount'] = $productCount;
    }
    return $wayforpay_products;
}