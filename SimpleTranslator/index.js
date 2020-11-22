class SimpleTranslator {
    
    /**
     * @param translations - content of the lang.translations.json file
     */
    constructor(translations) {
        this.translations = translations.lang !== undefined ? translations.lang : {};
        
        if (typeof(document) != "undefined" && document !== null) {
            this.lang = document.body.parentNode.lang;
        } else { // if script is running through cmd
            this.lang = 'en';
        }
    }

    /**
     * Get translation string based on given key
     * @param key - e.g. "de.auth.login"
     * @param attrs - object of arguments which are presented in the string and should be replaced
     * @return string
     */
    __(key, attrs = {}) {
        let keys = key.split('.');
        
        keys.unshift(this.lang);
        
        let value = this.getKeyRecursively(keys, this.translations);
        return this.replaceAttributes(value, attrs);
    }

    /**
     * Get deeply nested key of an object
     * @param keys - array of keys which are in a strict order to access object
     * @param object
     * @returns {string}
     */
    getKeyRecursively(keys, object) {
        let result = '';
        let key = keys.shift();
        let value = object[key];
        
        if (keys.length === 0) {
            return String(value);
        } else if (value !== undefined && value !== null) {
            result = this.getKeyRecursively(keys, value);
            return String(result);
        } else {
            return '';
        }
    }

    /**
     * Replace all provided attributes in a string.
     * e.g. {name: "Ben"} will replace :name attribute in string. "Hello, :name" => "Hello, Ben"
     * @param value
     * @param attrs
     * @returns {*}
     */
    replaceAttributes(value, attrs) {
        for (let key in attrs) {
            if (attrs.hasOwnProperty(key)) {
                value = value.replace(':' + key, attrs[key]);
            }
        }
        
        return value;
    }
}

module.exports = {
    SimpleTranslator
};