#!/bin/bash

# This is a stand in script until Armadillo is ready.

# Remove roles directory if it exists
if hash trash 2>/dev/null; then
    trash provisioning/roles
else
    rm -rf provisioning/roles
fi

# Create roles directory
mkdir -p provisioning/roles

# Clone repos
# (Note: Dependencies are currently manually tracked, Armadillo will handle this automatically)

git clone --depth 1 --branch v0.1.2 ssh://git@stash.ceh.ac.uk:7999/barc/bootstrap.git provisioning/roles/bootstrap
git clone --depth 1 --branch v0.4.3 ssh://git@stash.ceh.ac.uk:7999/barc/core.git provisioning/roles/core

git clone --depth 1 --branch v0.2.4 ssh://git@stash.ceh.ac.uk:7999/barc/php.git provisioning/roles/php

git clone --depth 1 --branch v0.1.4 ssh://git@stash.ceh.ac.uk:7999/barc/composer.git provisioning/roles/composer
