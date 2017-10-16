<?php

namespace AppBundle\Controller\Cashier;

use Symfony\Component\HttpFoundation\Request;

class WechatController extends PaymentController
{
    public function wechatJsPayAction(Request $request)
    {
        $tradeSn = $request->query->get('tradeSn');

        $trade = $this->getPayService()->getTradeByTradeSn($tradeSn);

        return $this->render(
            'cashier/wechat/h5.html.twig', array(
            'trade' => $trade,
        ));
    }

    public function returnAction(Request $request)
    {
        $tradeSn = $request->query->get('tradeSn');
        $trade = $this->getPayService()->queryTradeFromPlatform($tradeSn);

        file_put_contents('pay.log', 'WechatController -> returnAction'.json_encode($trade).PHP_EOL, FILE_APPEND);
        if ($trade['status'] == 'paid') {
            return $this->redirect($this->generateUrl('cashier_pay_success', array('trade_sn' => $trade['trade_sn'])));
        } else {
            return $this->redirect($this->generateUrl('cashier_show', array('sn' => $trade['order_sn'])));
        }
    }

    public function notifyAction(Request $request, $payment)
    {
        $result = $this->getPayService()->notifyPaid($payment, $request->getContent());

        return $this->createJsonResponse($result);
    }
}
