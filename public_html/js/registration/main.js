;(function ($, document, undefinedType) {

    /**
     * Hide specific fields
     *
     * @param {jQuery} form form
     */
    var hideSpecificFields = function (form) {
        var allElements = form.find('[data-specific-locale]');
        if (allElements.length === 0) {
            return;
        }
        var country = form.find('[id$="_country"]').first(),
            locales = [],
            otherLocale = 'other',
            elements = {};

        allElements.each(function () {
            var locale = $(this).data('specific-locale');
            if ($.inArray(locale, locales) === -1) {
                locales.push(locale);
            }
        }).find('label').addClass('required');

        $.each(locales, function (index, value) {
            var key = value !== '' ? value : otherLocale;
            elements[key] = form.find('[data-specific-locale="' + value + '"]');
        });

        var changeLocale = function () {
            var locale = country.val(),
                key = $.inArray(locale, locales) !== -1 ? locale : otherLocale;
            allElements.hide()
                .children('.form-control').prop('required', false);
            if (typeof elements[key] !== undefinedType) {
                elements[key].show()
                    .children('.form-control').prop('required', true);
            }
        };

        country.on('change', changeLocale);
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

    $(document).ready(function() {
        var form = $('.registration-form').first();
        if (form.length === 1) {
            hideSpecificFields(form);
            bindDistrictsWithRegions(form);
            manageServices(form);
            form.find('[data-toggle="tooltip"]').tooltip();
            form.find('.input-group.date').datepicker({
                format: 'yyyy-mm-dd',
                language: $('html').attr('lang')
            });
        }
    });

})(jQuery, document, 'undefined');
