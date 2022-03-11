#!/usr/bin/env node

/**
 * External dependencies
 */
import yargs from 'yargs';
import { hideBin } from 'yargs/helpers';

/**
 * Internal dependencies
 */
import commands from './commands/index.js';

yargs(hideBin(process.argv))
	.scriptName('tools')
	.usage('$0 <cmd> [args]')
	.command(commands)
	.demandCommand()
	.help().argv;
