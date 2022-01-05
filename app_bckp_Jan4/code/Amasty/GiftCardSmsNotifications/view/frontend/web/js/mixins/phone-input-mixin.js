define([], function () {
    'use strict';

    var mixin = {
        defaults: {
            phoneTemplate: 'Amasty_GiftCardSmsNotifications/phone'
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
