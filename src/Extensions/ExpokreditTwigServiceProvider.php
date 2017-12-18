<?php
/**
 * Created by IntelliJ IDEA.
 * User: ckunze
 * Date: 28/2/17
 * Time: 13:48
 */

namespace Expokredit\Extensions;

use Plenty\Plugin\Templates\Extensions\Twig_Extension;
use Expokredit\Services\SessionStorageService;
use Expokredit\Services\SettingsService;

class ExpokreditTwigServiceProvider extends Twig_Extension
{
    /**
     * Return the name of the extension. The name must be unique.
     *
     * @return string The name of the extension
     */
    public function getName():string
    {
        return "Expokredit_Extension_TwigServiceProvider";
    }

    /**
     * Return a list of filters to add.
     *
     * @return array The list of filters to add.
     */
    public function getFilters():array
    {
        return [];
    }

    /**
     * Return a list of functions to add.
     *
     * @return array the list of functions to add.
     */
    public function getFunctions():array
    {
        return [];
    }

    /**
     * Return a map of global helper objects to add.
     *
     * @return array the map of helper objects to add.
     */
    public function getGlobals():array
    {
        return [
            "expokredit" => [
                "settings"          => pluginApp( SettingsService::class ),
                "sessionStorage"    => pluginApp( SessionStorageService::class)
            ]
        ];
    }
}