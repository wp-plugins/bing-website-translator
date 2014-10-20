<div class="prisna_bwt_section prisna_bwt_{{ type }}{{ dependence.show.false:begin }} prisna_bwt_no_display{{ dependence.show.false:end }}{{ has_dependence.true:begin }} prisna_bwt_section_tabbed_{{ dependence_count }}{{ has_dependence.true:end }}" id="section_{{ id }}">
	
	<div class="prisna_bwt_tooltip"></div>
	<div class="prisna_bwt_description prisna_bwt_no_display">{{ description_message }}</div>
		
	<div class="prisna_bwt_title_container prisna_bwt_icon prisna_bwt_icon_grid2"><h3 class="prisna_bwt_title">{{ title_message }}</h3></div>
	<div class="prisna_bwt_setting">
		<div class="prisna_bwt_field">
			<select class="prisna_bwt_select" name="{{ id }}" id="{{ id }}">
{{ collection_formatted }}
			</select>
		</div>	
	</div>
	
	{{ has_dependence.true:begin }}
	<input type="hidden" name="{{ id }}_dependence" id="{{ id }}_dependence" value="{{ formatted_dependence }}" />
	<input type="hidden" name="{{ id }}_dependence_show_value" id="{{ id }}_dependence_show_value" value="{{ formatted_dependence_show_value }}" />
	{{ has_dependence.true:end }}	
	<div class="prisna_bwt_clear"></div>
	
</div>
