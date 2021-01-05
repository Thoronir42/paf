'use strict';

import chromedriver from 'chromedriver';
import * as selenium from 'selenium-webdriver';
import {ThenableWebDriver} from "selenium-webdriver";

/**
 * Creates a Selenium WebDriver using Chrome as the browser
 * @returns {ThenableWebDriver} selenium web driver
 */
export function createChromeDriver(): ThenableWebDriver {

    const driver = new selenium.Builder().withCapabilities({
        browserName: 'chrome',
        javascriptEnabled: true,
        acceptSslCerts: true,
        chromeOptions: {
            args: ['start-maximized', 'disable-extensions']
        },
        path: chromedriver.path,
    }).build();

    driver.manage().window().maximize();

    return driver;
}
