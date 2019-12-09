const assert = require('assert');
const puppeteer = require('puppeteer');
const dbConnection = require('./databaseConnection');
const loginHelper = require('./loginHelper');

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
            await page.goto('http://127.0.0.1:9543/register');
            await page.waitForSelector('body > div > div > div > form');
            await page.type('#registration_name', LOGIN);
            await page.type('#registration_emailAddress', EMAIL);
            await page.type('#registration_password_first', PW);
            await page.type('#registration_password_second', PW);

            await page.click('body > div > div > div > form > button');

            await page.waitForSelector('header > nav');

            const queryResult = await new Promise(function(resolve, reject) {
                db.query(`SELECT * FROM member WHERE \`name\` = '${LOGIN}'`, function(error, results) {
                    if (error) {
                        reject(error);
                    }
                    resolve(results);
                });
            });

            assert(queryResult.length !== 0);
        });
    });

    describe('Tests user connection', function() {
        it('Should connect user to dashboard', async function() {
            await loginHelper.login(page, EMAIL, PW);

            const userName = await page.evaluate(
                () => document.querySelector('#navbarContent > div.dropdown.px-lg-3 > a').textContent
            );

            assert(userName.includes(LOGIN));
        });
    });
});
