<h2>{$name}</h2>

{if $errors}

<div class="alert error">

	<img src="../img/admin/warning.gif"/>

	<ul>

		{foreach from=$errors item=error}

			<li>{$error}</li>

		{/foreach}

	</ul>

</div>

{/if}


{if $message}
<p>{$message}</p>
{/if}


<p><a href="{$module_dir}readme_en.pdf">{l s='Module Documentation' mod='canonicalseo'}</a></p>


{if $settings|@count>0}

<fieldset>

	<legend>

		{l mod='canonicalseo' s='Settings'}

	</legend>
	<form method="POST">
		<table>
			{foreach from=$settings item=setting key=n}
			<TR>
				<TD>{$setting.l}:</TD>
				<TD>
				{if $setting.o}
					<select name="{$n}">
						{foreach from=$setting.o item=name key=val}
						<option value="{$val}"{if $setting.v==$val} selected="selected"{/if}>{$name}</option>
						{/foreach}
					</select>
				{else}
					<input type="text" name="{$n}" value="{$setting.v}" />
				{/if}
				</TD>
			</TR>
			{/foreach}
			<TR>
				<TD></TD>
				<TD><input type="submit" name="updatesettings" value="{l mod='canonicalseo' s='Update'}" class="button" /></TD>
			</TR>
		</table>
	</form>
</fieldset>
{/if}
<p></p>


<fieldset>

	<legend>

		{l mod='canonicalseo' s='Custom Redirects'}

	</legend>
    
    <h3>{l mod='canonicalseo' s='Add New/Edit'}</h3>
	<form method="POST">
		<table>
			<TR>
				<TD>{l mod='canonicalseo' s='Pattern'}:</TD>
				<TD>
                                    <input type="text" name="pattern" value="{if $redirect}{$redirect.pattern}{/if}" />
				</TD>
			</TR>
			<TR>
				<TD>{l mod='canonicalseo' s='Destination'}:</TD>
				<TD>
                                    <input type="text" name="destination" value="{if $redirect}{$redirect.destination}{/if}" />
				</TD>
			</TR>
			<TR>
				<TD>{l mod='canonicalseo' s='Type'}:</TD>
				<TD>
					<select name="type">
                                            {foreach from=$types item='type' key='val'}
                                            <option value="{$val}"{if $redirect && $redirect.type==$val} selected{/if}>{$type}</option>
                                            {/foreach}
					</select>
				</TD>
			</TR>
			<TR>
				<TD>{l mod='canonicalseo' s='Active'}:</TD>
				<TD>
					<select name="active">
                                            <option value="1"{if $redirect && $redirect.active==1} selected{/if}>{l mod='canonicalseo' s='Active'}</option>
                                            <option value="0"{if $redirect && $redirect.active==0} selected{/if}>{l mod='canonicalseo' s='Inactive'}</option>
					</select>
				</TD>
			</TR>
			<TR>
				<TD></TD>
				<TD><input type="submit" name="saveredirect" value="{l mod='canonicalseo' s='Save'}" class="button" /></TD>
			</TR>
		</table>
	</form>
    
    
                        {if $pages>1}
    <p>
			{section name=page loop=$pages}
				{if $smarty.section.page.iteration<5 or ($smarty.section.page.iteration<$currentpage+5 and $smarty.section.page.iteration>$currentpage-5) or $smarty.section.page.iteration>$pages-5}
					{if $smarty.section.page.iteration!=$currentpage}
					<a href="{$baseurl}&p={$smarty.section.page.iteration}">{$smarty.section.page.iteration}</a>
					{else}
					{$smarty.section.page.iteration}
					{/if}
				{else}
					{if !$middle1 and $smarty.section.page.iteration>5 and $smarty.section.page.iteration<$currentpage-5}
					.....
					{assign var="middle1" value="1"}
					{else}
					{if !$middle2 and $smarty.section.page.iteration>$currentpage+5 and $smarty.section.page.iteration<$pages-5}
					.....
					{assign var="middle2" value="1"}
					{/if}
					{/if}
				{/if}
			{/section}
    </p>
                        {/if}
    <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <th>{l s='Pattern' mod='canonicalseo'}</th>
            <th>{l s='Destination' mod='canonicalseo'}</th>
            <th>{l s='Type' mod='canonicalseo'}</th>
            <th>{l s='Active' mod='canonicalseo'}</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$redirects item='redirect' name='red'}
        <tr{if $smarty.foreach.red.iteration%2==1} class="alt_row"{/if}>
            <td>{$redirect.pattern}</td>
            <td>{$redirect.destination}</td>
            <td>{$types[$redirect.type]}</td>
            <td><img src="../img/admin/{if $redirect.active==1}enabled{else}disabled{/if}.gif" /></td>
            <td><a href="{$baseurl}&id_redirect={$redirect.id_redirect}"><img title="{l s='Edit' mod='canonicalseo'}" src="../img/admin/edit.gif" /></a></td>
            <td><a href="{$baseurl}&deleteredirect={$redirect.id_redirect}" onclick="return confirm('{l s='Are you sure you wish to delete this item?' mod='canonicalseo'}');"><img title="{l s='Delete' mod='canonicalseo'}" src="../img/admin/delete.gif" /></a></td>
        </tr>
        {/foreach}
    </table>
</fieldset>
<p></p>

