<form id="myform" role="form" class="form-horizontal" action="#" method="post">
	<div id="duplicate" class="well well-sm">
		<p><i style="float:left; padding: 0px 10px 0px 3px;" class="icon-exclamation-triangle icon-3x"></i>
		{l s='Cart reminders must be sent to your prospects in their mother tongue.' mod='cartabandonmentpro'}
		<br>
		{l s='It is recommended to set cart reminders in the store default language for clients whom language is not defined.' mod='cartabandonmentpro'}
	</div>
	<div class="form-group">
		<div class="col-sm-3">
			<select name="language" id="language" class="form-control" onChange="changeLanguage();">
				{foreach from=$languages item=language}
					<option value="{$language.id_lang}" {if $id_lang == $language.id_lang} selected="selected" {/if}>{$language.name}</option>
				{/foreach}
			</select>
		</div>
	</div>