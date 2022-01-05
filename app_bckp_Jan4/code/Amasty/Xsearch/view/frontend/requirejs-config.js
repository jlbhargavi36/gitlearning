var config = {
    map: {
        '*': {
            amSearchSlick: 'Amasty_Base/vendor/slick/slick.min',
            'quickSearch-original' : 'Magento_Search/js/form-mini',
            'quickSearch' : 'Amasty_Xsearch/js/form-mini',
            amastyXsearchAnalyticsCollector: 'Amasty_Xsearch/js/analytics-data-collector',
            amastyXsearchCollectProductView: 'Amasty_Xsearch/js/handle-product-view',
            amSearchForm: 'Amasty_Xsearch/js/form-mini',
            amSearchWidgetOverride: 'Amasty_Xsearch/js/content-type/products/appearance/carousel/widget-override.js'
        }
    },
    paths: {
        catalogAddToCart: 'Magento_Catalog/js/catalog-add-to-cart'
    },
    shim: {
        amSearchForm: {
            deps: [ 'Amasty_Base/vendor/slick/slick.min' ]
        },
        amSearchWidgetOverride: {
            deps: [ 'Amasty_Base/vendor/slick/slick.min' ]
        },
        amSearchSlick: {
            deps: [ 'jquery' ]
        }
    }
};
