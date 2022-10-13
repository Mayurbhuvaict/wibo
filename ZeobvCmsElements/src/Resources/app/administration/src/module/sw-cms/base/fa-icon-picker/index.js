import FontAwesomeIcons from '../../assets/fontawesome/icons';

import template from './fa-icon-picker.html.twig';
import './fa-icon-picker.scss';

const { Component, Mixin } = Shopware;

Component.register('fa-icon-picker', {
    template,

    mixins: [
        Mixin.getByName('sw-form-field'),
    ],

    props: {
        icon: {
            type: String,
            required: false,
            default: 'accessible-icon'
        },

        iconColor: {
            type: String,
            required: false,
            default: ''
        },

        family: {
            type: String,
            required: false,
            default: 'regular'
        },

        disabled: {
            type: Boolean,
            required: false,
            default: false
        },

        readonly: {
            type: Boolean,
            required: false,
            default: false
        },

        zIndex: {
            type: [Number, null],
            required: false,
            default: 1001
        },

        itemsPerPage: {
            type: Number,
            required: false,
            default: 35
        }
    },

    data() {
        return {
            selectedIcon: this.icon,
            selectedFamily: this.family,
            visible: false,
            search: '',
            currentPage: 1,
        };
    },

    watch: {
        search: {
            deep: true,
            handler() {
                this.currentPage = 1;
            }
        },
    },

    computed: {
        icons() {
            let icons = FontAwesomeIcons.getIcons();

            if(this.search){
                let searchVal = this.search.toLowerCase();

                icons = icons.filter(iconName => {
                    let icon = FontAwesomeIcons.getIcon(iconName);

                    return icon.label.toLowerCase().includes(searchVal);
                })
            }

            let iconSVGs = [];
            icons.forEach((iconName) => {
                let icon = FontAwesomeIcons.getIcon(iconName);

                if(!icon.svg){
                    return;
                }

                for(let [family, svgData] of Object.entries(icon.svg)){
                    iconSVGs.push({
                        name: iconName,
                        family: family,
                        svg: svgData.raw
                    });
                }
            });

            return iconSVGs;
        },

        visibleIcons(){
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;

            return this.icons.slice(start, end);
        },

        totalPages(){
            return 1 + Math.floor(this.icons.length / this.itemsPerPage);
        },

        hasPrev(){
            return this.currentPage > 1;
        },

        hasNext(){
            return this.currentPage < this.totalPages;
        },
    },

    methods: {
        prevPage(){
            if(this.hasPrev){
                this.currentPage--;
            }
        },

        nextPage(){
            if(this.hasNext){
                this.currentPage++;
            }
        },

        onManualTextInput(){
            let icon = FontAwesomeIcons.getIcon(this.selectedIcon);

            if(icon){
                this.emitIconChange();
            }
        },

        onIconClicked(icon){
            if(this.selectedIcon === icon.name && this.selectedFamily === icon.family){
                return;
            }

            this.selectedIcon = icon.name;
            this.selectedFamily = icon.family;

            this.emitIconChange();
        },

        isIconSelected(icon){
            return (icon.name === this.selectedIcon)
                && icon.family === this.selectedFamily;
        },

        toggleColorPicker() {
            if (this.disabled) {
                return;
            }

            this.visible = !this.visible;

            if (this.visible) {
                this.setOutsideClickEvent();

                return;
            }

            this.removeOutsideClickEvent();
        },

        setOutsideClickEvent() {
            window.addEventListener('mousedown', this.outsideClick);
        },

        removeOutsideClickEvent() {
            window.removeEventListener('mousedown', this.outsideClick);
        },


        outsideClick(e) {
            if (/^fa-icon-picker__preview/.test(e.target.classList[0])) {
                return;
            }

            const isColorpicker = e.target.closest('.fa-icon-picker__colorpicker');

            if (isColorpicker !== null) {
                return;
            }

            this.visible = false;
            this.removeOutsideClickEvent();
        },

        emitIconChange(){
            let icon = FontAwesomeIcons.getIcon(this.selectedIcon);

            this.$emit('change', {
                icon: this.selectedIcon,
                family: this.selectedFamily,
                svg: icon.svg[this.selectedFamily].raw
            });
        }
    },
});
