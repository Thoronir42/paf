import {binding, given, then} from "cucumber-tsflow/dist";
import TestRuntime from "../../runtime/TestRuntime";
import BrowserHelpers from "../../Common/browser.helpers";
import {strict as assert} from "assert";


@binding([BrowserHelpers])
class SettingsSteps {

    constructor(private browser: BrowserHelpers) {
    }

    @given('Setting {string} is {string}')
    public async givenSettingIsString(fqn: string, value: any) {
        await this.setOptionValue(fqn, value);
    }

    @given(/Setting "([^!"]+)" is (true|false)/)
    public async givenSettingsIsBoolean(fqn: string, value: boolean | 'true' | 'false') {
        await this.setOptionValue(fqn, value === 'true');
    }

    @given(/Setting "([^!"]+)" is (\d+)/)
    public async givenSettingIsANumber(fqn: string, value: any) {
        await this.setOptionValue(fqn, Number.parseInt(value));
    }

    @then(/^Setting "([\w.]*)" should be "(truthy|falsy)"$/)
    public async thenSettingShouldBe(fqn: string, expectedValue: any) {
        let response = await TestRuntime.instance.api.get('settings/' + fqn);
        let {value} = await response.json();
        assert.equal(!!value, expectedValue === 'truthy', `Setting ${fqn} should be ${expectedValue}`);
    }

    public async setOptionValue(fqn: string, value: any) {
        let response = await TestRuntime.instance.api.put(`settings/${fqn}`, {value});

        let body = await response.json();
        if (body.value !== value) {
            throw new Error(`Failed to set value '${value}', setting returned '${body.value}'`);
        }
        if (body.value !== body.previousValue) {
            await this.browser.refresh();
        }
    }
}
