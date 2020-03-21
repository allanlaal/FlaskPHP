<?php if ($this->get('title')) { ?><h1>{{ title }}</h1><?php } ?>
<div class="form">
	{{ form_begin }}
	{{ form_field }}
	<?php if ($this->get('notes')) { ?><div class="notes">{{ notes }}</div><?php } ?>
	<?php if ($this->get('js')) { ?><script language="JavaScript"> $(function(){ {{ js }} }); </script><?php } ?>
	{{ form_submit }}
	{{ form_end }}
</div>