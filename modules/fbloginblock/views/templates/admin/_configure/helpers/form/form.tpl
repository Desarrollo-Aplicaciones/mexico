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

{extends file="helpers/form/form.tpl"}
{block name="field"}
	{if $input.type == 'file_img'}
		<div class="col-lg-9">
			
			{* custom *}
			{*{$input.name|@var_dump}*}
			
			{foreach $input.name as $k_image => $val_image}
				{foreach $val_image as $k_image_level => $val_image_level}
					{foreach $val_image_level as $k_prefix_full => $image_path_full}
					{/foreach}
				{/foreach}
			{/foreach} 
			
				<div class="form-group">
					<div class="col-lg-6">
					
						<input id="post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}" type="file" name="post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}" class="hide" />
						
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}-name" type="text" class="disabled" name="filename" readonly />
							<span class="input-group-btn">
								<button id="post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='fbloginblock'}
								</button>
							</span>
						</div>
						
					</div>
					
					
				</div>
				<div class="form-group">
					{if isset($image_path_full) && $image_path_full != ''}
					<div id="{$k_image_level|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}-images-thumbnails" class="col-lg-12">
						<img src="{$image_path_full[0]|escape:'htmlall':'UTF-8'}" id="image{$k_image_level|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}" />
						 
						 {if strlen($image_path_full[1])>0}
	    					&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="image{$k_image_level|escape:'htmlall':'UTF-8'}-click{$k_image|escape:'htmlall':'UTF-8'}" 
	    										  style="text-decoration:underline" 
	    										  onclick="return_default_img('{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}','{l s='Are you sure you want to remove this item?' mod='fbloginblock'}')"
	    										  >{l s='Click here to return the default image' mod='fbloginblock'}</a>

                         {literal}
                             <script type="text/javascript">
                                 var ajax_link_fbloginblock = '{/literal}{$input.ajax_url|escape:'htmlall':'UTF-8'}{literal}';
                                 var token_fbloginblock = '{/literal}{$input.token_custom|escape:'htmlall':'UTF-8'}{literal}';
                             </script>
                         {/literal}

	    				{/if}
	    
					</div>
					{/if}
				</div>


				<script>
				$(document).ready(function(){
					$('#post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}-selectbutton').click(function(e){
						$('#post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}').trigger('click');
					});
					$('#post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}').change(function(e){
						var val = $(this).val();
						var file = val.split(/[\\/]/);
						$('#post_image_{$k_prefix_full|escape:'htmlall':'UTF-8'}{$k_image|escape:'htmlall':'UTF-8'}-name').val(file[file.length-1]);
					});
				});
			</script>
				
			{* custom *}
			
			
			{if isset($input.desc) && !empty($input.desc)}
				<p class="help-block">
					{$input.desc|escape:'htmlall':'UTF-8'}
				</p>
			{/if}
		</div>
		
		
		
		
	{elseif $input.type == 'cms_pages'}

		{assign var=cms value=$input.values}
		{assign var=current_value value=$input.selected_data}
		
		
		
				<div class="col-lg-9">
					<div class="panel">
					
						{*
						<div class="panel-heading">
							{$input.label}
						</div>
						*}
						
						<table class="table">
							<thead>
								<tr>
									
									<th><b>{l s='Position' mod='fbloginblock'}</b></th>
									<th><b>{l s='Type of connect for position' mod='fbloginblock'}</b></th>
                                    <th><b>{l s='Preview' mod='fbloginblock'}</b></th>
								</tr>
							</thead>
							<tbody>
								{*{$cms|@var_dump}*}
								
								{*<pre>{$cms|@var_dump}*}
								
								{foreach $cms['position'] as $key => $cms_item}
									<tr class="alt_row">
										<td>
										<div class="checkbox">
										   
											<label for="{$key|escape:'htmlall':'UTF-8'}"><input type="checkbox" {if $current_value.position.$key == $key} checked="checked"{/if} value="{$key|escape:'htmlall':'UTF-8'}" id="{$key|escape:'htmlall':'UTF-8'}" name="{$key|escape:'htmlall':'UTF-8'}"/>{$cms_item|escape:'htmlall':'UTF-8'}</label>
										</div>
											
										</td>
										<td>
										<div class="col-lg-9">
											<select id="sz{$key|escape:'htmlall':'UTF-8'}" class="col-sm-12" name="sz{$key|escape:'htmlall':'UTF-8'}">
											{foreach $cms['image'][$key] as $key_image => $cms_item_image}
													<option {if $current_value.image.$key == $key_image} selected="selected" {/if}
                                                            value="{$key_image|escape:'htmlall':'UTF-8'}">{$cms_item_image|escape:'htmlall':'UTF-8'}</option>
													
											{/foreach}	
											</select>
										</div>

                                        {literal}
                                        <script type="text/javascript">
                                            $(document).ready(function() {

                                            $('#sz{/literal}{$key|escape:'htmlall':'UTF-8'}{literal}').change(function() {
                                                //alert($(this).val());

                                                {/literal}{foreach $cms['image'][$key] as $key_image => $cms_item_image}{literal}
                                                $('#preview-{/literal}{$key_image|escape:'htmlall':'UTF-8'}{literal}').hide();
                                                {/literal}{/foreach}{literal}

                                                $('#preview-'+$(this).val()).show();
                                            });

                                            });

                                        </script>
                                        {/literal}

										</td>
                                        <td class="fbloginblock-connects">


                                            <!-- large image -->
                                            {foreach $input.img_large as $k_image_large => $val_image_large}
                                                {foreach $val_image_large as $k_image_level_large => $val_image_level_large}
                                                    {foreach $val_image_level_large as $k_prefix_full_large => $image_path_full_large}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}



                                            <img src="{$image_path_full_large[0]|escape:'htmlall':'UTF-8'}"
                                                 id="preview-l{$key|escape:'htmlall':'UTF-8'}"
                                                 style="display: {if $current_value.image.$key == "l{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}" />
                                            <!-- large image -->



                                            <!-- large_small image -->
                                            {foreach $input.img_large_small as $k_image_large_small => $val_image_large_small}
                                                {foreach $val_image_large_small as $k_image_level_large_small => $val_image_level_large_small}
                                                    {foreach $val_image_level_large_small as $k_prefix_full_large_small => $image_path_full_large_small}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}

                                            <img src="{$image_path_full_large_small[0]|escape:'htmlall':'UTF-8'}"
                                                 id="preview-ls{$key|escape:'htmlall':'UTF-8'}"
                                                 style="display: {if $current_value.image.$key == "ls{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}" />
                                            <!-- large_small image -->


                                            <!-- small image -->
                                            {foreach $input.img_small as $k_image_small => $val_image_small}
                                                {foreach $val_image_small as $k_image_level_small => $val_image_level_small}
                                                    {foreach $val_image_level_small as $k_prefix_full_small => $image_path_full_small}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}

                                            <img src="{$image_path_full_small[0]|escape:'htmlall':'UTF-8'}"
                                                 id="preview-s{$key|escape:'htmlall':'UTF-8'}"
                                                 style="display: {if $current_value.image.$key == "s{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}" />
                                            <!-- small image -->


                                            <!-- micro_small image -->
                                            {foreach $input.img_micro_small as $k_image_micro_small => $val_image_micro_small}
                                                {foreach $val_image_micro_small as $k_image_level_micro_small => $val_image_level_micro_small}
                                                    {foreach $val_image_level_micro_small as $k_prefix_full_micro_small => $image_path_full_micro_small}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}

                                            <img src="{$image_path_full_micro_small[0]|escape:'htmlall':'UTF-8'}"
                                                 id="preview-sm{$key|escape:'htmlall':'UTF-8'}"
                                                 style="display: {if $current_value.image.$key == "sm{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}" />
                                            <!-- micro_small image -->


                                            <!-- bootstrap large image -->
                                            {foreach $input.img_micro_small as $k_image_micro_small => $val_image_micro_small}
                                                {foreach $val_image_micro_small as $k_image_level_micro_small => $val_image_level_micro_small}
                                                    {foreach $val_image_level_micro_small as $k_prefix_full_micro_small => $image_path_full_micro_small}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}
                                            <a href="javascript:void(0)"
                                               id="preview-bl{$key|escape:'htmlall':'UTF-8'}"
                                               style="display: {if $current_value.image.$key == "bl{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}"
                                               class="{$k_prefix_full_large|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-1"
                                               title="{$k_prefix_full_large|escape:'htmlall':'UTF-8'|capitalize}"
                                                    ><i class="fa fa-{$k_prefix_full_large|escape:'htmlall':'UTF-8'}"
                                                        >&nbsp;{$k_prefix_full_large|escape:'htmlall':'UTF-8'|capitalize}</i></a>
                                            <!-- bootstrap large image -->

                                            <!-- bootstrap medium image -->
                                            {foreach $input.img_micro_small as $k_image_micro_small => $val_image_micro_small}
                                                {foreach $val_image_micro_small as $k_image_level_micro_small => $val_image_level_micro_small}
                                                    {foreach $val_image_level_micro_small as $k_prefix_full_micro_small => $image_path_full_micro_small}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}
                                            <a href="javascript:void(0)"
                                               id="preview-bls{$key|escape:'htmlall':'UTF-8'}"
                                               style="display: {if $current_value.image.$key == "bls{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}"
                                               class="{$k_prefix_full_large|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-2"
                                               title="{$k_prefix_full_large|escape:'htmlall':'UTF-8'|capitalize}"
                                                    ><i class="fa fa-{$k_prefix_full_large|escape:'htmlall':'UTF-8'}"
                                                        ></i></a>

                                            <!-- bootstrap medium image -->

                                            <!-- bootstrap small image -->
                                            {foreach $input.img_micro_small as $k_image_micro_small => $val_image_micro_small}
                                                {foreach $val_image_micro_small as $k_image_level_micro_small => $val_image_level_micro_small}
                                                    {foreach $val_image_level_micro_small as $k_prefix_full_micro_small => $image_path_full_micro_small}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}
                                            <a href="javascript:void(0)"
                                               id="preview-bs{$key|escape:'htmlall':'UTF-8'}"
                                               style="display: {if $current_value.image.$key == "bs{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}"
                                               class="{$k_prefix_full_large|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-3"
                                               title="{$k_prefix_full_large|escape:'htmlall':'UTF-8'|capitalize}"
                                                    ><i class="fa fa-{$k_prefix_full_large|escape:'htmlall':'UTF-8'}"
                                                        >&nbsp;{$k_prefix_full_large|escape:'htmlall':'UTF-8'|capitalize}</i></a>

                                            <!-- bootstrap small image -->


                                            <!-- bootstrap very small image -->
                                            {foreach $input.img_micro_small as $k_image_micro_small => $val_image_micro_small}
                                                {foreach $val_image_micro_small as $k_image_level_micro_small => $val_image_level_micro_small}
                                                    {foreach $val_image_level_micro_small as $k_prefix_full_micro_small => $image_path_full_micro_small}
                                                    {/foreach}
                                                {/foreach}
                                            {/foreach}
                                            <a href="javascript:void(0)"
                                               id="preview-bsm{$key|escape:'htmlall':'UTF-8'}"
                                               style="display: {if $current_value.image.$key == "bsm{$key|escape:'htmlall':'UTF-8'}"}block{else}none{/if}"
                                               class="{$k_prefix_full_large|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-4"
                                               title="{$k_prefix_full_large|escape:'htmlall':'UTF-8'|capitalize}"
                                                    ><i class="fa fa-{$k_prefix_full_large|escape:'htmlall':'UTF-8'}"
                                                        ></i></a>

                                            <!-- bootstrap very small image -->

                                        </td>
									</tr>
								{/foreach}	
								
								
							</tbody>
						</table>
					</div>
					{if isset($input.desc) && !empty($input.desc)}
						<p class="help-block">
							{$input.desc|escape:'htmlall':'UTF-8'}
						</p>
					{/if}
				</div>
		
	{elseif $input.type == 'checkbox_custom'}
		<div class="col-lg-9">
				
				{foreach $input.values.query as $value}
					{assign var=id_checkbox value=$value[$input.values.id]}
					<div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
						{strip}
							<label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">
								<input type="checkbox" name="{$id_checkbox|escape:'htmlall':'UTF-8'}" id="{$id_checkbox|escape:'htmlall':'UTF-8'}" 
									   class="{if isset($input.class)}{$input.class}{/if}"{if isset($value.val)} 
									   value="{$value.val|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
								{$value[$input.values.name]|escape:'htmlall':'UTF-8'}
							</label>
						{/strip}
					</div>
				{/foreach}		
				
				{if isset($input.desc) && !empty($input.desc)}
						<p class="help-block">
							{$input.desc|escape:'htmlall':'UTF-8'}
						</p>
				{/if}
		</div>		
		
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
