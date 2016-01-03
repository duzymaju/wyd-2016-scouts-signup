;(function ($, document, undefinedType) {

    var fieldsChange = function () {
        var form = $('.registration-form'),
            allElements = form.find('[data-specific-locale]');
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

    $(document).ready(function() {
        fieldsChange();

        $('.registration-form [data-toggle="tooltip"]').tooltip();
    });

})(jQuery, document, 'undefined');
