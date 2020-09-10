var gplay = require('google-play-scraper');

let name = process.argv[2];

gplay.search({
    term: name,
	fullDetail: true,
    lang: 'ru',
    num: 10,
}).then(console.log, console.log);