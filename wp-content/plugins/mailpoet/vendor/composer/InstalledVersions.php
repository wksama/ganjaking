<?php
 namespace Composer; if (!defined('ABSPATH')) exit; use Composer\Semver\VersionParser; class InstalledVersions { private static $installed = array ( 'root' => array ( 'pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array ( ), 'reference' => '9db7c34b794247db4c1a383b27ed38222b394362', 'name' => '__root__', ), 'versions' => array ( '__root__' => array ( 'pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array ( ), 'reference' => '9db7c34b794247db4c1a383b27ed38222b394362', ), 'mtdowling/cron-expression' => array ( 'pretty_version' => 'v1.2.3', 'version' => '1.2.3.0', 'aliases' => array ( ), 'reference' => '9be552eebcc1ceec9776378f7dcc085246cacca6', ), 'soundasleep/html2text' => array ( 'pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array ( ), 'reference' => 'd907c8fc20605135b4ac29b7a2f43d8c1c6cddb8', ), 'tburry/pquery' => array ( 'pretty_version' => 'v1.1.1', 'version' => '1.1.1.0', 'aliases' => array ( ), 'reference' => '872339ffd38d261c4417ea1855428b1b4ff9abf1', ), ), ); public static function getInstalledPackages() { return array_keys(self::$installed['versions']); } public static function isInstalled($packageName) { return isset(self::$installed['versions'][$packageName]); } public static function satisfies(VersionParser $parser, $packageName, $constraint) { $constraint = $parser->parseConstraints($constraint); $provided = $parser->parseConstraints(self::getVersionRanges($packageName)); return $provided->matches($constraint); } public static function getVersionRanges($packageName) { if (!isset(self::$installed['versions'][$packageName])) { throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed'); } $ranges = array(); if (isset(self::$installed['versions'][$packageName]['pretty_version'])) { $ranges[] = self::$installed['versions'][$packageName]['pretty_version']; } if (array_key_exists('aliases', self::$installed['versions'][$packageName])) { $ranges = array_merge($ranges, self::$installed['versions'][$packageName]['aliases']); } if (array_key_exists('replaced', self::$installed['versions'][$packageName])) { $ranges = array_merge($ranges, self::$installed['versions'][$packageName]['replaced']); } if (array_key_exists('provided', self::$installed['versions'][$packageName])) { $ranges = array_merge($ranges, self::$installed['versions'][$packageName]['provided']); } return implode(' || ', $ranges); } public static function getVersion($packageName) { if (!isset(self::$installed['versions'][$packageName])) { throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed'); } if (!isset(self::$installed['versions'][$packageName]['version'])) { return null; } return self::$installed['versions'][$packageName]['version']; } public static function getPrettyVersion($packageName) { if (!isset(self::$installed['versions'][$packageName])) { throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed'); } if (!isset(self::$installed['versions'][$packageName]['pretty_version'])) { return null; } return self::$installed['versions'][$packageName]['pretty_version']; } public static function getReference($packageName) { if (!isset(self::$installed['versions'][$packageName])) { throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed'); } if (!isset(self::$installed['versions'][$packageName]['reference'])) { return null; } return self::$installed['versions'][$packageName]['reference']; } public static function getRootPackage() { return self::$installed['root']; } public static function getRawData() { return self::$installed; } public static function reload($data) { self::$installed = $data; } } 