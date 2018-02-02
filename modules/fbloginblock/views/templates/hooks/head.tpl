{*
 *
 * 2011 - 2017 StorePrestaModules SPM LLC.
 *
 * MODULE fbloginblock
 *
 * @author    SPM <kykyryzopresto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   1.7.7
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=61669
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 *
*}

{if $fbloginblockis15 == 0}
    <link href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/css/blocks-fbloginblock.css" rel="stylesheet" type="text/css" media="all" />
    <link href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all" />
    <link href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/css/fbloginblock.css" rel="stylesheet" type="text/css" media="all" />
    {literal}
    <script type="text/javascript" src="{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/views/js/fbloginblock.js"></script>

    {/literal}
    {if $fbloginblockislogged}
        {if $fbloginblockapipopup == 1}
            {literal}
                <script type="text/javascript" src="{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/views/js/fbloginblock-apipopup.js"></script>
                <link href="{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/views/css/fbloginblock-apipopup.css" rel="stylesheet" type="text/css" media="all" />
            {/literal}
        {/if}
    {/if}
    {literal}

    {/literal}


{/if}



{literal}
<script type="text/javascript">

var fbloginblock_is17 = '{/literal}{$fbloginblockis17|escape:'htmlall':'UTF-8'}{literal}';
var fbloginblock_is16 = '{/literal}{$fbloginblockis16|escape:'htmlall':'UTF-8'}{literal}';
var fbloginblockapipopup = '{/literal}{$fbloginblockapipopup|escape:'htmlall':'UTF-8'}{literal}';
var fbloginblockislogged = '{/literal}{$fbloginblockislogged|escape:'htmlall':'UTF-8'}{literal}';


{/literal}
    {if !$fbloginblockislogged}
        {literal}


        var fbloginblock_login_buttons_footer = '{/literal}{$fbloginblocklbfooter nofilter}{literal}';
        var fbloginblock_login_buttons_top = '{/literal}{$fbloginblocklbtop nofilter}{literal}';
        var fbloginblock_login_buttons_authpage = '{/literal}{$fbloginblocklbauthpage nofilter}{literal}';
        var fbloginblock_login_buttons_beforeauthpage = '{/literal}{$fbloginblocklbbauthpage nofilter}{literal}';
        var fbloginblock_login_buttons_welcome = '{/literal}{$fbloginblocklbwelcome nofilter}{literal}';




        // amazon connect variables
        var fbloginblockis_ssl = '{/literal}{$fbloginblockis_ssl|escape:'htmlall':'UTF-8'}{literal}';
        var fbloginblockamazonci = '{/literal}{$fbloginblockamazonci nofilter}{literal}';
        var fbloginblockssltxt = '{/literal}{$fbloginblockssltxt|escape:'htmlall':'UTF-8'}{literal}';
        var fbloginblock_aerror = '{/literal}{$aerror|escape:'htmlall':'UTF-8'}{literal}';
        var fbloginblockamazon_url = '{/literal}{$fbloginblockamazon_url nofilter}{literal}';
        // amazon connect variables

        {/literal}


{else}


        {if $fbloginblockapipopup == 1}
        <!--  show popup for twitter or instagram customer which not changed email address  -->
        {literal}
            var fbloginblock_htmlapipopup = '{/literal}{$fbloginblockhtmlapipopup nofilter}{literal}';
            var fbloginblockupdate_email = '{/literal}{$fbloginblockupdate_email nofilter}{literal}';
            var fbloginblockcid = '{/literal}{$fbloginblockcid nofilter}{literal}';
        {/literal}
        <!--  show popup for twitter customer which not changed email address  -->
        {/if}

{/if}

{literal}
</script>
{/literal}
