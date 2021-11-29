<?php
class CV_OPTIONS
{
	const C_VERIFIZIERUNG_KENNZEICHEN = 'cv_verifizierungskennzeichen';
	const C_VERIFIZIERUNG_STATUS = 'cv_verifizierungsstatus';
	const C_QR_CODE = 'cv_qr';
	const C_TABLE_MAX_ROWS = 'cv_max_rows';

	public function addOption($option,$value,$deprecated,$autoload){
		add_option( $option, $value, $deprecated, $autoload);
	}

	public function readOption($option){
		return get_option( $option, true );
	}

	public function updateOption($option,$value){
		update_option( $option, $value);
	}

	public function updateOrAddOption($option,$value,$deprecated,$autoload){
		if(get_option( $option)===false){
			CV_OPTIONS::addOption ($option,$value,$deprecated,$autoload);
		}else{
			update_option( $option, $value);
		}
		
	}
}