class IOProgressBar
{
    static total = 0;

    static iteration = 0;

    static logOutput(message) {
        return jQuery('#io-output').append(`<li>${message}</li>`);
    }
    static bar() {
        return jQuery('div#io-progress');
    }

    static message(message) {
        return jQuery('#io-progress-message').html(`<b>${message}</b>`);
    }

    static updateCounter() {
        let total = IOProgressBar.total,
            i = IOProgressBar.iteration;

        jQuery('#io-progress-counter').html(`${i} of ${total}`);
    }

    static updateWidth()
    {
        let i = IOProgressBar.iteration,
            total = IOProgressBar.total,
            percentage = (i/total)*100;

        // If total is zero, override.
        if ( 0 == total ) {
            percentage = 0;
        }

        IOProgressBar.bar().css( 'width', percentage + '%' );
        IOProgressBar.updateCounter();
    }

    static bump() {
        IOProgressBar.iteration++;
        IOProgressBar.updateWidth();
    }

    static reset(newTotal=0)
    {
        IOProgressBar.total = newTotal;
        IOProgressBar.iteration = 0;
        IOProgressBar.updateWidth();
    }
}
