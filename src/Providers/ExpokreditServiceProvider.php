<?php //strict

namespace Expokredit\Providers;

use Expokredit\Extensions\ExpokreditTwigServiceProvider;
use Plenty\Modules\Payment\Events\Checkout\ExecutePayment;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Plugin\ServiceProvider;
use Expokredit\Helper\ExpokreditHelper;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Plugin\Events\Dispatcher;

use Expokredit\Methods\ExpokreditPaymentMethod;

use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Plugin\Templates\Twig;

/**
 * Class ExpokreditServiceProvider
 * @package Expokredit\Providers
 */
 class ExpokreditServiceProvider extends ServiceProvider
 {
     public function register()
     {
         $this->getApplication()->register(ExpokreditRouteServiceProvider::class);
     }

     /**
      * Boot additional services for the payment method
      *
      * @param Twig $twig
      * @param ExpokreditHelper $paymentHelper
      * @param PaymentMethodContainer $payContainer
      * @param Dispatcher $eventDispatcher
      */
     public function boot(Twig $twig,
                          ExpokreditHelper $paymentHelper,
                          PaymentMethodContainer $payContainer,
                          Dispatcher $eventDispatcher)
     {

         $twig->addExtension(ExpokreditTwigServiceProvider::class);

         // Register the Expokredit payment method in the payment method container
         $payContainer->register('plenty::EXPOKREDIT', ExpokreditPaymentMethod::class,
                                [ AfterBasketChanged::class, AfterBasketItemAdd::class, AfterBasketCreate::class ]
         );

         // Listen for the event that gets the payment method content
         $eventDispatcher->listen(GetPaymentMethodContent::class,
                 function(GetPaymentMethodContent $event) use( $paymentHelper)
                 {
                     if($event->getMop() == $paymentHelper->getExpoMopId())
                     {
                         $event->setValue('');
                         $event->setType('continue');
                     }
                 });

         // Listen for the event that executes the payment
         $eventDispatcher->listen(ExecutePayment::class,
             function(ExecutePayment $event) use( $paymentHelper)
             {
                 if($event->getMop() == $paymentHelper->getExpoMopId())
                 {
                     $event->setValue('<h1>Expokredit<h1>');
                     $event->setType('htmlContent');
                 }
             });
     }
 }
