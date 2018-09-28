#!/usr/bin/env python
# encoding: utf-8
#
# Copyright © 2014 deanishe@deanishe.net
#
# MIT Licence. See http://opensource.org/licenses/MIT
#
# Created on 2014-03-21
#

"""
"""

from __future__ import print_function, unicode_literals

import re

CACHE_MAXAGE = 600

STATUS_UNKNOWN = -1  # not on Packal
STATUS_UP_TO_DATE = 0  # current version installed
STATUS_UPDATE_AVAILABLE = 1  # newer version on Packal
STATUS_SPLITTER = 2  # on Packal, but not installed from there
STATUS_NOT_INSTALLED = 3  # on Packal, but not installed


class Version(object):
    """Parse string version numbers into integers and make them comparable"""

    @classmethod
    def parse_version(self, version_string):
        """Parse a string of form `n.nn.n` into a tuple of integers"""
        components = re.split(r'\D', version_string)
        digits = []
        for s in components:
            try:
                digits.append(int(s))
            except ValueError:
                continue
        return tuple(digits)

    def __init__(self, version_string):
        self.version_string = version_string
        self.version_tuple = self.parse_version(version_string)

    def __cmp__(self, other):
        if self.version_tuple == other.version_tuple:
            return 0
        if self.version_tuple > other.version_tuple:
            return 1
        if self.version_tuple < other.version_tuple:
            return -1

    def __str__(self):
        return repr(self.version_tuple)

    __repr__ = __str__
