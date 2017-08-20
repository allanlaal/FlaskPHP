<? if ($this->get('title')) { ?><h1>{{ title }}</h1><? } ?>
<div class="form">
	{{ form_begin }}
	{{ form_field }}
	<? if ($this->get('notes')) { ?><div class="notes">{{ notes }}</div><? } ?>
	{{ form_submit }}
	{{ form_end }}
</div>