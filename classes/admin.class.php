<?php

class PrisnaBWTAdmin {

	public static function initialize() {

		if (!is_admin())
			return;

		add_action('admin_init', array('PrisnaBWTAdmin', '_initialize'));
		add_action('plugins_loaded', array('PrisnaBWTAdmin', 'initializeMenus'));

	}
	
	public static function _initialize() {

		self::_watch_events();

		self::_select_language();
		
		self::_load_styles();
		self::_load_scripts();
		
	}
	
	protected static function _watch_events() {

		@header('X-XSS-Protection: 0');

		if (PrisnaBWTAdminEvents::isSavingSettings() || PrisnaBWTAdminEvents::isResetingSettings())
			if (!check_admin_referer(PrisnaBWTConfig::getAdminHandle(), '_prisna_bwt_nonce'))
				PrisnaBWTCommon::redirect(PrisnaBWTCommon::getAdminPluginUrl());

		if (PrisnaBWTAdminEvents::isSavingSettings())
			self::_save_settings();

		if (PrisnaBWTAdminEvents::isResetingSettings())
			self::_reset_settings();

	}
	
	protected static function _save_settings() {

		PrisnaBWTAdminForm::save();
		
	}
	
	protected static function _reset_settings() {
		
		PrisnaBWTAdminForm::reset();
		
	}

	protected static function _load_scripts() {

		if (PrisnaBWTAdminEvents::isLoadingAdminPage()) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-widget');
			wp_enqueue_script('jquery-ui-mouse');
			wp_enqueue_script('prisna-bwt-admin-common', PRISNA_BWT__JS .'/common.class.js', 'jquery-ui-core', PrisnaBWTConfig::getVersion(), true);
			wp_enqueue_script('prisna-bwt-admin', PRISNA_BWT__JS .'/admin.class.js', array(), PrisnaBWTConfig::getVersion());
		}

	}
	
	protected static function _load_styles() {

		if (PrisnaBWTAdminEvents::isLoadingAdminPage() || strpos(PrisnaBWTCommon::getAdminWidgetsUrl(), $_SERVER['REQUEST_URI']) !== false)
			wp_enqueue_style('prisna-bwt-admin', PRISNA_BWT__CSS .'/admin.css', false, PrisnaBWTConfig::getVersion(), 'screen');

	}
	
	public static function initializeMenus() {

		add_action('admin_menu', array('PrisnaBWTAdmin', '_add_options_page'));

	}

	public static function _add_options_page() {
		
		add_submenu_page('plugins.php', PrisnaBWTConfig::getName(false, true), PrisnaBWTConfig::getName(false, true), 'manage_options', PrisnaBWTConfig::getAdminHandle(), array('PrisnaBWTAdmin', '_render_main_form'));
		
	}

	protected static function _gen_meta_tag_rules_for_tabs() {
		
		$tabs = array(
			array('general', 'advanced', 'premium'),
			array('advanced_general', 'advanced_import_export')
		);

		$current_tabs = array(
			PrisnaBWTCommon::getVariable('prisna_tab', 'POST'),
			PrisnaBWTCommon::getVariable('prisna_tab_2', 'POST')
		);

		$result = self::_gen_meta_tag_rules_for_tabs_aux($tabs, $current_tabs);

		return $result;
		
	}
	
	protected static function _gen_meta_tag_rules_for_tabs_aux($_tabs, $_currents, $_level=0) {
		
		$result = array();

		if (!is_array($_tabs[0])) {

			$current = $_currents[$_level];

			if (PrisnaBWTValidator::isEmpty($current))
				$current = $_tabs[0];

			for ($i=0; $i<count($_tabs); $i++)
				$result[] = array(
					'expression' => $_tabs[$i] == $current,
					'tag' => $_tabs[$i] . '.show'
				);

		}
		else 
			for ($j=0; $j<count($_tabs); $j++) 
				$result = array_merge($result, self::_gen_meta_tag_rules_for_tabs_aux($_tabs[$j], $_currents, $j));

		return $result;		
		
	}
	
	public static function _render_main_form() {

		$form = new PrisnaBWTAdminForm();

		echo $form->render(array(
			'type' => 'file',
			'content' => '/admin/main_form.tpl',
			'meta_tag_rules' => self::_gen_meta_tag_rules_for_tabs()
		));
	
	}

	protected static function _select_language() {

		load_plugin_textdomain('prisna-bwt', false, dirname(plugin_basename(__FILE__)) . '/../languages');

	}
	
}

class PrisnaBWTAdminBaseForm extends PrisnaBWTItem {
	
	public $title_message;
	public $saved_message;
	public $save_button_message;
	public $reset_message;
	public $reset_button_message;
	public $reseted_message;
	
	protected $_fields;
	
	public function __construct() {
		
		$this->title_message = __('Bing Website Translator', 'prisna-bwt');
		$this->saved_message = __('Settings saved.', 'prisna-bwt');
		$this->reseted_message = __('Settings reseted.', 'prisna-bwt');
		$this->reset_message = __('All the settings will be reseted and restored to their default values. Do you want to continue?', 'prisna-bwt');
		$this->save_button_message = __('Save changes', 'prisna-bwt');
		$this->reset_button_message = __('Reset settings', 'prisna-bwt');

	}
	
	public static function commit($_name, $_result) {
		
		self::_commit($_name, $_result);
		
	}
	
	protected static function _commit($_name, $_result) {

		if (!get_option($_name))
			add_option($_name, $_result);
		else
			update_option($_name, $_result);
		
		if (!get_option($_name)) {
			delete_option($_name);
			add_option($_name, $_result);
		}
		
	}
	
	public function render($_options, $_html_encode=false) {
		
		return parent::render($_options, $_html_encode);
		
	}
	
	protected function _prepare_settings() {}
	protected function _set_fields() {}
	
}

class PrisnaBWTAdminForm extends PrisnaBWTAdminBaseForm {
	
	public $group_0;
	public $group_1;
	public $group_2;
	public $group_3;
	public $group_4;
	public $group_5;
	public $group_6;
	public $group_7;
	public $group_8;
	public $group_9;
	public $group_10;
	public $group_11;
	public $group_12;

	public $nonce;
	
	public $tab;
	public $tab_2;
	
	public $general_message;
	public $advanced_message;
	public $advanced_general_message;
	public $advanced_import_export_message;
	public $premium_message;
	
	public $advanced_import_success_message;
	public $advanced_import_fail_message;
	public $wp_version_check_fail_message;
	
	protected static $_imported_status;
	
	public function __construct() {
		
		parent::__construct();
		
		$this->general_message = __('General', 'prisna-bwt');

		$this->advanced_message = __('Advanced', 'prisna-bwt');
		$this->advanced_general_message = __('General', 'prisna-bwt');
		$this->premium_message = __('Premium', 'prisna-bwt');
		$this->advanced_import_export_message = __('Import / Export', 'prisna-bwt');
		$this->advanced_import_success_message = __('Settings succesfully imported.', 'prisna-bwt');
		$this->advanced_import_fail_message = __('There was a problem while importing the settings. Please make sure the exported string is complete. Changes weren\'t saved.', 'prisna-bwt');
		$this->wp_version_check_fail_message = sprintf(__( 'Bing Website Translator requires WordPress version %s or later.', 'prisna-bwt'), PRISNA_BWT__MINIMUM_WP_VERSION);

		$this->nonce = wp_nonce_field(PrisnaBWTConfig::getAdminHandle(), '_prisna_bwt_nonce');

		$this->_set_fields();

	}
	
	public static function getImportedStatus() {
		
		return self::$_imported_status;
		
	}
	
	protected static function _set_imported_status($_status) {
	
		self::$_imported_status = $_status;
		
	}

	protected static function _import() {
		
		$settings = PrisnaBWTConfig::getDefaults(true);
		$key = $settings['import']['id'];
		
		$value = PrisnaBWTCommon::getVariable($key, 'POST');
		
		if ($value === false || PrisnaBWTValidator::isEmpty($value))
			return null;
		
		$decode = base64_decode($value);
		
		if ($decode === false) {
			self::_set_imported_status(false);
			return false;
		}
		
		$unserialize = @unserialize($decode);

		if (!is_array($unserialize)) {
			self::_set_imported_status(false);
			return false;
		}
		
		$result = array();

		foreach ($settings as $key => $setting) {
			
			if (in_array($key, array('import', 'export')))
				continue;
			
			if (array_key_exists($key, $unserialize))
				$result[$key] = $unserialize[$key];

		}

		if (count($result) == 0) {
			self::_set_imported_status(false);
			return false;
		}

		self::_commit(PrisnaBWTConfig::getDbSettingsName(), $result);		
		self::_set_imported_status(true);
		
		return true;
		
	}
	
	public static function save() {
		
		if (!is_null(self::_import()))
			return;

		$settings = PrisnaBWTConfig::getDefaults();
		$result = array();

		foreach ($settings as $key => $setting) {
			
			$value = PrisnaBWTCommon::getVariable($setting['id'], 'POST');
			
			switch ($key) {
				case 'languages': {
					$value = PrisnaBWTCommon::getVariable(str_replace('languages', 'languages_order', $setting['id']), 'POST');
					
					if ($value !== false) {
						$value = explode(',', $value);
						if ($value !== $setting['value'])
							$result[$key] = array('value' => $value);
						else
							unset($result[$key]);
					}
					else
						unset($result[$key]);
					
					break;
				}
				case 'import':
				case 'export': {
					continue;
					break;
				}
				default: {

					if ($key == 'id' || (PrisnaBWTCommon::endsWith($key, '_class') && $key != 'language_selector_class' && $key != 'translated_to_class'))
						$value = trim(PrisnaBWTCommon::cleanId($value));
					else if ($key == 'translated_to_class')
						$value = trim(PrisnaBWTCommon::cleanId($value, '-', false));

					$unset_template = PrisnaBWTCommon::endsWith($key, '_template') && PrisnaBWTCommon::stripBreakLinesAndTabs($value) == PrisnaBWTCommon::stripBreakLinesAndTabs($setting['value']);

					if (!$unset_template && $value !== false && $value != $setting['value'])
						$result[$key] = array('value' => $value);
					else
						unset($result[$key]);
					break;

				}
			}
		}
		
		if (array_key_exists('display_mode', $result) && $result['display_mode']['value'] == 'tabbed' && !array_key_exists('banner', $result))
			$result['banner'] = array(
				'value' => 'false'
			);
		
		self::_commit(PrisnaBWTConfig::getDbSettingsName(), $result);

	}
	
	public static function reset() {
		
		if (get_option(PrisnaBWTConfig::getDbSettingsName()))
			delete_option(PrisnaBWTConfig::getDbSettingsName());

	}

	public function render($_options, $_html_encode=false) {
		
		$this->_prepare_settings();

		$is_importing = PrisnaBWTAdminEvents::isSavingSettings() && PrisnaBWTValidator::isBool(self::getImportedStatus());

		if (!array_key_exists('meta_tag_rules', $_options))
			$_options['meta_tag_rules'] = array();

		$_options['meta_tag_rules'][] = array(
			'expression' => PrisnaBWTAdminEvents::isSavingSettings() && !$is_importing,
			'tag' => 'just_saved'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => $is_importing && self::getImportedStatus(),
			'tag' => 'just_imported_success'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => $is_importing && !self::getImportedStatus(),
			'tag' => 'just_imported_fail'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => !version_compare($GLOBALS['wp_version'], PRISNA_BWT__MINIMUM_WP_VERSION, '<'),
			'tag' => 'wp_version_check'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => PrisnaBWTAdminEvents::isResetingSettings(),
			'tag' => 'just_reseted'
		);
		
		return parent::render($_options, $_html_encode);

	}

	protected function _set_fields() {
		
		if (is_array($this->_fields))
			return;
			
		$this->_fields = array();
			
		$settings = PrisnaBWTConfig::getSettings(true);
		
		foreach ($settings as $key => $setting) { 
			
			if (!array_key_exists('type', $setting))
				continue;
			
			$field_class = 'PrisnaBWT' . ucfirst($setting['type']) . 'Field';
			
			if ($field_class == 'PrisnaBWTField')
				continue;
			
			$this->_fields[$key] = new $field_class($setting);
		}
		
	}
	
	protected function _prepare_settings() {
		
		$settings = PrisnaBWTConfig::getSettings();
		
		$groups = 4;
		
		for ($i=1; $i<$groups+1; $i++) {

			$partial = array();
			
			foreach ($this->_fields as $key => $field) {
				if ($field->group == $i) {
					$field->satisfyDependence($this->_fields);
					$partial[] = $field->output();
				}
			}

			$group = 'group_' . $i;
					
			$this->{$group} = implode("\n", $partial);
				
		}
		
		$tab = PrisnaBWTCommon::getVariable('prisna_tab', 'POST');
		$this->tab = $tab !== false ? $tab : '';

		$tab_2 = PrisnaBWTCommon::getVariable('prisna_tab_2', 'POST');
		$this->tab_2 = $tab_2 !== false ? $tab_2 : '';

	}

}

class PrisnaBWTAdminEvents {

	public static function isLoadingAdminPage() {
		
		return in_array(PrisnaBWTCommon::getVariable('page', 'GET'), array(PrisnaBWTConfig::getAdminHandle()));
		
	}
	
	public static function isSavingSettings() {
		
		return PrisnaBWTCommon::getVariable('prisna_bwt_admin_action', 'POST') === 'prisna_bwt_save_settings';
		
	}
	
	public static function isResetingSettings() {
		
		return PrisnaBWTCommon::getVariable('prisna_bwt_admin_action', 'POST') === 'prisna_bwt_reset_settings';
		
	}

}

PrisnaBWTAdmin::initialize();

?>