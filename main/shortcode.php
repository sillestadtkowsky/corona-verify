<?php
/* 
* ShortCode [corona-verify-form]
* @param qr (1 || 0) >> display QR Code or not 
*/
function corona_verify_shortcode($atts, $content = null, $tag = '')
{
  global $wpdb;
  $html = '';
  $options = new CV_OPTIONS();
  $style = 'green';
  
  $a = shortcode_atts(array(
    'style' => 'green',
  ), $atts);

  if (null != $a) {
    $style = $a['style'];
  }

  $ident = $_GET['ident'] ?? 'null';
  
  if ($ident === 'null' || null == $ident) {
    $personId = get_current_user_id();
    $showQR = true;
  } else {
    $showQR = false;
    $lesbar = CV_SECURE::decrypt($ident, "Wissen=M8");
    $paramPersId = explode("&", $lesbar)[0];
    $personId = explode("=", $paramPersId)[1];
    $paramTestId = explode("&", $lesbar)[1];
    $testId = explode("=", $paramTestId)[1];
  }

  // call DB Data
  $result = CV_DB::getLastTestForEmployee($personId);

  $DEBUGMESSAGE = 'personId: ' . $personId;
  CV_UTILS::debugCode($DEBUGMESSAGE);

  if($style==='green'){
    $html .= '<div class="corona-verify-form">
                <div class="corna-verify-heading"><h1>' .$options->readOption(CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN). ' Verifizierung</h1>';
                if (empty($result) != '1'){
                  $test_ergebnis = $result[0];
                  if (CV_UTILS::isGueltig($test_ergebnis->expired) == 1) {
                    $html .= '<div class="corna-verify-container-item">
                                <div class="paragraf">
                                  <p> Wir sind nach § 28 Infektionsschutzgesetz verpflichtet, den 3G-Status jedes unserer Mitarbeiter festzustellen und das Ergebnis zu dokumentieren. 
                                      Einen gültigen 3G-Status hat derjenige, der entweder geimpft, genesen oder getestet ist. 
                                      Wir versichern, dass jede Person, für die hier ein gültiger Status angezeigt wird, eine der vorgenannten Bedingungen erfüllt.
                                  </p>
                                </div>
                                <div class="corna-verify-container">
                                <label>Name</label>
                                <div class="lastname"><b>' . $test_ergebnis->firstname . ' ' . $test_ergebnis->lastname . '</b></div>
                                <div class="testresult">';
                                  if ($test_ergebnis->testresult === 'positiv') {
                                    $html .= '<div class="positiv">';
                                    $html .= '<b>Unser Mitarbeiter hat <u>KEINEN</u> gültigen ' .$options->readOption(CV_OPTIONS::C_VERIFIZIERUNG_STATUS). ' Status</b>';
                                  } else {
                                    $html .= '<div class="negativ">';
                                    $html .= '<div class="greenBackground"><div class="aktuellesDatum">' . $DateAndTime = date('d.m.Y H:i', time()) . ' Uhr</div> <b>' .$options->readOption(CV_OPTIONS::C_VERIFIZIERUNG_STATUS). ' Status gültig</b></div>';
                                      if ($options->readOption(CV_OPTIONS::C_QR_CODE)==='yes') {
                                        if($showQR){
                                          $personId = get_query_var('persId', -1);
                                          $testId = get_query_var('testId', -1);
                                          if ($personId == -1 || $testId == -1) {
                                            $personId = get_current_user_id();
                                            $testId = $test_ergebnis->persId;
                                          }
                                            $html .= '<div class="qr">';
                                            $html .= CV_QR::getCode($test_ergebnis->persId, $testId);
                                            $html .= '</div>';
                                        }
                                      }
                                  }
                    } else {
                      $html .= '<div class="expired">
                                <b>Dieser Link ist nicht mehr gültig</b>';
                    }
                  } else {
                    $html .= '<div class="expired">';
                    $html .= '<b>Dieser Link ist nicht mehr gültig</b>';
                  }
                  $html .= '</div></div><div></div></div>';
  }
  
  echo $html;
}
add_shortcode('corona-verify-form', 'corona_verify_shortcode');