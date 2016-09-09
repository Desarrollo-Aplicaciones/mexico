<b>{l s='To send reminders you have 2 possibilities.' mod='cartabandonmentpro'}</b><br><br>
{l s='You can enter the following urls in your browser' mod='cartabandonmentpro'}<br>
<div style="margin-left: 20px;margin-bottom: 10px;">
	{l s='First reminder:' mod='cartabandonmentpro'} {$url}modules/cartabandonmentpro/send.php?id_shop={$id_shop}&token={$token_send}&wich_remind=1<br>
	{l s='Second reminder:' mod='cartabandonmentpro'} {$url}modules/cartabandonmentpro/send.php?id_shop={$id_shop}&token={$token_send}&wich_remind=2<br>
	{l s='Third reminder:' mod='cartabandonmentpro'} {$url}modules/cartabandonmentpro/send.php?id_shop={$id_shop}&token={$token_send}&wich_remind=3<br>
</div>
{l s="You can set a cron's task (a recursive task that fulfills the sending of reminders)" mod='cartabandonmentpro'}<br>
<div style="margin-left: 20px;margin-bottom: 10px;">
	{l s='First reminder:' mod='cartabandonmentpro'} 0	*	*	*	* php -f {$dirname}/send.php?id_shop={$id_shop}&token={$token_send}&wich_remind=1<br>
	{l s='Second reminder:' mod='cartabandonmentpro'} 0	*	*	*	* php -f {$dirname}/send.php?id_shop={$id_shop}&token={$token_send}&wich_remind=2<br>
	{l s='Third reminder:' mod='cartabandonmentpro'} 0	*	*	*	* php -f {$dirname}/send.php?id_shop={$id_shop}&token={$token_send}&wich_remind=3<br>
</div>
{l s="How to configure a cron task ?" mod='cartabandonmentpro'}<br>
<div style="margin-left: 20px;">
	- {l s="On your server, the interface allows you to configure cron's tasks." mod='cartabandonmentpro'}<br>
	- {l s='If your server does not have an interface, you can contact a developer ' mod='cartabandonmentpro'} 
</div>