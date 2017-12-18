<?php
/**
 * Created by IntelliJ IDEA.
 * User: ckunze
 * Date: 23/2/17
 * Time: 15:48
 */

namespace Expokredit\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;
use Plenty\Plugin\Routing\ApiRouter;

class ExpokreditRouteServiceProvider extends RouteServiceProvider
{

    /**
     * @param Router $router
     */
    public function map(Router $router , ApiRouter $apiRouter)
    {
       $apiRouter->version(['v1'], ['middleware' => ['oauth']],
            function ($routerApi)
            {
                /** @var ApiRouter $routerApi*/
                $routerApi->get('payment/expokredit/settings/{plentyId}/{lang}', ['uses' => 'Expokredit\Controllers\SettingsController@loadSettings']);
                $routerApi->put('payment/expokredit/settings', ['uses' => 'Expokredit\Controllers\SettingsController@saveSettings']);
            });
    }

}