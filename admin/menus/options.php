<?php

    function viewAdminOptions(){
        $options = new CV_OPTIONS();
        ?>

        <div class="wrap"><div class ="admin-view"><div class ="admin-container"><h3>Optionen</h3>
                <form method="POST">
                    <table class="form-table">
                        <tbody>
                            <tr class="cv_verifizierungskennzeichen">
                                <th scope="row">Verifizierungskennzeichen</th>
                                <td>		
                                    <input type="text" id="cv_verifizierungskennzeichen" name="cv_verifizierungskennzeichen" value="<?php echo esc_sql(get_option(CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN)) ?>" class="regular-text"> 
                                    <div class="option_info">Wird im Kopf der Verifizierungsseite gezeigt.</div>
                                </td>
                            </tr>
                            <tr class="cv_verifizierungsstatus">
                                <th scope="row">Verifizierungsstatus</th>
                                <td>		
                                    <input type="text" id="cv_verifizierungsstatus" name="cv_verifizierungsstatus" value="<?php echo esc_sql(get_option(CV_OPTIONS::C_VERIFIZIERUNG_STATUS)) ?>" class="regular-text"> 
                                    <div class="option_info">Wird als Status unterhalb eines negativen Testergebnis auf der Verifizierungsseite gezeigt.</div>
                                </td>
                            </tr>
                            <tr class="cv_max_rows">
                                <th scope="row">Maximale Zeilenanzahl</th>
                                <td>		
                                    <input type="number" min="2" max="100" id="cv_max_rows" name="cv_max_rows" required value="<?php echo esc_sql(get_option(CV_OPTIONS::C_TABLE_MAX_ROWS)) ?>" class="regular-text"> 
                                    <span class="validity"></span>
                                    <div class="option_info">Legt fest, wie viele Zeilen in der Tabelle der Mitarbeiter und der Tabelle Mitarbeiter Tests angezeigt werden sollen.</div>
                                </td>
                            </tr>
                            <tr class="cv_settings_update_time">
                                <th scope="row"></th>
                                <td>
                                <input type="hidden" id="cv_settings_update_time" name="cv_settings_update_time" value="" class="regular-text">
                                </td>
                            </tr>
                            <tr class="cv_qr_support">
                                <th scope="row">Zeige QR Code</th>
                                <td>
                                    <input type="checkbox" name="cv_qr" value="yes"
                                    <?php
                                        if ( "yes" ===  esc_sql(get_option(CV_OPTIONS::C_QR_CODE))){
                                            echo ' checked="checked" >';
                                        }else{
                                            echo ' >';
                                        }
                                    ?>
                            <div class="option_info">Legt fest, ob eine QR Code auf der Verifizierungsseite für einen Kunden angezeigt werden soll.</div>
                                </td>
                            </tr>
                            <tr class="cv_clean_db_by_uninstall">
                                <th scope="row" style="color:red; font-weight:bold;">Lösche Tabellen</th>
                                <td>
                                    <input type="checkbox" id="cv_clean_db_by_uninstall" name="cv_clean_db_by_uninstall" value="yes"
                                    <?php
                                        if ( "yes" === esc_sql(get_option(CV_OPTIONS::C_CLEAN_DB_BY_UNINSTALL))){
                                            echo ' checked="checked" >';
                                        } else{
                                            echo ' >';
                                        }
                                        ?>
                                    <div class="option_info"><u>ACHTUNG!</u>  Legt fest, das bei einem Löschen des Plugin, die Tabellen ebenfalls entfernt werden. Dies kann <u>NICHT</u> rückgäng gemacht werden.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Änderungen speichern"></p>
                </form>
        <?php
        // save options
        if(isset($_POST['submit'])){
            $vk= sanitize_text_field($_POST[CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN]);
            $vs=sanitize_text_field($_POST[CV_OPTIONS::C_VERIFIZIERUNG_STATUS]);
            $qr=sanitize_text_field($_POST[CV_OPTIONS::C_QR_CODE]);
            $mr=sanitize_text_field($_POST[CV_OPTIONS::C_TABLE_MAX_ROWS]);
            $dt=sanitize_text_field($_POST[CV_OPTIONS::C_CLEAN_DB_BY_UNINSTALL]);
            
            $options->updateOrAddOption(CV_OPTIONS::C_CLEAN_DB_BY_UNINSTALL, $dt, '', '');
            $options->updateOrAddOption(CV_OPTIONS::C_TABLE_MAX_ROWS, $mr, '', '');
            $options->updateOrAddOption(CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN, $vk, '', '');
            $options->updateOrAddOption(CV_OPTIONS::C_VERIFIZIERUNG_STATUS, $vs, '', '');
            $options->updateOrAddOption(CV_OPTIONS::C_QR_CODE, $qr, '', '');
            $options->updateOrAddOption(CV_OPTIONS::C_SETTINGS_UPDATE_TIME, new DateTime(), '', '');
    
            wp_redirect( esc_url( add_query_arg()));
        }
        ?>
        </div></div></div>
        <?php 
    }