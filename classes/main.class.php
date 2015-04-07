<?php

class PrisnaBWT {

	public static function initialize() {

		add_shortcode(PrisnaBWTConfig::getWidgetName(true), array('PrisnaBWT', '_create_shortcode'));
		add_action('wp_footer', array('PrisnaBWT', '_auto_initialize'));

	}

	public static function _auto_initialize() {

		if (!self::isAvailable())
			return;

		$display_mode = PrisnaBWTConfig::getSettingValue('display_mode');
		
		if ($display_mode != 'tabbed')
			return;

		echo do_shortcode('[' . PrisnaBWTConfig::getWidgetName(true) . ']');
		
	}
	
	public static function _create_shortcode() {

		if (!self::isAvailable())
			return;

		$settings = PrisnaBWTConfig::getSettingsValues(false, false);
		
		$translator = new PrisnaBWTOutput((object) $settings);

		return $translator->render(array(
			'type' => 'file',
			'content' => '/main.tpl'
		));
		
	}
	
	public static function isAvailable() {

		if (is_admin())
			return false;

		if (PrisnaBWTConfig::getSettingValue('test_mode') == 'true' && !current_user_can('administrator'))
			return false;

		global $post;
		
		if (!is_object($post))
			return true;
		
		$settings = PrisnaBWTConfig::getSettingsValues();
		
		if ($post->post_type == 'page' && array_key_exists('exclude_pages', $settings)) {
		
			$pages = $settings['exclude_pages']['value'];
		
			if (in_array($post->ID, $pages))
				return false;
		
		}

		if ($post->post_type == 'post' && array_key_exists('exclude_posts', $settings)) {
		
			$posts = $settings['exclude_posts']['value'];
		
			if (in_array($post->ID, $posts))
				return false;
		
		}
		
		if ($post->post_type == 'post' && array_key_exists('exclude_categories', $settings)) {
		
			$categories = $settings['exclude_categories']['value'];
		
			$post_categories = wp_get_post_categories($post->ID);

			if (PrisnaBWTCommon::inArray($categories, $post_categories))
				return false;
		
		}
		
		return true;
		
	}
	
}

class PrisnaBWTOutput extends PrisnaBWTItem {
	
	protected static $_rendered;

	public $custom_css;
	public $flags_formatted;
	public $options_formatted;
	
	protected static $_exclude_rules;
	
	public function __construct($_properties) {

		$this->_properties = $_properties;
		$this->_set_properties();
		$this->_gen_options();
		self::_set_rendered(false);

	}
	
	public function setProperty($_property, $_value) {

		return $this->{$_property} = $_value['value'];

	}
	
	protected static function _set_rendered($_state) {
		
		if (self::_get_rendered() === true)
			return;
		
		self::$_rendered = $_state;
		
	}
	
	protected static function _get_rendered() {
		
		return self::$_rendered;
		
	}
	
	public function _prepare_option_value($_id, $_value) {
		
		$value = $_value;
				
		if (PrisnaBWTValidator::isBool($value))
			$value = $value == 'true' || $value === true;

		return PrisnaBWTFastJSON::encode($value);
		
	}
	
	public function render($_options, $_html_encode=false) {
		
		if (self::_get_rendered())
			return '';
		
		if (!array_key_exists('meta_tag_rules', $_options))
			$_options['meta_tag_rules'] = array();

		$_options['meta_tag_rules'][] = array(
			'expression' => $this->_has_flags(),
			'tag' => 'has_flags'
		);

		self::_set_rendered(true);

		return parent::render($_options, $_html_encode);
		
	}
	
	protected function _has_flags() {

		$show_flags = PrisnaBWTConfig::getSettingValue('show_flags');
		
		if (!$show_flags)
			return false;
	
		$languages = PrisnaBWTConfig::getSettingValue('languages');
		
		if (empty($languages))
			return false;
			
		return true;
		
	}
	
	protected function _gen_flags() {

		if (!$this->_has_flags())
			return;
			
		$flags_container_template = PrisnaBWTConfig::getSettingValue('flags_container_template');
		$flag_template = PrisnaBWTConfig::getSettingValue('flag_template');

		$languages = PrisnaBWTConfig::getSettingValue('languages');

		$flags_items = array();
		
		foreach ($languages as $language)
			$flags_items[] = array(
				'language_code' => $language,
				'language_name' => PrisnaBWTCommon::getLanguage($language),
				'language_name_no_space' => PrisnaBWTCommon::getLanguage($language, '_'),
				'flags_path' => PRISNA_BWT__IMAGES . '/'
			);
		
		$flags = PrisnaBWTCommon::renderObject($flags_items, array(
			'type' => 'html',
			'content' => $flag_template
		));

		$result = array(
			'content' => $flags,
			'align_mode' => $this->align_mode
		);
		
		$this->flags_formatted = PrisnaBWTCommon::renderObject((object) $result, array(
			'type' => 'html',
			'content' => $flags_container_template
		));
		
	}
	
	protected function _gen_options() {

		$this->style = ucfirst($this->style);
		$this->when = ucfirst($this->when);

		$this->_gen_flags();

	}
	
}

PrisnaBWT::initialize();

?>
