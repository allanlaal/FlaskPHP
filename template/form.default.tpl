<? if ($this->get('title')) { ?><h1>{{ title }}</h1><? } ?>
<div class="form">
	{{ form_begin }}
	{{ form_field }}
	<? if ($this->get('notes')) { ?><div class="notes">{{ notes }}</div><? } ?>
	<? if ($this->get('js')) { ?><script language="JavaScript"> $(function(){ {{ js }} }); </script><? } ?>
	{{ form_submit }}
	{{ form_end }}
</div>