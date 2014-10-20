
<style type="text/css">
<!--
.prisna-bwt-align-left {
	text-align: left !important;
}
.prisna-bwt-align-right {
	text-align: right !important;
}
div#MicrosoftTranslatorWidget {
	display: block !important;
}
#WidgetLauncher div#LauncherTranslatePhrase {
	height: auto !important;
}
.prisna-bwt-align-left #WidgetLauncher {
	margin: 5px auto 5px 0 !important;
}
.prisna-bwt-align-right #WidgetLauncher {
	margin: 5px 0 5px auto !important;
}
{{ has_flags.true:begin }}
#prisna-bwt-flags-container {
	list-style: none !important;
	margin: 0 !important;
	padding: 0 !important;
	border: none !important;
	clear: both !important;
}
.prisna-bwt-display {
	display: block !important;
}
.prisna-bwt-flag-container {
	list-style: none !important;
	display: inline-block;
	margin: 0 2px 0 0 !important;
	padding: 0 !important;
	border: none !important;
}
.prisna-bwt-flag-container a,
.prisna-bwt-flag-container a img {
	display: inline-block;
	margin: 0 !important;
	padding: 0 !important;
	border: none !important;
}
{{ has_flags.true:end }}
{{ custom_css }}
-->
</style>
<script type="text/javascript">
/*<![CDATA[*/
var PrisnaBWT = {

	initialize: function() {
	
		setTimeout(function() {
			var s = document.createElement('script');
			s.type = 'text/javascript';
			s.charset = 'UTF-8';
			s.src = (location && location.href && location.href.indexOf('https') == 0 ? 'https://ssl.microsofttranslator.com' : 'http://www.microsofttranslator.com') + '/ajax/v3/WidgetV3.ashx?siteData=ueOIGRSKkd965FeEGM5JtQ**&ctf=False&ui=true&settings={{ when }}&from={{ from }}';
			var p = document.getElementsByTagName('head')[0] || document.documentElement;p.insertBefore(s,p.firstChild); 
		}, 0);
	
		{{ has_flags.true:begin }}
		this._show_flags();
		{{ has_flags.true:end }}
	
	}{{ has_flags.true:begin }},
	
	_fire_event: function(_element, _event) {
		
		try {
			if (document.createEvent) {
				var ev = document.createEvent("HTMLEvents");
				ev.initEvent(_event, true, true);
				_element.dispatchEvent(ev);
			} 
			else {
				var ev = document.createEventObject();
				_element.fireEvent("on" + _event, ev);
			}
		} 
		catch (e) {
			console.log("Prisna BWT: Browser not supported!");
		}
		
	},

	_show_flags: function() {
	
		var button = document.getElementById("TranslateSpan");
		if (!button)
			setTimeout(function() {
				PrisnaBWT._show_flags();
			}, 200);
		else {
			var flags_container = document.getElementById("prisna-bwt-flags-container");
			flags_container.className += " prisna-bwt-display";
		}
	
	},
	
	_enable_widget: function(_language) {
	
		var widget = document.getElementById("LauncherTranslatePhrase");
		var button = document.getElementById("TranslateSpan");
		
		if (widget.className == "WidgetEnabled")
			this._fire_event(button, "click");
			
	},
	
	translate: function(_language) {
	
		this._enable_widget();
		
		setTimeout(function() {
			LanguageMenu.onclick(_language);
		}, 200);
		
	}{{ has_flags.true:end }}
	
};
/*]]>*/
</script>
{{ flags_formatted }}
<div id="MicrosoftTranslatorWidget" class="prisna-bwt-align-{{ align_mode }} {{ style }}" style="color: white; background-color: #555555;"></div>
<script type="text/javascript">
/*<![CDATA[*/
PrisnaBWT.initialize();
/*]]>*/
</script>
