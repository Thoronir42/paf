import * as fs from "fs-extra";
import * as path from "path";

import {After, AfterAll, Before, BeforeAll, World} from "@cucumber/cucumber";
import {io} from "@cucumber/messages/dist/src/messages";
import Status = io.cucumber.messages.TestStepFinished.TestStepResult.Status;

import TestRuntime from "./runtime/TestRuntime";
import {getRuntimeOptions} from "./TestOptions";

const FAILING_STATES = [
    Status.UNDEFINED,
    Status.AMBIGUOUS,
    Status.FAILED,
];

async function _initDirStructure(testOutRoot: string) {
    let screenshotDir = path.join(testOutRoot, "screenshots");
    if (await fs.pathExists(screenshotDir)) {
        await fs.remove(screenshotDir);
    }
    await fs.mkdirp(screenshotDir);
}

BeforeAll(async () => {
    let runtimeOptions = getRuntimeOptions();
    await _initDirStructure(runtimeOptions.testOutDirectory);
    await TestRuntime.initialize(runtimeOptions).ensureBrowserDriverInitialized();
});

After(async function (this: World, scenario) {
    if (FAILING_STATES.includes(scenario.result.status)) {
        let id = scenario.testCaseStartedId.split('-').pop();
        let fileName = `${id}-err ${scenario.pickle.name} - ${scenario.gherkinDocument.feature.name}`;
        let screenshot = await TestRuntime.instance.captureScreenshot(fileName);
        if (screenshot) {
            await this.attach(screenshot.data, 'image/png');
        }
    }
});

AfterAll(() => TestRuntime.instance.shutdown());
