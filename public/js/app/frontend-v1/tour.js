require(['jquery','bootstrapTour', 'bootstrapTransition', 'bootstrapTooltip', 'bootstrapPopover'], function($, Tour) {

    var steps = [];

    for (var i=1;i<=15;i++) {
        var name = ".js-welcome-tour-step"+i;


        if ( $(name).length==0 ) {
            break;
        }

        steps[steps.length] = {
            element: name,
            title: $(name).data('tourTitle'),
            content: $(name).data('tourDescription'),
            placement: "auto",
            backdrop: true
        };
    }

    // console.log(steps);

    var tour = new Tour({
        debug: true,
        steps: steps,
        onEnd: function () {
            
        }
    });

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();

});