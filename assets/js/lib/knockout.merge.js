(function (factory) {
    // Module systems magic dance.

    if (typeof require === "function" && typeof exports === "object" && typeof module === "object") {
        // CommonJS or Node: hard-coded dependency on "knockout"
        factory(require("knockout"), exports);
    } else if (typeof define === "function" && define["amd"]) {
        // AMD anonymous module with hard-coded dependency on "knockout"
        define(["knockout", "exports"], factory);
    } else {
        // <script> tag: use the global `ko` object, attaching a `mapping` property
        factory(ko, ko.merge = {});
    }
}
(function (ko, exports) {

    var knockoutElementMapping = function(knockoutElement, dataElement, options)
    {
        if(typeof(knockoutElement.mergeConstructor) == "undefined") {
            if (ko.isWriteableObservable(knockoutElement)) {
                if(knockoutElement.mergeMethod) {
                    knockoutElement.mergeMethod(knockoutElement, dataElement, options);
                } else if(knockoutElement.mergeRule) {
                    var mergeMethod = getMethodForMergeRule(knockoutElement.mergeRule);
                    if(mergeMethod) {
                        mergeMethod(knockoutElement, dataElement, options);
                    }
                } else if(isObservableArray(knockoutElement) && isArray(dataElement)) {

                    // If we have an observable array and a data element which is an array
                    // then we need to merge the values item by item
                    for(var i = 0; i < dataElement.length; i++) {

                        // We don't yet have an item in the array so we need to
                        // create one. Use a placeholder and the standard merge
                        // logic to do this
                        if(i >= knockoutElement().length) {
                            if(isPrimitive(dataElement[i])) {
                                if(options.mergeMissingAsObservables === true) {
                                    knockoutElement.push(ko.observable(dataElement[i]));
                                }
                                else {
                                    knockoutElement.push(dataElement[i]);
                                }
                            } else {
                                var placeholder = {};
                                exports.fromJS(placeholder, dataElement[i]);
                                knockoutElement.push(placeholder);
                            }
                        } else if (isPrimitive(knockoutElement()[i]) && isPrimitive(dataElement[i])) {
                            // Handle primitive array merging by simply splicing the value into the array
                            knockoutElement.splice(i, 1, dataElement[i]);
                        } else {
                            exports.fromJS(knockoutElement()[i], dataElement[i], options);
                        }
                    }
                } else {
                    knockoutElement(dataElement);
                }
            }
        } else {
            if(knockoutElement.replaceOnMerge) {
                knockoutElement.removeAll();
            }

            dataElement.forEach(function(element) {
                var arrayElement = new knockoutElement.mergeConstructor();
                exports.fromJS(arrayElement, element, options);
                knockoutElement.push(arrayElement);
            });
        }
    };

    var isObservableArray = function(koElement) {
        // Determine if the knockout element is an observable array - based upon
        // impelemntation suggested at https://github.com/knockout/knockout/issues/619
        return ko.isObservable(koElement) && !(koElement.destroyAll === undefined);
    };

    var isArray = function(dataElement) {
        // Determine if the given data item is an array
        return Object.prototype.toString.call(dataElement) === '[object Array]';
    };

    // Determine if the given item is a primitive type or not
    var isPrimitive = function (element) {

        // Technically these are primitives and we may want to overwrite with these values
        if (element === null) return true;
        if (element === undefined) return true;

        // Date is a bit special, in that typeof reports an object
        if (element instanceof Date) return true;

        // Handle the regular primitives
        switch (typeof element) {
            case "string":
            case "number":
            case "boolean":
            case "symbol":
                return true;

            default:
                return false;
        }
    };

    var getMethodForMergeRule = function(mergeRule) {
        for(var property in exports.rules)
        {
            if(property.toLowerCase() == mergeRule.toLowerCase())
            { return exports.rules[property]; }
        }
    };

    var passToGlobalHandlers = function(knockoutElement, dataElement)
    {
        if(exports.globalHandlers.length == 0) { return false; }

        for(var i=0;i<exports.globalHandlers.length;i++)
        {
            if(exports.globalHandlers[i](knockoutElement, dataElement)) {
                return true;
            }
        }

        return false;
    };

    exports.fromJS = function (koModel, data, options) {
        options = options || {};
        var isEmptyObject = (Object.keys(koModel).length == 0);

        if(!isEmptyObject && isObservableArray(koModel)){
            knockoutElementMapping(koModel, data, options);
            return;
        }

        for (var parameter in data)
        {
            if(passToGlobalHandlers(koModel[parameter], data[parameter], options)) {
            }
            else if (koModel[parameter] !== null && typeof (koModel[parameter]) == "object" &&
                !(koModel[parameter] instanceof Date) &&
                !isArray(koModel[parameter])) {
                exports.fromJS(koModel[parameter], data[parameter], options);
            }
            else if (typeof (koModel[parameter]) == "function") {
                knockoutElementMapping(koModel[parameter], data[parameter], options);
            }
            else if(ko.getObservable && ko.getObservable(koModel, parameter)) // ko-es5
            {
                var temporaryObservable =  ko.getObservable(koModel, parameter);
                knockoutElementMapping(temporaryObservable, data[parameter], options);
            }
            else if(typeof(koModel[parameter]) != "undefined") {
                koModel[parameter] = data[parameter];
            }
            else if(isEmptyObject){
                if(options.mergeMissingAsObservables === true) {
                    koModel[parameter] = ko.observable(data[parameter]);
                }
                else {
                    koModel[parameter] = data[parameter];
                }
            }
        }
    };

    ko.observableArray.fn.withMergeConstructor = function(mergeConstructor, replaceOnMerge) {
        this.mergeConstructor = mergeConstructor;

        if(replaceOnMerge) {
            this.replaceOnMerge = replaceOnMerge;
        }

        return this;
    };

    ko.observable.fn.withMergeMethod = function(method) {
        this.mergeMethod = method;
        return this;
    };

    ko.observable.fn.withMergeRule = function(rule) {
        this.mergeRule = rule;
        return this;
    };

    exports.rules = [];
    exports.globalHandlers = [];

}));
