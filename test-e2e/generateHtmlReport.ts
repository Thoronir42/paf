import * as path from "path";
import * as htmlReporter from "cucumber-html-reporter";
import {getRuntimeOptions} from "./TestOptions";

const outDirectory = getRuntimeOptions().testOutDirectory;

htmlReporter.generate({
    theme: 'hierarchy',
    jsonFile: path.resolve(outDirectory, 'cucumber_report.json'),
    output: path.resolve(outDirectory, 'cucumber_report.html'),
    reportSuiteAsScenarios: true,
    launchReport: false,
    ignoreBadJsonFile: true,
})
