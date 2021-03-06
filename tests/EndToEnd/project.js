const assert = require('assert');
const puppeteer = require('puppeteer');
const dbConnection = require('./databaseConnection');
const helpers = require('./helpers');

const LOGIN = 'A random user';
const EMAIL = 'randomuser@random.com';
const PW = 'randomPass';

const RANDOM = helpers.generateRandomString();
const RANDOM_EDITED = helpers.generateRandomString();

describe('Project management tests', function() {
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

    describe('Tests project creation', function() {
        it('Should create a project', async function() {
            await helpers.register(page, LOGIN, EMAIL, PW);
            await helpers.login(page, EMAIL, PW);
            await page.click('#navbarContent > div.navbar-nav.mr-auto > a:nth-child(2)');

            await page.waitForSelector('header > nav');
            await page.type('#project_name', RANDOM);
            await page.type('#project_description', RANDOM);
            await page.click('body > div > div > div > form > button');

            const queryResult = await helpers.databaseQuery(db,
                `SELECT * FROM project WHERE \`name\` = '${RANDOM}' AND  \`description\` = '${RANDOM}'`);

            assert(queryResult.length !== 0);
        });
    });

    describe('Tests project visualization', function() {
        it('Should display a project', async function() {
            await page.goto('http://127.0.0.1:9543/dashboard');

            await page.waitForSelector('header > nav');
            await page.click('#project > a:last-child');

            await page.waitForSelector('header > nav');
            const projectTitle = await page.evaluate(
                () => document.querySelector('#content > h1').textContent.trim()
            );
            const projectDesc = await page.evaluate(
                () => document.querySelector(
                    '#content > div.container > div > div.col-sm-6.col-lg-8 > div > div > p.text-justify'
                ).textContent.trim()
            );

            assert(projectTitle === RANDOM);
            assert(projectDesc.includes(RANDOM));
        });
    });

    describe('Tests project edition', function() {
        it('Should edit a project', async function() {
            await page.goto('http://127.0.0.1:9543/dashboard');

            await page.waitForSelector('header > nav');
            await page.click('#project > a:last-child');

            await page.waitForSelector('header > nav');
            await page.click('#content > div.container > div > div.col-sm-6.col-lg-8 > div > div > a');

            await page.waitForSelector('header > nav');
            await page.evaluate(() => document.querySelector('#project_name').value = '');
            await page.evaluate(() => document.querySelector('#project_description').value = '');
            await page.type('#project_name', RANDOM_EDITED);
            await page.type('#project_description', RANDOM_EDITED);
            await page.click('#content > div > div > div > form > button');

            await page.waitForSelector('header > nav');

            const queryResult = await helpers.databaseQuery(db,
                `SELECT * FROM project WHERE \`name\` = '${RANDOM_EDITED}' AND  \`description\` = '${RANDOM_EDITED}'`);

            assert(queryResult.length !== 0);
        });
    });

    describe('Tests project deletion', function() {
        it('Should delete a project', async function() {
            await page.goto('http://127.0.0.1:9543/dashboard');

            await page.waitForSelector('header > nav');
            await page.click('#project > a:last-child');

            await page.waitForSelector('header > nav');
            await page.click('#content > div.container > div > div.col-sm-6.col-lg-8 > div > div > button');
            await page.waitForSelector('#project-delete-confirm > div > div > div.modal-footer > a', {visible: true});
            await page.waitFor(500);
            await page.click('#project-delete-confirm > div > div > div.modal-footer > a');
            await page.waitFor(500);

            await page.waitForSelector('header > nav');

            const queryResult = await helpers.databaseQuery(db, `SELECT * FROM project WHERE \`name\` = '${RANDOM_EDITED}'`);

            assert(queryResult.length === 0);
            await helpers.logout(page);
        });
    });
});
