/**
 * Generic form actions.
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Amasty_ShopByQuickConfig/js/action/confirm-cancel',
    './active-filter',
    './form-state',
    'mage/translate'
], function ($, _, registry, confirm, activeFilter, formState) {
    'use strict';

    var formActions = {
        options: {
            uiFormName: 'amasty_shopby_filters.amasty_shopby_filters',
            uiInsertFormName: 'index = filter_edit_form',
            uiFormPlaceholderName: 'index = catalog_placeholder'
        },

        getUiForm: function () {
            return registry.get(this.options.uiFormName);
        },

        cancelAction: function () {
            confirm().done(this.removeForm);
        },

        removeForm: function () {
            var insert = registry.get(this.options.uiInsertFormName),
                placeholder = registry.get(this.options.uiFormPlaceholderName);

            activeFilter.activeFilterCode('');
            formState.resetState();
            insert.destroyInserted();
            placeholder.visible(true);
        },

        reloadForm: function () {
            var insert = registry.get(this.options.uiInsertFormName);

            formState.resetState();
            insert.destroyInserted();
            insert.render();
        },

        /**
         * @param {HTMLFormElement} form
         * @param {jQuery.Event} event
         * @returns {void}
         */
        formSubmitHandle: function (form, event) {
            var $form = $(form),
                $body = $('body');

            event.preventDefault();

            $body.trigger('processStart');

            return $.ajax({
                type: $form.attr('method'),
                url: $form.attr('action'),
                data: $form.serialize(),
                global: true
            })
                .done(this.onSuccess)
                .fail(this.onError)
                .always(function () {
                    $body.trigger('processStop');
                });
        },

        onSuccess: function (data) {
            var form = this.getUiForm();

            form.responseStatus(null);
            form.responseStatus(!data.error);

            if (data) {
                form.responseData(data);
            }

            form.reload();
            this.reloadForm();
        },

        onError: function () {
            var form = this.getUiForm();

            form.responseStatus(null);
            form.responseStatus(false);
            form.responseData({
                error: true,
                messages: 'Something went wrong.'
            });
        }
    };

    _.bindAll(
        formActions,
        'cancelAction',
        'removeForm',
        'onSuccess',
        'onError',
        'formSubmitHandle'
    );

    return formActions;
});
