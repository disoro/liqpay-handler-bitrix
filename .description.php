<?
/**
 * Liqpay Payment Handler
 *
 * Customized liqpay payment handler.
 * It uses the extended capability.
 *
 * @category    Liqliqpaycheckout
 *
 * @author      Dmytro Sorokin <disoro@mail.com>
 *
 * @license     Code and contributions have 'MIT License'
 *              More details: https://github.com/disoro/liqpay-handler-bitrix/blob/master/LICENSE
 *
 * @link        GitHub Repo:  https://github.com/disoro/liqpay-handler-bitrix
 *
 * @version     1.0.0
 *
 * EXTENSION INFORMATION
 *
 * 1C-Bitrix        17.0
 * LIQPAY API       https://www.liqpay.ua/documentation/ru
 *
 */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$data = array(
    'NAME' => 'LiqPayCheckout',
    'SORT' => 400,
    'CODES' => array(
        'LP_IS_TEST' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_TEST'),
            'SORT' => 100,
            'GROUP' => 'CONNECT_SETTINGS_LIQPAY',
            "INPUT" => array(
                'TYPE' => 'Y/N'
            )
        ),
        'LP_PUBLIC_KEY' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_PUBLIC_KEY'),
            'GROUP' => 'CONNECT_SETTINGS_LIQPAY',
            'SORT' => 200,
        ),
        'LP_PRIVATE_KEY' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_PRIVATE_KEY'),
            'GROUP' => 'CONNECT_SETTINGS_LIQPAY',
            'SORT' => 300,
        ),
        'LP_PATH_TO_RESULT_URL' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_PATH_TO_RESULT_URL'),
            'SORT' => 400,
            'GROUP' => 'CONNECT_SETTINGS_LIQPAY',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'http://'.$_SERVER['HTTP_HOST'].'/personal/order/',
                'PROVIDER_KEY' => 'VALUE'
            )
        ),
        'LP_PATH_TO_SERVER_URL' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_PATH_TO_SERVER_URL'),
            'SORT' => 500,
            'GROUP' => 'CONNECT_SETTINGS_LIQPAY',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'https://'.$_SERVER['HTTP_HOST'].'/bitrix/tools/sale_ps_result.php',
                'PROVIDER_KEY' => 'VALUE'
            )
        ),
        'PAYMENT_ID' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_ORDER_ID'),
            'SORT' => 600,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'ID',
                'PROVIDER_KEY' => 'PAYMENT'
            )
        ),
        'PAYMENT_CURRENCY' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_CURRENCY'),
            'SORT' => 700,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'CURRENCY',
                'PROVIDER_KEY' => 'PAYMENT'
            )
        ),
        'PAYMENT_SHOULD_PAY' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_SHOULD_PAY'),
            'SORT' => 800,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'SUM',
                'PROVIDER_KEY' => 'PAYMENT'
            )
        ),
        'LP_PAY_TYPE' => array(
            'NAME' => Loc::getMessage('SALE_HPS_LP_PAYMENT_TYPE'),
            'SORT' => 900,
            'GROUP' => 'CONNECT_SETTINGS_LIQPAY',
            'DEFAULT' => 'card',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'liqpay' => Loc::getMessage('SALE_HPS_LP_TYPE_LIQPAY'),
                    'card' => Loc::getMessage('SALE_HPS_LP_TYPE_CARD'),
                    'privat24' => Loc::getMessage('SALE_HPS_LP_TYPE_PRIVAT24'),
                    'cash' => Loc::getMessage('SALE_HPS_LP_TYPE_CASH')
                )
            )
        ),
        'LP_ACTION_URL' => array(
            'NAME'  => GetMessage('SALE_HPS_LP_ACTION'),
            'SORT' => 1000,
            'GROUP' => 'CONNECT_SETTINGS_LIQPAY',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'https://www.liqpay.ua/api/3/checkout',
                'PROVIDER_KEY' => 'VALUE'
            )
        )
    )
);
