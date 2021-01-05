const all = [
    '--require-module ts-node/register',
    '-r bootstrapCucumber.ts',
    '-r Common/*.steps.ts',
    '-r Modules/**/*.steps.ts',
    '--format progress-bar',
    '--format json:../test-out/cucumber_report.json',
    '--publish-quiet',
];

module.exports = {
    default: all.join(' '),
};
