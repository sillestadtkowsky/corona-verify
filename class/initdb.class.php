<?php

require_once __DIR__ . '/../class/option.class.php';

class CV_INITDB{
    
    Const DB_VERSION = '1.0.0';

    public static function installDB(){
        $intiDbClass = new CV_INITDB();
        $options = new CV_OPTIONS();
        global $jal_db_version;
        $installed_ver = $options->readOption(CV_OPTIONS::C_DB_VERSION);

        if ( $installed_ver === CV_INITDB::DB_VERSION ) {
            echo 'create NO tables ';
        }else{
            echo 'create tables ';
      
            $intiDbClass->createEmployee();

            $intiDbClass->createTestForEmployee();
        
            $intiDbClass->setDefaultOptions();
        }
    }

    private function createEmployee(){
        global $wpdb;
        $sql =  'CREATE TABLE '.$wpdb->prefix .'corona_test_to_employee (
                id mediumint(9) NOT NULL,
                persId int(11) DEFAULT NULL,
                dateTime datetime NOT NULL,
                testResult varchar(10) DEFAULT NULL,
                symptom tinyint(1) DEFAULT NULL,
                dateExpired datetime NOT NULL
            )';
        $wpdb->get_results($sql);
        
        $primary =  'ALTER TABLE '.$wpdb->prefix .'corona_test_to_employee ADD PRIMARY KEY (id);';
        $wpdb->get_results($primary);

        $autoIncrement =  'ALTER TABLE '.$wpdb->prefix .'corona_test_to_employee MODIFY id mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41';
        $wpdb->get_results($autoIncrement);
        
        $wpdb->get_results('commit;');

    }

    private function createTestForEmployee(){
        global $wpdb;
        $sql =  'CREATE TABLE '.$wpdb->prefix .'corona_employee (
                id int(11) NOT NULL,
                persId int(11) DEFAULT NULL,
                firstname varchar(150) DEFAULT NULL,
                lastname varchar(150) DEFAULT NULL
                )';
        $wpdb->get_results($sql);

        $primary =  'ALTER TABLE '.$wpdb->prefix .'corona_employee ADD PRIMARY KEY (id), ADD UNIQUE KEY persID (persId);';
        $wpdb->get_results($primary);

        $autoIncrement =  'ALTER TABLE '.$wpdb->prefix .'corona_employee MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;';
        $wpdb->get_results($autoIncrement);
        
        $wpdb->get_results('commit;');
    }

    private function setDefaultOptions(){
        $options = new CV_OPTIONS();
        $options->updateOrAddOption(CV_OPTIONS::C_DB_VERSION, CV_INITDB::DB_VERSION,'','no');
        $options->updateOrAddOption(CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN, '3G','','no' );
        $options->updateOrAddOption(CV_OPTIONS::C_VERIFIZIERUNG_STATUS, '3-G','','no' );
        $options->updateOrAddOption(CV_OPTIONS::C_QR_CODE, '','','no' );
        $options->updateOrAddOption(CV_OPTIONS::C_TABLE_MAX_ROWS, 8,'','no' );
    }
}