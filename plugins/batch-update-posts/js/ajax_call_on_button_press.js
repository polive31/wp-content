/* <![CDATA[ */

jQuery(document).ready(function(){
	
  jQuery('input[type="submit"]').click(function(){
  	var ScriptName = jQuery(this).data('name');
  	var ScriptInst = jQuery(this).data('instance');
  	
		//console.log('Lancement du script = '  + 'script' + ScriptName + ScriptInst);
		
  	// Récupération des données de WPLocalize propres au shortcode sélectionné
		var WPLocalizeVar = window['script' + ScriptName + ScriptInst];
		var ShortcodeArgs = jQuery.parseJSON( WPLocalizeVar.data );
		var AjaxURL = WPLocalizeVar.url;
		
		//console.log('Arguments du shortcode = %0',ShortcodeArgs);
		//console.log('URL de la page = '  + AjaxURL);
		
		jQuery('div#resp' + ScriptName + ScriptInst).html( 'Ajax call launched...' );

		jQuery.post(
		 AjaxURL,
		 {
		 	action : ScriptName,
			args : ShortcodeArgs,
			dataType: "text",
		 },
		function( response ) {
		 		//console.log( 'Ajax call successfull');
		 		//console.log('div#resp' + ScriptName + ScriptInst);
		 		//console.log( response );
		 		jQuery('div#resp' + ScriptName + ScriptInst).html( response );
		});
		
  });
});

/* ]]> */	