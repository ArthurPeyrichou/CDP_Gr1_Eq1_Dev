async function login(page, email, password) {
    await page.goto('http://127.0.0.1:9543/login');
    await page.waitForSelector('body > div > div > div > form');
    await page.type('#email', email);
    await page.type('#password', password);
    const navigation = page.waitForNavigation();
    await page.click('body > div > div > div > form > button');

    await navigation;
}

async function logout(page) {
    await page.click('#navbarContent > div.dropdown.px-lg-3 > a');
    await page.waitForSelector('#navbarContent > div.dropdown.px-lg-3.show > div > a', {visible: true});
    await page.click('#navbarContent > div.dropdown.px-lg-3.show > div > a');
}

module.exports = {
    login,
    logout
};
