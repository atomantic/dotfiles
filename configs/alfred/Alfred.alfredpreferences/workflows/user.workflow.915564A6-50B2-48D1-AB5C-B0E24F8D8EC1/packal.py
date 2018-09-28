#!/usr/bin/env python
# encoding: utf-8
#
# Copyright © 2014 deanishe@deanishe.net
#
# MIT Licence. See http://opensource.org/licenses/MIT
#
# Created on 2014-03-03
#

"""
Simple search of Packal.org for workflows based on the exported manifest.xml

Uses Alfred-Workflow library:
https://github.com/deanishe/alfred-workflow
"""

from __future__ import print_function, unicode_literals

from datetime import datetime
from operator import itemgetter
from collections import defaultdict
import subprocess

from workflow import Workflow, ICON_WARNING, ICON_INFO
from workflow.background import is_running, run_in_background

from common import (CACHE_MAXAGE,
                    STATUS_SPLITTER, STATUS_UNKNOWN, STATUS_UPDATE_AVAILABLE,
                    STATUS_UP_TO_DATE, STATUS_NOT_INSTALLED)

log = None

DELIMITER = '➣'

ICON_WFLOW = '/Applications/Alfred 2.app/Contents/Resources/workflowicon.icns'

# Icons shown in Alfred results
STATUS_SUFFIXES = {
    STATUS_SPLITTER: '❓',
    STATUS_UNKNOWN: '',
    STATUS_UPDATE_AVAILABLE: '❗',
    STATUS_UP_TO_DATE: '✅',
    STATUS_NOT_INSTALLED: '',
}


# Map status values to names
STATUS_NAMES = {
    STATUS_SPLITTER: 'STATUS_SPLITTER',
    STATUS_UNKNOWN: 'STATUS_UNKNOWN',
    STATUS_UPDATE_AVAILABLE: 'STATUS_UPDATE_AVAILABLE',
    STATUS_UP_TO_DATE: 'STATUS_UP_TO_DATE',
    STATUS_NOT_INSTALLED: 'STATUS_NOT_INSTALLED',
}

ITEM_ICONS = {
    'workflows': ICON_WFLOW,
    'tags': 'tag.png',
    'categories': 'category.png',
    'author': 'author.png'
}


__usage__ = """packal.py [options] <action> [<query>]

Usage:
    packal.py workflows [<query>]
    packal.py update
    packal.py tags [<query>]
    packal.py categories [<query>]
    packal.py versions [<query>]
    packal.py authors [<query>]
    packal.py open <bundleid>
    packal.py author-workflows <bundleid>
    packal.py ignore-author <author>
    packal.py status
"""


def run_alfred(query):
    """Call Alfred with ``query``"""
    subprocess.call([
        'osascript', '-e',
        'tell application "Alfred 2" to search "{} "'.format(query)])


def relative_time(dt):
    """Human-readable relative time, e.g. '1 hour ago'"""
    td = datetime.now() - dt
    hours = (td.days * 24.0) + (td.seconds / 3600.0)
    # log.debug('{}  -->  {:0.2f} hours ago'.format(td, hours))
    minutes = int(hours * 60)
    hours = int(hours)
    days = int(hours) / 24
    if days > 60:
        return '{:d} months ago'.format(days / 30)
    elif days > 30:
        return '1 month ago'
    elif hours > 48:
        return '{:d} days ago'.format(hours / 24)
    elif hours > 23:
        return 'yesterday'
    elif hours > 1:
        return '{:d} hours ago'.format(hours)
    elif hours == 1:
        return '1 hour ago'
    else:
        return '{:d} minutes ago'.format(minutes)


def suffix_for_status(status):
    """Return ``title`` suffix for given status"""
    suffix = STATUS_SUFFIXES.get(status)
    if not suffix:
        return ''
    return ' {}'.format(suffix)


def workflow_key(workflow):
    """Return text search key for workflow"""
    # I wish tags were in the manifest :(
    elements = [workflow['name']]
    elements.extend(workflow['tags'])
    elements.extend(workflow['categories'])
    elements.append(workflow['author'])
    return ' '.join(elements)


class GoBack(Exception):
    """Raised when Workflows should back up"""


class PackalWorkflow(object):
    """Encapsulates the Workflow"""

    def __init__(self):
        self.wf = None

    def run(self, wf):
        from docopt import docopt
        self.wf = wf

        args = docopt(__usage__, argv=self.wf.args)

        self.workflows = self.wf.cached_data('workflows', None,
                                             max_age=0)

        if self.workflows:
            log.debug('{} workflows in cache'.format(len(self.workflows)))
        else:
            log.debug('0 workflows in cache')

        # Start update scripts if cached data is too old
        if not self.wf.cached_data_fresh('workflows',
                                         max_age=CACHE_MAXAGE):
            self._update()

        # Notify user if cache is being updated
        if is_running('update'):
            self.wf.add_item('Updating from Packal…',
                             'Please try again in a second or two',
                             valid=False, icon=ICON_INFO)

        if not self.workflows:
            self.wf.send_feedback()
            return 0

        self.workflows.sort(key=itemgetter('updated'), reverse=True)

        log.debug('%d workflows found in cache', len(self.workflows))

        self.query = args.get('<query>')
        self.author = args.get('<author>')
        self.bundleid = args.get('<bundleid>')

        for key in ('tags', 'categories', 'versions', 'authors'):
            if args.get(key):
                return self._two_stage_filter(key)

        if args.get('author-workflows'):
            return self.do_author_workflows()
        elif args.get('workflows'):
            return self._filter_workflows(self.workflows, self.query)
        elif args.get('update'):
            return self.do_update()
        elif args.get('open'):
            return self.do_open()
        elif args.get('status'):
            return self.do_status()
        elif args.get('ignore-author'):
            return self.do_ignore_author()
        else:
            raise ValueError('No action specified')

    def do_update(self):
        """Force update of cached data"""
        return self._update(force=True)

    def do_open(self):
        """Open Packal workflow page in browser"""
        workflow = self._workflow_by_bundleid(self.bundleid)
        log.debug('Opening : {}'.format(workflow['url']))
        subprocess.call(['open', workflow['url']])
        return 0

    def do_author_workflows(self):
        """Tell Alfred to show workflows by the same author"""
        author = self._workflow_by_bundleid(self.bundleid)['author']
        run_alfred('packal authors {} {}'.format(author, DELIMITER))
        return 0

    def do_status(self):
        """List workflows that can be updated or installed from Packal"""
        results = []
        ignored_authors = self.wf.settings.get('ignored_authors') or []
        for workflow in self.workflows:
            if workflow['author'] in ignored_authors:
                log.debug('Workflow `{}` by ignored author. Skipping.'.format(
                          workflow['bundle']))
                continue
            if workflow['status'] == STATUS_UPDATE_AVAILABLE:
                results.append((1, workflow['updated'], workflow))
            elif workflow['status'] == STATUS_SPLITTER:
                results.append((0, workflow['updated'], workflow))
        results.sort(reverse=True)
        workflows = [t[2] for t in results]
        return self._filter_workflows(workflows, None)

    def do_ignore_author(self):
        """Add author to update blacklist.

        This hides workflows by the specified author in the update list.

        """
        ignored = self.wf.settings.get('ignored_authors') or []
        ignored.append(self.author)
        ignored = sorted(set(ignored))
        self.wf.settings['ignored_authors'] = ignored
        log.info('Adding `{}` to ignored authors'.format(self.author))
        print(self.author.encode('utf-8'))
        return 0

    def _two_stage_filter(self, key):
        """Handle queries including ``DELIMITER``

        :attr:``~PackalWorkflow.query`` is split into ``subset`` and ``query``.
        ``subset`` is the category/tag/author/OS X version name.

        If there's only a ``subset``, show all matching workflows newest first.

        If there's a ``subset`` and a ``query``, first get workflows matching
        ``subset`` then filter them by ``query``.

        If only ``query`` is provided, search the attribute specifed by ``key``

        :param key: ``tags/categories/authors/versions``. Which attribute to
        search.
        """

        valid = False

        try:
            subset, query = self._split_query(self.query)
        except GoBack:
            query = 'packal {}'.format(key)
            log.debug('Going back to : {}'.format(query))
            run_alfred(query)
            return 0
        else:
            query = query.strip()

        if key == 'authors':
            key = 'author'
            # Enable `ignore author`
            valid = True
        elif key == 'versions':
            key = 'osx'

        if subset:
            if isinstance(self.workflows[0][key], list):
                workflows = [w for w in self.workflows if subset in w[key]]
            else:
                workflows = [w for w in self.workflows if subset == w[key]]
            return self._filter_workflows(workflows, query)

        subsets = defaultdict(int)
        for workflow in self.workflows:
            if isinstance(workflow[key], list):
                for subset in workflow[key]:
                    subsets[subset] += 1
            else:
                subsets[workflow[key]] += 1

        subsets = sorted([(v, k) for (k, v) in subsets.items()], reverse=True)

        if query:
            subsets = wf.filter(query, subsets, lambda t: t[1], min_score=30)

        icon = ITEM_ICONS.get(key, ICON_WFLOW)

        if not len(subsets):
            self.wf.add_item('Nothing found', 'Try a different query',
                             valid=False, icon=ICON_WARNING)

        for count, subset in subsets:
            arg = None
            if valid:
                arg = subset
            wf.add_item(subset, '{} workflows'.format(count),
                        autocomplete='{} {} '.format(subset, DELIMITER),
                        valid=valid,
                        arg=arg,
                        icon=icon)

        wf.send_feedback()
        return 0

    def _filter_workflows(self, workflows, query):
        """Filter ``workflows`` against ``query`` and send the results
        to Alfred

        """

        if isinstance(query, basestring):
            query = query.strip()

        if query:
            workflows = self.wf.filter(query, workflows, key=workflow_key,
                                       min_score=30)
        if not len(workflows):
            self.wf.add_item('Nothing found', 'Try a different query',
                             valid=False, icon=ICON_WARNING)

        for workflow in workflows:
            log.debug('`{}` status : {}'.format(
                      workflow['name'], STATUS_NAMES[workflow['status']]))
            suffix = suffix_for_status(workflow['status'])
            title = '{}{}'.format(workflow['name'], suffix)
            subtitle = 'by {0}, updated {1}'.format(workflow['author'],
                                                    relative_time(
                                                        workflow['updated']))
            self.wf.add_item(title,
                             subtitle,
                             # Pass bundle ID to Packal.org search
                             arg=workflow['bundle'],
                             valid=True,
                             icon=ICON_WFLOW)

        self.wf.send_feedback()
        return 0

    def _workflow_by_bundleid(self, bid):
        for workflow in self.workflows:
            if workflow['bundle'] == bid:
                return workflow
        log.error('Bundle ID not found : {}'.format(self.bundleid))
        raise KeyError('Bundle ID unknown : {}'.format(bid))

    def _split_query(self, query):
        if not query or DELIMITER not in query:
            return None, query
        elif query.endswith(DELIMITER):  # trailing space deleted
            raise GoBack(query.rstrip(DELIMITER).strip())
        return [s.strip() for s in query.split(DELIMITER)]

    def _update(self, force=False):
        """Update cached data"""
        log.debug('Updating workflow lists...')
        args = ['/usr/bin/python',
                self.wf.workflowfile('update_workflows.py')]
        if force:
            args.append('--force-update')
        log.debug('update command : {}'.format(args))
        retcode = run_in_background('update', args)
        if retcode:
            log.debug('Update failed with code {}'.format(retcode))
            print('Update failed')
            return 1
        if force:
            print('Updating workflow list…'.encode('utf-8'))
        return 0

if __name__ == '__main__':
    wf = Workflow()
    log = wf.logger
    pk = PackalWorkflow()
    wf.run(pk.run)
