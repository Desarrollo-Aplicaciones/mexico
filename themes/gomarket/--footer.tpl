{if !$content_only}
    </div><!-- /Center -->
        {if isset($settings)}
                {if $page_name != 'index'}
                        {if (($settings->column == '2_column_right' || $settings->column == '3_column'))}
                                <!-- Left -->
                                <div id="right_column" class="{$settings->right_class} omega">
                                        {$HOOK_RIGHT_COLUMN}
                                </div>
                        {/if}
                {/if}
        {/if}
        </div><!--/columns-->
</div><!--/container_24-->
</div>
<!-- Footer -->		
            <div class="mode_footer">
                <div class="container_24">
                    <div id="footer" class="grid_24 clearfix  omega alpha">
                            {if isset($HOOK_CS_FOOTER_TOP) && $HOOK_CS_FOOTER_TOP}{$HOOK_CS_FOOTER_TOP}{/if}
                            {$HOOK_FOOTER}
                            {if isset($HOOK_CS_FOOTER_BOTTOM) && $HOOK_CS_FOOTER_BOTTOM}{$HOOK_CS_FOOTER_BOTTOM}{/if}
                            {if $PS_ALLOW_MOBILE_DEVICE}
                                    <p class="center clearBoth"><a href="{$link->getPageLink('index', true)}?mobile_theme_ok">{l s='Browse the mobile site'}</a></p>
                            {/if}
                    </div>
                </div>
            </div>
            <div id="toTop">top</div>
        </div><!--/page-->
    {/if}
    </body>
</html>
