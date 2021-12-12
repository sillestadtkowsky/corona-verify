<?php

class CV_INITDB{
    
    Const DB_VERSION = '1.0.0';

    /*
    * install plugin
    */

    public static function installDB(){
        $intiDbClass = new CV_INITDB();
        $options = new CV_OPTIONS();

        $installed_ver = $options->readOption(CV_OPTIONS::C_DB_VERSION);

        if ( $installed_ver === CV_INITDB::DB_VERSION ) {
        }else{
            $intiDbClass->createEmployee();
            $intiDbClass->createTestForEmployee();
            $intiDbClass->setDefaultOptions();
        }
    }
    
    private function createTestForEmployee(){
        global $wpdb;
        $sql =  'CREATE TABLE '.$wpdb->prefix .'corona_test_to_employee (
                id mediumint(9) NOT NULL,
                persId int(11) DEFAULT NULL,
                dateTime datetime NOT NULL,
                testResult varchar(10) DEFAULT NULL,
                symptom tinyint(1) DEFAULT NULL,
                dateExpired datetime NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1';
        $wpdb->get_results($sql);
        
        $primary =  'ALTER TABLE '.$wpdb->prefix .'corona_test_to_employee ADD PRIMARY KEY (id);';
        $wpdb->get_results($primary);

        $autoIncrement =  'ALTER TABLE '.$wpdb->prefix .'corona_test_to_employee MODIFY id mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41';
        $wpdb->get_results($autoIncrement);
        
        $wpdb->get_results('commit;');

    }

    private function createEmployee(){
        global $wpdb;
        $sql =  'CREATE TABLE '.$wpdb->prefix .'corona_employee (
                id int(11) NOT NULL,
                persId int(11) DEFAULT NULL,
                firstname varchar(150) DEFAULT NULL,
                lastname varchar(150) DEFAULT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=latin1';
        $wpdb->get_results($sql);

        $primary =  'ALTER TABLE '.$wpdb->prefix .'corona_employee ADD PRIMARY KEY (id), ADD UNIQUE KEY persID (persId);';
        $wpdb->get_results($primary);

        $autoIncrement =  'ALTER TABLE '.$wpdb->prefix .'corona_employee MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;';
        $wpdb->get_results($autoIncrement);
        
        $wpdb->get_results('commit;');
    }

    private function setDefaultOptions(){
        $options = new CV_OPTIONS();
        $options->updateOrAddOption(CV_OPTIONS::C_DB_VERSION, esc_html(CV_INITDB::DB_VERSION),'','no');
        $options->updateOrAddOption(CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN, esc_html('3G'),'','no' );
        $options->updateOrAddOption(CV_OPTIONS::C_VERIFIZIERUNG_STATUS, esc_html('3-G'),'','no' );
        $options->updateOrAddOption(CV_OPTIONS::C_QR_CODE, '','','no' );
        $options->updateOrAddOption(CV_OPTIONS::C_CLEAN_DB_BY_UNINSTALL, '','','no' );
        $options->updateOrAddOption(CV_OPTIONS::C_TABLE_MAX_ROWS, esc_html(8),'','no' );
    }
    /*
    * uninstall plugin
    */
    public static function deInstallDB(){
        $options = new CV_OPTIONS();
        echo 'deInstallDB' . $options->readOption(CV_OPTIONS::C_CLEAN_DB_BY_UNINSTALL);
        if ($options->readOption(CV_OPTIONS::C_CLEAN_DB_BY_UNINSTALL)==='yes') {
            $intiDbClass = new CV_INITDB();     
            $intiDbClass->deleteEmployee();
            $intiDbClass->deleteTestForEmployee();
            $intiDbClass->deleteDefaultOptions();
        }
    }

    private function deleteEmployee(){
        global $wpdb;
        $sql =  'DROP TABLE '.$wpdb->prefix .'corona_employee;';
        $wpdb->get_results($sql);        
        $wpdb->get_results('commit;');

    }
    
    private function deleteTestForEmployee(){
        global $wpdb;
        $sql =  'DROP TABLE '.$wpdb->prefix .'corona_test_to_employee;';
        $wpdb->get_results($sql);        
        $wpdb->get_results('commit;');

    }

    private function deleteDefaultOptions(){
        global $wpdb;
        $sql =  'DELETE FROM '.$wpdb->prefix .'options WHERE option_name like "cv_%";';
        $wpdb->get_results($sql);        
        $wpdb->get_results('commit;');

    }
}