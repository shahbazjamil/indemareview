
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'b96d73bfcdcd9843e613',
    cluster: 'us2',
    forceTLS: true
});


import Vue from 'vue';

const app = new Vue({
    el: '#new_item',
    data() {
        return {
            imageState: true,
            priceState: false,
            descriptionState: false,
            clientDescriptionState: false,
            dimensionsState: false,
            manufacturerState: false,
            materialState: false,
            finishState: false,
            skuState: false,
            urlState: false,
            markupValue: 0,
            priceValue: 0,
            originUrl: '',
            titleState: false,
            projectState: false,
            categoryState: false,
            locationState: false,
        }
    },
    mounted() {
        window.Echo.channel('channel-' + uuid)
            .listen('ParsingCallEvent', (e) => {
                if (e.data.type === 'pageData') {
                    let matches = e.data.value.match('\:\/\/([^\/]+)');
                    this.originUrl = e.data.value;
                    $('#item_name').val(e.data.text);
                    $('#item_vendor').val(matches[1]);
                    $('#item_url').val(e.data.value);
                } else if (e.data.type === 'img') {
                    if (this.imageState) {
                        $('#bookmarklet-image-preview').attr("src", e.data.value);
                        $('#bookmarklet-image-input').val(e.data.value);
                        this.imageState = false;
                        $('#indemia_loader_block').css('display', 'none');
                        $('#new_item').css('display', 'block');
                    }
                } else {
                    if (this.descriptionState) {
                        $('#item_description').val(e.data.value);
                        this.descriptionState = false;
                    }
                    if (this.clientDescriptionState) {
                        $('#item_client_description').val(e.data.value);
                        this.clientDescriptionState = false;
                    }
                    
                    if (this.priceState) {
                        let price = e.data.value.replace(/[^0-9\.\,]+/gi, "");
                        price = parseFloat(price.replace(',', '.'));
                        $('#item_price').val(price);
                        $('#item_unit_price').val(price);
                        this.priceValue = price;
                        this.priceState = false;
                    }
                    if (this.dimensionsState) {
                        $('#item_dimensions').val(e.data.value);
                        this.dimensionsState = false;
                    }
                    if (this.materialState) {
                        $('#item_material').val(e.data.value);
                        this.materialState = false;
                    }
                    if (this.manufacturerState) {
                        $('#item_manufacturer').val(e.data.value);
                        this.manufacturerState = false;
                    }
                    if (this.finishState) {
                        $('#item_finish').val(e.data.value);
                        this.finishState = false;
                    }
                    if (this.skuState) {
                        $('#item_sku').val(e.data.value);
                        this.skuState = false;
                    }
                    if (this.urlState) {
                        $('#item_url').val(e.data.value);
                        this.urlState = false;
                    }
                    if (this.imageState) {
                        $('#bookmarklet-image-preview').attr("src", e.data.value);
                        $('#bookmarklet-image-input').val(e.data.value);
                        $('#indemia_loader_block').css('display', 'none');
                        $('#new_item').css('display', 'block');
                        this.imageState = false;
                    }
                    if (this.titleState) {
                        $('#item_name').val(e.data.value);
                        this.titleState = false;
                    }
                    if (this.projectState) {
                        $('#item_project').val(e.data.value);
                        this.projectState = false;
                    }
                    if (this.categoryState) {
                        $('#item_category').val(e.data.value);
                        this.categoryState = false;
                    }
                    if (this.locationState) {
                        $('#item_location').val(e.data.value);
                        this.locationState = false;
                    }
                    
                    
                }
            });
    },

    methods: {
        sendPostMessage(message) {
            window.top.postMessage(message, this.originUrl)
        },
        getPrice() {
            this.priceState = !this.priceState;
            if (this.priceState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }
        },
        selectImage() {
            this.imageState = !this.imageState;
            if (this.imageState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }
        },
        selectTitle() {
            this.titleState = !this.titleState;
            if (this.titleState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }
        },
        selectDescription() {
            this.descriptionState = !this.descriptionState;
            if (this.descriptionState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }
        },
        selectClientDescription() {
            this.clientDescriptionState = !this.clientDescriptionState;
            if (this.clientDescriptionState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }
        },
        selectManufacturer() {
            this.manufacturerState = !this.manufacturerState;
            if (this.manufacturerState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }

        },
        selectMaterial() {
            this.materialState = !this.materialState;
            if (this.materialState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }
        },
        selectFinish() {
            this.finishState = !this.finishState;
            if (this.finishState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }

        },
        selectDimensions() {
            this.dimensionsState = !this.dimensionsState;
            if (this.dimensionsState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }
        },
        selectSku() {
            this.skuState = !this.skuState;
            if (this.skuState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }

        },
        selectUrl() {
            this.urlState = !this.urlState;
            if (this.urlState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }

        },
        selectProject() {
            this.projectState = !this.projectState;
            if (this.projectState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }

        },
        selectCategory() {
            this.categoryState = !this.categoryState;
            if (this.categoryState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }

        },
        selectLocation() {
            this.locationState = !this.locationState;
            if (this.locationState) {
                this.sendPostMessage('active');
            } else {
                this.sendPostMessage('deactive')
            }

        },
        closeModal() {
            this.sendPostMessage('close')
        },
        saveForm(e) {
            e.preventDefault();
            let data = $(e.target).parents('form').serializeArray();
            $.ajax({
                url: '/api-parse/form/save/' + uuid,
                data: data,
                method: 'POST',
                dataType: "json",
                success: (data) => {
                    $('#new_item').css('display', 'none');
                    $('#success-message').css('display', 'flex');
                    setTimeout(() => {
                        this.closeModal();
                        this.sendPostMessage('reload')
                    }, 3000);

                },
                error: function () {

                }
            });
        }
    },
    computed: {
        markupData() {
            return parseFloat(this.priceValue) + (parseFloat(this.markupValue) * parseFloat(this.priceValue)) / 100;
        }
    }
});

