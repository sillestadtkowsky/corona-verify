<?php

class CV_OPTIONS

{
	const C_SETTINGS_UPDATE_TIME= 'cv_settings_update_time';
	const C_CLEAN_DB_BY_UNINSTALL= 'cv_clean_db_by_uninstall';
	const C_DB_VERSION= 'cv_db_version';
	const C_VERIFIZIERUNG_KENNZEICHEN = 'cv_verifizierungskennzeichen';
	const C_VERIFIZIERUNG_STATUS = 'cv_verifizierungsstatus';
	const C_QR_CODE = 'cv_qr';
	const C_TABLE_MAX_ROWS = 'cv_max_rows';

	public function addOption($option,$value,$deprecated,$autoload){
		add_option( $option, sanitize_text_field($value), $deprecated, $autoload);
	}

	public function readOption($option){
		return sanitize_text_field(get_option( $option, true ));
	}

	public function updateOption($option,$value){
		update_option($option, sanitize_text_field($value));
	}

	public function updateOrAddOption($option,$value,$deprecated,$autoload){
		if(get_option($option)===false){
			CV_OPTIONS::addOption ($option,sanitize_text_field($value),$deprecated,$autoload);
		}else{
			update_option($option, sanitize_text_field($value));
		}
	}
}
