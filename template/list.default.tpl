<? if ($this->get('title')) { ?><h1>{{ title }}</h1><? } ?>
<div class="list">
	{{ list_extraheader }}
	{{ list_globalaction }}
	{{ list_filter }}
	{{ list_content }}
	{{ list_paging }}
	{{ list_extrafooter }}
</div>
{{ list_returnlink }}
{{ list_js }}