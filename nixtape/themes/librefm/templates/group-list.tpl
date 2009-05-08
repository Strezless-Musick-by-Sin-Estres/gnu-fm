{include file='header.tpl'}

<h2 property="dc:title">{t}All Groups{/t}</h2>

<div about="#groups" typeof="foaf:Group" property="foaf:name" content="All Groups">

<ul rel="foaf:member" class="userlist">
{foreach from=$groups item=g}

	<li about="{$g->id}" typeof="foaf:Group">
		<div class="group vcard">
			<div class="avatar" rel="foaf:depiction">
				<img src="{$g->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" />
			</div>
			<dl>
				<dt>
					<span class="fn org" property="foaf:name">{$g->fullname|escape:'html':'UTF-8'}</span>
					(<span class="nickname" property="foaf:nick">{$g->name|escape:'html':'UTF-8'}</span>)
				</dt>
				<dd>{if $g->homepage}<a class="url" rel="foaf:page" href="{$g->homepage|escape:'html':'UTF-8'}">{$g->homepage|escape:'html':'UTF-8'}</a>{/if}</dd>
				<dd class="note" property="dc:abstract">{$g->bio|escape:'html':'UTF-8'}</dd>
				<dd><a rel="foaf:homepage" rev="foaf:primaryTopic" property="dc:description" href="{$g->getURL()|escape:'html':'UTF-8'}">{t members='$g->count}%1 members{/t}</a></dd>
			</dl>
			<hr style="border: 1px solid transparent; clear: both;" />
		</div>
	</li>
	
{/foreach}
</ul>

</div>

{include file='footer.tpl'}
