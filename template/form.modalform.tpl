<div class="modalform">
	{{ form_begin }}
	{{ form_field }}
	<?php if ($this->get('notes')) { ?><div class="notes">{{ notes }}</div><?php } ?>
	{{ form_submit }}
	{{ form_end }}
</div>