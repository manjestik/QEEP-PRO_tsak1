var store = require('app-store-scraper');

let name = process.argv[2];

store.search({
    term: name,
    lang: 'ru',
    country: 'ru',
    num: 50,
}).then(console.log, console.log);