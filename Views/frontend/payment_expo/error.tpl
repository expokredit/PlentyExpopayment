{extends file='parent:frontend/index/index.tpl'}
{* http://srv-a-io.c-786.maxcluster.net/payment_expo/error *}
{block name="frontend_index_content"}
    <div id="error">
        <h1>Fehler während der Kreditanfrage</h1>
        <div class="alert is--error is--rounded">
            <div class="alert--icon">
                <i class="icon--element icon--warning"></i>
            </div>
            <div class="alert--content">
                <strong>Ihre Finanzierungsanfrage wurde leider abgelehnt.</strong>
                <p>
                    Bitte wählen Sie eine andere Zahlungsart aus, um Ihre Bestellung erfolgreich abzuschließen.<br /><br />Sie werden jetzt automatisch zum Checkout weitergeleitet.
                </p>
            </div>
        </div>
        <a href="" class="btn is--primary" style="float: right;margin-top: 10px;">zurück zum Checkout</a>

    </div>
{/block}

{block name='frontend_index_content_left'}
{/block}

{block name="frontend_index_header_javascript_jquery_lib" append}

    <script>
        {literal}
        $(function () {

            setTimeout(function () {
               var  redirectFailureUrl = '{/literal}{url module=frontend controller=checkout action=confirm}{literal}';
                window.location.href = redirectFailureUrl;
            },4000);

        });
        {/literal}
    </script>

{/block}