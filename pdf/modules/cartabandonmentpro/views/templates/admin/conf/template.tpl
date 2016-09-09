	<div class="form-group">
		<label class="col-sm-5 control-label" for="tpl_same">
			{l s='Do you want the template to be identical for all your reminders ?' mod='cartabandonmentpro'}
		</label>
		<span style="float:left;" class="switch prestashop-switch input-group col-lg-2">
			<input type="radio" name="tpl_same" id="tpl_same_on" {if isset($templates.0.tpl_same) and $templates.0.tpl_same eq 1}checked="checked"{/if}value="1"/>
			<label for="tpl_same_on" class="radioCheck" onClick="tplSame(1);">
				<i class="color_success"></i> {l s='Yes' mod='cartabandonmentpro'}
			</label>
			<input type="radio" name="tpl_same" id="tpl_same_off" value="0" {if !isset($templates.0.tpl_same) or $templates.0.tpl_same eq 0}checked="checked"{/if} />
			<label for="tpl_same_off" class="radioCheck" onClick="tplSame(0);">
				<i class="color_danger"></i> {l s='No' mod='cartabandonmentpro'}
			</label>
			<a class="slide-button btn"></a>
		</span>
		<input type="hidden" id="tpl_same" name="tpl_same" value="{if isset($templates.0.tpl_same)}{$templates.0.tpl_same}{/if}" />
	</div>
	<br>
	<div class="row form-group" style="margin-left: 5px;clear:both;">
		<div class="col-sm-5">
			<select class="form-control" id="wich_template" name="wich_template" style="{if isset($templates.0.tpl_same) and $templates.0.tpl_same eq 1}display: none;{/if}" onChange="changeTemplate();">
				<option id="wich_template_1" value="1">{l s='First reminder' mod='cartabandonmentpro'}</option>
				<option id="wich_template_2" value="2">{l s='Second reminder' mod='cartabandonmentpro'}</option>
				<option id="wich_template_3" value="3">{l s='Third reminder' mod='cartabandonmentpro'}</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label">
			{l s='Message title' mod='cartabandonmentpro'}
		</label>
		<div class="col-sm-4">
			<input type="text" placeholder="" name="name_1" id="template_name_1" value="{if isset($templates.0.template_name)}{$templates.0.template_name|escape:'htmlall':'UTF-8'}{/if}" class="form-control template_names" 
			>
			<input type="text" placeholder="" name="name_2" id="template_name_2" value="{if isset($templates.1.template_name)}{$templates.1.template_name|escape:'htmlall':'UTF-8'}{/if}" class="form-control template_names" 
			style="display:none">
			<input type="text" placeholder="" name="name_3" id="template_name_3" value="{if isset($templates.2.template_name)}{$templates.2.template_name|escape:'htmlall':'UTF-8'}{/if}" class="form-control template_names" 
			style="display:none">
		</div>
		<br><br><br>
		{l s='You can customize your abandoned cart titles by adding your client first and last name or the products that were on the abandonned cart by using the following tags.' mod='cartabandonmentpro'}
		<br>
        %FIRSTNAME% - {l s='will be replace by the client first name' mod='cartabandonmentpro'}<br>
        %LASTNAME% - {l s='will be replace by the client last name' mod='cartabandonmentpro'}<br>
		%GENDER% - {l s='will be replace by the client gender' mod='cartabandonmentpro'}<br>
        %CART_PRODUCTS% - {l s='will be replace by the custumer\'s cart content' mod='cartabandonmentpro'}<br>
        {l s='i.e. Mr' mod='cartabandonmentpro'} %FIRSTNAME%  %LASTNAME%, {l s='your products are waiting for you !' mod='cartabandonmentpro'}<br><br>
		{l s='In order to insert a link pointing to your store : click %SHOP_LINK_OPEN%here%SHOP_LINK_CLOSE% to access to our shop.' mod='cartabandonmentpro'}<br>
		{l s='In order to insert an unsubscribe link : click %UNUBSCRIBE_OPEN%here%UNUBSCRIBE_CLOSE% to not receive future emails.' mod='cartabandonmentpro'}<br>
		{l s='The unsubscribe link is mandatory in every commercial email.' mod='cartabandonmentpro'}<br><br>
		
		<p>
		{l s='If you have to send emails for French prospects, you have to add the CNIL mention ' mod='cartabandonmentpro'}
		(
		{l s='you will find all the information in the following website : ' mod='cartabandonmentpro'}
		<a href="{l s='http://www.cnil.fr/english/' mod='cartabandonmentpro'}" target="_blank">{l s='http://www.cnil.fr/english/' mod='cartabandonmentpro'}</a>
		)
		<br>
		{l s='CNIL mention: ' mod='cartabandonmentpro'} Conformément à la loi informatique et libertés du 06/01/1978 (art. 27, 34)
		vous disposez d'un droit d'accès, de modification et de suppression des données vous concernant.
		</p>

		<br>
	</div>
	<div>
		<p>
			<a onClick="exempleTemplate(1, '{$token}', '{$iso_lang}')">{l s='Template example' mod='cartabandonmentpro'}</a>
		</p>
	</div>
	<!-- Template 1 -->
	<div id="model1_tpl1" class="row picto_model picto_tpl_1 tpl1" style="margin-bottom: 50px;float: left;margin-left: 5px;">
		<div style="width: 115px; height: 150px; background-image:url({$module_dir}model/1.png); margin: auto;" onClick="selectModel(1, 1);">&nbsp;</div>
	</div>
	<div id="model1_tpl2" class="row picto_model picto_tpl_2 tpl2" style="margin-bottom: 50px;display:none;float: left;margin-left: 60px;">
		<div style="width: 115px; height: 150px; background-image:url({$module_dir}model/1.png); margin: auto;" onClick="selectModel(1, 2);">&nbsp;</div>
	</div>
	<div id="model1_tpl3" class="row picto_model picto_tpl_3 tpl3" style="margin-bottom: 50px;display:none;float: left;margin-left: 60px;">
		<div style="width: 115px; height: 150px; background-image:url({$module_dir}model/1.png); margin: auto;" onClick="selectModel(1, 3);">&nbsp;</div>
	</div>
	<!-- Template 2 -->                    
	<div id="model2_tpl1" class="row picto_model picto_tpl_1 tpl1" style="margin-bottom: 50px;float: left;margin-left: 60px;">
		<div style="width: 115px; height: 150px; background-image:url({$module_dir}model/2.png); margin: auto;" onClick="selectModel(2, 1);">&nbsp;</div>
	</div>
	<div id="model2_tpl2" class="row picto_model picto_tpl_2 tpl2" style="margin-bottom: 50px;display:none;float: left;margin-left: 60px;">
		<div style="width: 115px; height: 150px; background-image:url({$module_dir}model/2.png); margin: auto;" onClick="selectModel(2, 2);">&nbsp;</div>
	</div>
	<div id="model2_tpl3" class="row picto_model picto_tpl_3 tpl3" style="margin-bottom: 50px;display:none;float: left;margin-left: 60px;">
		<div style="width: 115px; height: 150px; background-image:url({$module_dir}model/2.png); margin: auto;" onClick="selectModel(2, 3);">&nbsp;</div>
	</div>
	
	<div id="model" style="width:1024px; clear:both; margin-left: 5px;">
		{if $editor == 0}
			<!-- Template 1 -->
			<div style="width: 1024px; height: auto; margin: auto; display: none; border: 1px solid black;" id="model_1_1" class="models model_1" onClick="setModel(1);">
				{include file="../../../../model/1_form.tpl"}
			</div>
			<div style="width: 1024px; height: auto; margin: auto; display: none; border: 1px solid black;" id="model_1_2" class="models model_1" onClick="setModel(1);">
				{include file="../../../../model/1_form2.tpl"}
			</div>
			<div style="width: 1024px; height: auto; margin: auto; display: none; border: 1px solid black;" id="model_1_3" class="models model_1" onClick="setModel(1);">
				{include file="../../../../model/1_form3.tpl"}
			</div>
			<!-- Template 2 -->
			<div style="width: 1024px; height: auto; margin: auto; display: none; border: 1px solid black;" id="model_2_1" class="models model_2" onClick="setModel(2);">
				{include file="../../../../model/2_form.tpl"}
			</div>
			<div style="width: 1024px; height: auto; margin: auto; display: none; border: 1px solid black;" id="model_2_2" class="models model_2" onClick="setModel(2);">
				{include file="../../../../model/2_form2.tpl"}
			</div>
			<div style="width: 1024px; height: auto; margin: auto; display: none; border: 1px solid black;" id="model_2_3" class="models model_2" onClick="setModel(2);">
				{include file="../../../../model/2_form3.tpl"}
			</div>
			
			<input type="hidden" name="model1" id="model1" value="1">
			<input type="hidden" name="model2" id="model2" value="1">
			<input type="hidden" name="model3" id="model3" value="1">
		{else}
			<!-- Created templates -->
			<div style="width: 1024px; height: auto; margin: auto;" id="model_{$edit_model_id1}_1" class="models model_{$edit_model_id1} reminder1">
				{include file="../../../../tpls/$template_file_1"}
			</div>
			<div style="width: 1024px; height: auto; margin: auto;display: none;" id="model_{$edit_model_id2}_2" class="models model_{$edit_model_id2} reminder2">
				{include file="../../../../tpls/$template_file_2"}
			</div>
			<div style="width: 1024px; height: auto; margin: auto;display: none;" id="model_{$edit_model_id3}_3" class="models model_{$edit_model_id3} reminder3">
				{include file="../../../../tpls/$template_file_3"}
			</div>
			
			<!-- Template 2 -->
			{if $edit_model_id1 neq "2"}
			<div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_2_1" class="models model_2 reminder1">
				{include file="../../../../model/2_form.tpl"}
			</div>
			{/if}
			{if $edit_model_id2 neq "2"}
			<div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_2_2" class="models model_2 reminder2">
				{include file="../../../../model/2_form2.tpl"}
			</div>
			{/if}
			{if $edit_model_id3 neq "2"}
			<div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_2_3" class="models model_2 reminder3">
				{include file="../../../../model/2_form3.tpl"}
			</div>
			{/if}
			
			<!-- Template 1 -->
			{if $edit_model_id1 neq "1"}
			<div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_1_1" class="models model_1 reminder1">
				{include file="../../../../model/1_form.tpl"}
			</div>
			{/if}
			{if $edit_model_id2 neq "1"}
			<div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_1_2" class="models model_1 reminder2">
				{include file="../../../../model/1_form2.tpl"}
			</div>
			{/if}
			{if $edit_model_id3 neq "1"}
			<div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_1_3" class="models model_1 reminder3">
				{include file="../../../../model/1_form3.tpl"}
			</div>
			{/if}
			<input type="hidden" name="model1" id="model1" value="{$edit_model_id1}">
			<input type="hidden" name="model2" id="model2" value="{$edit_model_id2}">
			<input type="hidden" name="model3" id="model3" value="{$edit_model_id3}">
			<script>
				$("#model{$edit_model_id1}_tpl{$template_file_1}").css('border', '1px #585858 solid');
				$("#model{$edit_model_id2}_tpl{$template_file_2}").css('border', '1px #585858 solid');
				$("#model{$edit_model_id2}_tpl{$template_file_2}").css('border', '1px #585858 solid');
			</script>
		{/if}
	</div>
	<input type="hidden" name="edit" value="1">
	<input type="hidden" id="id_lang" name="id_lang" value="{$language}">
	<input type="hidden" id="tpl" name="tpl" value="1">
	<input type="hidden" id="edittpl" name="edittpl1" value="{if isset($edit_template_id1)}{$edit_template_id1}{/if}">
	<input type="hidden" id="edittpl" name="edittpl2" value="{if isset($edit_template_id2)}{$edit_template_id2}{/if}">
	<input type="hidden" id="edittpl" name="edittpl3" value="{if isset($edit_template_id3)}{$edit_template_id3}{/if}">
	<input type="hidden" name="uri" value="{$uri}">
	
	<input type="hidden" id="token_cartabandonment" name="token_cartabandonment" value="{$token}">
	<div class="row" style="margin-top: 5px;margin-left: 50px;">
		<div class="col-md-9">
			<button class="btn btn-teal btn-block btn-primary" type="submit">
				{l s='Save' mod='cartabandonmentpro'} <i class="fa fa-arrow-circle-right"></i>
			</button>
		</div>
	</div>
	<input type="hidden" name="id_shop" value="{$id_shop}">
</form>