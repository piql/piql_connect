#!/bin/bash
cd /app
npm install cross-env
npm install
npm rebuild node-sass
npm run $NPM_RUN
