#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag | tail -n 1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf FroshViewSnapshots FroshViewSnapshots-*.zip

# Build new release
mkdir -p FroshViewSnapshots
git archive ${commit} | tar -x -C FroshViewSnapshots
composer install --no-dev -n -o -d FroshViewSnapshots
zip -r FroshViewSnapshots-${commit}.zip FroshViewSnapshots