<?php

namespace Expokredit\Providers;

use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Templates\Twig;

use Expokredit\Helper\ExpokreditHelper;
use Expokredit\Services\SessionStorageService;
use Expokredit\Services\SettingsService;
/**
 * Class ExpokreditOrderConfirmationDataProvider
 * @package Invoice\Providers
 */
class ExpokreditOrderConfirmationDataProvider
{
    public function call(Twig $twig, SettingsService $settings, ExpokreditHelper $expoHelper,
                         SessionStorageService $service, $args)
    {
        $mop = $service->getOrderMopId();

        $content = '';

        if($mop ==$expoHelper->getExpoMopId())
        {
            $lang = $service->getLang();
            $content .= $twig->render('Expokredit::ExpoForm');


        }

        return $content;
    }
}