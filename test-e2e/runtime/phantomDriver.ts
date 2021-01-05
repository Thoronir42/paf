'use strict';

import phantomjs from 'phantomjs-prebuilt';
import * as selenium from 'selenium-webdriver';
import {ThenableWebDriver} from "selenium-webdriver";

/**
 * Creates a Selenium WebDriver using PhantomJS as the browser
 * @returns {ThenableWebDriver} selenium web driver
 */
export function createPhantomJsDriver(): ThenableWebDriver {
    const driver = new selenium.Builder().withCapabilities({
        browserName: 'phantomjs',
        javascriptEnabled: true,
        acceptSslCerts: true,
        'phantomjs.binary.path': phantomjs.path,
        'phantomjs.cli.args': '--ignore-ssl-errors=true'
    }).build();

    driver.manage().window().maximize();

    return driver;
}
