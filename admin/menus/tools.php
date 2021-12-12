<?php
function viewAdminTools(){
    $html = '<div class="wrap"><h3>Nutzung</h3></div>
    <table class="form-table">
        <tbody>
        <tr class="cv_shortcode">
            <th scope="row">shortCode</th>
            <td>		
                <code>[corona-verify-form]</code>
                <div class="option_info">Erstellen Sie seine Seite und tragen Sie dort den oberhalb angezeigten Shortcode ein.</br>Ab sofort können Sie diese Seite aufrufen und bekommen die Verifizierungsseite angezeigt.</div>
            </td>
        </tr>
        </tbody>
    </table>';

    return $html;
}