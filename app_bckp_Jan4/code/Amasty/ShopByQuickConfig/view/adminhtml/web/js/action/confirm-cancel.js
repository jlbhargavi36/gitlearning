/**
 * Show confirmation window before delete form.
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/modal/confirm',
    'Amasty_ShopByQuickConfig/js/model/form-state',
    'mage/template',
    'text!Amasty_ShopByQuickConfig/template/confirm-cancel.html',
    'mage/translate'
], function ($, ko, confirm, formState, templateRender, confirmTemplate) {
    'use strict';

    var doNotShowAgain = ko.observable(false);

    return function () {
        var deferred = $.Deferred(),
            content;

        if (doNotShowAgain() || !formState.isFormModified()) {
            deferred.resolve();

            return deferred.promise();
        }

        content = templateRender(
            confirmTemplate,
            {
                message: $.mage.__('You have unsaved changes that will be lost'
                    + ' if you decide to exit the settings area.'),
                label: $.mage.__('Do not display this warning again.')
            }
        );

        confirm({
            title: $.mage.__('Are you sure?'),
            content: content,
            actions: {
                confirm: function () {
                    doNotShowAgain($('#amshopbyconfig-dont-show-confirm-checkbox').is(':checked'));
                    deferred.resolve();
                },
                cancel: function () {
                    deferred.reject();
                }
            }
        });

        return deferred.promise();
    };
});
