/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file product-options-media-gallery.js
 * @author Philipp Breitsprecher
 * @date 18.11.25, 12:54
 * @email philippbreitsprecher@gmail.com
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'jquery/ui'
], function ($, alert, $t) {
    'use strict';

    return {
        uploadUrl: null,
        mediaGalleryUrl: null,

        /**
         * Initialize media gallery integration
         */
        init: function(uploadUrl, mediaGalleryUrl) {
            this.uploadUrl = uploadUrl;
            this.mediaGalleryUrl = mediaGalleryUrl;
            this.bindEvents();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Direct upload button
            $(document).on('click', '.upload-image-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $fileInput = $btn.siblings('.image-uploader');
                $fileInput.click();
            });

            // Media gallery button
            $(document).on('click', '.select-from-gallery-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $imageField = $btn.siblings('input[type="text"]');
                self.openMediaGallery($imageField);
            });

            // File upload handler
            $(document).on('change', '.image-uploader', function() {
                self.handleFileUpload($(this));
            });
        },

        /**
         * Open Magento Media Gallery
         */
        openMediaGallery: function($targetField) {
            var self = this;

            require([
                'Magento_Cms/js/browser/adapter'
            ], function(browserAdapter) {
                browserAdapter({
                    targetElementId: $targetField.attr('id'),
                    onInsertFile: function(file) {
                        // Extract relative path from full URL
                        var mediaUrl = file.url.replace(window.location.origin, '');
                        var path = mediaUrl.replace(/^\/media\//, '');
                        $targetField.val(path).trigger('change');

                        alert({
                            title: $t('Success'),
                            content: $t('Image selected from gallery!')
                        });
                    }
                }).open();
            });
        },

        /**
         * Handle direct file upload
         */
        handleFileUpload: function($fileInput) {
            var self = this;
            var $imageField = $fileInput.siblings('input[type="text"]');

            if ($fileInput[0].files && $fileInput[0].files[0]) {
                var formData = new FormData();
                formData.append('image', $fileInput[0].files[0]);
                formData.append('form_key', window.FORM_KEY);

                $.ajax({
                    url: self.uploadUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    showLoader: true,
                    success: function(response) {
                        if (response.success) {
                            $imageField.val('catalog/customoption/' + response.file).trigger('change');
                            alert({
                                title: $t('Success'),
                                content: $t('Image uploaded successfully!')
                            });
                        } else {
                            alert({
                                title: $t('Error'),
                                content: response.error || $t('Upload failed')
                            });
                        }
                    },
                    error: function() {
                        alert({
                            title: $t('Error'),
                            content: $t('An error occurred during upload')
                        });
                    }
                });

                $fileInput.val('');
            }
        }
    };
});