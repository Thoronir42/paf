import {binding, given, then, when} from "cucumber-tsflow/dist";
import BrowserHelpers from "./browser.helpers";
import {strict as assert} from "assert";
import * as path from "path";
import TestRuntime from "../runtime/TestRuntime";
import {By} from "selenium-webdriver";

@binding([BrowserHelpers])
class BrowserSteps {
    constructor(private browser: BrowserHelpers) {
    }

    @given('I am a new visitor')
    public async givenIamNewVisitor() {
        await this.browser.clearCookiesAndStorages();
        await this.browser.refresh();
    }

    @when('I open URL {string}')
    public async openUrl(url: string) {
        await this.browser.loadPage(url);
    }

    @then('I should see {string}')
    public async thenIShouldSee(text: string) {
        let el = await this.browser.findElement('//*[contains(text(),"' + text + '")]');
        assert.equal(!!el, true, "Element containing text should be found");
    }

    @then('I should not see {string}')
    public async thenIShouldNotSee(text: string) {
        try {
            await this.browser.findElement('//*[contains(text(),"' + text + '")]', 150);
        } catch (err) {
            return
        }

        throw Error("Element should not be visible");
    }

    @then('I should see {string} within {string} element')
    public async iShouldSeeWithin(text: string, parentSelector: string) {
        let parent = await this.browser.findElement(parentSelector);
        await parent.findElement(By.xpath('//*[contains(., "' +text + '")]'));
    }

    @then('I should not see {string} within {string} element')
    public async iShouldNotSeeWithin(text: string, parentSelector: string) {
        let parent = await this.browser.findElement(parentSelector);
        try {
            let el = await parent.findElement(By.xpath('*[contains(., "' +text + '")]'));
            console.log(await el.getText());
        } catch (err) {
            return;
        }
        assert.fail("Element should not be visible");
    }

    @then('I click on {string}')
    public async clickOn(text: string) {
        let selector = '//*[text()="' + text + '" or ((@type="submit" or @type="button") and @value="' + text + '")]';
        await this.browser.findElement(selector).click();
    }

    @when('I fill the field {string} with {string}')
    public async fillInFieldByName(name: string, value: string) {
        let inputEl = await this.browser.findElement('[name="' + name + '"]');

        let classList = await inputEl.getAttribute('class');
        if (classList.includes('select2-hidden-accessible')) {
            await this.selectItemOfField(name, value);
        } else {
            await inputEl.sendKeys(value);
        }
    }

    public async selectItemOfField(name: string, caption: string) {
        await this.browser.findElement('[name="' + name + '"] + .select2').click();
        await this.browser.findElement('//*[contains(@class, "select2-container--open")]//*[@class="select2-results"]//li[.="' + caption + '"]').click();
    }

    @when('I fill in a form with:')
    public async fillInForm({rawTable}: { rawTable: string[][] }) {
        for (let [name, value] of rawTable) {
            await this.fillInFieldByName(name, value);
        }
    }

    @when('I select file {string} to field {string}')
    public async selectFileForField(fileName: string, fieldName: string) {
        let filePath = path.resolve(TestRuntime.instance.options.testFilesDirectory, fileName);
        await this.fillInFieldByName(fieldName, filePath);
    }
}

export default BrowserSteps;
