<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains classes that are used by the Cache API only when it is disabled.
 *
 * These classes are derivatives of other significant classes used by the Cache API customised specifically
 * to only do what is absolutely necessary when initialising and using the Cache API when its been disabled.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Required as it is needed for cache_config_disabled which extends cache_config_writer.
 */
require_once($CFG->dirroot.'/cache/locallib.php');

/**
 * The cache loader class used when the Cache has been disabled.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_disabled extends cache implements cache_loader_with_locking {

    /**
     * Constructs the cache.
     *
     * @param cache_definition $definition
     * @param cache_store $store
     * @param null $loader Unused.
     */
    public function __construct(cache_definition $definition, cache_store $store, $loader = null) {
        if ($loader instanceof cache_data_source) {
            // Set the data source to allow data sources to work when caching is entirely disabled.
            $this->set_data_source($loader);
        }

        // No other features are handled.
    }

    /**
     * Gets a key from the cache.
     *
     * @param int|string $key
     * @param int $requiredversion Minimum required version of the data or cache::VERSION_NONE
     * @param int $strictness Unused.
     * @param mixed &$actualversion If specified, will be set to the actual version number retrieved
     * @return bool
     */
    protected function get_implementation($key, int $requiredversion, int $strictness, &$actualversion = null) {
        $datasource = $this->get_datasource();
        if ($datasource !== false) {
            if ($requiredversion === cache::VERSION_NONE) {
                return $datasource->load_for_cache($key);
            } else {
                if (!$datasource instanceof cache_data_source_versionable) {
                    throw new \coding_exception('Data source is not versionable');
                }
                $result = $datasource->load_for_cache_versioned($key, $requiredversion, $actualversion);
                if ($result && $actualversion < $requiredversion) {
                    throw new \coding_exception('Data source returned outdated version');
                }
                return $result;
            }
        }
        return false;
    }

    /**
     * Gets many keys at once from the cache.
     *
     * @param array $keys
     * @param int $strictness Unused.
     * @return array
     */
    public function get_many(array $keys, $strictness = IGNORE_MISSING) {
        if ($this->get_datasource() !== false) {
            return $this->get_datasource()->load_many_for_cache($keys);
        }

        return array_combine($keys, array_fill(0, count($keys), false));
    }

    /**
     * Sets a key value pair in the cache.
     *
     * @param int|string $key Unused.
     * @param int $version Unused.
     * @param mixed $data Unused.
     * @param bool $setparents Unused.
     * @return bool
     */
    protected function set_implementation($key, int $version, $data, bool $setparents = true): bool {
        return false;
    }

    /**
     * Sets many key value pairs in the cache at once.
     *
     * @param array $keyvaluearray Unused.
     * @return int
     */
    public function set_many(array $keyvaluearray) {
        return 0;
    }

    /**
     * Deletes an item from the cache.
     *
     * @param int|string $key Unused.
     * @param bool $recurse Unused.
     * @return bool
     */
    public function delete($key, $recurse = true) {
        return false;
    }

    /**
     * Deletes many items at once from the cache.
     *
     * @param array $keys Unused.
     * @param bool $recurse Unused.
     * @return int
     */
    public function delete_many(array $keys, $recurse = true) {
        return 0;
    }

    /**
     * Checks if the cache has the requested key.
     *
     * @param int|string $key Unused.
     * @param bool $tryloadifpossible Unused.
     * @return bool
     */
    public function has($key, $tryloadifpossible = false) {
        $result = $this->get($key);

        return $result !== false;
    }

    /**
     * Checks if the cache has all of the requested keys.
     * @param array $keys Unused.
     * @return bool
     */
    public function has_all(array $keys) {
        if (!$this->get_datasource()) {
            return false;
        }

        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the cache has any of the requested keys.
     *
     * @param array $keys Unused.
     * @return bool
     */
    public function has_any(array $keys) {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Purges all items from the cache.
     *
     * @return bool
     */
    public function purge() {
        return true;
    }

    /**
     * Pretend that we got a lock to avoid errors.
     *
     * @param int|string $key
     * @return bool
     */
    public function acquire_lock($key): bool {
        return true;
    }

    /**
     * Pretend that we released a lock to avoid errors.
     *
     * @param int|string $key
     * @return bool
     */
    public function release_lock($key): bool {
        return true;
    }

    /**
     * Pretend that we have a lock to avoid errors.
     *
     * @param int|string $key
     * @return bool
     */
    public function check_lock_state($key): bool {
        return true;
    }
}

/**
 * The cache factory class used when the Cache has been disabled.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_factory_disabled extends cache_factory {
    /** @var array Array of temporary caches in use. */
    protected static $tempcaches = [];

    /**
     * Returns an instance of the cache_factor method.
     *
     * @param bool $forcereload Unused.
     * @return cache_factory
     * @throws coding_exception
     */
    public static function instance($forcereload = false) {
        throw new coding_exception('You must not call to this cache factory within your code.');
    }

    /**
     * Creates a definition instance or returns the existing one if it has already been created.
     *
     * @param string $component
     * @param string $area
     * @param string $unused Used to be datasourceaggregate but that was removed and this is now unused.
     * @return cache_definition
     */
    public function create_definition($component, $area, $unused = null) {
        $definition = parent::create_definition($component, $area);
        if ($definition->has_data_source()) {
            return $definition;
        }

        return cache_definition::load_adhoc(cache_store::MODE_REQUEST, $component, $area);
    }

    /**
     * Common public method to create a cache instance given a definition.
     *
     * @param cache_definition $definition
     * @return cache_application|cache_session|cache_store
     * @throws coding_exception
     */
    public function create_cache(cache_definition $definition) {
        $loader = null;
        if ($definition->has_data_source()) {
            $loader = $definition->get_data_source();
        }
        return new cache_disabled($definition, $this->create_dummy_store($definition), $loader);
    }

    /**
     * Creates a cache object given the parameters for a definition.
     *
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param string $unused Used to be datasourceaggregate but that was removed and this is now unused.
     * @return cache_application|cache_session|cache_request
     */
    public function create_cache_from_definition($component, $area, array $identifiers = array(), $unused = null) {
        // Temporary in-memory caches are sometimes allowed when caching is disabled.
        if (\core_cache\allow_temporary_caches::is_allowed() && !$identifiers) {
            $key = $component . '/' . $area;
            if (array_key_exists($key, self::$tempcaches)) {
                $cache = self::$tempcaches[$key];
            } else {
                $definition = $this->create_definition($component, $area);
                // The cachestore_static class returns true to all three 'SUPPORTS_' checks so it
                // can be used with all definitions.
                $store = new cachestore_static('TEMP:' . $component . '/' . $area);
                $store->initialise($definition);
                // We need to use a cache loader wrapper rather than directly returning the store,
                // or it wouldn't have support for versioning. The cache_application class is used
                // (rather than cache_request which might make more sense logically) because it
                // includes support for locking, which might be necessary for some caches.
                $cache = new cache_application($definition, $store);
                self::$tempcaches[$key] = $cache;
            }
            return $cache;
        }

        // Regular cache definitions are cached inside create_definition().  This is not the case for disabledlib.php
        // definitions as they use load_adhoc().  They are built as a new object on each call.
        // We do not need to clone the definition because we know it's new.
        $definition = $this->create_definition($component, $area);
        $definition->set_identifiers($identifiers);
        $cache = $this->create_cache($definition);
        return $cache;
    }

    /**
     * Removes all temporary caches.
     *
     * Don't call this directly - used by {@see \core_cache\allow_temporary_caches}.
     */
    public static function clear_temporary_caches(): void {
        self::$tempcaches = [];
    }

    /**
     * Creates an ad-hoc cache from the given param.
     *
     * @param int $mode
     * @param string $component
     * @param string $area
     * @param array $identifiers
     * @param array $options An array of options, available options are:
     *   - simplekeys : Set to true if the keys you will use are a-zA-Z0-9_
     *   - simpledata : Set to true if the type of the data you are going to store is scalar, or an array of scalar vars
     *   - staticacceleration : If set to true the cache will hold onto all data passing through it.
     *   - staticaccelerationsize : Sets the max size of the static acceleration array.
     * @return cache_application|cache_session|cache_request
     */
    public function create_cache_from_params($mode, $component, $area, array $identifiers = array(), array $options = array()) {
        // Regular cache definitions are cached inside create_definition().  This is not the case for disabledlib.php
        // definitions as they use load_adhoc().  They are built as a new object on each call.
        // We do not need to clone the definition because we know it's new.
        $definition = cache_definition::load_adhoc($mode, $component, $area, $options);
        $definition->set_identifiers($identifiers);
        $cache = $this->create_cache($definition);
        return $cache;
    }

    /**
     * Creates a store instance given its name and configuration.
     *
     * @param string $name Unused.
     * @param array $details Unused.
     * @param cache_definition $definition
     * @return boolean|cache_store
     */
    public function create_store_from_config($name, array $details, cache_definition $definition) {
        return $this->create_dummy_store($definition);
    }

    /**
     * Creates a cache config instance with the ability to write if required.
     *
     * @param bool $writer Unused.
     * @return cache_config_disabled|cache_config_writer
     */
    public function create_config_instance($writer = false) {
        // We are always going to use the cache_config_disabled class for all regular request.
        // However if the code has requested the writer then likely something is changing and
        // we're going to need to interact with the config.php file.
        // In this case we will still use the cache_config_writer.
        $class = 'cache_config_disabled';
        if ($writer) {
            // If the writer was requested then something is changing.
            $class = 'cache_config_writer';
        }
        if (!array_key_exists($class, $this->configs)) {
            self::set_state(self::STATE_INITIALISING);
            if ($class === 'cache_config_disabled') {
                $configuration = $class::create_default_configuration();
                $this->configs[$class] = new $class;
            } else {
                $configuration = false;
                // If we need a writer, we should get the classname from the generic factory.
                // This is so alternative classes can be used if a different writer is required.
                $this->configs[$class] = parent::get_disabled_writer();
            }
            $this->configs[$class]->load($configuration);
        }
        self::set_state(self::STATE_READY);

        // Return the instance.
        return $this->configs[$class];
    }

    /**
     * Returns true if the cache API has been disabled.
     *
     * @return bool
     */
    public function is_disabled() {
        return true;
    }
}

/**
 * The cache config class used when the Cache has been disabled.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_config_disabled extends cache_config_writer {

    /**
     * Returns an instance of the configuration writer.
     *
     * @return cache_config_disabled
     */
    public static function instance() {
        $factory = cache_factory::instance();
        return $factory->create_config_instance(true);
    }

    /**
     * Saves the current configuration.
     */
    protected function config_save() {
        // Nothing to do here.
    }

    /**
     * Generates a configuration array suitable to be written to the config file.
     *
     * @return array
     */
    protected function generate_configuration_array() {
        $configuration = array();
        $configuration['stores'] = $this->configstores;
        $configuration['modemappings'] = $this->configmodemappings;
        $configuration['definitions'] = $this->configdefinitions;
        $configuration['definitionmappings'] = $this->configdefinitionmappings;
        $configuration['locks'] = $this->configlocks;
        return $configuration;
    }

    /**
     * Adds a plugin instance.
     *
     * @param string $name Unused.
     * @param string $plugin Unused.
     * @param array $configuration Unused.
     * @return bool
     * @throws cache_exception
     */
    public function add_store_instance($name, $plugin, array $configuration = array()) {
        return false;
    }

    /**
     * Sets the mode mappings.
     *
     * @param array $modemappings Unused.
     * @return bool
     * @throws cache_exception
     */
    public function set_mode_mappings(array $modemappings) {
        return false;
    }

    /**
     * Edits a give plugin instance.
     *
     * @param string $name Unused.
     * @param string $plugin Unused.
     * @param array $configuration Unused.
     * @return bool
     * @throws cache_exception
     */
    public function edit_store_instance($name, $plugin, $configuration) {
        return false;
    }

    /**
     * Deletes a store instance.
     *
     * @param string $name Unused.
     * @return bool
     * @throws cache_exception
     */
    public function delete_store_instance($name) {
        return false;
    }

    /**
     * Creates the default configuration and saves it.
     *
     * @param bool $forcesave Ignored because we are disabled!
     * @return array
     */
    public static function create_default_configuration($forcesave = false) {
        global $CFG;

        // HACK ALERT.
        // We probably need to come up with a better way to create the default stores, or at least ensure 100% that the
        // default store plugins are protected from deletion.
        require_once($CFG->dirroot.'/cache/stores/file/lib.php');
        require_once($CFG->dirroot.'/cache/stores/session/lib.php');
        require_once($CFG->dirroot.'/cache/stores/static/lib.php');

        $writer = new self;
        $writer->configstores = array(
            'default_application' => array(
                'name' => 'default_application',
                'plugin' => 'file',
                'configuration' => array(),
                'features' => cachestore_file::get_supported_features(),
                'modes' => cache_store::MODE_APPLICATION,
                'default' => true,
            ),
            'default_session' => array(
                'name' => 'default_session',
                'plugin' => 'session',
                'configuration' => array(),
                'features' => cachestore_session::get_supported_features(),
                'modes' => cache_store::MODE_SESSION,
                'default' => true,
            ),
            'default_request' => array(
                'name' => 'default_request',
                'plugin' => 'static',
                'configuration' => array(),
                'features' => cachestore_static::get_supported_features(),
                'modes' => cache_store::MODE_REQUEST,
                'default' => true,
            )
        );
        $writer->configdefinitions = array();
        $writer->configmodemappings = array(
            array(
                'mode' => cache_store::MODE_APPLICATION,
                'store' => 'default_application',
                'sort' => -1
            ),
            array(
                'mode' => cache_store::MODE_SESSION,
                'store' => 'default_session',
                'sort' => -1
            ),
            array(
                'mode' => cache_store::MODE_REQUEST,
                'store' => 'default_request',
                'sort' => -1
            )
        );
        $writer->configlocks = array(
            'default_file_lock' => array(
                'name' => 'cachelock_file_default',
                'type' => 'cachelock_file',
                'dir' => 'filelocks',
                'default' => true
            )
        );

        return $writer->generate_configuration_array();
    }

    /**
     * Updates the definition in the configuration from those found in the cache files.
     *
     * @param bool $coreonly Unused.
     */
    public static function update_definitions($coreonly = false) {
        // Nothing to do here.
    }

    /**
     * Locates all of the definition files.
     *
     * @param bool $coreonly Unused.
     * @return array
     */
    protected static function locate_definitions($coreonly = false) {
        return array();
    }

    /**
     * Sets the mappings for a given definition.
     *
     * @param string $definition Unused.
     * @param array $mappings Unused.
     * @throws coding_exception
     */
    public function set_definition_mappings($definition, $mappings) {
        // Nothing to do here.
    }
}
