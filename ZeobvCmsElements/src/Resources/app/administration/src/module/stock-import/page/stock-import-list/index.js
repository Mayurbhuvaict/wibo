import template from './stock-import-list.html.twig';

const { Component,Mixin } = Shopware;

import deDE from './../../snippet/de-DE.json';
import enGB from './../../snippet/en-GB.json';

Component.register('stock-import-list', {
    template,

    inject: [
        'configService',
        'systemConfigApiService'
    ],
    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            counter: null,
            PimImportSetting:{
                'PimImport.config.mainProductCounter' : null,
                'PimImport.config.relatedProductCounter' : null,
                'PimImport.config.productPartCounter' : null,
                'PimImport.config.addonProductCounter' : null,
                'PimImport.config.category_counter' : null,
                'PimImport.config.CategoryPublicationCode' : null,
            },
            mainProductCounter:null
        };
    },

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    created() {
        this.createdComponent()
    },

    methods: {

        async createdComponent(){
            this.PimImportSetting = await this.systemConfigApiService.getValues('PimImport');
            this.mainProductCounter = this.PimImportSetting['PimImport.config.mainProductCounter'];
            this.relatedProductCounter = this.PimImportSetting['PimImport.config.relatedProductCounter'];
            this.productPartCounter = this.PimImportSetting['PimImport.config.productPartCounter'];
            this.addonProductCounter = this.PimImportSetting['PimImport.config.addonProductCounter'];
            this.category_counter = this.PimImportSetting['PimImport.config.category_counter'];
            this.CategoryPublicationCode = this.PimImportSetting['PimImport.config.CategoryPublicationCode'];
        },

        onSave(){
            let headers = this.configService.getBasicHeaders();
            //params:{counter:document.getElementById("pim_main_p_counter").textContent},
            return this.configService.httpClient.get('/zeostock/aepsupplier',{
                        headers
                    }).then((response) => {
                        let loopcounter = response.data.counter;
                        let endcounter = response.data.endcounter;

                        if (response.data.type === 'error') {
                            this.createNotificationError({
                                title: response.data.type,
                                message: response.data.message
                            });
                            return;
                        }
                        this.createNotificationSuccess({
                            title: response.data.type,
                            message: response.data.message
                        });
            if (loopcounter) {
                document.getElementById("pim_main_p_counter").innerHTML = loopcounter;
                this.counter = (loopcounter-1)+'/'+(endcounter-1);
                if (loopcounter === endcounter || loopcounter > endcounter) {
                    //stop import product
                } else {
                    this.$refs.pimMainButton.$el.click();
                }
            }
                    });
        },
    }
});
