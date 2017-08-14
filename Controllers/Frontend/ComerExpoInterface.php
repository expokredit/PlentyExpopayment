<?php

/**
 * Created by PhpStorm.
 * User: farouk
 * Date: 06.11.2016
 * Time: 10:59
 */

class Shopware_Controllers_Frontend_ComerExpoInterface extends Enlight_Controller_Action
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

        if (in_array($this->Request()->getActionName(), array('notify','subject'))) {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();

        }

    }

    public function gatewayAction()
    {

        parse_str($this->Request()->getParam('queryParam'), $params);
        $this->View()->loadTemplate('frontend/comer_expo_interface/gateway.tpl');
        $this->View()->assign('basketAmount', Shopware()->Modules()->Basket()->sGetAmount()['totalAmount']);
        $this->View()->assign('userInfo', $this->fetchCurrentUserData());

    }


    private function fetchCurrentUserData(){

    $sql = 'SELECT * FROM s_user_billingaddress as ba  JOIN  s_user as u WHERE ba.userID = u.id AND u.id = '.Shopware()->Session()->get('sUserId');
        $userInfo = Shopware()->Db()->fetchAll($sql)[0];

return $userInfo;

    }

    /**
     * Request source verification _csrf token
     */
    public function notifyAction()
    {


        $session = $this->container->get('session');
        $token = $session->offsetGet('X-CSRF-Token');

        $transactionID = $this->Request()->getParam('transactionID');

        $status = $this->Request()->getParam('status');
        $secret = Shopware()->Config()->get('secretKey');
        $params['hash']= md5($secret.'|'.$transactionID.'|'.$status.'|'.$token);
        $params['status']= $status;
        $params['transactionID']= $transactionID;
        $queryParams = http_build_query($params);


        return $this->forward('end','payment_expo','frontend',array('queryParams'=>$queryParams));

    }




}