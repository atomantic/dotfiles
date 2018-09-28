<?php
/**
 *  Configure Alfred DevDocs Workflow
 *  Availables Commands :
 *    add : Add a doc to the workflow
 *    remove  : Remove a doc to the workflow
 *    refrehs : Refresh all installed docs databases
 *    list : List all availlables databases
 *      nuke : Reset to no docs selected
 *      addall : Add all docs in workflow
 */

namespace CFPropertyList;
require_once 'vendor/autoload.php';
require_once 'workflows.php';

class DevDocsConf {

  private static $cacheDirectory = 'cache/';

  private $commands = ['add' => 1, 'remove' => 1, 'refresh' => 1, 'list' => 1, 'alias' => 1, 'unalias' => 1, 'select' => 0, 'addAll' => 0, 'nuke' => 0];
  private $currentCmd = [];
  private $currentConfig;
  private $query;
  private $documentations;
  private $aliases;
  private $workflows;
  private $pList;
  private $rootPath;

  public function __construct($query) {
    $this->query = $query;
    $this->workflows = new \Workflows();
    $cache = $this->workflows->cache();
    if ($cache !== false) {
      self::$cacheDirectory = $cache . '/';
    }

    $this->loadAliases();
    $this->loadDocs();
    $this->parseCommand($query);
    $this->buildRootPath();
    $this->openPlist();
    $this->setDocumentations();

    if (method_exists($this, $this->currentCmd[0] . 'Cmd')) {
      $this->{$this->currentCmd[0] . 'Cmd'}();
    }
  }

  private function openPlist() {
    $this->pList = new CFPropertyList($this->rootPath . '/info.plist');
    $this->pList = $this->pList->toArray();
  }

  private function parseCommand($rawQuery) {
    $this->currentCmd = explode(' ', $rawQuery);
    if (!empty($this->currentCmd)) {
      $commandToCheck = (strpos($this->currentCmd[0], 'select') === 0) ? 'select' : $this->currentCmd[0];
      return (
        ($commandToCheck === 'select' || key_exists($commandToCheck, $this->commands)) &&
        (count($this->currentCmd) - 1 >= $this->commands[$commandToCheck])
      );
    } else {
      $this->currentCmd[0] = '';
    }
    return false;
  }

  private function buildRootPath() {
    $this->rootPath = str_replace('/scripts', '', $this->workflows->path());
  }

  private function flushToAlfred() {
    echo $this->workflows->toxml();
  }

  private function regeneratePlist() {
    $buildPlist = function ($rootPath, $documentations, $aliases) { // $documentations are used in the template
      ob_start();
      include $rootPath . '/scripts/plist.phtml';
      $fileContent = ob_get_contents();
      ob_end_clean();
      file_put_contents($rootPath . '/info.plist', $fileContent);
    };
    $buildPlist($this->rootPath, $this->currentConfig, $this->aliases);
  }

  private function setDocumentations() {
    $docFile = self::$cacheDirectory . 'docs.json';
    // Keep the docs in cache during 7 days
    if (!file_exists($docFile) || (filemtime($docFile) <= time() - 86400 * 7) || is_null(@json_decode(file_get_contents($docFile)))) {
      file_put_contents($docFile, $this->workflows->fetch('http://devdocs.io/docs/docs.json'));
    }
    $docs = @json_decode(file_get_contents($docFile));
    $this->documentations = [];
    if (is_array($docs)) {
      foreach ($docs as $doc) {
        $doc->fullName = $doc->name . (!empty($doc->version) ? ' ' . $doc->version : '');
        $this->documentations[$doc->slug] = $doc;
      }
    }
  }

  private function loadAliases() {
    $aliasesFile = $this->workflows->data() . '/aliases.json';
    $this->aliases = @json_decode(file_get_contents($aliasesFile), true);
    if (!is_array($this->aliases)) {
      $this->aliases = [];
      $this->saveAliases();
    }
  }

  private function saveAliases() {
    $aliasesFile = $this->workflows->data() . '/aliases.json';
    file_put_contents($aliasesFile, json_encode($this->aliases));
  }

  private function loadDocs() {
    $docsFile = $this->workflows->data() . '/docs.json';
    $this->currentConfig = @json_decode(file_get_contents($docsFile), true);
    if (!is_array($this->currentConfig)) {
      $this->currentConfig = [];
      $this->saveDocs();
    } else {
      foreach ($this->currentConfig as $key => $doc) {
        $this->currentConfig[$key] = (object) $doc;
      }
    }
  }

  private function saveDocs() {
    $docsFile = $this->workflows->data() . '/docs.json';
    file_put_contents($docsFile, json_encode($this->currentConfig));
  }

  private function filter($search, $collection) {
    $filtered = array_filter(
      $collection,
      function ($element) use ($search) {
        return ($search !== '') ? stripos($element->slug, $search) !== false : true;
      }
    );
    uasort($filtered, function ($elementA, $elementB) {
      return $elementA->slug >= $elementB->slug;
    });
    return $filtered;
  }

  private function flatten($arr) {
    $flattenAliases = [];
    array_walk_recursive($arr, function ($a) use (&$flattenAliases) {
      $flattenAliases[] = $a;
    });
    return $flattenAliases;
  }

  private function helpText($title, $subtitle = '') {
    $this->workflows->result(
      '',
      '',
      $title,
      $subtitle,
      '',
      'no',
      "{$this->currentCmd[1]} "
    );
  }

  private function selectAddCmd() {
    $search = (isset($this->currentCmd[1])) ? $this->currentCmd[1] : '';
    $availableDocs = array_diff_key($this->documentations, $this->currentConfig);
    $availableDocs = $this->filter($search, $availableDocs);
    foreach ($availableDocs as $doc) {
      $this->workflows->result(
        $doc->slug,
        "add " . $doc->slug,
        $doc->fullName,
        '',
        $this->getIcon($doc),
        'yes',
        $doc->slug
      );
    }
    if (count($availableDocs) === 0) {
      $this->helpText('No results.');
    }
    $this->flushToAlfred();
  }

  private function addCmd() {
    $doc = $this->documentations[$this->currentCmd[1]];
    $this->currentConfig[$this->currentCmd[1]] = $doc;
    $this->regeneratePlist();
    $this->saveDocs();
    if (!file_exists($this->rootPath . '/' . $doc->slug . '.png')) {
      @copy($this->rootPath . '/' . $doc->type . '.png', $this->rootPath . '/' . $doc->slug . '.png');
    }
    echo $this->currentCmd[1] . ' added!';
  }

  private function selectRemoveCmd() {
    $search = isset($this->currentCmd[1]) ? $this->currentCmd[1] : '';
    $availableDocs = $this->filter($search, $this->currentConfig);
    foreach ($availableDocs as $doc) {
      $this->workflows->result(
        $doc->slug,
        "remove " . $doc->slug,
        $doc->fullName,
        '',
        $this->getIcon($doc),
        'yes',
        $doc->slug
      );
    }
    if (count($availableDocs) === 0) {
      $this->helpText('No results.');
    }
    $this->flushToAlfred();
  }

  private function removeCmd() {
    unset($this->currentConfig[$this->currentCmd[1]]);
    unset($this->aliases[$this->currentCmd[1]]);
    $this->regeneratePlist();
    $this->saveDocs();
    $this->saveAliases();
    echo $this->currentCmd[1] . ' removed!';
  }

  private function selectRefreshCmd() {
    $search = isset($this->currentCmd[1]) ? $this->currentCmd[1] : '';
    $availableDocs = $this->filter($search, $this->currentConfig);

    if (count($availableDocs) === 0) {
      $this->helpText('No results.');
    } else {
      $this->workflows->result(
        'all',
        "refresh all",
        "All docs",
        '',
        $this->rootPath . '/all.png'
      );
      foreach ($availableDocs as $doc) {
        $this->workflows->result(
          $doc->slug,
          "refresh " . $doc->slug,
          $doc->fullName,
          '',
          $this->getIcon($doc),
          'yes',
          $doc->slug
        );
      }
    }
    $this->flushToAlfred();
  }

  private function refreshCmd() {
    $updateAll = ($this->currentCmd[1] === 'all');
    $docToUpdate = $updateAll ? $this->currentConfig : [$this->currentCmd[1] => $this->currentConfig[$this->currentCmd[1]]];
    foreach ($docToUpdate as $doc) {
      file_put_contents(
        self::$cacheDirectory . $doc->slug . '.json',
        $this->workflows->fetch('http://docs.devdocs.io/' . $doc->slug . '/index.json')
      );
    }
    echo (($updateAll) ? 'All data docs' : $this->currentCmd[1] . ' doc') . ' updated!';
  }

  private function listCmd() {
    $search = (isset($this->currentCmd[1])) ? $this->currentCmd[1] : '';
    $docs = $this->filter($search, $this->documentations);
    foreach ($docs as $doc) {
      $this->workflows->result(
        $doc->slug,
        json_encode($doc),
        $doc->fullName,
        (isset($this->currentConfig[$doc->slug])) ? 'Already in your doc list' : '',
        $this->getIcon($doc),
        'yes',
        $doc->slug
      );
    }
    $this->flushToAlfred();
  }

  private function getIcon($doc) {
    if (file_exists($this->rootPath . '/' . $doc->slug . '.png')) {
      return $this->rootPath . '/' . $doc->slug . '.png';
    } else {
      return $this->rootPath . '/' . $doc->type . '.png';
    }
  }

  private function addAllCmd() {
    $this->currentConfig = $this->documentations;
    $this->regeneratePlist();
    $this->saveDocs();
    echo 'All docs added!';
  }

  private function nukeCmd() {
    $this->currentConfig = [];
    $this->aliases = [];
    $this->regeneratePlist();
    $this->saveDocs();
    $this->saveAliases();
    echo 'All docs removed!';
  }

  private function selectAliasCmd() {
    if (!isset($this->currentCmd[1]) || empty($this->currentCmd[1]) ||
      !isset($this->currentCmd[2]) || empty($this->currentCmd[2])
    ) {

      $this->helpText('Enter an alias and a documentation', 'e.g.: cdoc:alias ng angular~4_typescript');
    } else {
      $alias = $this->currentCmd[1];
      $docName = $this->currentCmd[2];

      $availableDocs = $this->filter($docName, $this->currentConfig);
      foreach ($availableDocs as $doc) {
        $this->workflows->result(
          $doc->slug,
          "alias $alias $doc->slug",
          $doc->fullName,
          '',
          $this->getIcon($doc),
          'yes',
          "$alias $doc->slug"
        );
      }
      if (count($availableDocs) === 0) {
        $this->helpText('No results.');
      }
    }
    $this->flushToAlfred();
  }

  private function aliasCmd() {
    $alias = $this->currentCmd[1];
    $docName = $this->currentCmd[2];
    $docExists = isset($this->documentations[$docName]);
    if ($docExists) {
      if (!isset($this->aliases[$docName])) {
        $this->aliases[$docName] = [];
      }
      $this->aliases[$docName][] = $alias;
    }

    $this->regeneratePlist();
    $this->saveAliases();

    echo "Alias $alias to $docName added!";
  }

  private function selectUnaliasCmd() {
    $search = isset($this->currentCmd[1]) ? $this->currentCmd[1] : '';
    $aliases = $this->flatten($this->aliases);
    if (!empty($search)) {
      $aliases = array_filter(
        $aliases,
        function ($element) use ($search) {
          return ($search !== '') ? stripos($element, $search) !== false : true;
        }
      );
    }
    sort($aliases);
    foreach ($aliases as $alias) {
      $this->workflows->result(
        $alias,
        "unalias $alias",
        $alias,
        '',
        '',
        'yes',
        $alias
      );
    }
    if (count($aliases) === 0) {
      $this->helpText('No results.');
    }
    $this->flushToAlfred();
  }

  private function unaliasCmd() {
    $alias = $this->currentCmd[1];
    foreach ($this->aliases as $docName => $docAliases) {
      if (in_array($alias, $docAliases)) {
        $this->aliases[$docName] = array_diff($docAliases, [$alias]);
      }
      if (count($this->aliases[$docName]) === 0) {
        unset($this->aliases[$docName]);
      }
    }
    $this->regeneratePlist();
    $this->saveAliases();

    echo "Alias $alias to $docName removed!";
  }

}

$query = isset($query) ? $query : '';
new DevDocsConf($query);
