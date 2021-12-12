jQuery(document).ready(function(){
    console.log('jquery-load');   
    jQuery("#cv_clean_db_by_uninstall").change(function() {
        if(this.checked) {
            alert('Achtung! Durch die Auswahl dieses Schalters, werden beim löschen des Plugins die Tabellen mit den Daten gelöscht!');
        }
    });
}); 
