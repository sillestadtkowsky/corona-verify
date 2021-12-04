<?php

    require_once __DIR__ . '/db.class.php';
    require_once __DIR__ . '/option.class.php';

    class CV_EXCEL {

        static function downloadTestVerifizierungen($testIds, $link = NULL)
        {
          ob_clean();
          ob_start();
          $link = 'daten.csv' ;
          
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
          
          $results = CV_DB::getTestsForEmployeesByIdArray($testIds);

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
          header('Content-Disposition: attachment; filename="Corona-Verify-Test.csv"');
          
          readfile($link);
          unlink($link);
        }
    }
