const assert = require('assert');
const puppeteer = require('puppeteer');
const dbConnection = require('./databaseConnection');
const helpers = require('./helpers');

const LOGIN = 'A random user';
const EMAIL = 'randomuser@random.com';
const PW = 'randomPass';

describe('Member management tests', function() {
    let chrome;
    let page;
    let db;

    before(async function() {
        chrome = await puppeteer.launch();
        db = dbConnection.connectToDatabase();
    });

    beforeEach(async function() {
        page = await chrome.newPage();
        await page.setViewport({
            width: 1280,
            height: 720
        });
    });

    afterEach(async function() {
        await page.close();
    });

    after(async function() {
        await chrome.close();
        db.query(`DELETE FROM member WHERE \`name\` = '${LOGIN}'`);
        db.end();
    });

    describe('Tests user creation', function() {
        it('Should create user', async function() {
            await helpers.register(page, LOGIN, EMAIL, PW);


            const queryResult = await helpers.databaseQuery(db, `SELECT * FROM member WHERE \`name\` = '${LOGIN}'`);

            assert(queryResult.length !== 0);
        });
    });

    describe('Tests user connection', function() {
        it('Should connect user to dashboard', async function() {
            await helpers.login(page, EMAIL, PW);

            const userName = await page.evaluate(
                () => document.querySelector('#navbarContent > div.dropdown.px-lg-3 > a').textContent
            );

            assert(userName.includes(LOGIN));
        });
    });
});
