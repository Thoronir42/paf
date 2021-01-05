'use strict';

import firefox from 'geckodriver';
import * as selenium from 'selenium-webdriver';
import {ThenableWebDriver} from "selenium-webdriver";

/**
 * Creates a Selenium WebDriver using Firefox as the browser
 * @returns {ThenableWebDriver} selenium web driver
 */
export function createFirefoxDriver(): ThenableWebDriver {
    const driver = new selenium.Builder().withCapabilities({
        browserName: 'firefox',
        javascriptEnabled: true,
        acceptSslCerts: true,
        // 'webdriver.firefox.bin': firefox.path
    }).build();

    driver.manage().window().maximize();

    return driver;
}
