<div class="prisna_bwt_section prisna_bwt_{{ type }}{{ dependence.show.false:begin }} prisna_bwt_no_display{{ dependence.show.false:end }}{{ has_dependence.true:begin }} prisna_bwt_section_tabbed_{{ dependence_count }}{{ has_dependence.true:end }}" id="section_{{ id }}">
	
	<div class="prisna_bwt_tooltip"></div>
	<div class="prisna_bwt_description prisna_bwt_no_display">{{ description_message }}</div>
		
	<div class="prisna_bwt_title_container prisna_bwt_icon prisna_bwt_icon_grid2"><h3 class="prisna_bwt_title">{{ title_message }}</h3></div>
	<div class="prisna_bwt_setting">
		<div class="prisna_bwt_field" id="{{ id }}">
			<div class="prisna_bwt_toggle_container">
				<input type="radio" name="{{ name }}" value="{{ value_true }}"{{ value_true.checked.true:begin }} checked="checked"{{ value_true.checked.true:end }} id="{{ id }}_true" class="prisna_bwt_radio_option" />
				<label for="{{ id }}_true">{{ option_true }}</label>
			</div>
			<div class="prisna_bwt_toggle_container">
				<input type="radio" name="{{ name }}" value="{{ value_false }}"{{ value_false.checked.true:begin }} checked="checked"{{ value_false.checked.true:end }} id="{{ id }}_false" class="prisna_bwt_radio_option" />
				<label for="{{ id }}_false">{{ option_false }}</label>
			</div>
		</div>
		{{ has_dependence.true:begin }}
		<input type="hidden" name="{{ id }}_dependence" id="{{ id }}_dependence" value="{{ formatted_dependence }}" />
		<input type="hidden" name="{{ id }}_dependence_show_value" id="{{ id }}_dependence_show_value" value="{{ formatted_dependence_show_value }}" />
		{{ has_dependence.true:end }}
		<div class="prisna_bwt_clear"></div>
	
	</div>
</div>
