//jQuery(document).ready(function() {
//	console.log('Custom shopping list loaded');
//});


jQuery(document).ready(function() {

    jQuery(document).on('click', '.wpurp-recipe-add-to-shopping-list', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var button = jQuery(this);
				//console.log('ADD TO SHOPPING LIST CLICK !!!!');

        if(!button.hasClass('in-shopping-list')) {
            // Activate shopping list button
            button.addClass('in-shopping-list');
            
            var recipeId = button.data('recipe-id');
						//console.log('Recipe ID :'+recipeId);
						
            var recipe = button.parents('.wpurp-container');
            var servings = 0;
            // Check if there is a servings changer (both free and Premium)
            var servings_input = recipe.find('input.adjust-recipe-servings');
            
            if(servings_input.length == 0) {
                servings_input = recipe.find('input.advanced-adjust-recipe-servings');
            }

            // Take servings from serving changer if available
            if(servings_input.length != 0) {
                servings = parseInt(servings_input.val());
            }

            var data = {
                action: 'add_to_shopping_list',
                security: wpurp_add_to_shopping_list.nonce,
                recipe_id: recipeId,
                servings_wanted: servings
            };
            
						//console.log( 'Data : ' );
						//console.log( data );

            jQuery.post(wpurp_add_to_shopping_list.ajaxurl, data, function(html) {

								//console.log('Class added !!!!');
                
                if(button.next().hasClass('recipe-tooltip-content')) {
                    var tooltip = button.next().find('.tooltip-shown').first();
                    var tooltip_alt = button.next().find('.tooltip-alt').first();

                    var tooltip_text = tooltip.html();
                    var tooltip_alt_text = tooltip_alt.html();

                    tooltip.html(tooltip_alt_text);
                    tooltip_alt.html(tooltip_text);
                }
            });
        }
    });
});
