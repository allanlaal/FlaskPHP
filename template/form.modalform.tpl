<div class="modalform">
	{{ form_begin }}
	{{ form_field }}
	<? if ($this->get('notes')) { ?><div class="notes">{{ notes }}</div><? } ?>
	{{ form_submit }}
	{{ form_end }}
</div>