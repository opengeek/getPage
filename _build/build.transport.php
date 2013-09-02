<?php

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);


require_once 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
	'root' => $root,
	'build' => $root . '_build/',
	'lexicon' => $root . '_build/lexicon/',
	'properties' => $root . '_build/properties/',
	'source_core' => $root . 'core/components/getpageext',
);
unset($root);

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
//$builder->registerNamespace(PKG_NAME_LOWER,false,true);

/* create snippet object */
$snippet= $modx->newObject('modSnippet');
$snippet->set('name',PKG_NAME);
$snippet->set('description', '<b>' . PKG_VERSION . '-' . PKG_RELEASE . '</b> A generic wrapper snippet for returning paged results and navigation from snippets that return limitable collections.');
$snippet->set('category', 0);
$snippet->set('snippet', file_get_contents($sources['source_core'] . '/snippet.getpageext.php'));
$properties = include $sources['properties'].'properties.getpageext.php';
$snippet->setProperties($properties);

/* package in snippet */
$attributes= array(
	xPDOTransport::UNIQUE_KEY => 'name',
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UPDATE_OBJECT => true,
);
$vehicle = $builder->createVehicle($snippet, $attributes);
$vehicle->resolve('file',array(
	'source' => $sources['source_core'],
	'target' => "return MODX_CORE_PATH . 'components/';",
));
$builder->putVehicle($vehicle);
unset($properties,$snippet,$vehicle);

/* load lexicon strings */
//$builder->buildLexicon($sources['lexicon']);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
	'license' => file_get_contents($sources['source_core'] . '/docs/license.txt'),
	'readme' => file_get_contents($sources['source_core'] . '/docs/readme.txt'),
	'changelog' => file_get_contents($sources['source_core'] . '/docs/changelog.txt'),
));

/* zip up the package */
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

if (defined('PKG_AUTO_INSTALL') && PKG_AUTO_INSTALL) {
	$signature = $builder->getSignature();
	$sig = explode('-',$signature);
	$versionSignature = explode('.',$sig[1]);

	/* @var modTransportPackage $package */
	if (!$package = $modx->getObject('transport.modTransportPackage', array('signature' => $signature))) {
		$package = $modx->newObject('transport.modTransportPackage');
		$package->set('signature', $signature);
		$package->fromArray(array(
			'created' => date('Y-m-d h:i:s'),
			'updated' => null,
			'state' => 1,
			'workspace' => 1,
			'provider' => 0,
			'source' => $signature.'.transport.zip',
			'package_name' => $sig[0],
			'version_major' => $versionSignature[0],
			'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
			'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
		));
		if (!empty($sig[2])) {
			$r = preg_split('/([0-9]+)/',$sig[2],-1,PREG_SPLIT_DELIM_CAPTURE);
			if (is_array($r) && !empty($r)) {
				$package->set('release',$r[0]);
				$package->set('release_index',(isset($r[1]) ? $r[1] : '0'));
			} else {
				$package->set('release',$sig[2]);
			}
		}
		$package->save();
	}
	$package->install();
}

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Execution time: {$totalTime}\n");
echo '</pre>';
