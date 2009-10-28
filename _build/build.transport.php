<?php
/**
 * Component Template
 *
 * @package component-template
 * @version 1.0
 * @release ga
 * @author Test <test@test.com>
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
// get rid of time limit
set_time_limit(0);

$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    'lexicon' => $root . '_build/lexicon/',
    'assets' => $root . 'assets/',
);
unset($root);

// override with your own defines here (see build.config.sample.php)
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$name = 'getpage';
$version = '1.0.0';
$release = 'beta';

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage($name, $version, $release);
//$builder->registerNamespace('getpage',false,true);


// get the source from the actual snippet in your database OR
// manually create the object, grabbing the source from a file
$c= $modx->newObject('modSnippet');
$c->set('name', 'getPage');
$c->set('description', '<strong>' . $version . '-' . $release . '</strong> A generic wrapper snippet for returning paged results and navigation from snippets that return limitable collections.');
$c->set('category', 0);
$c->set('snippet', file_get_contents($sources['assets'] . 'snippet.getpage.php'));

// create a transport vehicle for the data object
$attributes= array(xPDOTransport::UNIQUE_KEY => 'name');
$vehicle = $builder->createVehicle($c, $attributes);
$vehicle->resolve('file',array(
    'source' => $sources['assets'] . 'getpage',
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$builder->putVehicle($vehicle);

// load lexicon strings
//$builder->buildLexicon($sources['lexicon']);

// zip up the package
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, "Package Built Successfully.");
$modx->log(modX::LOG_LEVEL_INFO, "Execution time: {$totalTime}");
exit();
