{extends file='parent:frontend/index/index.tpl'}

{block name="frontend_index_content"}
    <div id="expo">{s name='loadingExpoInterface' namespace='frontend/plugins/expokredit'}Das Expokredit-Interface wird initialisiert....{/s}</div>
{/block}

{block name='frontend_index_content_left'}
{/block}

{block name="frontend_index_header_javascript_tracking" append}
    <script language="JavaScript" src="//expokredit.de/expoapi-script/{$supplierID}/1/jq/script.js"></script>
{/block}
{block name="frontend_index_header_javascript_jquery_lib" append}

    <script>
        {literal}
        $(function () {

            var userInfo = {salutation:'{/literal}{$userInfo['salutation']}{literal}',
                street:'{/literal}{$userInfo['street']}{literal}',
                vorname:'{/literal}{$userInfo['firstname']}{literal}',
                lastname:'{/literal}{$userInfo['lastname']}{literal}',
                zipcode:'{/literal}{$userInfo['zipcode']}{literal}',
                email:'{/literal}{$userInfo['email']}{literal}',
                city:'{/literal}{$userInfo['city']}{literal}'

                };
 var settings = null;
    var initializeExpoClient = function (options) {
        settings = $.extend({
            basketAmount:0,
            userInfo:[],
            defaultOrderNumber:'',
            notifyUrl:'',
            redirectSuccessUrl:'',
            redirectFailureUrl:''
        },options);


        Expo.API.afterLoad = function(){
            var sex = (settings.userInfo['salutation']==='mr')? 1:2;
            var house =  /\d+/.exec(settings.userInfo['street']);
            var street = (settings.userInfo['street']).replace(/[0-9]/g, '');

            Expo.API.setonce = {
                vorname:settings.userInfo['vorname'],
                name: settings.userInfo['lastname'],
                plz: settings.userInfo['zipcode'],
                email:settings.userInfo['email'],
                city: settings.userInfo['city'],
                street: street,
                house:house,
                sex:  sex
            };
            Expo.API.A.flags = 'W';
        };
        Expo.API.setProduct(settings.defaultOrderNumber, Math.round(settings.basketAmount));

        //**********************************************
        //StartNew()
        //**********************************************
        Expo.API.start();
        //**********************************************

        //
        Expo.API.notify = function (res) {
            var transactionID   = Expo.API.A.id;
            var pass = Expo.API.A.pass;
            var URL = settings.notifyUrl;
            switch (res.state){
                case 'ok':
                    var secilink  = res.cesi;
                    var pdflink  = res.pdf;

                    var data = {transactionID:transactionID,status:'ok'};
                    var dataType = 'html';
                    var successCallback = function () {
                        window.location.href = settings.redirectSuccessUrl;
                        console.log(settings.redirectSuccessUrl);
                    };
                    $.get(URL,data,successCallback,dataType);
                    break;

                case 'abgelehnt':
                case 'paid':
                case'check ':
                default:
                    data = {transactionID:transactionID,status:'abgelehnt'};
                    dataType = 'html';
                    successCallback = function () {
                        window.location.href = settings.redirectFailureUrl;
                    };
                    $.get(URL,data,successCallback,dataType);
                    break;
            }


        };

    };
            initializeExpoClient({
                basketAmount: {/literal}{$basketAmount}{literal},

                defaultOrderNumber: {/literal}{$defaultOrderNumber}{literal},
                notifyUrl: '{/literal}{url module=frontend controller=comer_expo_interface action=notify}{literal}',
                redirectSuccessUrl: '{/literal}{url module=frontend controller=checkout action=finish}{literal}',
                redirectFailureUrl: '{/literal}{url module=frontend controller=payment_expo action=error}{literal}',
                userInfo: userInfo
        });

        });
        {/literal}
    </script>

{/block}


