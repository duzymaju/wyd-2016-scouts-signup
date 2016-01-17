;(function ($, document, undefinedType) {

    /**
     * Hide specific fields
     *
     * @param {jQuery} form form
     */
    var hideSpecificFields = function (form) {
        var country = form.find('[id$="_country"]').first(),
            availableLocales = [];

        var checkLocales = function () {
            var allElements = form.find('[data-specific-locale]');
            if (allElements.length === 0) {
                return false;
            }
            allElements.each(function () {
                var locale = $(this).data('specific-locale');
                if ($.inArray(locale, availableLocales) === -1) {
                    availableLocales.push(locale);
                }
            }).find('label').addClass('required');

            return true;
        };

        var changeLocale = function () {
            form.find('[data-specific-locale]')
                .trigger('locale:change', '' + country.val());
        };

        if (!checkLocales()) {
            return;
        }

        form.on('locale:change', '[data-specific-locale]', function (event, selectedLocale) {
            var locale = '' + $(this).data('specific-locale');
            if (locale === selectedLocale || ($.inArray(selectedLocale, availableLocales) === -1 && locale === '')) {
                $(this).show()
                    .children('.form-control').prop('required', true);
            } else {
                $(this).hide()
                    .children('.form-control').prop('required', false);
            }
        });

        country.on('change', changeLocale);
        form.on('enlarge', function () {
            checkLocales();
            changeLocale();
        });
        changeLocale();
    };

    /**
     * Bind districts with regions
     *
     * @param {jQuery} form form
     */
    var bindDistrictsWithRegions = function (form) {
        var regionsField = form.find('[id$="_regionId"]').first(),
            districtsField = form.find('[id$="_districtId"]').first();
        if (regionsField.length !== 1 || districtsField.length !== 1) {
            return;
        }
        var allDistricts = districtsField.find('option'),
            districts = {};

        districtsField.find('option').each(function () {
            var regionId = parseInt(Math.floor(parseInt($(this).val(), 10) / 1000), 10);
            if (typeof districts[regionId] === undefinedType) {
                districts[regionId] = $(this);
            } else {
                districts[regionId] = districts[regionId].add($(this));
            }
        });

        var changeDistricts = function () {
            var regionId = parseInt(regionsField.val(), 10);
            allDistricts.hide();
            if (typeof districts[regionId] !== undefinedType) {
                districts[regionId].show();
            }
            allDistricts.filter(':visible:first').prop('selected', true);
        };
        
        regionsField.on('change', changeDistricts);
        changeDistricts();
    };

    /**
     * Manage services
     *
     * @param {jQuery} form form
     */
    var manageServices = function (form) {
        var serviceMainField = form.find('[id$="_serviceMainId"]:first'),
            serviceMainOptions = serviceMainField.find('option'),
            serviceExtraOptions = form.find('[id$="_serviceExtraId"]:first option');
        if (serviceMainField.length !== 1 || serviceMainOptions.length < 1 || serviceExtraOptions.length < 1) {
            return;
        }

        var diversifyServices = function () {
            var index = serviceMainOptions.filter(':selected').val(),
                toHide = serviceExtraOptions.filter('[value="' + index + '"]');
            serviceExtraOptions.show();
            toHide.hide();

            if (toHide.is(':selected')) {
                toHide.prop('selected', false);
                serviceExtraOptions.filter(':visible:first').prop('selected', true);
            }
        };

        serviceMainField.on('change', diversifyServices);
        diversifyServices();
    };

    /**
     * Manage services
     *
     * @param {jQuery}   form     form
     * @param {function} callback callback
     */
    var manageSubforms = function (form, callback) {
        $('[data-prototype]').each(function () {
            var colsInRow = 2,
                rowClassName = 'row',
                collection = $(this),
                itemName = collection.data('item-name'),
                maxSize = collection.data('max-size'),
                prototype = collection.data('prototype'),
                items = collection.find('.' + itemName);

            if (items.length >= maxSize) {
                return;
            }
            form.find('#add-' + itemName).on('click', function () {
                if (items.length < maxSize) {
                    var row;
                    if (items.length % colsInRow) {
                        row = items.last().closest('.' + rowClassName);
                    } else {
                        row = $('<div class="' + rowClassName + '">');
                        collection.append(row);
                    }
                    var newItem = $(prototype.replace(/__name__/g, items.length).replace(/__no__/g, items.length + 1));
                    row.append(newItem);
                    items = collection.find('.' + itemName);
                    form.trigger('enlarge');
                    callback(newItem);
                    if (items.length === maxSize) {
                        $(this).parent().hide();
                    }
                }
            }).parent().show();
        });
    };
    
    var addTools = function (range) {
        range.find('[data-toggle="tooltip"]').tooltip();
        range.find('.input-group.date').datepicker({
            endDate: '0d',
            format: 'yyyy-mm-dd',
            language: $('html').attr('lang')
        });
    };

    $(document).ready(function() {
        var form = $('.registration-form').first();
        if (form.length === 1) {
            hideSpecificFields(form);
            bindDistrictsWithRegions(form);
            manageServices(form);
            manageSubforms(form, function (item) {
                addTools(item);
            });
            addTools(form);
        }
    });

})(jQuery, document, 'undefined');
