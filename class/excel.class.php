<?php
    
    class CV_EXCEL {

        static function downloadTestVerifizierungen($testIds, $link = NULL)
        {
          ob_clean();
          ob_start();
          
          $fp = fopen($link,'w');
          $first_row = array(
              'Personen ID',
              'Vorname',
              'Nachname',
              'Testergebnis',
              'Test Datum',
              'Test Zeit',
              'Symptome',
              'Gültig bis Tag',
              'Gültig bis Zeit'
          );
          
          fputcsv($fp, $first_row);
          
          $results = CV_DB::getTestsForEmployeesByIdArray(CV_UTILS::recursive_sanitize_text_field($testIds));

          foreach($results as $key=>$value)
          {
              $array_add = array(
                  $value['persId'], $value['firstname'],
                  $value['lastname'],$value['testresult'],$value['datum'],$value['zeit'],
                  $value['symptom'],$value['expiredDate'],$value['expiredTime']
              );
          
              fputcsv($fp, $array_add);
          
          };
          
          fclose($fp);
          
          header('Content-Type: application/csv');
          header('Content-Disposition: attachment; filename="' . $link . '.csv"');
          
          readfile($link);
          unlink($link);
        }
    }
