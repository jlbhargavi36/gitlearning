/**
 * Filters form provider.
 */
define([
    'ko',
    'underscore',
    'Magento_Ui/js/form/provider',
    'uiRegistry',
    'mageUtils',
    'Amasty_ShopByQuickConfig/js/model/active-filter',
    'rjsResolver'
], function (ko, _, Provider, registry, utils, activeFilter, rjsResolver) {
    'use strict';

    return Provider.extend({
        defaults: {
            ignoreTmpls: {
                originData: true
            }
        },

        /**
         * Initializes provider component.
         *
         * @returns {Provider} Chainable.
         */
        initialize: function () {
            this._super();

            rjsResolver(function () {
                this.set('params.isSaveAvailable', true);
            }.bind(this));

            return this;
        },

        initObservable: function () {
            this._super();

            this.on('data.reset', this.reset.bind(this));

            return this;
        },

        initConfig: function () {
            this._super();

            this.originData = this.data;

            return this;
        },

        /**
         * Compare stored and origin currently edited item data.
         *
         * @return {boolean}
         */
        isCurrentFilterEdited: function () {
            var filterCode = activeFilter.activeFilterCode(),
                result = false;

            if (!filterCode) {
                return result;
            }

            _.find(this.originData, function (items, blockName) {
                var parentPath,
                    originItem,
                    item;

                if (!_.isArray(items)) {
                    return false;
                }

                parentPath = utils.fullPath('data', blockName);
                originItem = _.find(items, { 'filter_code': filterCode });
                item = _.find(this.get(parentPath), { 'filter_code': filterCode });

                if (_.isObject(originItem)) {
                    delete originItem['record_id'];
                }

                if (_.isObject(item)) {
                    delete item['record_id'];
                }

                result = !_.isEqual(item, originItem);

                return result;
            }, this);

            return result;
        },

        reset: function () {
            this.set('params.isSaveAvailable', false);

            this.setData(this.get('data'), this.originData, this.data, 'data');

            this.set('params.isSaveAvailable', true);
        },

        /**
         * Update data that stored in provider.
         *
         * @param {Boolean} isProvider
         * @param {Object} newData
         *
         * @returns {Provider}
         */
        updateConfig: function (isProvider, newData) {
            if (isProvider === true) {
                this.originData = newData.data;
            }

            return this._super();
        },

        /**
         *  Set data to provider based on current data.
         *  Overridden to fix behavior when items are added or deleted.
         *
         * @param {Object} oldData
         * @param {Object} newData
         * @param {Provider} current
         * @param {String} parentPath
         * @returns {void}
         */
        setData: function (oldData, newData, current, parentPath) {
            _.each(newData, function (val, key) {
                if (_.isArray(val)) {
                    this._updateDynamicRowsData(key, val, oldData);
                }

                if (_.isUndefined(oldData[key])) {
                    this.set(utils.fullPath(parentPath, key), val);
                } else if (_.isObject(val) || _.isArray(val)) {
                    this.setData(oldData[key], val, current[key], utils.fullPath(parentPath, key));
                } else if (val != oldData[key]) { // eslint-disable-line eqeqeq
                    this.set(utils.fullPath(parentPath, key), val);
                }
            }, this);

            this._checkDelete(oldData, newData, current, parentPath);
        },

        _updateDynamicRowsData: function (key, val, oldData) {
            var intersectionCount = val.length - oldData[key].length,
                dynamicRowsComponent = intersectionCount !== 0 ? registry.get('index = ' + key) : null,
                id;

            if (intersectionCount > 0) {
                for (intersectionCount; intersectionCount > 0; intersectionCount--) {
                    dynamicRowsComponent.processingAddChild(val[val.length - intersectionCount]);
                }
            } else if (intersectionCount < 0) {
                for (intersectionCount; intersectionCount < 0; intersectionCount++) {
                    id = oldData[key].length + intersectionCount;
                    dynamicRowsComponent.deleteRecord(
                        id,
                        oldData[key][id][dynamicRowsComponent.identificationProperty]
                    );
                }
            }
        },

        _checkDelete: function (oldData, newData, current, parentPath) {
            _.each(oldData, function (val, key) {
                if (_.isUndefined(newData[key])) {
                    this.remove(utils.fullPath(parentPath, key));
                } else if (_.isObject(val) || _.isArray(val)) {
                    this._checkDelete(val, newData[key], current[key], utils.fullPath(parentPath, key));
                }
            }, this);
        }
    });
});
