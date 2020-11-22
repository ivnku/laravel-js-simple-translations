let translations = require('./resources/lang/lang.translations.json');
let t = new (require('./SimpleTranslator').SimpleTranslator)(translations);

console.log(t.__('subfolder.another.greeting', {name: 'John'})); // returns "Hello, John, we are glad you are here"