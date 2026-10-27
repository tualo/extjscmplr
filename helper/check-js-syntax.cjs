#!/usr/bin/env node
'use strict';

const { readdirSync } = require('node:fs');
const { join, resolve } = require('node:path');
const { spawnSync } = require('node:child_process');

// Aufruf: node check-js-syntax.cjs [ordner]
// Prueft rekursiv alle .js-Dateien, ohne sie auszufuehren.
function* findJavaScriptFiles(directory) {
    for (const entry of readdirSync(directory, { withFileTypes: true })) {
        const path = join(directory, entry.name);
        if (entry.isDirectory()) {
            yield* findJavaScriptFiles(path);
        } else if (entry.isFile() && entry.name.endsWith('.js')) {
            yield path;
        }
    }
}

function main() {
    const directory = resolve(process.argv[2] || '.');
    let checked = 0;
    let failed = 0;

    for (const file of findJavaScriptFiles(directory)) {
        const result = spawnSync(process.execPath, ['--check', file], {
            stdio: 'inherit'
        });

        if (result.error) {
            throw result.error;
        }
        if (result.signal) {
            throw new Error(`Pruefung abgebrochen (${result.signal}): ${file}`);
        }
        checked++;
        if (result.status !== 0) {
            failed++;
            console.error(`Syntaxpruefung fehlgeschlagen: ${file}`);
        }
    }

    console.log(`${checked} JS-Dateien geprueft, ${failed} fehlgeschlagen.`);
    process.exitCode = failed > 0 ? 1 : 0;
}

try {
    main();
} catch (error) {
    console.error(`Fehler: ${error.message}`);
    process.exitCode = 1;
}
