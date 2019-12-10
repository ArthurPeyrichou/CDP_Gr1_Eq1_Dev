async function register(page, login, email, password) {
    await page.goto('http://127.0.0.1:9543/register');
    await page.waitForSelector('body > div > div > div > form');
    await page.type('#registration_name', login);
    await page.type('#registration_emailAddress', email);
    await page.type('#registration_password_first', password);
    await page.type('#registration_password_second', password);
    await page.click('body > div > div > div > form > button');

    await page.waitForSelector('header > nav');
}

async function login(page, email, password) {
    await page.goto('http://127.0.0.1:9543/login');
    await page.waitForSelector('body > div > div > div > form');
    await page.type('#email', email);
    await page.type('#password', password);
    await page.click('body > div > div > div > form > button');

    await page.waitForSelector('header > nav');
}

async function logout(page) {
    await page.click('#navbarContent > div.dropdown.px-lg-3 > a');
    await page.waitForSelector('#navbarContent > div.dropdown.px-lg-3.show > div > a', {visible: true});
    await page.click('#navbarContent > div.dropdown.px-lg-3.show > div > a');
}

async function databaseQuery(db, query) {
    return await new Promise(function(resolve, reject) {
        db.query(query, function(error, results) {
            if (error) {
                reject(error);
            }
            resolve(results);
        });
    });
}

function generateRandomString() {
    return Math.random().toString(36).substring(7);
}

module.exports = {
    register,
    login,
    logout,
    databaseQuery,
    generateRandomString
};
