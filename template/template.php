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

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>

<?=Loc::getMessage("PAYMENT_DESCRIPTION_SUM")?>: <b><?=CurrencyFormat($params["PAYMENT_SHOULD_PAY"], $params['PAYMENT_CURRENCY'])?></b><br /><br />
<?
if ($params['PAYMENT_CURRENCY'] == "RUB")
    $params['PAYMENT_CURRENCY'] = "RUR";
?>
<form action="<?= $params['URL']?>" method="post">
    <input type="hidden" name="signature" value="<?=$params['SIGNATURE'];?>" />
    <input type="hidden" name="data" value="<?=$params['DATA'];?>" />
    <input type="submit" value="<?= GetMessage("PAYMENT_PAY")?>" />
</form>