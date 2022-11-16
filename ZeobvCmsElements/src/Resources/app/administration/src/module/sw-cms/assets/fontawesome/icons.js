export class FontAwesomeIcons{
    constructor(iconsData) {
        this.icons = iconsData;

        this.icons[Symbol.iterator] = function* () {
            for (let key of Object.keys(this)) {
                yield [key, this[key]];
            }
        };
    }

    getIcons() {
        return Object.keys(this.icons);
    }

    getIcon(icon){
        if(this.icons[icon] === undefined){
            return null;
        }

        return this.icons[icon];
    }

    getRandomIcon(){
        var keys = Object.keys(this.icons);
        return keys[ keys.length * Math.random() << 0];
    }

    getStyles(){
        let styles = [];

        for(let [key, icon] of this.icons){
            if(typeof icon['styles'] === 'undefined'){
                continue;
            }

            let iconStyles = icon['styles'];

            for(let iconStyle of iconStyles){
                // If icon style does not exists, add it to our unique list
                if(styles.indexOf(iconStyle) === -1){
                    styles.push(iconStyle);
                }
            }
        }

        return styles;
    }

    getIconsOfStyle(style){
        let icons = [];

        for(let [iconName, iconData] of this.icons){
            if(typeof iconData['styles'] === 'undefined'){
                continue;
            }

            if(iconData['styles'].indexOf(style) !== -1){
                icons.push(iconName);
            }
        }

        return icons;
    }
}

// Font awesome Free
// import FontAwesomeFreeIconsData from './dist/fontawesome-free-5.13.0-web/metadata/icons.json';

// Font awesome Pro
import FontAwesomeProIconsData from './dist/fontawesome-pro-5.13.0-web/metadata/icons.json';


export default new FontAwesomeIcons(FontAwesomeProIconsData);
