const mysql = require('mysql');
const fs = require('fs');
const path = require('path');

const LOCAL_TEST_ENV = '.env.test.local';
const TEST_ENV = '.env.test';

let credentials = null;

function getDatabaseCredentials() {
    if (credentials !== null) {
        return credentials;
    }
    const pathToLocalTestEnv = path.join(__dirname, '../..', LOCAL_TEST_ENV);
    const pathToTestEnv = path.join(__dirname, '../..', TEST_ENV);
    if (process.env.DATABASE_URL) {
        credentials = parseDatabaseCredentials(process.env.DATABASE_URL);
    } else if (fs.existsSync(pathToLocalTestEnv)) {
        credentials = getDatabaseCredentialsFromFile(pathToLocalTestEnv) ||
            getDatabaseCredentialsFromFile(pathToTestEnv);
    } else {
        credentials = getDatabaseCredentialsFromFile(pathToTestEnv);
    }
    return credentials;
}

function getDatabaseCredentialsFromFile(filename) {
    const data = fs.readFileSync(filename, {encoding: 'utf-8'});
    let credentials = null;
    data.toString().split('\n').forEach(function(line) {
        if (line.startsWith('DATABASE_URL')) {
            credentials = parseDatabaseCredentials(line.substring(line.indexOf('=') + 1));
        }
    });
    return credentials;
}

function parseDatabaseCredentials(url) {
    const reducedUrl = url.replace('mysql://', '');
    const host = reducedUrl.substring(reducedUrl.indexOf('@') + 1, reducedUrl.lastIndexOf(':')).trim();
    const user = reducedUrl.substring(0, reducedUrl.indexOf(':')).trim();
    const password = reducedUrl.substring(reducedUrl.indexOf(':') + 1, reducedUrl.indexOf('@')).trim();
    const database = reducedUrl.substring(reducedUrl.indexOf('/') + 1).trim();
    return {host, user, password, database};
}

function connectToDatabase() {
    return mysql.createConnection(getDatabaseCredentials());
}

module.exports = {
    connectToDatabase
};
