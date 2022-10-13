import FontAwesomeIcons from '../../assets/fontawesome/icons';

import template from './fa-icon.html.twig';
import './fa-icon.scss';

const { Component } = Shopware;

Component.register('fa-icon', {
    template,

    props: {
        icon: {
            type: String,
            required: false,
        },
        family: {
            type: String,
            required: false,
        },
        color: {
            type: String,
            required: false,
        },
        random: {
            type: Boolean,
            required: false,
            default: false
        },
        size: {
            type: String,
            required: false,
            default: '80px'
        },
        showLabel: {
            type: Boolean,
            required: false,
            default: false
        },
        showTitle: {
            type: Boolean,
            required: false,
            default: false
        },
    },

    computed: {
        iconLabel() {
            try {
                return this.iconData.label;
            } catch (e) {
                return '';
            }
        },

        iconTitle() {
            if(!this.showTitle){
                return '';
            }

            return this.iconLabel();
        },

        iconFamily() {
            if(this.family) {
                return this.family;
            }

            try {
                let iconStyles = Object.values(this.iconData.styles);

                if(iconStyles === undefined || iconStyles[0] === undefined){
                    return null;
                }

                return iconStyles[0];
            } catch (e) {
                return '';
            }
        },

        iconSVG() {
            try {
                if(this.iconData.svg[this.iconFamily] === undefined){
                    return null;
                }

                return this.iconData.svg[this.iconFamily].raw;
            } catch (e) {
                return '';
            }
        },

        iconStyles() {
            return {
                fill: this.color,
                width: this.size
            }
        },

        iconData(){
            let icon = (!this.icon && this.random)
                ? FontAwesomeIcons.getRandomIcon()
                : this.icon;

            return FontAwesomeIcons.getIcon(icon);
        }
    },
});
