jQuery(document).ready(function()   {
    // jQuery(document).ajaxStop(function () {
        // sidebarHeightAdjustToContent();
    // });


    /*  Minify header on scroll
    -------------------------------------*/
    var hintAlreadyRead="false";
    var didScroll = "no";
    var count = 0;

    jQuery("header.site-header").css({ "position": "fixed"});
    setContainerTopMargin( true );

    jQuery(window).scroll(function(event){
        // console.log( "Init Done in scroll function = " + window.initDone );
        if (didScroll == "no" && count > 4) {
            didScroll = "yes";
            // console.log( "SCROLLED !");
        }
    });

    setInterval(function() {
        if (didScroll == "yes" ) {
            // ****** Trigger the resize Header function !
            actionsOnScroll();
            // *******************************************
            didScroll = "last";
        }
        else if (didScroll == "last") {
            // Allows for the css transition to be completed before computing the margin again
            setContainerTopMargin();
            didScroll = "stop";
        }
        else if (didScroll == "no" && count <= 4) {
            count++;
            // console.log( "count = " + count);
        }
    }, 250);

});


/* -----------------------------------------
    Sidebar height adjust to content
------------------------------------------*/
function sidebarHeightAdjustToContent() {

    // console.log( "In sidebarHeightAdjustToContent" );

    // Variables for Sidebar height adjust to content (shac)
    var shacContentClass = ".site-inner .content";
    var shacSidebarClass = ".sidebar.widget-area";
    var shacElementToRemove = "section";
    var shacExtraHeight = 300;

    var contentHeight = jQuery(shacContentClass).outerHeight();
    var sidebarHeight = jQuery(shacSidebarClass).outerHeight();
    // console.log( "Content height", contentHeight );
    // console.log( "Sidebar height", sidebarHeight );
    // console.log( "Allowed max height", contentHeight + shacExtraHeight );

    var searchAutoHideWidgets = shacSidebarClass + ' ' + shacElementToRemove + '.autohide';

    // console.log('Widgets with autohide class', jQuery(searchAutoHideWidgets));

    var element = jQuery(searchAutoHideWidgets).last();
    var prevElement;

    while ( (sidebarHeight > contentHeight + shacExtraHeight) && element.length>0/*&& (count < shacMaxWidgetRemove )*/ ) {

        // console.log( "In adjust sidebar height loop" );
        // console.log( "current element is : ", element );

        prevElement = element.prev();
        if ( element.hasClass('autohide') ) {
            // console.log('Element has class autohide');
            element.remove();
            sidebarHeight = jQuery(shacSidebarClass).outerHeight();
            // console.log("Removed one widget, new sidebar height is ", sidebarHeight );
        }
        else {
            // console.log('Element does not have class autohide');
        }
        element = prevElement;
        // console.log( 'Setting current element to : ', element);
    };

    // console.log( "Out of while loop" );
}


function actionsOnScroll() {
    // Resize header
    jQuery('header').addClass('nav-up');
    // Hide hint
    jQuery('.mobile-menu-hint-container').addClass('transparent');
}


function setContainerTopMargin( scrollTop ) {
    var height = jQuery( "header.site-header" ).outerHeight();
    var htmlMargin = parseInt(jQuery("html").css("margin-top"));
    topMargin = height - htmlMargin + 10;
    // console.log( "height = " + height )
    // console.log( "htmlMargin = " + htmlMargin )
    // console.log( "topMargin = " + topMargin )
    if ( scrollTop ) {
        jQuery(window).scrollTop(0);
        // console.log( "Scroll Position = " + jQuery(window).scrollTop() );
    }
    jQuery(".site-container").css("margin-top", topMargin);

}
