const assert = require('assert');
const puppeteer = require('puppeteer');
const dbConnection = require('./databaseConnection');
const helpers = require('./helpers');

const EMAIL = 'member1@domain.com';
const PW = 'someReallySecurePassword';

const NAME = 'Some random project';
const DESC = 'As the title says, this is some random project';
const EDITED_DESC = 'This is now some random edited project';

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
        await helpers.login(page, EMAIL, PW);
    });

    afterEach(async function() {
        await helpers.logout(page);
        await page.close();
    });

    after(async function() {
        await chrome.close();
        db.end();
    });

    describe('Tests project creation', function() {
        it('Should create a project', async function() {
            await page.click('#navbarContent > div.navbar-nav.mr-auto > a:nth-child(2)');

            await page.waitForSelector('header > nav');
            await page.type('#project_name', NAME);
            await page.type('#project_description', DESC);
            await page.click('body > div > div > div > form > button');

            const queryResult = await helpers.databaseQuery(db,
                `SELECT * FROM project WHERE \`name\` = '${NAME}' AND  \`description\` = '${DESC}'`);

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

            assert(projectTitle === NAME);
            assert(projectDesc.includes(DESC));
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
            await page.evaluate(() => document.querySelector('#project_description').value = '');
            await page.type('#project_description', EDITED_DESC);
            await page.click('#content > div > div > div > form > button');

            await page.waitForSelector('header > nav');

            const queryResult = await helpers.databaseQuery(db,
                `SELECT * FROM project WHERE \`name\` = '${NAME}' AND  \`description\` = '${EDITED_DESC}'`);

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
            await page.click('#project-delete-confirm > div > div > div.modal-footer > a');

            await page.waitForSelector('header > nav');

            const queryResult = helpers.databaseQuery(db, `SELECT * FROM project WHERE \`name\` = '${NAME}'`);

            assert(queryResult.length === 0);
        });
    });
});
