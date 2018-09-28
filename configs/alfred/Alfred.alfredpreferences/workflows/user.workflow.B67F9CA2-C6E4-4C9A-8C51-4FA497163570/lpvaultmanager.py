# encoding: utf-8

from __future__ import unicode_literals

import csv
import random
import string
import StringIO
import subprocess


####################################################################
# Paths
####################################################################
DEFAULT_LPASS_PATH = '/usr/local/bin/lpass'

####################################################################
# lpass Commands
####################################################################
LPASS_COMMAND_LOGIN = 'login'
LPASS_COMMAND_DOWNLOAD = 'export'
LPASS_COMMAND_DETAILS = 'show'

####################################################################
# Password Components
####################################################################
DEFAULT_PASSWORD_NUMBER = 10
DEFAULT_PASSWORD_LENGTH = 20
CHARS_SYMBOLS = '^!\$%&/()=?{[]}+~#-_.:,;<>|\\'
CHARS_AMBIGUOUS = '0OIl1'

####################################################################
# lpass Error Messages
####################################################################
MSG_HOSTNAME_NOT_FOUND = 'Error: Could not find specified account'
MSG_MULTIPLE_MATCH = 'Multiple matches found.'

####################################################################
# Miscellaneous
####################################################################
DEFAULT_CACHE_TIMEOUT = 300


class LastPassVaultManagerError(Exception):
    """
    Raised by method :meth:`LastPassVaultManager.download_data` and
    :meth`LastPassVaultManager.get_field_value when something bad happens while
    querying LastPass.
    """


class MultipleHostnamesError(LastPassVaultManagerError):
    """
    Raised by method and :meth`LastPassVaultManager.get_field_value when
    multiple entries with the same hostname are found.
    """


class HostnameNotFoundError(LastPassVaultManagerError):
    """
    Raised by method and :meth`LastPassVaultManager.get_field_value when
    no hostname with the provided name is found.
    """


class LastPassVaultManager:
    """
    A wrapper class for interacting with a LastPass vault.
    """
    def __init__(self, lpass_path=DEFAULT_LPASS_PATH):
        """
        Initializer.
        """
        self.lpass_path = lpass_path

    def download_data(self):
        """
        Downloads data via `lpass export` and returns only the whitelisted
        fields specified below.
        """
        fields = ['url', 'hostname']
        try:
            data = subprocess.check_output(
                [self.lpass_path, LPASS_COMMAND_DOWNLOAD]
            )
            r = csv.DictReader(StringIO.StringIO(data))
            return [{k: v
                     for k, v in d.iteritems() if k in fields}
                    for d in r]
        except subprocess.CalledProcessError, e:
            raise LastPassVaultManagerError(e)

    def generate_passwords(self, number=10, length=20, upper=True, lower=True,
                           digits=True, symbols=True, avoid_ambiguous=True):
        """
        Generates a list of passwords that conform to the provided parameters.
        """
        charsets = []

        # Uppercase ASCII letters:
        if upper:
            charsets.append(string.ascii_uppercase)

        # Lowercase ASCII letters:
        if lower:
            charsets.append(string.ascii_lowercase)

        # Digits:
        if digits:
            charsets.append(string.digits)

        # Symbols
        if symbols:
            charsets.append(CHARS_SYMBOLS)

        # Combine all the charsets into one string and remove
        # ambigious chars if requested:
        chars = ''.join(charsets)
        if avoid_ambiguous:
            chars = chars.translate(CHARS_AMBIGUOUS)

        passwords = []
        for i in xrange(0, number):
            pw = ''.join(random.SystemRandom().choice(chars)
                         for _ in xrange(length))
            passwords.append(pw)

        return passwords

    def get_details(self, hostname):
        """
        Returns all the fields stored for a particular vault item.
        """
        try:
            p = subprocess.Popen(
                [self.lpass_path, 'show', hostname],
                stdout=subprocess.PIPE,
                stderr=subprocess.STDOUT
            )
            details = p.stdout.read().strip().decode('utf-8').split('\n')

            if details[0].startswith(MSG_HOSTNAME_NOT_FOUND):
                raise HostnameNotFoundError()
            elif details[0] == MSG_MULTIPLE_MATCH:
                raise MultipleHostnamesError()
            else:
                return details
        except subprocess.CalledProcessError, e:
            raise LastPassVaultManagerError(e)

    def get_field_value(self, hostname, field_name):
        """
        Returns the value for a particular field within a vault item (specified
        by the provided hostname).
        """
        try:
            field_output = subprocess.check_output(
                [self.lpass_path, 'show', '--{}'.format(field_name), hostname]
            ).split('\n')

            if field_output[0].startswith(MSG_HOSTNAME_NOT_FOUND):
                raise HostnameNotFoundError()
            elif field_output[0] == MSG_MULTIPLE_MATCH:
                raise MultipleHostnamesError()
            else:
                return field_output[0]
        except subprocess.CalledProcessError, e:
            raise LastPassVaultManagerError(e)
