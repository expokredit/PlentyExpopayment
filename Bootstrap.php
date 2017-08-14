<?php

class Shopware_Plugins_Frontend_ComerPluginExpoKredit_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    public function getVersion()
    {
        return '1.1.0';
    }

    public function getLabel()
    {
        return 'ExpoKredit Schnittstelle';
    }

    public function getInfo()
    {
        return array(
            'version'     => $this->getVersion(),
            'autor'       => 'comersio',
            'copyright'   => '&copy; 2015 ',
            'label'       => $this->getLabel(),
            'source'      => 'Community',
            'description' => 'Expokredit',
            'license'     => 'commercial',
            'support'     => '',
            'link'        => 'http://www.comersio.de'
        );
    }

    public function update($oldVersion)
    {
        $this->checkLicense();
        return true;
    }

    public function install()
    {
        $this->checkLicense();
        $this->createExpoPaymentMethod();

        $this->registerFrontendControllers();
        //$this->subscribeEvent('Theme_Compiler_Collect_Plugin_Javascript', 'addJsFiles');
        $this->subscribeEvent('Theme_Compiler_Collect_Plugin_Less', 'addLessFiles');
        $this->installOrderAtribute();

        $this->createConfig();
        return array('success' => true);
    }

    public function enable() {

        $payment = $this->Payment();
        $payment->setActive(true);
        $this->checkLicense();
        return array('success' => true, 'invalidateCache' => array('frontend'));
    }

    public function disable() {
        //TODO:
        return array('success' => true, 'invalidateCache' => array('frontend'));
    }

    public function uninstall() {
        $this->deleteOrderAttribute();
        return array('success' => true, 'invalidateCache' => array('frontend'));
    }


    public function Payment()
    {
        return $this->Payments()->findOneBy(
            array('name' => 'expoPayment')
        );
    }

    private function registerFrontendControllers()
    {
        $this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_PaymentExpo', 'onGetPaymentExpoController');
        $this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_ComerExpoInterface', 'onGetComerPaymentInterface');
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatchSecure', 'assignConfigValues');
        $this->subscribeEvent('Theme_Compiler_Collect_Plugin_Javascript', 'addJsFiles');
    }


    private function  createExpoPaymentMethod(){

        $this->createPayment(array(

            'name'        =>'expoPayment',
            'description' =>'ExpoKredit Payment',
            'additionaldescription'=>'ExpoKredit Payment Method',
            'action'      =>'PaymentExpo',
            'active'      => 1,
            'position'    => 1

        ));

    }

    private function installOrderAtribute()
    {
        $service =Shopware()->Container()->get('shopware_attribute.crud_service');

        $service->update('s_order_attributes', 'expokreditTransactionID', 'string', [
            'label' => 'ExpoTransactionID',
            'supportText' => 'ExpoID Transaction ID',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //in case of multi_selection or single_selection type, article entities can be selected,
            'entity' => 'Shopware\Models\Order\Order',

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => true,

        ]);
    }

    private function deleteOrderAttribute(){
        $service = Shopware()->Container()->get('shopware_attribute.crud_service');
        $service->delete('s_order_attributes', 'expokreditTransactionID');
    }
//    public function addJsFiles(Enlight_Event_EventArgs $args)
//    {

        //$jsFiles = array(__DIR__ . '/Views/frontend/_public/src/js/expoPricer.js');
        //$jsFiles = array(__DIR__ . '/Views/frontend/_public/src/js/expoClient_clean.js');
        //$jsFiles = array(__DIR__ . '/Views/frontend/_public/src/js/expoClient.js');
//        return new Doctrine\Common\Collections\ArrayCollection($jsFiles);
//    }

    public function addLessFiles(Enlight_Event_EventArgs $args)
    {
        $less = new \Shopware\Components\Theme\LessDefinition(
            array(
                't_align' => 'center',           // container top > tablet landscape
            ),
            //less files to compile
            array(
                __DIR__ . '/Views/frontend/_public/src/less/all.less'
            ),
            //import directory
            __DIR__
        );
        return new Doctrine\Common\Collections\ArrayCollection(array($less));
    }


    public function assignConfigValues (Enlight_Event_EventArgs $arguments)
    {
        $controller = $arguments->getSubject();
        $request = $controller->Request();
        if ($request->getModuleName() !== 'frontend') {return;}

        $arguments->getSubject()->View()->assign('defaultOrderNumber', $this->Config()->get('defaultOrderNumber'));
        $arguments->getSubject()->View()->assign('secretKey', $this->Config()->get('secretKey'));
        $arguments->getSubject()->View()->assign('supplierID', $this->Config()->get('supplierID'));
    }


    private function createConfig()
    {
        $this->Form()->setElement('text', 'supplierID',
            array(
                'label' => 'ExpoKredit Supplier ID',
                'value' => '2359',
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
            )
        );

        $this->Form()->setElement('text', 'defaultOrderNumber',
            array(
                'label' => 'default temporary order number',
                'value' => '123123123',
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
                'description'=>'temporary order number user to submit the expo Form'
            )
        );
        $this->Form()->setElement('text', 'secretKey',
            array(
                'label' => 'key for intern request checking',
                'value' => 'VdSfdGAXx5LF466XEbsxhPDh',
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
            )
        );
    }

    /**
     * @param $arguments
     * @return string
     */
    public function onGetPaymentExpoController(Enlight_Event_EventArgs $arguments)
    {

        return $this->Path() . 'Controllers/Frontend/PaymentExpo.php';
    }

    public function onGetComerPaymentInterface(Enlight_Event_EventArgs $arguments){

        return $this->Path().'Controllers/Frontend/ComerExpoInterface.php';
    }

    /**
     * checkLicense()-method for ComerPluginCustomLogoutPage
     */
    public function checkLicense($throwException = true)
    {
        return true;
    }

	/**
	 * Event listener method
	 *
	 * @param Enlight_Controller_ActionEventArgs $args
	 */
	public function onPostDispatch(Enlight_Controller_ActionEventArgs $args)
	{
		$request = $args->getSubject()->Request();
		$view = $args->getSubject()->View();

		if ($request->isXmlHttpRequest()) {
			return;
		}

		$view->addTemplateDir(__DIR__ . '/Views/');
		$view->assign('supplierID', $this->Config()->get('supplierID'));
	}
}
