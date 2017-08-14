<?php
/**
 * Created by PhpStorm.
 * User: farouk
 * Date: 05.11.2016
 * Time: 11:59
 */


class  Shopware_Controllers_Frontend_PaymentExpo extends Shopware_Controllers_Frontend_Payment
{

    public function init()
    {
        $this->View()->addTemplateDir(dirname(__FILE__) . "/../../Views/");
    }

    public function preDispatch()
    {
        // check if user is logged in
        if (Shopware()->Session()->get('sUserId') == null)
        {
            $this->redirect(
                [
                    'controller' => 'account'
                ]
            );

        }
        if (in_array($this->Request()->getActionName(), array('end'))) {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();

        }
    }

    public function indexAction()
    {

        return $this->redirect(array('action' => 'direct', 'forceSecure' => true));

    }

    public function directAction()
    {
        
        $router = $this->Front()->Router();
        $params['returnUrl'] = $router->assemble(array('action' => 'notify', 'forceSecure' => true, 'appendSession' => true));
        $queryParams = http_build_query($params);
        $this->View()->loadTemplate('frontend/payment_expo/direct.tpl');
        $this->forward('gateway','comer_expo_interface','frontend',array('queryParams'=>$queryParams));
      
    }

    public function endAction()
    {
        $secret = Shopware()->Config()->get('secretKey');
        $session = $this->container->get('session');
        $token = $session->offsetGet('X-CSRF-Token');
        parse_str($this->Request()->getParam('queryParams'), $params);

        /**
         * The transactionID generally comes from the interface and is used for assigning orders in the system of the payment method provider.
         * If the provider does not return a transactionID, any arbitrary value can be assigne
         */
        $transactionID = $params['transactionID'];
        /**
         * The uniquepaymentID is a unique identifier of the payment process.
         * This identifier should not be displayed to customers until the order is complete
         */
        $uniquePaymentID = $this->createPaymentUniqueId();

        $status = $params['status'];
        $hash = $params['hash'];

        if (empty($transactionID) ||$status != 'ok'|| (!hash_equals($hash,
                md5($secret. '|' . $transactionID . '|' . 'ok' . '|' . $token)))) {
            return $this->forward('error');
        }
        /** 2
         *  32 :: the_credit_has_been_accepted
         */
        $paymentStatusID = 32;
        $orderNumber = $this->saveOrder($uniquePaymentID, $uniquePaymentID, $paymentStatusID);

        $setExpoTransactionIdSql ='
        UPDATE  s_order_attributes SET expokredittransactionid='.$transactionID.'
         WHERE orderID IN
        (SELECT id FROM s_order WHERE ordernumber='.$orderNumber.' )';

        Shopware()->Db()->query($setExpoTransactionIdSql);

    }

    public function errorAction()
    {
        $this->View()->loadTemplate('frontend/payment_expo/error.tpl');

    }
}
