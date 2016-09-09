<table class="table table-striped" style="border: 0; width: 100%;">
	<thead>
		<tr>
			<th>{l s='Language' mod='cartabandonmentpro'}</th>
			<th>{l s='Template name' mod='cartabandonmentpro'}</th>
			<th>{l s='Reminder' mod='cartabandonmentpro'}</th>
			<th>{l s='Shared template' mod='cartabandonmentpro'}</th>
			<th>{l s='Visualize' mod='cartabandonmentpro'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$templates item=template}
			<tr class="{$template.id_template} {$template.id_lang} tpl_list">
				<td>{$template.lang_name}</td>
				<td>{if $template.tpl_same eq 1} {$templates.0.template_name} {else} {$template.template_name} {/if}</td>
				<td>{$template.wich_remind}</td>
				<td>{if $template.tpl_same eq 1}Oui{else}Non{/if}</td>
				<td><img src="{$module_dir}img/eye.png" style="cursor:hand;cursor:pointer;" onClick="previewTemplate({if $template.tpl_same eq 1} {$id_tpl_same} {else} {$template.id_template} {/if}, '{$token}')"></td>
			</tr>
		{/foreach}
	</tbody>
</table>
<br>
<div class="row">
	<div class="col-xs-12">
		<h4>{l s='Send a test' mod='cartabandonmentpro'}</h4>
	</div>
</div>
<div class="row">
  <div class="col-xs-6">
	<input type="text" class="form-control" id="test_mail" name="test_mail" placeholder="{l s='Email' mod='cartabandonmentpro'}">
  </div>
  <div class="col-xs-4">
	<button class="btn btn-primary" onClick="mailTest({$id_lang}, {$id_shop}, '{$token}');return false;">{l s='Send' mod='cartabandonmentpro'}</button>
  </div>
</div>