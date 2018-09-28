# -*- coding: utf-8 -*-

import alfred
import urllib2
import json


def prompt(title="<amount> <currency> in <other currency>", subtitle="i.e. 123 EUR in USD", arg=None):
    alfred.write(alfred.xml([alfred.Item(attributes={'uid': alfred.uid(0), 'arg': arg}, title=title, subtitle=subtitle)]))

try:
    import settings
except ImportError:
    prompt(title="no settings.py file found", subtitle="Did you follow the installation instructions?")
    exit()

try:
    (value, from_curr, to, to_curr) = alfred.args()  # proper decoding and unescaping of command line arguments
except ValueError:
    prompt()
    exit()

from_curr = from_curr.upper()
to_curr = to_curr.upper()
value = float(value.replace(',', '.'))

# all possible currency ids
CURR_IDS = [u'DZD', u'NAD', u'GHS', u'EGP', u'BGN', u'PAB', u'BOB', u'DKK', u'BWP', u'LBP', u'TZS', u'VND', u'AOA', u'KHR', u'MYR', u'KYD', u'LYD', u'UAH', u'JOD', u'AWG', u'SAR', u'EUR', u'HKD', u'CHF', u'GIP', u'BYR', u'ALL', u'MRO', u'HRK', u'DJF', u'SZL', u'THB', u'XAF', u'BND', u'ISK', u'UYU', u'NIO', u'LAK', u'SYP', u'MAD', u'MZN', u'PHP', u'ZAR', u'NPR', u'ZWL', u'NGN', u'CRC', u'AED', u'EEK', u'MWK', u'LKR', u'PKR', u'HUF', u'BMD', u'LSL', u'MNT', u'AMD', u'UGX', u'QAR', u'XDR', u'JMD', u'GEL', u'SHP', u'AFN', u'SBD', u'KPW', u'TRY', u'BDT', u'YER', u'HTG', u'XOF', u'MGA', u'ANG', u'LRD', u'RWF', u'NOK', u'MOP', u'INR', u'MXN', u'CZK', u'TJS', u'BTC', u'BTN', u'COP', u'TMT', u'MUR', u'IDR', u'HNL', u'XPF', u'FJD', u'ETB', u'PEN', u'BZD', u'ILS', u'DOP', u'TWD', u'MDL', u'BSD', u'SEK', u'ZMK', u'JEP', u'AUD', u'SRD', u'CUP', u'CLF', u'BBD', u'KMF', u'KRW', u'GMD', u'VEF', u'GTQ', u'CLP', u'ZMW', u'LTL', u'CDF', u'XCD', u'KZT', u'RUB', u'XAG', u'TTD', u'OMR', u'BRL', u'MMK', u'PLN', u'PYG', u'KES', u'SVC', u'MKD', u'GBP', u'AZN', u'TOP', u'MVR', u'VUV', u'GNF', u'WST', u'IQD', u'ERN', u'BAM', u'SCR', u'CAD', u'CVE', u'KWD', u'BIF', u'PGK', u'SOS', u'SGD', u'UZS', u'STD', u'IRR', u'CNY', u'SLL', u'TND', u'GYD', u'MTL', u'NZD', u'FKP', u'LVL', u'USD', u'KGS', u'ARS', u'RON', u'RSD', u'BHD', u'JPY', u'SDG', u'XAU']

no_curr = []

if from_curr not in CURR_IDS:
    no_curr.append(from_curr)

if to_curr not in CURR_IDS:
    if len(to_curr) >= 3:  # so the message isn't shown when the second currency isn't fully typed
        no_curr.append(to_curr)
    else:
        prompt()
        exit()

if no_curr != []:
    prompt(title="No such currency: %s" % ', '.join(no_curr), subtitle='<amount> <currency> in <other currency>, i.e. 123 EUR in USD')
    exit()

try:
    rates = json.loads(urllib2.urlopen('https://openexchangerates.org/api/latest.json?app_id=%s' % settings.oer_app_id, timeout=5).read())['rates']
except urllib2.URLError:
    prompt(title="Rates source is unavailable at the moment. Please try again later.")

# in free version only available base is USD, so we have to recalculate

result = value * (rates[to_curr] / rates[from_curr])

from_string = u"%.2f %s" % (value, from_curr)
to_string = u"%.2f %s" % (result, to_curr)

prompt(title=to_string, subtitle=from_string, arg=to_string)