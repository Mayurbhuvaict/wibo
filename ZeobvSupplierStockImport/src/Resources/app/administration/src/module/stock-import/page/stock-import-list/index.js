import template from './stock-import-list.html.twig';
import './stock-import-list.scss'

const { Component,Mixin } = Shopware;

import deDE from './../../snippet/de-DE.json';
import enGB from './../../snippet/en-GB.json';

Component.register('stock-import-list', {
    template,

    inject: [
        'configService'
    ],
    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
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
        },

        onAtagSave(){
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/atagsupplier',{
                        headers
            }).then((response) => {
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
            });
        },

        onEtnaSave(){
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/etnasupplier',{
                headers
            }).then((response) => {
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
            });
        },

        onAskoSave(){
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/askosupplier',{
                headers
            }).then((response) => {
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
            });
        },

        onPelgrimSave(){
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/pelgrimsupplier',{
                headers
            }).then((response) => {
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
            });
        },

        onHisenseSave(){
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/hisensesupplier',{
                headers
            }).then((response) => {
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
            });
        },

        onAmacomSave(){
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/amacomsupplier',{
                headers
            }).then((response) => {
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
            });
        },

        onBorettiSave(){
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/borettisupplier',{
                headers
            }).then((response) => {
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
            });
        },

        onInventumSave() {
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/inventumsupplier',{
                headers
            }).then((response) => {
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
            });
        },

        onSmegSave() {
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/zeostock/smegsupplier',{
                headers
            }).then((response) => {
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
            });
        }
    }
});
