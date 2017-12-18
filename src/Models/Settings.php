<?php

namespace Expokredit\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class Settings
 *
 * @property int $id
 * @property int $plentyId
 * @property string $lang
 * @property string $name
 * @property string $value
 * @property string $updatedAt
 */
class Settings extends Model
{
    const AVAILABLE_SETTINGS = array(        "plentyId"                         => "int"     ,
                                             "lang"                             => "string"  ,
                                             "name"                             => "string"  ,
                                             "infoPageType"                     => "int"     ,
                                             "infoPageIntern"                   => "int"     ,
                                             "infoPageExtern"                   => "string"  ,
                                             "logo"                             => "int"     ,
                                             "logoUrl"                          => "string"  ,
                                             "description"                      => "string"  ,
                                             "minimumAmount"                    => "float"   ,
                                             "maximumAmount"                    => "float"   );

    const SETTINGS_DEFAULT_VALUES = array(   "de"  => array( "name"                => "Expokredit"         ,
                                                             "infoPageType"        => "2"                ,
                                                             "infoPageIntern"      => ""                 ,
                                                             "infoPageExtern"      => ""                 ,
                                                             "logo"                => "2"                ,
                                                             "logoUrl"             => ""                 ,
                                                             "description"         => ""                 ),
                                             "en"  => array( "name"                => "Invoice"   ,
                                                             "infoPageType"        => "2"                ,
                                                             "infoPageIntern"      => ""                 ,
                                                             "infoPageExtern"      => ""                 ,
                                                             "logo"                => "0"                ,
                                                             "logoUrl"             => ""                 ,
                                                             "description"         => ""                 ) );

    const LANG_INDEPENDENT_SETTINGS = array(
                                                "minimumAmount"                 ,
                                                "maximumAmount"                  );

    const AVAILABLE_LANGUAGES = array( "de",
                                       "en");

    const DEFAULT_LANGUAGE = "de";

    const MODEL_NAMESPACE = 'Expokredit\Models\Settings';


    public $id;
    public $plentyId;
    public $lang        = '';
    public $name        = '';
    public $value       = '';
    public $updatedAt   = '';


    /**
     * @return string
     */
    public function getTableName():string
    {
        return 'Expokredit::settings';
    }
}