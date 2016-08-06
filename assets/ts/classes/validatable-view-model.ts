/**
 * An extensible View Model with build-in validation support.
 */
class ValidatableViewModel
{
    rules: any;

    busy = ko.observable(false);

    formTrigger: string;

    clearValidation() {
        _.each( this.rules, (rules, key) => {
            this[key+'_validation'].removeAll();
        });
    }

    getData() {
        let data = {};
        _.each( this.rules, (rules, key) => {
            data[key] = this[key]();
        });
        return data;
    }

    validate() {
        let data = {},
            errors;

        // Loop through the validation rule keys, get current observable values.
        _.each( this.rules, (rules, key) => {
            data[key] = this[key]();
        });

        // Clear validation rules.
        this.clearValidation();

        // Validate.
        errors = validate(data, this.rules);

        // If there are errors, push them to their respective observable array.
        if ( errors ) {
            _.each(errors, (errors, key) => {
                _.each(errors, (error) => {
                    this[key+'_validation'].push(error);
                });
            });
            return false;
        }

        // All good.
        return true;
    }

    initialize() {

        // Loop through the view model's rules.
        _.each( this.rules, (rules, key) =>
        {
            // Create an observable for this key which holds the input/form value.
            this[key] = ko.observable('');

            // Create an observable array, which holds the validation rules.
            this[key + '_validation'] = ko.observableArray([]);
        });

        // If a form trigger element is specified, bind a function to the viewModel
        // "keydown", so we can automatically trigger forms when the user hits enter.
        if ( this.formTrigger ) {
            this.keydown = (self, event) => {
                if ( 13 == event.keyCode ) {
                    jQuery(this.formTrigger).trigger('click');
                    return false;
                }
                return true;
            };
        }
    }
}