import BrowserHelpers from "../../Common/browser.helpers";

class SignPage {
    constructor(private browser: BrowserHelpers) {
    }

    public async signIn(login: string, password: string) {
        await this.browser.findElement('[name="login"]').sendKeys(login);
        await this.browser.findElement('[name="password"]').sendKeys(password);
        await this.browser.findElement('[name="send"]').click();
    }
};

export default SignPage;
