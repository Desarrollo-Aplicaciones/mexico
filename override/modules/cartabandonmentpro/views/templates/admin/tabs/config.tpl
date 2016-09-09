{if $edit eq 1}
	<div class="alert alert-success">
		{l s='Sauvegarde réussie' mod='cartabandonmentpro'}
	</div>
{/if}
<h3><i class="icon-book"></i> {l s='Configurez vos relances de paniers abandonnés' mod='cartabandonmentpro'}</h3>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-credit-card"></i>
				1 - {l s='Language' mod='cartabandonmentpro'}
			</div>
			<div class="panel-body">
				{include file="../conf/lang.tpl"}
				<h4>{l s='Templates list' mod='cartabandonmentpro'}</h4>
				{include file="../conf/templates_list.tpl"}
			</div>
		</div>
	</div>
</div>
<div class="row" style="margin-top: 25px;">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-cogs"></i>
				2 - {l s='Reminders frequencies' mod='cartabandonmentpro'}
			</div>
			<div class="panel-body">
				{include file="../conf/reminders.tpl"}
			</div>
		</div>
	</div>
</div>
<div class="row" style="margin-top: 25px;">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-cogs"></i>
				3 - {l s='Personalize your templates' mod='cartabandonmentpro'}
			</div>
			<div class="panel-body">
				{include file="../conf/template.tpl"}
			</div>
		</div>
	</div>
</div>
<!--
<div class="row" style="margin-top: 25px;">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-cogs"></i>
				4 - {l s='Templates' mod='cartabandonmentpro'}
			</div>
			<div class="panel-body">
				{include file="../conf/templates_list.tpl"}
			</div>
		</div>
	</div>
</div>
-->
<div class="row" style="margin-top: 25px;">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-cogs"></i>
				4 - {l s='Relances' mod='cartabandonmentpro'}
			</div>
			<div class="panel-body">
				{include file="../conf/cron.tpl"}
			</div>
		</div>
	</div>
</div>