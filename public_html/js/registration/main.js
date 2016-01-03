;(function ($, document) {

    var fieldsChange = function () {
        var form = $('.registration-form'),
            country = form.find('[id$="_country"]').first(),
            locales = [],
            otherLocale = 'other',
            allElements = form.find('[data-specific-locale]'),
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
            elements[key].show()
                .children('.form-control').prop('required', true);
        };

        country.on('change', changeLocale);
        changeLocale();
    };

    $(document).ready(function() {
        fieldsChange();

        $('.registration-form [data-toggle="tooltip"]').tooltip();
    });

})(jQuery, document);
