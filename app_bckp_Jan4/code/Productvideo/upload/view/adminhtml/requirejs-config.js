/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/*var config = {
    config: {
        mixins: {
            'Magento_ProductVideo/js/get-video-information':{
            'Productvideo_upload/js/get-video-information-mixin':true
        },
             'Magento_ProductVideo/js/new-video-dialog':{
            'Productvideo_upload/js/new-video-dialog-mixin':true
        },
             'Magento_ProductVideo/js/video-modal':{
            'Productvideo_upload/js/video-modal-mixin':true
        }
    }
    }
};*/

var config = {
    "map": {
        "*": {
            "Magento_ProductVideo/js/get-video-information": "Productvideo_upload/js/get-video-information",
            newVideoDialog: "Productvideo_upload/js/new-video-dialog",
            openVideoModal: "Productvideo_upload/js/video-modal",
        }
    }
};
