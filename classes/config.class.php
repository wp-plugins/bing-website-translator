<?php
 
class PrisnaBWTConfig {
	
	const NAME = 'PrisnaBWT';
	const UI_NAME = 'Bing Website Translator by Prisna';
	const WIDGET_NAME = 'Prisna BWT';
	const WIDGET_INTERNAL_NAME = 'prisna-bing-website-translator';
	const ADMIN_SETTINGS_NAME = 'prisna-bing-website-translator-settings';
	const ADMIN_SETTINGS_IMPORT_EXPORT_NAME = 'prisna-bing-website-translator-plugin-import-export-settings';
	const DB_SETTINGS_NAME = 'prisna-bing-website-translator-settings';
	
	protected static $_settings = null;

	public static function getName($_to_lower=false, $_ui=false) {
		
		if ($_ui)
			return $_to_lower ? strtolower(self::UI_NAME) : self::UI_NAME;
		else
			return $_to_lower ? strtolower(self::NAME) : self::NAME;
		
	}

	public static function getWidgetName($_internal=false) {
	
		return $_internal ? self::WIDGET_INTERNAL_NAME : self::WIDGET_NAME;
		
	}

	public static function getVersion() {
	
		return PRISNA_BWT__VERSION;
		
	}	

	public static function getAdminHandle() {
		
		return self::ADMIN_SETTINGS_NAME;
		
	}

	public static function getAdminImportExportHandle() {
		
		return self::ADMIN_SETTINGS_IMPORT_EXPORT_NAME;
		
	}

	public static function getDbSettingsName() {
		
		return self::DB_SETTINGS_NAME;
		
	}

	protected static function _get_settings() {
		
		$option = get_option(self::getDbSettingsName());
		return !$option ? array() : $option;
		
	}
	
	public static function getSettings($_force=false, $_direct=false) {
		
		if (is_array(self::$_settings) && $_force == false)
			return self::$_settings;
		
		$current = self::_get_settings();

		if ($_direct)
			return $current;

		$defaults = self::getDefaults();

		$result = PrisnaBWTCommon::mergeArrays($defaults, $current);

		$result = self::_adjust_languages($result, $current);
		
		return self::$_settings = $result;
		
	}

	protected static function _adjust_languages($_settings, $_current) {
		
		$result = $_settings;
		
		if (array_key_exists('languages', $_current))
			$result['languages']['value'] = $_current['languages']['value'];
		
		return $result;
		
	}
	
	public static function getSetting($_name, $_force=false) {
		
		$settings = self::getSettings($_force);
		
		return array_key_exists($_name, $settings) ? $settings[$_name] : null;
		
	}

	protected static function _compare_settings($_id, $_setting_1, $_setting_2) {
		
		if (PrisnaBWTCommon::endsWith($_id, '_template') || PrisnaBWTCommon::endsWith($_id, '_template_dd'))
			return PrisnaBWTCommon::stripBreakLinesAndTabs($_setting_1['value']) == PrisnaBWTCommon::stripBreakLinesAndTabs($_setting_2['value']);
		
		if ($_id == 'override')
			if ($_setting_1['value'] != $_setting_2['value'] && PrisnaBWTValidator::isEmpty($_setting_1['value']))
				return true;
		
		if ($_id == 'languages')
			return $_setting_1['value'] === $_setting_2['value'];
			
		return $_setting_1['value'] == $_setting_2['value'];
		
	}
	
	protected static function _get_settings_values_for_export() {
		
		$settings = self::_get_settings();
		
		return count($settings) > 0 ? base64_encode(serialize($settings)) : __('No settings to export. The current settings are the default ones.', 'prisna-bwt');
		
	}
	
	public static function getSettingsValues($_force=false, $_new=true) {
		
		$result = array();
		$settings = self::getSettings($_force);
				
		$defaults = self::getDefaults();

		foreach ($settings as $key => $setting) {
		
			if (!array_key_exists($key, $defaults))
				continue;
		
			if ($_new == false || !self::_compare_settings($key, $setting, $defaults[$key])) {
				$result[$key] = array(
					'value' => $setting['value'],
					'option_id' => array_key_exists('option_id', $setting) ? $setting['option_id'] : null
				);
			}
			
		}
			
		return $result;

	}
	
	public static function getSettingValue($_name, $_force=false) {
		
		$setting = self::getSetting($_name, $_force);
		
		if (is_null($setting))
			return null;
		
		$result = $setting['value'];
		
		if (PrisnaBWTValidator::isBool($result))
			$result = $result == 'true' || $result === true;
		
		return $result;
		
	}

	public static function getDefaults($_force=false) {
		
		$settings = self::_get_settings();
		
		$result = array(

			'usage' => array(
				'title_message' => __('Usage', 'prisna-bwt'),
				'description_message' => '',
				'id' => 'prisna_usage',
				'type' => 'usage',
				'value' => sprintf(__('
				
				- Go to the <em>Appereance &gt; Widgets</em> panel, search for the following widget<br /><br />
				
				<code>%s</code><br /><br />
				
				- Copy and paste the following code into pages, posts, etc...<br /><br />
				
				<code>[prisna-bing-website-translator]</code><br /><br />
				
				- Copy and paste the following code into any PHP file<br /><br />
				
				<code>&lt;?php echo do_shortcode(\'[prisna-bing-website-translator]\'); ?&gt;</code><br />
				
				', 'prisna-bwt'), self::getWidgetName()),
				'group' => 1
			),
			
			'premium' => array(
				'title_message' => '',
				'description_message' => '',
				'id' => 'prisna_usage',
				'type' => 'premium',
				'value' => '',
				'group' => 4
			),

			'from' => array(
				'title_message' => __('Website\'s language', 'prisna-bwt'),
				'description_message' => __('Sets the website\'s source language.', 'prisna-bwt'),
				'id' => 'prisna_from',
				'option_id' => 'pageLanguage',
				'type' => 'select',
				'values' => PrisnaBWTCommon::getLanguages(),
				'value' => 'en',
				'group' => 1
			),

			'when' => array(
				'title_message' => __('When to translate', 'prisna-bwt'),
				'description_message' => __('Sets when the translation takes place.', 'prisna-bwt'),
				'id' => 'prisna_when',
				'type' => 'radio',
				'value' => 'manual',
				'values' => array(
					'manual' => __('Manual, translate when visitor clicks on the translate button', 'prisna-bwt'),
					'auto' => __('Auto, translate automatically based on the visitor\'s browser language', 'prisna-bwt')
				),
				'group' => 1
			),
			
			'style' => array(
				'title_message' => __('Style mode', 'prisna-bwt'),
				'id' => 'prisna_style',
				'values' => array(
					'dark' => PRISNA_BWT__IMAGES . '/style_dark.png',
					'light' => PRISNA_BWT__IMAGES . '/style_light.png'
				),
				'value' => 'dark',
				'type' => 'visual',
				'col_count' => 2,
				'group' => 1
			),
			
			'align_mode' => array(
				'title_message' => __('Align mode', 'prisna-bwt'),
				'description_message' => __('Sets the alignment mode of the translator within its container.', 'prisna-bwt'),
				'id' => 'prisna_align_mode',
				'type' => 'radio',
				'value' => 'left',
				'values' => array(
					'left' => __('Left', 'prisna-bwt'),
					'right' => __('Right', 'prisna-bwt')
				),
				'group' => 1
			),
			
			'show_flags' => array(
				'title_message' => __('Show flags over translator', 'prisna-bwt'),
				'description_message' => __('Sets whether to display a few flags over the translator, or not.', 'prisna-bwt'),
				'id' => 'prisna_show_flags',
				'type' => 'toggle',
				'value' => 'false',
				'values' => array(
					'true' => __('Yes, show flags', 'prisna-bwt'),
					'false' => __('No, don\'t show flags', 'prisna-bwt')
				),
				'group' => 1
			),
			
			'languages' => array(
				'title_message' => __('Select languages', 'prisna-bwt'),
				'description_message' => __('Sets the available languages to display over the translator.', 'prisna-bwt'),
				'title_order_message' => __('Languages order', 'prisna-bwt'),
				'description_order_message' => __('Defines the order to display the languages.', 'prisna-bwt'),
				'id' => 'prisna_languages',
				'values' => PrisnaBWTCommon::getLanguages(),
				'value' => array('en', 'es', 'de', 'fr', 'pt', 'da'),
				'type' => 'language',
				'enable_order' => true,
				'columns' => 4,
				'dependence' => array('show_flags', 'display_mode'),
				'dependence_show_value' => array('true', 'inline'),
				'group' => 1
			),
			
			'test_mode' => array(
				'title_message' => __('Test mode', 'prisna-bwt'),
				'description_message' => __('Sets whether the translator is in test mode or not. In "test mode", the translator will be displayed only if the current logged in user has admin privileges.<br />Is useful for setting up the translator without letting visitors to see the changes while the plugin is being implemented.', 'prisna-bwt'),
				'id' => 'prisna_test_mode',
				'type' => 'toggle',
				'value' => 'false',
				'values' => array(
					'true' => __('Yes, enable test mode', 'prisna-bwt'),
					'false' => __('No, disable test mode', 'prisna-bwt')
				),
				'group' => 2
			),

			'custom_css' => array(
				'title_message' => __('Custom CSS', 'prisna-bwt'),
				'description_message' => __('Defines custom CSS rules.', 'prisna-bwt'),
				'id' => 'prisna_custom_css',
				'type' => 'textarea',
				'value' => '',
				'group' => 2
			),

			'display_heading' => array(
				'title_message' => __('Hide on pages, posts and categories', 'prisna-bwt'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_display_heading',
				'type' => 'heading',
				'group' => 2
			),
			
			'exclude_pages' => array(
				'title_message' => __('Pages', 'prisna-bwt'),
				'description_message' => __('Selects the pages where the translator should not be displayed.', 'prisna-bwt'),
				'id' => 'prisna_exclude_pages',
				'value' => array(''),
				'type' => 'expage',
				'dependence' => 'display_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),

			'exclude_posts' => array(
				'title_message' => __('Posts', 'prisna-bwt'),
				'description_message' => __('Selects the posts where the translator should not be displayed.', 'prisna-bwt'),
				'id' => 'prisna_exclude_posts',
				'value' => array(''),
				'type' => 'expost',
				'dependence' => 'display_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'exclude_categories' => array(
				'title_message' => __('Categories', 'prisna-bwt'),
				'description_message' => __('Selects the categories where the translator should not be displayed.', 'prisna-bwt'),
				'id' => 'prisna_exclude_categories',
				'value' => array(''),
				'type' => 'excategory',
				'dependence' => 'display_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'templates_heading' => array(
				'title_message' => __('Templates', 'prisna-bwt'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_templates_heading',
				'type' => 'heading',
				'group' => 2
			),
			
			'flags_container_template' => array(
				'title_message' => __('Flags container template', 'prisna-bwt'),
				'description_message' => __('Sets the flags\' container template. New templates can be created if the provided one doesn\'t fit the web page requirements.', 'prisna-bwt'),
				'id' => 'prisna_flags_container_template',
				'type' => 'textarea',
				'value' => '<ul id="prisna-bwt-flags-container" class="prisna-bwt-align-{{ align_mode }} notranslate" style="display: none;">
	{{ content }}
</ul>',
				'dependence' => 'templates_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'flag_template' => array(
				'title_message' => __('Flag template', 'prisna-bwt'),
				'description_message' => __('Sets the flag\'s template. New templates can be created if the provided one doesn\'t fit the web page requirements.', 'prisna-bwt'),
				'id' => 'prisna_flag_template',
				'type' => 'textarea',
				'value' => '<li class="prisna-bwt-flag-container prisna-bwt-language-{{ language_code }}">
	<a href="javascript:;" onclick="PrisnaBWT.translate(\'{{ language_code }}\'); return false;" title="{{ language_name }}"><img src="{{ flags_path }}{{ language_name_no_space }}.gif" alt="{{ language_name }}"/></a>
</li>',
				'dependence' => 'templates_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'import' => array(
				'title_message' => __('Import settings', 'prisna-bwt'),
				'description_message' => __('Imports previously exported settings. Paste the previously exported settings in the field. If the data\'s structure is correct, it will overwrite the current settings.', 'prisna-bwt'),
				'id' => 'prisna_import',
				'value' => '',
				'type' => 'textarea',
				'group' => 3
			),

			'export' => array(
				'title_message' => __('Export settings', 'prisna-bwt'),
				'description_message' => __('Exports the current settings to make a backup or to transfer the settings from the development server to the production server. Triple click on the field to select all the content.', 'prisna-bwt'),
				'id' => 'prisna_export',
				'value' => self::_get_settings_values_for_export(),
				'type' => 'export',
				'group' => 3
			)
			
		);
			
		return $result;
		
	}

}

?>
