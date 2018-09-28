# -*- coding: utf-8 -*-

import sys
import os
import hmac
import base64
import struct
import hashlib
import time
import ConfigParser
import os.path
import math

import alfred


_MAX_RESULTS = 20
_CONFIG_FILE = os.path.expanduser("~") + '/.gauth'
_CONFIG_FILE_INITIAL_CONTENT = \
    """#Examples of valid configurations:
#[google - bob@gmail.com]
#secret=xxxxxxxxxxxxxxxxxx
#
#[evernote - robert]
#secret=yyyyyyyyyyyyyyyyyy
"""


def get_hotp_token(key, intervals_no):
    msg = struct.pack(">Q", intervals_no)
    h = hmac.new(key, msg, hashlib.sha1).digest()
    o = ord(h[19]) & 15
    h = (struct.unpack(">I", h[o:o + 4])[0] & 0x7fffffff) % 1000000
    return h


def get_totp_token(key):
    return get_hotp_token(key, intervals_no=int(time.time()) // 30)


def get_section_token(config, section):
    try:
        secret = config.get(section, 'secret')
    except:
        secret = None

    try:
        key = config.get(section, 'key')
    except:
        key = None

    try:
        hexkey = config.get(section, 'hexkey')
    except:
        hexkey = None

    if hexkey:
        key = hexkey.decode('hex')

    if secret:
        secret = secret.replace(' ', '')
        secret = secret.ljust(int(math.ceil(len(secret) / 16.0) * 16), '=')
        key = base64.b32decode(secret, casefold=True)

    return str(get_totp_token(key)).zfill(6)


def get_time_remaining():
    return int(30 - (time.time() % 30))


def is_secret_valid(secret):
    try:
        secret = secret.replace(' ', '')
        secret = secret.ljust(int(math.ceil(len(secret) / 16.0) * 16), '=')
        key = base64.b32decode(secret, casefold=True)
        get_totp_token(key)
    except:
        return False

    return True


def list_accounts(config, query):
    i = 0
    for section in config.sections():
        if len(query.strip()) and not query.lower() in str(section).lower():
            continue

        try:
            token = get_section_token(config, section)
            yield alfred.Item({u'uid': alfred.uid(i), u'arg': token,
                               u'autocomplete': section},
                              section,
                              'Post {} at cursor'.format(token),
                              'icon.png')
            i += 1
        except:
            pass

    if i > 0:
        # The uid for the remaining time will be the current time,
        # so it will appears always at the last position in the list
        yield alfred.Item({u'uid': time.time(), u'arg': '', u'ignore': 'yes'},
                          'Time Remaining: {}s'.format(get_time_remaining()),
                          None, 'time.png')
    else:
        yield alfred.Item({u'uid': alfred.uid(0), u'arg': '',
                           u'ignore': 'yes'},
                          "Account not found",
                          "There is no account named '" + query +
                          "' on your configuration file (~/.gauth)",
                          'warning.png')


def add_account(config, query):
    try:
        account, secret = query.split(",", 1)
        account = account.strip()
        secret = secret.strip()
    except ValueError:
        return "Invalid arguments!\n" + \
               "Please enter: account, secret."

    if not is_secret_valid(secret):
        return "Invalid secret:\n[{0}]".format(secret)

    config_file = open(_CONFIG_FILE, 'r+')
    try:
        config.add_section(account)
        config.set(account, "secret", secret)
        config.write(config_file)
    except ConfigParser.DuplicateSectionError:
        return "Account already exists:\n[{0}]".format(account)
    finally:
        config_file.close()

    return "A new account was added:\n[{0}]".format(account)


def config_file_is_empty():
    yield alfred.Item({u'uid': alfred.uid(0), u'arg': '', u'ignore': 'yes'},
                      'Google Authenticator is not yet configured',
                      "You must add your secrets to the '~/.gauth' file " +
                      "(see documentation)",
                      'warning.png')


def get_config():
    config = ConfigParser.RawConfigParser()
    config.read(os.path.expanduser(_CONFIG_FILE))
    return config


def create_config():
    with open(_CONFIG_FILE, 'w') as f:
        f.write(_CONFIG_FILE_INITIAL_CONTENT)
        f.close()


def is_command(query):
    try:
        command, rest = query.split(' ', 1)
    except ValueError:
        command = query
    command = command.strip()
    return command == 'add' or command == 'update' or command == 'remove'


def main(action, query):
    # If the configuration file doesn't exist, create an empty one
    if not os.path.isfile(_CONFIG_FILE):
        create_config()
        alfred.write(alfred.xml(config_file_is_empty()))
        return

    try:
        config = get_config()
        if not config.sections() and action != 'add':
            # If the configuration file is empty, tell the user to add secrets to it
            alfred.write(alfred.xml(config_file_is_empty()))
            return
    except Exception as e:
        alfred.write(alfred.xml([alfred.Item({u'uid': alfred.uid(0),
                                              u'arg': '', u'ignore': 'yes'},
                                             "~/.gauth: Invalid syntax",
                                             str(e).replace('\n', ' '),
                                             "error.png")]))
        sys.exit(1)

    if action == 'list' and not is_command(query):
        alfred.write(alfred.xml(list_accounts(config, query), maxresults=_MAX_RESULTS))
    elif action == 'add':
        alfred.write(add_account(config, query))


if __name__ == "__main__":
    main(action=alfred.args()[0], query=alfred.args()[1])
