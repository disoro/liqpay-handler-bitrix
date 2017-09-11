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

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Error;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem\ServiceResult;
use Bitrix\Sale\PriceMaths;

class LiqpayCheckoutHandler extends PaySystem\ServiceHandler
{

    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return ServiceResult
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        $busValues = $this->getParamsBusValue($payment);

        $action      = 'pay';
        $amount      = $busValues['PAYMENT_SHOULD_PAY'];
        $currency    = $busValues['PAYMENT_CURRENCY'];
        $description = 'Оплата заказа';
        $public_key  = $busValues['LP_PUBLIC_KEY'];
        $order_id    = $busValues['PAYMENT_ID'];
        $paytypes    = $busValues['LP_PAY_TYPE'];
        $private_key = $busValues['LP_PRIVATE_KEY'];
        $result_url  = $busValues['LP_PATH_TO_RESULT_URL'];
        $sandbox     = $busValues['LP_IS_TEST'] == 'Y' ? 1 : 0;
        $server_url  = $busValues['LP_PATH_TO_SERVER_URL'];
        $version     = '3';

        if ($currency == 'RUR') { $currency = 'RUB'; }

        $params = array(
            'action'      => $action,
            'amount'      => $amount,
            'currency'    => $currency,
            'description' => $description,
            'public_key'  => $public_key,
            'order_id'    => $order_id,
            'paytypes'    => $paytypes,
            'result_url'  => $result_url,
            'sandbox'     => $sandbox,
            'server_url'  => $server_url,
            'version'     => $version,
        );

        $data = base64_encode(json_encode($params));

        $signature = base64_encode(sha1($private_key.$data.$private_key, 1));

        $extraParams = array(
            'URL' => $busValues['LP_ACTION_URL'],
            'DATA' => $data,
            'SIGNATURE' => $signature,
        );

        $this->setExtraParams($extraParams);

        return $this->showTemplate($payment, "template");
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        return array('RUB', 'UAH', 'USD', 'EUR');
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return mixed
     */
    public function processRequest(Payment $payment, Request $request)
    {
        $result = new PaySystem\ServiceResult();

        $data = $request->get('data');
        $parsed_data = json_decode(base64_decode($data), true);
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/test_ds_'.$parsed_data['order_id'].'.txt', print_r($parsed_data, true));
        if ($this->isCorrectHash($payment, $request))
        {
            if ($parsed_data['status'] == 'success' || $parsed_data['status'] == 'sandbox')
            {
                return $this->processNoticeAction($payment, $request);
            }
            else if ($parsed_data['status'] == 'wait_secure')
            {
                return new PaySystem\ServiceResult();
            }
        }
        else
        {
            PaySystem\ErrorLog::add(array(
                'ACTION' => 'processRequest',
                'MESSAGE' => 'Incorrect hash'
            ));
            $result->addError(new Error('Incorrect hash'));
        }

        return $result;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        $data = $request->get('data');
        $parsed_data = json_decode(base64_decode($data), true);

        return $parsed_data['order_id'];
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     */
    private function processNoticeAction(Payment $payment, Request $request)
    {
        $result = new PaySystem\ServiceResult();

        $data = $request->get('data');
        $parsed_data = json_decode(base64_decode($data), true);

        $description = 'sender phone: '.$parsed_data['sender_phone'].'; ';
        $description .= 'amount: '.$parsed_data['amount'].'; ';
        $description .= 'currency: '.$parsed_data['currency'].'; ';

        $statusMessage = 'status: '.$parsed_data['status'].'; ';
        $statusMessage .= 'transaction_id: '.$parsed_data['transaction_id'].'; ';
        $statusMessage .= 'payment_id: '.$parsed_data['order_id'].'; ';

        $fields = array(
            "PS_STATUS" => "Y",
            "PS_STATUS_CODE" => $parsed_data['status'],
            "PS_STATUS_DESCRIPTION" => $description,
            "PS_STATUS_MESSAGE" => $statusMessage,
            "PS_SUM" => $parsed_data['amount'],
            "PS_CURRENCY" => $parsed_data['currency'],
            "PS_RESPONSE_DATE" => new DateTime(),
        );

        $result->setPsData($fields);

        $paymentPrice = PriceMaths::roundByFormatCurrency($this->getBusinessValue($payment, 'PAYMENT_SHOULD_PAY'), $payment->getField('CURRENCY'));
        $liqpayPrice = PriceMaths::roundByFormatCurrency($parsed_data['amount'], $parsed_data['currency']);

        if ($liqpayPrice === $paymentPrice)
        {
            $result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
        }
        else
        {
            PaySystem\ErrorLog::add(array(
                'ACTION' => 'processNoticeAction',
                'MESSAGE' => 'Incorrect sum'
            ));
            $result->addError(new Error('Incorrect sum'));
        }

        return $result;
    }

    /**
     * @param Payment $payment
     * @param $request
     * @return bool
     */
    private function isCorrectHash(Payment $payment, Request $request)
    {
        $data = $request->get('data');
        $received_signature = $request->get('signature');

        if ($received_signature !== null && $data !== null)
        {
            $public_key = $this->getBusinessValue($payment, 'LP_PUBLIC_KEY');
            $private_key = $this->getBusinessValue($payment, 'LP_PRIVATE_KEY');

            $parsed_data = json_decode(base64_decode($data), true);
            $received_public_key = $parsed_data['public_key'];


            $generated_signature = base64_encode(sha1($private_key.$data.$private_key, 1));

            return $received_signature == $generated_signature && $received_public_key == $public_key;
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getIndicativeFields()
    {
        return array('data', 'signature');
    }

    /**
     * @param Request $request
     * @param $paySystemId
     * @return bool
     */
    static protected function isMyResponseExtended(Request $request, $paySystemId)
    {
        return true;
    }
}
