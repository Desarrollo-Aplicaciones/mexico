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

<section class="panel widget allow_push">

<section id="social_login_customer_stat">
    <header>
        <i class="icon-AdminParentCustomer"></i> {l s='Social Login Statistics' mod='fbloginblock'}
    </header>

    <div id="dash_recent_orders" class="tab-content panel">
        <h3>{l s='Total registrations' mod='fbloginblock'} - {$fbloginblockcall_dash|escape:'htmlall':'UTF-8'}</h3>

        <div class="table-responsive">
            <table class="table data_table">
                <tbody>
                    {foreach $fbloginblockdata_dash as $text_type => $count_types}
                    <tr>
                        <td class="text-left">
                            <img title="{$text_type|escape:'htmlall':'UTF-8'}" alt="{$text_type|escape:'htmlall':'UTF-8'}"
                                 src="../modules/fbloginblock/views/img/{$text_type|escape:'htmlall':'UTF-8'}-small.png">
                        </td>
                        <td id="total_products" class="text-center">{if $count_types > 0}<b>{/if}{$count_types|escape:'htmlall':'UTF-8'}{if $count_types > 0}</b>{/if}</td>

                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

    </div>

</section>

</section>