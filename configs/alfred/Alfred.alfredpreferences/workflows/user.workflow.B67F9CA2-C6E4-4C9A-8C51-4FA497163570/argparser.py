# encoding: utf-8

from __future__ import unicode_literals

import re


class ArgParserError(Exception):
    """
    Raised by method :meth:`ArgParser.__init__` when the user fails to pass in
    a query that adheres to `PIECES_REGEX`.
    """


class ArgParser:
    DEFAULT_DELIMITER = '>'
    PIECES_REGEX = r'([^\s]+)(?:\s?{}?\s?(.+))?'

    def __init__(self, args, delimiter=DEFAULT_DELIMITER):
        """
        Initialize an ArgParser instance and split out the passed query into
        its component parts.
        """
        try:
            # Parse the query into its component pieces and save them:
            arg_string = ' '.join(args)
            matches = re.match(self.PIECES_REGEX.format(delimiter), arg_string)
            self.command = matches.group(1)
            self.arg = matches.group(2)
            self.delimiter = delimiter
            self.query = arg_string
        except AttributeError, e:
            raise ArgParserError(e)
