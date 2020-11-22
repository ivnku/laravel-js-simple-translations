##Laravel JavaScript Simple Translator

**The problem**:

When you are working with Laravel you have an ability to create multilingual sites - https://laravel.com/docs/8.x/localization.
But when you start to use any frontend framework (React, Vue, Angular etc) it turned out that you cannot use localization features there provided
by Laravel.

**The solution**:

The idea is to generate json file with all the translations from `resources/lang` folder and then use it inside the
custom SimpleTranslator class which mimics the functionality of the `__()` function in Laravel.

Steps to make it work:
- Download repository files
- Move **SimpleTranslator** and **create_translations_json.php** anywhere inside your project (I've chosen `resources/js/utils` for
SimpleTranslator and root of the project for the create_translations_json.php, if you placed **create_translations_json.php**
 somewhere else, don't forget to set `$resourceRoot` path inside.
- Open your `package.json` and add execution of `create_translations_json.php` before compiling your assets.
In my case it was like this:

        ...
        "scripts": {
            "dev": "php create_translations_json.php && npm run development",
            "development": "cross-env NODE_ENV=development node_modules/webpack/bin/webpack.js —progress —hide-modules —config=node_modules/laravel-mix/setup/webpack.config.js"
        ...
    It will generate a fresh file with translations in `resources/lang/lang.translations.json` after the every execution of `npm run dev`, so your js apps can use the fresh version of it.
- import SimpleTranslator in your js app:

        const translations = require(<path_to_lang.translations.json>);
        let t = new (require(<path_to_SimpleTranslator>).SimpleTranslator)(translations);
        
        t.__('subfolder.another.greeting');
        
After that we can use the same keys as in laravel `__()` function.