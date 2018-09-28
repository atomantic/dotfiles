# encoding: utf-8

from __future__ import unicode_literals
from argparser import ArgParser, ArgParserError
from workflow import Workflow

import sys
import utilities

####################################################################
# Globals
####################################################################
log = None
util = None


def main(wf):
    """
    The main function, which executes the program. You know. :)
    """
    log.debug('Query arguments: {}'.format(wf.args))

    # Parse the query into a command into a query:
    try:
        ap = ArgParser(wf.args)
    except ArgParserError, e:
        log.error('Argument parsing failed: {}'.format(e))
        sys.exit(1)

    log.debug('Parsed command: {}'.format(ap.command))
    log.debug('Parsed argument: {}'.format(ap.arg))
    log.debug('Parsed delimiter: {}'.format(ap.delimiter))

    # COMMAND: Generate Passwords
    if ap.command == 'generate-passwords':
        log.debug('Executing command: generate-passwords')
        passwords = util.generate_passwords()
        for pw in passwords:
            wf.add_item(
                pw,
                'Hit ENTER to copy to clipboard.',
                arg=pw,
                valid=True,
                uid=pw
            )
        wf.send_feedback()
        sys.exit(0)
    else:
        log.error('Unknown command: {}'.format(ap.command))
        sys.exit(1)

if __name__ == '__main__':
    # Configure a Workflow class and a logger:
    wf = Workflow(libraries=['./lib'])
    log = wf.logger

    # Configure a LpvmUtilities class:
    util = utilities.LpvmUtilities(wf)

    # Run!
    sys.exit(wf.run(main))
