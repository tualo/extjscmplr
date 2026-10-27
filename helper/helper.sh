#!/bin/bash
node ./check-js-syntax.cjs ext-build/default/build/production/Tualo/


# sencha -debug app build classic > sencha-build-debug.log 2>&1
# grep -n -B 30 -A 15 -E 'C2001|unknown-uri|Closure Compiler Error' sencha-build-debug.log
