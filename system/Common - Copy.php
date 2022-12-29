<?php
use App\Models\Db_model;
/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */


use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\Factories;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use CodeIgniter\Cookie\Exceptions\CookieException;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Debug\Timer;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\URI;
use CodeIgniter\Model;
use CodeIgniter\Session\Session;
use CodeIgniter\Test\TestLogger;
use Config\App;
use Config\Database;
use Config\Logger;
use Config\Services;
use Config\View;
use Laminas\Escaper\Escaper;

// Services Convenience Functions

if (! function_exists('app_timezone')) {
    /**
     * Returns the timezone the application has been set to display
     * dates in. This might be different than the timezone set
     * at the server level, as you often want to stores dates in UTC
     * and convert them on the fly for the user.
     */
    function app_timezone(): string
    {
        $config = config(App::class);

        return $config->appTimezone;
    }
}

if (! function_exists('cache')) {
    /**
     * A convenience method that provides access to the Cache
     * object. If no parameter is provided, will return the object,
     * otherwise, will attempt to return the cached value.
     *
     * Examples:
     *    cache()->save('foo', 'bar');
     *    $foo = cache('bar');
     *
     * @return CacheInterface|mixed
     */
    function cache(?string $key = null)
    {
        $cache = Services::cache();

        // No params - return cache object
        if ($key === null) {
            return $cache;
        }

        // Still here? Retrieve the value.
        return $cache->get($key);
    }
}

if (! function_exists('clean_path')) {
    /**
     * A convenience method to clean paths for
     * a nicer looking output. Useful for exception
     * handling, error logging, etc.
     */
    function clean_path(string $path): string
    {
        // Resolve relative paths
        $path = realpath($path) ?: $path;

        switch (true) {
            case strpos($path, APPPATH) === 0:
                return 'APPPATH' . DIRECTORY_SEPARATOR . substr($path, strlen(APPPATH));

            case strpos($path, SYSTEMPATH) === 0:
                return 'SYSTEMPATH' . DIRECTORY_SEPARATOR . substr($path, strlen(SYSTEMPATH));

            case strpos($path, FCPATH) === 0:
                return 'FCPATH' . DIRECTORY_SEPARATOR . substr($path, strlen(FCPATH));

            case defined('VENDORPATH') && strpos($path, VENDORPATH) === 0:
                return 'VENDORPATH' . DIRECTORY_SEPARATOR . substr($path, strlen(VENDORPATH));

            case strpos($path, ROOTPATH) === 0:
                return 'ROOTPATH' . DIRECTORY_SEPARATOR . substr($path, strlen(ROOTPATH));

            default:
                return $path;
        }
    }
}

if (! function_exists('command')) {
    /**
     * Runs a single command.
     * Input expected in a single string as would
     * be used on the command line itself:
     *
     *  > command('migrate:create SomeMigration');
     *
     * @return false|string
     */
    function command(string $command)
    {
        $runner      = service('commands');
        $regexString = '([^\s]+?)(?:\s|(?<!\\\\)"|(?<!\\\\)\'|$)';
        $regexQuoted = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')';

        $args   = [];
        $length = strlen($command);
        $cursor = 0;

        /**
         * Adopted from Symfony's StringInput::tokenize() with few changes.
         *
         * @see https://github.com/symfony/symfony/blob/master/src/Symfony/Component/Console/Input/StringInput.php
         */
        while ($cursor < $length) {
            if (preg_match('/\s+/A', $command, $match, 0, $cursor)) {
                // nothing to do
            } elseif (preg_match('/' . $regexQuoted . '/A', $command, $match, 0, $cursor)) {
                $args[] = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
            } elseif (preg_match('/' . $regexString . '/A', $command, $match, 0, $cursor)) {
                $args[] = stripcslashes($match[1]);
            } else {
                // @codeCoverageIgnoreStart
                throw new InvalidArgumentException(sprintf('Unable to parse input near "... %s ...".', substr($command, $cursor, 10)));
                // @codeCoverageIgnoreEnd
            }

            $cursor += strlen($match[0]);
        }

        $command     = array_shift($args);
        $params      = [];
        $optionValue = false;

        foreach ($args as $i => $arg) {
            if (mb_strpos($arg, '-') !== 0) {
                if ($optionValue) {
                    // if this was an option value, it was already
                    // included in the previous iteration
                    $optionValue = false;
                } else {
                    // add to segments if not starting with '-'
                    // and not an option value
                    $params[] = $arg;
                }

                continue;
            }

            $arg   = ltrim($arg, '-');
            $value = null;

            if (isset($args[$i + 1]) && mb_strpos($args[$i + 1], '-') !== 0) {
                $value       = $args[$i + 1];
                $optionValue = true;
            }

            $params[$arg] = $value;
        }

        ob_start();
        $runner->run($command, $params);

        return ob_get_clean();
    }
}

if (! function_exists('config')) {
    /**
     * More simple way of getting config instances from Factories
     *
     * @return mixed
     */
    function config(string $name, bool $getShared = true)
    {
        return Factories::config($name, ['getShared' => $getShared]);
    }
}

if (! function_exists('cookie')) {
    /**
     * Simpler way to create a new Cookie instance.
     *
     * @param string $name    Name of the cookie
     * @param string $value   Value of the cookie
     * @param array  $options Array of options to be passed to the cookie
     *
     * @throws CookieException
     */
    function cookie(string $name, string $value = '', array $options = []): Cookie
    {
        return new Cookie($name, $value, $options);
    }
}

if (! function_exists('cookies')) {
    /**
     * Fetches the global CookieStore instance held by Response.
     *
     * @param Cookie[] $cookies   If getGlobal is false, this is passed to CookieStore's constructor
     * @param bool     $getGlobal If false, creates a new instance of CookieStore
     */
    function cookies(array $cookies = [], bool $getGlobal = true): CookieStore
    {
        if ($getGlobal) {
            return Services::response()->getCookieStore();
        }

        return new CookieStore($cookies);
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Returns the CSRF token name.
     * Can be used in Views when building hidden inputs manually,
     * or used in javascript vars when using APIs.
     */
    function csrf_token(): string
    {
        return Services::security()->getTokenName();
    }
}

if (! function_exists('csrf_header')) {
    /**
     * Returns the CSRF header name.
     * Can be used in Views by adding it to the meta tag
     * or used in javascript to define a header name when using APIs.
     */
    function csrf_header(): string
    {
        return Services::security()->getHeaderName();
    }
}

if (! function_exists('csrf_hash')) {
    /**
     * Returns the current hash value for the CSRF protection.
     * Can be used in Views when building hidden inputs manually,
     * or used in javascript vars for API usage.
     */
    function csrf_hash(): string
    {
        return Services::security()->getHash();
    }
}

if (! function_exists('csrf_field')) {
    /**
     * Generates a hidden input field for use within manually generated forms.
     */
    function csrf_field(?string $id = null): string
    {
        return '<input type="hidden"' . (! empty($id) ? ' id="' . esc($id, 'attr') . '"' : '') . ' name="' . csrf_token() . '" value="' . csrf_hash() . '" />';
    }
}

if (! function_exists('csrf_meta')) {
    /**
     * Generates a meta tag for use within javascript calls.
     */
    function csrf_meta(?string $id = null): string
    {
        return '<meta' . (! empty($id) ? ' id="' . esc($id, 'attr') . '"' : '') . ' name="' . csrf_header() . '" content="' . csrf_hash() . '" />';
    }
}

if (! function_exists('db_connect')) {
    /**
     * Grabs a database connection and returns it to the user.
     *
     * This is a convenience wrapper for \Config\Database::connect()
     * and supports the same parameters. Namely:
     *
     * When passing in $db, you may pass any of the following to connect:
     * - group name
     * - existing connection instance
     * - array of database configuration values
     *
     * If $getShared === false then a new connection instance will be provided,
     * otherwise it will all calls will return the same instance.
     *
     * @param array|ConnectionInterface|string|null $db
     *
     * @return BaseConnection
     */
    function db_connect($db = null, bool $getShared = true)
    {
        return Database::connect($db, $getShared);
    }
}

if (! function_exists('dd')) {
    /**
     * Prints a Kint debug report and exits.
     *
     * @param array ...$vars
     *
     * @codeCoverageIgnore Can't be tested ... exits
     */
    function dd(...$vars)
    {
        // @codeCoverageIgnoreStart
        Kint::$aliases[] = 'dd';
        Kint::dump(...$vars);

        exit;
        // @codeCoverageIgnoreEnd
    }
}

if (! function_exists('env')) {
    /**
     * Allows user to retrieve values from the environment
     * variables that have been set. Especially useful for
     * retrieving values set from the .env file for
     * use in config files.
     *
     * @param string|null $default
     *
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        // Not found? Return the default value
        if ($value === false) {
            return $default;
        }

        // Handle any boolean values
        switch (strtolower($value)) {
            case 'true':
                return true;

            case 'false':
                return false;

            case 'empty':
                return '';

            case 'null':
                return null;
        }

        return $value;
    }
}

if (! function_exists('esc')) {
    /**
     * Performs simple auto-escaping of data for security reasons.
     * Might consider making this more complex at a later date.
     *
     * If $data is a string, then it simply escapes and returns it.
     * If $data is an array, then it loops over it, escaping each
     * 'value' of the key/value pairs.
     *
     * Valid context values: html, js, css, url, attr, raw
     *
     * @param array|string $data
     * @param string       $encoding
     *
     * @throws InvalidArgumentException
     *
     * @return array|string
     */
    function esc($data, string $context = 'html', ?string $encoding = null)
    {
        if (is_array($data)) {
            foreach ($data as &$value) {
                $value = esc($value, $context);
            }
        }

        if (is_string($data)) {
            $context = strtolower($context);

            // Provide a way to NOT escape data since
            // this could be called automatically by
            // the View library.
            if (empty($context) || $context === 'raw') {
                return $data;
            }

            if (! in_array($context, ['html', 'js', 'css', 'url', 'attr'], true)) {
                throw new InvalidArgumentException('Invalid escape context provided.');
            }

            $method = $context === 'attr' ? 'escapeHtmlAttr' : 'escape' . ucfirst($context);

            static $escaper;
            if (! $escaper) {
                $escaper = new Escaper($encoding);
            }

            if ($encoding && $escaper->getEncoding() !== $encoding) {
                $escaper = new Escaper($encoding);
            }

            $data = $escaper->{$method}($data);
        }

        return $data;
    }
}

if (! function_exists('force_https')) {
    /**
     * Used to force a page to be accessed in via HTTPS.
     * Uses a standard redirect, plus will set the HSTS header
     * for modern browsers that support, which gives best
     * protection against man-in-the-middle attacks.
     *
     * @see https://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security
     *
     * @param int               $duration How long should the SSL header be set for? (in seconds)
     *                                    Defaults to 1 year.
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @throws HTTPException
     */
    function force_https(int $duration = 31536000, ?RequestInterface $request = null, ?ResponseInterface $response = null)
    {
        if ($request === null) {
            $request = Services::request(null, true);
        }
        if ($response === null) {
            $response = Services::response(null, true);
        }

        if ((ENVIRONMENT !== 'testing' && (is_cli() || $request->isSecure())) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'test')) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        // If the session status is active, we should regenerate
        // the session ID for safety sake.
        if (ENVIRONMENT !== 'testing' && session_status() === PHP_SESSION_ACTIVE) {
            // @codeCoverageIgnoreStart
            Services::session(null, true)
                ->regenerate();
            // @codeCoverageIgnoreEnd
        }

        $baseURL = config(App::class)->baseURL;

        if (strpos($baseURL, 'https://') === 0) {
            $baseURL = substr($baseURL, strlen('https://'));
        } elseif (strpos($baseURL, 'http://') === 0) {
            $baseURL = substr($baseURL, strlen('http://'));
        }

        $uri = URI::createURIString(
            'https',
            $baseURL,
            $request->getUri()->getPath(), // Absolute URIs should use a "/" for an empty path
            $request->getUri()->getQuery(),
            $request->getUri()->getFragment()
        );

        // Set an HSTS header
        $response->setHeader('Strict-Transport-Security', 'max-age=' . $duration);
        $response->redirect($uri);
        $response->sendHeaders();

        if (ENVIRONMENT !== 'testing') {
            // @codeCoverageIgnoreStart
            exit();
            // @codeCoverageIgnoreEnd
        }
    }
}

if (! function_exists('function_usable')) {
    /**
     * Function usable
     *
     * Executes a function_exists() check, and if the Suhosin PHP
     * extension is loaded - checks whether the function that is
     * checked might be disabled in there as well.
     *
     * This is useful as function_exists() will return FALSE for
     * functions disabled via the *disable_functions* php.ini
     * setting, but not for *suhosin.executor.func.blacklist* and
     * *suhosin.executor.disable_eval*. These settings will just
     * terminate script execution if a disabled function is executed.
     *
     * The above described behavior turned out to be a bug in Suhosin,
     * but even though a fix was committed for 0.9.34 on 2012-02-12,
     * that version is yet to be released. This function will therefore
     * be just temporary, but would probably be kept for a few years.
     *
     * @see   http://www.hardened-php.net/suhosin/
     *
     * @param string $functionName Function to check for
     *
     * @return bool TRUE if the function exists and is safe to call,
     *              FALSE otherwise.
     *
     * @codeCoverageIgnore This is too exotic
     */
    function function_usable(string $functionName): bool
    {
        static $_suhosin_func_blacklist;

        if (function_exists($functionName)) {
            if (! isset($_suhosin_func_blacklist)) {
                $_suhosin_func_blacklist = extension_loaded('suhosin') ? explode(',', trim(ini_get('suhosin.executor.func.blacklist'))) : [];
            }

            return ! in_array($functionName, $_suhosin_func_blacklist, true);
        }

        return false;
    }
}

if (! function_exists('helper')) {
    /**
     * Loads a helper file into memory. Supports namespaced helpers,
     * both in and out of the 'helpers' directory of a namespaced directory.
     *
     * Will load ALL helpers of the matching name, in the following order:
     *   1. app/Helpers
     *   2. {namespace}/Helpers
     *   3. system/Helpers
     *
     * @param array|string $filenames
     *
     * @throws FileNotFoundException
     */
    function helper($filenames)
    {
        static $loaded = [];

        $loader = Services::locator();

        if (! is_array($filenames)) {
            $filenames = [$filenames];
        }

        // Store a list of all files to include...
        $includes = [];

        foreach ($filenames as $filename) {
            // Store our system and application helper
            // versions so that we can control the load ordering.
            $systemHelper  = null;
            $appHelper     = null;
            $localIncludes = [];

            if (strpos($filename, '_helper') === false) {
                $filename .= '_helper';
            }

            // Check if this helper has already been loaded
            if (in_array($filename, $loaded, true)) {
                continue;
            }

            // If the file is namespaced, we'll just grab that
            // file and not search for any others
            if (strpos($filename, '\\') !== false) {
                $path = $loader->locateFile($filename, 'Helpers');

                if (empty($path)) {
                    throw FileNotFoundException::forFileNotFound($filename);
                }

                $includes[] = $path;
                $loaded[]   = $filename;
            } else {
                // No namespaces, so search in all available locations
                $paths = $loader->search('Helpers/' . $filename);

                foreach ($paths as $path) {
                    if (strpos($path, APPPATH . 'Helpers' . DIRECTORY_SEPARATOR) === 0) {
                        $appHelper = $path;
                    } elseif (strpos($path, SYSTEMPATH . 'Helpers' . DIRECTORY_SEPARATOR) === 0) {
                        $systemHelper = $path;
                    } else {
                        $localIncludes[] = $path;
                        $loaded[]        = $filename;
                    }
                }

                // App-level helpers should override all others
                if (! empty($appHelper)) {
                    $includes[] = $appHelper;
                    $loaded[]   = $filename;
                }

                // All namespaced files get added in next
                $includes = array_merge($includes, $localIncludes);

                // And the system default one should be added in last.
                if (! empty($systemHelper)) {
                    $includes[] = $systemHelper;
                    $loaded[]   = $filename;
                }
            }
        }

        // Now actually include all of the files
        foreach ($includes as $path) {
            include_once $path;
        }
    }
}

if (! function_exists('is_cli')) {
    /**
     * Check if PHP was invoked from the command line.
     *
     * @codeCoverageIgnore Cannot be tested fully as PHPUnit always run in php-cli
     */
    function is_cli(): bool
    {
        if (in_array(PHP_SAPI, ['cli', 'phpdbg'], true)) {
            return true;
        }

        // PHP_SAPI could be 'cgi-fcgi', 'fpm-fcgi'.
        // See https://github.com/codeigniter4/CodeIgniter4/pull/5393
        return ! isset($_SERVER['REMOTE_ADDR']) && ! isset($_SERVER['REQUEST_METHOD']);
    }
}

if (! function_exists('is_really_writable')) {
    /**
     * Tests for file writability
     *
     * is_writable() returns TRUE on Windows servers when you really can't write to
     * the file, based on the read-only attribute. is_writable() is also unreliable
     * on Unix servers if safe_mode is on.
     *
     * @see https://bugs.php.net/bug.php?id=54709
     *
     * @throws Exception
     *
     * @codeCoverageIgnore Not practical to test, as travis runs on linux
     */
    function is_really_writable(string $file): bool
    {
        // If we're on a Unix server we call is_writable
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }

        /* For Windows servers and safe_mode "on" installations we'll actually
         * write a file then read it. Bah...
         */
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . bin2hex(random_bytes(16));
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }

            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);

            return true;
        }

        if (! is_file($file) || ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }

        fclose($fp);

        return true;
    }
}

if (! function_exists('lang')) {
    /**
     * A convenience method to translate a string or array of them and format
     * the result with the intl extension's MessageFormatter.
     *
     * @return string
     */
    function lang(string $line, array $args = [], ?string $locale = null)
    {
        $language = Services::language();

        // Get active locale
        $activeLocale = $language->getLocale();

        if ($locale && $locale !== $activeLocale) {
            $language->setLocale($locale);
        }

        $line = $language->getLine($line, $args);

        if ($locale && $locale !== $activeLocale) {
            // Reset to active locale
            $language->setLocale($activeLocale);
        }

        return $line;
    }
}

if (! function_exists('log_message')) {
    /**
     * A convenience/compatibility method for logging events through
     * the Log system.
     *
     * Allowed log levels are:
     *  - emergency
     *  - alert
     *  - critical
     *  - error
     *  - warning
     *  - notice
     *  - info
     *  - debug
     *
     * @return mixed
     */
    function log_message(string $level, string $message, array $context = [])
    {
        // When running tests, we want to always ensure that the
        // TestLogger is running, which provides utilities for
        // for asserting that logs were called in the test code.
        if (ENVIRONMENT === 'testing') {
            $logger = new TestLogger(new Logger());

            return $logger->log($level, $message, $context);
        }

        // @codeCoverageIgnoreStart
        return Services::logger(true)
            ->log($level, $message, $context);
        // @codeCoverageIgnoreEnd
    }
}

if (! function_exists('model')) {
    /**
     * More simple way of getting model instances from Factories
     *
     * @template T of Model
     *
     * @param class-string<T> $name
     *
     * @return T
     * @phpstan-return Model
     */
    function model(string $name, bool $getShared = true, ?ConnectionInterface &$conn = null)
    {
        return Factories::models($name, ['getShared' => $getShared], $conn);
    }
}

if (! function_exists('old')) {
    /**
     * Provides access to "old input" that was set in the session
     * during a redirect()->withInput().
     *
     * @param null        $default
     * @param bool|string $escape
     *
     * @return mixed|null
     */
    function old(string $key, $default = null, $escape = 'html')
    {
        // Ensure the session is loaded
        if (session_status() === PHP_SESSION_NONE && ENVIRONMENT !== 'testing') {
            // @codeCoverageIgnoreStart
            session();
            // @codeCoverageIgnoreEnd
        }

        $request = Services::request();

        $value = $request->getOldInput($key);

        // Return the default value if nothing
        // found in the old input.
        if ($value === null) {
            return $default;
        }

        return $escape === false ? $value : esc($value, $escape);
    }
}

if (! function_exists('redirect')) {
    /**
     * Convenience method that works with the current global $request and
     * $router instances to redirect using named/reverse-routed routes
     * to determine the URL to go to.
     *
     * If more control is needed, you must use $response->redirect explicitly.
     *
     * @param string $route
     */
    function redirect(?string $route = null): RedirectResponse
    {
        $response = Services::redirectresponse(null, true);

        if (! empty($route)) {
            return $response->route($route);
        }

        return $response;
    }
}

if (! function_exists('remove_invisible_characters')) {
    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     */
    function remove_invisible_characters(string $str, bool $urlEncoded = true): string
    {
        $nonDisplayables = [];

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($urlEncoded) {
            $nonDisplayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
            $nonDisplayables[] = '/%1[0-9a-f]/';   // url encoded 16-31
        }

        $nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($nonDisplayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }
}

if (! function_exists('route_to')) {
    /**
     * Given a controller/method string and any params,
     * will attempt to build the relative URL to the
     * matching route.
     *
     * NOTE: This requires the controller/method to
     * have a route defined in the routes Config file.
     *
     * @param mixed ...$params
     *
     * @return false|string
     */
    function route_to(string $method, ...$params)
    {
        return Services::routes()->reverseRoute($method, ...$params);
    }
}

if (! function_exists('session')) {
    /**
     * A convenience method for accessing the session instance,
     * or an item that has been set in the session.
     *
     * Examples:
     *    session()->set('foo', 'bar');
     *    $foo = session('bar');
     *
     * @param string $val
     *
     * @return mixed|Session|null
     */
    function session(?string $val = null)
    {
        $session = Services::session();

        // Returning a single item?
        if (is_string($val)) {
            return $session->get($val);
        }

        return $session;
    }
}

if (! function_exists('service')) {
    /**
     * Allows cleaner access to the Services Config file.
     * Always returns a SHARED instance of the class, so
     * calling the function multiple times should always
     * return the same instance.
     *
     * These are equal:
     *  - $timer = service('timer')
     *  - $timer = \CodeIgniter\Config\Services::timer();
     *
     * @param mixed ...$params
     *
     * @return mixed
     */
    function service(string $name, ...$params)
    {
        return Services::$name(...$params);
    }
}

if (! function_exists('single_service')) {
    /**
     * Always returns a new instance of the class.
     *
     * @param mixed ...$params
     *
     * @return mixed
     */
    function single_service(string $name, ...$params)
    {
        $service = Services::serviceExists($name);

        if ($service === null) {
            // The service is not defined anywhere so just return.
            return null;
        }

        $method = new ReflectionMethod($service, $name);
        $count  = $method->getNumberOfParameters();
        $mParam = $method->getParameters();
        $params = $params ?? [];

        if ($count === 1) {
            // This service needs only one argument, which is the shared
            // instance flag, so let's wrap up and pass false here.
            return $service::$name(false);
        }

        // Fill in the params with the defaults, but stop before the last
        for ($startIndex = count($params); $startIndex <= $count - 2; $startIndex++) {
            $params[$startIndex] = $mParam[$startIndex]->getDefaultValue();
        }

        // Ensure the last argument will not create a shared instance
        $params[$count - 1] = false;

        return $service::$name(...$params);
    }
}

if (! function_exists('slash_item')) {
    // Unlike CI3, this function is placed here because
    // it's not a config, or part of a config.
    /**
     * Fetch a config file item with slash appended (if not empty)
     *
     * @param string $item Config item name
     *
     * @return string|null The configuration item or NULL if
     *                     the item doesn't exist
     */
    function slash_item(string $item): ?string
    {
        $config     = config(App::class);
        $configItem = $config->{$item};

        if (! isset($configItem) || empty(trim($configItem))) {
            return $configItem;
        }

        return rtrim($configItem, '/') . '/';
    }
}

if (! function_exists('stringify_attributes')) {
    /**
     * Stringify attributes for use in HTML tags.
     *
     * Helper function used to convert a string, array, or object
     * of attributes to a string.
     *
     * @param mixed $attributes string, array, object
     */
    function stringify_attributes($attributes, bool $js = false): string
    {
        $atts = '';

        if (empty($attributes)) {
            return $atts;
        }

        if (is_string($attributes)) {
            return ' ' . $attributes;
        }

        $attributes = (array) $attributes;

        foreach ($attributes as $key => $val) {
            $atts .= ($js) ? $key . '=' . esc($val, 'js') . ',' : ' ' . $key . '="' . esc($val) . '"';
        }

        return rtrim($atts, ',');
    }
}

if (! function_exists('timer')) {
    /**
     * A convenience method for working with the timer.
     * If no parameter is passed, it will return the timer instance,
     * otherwise will start or stop the timer intelligently.
     *
     * @return mixed|Timer
     */
    function timer(?string $name = null)
    {
        $timer = Services::timer();

        if (empty($name)) {
            return $timer;
        }

        if ($timer->has($name)) {
            return $timer->stop($name);
        }

        return $timer->start($name);
    }
}

if (! function_exists('trace')) {
    /**
     * Provides a backtrace to the current execution point, from Kint.
     */
    function trace()
    {
        Kint::$aliases[] = 'trace';
        Kint::trace();
    }
}

if (! function_exists('view')) {
    /**
     * Grabs the current RendererInterface-compatible class
     * and tells it to render the specified view. Simply provides
     * a convenience method that can be used in Controllers,
     * libraries, and routed closures.
     *
     * NOTE: Does not provide any escaping of the data, so that must
     * all be handled manually by the developer.
     *
     * @param array $options Unused - reserved for third-party extensions.
     */
    function view(string $name, array $data = [], array $options = []): string
    {
        /**
         * @var CodeIgniter\View\View $renderer
         */
        $renderer = Services::renderer();

        $saveData = config(View::class)->saveData;

        if (array_key_exists('saveData', $options)) {
            $saveData = (bool) $options['saveData'];
            unset($options['saveData']);
        }

        return $renderer->setData($data, 'raw')->render($name, $options, $saveData);
    }
}

if (! function_exists('view_cell')) {
    /**
     * View cells are used within views to insert HTML chunks that are managed
     * by other classes.
     *
     * @param null $params
     *
     * @throws ReflectionException
     */
    function view_cell(string $library, $params = null, int $ttl = 0, ?string $cacheName = null): string
    {
        return Services::viewcell()
            ->render($library, $params, $ttl, $cacheName);
    }
}

/**
 * These helpers come from Laravel so will not be
 * re-tested and can be ignored safely.
 *
 * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/helpers.php
 */
if (! function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param object|string $class
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (! function_exists('class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     *
     * @param object|string $class
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    function class_uses_recursive($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (! function_exists('trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param string $trait
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait) ?: [];

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}



function arr($arr = array())
{
	echo '<pre>';
	print_r($arr);
}

function getDb() {
	
	return new Db_model();	
}

function getFront_model() {
	
	require_once 'app/models/Front_model.php';
	
	return new Front_model();	
}

function getMenuModel() {
	require_once 'app/models/admin/Menu_model.php';
	
	return new Menu_model();	
}
//
//TO Check Email
function validEmail_( $email ) {

	if( preg_match( '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email ) )
		return true;

	return false;
}
//TO Check Password
function vailPassword_( $password ) {

	if( preg_match( '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $password ) )
		return true;

	return false;
}
//
//
function getSkipId( $table_name, $pri_key, $skip = array() ) {

	$dao = getDb();

	$keep[] = "
		SELECT
			". $pri_key .",
			". $pri_key ." + 1 as new_id
		FROM ". $table_name ."
	";

	$keep[] = "
		SELECT
			0,
			0 + 1 as new_id
	";

	foreach ( $skip as $ka => $va ) {

		$keep[] = "
			SELECT
				". $va .",
				". $va ." + 1 as new_id
		";
	}

	$sql = "

		SELECT
			MIN( new_tb.new_id ) as new_id

		FROM (". implode( ' UNION ', $keep ) .") as new_tb

		WHERE new_tb.new_id NOT IN (
			SELECT
				". $pri_key ."
			FROM ". $table_name ."
		)
		AND new_tb.new_id NOT IN ('". implode( "','", $skip ) ."')
	";
	
	//arr( $sql );

	$res = $dao->fetch( $sql );

	return $res->new_id;

}

//
//selectFDate( $param )
/*
$param['tbName'] = $va->tbName;
$param['due_date'] = $va->extra_due_date;
$param['view'] = 'getFdate';
$param['parent_id'] = $va->company_id;
$param['company_id'] = $va->company_id;
$param['admin_company_id'] = $va->admin_company_id;
*/
function selectFDate( $param ) {
	
	$dao = getDb();

	if( isset( $param['view'] ) && $param['view'] == 'getFdate' ) {
		
		$tbName = $param['tbName'];
		
		if( $tbName == 'ap' ) {
			
			$tbName = 'erp_purchase_inv';
		}
		else if( $tbName == 'ar' ) {
			
			$tbName = 'erp_sale_inv';

		}
		
		$due_day = $param['due_date'];
		
		if( in_array( $tbName, array(  'erp_sale_inv' ) )   ) {
		
			$sql = "
				SELECT
					g.select_date_week
				FROM erp_company_group g
				INNER JOIN erp_company c ON g.company_group_id = c.company_group_id
				WHERE c.company_id = ". $param['company_id'] ."
			";

			$res = $dao->fetch( $sql );
			
		}
		else {
			
			
			$sql = "
				SELECT
					select_date_week
				FROM erp_company
				WHERE company_id = ". $param['company_id'] ."
			";

			$res = $dao->fetch( $sql );
			
		}

		if( $res ) {
			
			$json_decode = json_decode( $res->select_date_week );
			
			if( !empty( $json_decode->$tbName ) ) {
			
				$select_date_week = $json_decode->$tbName;
				
			}

		}
		else if( in_array( $tbName, array( 'erp_purchase_inv' ) ) ) {
		
			$sql = "
				SELECT
					select_date_week
				FROM admin_company
				WHERE company_id = 1
			";

			$res = $dao->fetch( $sql );

			if( $res ) {
				
				$json_decode = json_decode( $res->select_date_week );
				if( !empty( $json_decode->$tbName ) ) 
					$select_date_week = $json_decode->$tbName;
			}
		}
		else {
			
			$sql = "
				SELECT
					select_date_week
				FROM erp_company
				WHERE company_id = ". $param['company_id'] ."
			";

			$res = $dao->fetch( $sql );
			
			if( $res ) {
				
				$json_decode = json_decode( $res->select_date_week );
				if( !empty( $json_decode->$tbName ) ) 
					$select_date_week = $json_decode->$tbName;
			}
			
		}
	
		
		if( empty( $select_date_week ) ) {
			
			return array( 'f_date' => $due_day, 'f_date_remark' => 'ddds' );
			
		}
		
		foreach( $select_date_week as $ka => $va ) {
			
			$va = trim( $va );

			if( is_numeric( $va ) ) {

				$va = makeFrontZero( $va, 2 );

				$sql = "
					SELECT
						IF( new_tb.select_date_week >= new_tb.due_date, new_tb.select_date_week, new_tb.select_date_week_next_month ) as keep_date

					FROM (
						SELECT
							'". $due_day ."' as due_date,
							DATE_FORMAT( ADDDATE( '". $due_day ."', INTERVAL 0 day ), '%Y-%m-". $va ."' ) as select_date_week,
							DATE_FORMAT( ADDDATE( '". $due_day ."', INTERVAL 1 month ), '%Y-%m-". $va ."' ) as select_date_week_next_month

					) as new_tb

				";

				$res = $dao->fetch( $sql );

				$keep[$va] = "
					SELECT
						'". $res->keep_date ."' as f_date,
						'". $va ."' as f_date_remark
				";

			}
			else {

				$ex = explode( '-', trim( $va ) );

				$expectDayOfWeek = $ex[0];

				$expectOrder = $ex[1];


				if( $expectOrder == 'last' ) {


					for( $m = 0; $m <= 3; ++$m ) {

						$break = NULL;

						$sql = "
							SELECT
								LAST_DAY( ADDDATE( '". $due_day ."', INTERVAL ". $m ." month ) ) as on_day
						";

						$on_day = $dao->fetch( $sql )->on_day;

						for( $i = 1; $i <= 100; ++$i ) {

							$sql = "
								SELECT
									DATE_FORMAT( ADDDATE( '". $on_day ."', INTERVAL 0 day ), '%Y-%m-%d' ) as keep_date,
									DATE_FORMAT( ADDDATE( '". $on_day ."', INTERVAL -1 day ), '%Y-%m-%d' ) as prev_date,
									DATE_FORMAT( ADDDATE( '". $on_day ."', INTERVAL 0 day ), '%W' ) as w
							";

							$res = $dao->fetch( $sql );

							if( $res->w == $expectDayOfWeek ) {

								if( $res->keep_date >= $due_day ) {

									///$keep[$va] = $res->keep_date;


									$keep[$va] = "
										SELECT
											'". $res->keep_date ."' as f_date,
											'". $va ."' as f_date_remark
									";
									$break = true;
								}


								break;

							}

							$on_day = $res->prev_date;
						}


						if( !empty( $break ) ) {
							break;

						}

					}


				}
				else {
 
					$keepD = array();

					$sql = "
						SELECT
							DATE_FORMAT( ADDDATE( '". $due_day ."', INTERVAL 0 day ), '%Y-%m-01' ) as on_day
					";

					$on_day = $dao->fetch( $sql )->on_day;

					for( $i = 1; $i <= 100; ++$i ) {

						$sql = "
							SELECT
								DATE_FORMAT( ADDDATE( '". $on_day ."', INTERVAL 0 day ), '%Y-%m-%d' ) as keep_date,
								DATE_FORMAT( ADDDATE( '". $on_day ."', INTERVAL 1 day ), '%Y-%m-%d' ) as next_date,
								DATE_FORMAT( ADDDATE( '". $on_day ."', INTERVAL 0 day ), '%W-%m' ) as w

						";

						$res = $dao->fetch( $sql );

						$keepD[$res->w][] = $res->keep_date;

						$ex = explode( '-', $res->w );


						if( $ex[0] == $expectDayOfWeek ) {

							if( count( $keepD[$res->w] ) == $expectOrder ) {

								 if( $res->keep_date >= $due_day ) {

									// $keep[$va] = $res->keep_date;

									$keep[$va] = "
										SELECT
											'". $res->keep_date ."' as f_date,
											'". $va ."' as f_date_remark
									";


									 break;
								 }
							}
						}

						$on_day = $res->next_date;

					}
				}

			}

		}

		if( !empty( $keep ) ) {
			
			$sql = "
			
				SELECT 
					new_tb.*
				FROM (
				". implode( ' UNION ', $keep ) ."
				) as new_tb	
				ORDER BY new_tb.f_date ASC 
				LIMIT 0, 1
			";
			
			foreach( $dao->fetchAll( $sql ) as $kr => $vr  ) {
				
				return array( 'f_date' => $vr->f_date, 'f_date_remark' => $vr->f_date_remark );
			}
		}
		
		return array( 'f_date' => $due_day, 'f_date_remark' => 'ใช้ due_date' );

	}
	else if( isset( $param['view'] ) && $param['view'] == 'form' ) {

		$test = new stdClass;
		if( !empty( $param['data']['rows'][0]->select_date_week ) ) {
			
			$test = json_decode( $param['data']['rows'][0]->select_date_week );
		}

		$dayWeek = '[{"keep_date":"Monday"},{"keep_date":"Tuesday"},{"keep_date":"Wednesday"},{"keep_date":"Thursday"},{"keep_date":"Friday"},{"keep_date":"Saturday"},{"keep_date":"Sunday"}]';


		$datas = array( 1, 2, 3, 4, 'last' );
		//echo $param['selectTables'];
		$selectTables = empty( $param['selectTables'] )? array( 'erp_sale_inv', 'erp_purchase_inv' ): $param['selectTables'];
		
		foreach( $selectTables as $ko => $vo ) {
			
			if( empty( $test->$vo ) )
				$test->$vo = array();
			
			$trs = array();
			
			foreach( $datas as $kg => $vg ) {

				$tds = array();

				foreach( json_decode( $dayWeek ) as $kt => $vt ) {
					
					$label = $vt->keep_date .' '. $vg .'';
					
					$tds[] = '
						<td class="C select-date-week">
							<input '. select( 1, in_array( $vt->keep_date .'-'. $vg, $test->$vo ), ' checked ', '' ) .' type="checkbox" name="select-date-week['. $vo .'][]" value="'. $vt->keep_date .'-'. $vg .'" />

							'. $label .'


					</td>';
				}

				$trs[] = '<tr>'. implode( '', $tds ) .'</tr>';

			}
			
		
			$tds = array();

			for( $d = 1; $d <= 31; ++$d ) {

				$tds[] = '
					<td class="C select-date-week">
						<input '. select( 1, in_array( $d, $test->$vo ), ' checked ', '' ) .' type="checkbox" name="select-date-week['. $vo .'][]" value="'. $d .'" />

					ทุกวันที่ '. $d .'ของเดือน

				</td>';

				if( count( $tds ) == 7 || $d == 31 ) {

					$trs[] = '<tr>'. implode( '', $tds ) .'</tr>';
					$tds = array();
				}
			}
			
			$title = '';
			
			if( $vo == 'erp_sale_inv' ) {
				$title = 'การรับเงินจากลูกหนี้';
			}
			else {
				$title = 'การจ่ายเงินให้เจ้าหนี้';
				
			}
			
			$tables[] = '
				<b>'. $title .'</b>
				<table class="flexme3">'. implode( '', $trs ) .'</table>';
		}
		
		
		return '
			<form action="'. setLink( 'ajax/saveFDate' ) .'" enctype="multipart/form-data">
				<input type="submit" value="save">
				<input type="hidden" name="ajax" value="1">
				<input type="hidden" name="use_tb" value="'. $param['use_tb'] .'">
				<input type="hidden" name="company_id" value="'. $param['parent_id'] .'">
				
				'. implode( '<br>', $tables ) .'
			</form>
			<script>
			$( function() {


			});
			</script>
		';
	}
	 
}







//
//
function allFiledOption( $tb_name, $name = 'request[3][pri_key]', $def_val = NULL, $class = NULL ) {
//echo $def_val;
	$dao = getDb();

	$option = '<option value="">เลือก</option>';
	foreach ( $dao->showColumns( $tb_name ) as $v ) {
		$option .= '<option value="'. $v .'" '. select( $def_val, $v ) .' >'. $v .'</option>';
	}

	if ( !empty( $class ) ) {
		$class = 'class="'. $class .'"';
    }


	return '<select '. $class .' name="'. $name .'">'. $option .'</select>';
}

//
//
function option( $name, $def_val = NULL, $array = array( 'Yes' => 1, 'No' => 0 ), $class = NULL ) {

	$option = '<option value="">เลือก</option>';
	foreach ( $array as $k => $v ) {
		$option .= '<option value="'. $v .'" '. select( $def_val, $v, 'selected' ) .'>'. $k .'</option>';
	}

	return $html = '<select name="'. $name .'" class="ooo" >'. $option .'</select>';

}
//
//
function inputFormat( $name, $val ) {

	$array = array( 'email', 'number', 'money', 'date', 'short_date', 'time', 'help', 'percent', 'str_percent', 'checkbox', 'password', 'forum', 'csv', 'comment', 'auto_number' );

	$option = '<option value="">เลือก</option>';
	foreach ( $array as $k => $v ) {
		$option .= '<option value="'. $v .'" '. select( $val, $v, 'selected' ) .'>'. $v .'</option>';
	}

	return '<select name="'. $name .'" class="input-format-option" >'. $option .'</select>';

}

//
//
function convertObJectToArray( $data ) {

	if ( empty( $data ) ) {
		return array();
	}

	$keep = array();
	foreach ( $data as $ka => $va ) {
		$keep[$ka] = $va;
	}

	return $keep;
}

//
//
function getLink____( $model_id, $sub = array(), $get = array() ) {

	$dao = getDb();
	
	
	$ex = explode( '/', uri_string() );

 
	if( isset( $ex[2] ) ) {
		unset( $ex[2] );
	}
	
	//arr( $ex );

	$sql = "
		SELECT *
		FROM admin_model
		WHERE model_id = ". $model_id ."";

	//$data = $dao->fetch( $sql );
	
	foreach( $dao->fetchAll( $sql ) as $ka => $data ) {
		
		$sub_model = '';

		if ( !empty( $sub ) )
			$sub_model = '/' . implode( '/', $sub );
		
		$link = implode( '/', $ex ) . '/' . $data->model_alias . $sub_model;

		if ( !empty( $get ) )
			$link .= '?' . http_build_query( $get, 'flags_' );

		return base_url() . $link;
	}


}



//
//
function getAlignment( $va ) {
	if ( in_array( $va['inputformat'], array( 'number', 'money' ) ) ) {
		$va['a'] = 'R';


	}

	return $va['a'];

}

//
//
function genCond_( $sql, $replace = array(), $sort = NULL, $tbMain = NULL, $prefix = NULL ) {

	$defConditions = array( 'WHERE', 'HAVING' );

	$keep = array();
	foreach( $defConditions as $kr => $vr ) {
		$keep['['. $vr .']'] = '';
	}
	//arr( $_SESSION['u'] );
	if( !empty( $tbMain ) ) {
		
		if( empty( $prefix ) ) {
			$prefix = $tbMain . '.';
		}
		else {
			
			$prefix .= '.';
		}
		
		$replace['WHERE'][] = ''. $prefix .'admin_company_id = '. $_SESSION['u']->user_company_id .'';
		
	}

	foreach( $replace as $kr => $vr ) {

		if( in_array( $kr, $defConditions ) ) {

			if( !empty( $vr ) ) {
				$keep['['. $kr .']'] = $kr ." " . implode( ' AND ', $vr );
			}
			else {

				$keep['['. $kr .']'] = '';
			}
		}
		else {
			$keep['['. $kr .']'] = $vr;

		}
	}
	if( empty( $sort ) ) {
		
		$keep['[sort]'] = NULL;
	}
	return str_replace( array_keys( $keep ), $keep, $sql );
}


 


//
//
//$param['data']['doc_date'] = '1980-01-01'
//$param['gl_ids'] = array( 1, 2 , 4)
//$param['main_data_before']->doc_date
function updateGlTrn( $param ) {
	
	//arr( $param );
	$dao = getDb();
	
	$dates[] = $param['data']['doc_date'];

	if( isset( $param['main_data_before']->doc_date ) ) {

		$dates[] = $param['main_data_before']->doc_date;
	}

	$doc_date = MIN( $dates );
	
//	$doc_date = '1980-11-01';
	

	$sql = "
		SELECT
			*
		FROM erp_gl_trn_dt
		[WHERE]
	
		ORDER BY
			doc_date ASC,
			doc_no ASC,
			admin_company_id ASC,
			id ASC 
	";

	$filters = array();
	$filters['WHERE'][] = "doc_date >= '". $doc_date ."'";
	
	if( !empty( $param['gl_ids'] ) ) {
		
		$filters['WHERE'][] = "gl_id IN ( ". implode( ', ', $param['gl_ids'] ) ." )";
	}
	
	$sql = genCond_( $sql, $filters );
	
 
	$sqlUnion = array();
	//
	//
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
	//arr( $va );
		$sql = "
			SELECT
				gl_gr.credit as credit,
				gl_gr.debit as debit,
				CONCAT( gl.gl_code, ' ', gl.gl_name ) as gl_name
			FROM erp_gl gl
			LEFT JOIN erp_gl_group gl_gr ON gl.gl_group_id = gl_gr.id
			WHERE gl.id = ". $va->gl_id ."
		";
		
		$gl[$va->gl_id] = $dao->fetch( $sql );

		if( empty( $gl[$va->gl_id] ) ) {

			continue;
		}
		
		$gName = $va->gl_id . '-' .$va->admin_company_id;
		
	
		if( !isset( $balance[$gName] ) ) {

			$sql = "

				SELECT
					dt.*
				FROM erp_gl_trn_dt dt
				WHERE CONCAT( dt.gl_id, '-', dt.admin_company_id ) = ". $gName ."
				AND dt.doc_date < '". $doc_date ."'
				ORDER BY order_number DESC
				LIMIT 0, 1
			";

			$balance[$gName] = new stdClass;
			$balance[$gName]->credit_debit_bf = 0;
			$balance[$gName]->order_number = 0;
			$balance[$gName]->credit_debit_bal = 0;

			foreach( $dao->fetchAll( $sql ) as $kb => $vb ) {
				$balance[$gName] = $vb;
			}
		}

		$balance[$gName]->order_number += 1;

		$sum_credit_debit = ( $va->credit * $gl[$va->gl_id]->credit ) + ( $va->debit * $gl[$va->gl_id]->debit );

		$balance[$gName]->credit_debit_bal += $sum_credit_debit;

		if( $va->insert_type == 'manual' ) {

			$id = $va->id; 
			
			$his_name = $va->doc_no .'-'. $gName .'-'. $va->insert_type . '-'. $id;
		}
		else {

			$his_name = $va->doc_no .'-'. $gName .'-'. $va->insert_type . '';
		}


		$sqlUnion[] = "
			SELECT 
				". $gl[$va->gl_id]->credit ." as factor_credit,
				". $gl[$va->gl_id]->debit ." as factor_debit,
				'". $his_name ."' as his_name,
				". $balance[$gName]->credit_debit_bf ." as credit_debit_bf,
				". $balance[$gName]->order_number ." as order_number,
				". $balance[$gName]->credit_debit_bal ." as credit_debit_bal,
				". $sum_credit_debit ." as sum_credit_debit,
				'". $gl[$va->gl_id]->gl_name ."' as gl_name,
				". $va->id ." as id
				
		";
		
		$keepIds[] = $va->id;
		
		
		if( count( $sqlUnion ) > 300 ) {
			
			$sql = "
				UPDATE erp_gl_trn_dt gl 
				INNER JOIN  (
				". implode( ' UNION ', $sqlUnion ) ."
				) as new_tb ON gl.id = new_tb.id
				SET
					gl.factor_credit = new_tb.factor_credit,
					gl.factor_debit = new_tb.factor_debit,
					gl.his_name = new_tb.his_name,
					gl.credit_debit_bf = new_tb.credit_debit_bf,
					gl.order_number = new_tb.order_number,
					gl.credit_debit_bal = new_tb.credit_debit_bal,
					gl.sum_credit_debit = new_tb.sum_credit_debit,
					gl.gl_name = new_tb.gl_name
				
			";


			$dao->execDatas( $sql );
		
			$sqlUnion = array();
			$keepIds = array();
			
			
		}

		$balance[$gName]->credit_debit_bf = $sum_credit_debit;
	}
	
	
	if( count( $sqlUnion ) > 0 ) {
		
		$sql = "
				UPDATE erp_gl_trn_dt gl 
				INNER JOIN  (
				". implode( ' UNION ', $sqlUnion ) ."
				) as new_tb ON gl.id = new_tb.id
				SET
					gl.factor_credit = new_tb.factor_credit,
					gl.factor_debit = new_tb.factor_debit,
					gl.his_name = new_tb.his_name,
					gl.credit_debit_bf = new_tb.credit_debit_bf,
					gl.order_number = new_tb.order_number,
					gl.credit_debit_bal = new_tb.credit_debit_bal,
					gl.sum_credit_debit = new_tb.sum_credit_debit,
					gl.gl_name = new_tb.gl_name
				
			
		";

 
		$dao->execDatas( $sql );
		
		$sqlUnion = array();
		$keepIds = array();
		
		
	}

}





//
//
function getIcon( $type ) {

	$icon['gminus'] = '<span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>';
	$icon['gplus'] = '<i class="fa fa-plus-circle"></i>';
	$icon['gsave'] = '<span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span>';
	$icon['gedit'] = '<i class="fa fa-edit"></i>';
	$icon['gcopy'] = '<span class="glyphicon glyphicon-copy" aria-hidden="true"></span>';
	$icon['gdel'] = '<i class="fa fa-trash-o"></i>';
	$icon['gnew'] = '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>';

	$icon['add'] = '<i class="fa fa-plus-circle"></i>';

	$icon['edit'] = '<i class="fa fa-edit"></i>';

	$icon['delete'] = '<i class="fa fa-trash-o"></i>';


	$icon['save'] = '<i class="fa fa-save"></i>';


	$icon['cancel'] = '<i class="fa fa-arrow-left" aria-hidden="true"></i>';

	$icon['first'] = '<<';

	$icon['prev'] = '<';

	$icon['next'] = '>';

	$icon['last'] = '>>';

	$icon['search'] = '<i class="fa fa-search"></i>';

	$icon['shop'] = '<svg viewBox="0 0 512 512"><path d="m448 32l-384 0c-16 0-32 16-32 32l0 384c0 16 16 32 32 32l384 0c16 0 32-16 32-32l0-384c0-16-16-32-32-32z m-32 368c0 8-8 16-16 16l-288 0c-9 0-16-6-16-16l0-288c0-8 8-16 16-16l288 0c8 0 16 8 16 16z m-160-176l-96 0 0 64 96 0 0 64 128-96-128-96z" transform="scale( 1 )"></path></svg>';

	$icon['cancel_doc'] = '<svg viewBox="0 0 512 512"><path d="m416 160l-64-64-96 96-96-96-64 64 96 96-96 96 64 64 96-96 96 96 64-64-96-96z" transform="scale( 1 )"></path></svg>';

	$icon['active_doc'] = '<svg viewBox="0 0 512 512"><path d="m64 256l64-64l96 96l192-192l64 64l-256 256z" transform="scale( 1 )"></path></svg>';

	return $icon[$type];
}





function selectExtraDate( $param ) {
	
	$dao = getDb();
	//arr( $param['data']['rows'] );
	//arr( $param['use_tb'] );
	
	foreach( $param['data']['rows'] as $ka => $va ) {
		
		if( $param['use_tb'] == 'erp_company_group' ) {
			$sql = "
			
				SELECT
					CONCAT( '<input autocomplete=\"off\" type=\"number\" name=\"sale_extra_due\" value=\"', c.sale_extra_due ,'\">' ) as sale_extra_due_input,
				
					
					CONCAT( '<input autocomplete=\"off\" onclick=\"loadDatePicker( this, \'yy-mm-dd\' )\" type=\"text\" name=\"start_update\" value=\"', date_format( NOW(), '%Y-%m-01' )  ,'\">' ) as start_update,
					
					c.time_update
					
				FROM erp_company_group c 
				WHERE c.company_group_id = ". $va->company_group_id ."
			";
		
			$tables[] = ''. getTable____( $dao->fetchAll( $sql ) ) .'';		
			
			return '
				<div class="clear-fix">
					<form action="'. setLink( 'ajax/saveExtraDueDate' ) .'" enctype="multipart/form-data">

						<input name="use_tb" type="hidden" value="erp_company_group">
						<input type="submit" value="Save">

						<input type="hidden" name="company_group_id" value="'. $va->company_group_id .'">

						'. implode( '<br>', $tables ) .'
					</form>
				</div>
			';
		}
		else {
			
			$sql = "
			
				SELECT
					CONCAT( '<input autocomplete=\"off\" type=\"number\" name=\"sale_extra_due\" value=\"', c.sale_extra_due ,'\">' ) as sale_extra_due_input,
					
					CONCAT( '<input autocomplete=\"off\" type=\"number\" name=\"purchase_extra_due\" value=\"', c.purchase_extra_due ,'\">' ) as purchase_extra_due_input,
					
					CONCAT( '<input autocomplete=\"off\" onclick=\"loadDatePicker( this, \'yy-mm-dd\' )\" type=\"text\" name=\"start_update\" value=\"', date_format( NOW(), '%Y-%m-01' )  ,'\">' ) as start_update,
					
					c.time_update
					
				FROM erp_company c 
				WHERE c.company_id = ". $va->company_id ."
			";
		
			$tables[] = ''. getTable____( $dao->fetchAll( $sql ) ) .'';		
			
			return '
				<div class="clear-fix">
					<form action="'. setLink( 'ajax/saveExtraDueDate' ) .'" enctype="multipart/form-data">

						<input type="submit" value="Save">

						<input type="hidden" name="company_id" value="'. $va->company_id .'">

						'. implode( '<br>', $tables ) .'
					</form>
				</div>
			';
		}
		
	
	}
	
}

//
//
function gdgsdgg( $param ) {


//arr( $_REQUEST);
//exit;
//arr($param['keep_stop_doc'] );
	$dao = getDb();

	$defRequires = array( 'amt', 'percent_amt', 'comment' );

	$parent_id = $param['parent_id'];

	$sql = "
		SELECT
			*
		FROM aa_payment_vat_config
	";

	$rows = $dao->fetchAll( $sql );

	if( isset( $_REQUEST['ajax'] ) ) {

		if( isset( $_REQUEST['clear'] ) ) {

			$sql = "
				DELETE FROM aa_payment_vat_dt
				WHERE parent_id = ". $parent_id;

			$dao->execDatas( $sql );

			$sql = "
				UPDATE aa_payment_vat p
				SET 
					p.nprice = 0,
					p.amt = 0
				WHERE p.id = ". $parent_id ."";
			
			$dao->execDatas( $sql );

			header( 'Location: '. comeBack() );

			exit;
		}

		$errors = array();
		foreach( $rows as $lineNo => $vr ) {

			$requires = $defRequires;

			if( !empty( $vr->remark ) ) {

				$requires[] = 'remark';
			}
			
			//arr( $requires);

			$lineNo += 1;

			if( !empty( $vr->input ) ) {

				$haveWork = 0;
				$vals['remark'] = NULL;
				foreach( $requires as $ki => $vi ) {

					if( !empty( $_REQUEST[$lineNo][$vi] ) ) {

						$haveWork = 1;

						$vals[$vi] = $_REQUEST[$lineNo][$vi];
					}
				}

				if( $haveWork == 1 ) {

					foreach( $requires as $ki => $vi ) {

						if( empty( $_REQUEST[$lineNo][$vi] ) ) {
							$errors[] = $lineNo .'['. $vi .']';
						}
					}
				}

				if( count( $errors ) == 0 && $haveWork == 1 ) {
					
					
					
					//arr( $_REQUEST[$lineNo] );
					
					if( !empty( $_REQUEST[$lineNo]['last_amt'] ) ) {
						
						//echo 'dsfadsff';
						$last_amt = "". ( str_replace( ',', '', $_REQUEST[$lineNo]['last_amt'] ) ) ." as last_amt";
						
					}
					else {
						
						
						$last_amt = "". ( str_replace( ',', '', $vals['amt'] ) ) ." * ". $vals['percent_amt'] ." / 100 as last_amt";
					}
					
					///exit;

					$sql = "
						REPLACE INTO aa_payment_vat_dt ( remark, line_no, title, parent_id, amt, last_amt, percent_amt, comment )
						SELECT
							'". $vals['remark'] ."' as remark,
							". $lineNo ." as line_no,
							'". $vr->title ."' as title,
							". $parent_id ." as parent_id,
							'". str_replace( ',', '', $vals['amt'] ) ."' as amt,
							". $last_amt .",
							". $vals['percent_amt'] ." as percent_amt,
							'". $vals['comment'] ."' as comment
						FROM  aa_payment_vat
						WHERE id = ". $parent_id ."


					";
					
					//arr( $vals );
				//arr( $sql );
					$dao->execDatas( $sql );
				}
			}
		}

		$json = array();
		$json['errors'] = $errors;
		echo json_encode( $json );

		$sql = "
			UPDATE aa_payment_vat p
			LEFT JOIN (
				SELECT
					parent_id,
					SUM( last_amt ) as nprice,
					SUM( amt ) as amt	
				FROM aa_payment_vat_dt
				WHERE parent_id = ". $parent_id ."
				GROUP BY 
					parent_id
			) as new_tb ON p.id = new_tb.parent_id

			SET 
				p.nprice = new_tb.nprice,
				p.amt = new_tb.amt 
			
			WHERE p.id = ". $parent_id ."";
		
		$dao->execDatas( $sql );


		
		exit;

	}

	$proove = false;
	if( in_array( 1, array_keys( $param['keep_stop_doc'] ) ) || in_array( 2, array_keys( $param['keep_stop_doc'] ) ) )
		$proove = 'ตรวจสอบ/อนุมัติแล้ว';
	
	
	
	$sql = "
		SELECT 
			p.doc_no 
		FROM erp_ap_pay_vat pay 
		LEFT JOIN erp_ap_pay p ON pay.parent_id = p.id
		WHERE pay.cheque_id = ". $parent_id ."
		LIMIT 0, 1 

	";
	
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		$proove = 'เอกสารนี้ถูกใช้งานโดย ' . $va->doc_no;
		
	}

//$proove = true;
	$tds = array();
	$tds[-1][] = '<th style="width: 50%;">ประเภทเงินได้พึงประเมินจ่าย</th>';
	$tds[-1][] = '<th>วัน เดือนหรือปีภาษี ที่จ่าย</th>';
	$tds[-1][] = '<th>จำนวนเงินที่จ่าย</th>';
	$tds[-1][] = '<th>ภาษีที่หัก และนำส่งไว้ (%)</th>';
	$tds[-1][] = '<th>ประเภทเงินได้พึงประเมินที่จ่าย</th>';
	$tds[-1][] = '<th>ภาษีที่หัก และนำส่งไว้</th>';

	$trs = array();

	foreach( $rows as $lineNo => $vr ) {

		$lineNo += 1;
		$styles = array();

		$styles[] = 'white-space: inherit;';
		$styles[] = 'padding: 5px;';
		if( !empty( $vr->paddingStep ) ) {

			$styles[] = 'padding-left: '. ( 5 + ( $vr->paddingStep * 15 ) ) .'px;';
		}

		$style = 'style="'. implode( '', $styles ) .'"';

		if( !empty( $vr->input ) ) {

			$sql = "
				SELECT
					dt.line_no,
					dt.remark,
					pv.doc_date, 
					dt.title,
					dt.parent_id,
					dt.amt,
					dt.comment,
					dt.percent_amt,
					dt.last_amt
				FROM aa_payment_vat_dt dt
				LEFT JOIN aa_payment_vat pv ON dt.parent_id = pv.id
				WHERE dt.line_no = ". $lineNo ."
				AND dt.parent_id = ". $parent_id ."
			";

			$vals = new stdClass;
			$vals->doc_date = NULL;
			$vals->amt = NULL;
			$vals->last_amt = NULL;
			$vals->remark = NULL;
			$vals->percent_amt = NULL;
			$vals->comment = NULL;
			foreach( $dao->fetchAll( $sql ) as $kd => $vd ) {
			
			
				$vals = $vd;
			}

		//	arr( $sql );

			$replace = array();

			if( $proove ) {

				$replace['[val]'] = '<span style="display: inline-block; font-weight: bold; padding: 3px; text-decoration: underline;">'. $vals->remark .'</span>';

				$tds[$lineNo][] = '<td class="L" '. $style .'>'. str_replace( array_keys( $replace ), $replace, $vr->title ) .'</td>';

				$styles = array();

				$styles[] = 'padding: 5px;';

				$style = 'style="'. implode( '', $styles ) .'"';

				$tds[$lineNo][] = '<td class="C" '. $style .'><div class="contain-input-box">'. $vals->doc_date .'</div>
				</td>';

				$tds[$lineNo][] = '<td class="R" '. $style .'><div class="contain-input-box">'. $vals->amt .'</div></td>';

				$tds[$lineNo][] = '<td class="R" '. $style .'><div class="contain-input-box">'. $vals->percent_amt .'</div></td>';
				
				$tds[$lineNo][] = '<td class="R" '. $style .'><div class="contain-input-box">'. $vals->comment .'</div></td>';

			}
			else {

				$replace['[val]'] = '<div class="contain-input-box" style="display: inline-block;"><input style="width: 100px;" type="text" name="'. $lineNo . '[remark]" value="'. $vals->remark .'" /></div>';


				$tds[$lineNo][] = '<td class="L" '. $style .'>'. str_replace( array_keys( $replace ), $replace, $vr->title ) .'</td>';

				$styles = array();

				$styles[] = 'padding: 5px;';

				$style = 'style="'. implode( '', $styles ) .'"';

				$tds[$lineNo][] = '<td class="C" '. $style .'><div class="contain-input-box">'. $vals->doc_date .'</div>
				</td>';

				$tds[$lineNo][] = '<td '. $style .'><div class="contain-input-box"><input name="'. $lineNo .'[amt]" autocomplete="off" onkeyup="javascript:controlnumbers( this, fm_numeric );" type="text" value="'. $vals->amt .'" class="box-edit-line"></div></td>';

				$tds[$lineNo][] = '
				
					<td class="R" '. $style .'>
						<div class="contain-input-box"><input name="'. $lineNo .'[percent_amt]" autocomplete="off" onkeyup="javascript:controlnumbers( this, fm_numeric );" type="text" value="'. $vals->percent_amt .'" class="box-edit-line"></div>
					
					</td>';
					
					
				$tds[$lineNo][] = '
				
					<td class="R" '. $style .'>
						<div class="contain-input-box">
							<input name="'. $lineNo .'[comment]" autocomplete="off" type="text" value="'. $vals->comment .'" class="box-edit-line">
						</div>
					
					</td>';
					
					

			}
			
					
					

			$tds[$lineNo][] = '
			<td class="R" '. $style .'>
						<div class="contain-input-box"><input name="'. $lineNo .'[last_amt]" autocomplete="off" onkeyup="javascript:controlnumbers( this, fm_numeric );" type="text" value="'. $vals->last_amt .'" class="box-edit-line"></div>
					
					</td>
			
		 
			
			
			';
		}
		else {

			$tds[$lineNo][] = '<td class="L" '. $style .'>'. $vr->title .'</td>';
			$tds[$lineNo][] = '<td></td>';
			$tds[$lineNo][] = '<td></td>';
			$tds[$lineNo][] = '<td></td>';
			$tds[$lineNo][] = '<td></td>';
			$tds[$lineNo][] = '<td></td>';
		}

	}

	foreach( $tds as $kt => $vt ) {

		$trs[] = '<tr>'. implode( '', $vt ) .'</tr>';
	}
	
	if( $proove ) {
		
		return '
			<div style="margin: 10px">
			'. $proove .'
			<table class="flexme3">'. implode( '', $trs ) .'</table>
			</div>
		';
	}

	return '
		<div style="margin: 10px">
			
			<form action="'. setLink( 'ajax/insertPaymentVatDt' ) .'" class="form_in_line" method="get">
				<input type="hidden" name="ajax" value="1" >
				<input type="hidden" name="parent_id" value="'. $parent_id .'" >
				<input class="gogo_submit" type="submit" > <a href="'. setLink( 'ajax/insertPaymentVatDt', array( 'clear' => 1, 'ajax' => 1, 'parent_id' => $parent_id ) ) .'" >clear</a>
				<table class="flexme3">'. implode( '', $trs ) .'</table>
			</form>
		</div>

		<script>
		$(function(){

		//
		//
		$( \'.gogo_submit\' ).live( \'click\', function () {

			form = $( this ).parents( \'form\' );

			form.find( \'.contain-input-box\' ).removeClass( \'error\' );

			var completed = \'0%\';

			form.ajaxForm({
				beforeSend: function() {
					var data = {};

				},
				complete: function( response ) {

					protect = 0;

					data = $.parseJSON( response.responseText );

					if( data.errors.length == 0 ) {

						location.reload();

					}

					for ( x in data.errors ) {

						$( \'[name="\'+ data.errors[x] +\'"]\' ).parent().addClass( \'error\' );

					}


				}
			});
		});
		});
		</script>
	';
}







//
//
function howToPrint( $param ) {
	
	return '<img style="width: 250px;" class="" src="http://'. OnlineUrl .'/sac2015/file_upload/aa_chq_out/105-36349476937671288639176337.jpg?rand=1050307425file_upload/aa_chq_out/105-36349476937671288639176337.jpg">';	
	
}



//
//
function yiuouo( $param ) {
	
	return;
	
	$dao = getDb();
		
	$sql = "
		SELECT
			dt.doc_no,
			CONCAT( p.product_code, ' ', p.product_name ) as product,
			dt.book_id,
			concat( dt.product_id, '-', IF( dt.color = '', 'xxx', dt.color ), '-', dt.zone_id, '-', dt.admin_gcompany_id, '-', dt.admin_company_id ) as pczgc,
			( dt.qty * dt.factor ) as qty,
			dt.qty_bal,
			
			dt.pcz_qty_bal,
			dt.order_number as n,
			z.zone_name,
			dt.pcz_send_qty_bal as send,
			dt.move_pare,
			dt.doc_date,
			dt.doc_priority,
			dt.tbName,
			dt.factor,
			dt.id,
			dt.act_id,
			dt.amt_bal,
			
			dt.cost_bal,
			dt.cost_amt,
			dt.g_qty_bal,
			dt.g_cost_amt,
			dt.g_amt_bal,
			dt.g_cost_bal,
			act.stock_act_name
		FROM erp_stock_dt dt
		LEFT JOIN erp_stock_act act ON dt.act_id = act.stock_act_id
		LEFT JOIN erp_zone z ON dt.zone_id = z.zone_id
		LEFT JOIN erp_product p ON dt.product_id = p.product_id
		[WHERE]
		ORDER BY
			product ASC,
			dt.order_number ASC
	";
	

	$date = $param['data']['rows'][0]->doc_date;
	


	//$date = '2000-01-01';

	//$filters['WHERE'][] = "dt.doc_date >= '". $date ."'";
	//$filters['WHERE'][] = "dt.admin_company_id = ". $_SESSION['company_id'] ."";

	if( !empty( $_SESSION['error_products'] ) ) {
		//$filters['WHERE'][] = "dt.product_id IN ( ". implode( ', ', $_SESSION['error_products'] ) ." )";
	}

	else {
		//return '';

	}
	
	$filters = array();
	$filters['WHERE'][] = "dt.product_id IN (  1738 )";
	$filters['WHERE'][] = "dt.color IN (  'L046' )";
	
	$sql = genCond_( $sql, $filters );
	
	$config['book_id'] = array( 'a' => 'L' );
	$config['pczgc'] = array( 'a' => 'L' );
	$config['qty'] = array( 'a' => 'R' );
	$config['pd'] = array( 'a' => 'L' );
	$config['g_amt_bal'] = array( 'a' => 'R' );
	$config['g_qty_bal'] = array( 'a' => 'R' );
	$config['g_cost_bal'] = array( 'a' => 'R' );
	$config['send'] = array( 'a' => 'R' );
	$config['pcz_qty_bal'] = array( 'a' => 'R' );
	$config['amt_bal'] = array( 'a' => 'R' );
	$config['cost_bal'] = array( 'a' => 'R' );
	$config['cost_amt'] = array( 'a' => 'R' );
	$config['product'] = array( 'a' => 'L' );
	$config['qty_bal'] = array( 'a' => 'R' );
	$config['g_cost_amt'] = array( 'a' => 'R' );
	
	
	$tables[] = getTable____( $dao->fetchAll( $sql ), $config );

	return '
		<div style="margin: 10px; ">
			<div class="po-re pd-10-bd" style="padding: 0; border: none;">
				<center><b class="red">**รายการสินค้าในสต็อค ณ วันที่ '. $date .'**</b></center>
			</div>
		</div>

		<div style="margin: 10px; overflow: auto;">'. implode( '', $tables ) .'</div>
	';

}

//
//
function dsadjdskdsjdjd( $param ) {

	$dao = getDb();

	$data['status'] = 0;
	$article_ids = array();
	if( isset( $_REQUEST['ajax'] ) ) {

		$config = array(
			'article_ids', 'type'
		);

		$update = 1;
		foreach( $config as $kc => $vc ) {

			if( empty( $_REQUEST[$vc] ) ) {

				$update = 0;
			}
		}


		if( $update == 1 ) {

			$article_ids = $_REQUEST['article_ids'];

			$sql = "
				UPDATE aa_sale_product
				SET type = '". $_REQUEST['type'] ."'
				WHERE article_id IN ( ". implode( ',', $article_ids ) ." )
			";

			$data['sql'] = $sql;

			if( $dao->execDatas( $sql ) ) {

				$data['status'] = 1;
			}
		}
	}

	$sql = "
		SELECT
			*
		FROM aa_sale_product
		ORDER BY
			time_update DESC,
			type ASC
	";

	$trs = array();

	$trs[] = '
		<tr>
			<th>เลือกรายการ</th>
			<th>รายการ</th>
			<th>ขนาด</th>
			<th>กลุ่ม</th>
			<th>อัพเดทเมื่อ</th>
			<th>Status</th>
		</tr>
	';

	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {

		$trs[] = '
			<tr>
				<td><input type="checkbox" name="article_ids[]" value="'. $va->article_id .'" /></td>
				<td style="padding: 3px;" class="L">'. $va->name .'</td>
				<td>'. $va->size .'</td>
				<td>'. $va->type .'</td>
				<td>'. $va->time_update .'</td>
				<td>'. select( 1, in_array( $va->article_id, $article_ids ), '<span class="green">Update</span>', 'Ready' ) .'</td>
			</tr>
		';
	}

	$data['html'] = implode( '', $trs );
	if( isset( $_REQUEST['ajax'] ) ) {

		echo json_encode( $data );

		exit;
	}


	return '
		<div style="margin: 10px;">
			<form action="'. setLink( 'ajax/updateSaleProduct' ) .'" enctype="multipart/form-data">
				<input type="hidden" name="ajax" value="1">

				<div style="">
					<label>เลือกประเภทการผลิตกระเบื้อง</label>
					<div class="clear-fix">

						<div class="fl" style="width: 30%; margin-right: 10px;">

							<select class="box-edit-line" name="type">

								<option selected="-" value="-">-</option>
								<option value="MONO">MONO</option>
								<option value="DECOR">DECOR</option>

							</select>

						</div>

						<div class="fl" style="width: 30%;">
							<input type="submit" value="Update" class="web-bt" style="padding: 5px;" >
						</div>
					</div>

				</div>
				<br>


				<div class="load-update"></div>

				<table class="flexme3 load-data">'. $data['html'] .'</table>
			</form>

			<script>

			$( function() {

				$( \'form\' ).submit( function() {

					me = $( this );

					$.getJSON( me.attr( \'action\' ), me.serialize(), function( data ) {


						if( data.status == 1 ) {
							$( \'.load-data\' ).html( data.html );

						}

					});
					return false;
				});
			});
			</script>
		</div>
	';



}



//
//
function schedulePlanView( $param ) {


	$dao = getDb();


	$weeks = array(
		'Wed' => 'พ',
		'Sat' => 'ส',
		'Mon' => 'จ',
		'Tue' => 'อ',
		'Fri' => 'ศ',
		'Thu' => 'พฤ',
		'Sun' => 'อา',
	);

	$bg[0] = '#f5f3c9';
	$bg[1] = '#fdfd86';


	$boxW['rowNumber'] = '30px';
	$boxW['name'] = '110px';
	$boxW['image'] = '70px';
	$boxW['day'] = '11';
	$imgStyle = 'width: auto; height: 64px; border: none;';
	$perPage = 10;

	$border = "border: 1px solid #000;";

	$sundayBg = '#f5a9a7';

	$replace = array();
	if( !empty( $param['parent_id'] ) ) {

		$replace['WHERE'][] = "dt.parent_id = ". $param['parent_id'] ."";
	}

	///$replace['WHERE'][] = "dt.parent_id = 35";

	$sql = "
		SELECT
			LAST_DAY( MIN( new_tb.minDate ) ) as before_start,
			DATE_FORMAT( MIN( new_tb.minDate ), '%Y-%m-01' ) as minDate,
			DATE_FORMAT( MAX( new_tb.maxDate ), '%Y-%m-01' ) as maxDate
		FROM (
			SELECT
				new_tb.parent_id,
				MIN( ADDDATE( new_tb.atDate, INTERVAL -1 month ) ) as minDate,
				MAX( new_tb.atDate ) as maxDate
			FROM (

				SELECT
					dt.parent_id,
					dt.atDate
				FROM aa_schedule_dt dt
				[WHERE]

				UNION

				SELECT
					dt.parent_id,
					IFNULL( (

						SELECT
							atDate
						FROM aa_schedule_dt
						WHERE status != 0
						AND parent_id = dt.parent_id
						LIMIT 0, 1
					), NOW() ) as atDate
				FROM aa_schedule_dt dt
				LEFT JOIN aa_schedule sc ON dt.parent_id = sc.id
				[WHERE]

			) as new_tb
			GROUP BY
				parent_id
		) as new_tb
	";

	$sql = genCond_( $sql, $replace );



	foreach( $dao->fetchAll( $sql ) as $kg => $vg ) {

		$defalt_before_start = $vg->before_start;
		//arr( $vg );

		$curMonth = $vg->minDate;

		for( $i = 1; $i <= 100; ++$i ) {

			$subSql = "
				SELECT
					DATE_FORMAT( ADDDATE( '". $curMonth ."', INTERVAL 1 month ), '%Y-%m-01' ) as curMonth
			";

			$sql = "
				SELECT
					DATE_FORMAT( LAST_DAY( new_tb.curMonth ), '%d' ) as amt_d,
					DATE_FORMAT( LAST_DAY( new_tb.curMonth ), '%Y-%m-%d' ) as last_d,
					new_tb.curMonth,
					DATE_FORMAT( new_tb.curMonth, '%M %Y' ) as title
				FROM (
					". $subSql ."
				) as new_tb
			";

			$res = $dao->fetch( $sql );

			$curMonth = $res->curMonth;

			$months[] = convertObJectToArray( $res );

			if( count( $months ) == 2 ) {

				$monthsGroup[] = array(

					'months' => $months,
					'last_d' => $res->last_d
				);
				$months = array();
			}

			if( $curMonth == $vg->maxDate ) {

				break;
			}
		}
	}

	if( count( $months ) > 0 ) {

		$monthsGroup[] = array(

			'months' => $months,
			'last_d' => $res->last_d
		);

		$months = array();
	}


	$sql = "
		SELECT
			dt.atDate as myFullFate,
			dt.parent_id,
			dt.text,
			sc.name,
			sc.img,
			dt.worker,
			IFNULL( (
				SELECT
					atDate
				FROM aa_schedule_dt
				WHERE status != 0
				AND parent_id = dt.parent_id
				LIMIT 0, 1

			), NOW() ) as end_date,
			dt.status
		FROM aa_schedule_dt dt
		LEFT JOIN aa_schedule sc ON dt.parent_id = sc.id
		[WHERE]
		ORDER BY
			parent_id ASC,
			myFullFate ASC
	";

	$sql = genCond_( $sql, $replace );


	$datasGroup = array();
	foreach( $dao->fetchAll( $sql ) as $kd => $vd ) {

		$datasGroup[$vd->parent_id][] = $vd;
	}

	$array_keys = array_keys( $datasGroup );

	$tables = array();
	$datas = array();

	$page = 0;
	foreach( $datasGroup as $kdg => $vPage ) {

		//arr($kdg);
		$datas[$kdg] = $vPage;


		if( count( $datas ) == $perPage || $kdg == $array_keys[count($array_keys)-1] ) {

			++$page;
			$before_start = $defalt_before_start;

			foreach( $monthsGroup as $kmg => $vmg ) {

				$trs = array();
				$tds = array();

				$rowNumber = 0;

				$tds[$rowNumber][] = '<th rowspan="3" style="text-align: center; width: '. $boxW['rowNumber'] .'; '. $border .'">#</th>';
				$tds[$rowNumber][] = '<th rowspan="3" style="text-align: center; width: '. $boxW['name'] .'; '. $border .'">รายการ</th>';
				$tds[$rowNumber][] = '<th rowspan="3" style="text-align: center; width: '. $boxW['image'] .'; '. $border .'">ภาพ</th>';


				foreach( $vmg['months'] as $km => $vm ) {

					$tds[$rowNumber][] = '<th colspan="'. $vm['amt_d'] .'" style="'. $border .' text-align: center;">'. $vm['title'] .'</th>';

				}

				$rowNumber = 1;


				++$rowNumber;


				$toDay = $before_start;

				$d = 1;
				for( $d = $d; $d < 500; ++$d ) {

					$sql = "

						SELECT
							new_tb.toDay,
							DATE_FORMAT( new_tb.toDay, '%d' ) as d,
							DATE_FORMAT( new_tb.toDay, '%a' ) as a,
							IF( DATE_FORMAT( new_tb.toDay, '%a' ) = 'Sun', '". $sundayBg ."', 'white' ) as bgColor

						FROM (
							SELECT
								ADDDATE( '". $toDay ."', INTERVAL 1 day ) as toDay
						) as new_tb
						WHERE new_tb.toDay <= '". $vmg['last_d'] ."'
					";

					$res = $dao->fetch( $sql );

					if ( !$res ) {

						break;
					}

					$toDay = $res->toDay;

					$tds[1][] = '<td style="background-color: '. $res->bgColor .'; text-align: center; width: '. $boxW['day'] .'px; '. $border .'">'. $weeks[$res->a] .'</td>';

					$tds[2][] = '<td style="background-color: '. $res->bgColor .'; text-align: center; width: '. $boxW['day'] .'px; '. $border .'">'. $res->d .'</td>';

				}

				//
				//detail
				$itemNumber = $perPage * ( $page - 1 );
				foreach( $datas as $kd => $vd ) {

					++$itemNumber;

					++$rowNumber;


					$tds[$rowNumber][] = '<th style="text-align: center; width: '. $boxW['rowNumber'] .'; '. $border .'">'. $itemNumber .'</th>';

					$tds[$rowNumber][] = '<th style="text-align: center; width: '. $boxW['name'] .'; '. $border .'">'. $vd[0]->name .'</th>';


					$img = '';

					if( file_exists( 'file_upload/'. $vd[0]->img ) ) {
						$img = '<img style="'. $imgStyle .'" class="full-size" data-target="#myModalPop" data-toggle="modal" src="file_upload/'. $vd[0]->img .'">';
					}


					$tds[$rowNumber][] = '
						<th style="vertical-align: middle; text-align: center; width: '. $boxW['image'] .'; '. $border .'">
							<a target="blank_" href="'. getLink( 75, array(), array( 'parent_id' => $vd[0]->parent_id ) ) .'">
								'. $img .'
							</a>
						</th>
					';

					$toDay = $before_start;

					$d = 1;
					for( $d = $d; $d < 500; ++$d ) {

						$sql = "

							SELECT
								new_tb.toDay,
								DATE_FORMAT( new_tb.toDay, '%d' ) as d,
								DATE_FORMAT( new_tb.toDay, '%a' ) as a,
								IF( DATE_FORMAT( new_tb.toDay, '%a' ) = 'Sun', '". $sundayBg ."', 'white' ) as bgColor

							FROM (
								SELECT
									ADDDATE( '". $toDay ."', INTERVAL 1 day ) as toDay
							) as new_tb
							WHERE new_tb.toDay < '". $vd[0]->myFullFate ."' AND new_tb.toDay <= '". $vmg['last_d'] ."'
						";

						$res = $dao->fetch( $sql );

						if ( !$res ) {

							break;
						}

						$toDay = $res->toDay;
						$tds[$rowNumber][] = '<td style="background-color: '. $res->bgColor .'; text-align: center; width: '. $boxW['day'] .'px; '. $border .'">'. $res->d .'</td>';
					}

					foreach( $vd as $kg => $vg ) {


						if( $kg != 0 ) {

							$colspan = 0;
							for( $d = $d; $d < 500; ++$d ) {

								$sql = "

									SELECT
										new_tb.toDay,
										DATE_FORMAT( new_tb.toDay, '%d' ) as d,
										DATE_FORMAT( new_tb.toDay, '%a' ) as a
									FROM (
										SELECT
											ADDDATE( '". $toDay ."', INTERVAL 1 day ) as toDay
									) as new_tb
									WHERE new_tb.toDay < '". $vg->myFullFate ."' AND new_tb.toDay <= '". $vmg['last_d'] ."'
								";

								$res = $dao->fetch( $sql );

								if ( !$res ) {

									break;
								}

								$toDay = $res->toDay;
								++$colspan;

							}


							if( $colspan > 0 ) {

								$rowBefore->text = str_replace( '[amt_d]', '('. $colspan .'วัน)', $rowBefore->text );

								$text = array();
								if( !empty( $rowBefore->worker ) ) {

									$text[] = $rowBefore->worker;
								}

								$text[] = ''. $rowBefore->text .' ('. $colspan .'วัน)';

								$text = implode( ' : ', $text );

								$tds[$rowNumber][] = '<td colspan="'. $colspan .'" style="background-color: '. $bg[$kg%2] .'; width: '. ( $boxW['day'] * $colspan ) .'px; '. $border .'">'. $text .'</td>';

							}
						}

						$rowBefore = $vg;

					}


					$colspan = 0;
					for( $d = $d; $d < 500; ++$d ) {

						$sql = "

							SELECT
								new_tb.toDay,
								DATE_FORMAT( new_tb.toDay, '%d' ) as d,
								DATE_FORMAT( new_tb.toDay, '%a' ) as a
							FROM (
								SELECT
									ADDDATE( '". $toDay ."', INTERVAL 1 day ) as toDay
							) as new_tb
							WHERE new_tb.toDay < '". $vg->end_date ."' AND new_tb.toDay <= '". $vmg['last_d'] ."'
						";

						$res = $dao->fetch( $sql );

						if ( !$res ) {

							break;
						}

						$toDay = $res->toDay;

						++$colspan;
					}

					if( $colspan > 0 ) {

						if( $rowBefore->status == 0 ) {

							$bgColor = $bg[0];

							if( $kg % 2 == 0 ) {
								$bgColor = $bg[1];
							}

							$rowBefore->text = str_replace( '[amt_d]', '('. $colspan .'วัน)', $rowBefore->text );


							$text = array();
							if( !empty( $rowBefore->worker ) ) {

								$text[] = $rowBefore->worker;
							}

							$text[] = ''. $rowBefore->text .' ('. $colspan .'วัน)';

							$text = implode( ' : ', $text );


							$tds[$rowNumber][] = '<td colspan="'. $colspan .'" style="background-color: '. $bgColor .'; width: '. ( $boxW['day'] * $colspan ) .'px; '. $border .'">'. $text .' </td>';

						}
						else {

							$json_decode = json_decode( $param['sub_config']->columns['status']->input_type );

							$filters = array();
							$filters['WHERE'][] = "tb.id = ". $rowBefore->status ."";

							$sql = str_replace( '%filter;', '[WHERE]', $json_decode->sql );

							$sql = genCond_( $sql, $filters );

							$res = $dao->fetch( $sql );

							$text = '';
							if( !empty( $rowBefore->text ) ) {

								$text = str_replace( '[amt_d]', '', $rowBefore->text );
							}

							$tds[$rowNumber][] = '<td style="background-color: '. $res->bgColor .'; width: '. $boxW['day'] .'px; '. $border .'">'. $text .'</td>';
						}
					}


					for( $d = $d; $d < 500; ++$d ) {

						$sql = "

							SELECT
								new_tb.toDay,
								DATE_FORMAT( new_tb.toDay, '%d' ) as d,
								DATE_FORMAT( new_tb.toDay, '%a' ) as a,
								IF( DATE_FORMAT( new_tb.toDay, '%a' ) = 'Sun', '". $sundayBg ."', 'white' ) as bgColor

							FROM (
								SELECT
									ADDDATE( '". $toDay ."', INTERVAL 1 day ) as toDay
							) as new_tb
							WHERE new_tb.toDay <= '". $vmg['last_d'] ."'
						";

						$res = $dao->fetch( $sql );

						if ( !$res ) {

							break;
						}

						$toDay = $res->toDay;

						$tds[$rowNumber][] = '<td style="background-color: '. $res->bgColor .'; text-align: center; width: '. $boxW['day'] .'px; '. $border .'">'. $res->d .'</td>';


					}
				}

				foreach( $tds as $kt => $vt ) {

					$trs[] = '
						<tr>'. implode( '', $vt ) .'</tr>
					';
				}

				if( isset( $param['view'] ) ) {

					$tables[] = '
						<page backtop="10mm" backbottom="5mm" backleft="5mm" backright="5mm">
							<page_header>
								<div class="content-header-footer">
									<table style="width: 100%;">
										<tr>
											<td style="width: 20%; border: none;"></td>
											<td style="width: 60%; border: none;"><h2 style="text-align: center;"><a target="_blank" href="'. setLink( 'ajax/schedulePlanView' ) .'">ตารางออกแบบลายใหม่กระเบื้องโมโน </a></h2></td>
											<td style="width: 20%; border: none; text-align: right;">
											'. gettime_( NULL, 16 ) .'
											</td>
										</tr>
									</table>
								</div>
							</page_header>
							<page_footer>
								<p style="text-align: center;">หน้า [[page_cu]] / [[page_nb]]</p>
							</page_footer>

							<table style="width: 100%;">'. implode( '', $trs ) .'</table>
						</page>
					';
				}
				else {

					$tables[] = '
						<div style="margin: 10px;">
							<h2 style="text-align: center;"><a target="_blank" href="'. setLink( 'ajax/schedulePlanView' ) .'">ตารางออกแบบลายใหม่กระเบื้องโมโน </a></h2>

							<table style="width: 100%;">'. implode( '', $trs ) .'</table>
						</div>

					';
				}

				$before_start = $toDay;

			}

			$datas = array();
		}
	}

	return implode( '', $tables );



}


//
//
function dashboardImgsHtml_( $param ) {

	$dao = getDb();

	$trs = array();

	$totalSqPixel = $param['data']['rows'][0]->sq_pixel;

	$totalSqCm = $param['data']['rows'][0]->sqcm;

	$trs[] = '
		<tr>
			<th rowspan="2" style="width: 10%;">img</th>
			<th rowspan="2" style="width: 10%;">color</th>
			<th rowspan="2" style="width: 6%;">rgb</th>
			<th rowspan="2" style="width: 6%;">hex</th>
			<th rowspan="2" style="width: 5%;">pixel</th>
			<th rowspan="2" style="width: 5%;">%</th>
			<th rowspan="2" style="width: 5%;">sqCm.</th>
			<th colspan="2">นน.(grm)</th>

		</tr>
	';

	$trs[] = '
		<tr>
			<th style="width: 5%;">ทอง</th>
			<th style="width: 5%;">แก้ว</th>
		</tr>
	';

	$factorGold = 0.0027;

	$factorGlass = 0.0001;

	$sql = "
		SELECT
			rgb,
			hex,
			SUM( pixel ) as pixel,
			SUM( pixel ) / ". $totalSqPixel ." * 100 as percentage,
			SUM( pixel ) / ". $totalSqPixel ." * ". $totalSqCm ." as sqCm,
			SUM( pixel ) / ". $totalSqPixel ." * ". $totalSqCm ." * ". $factorGold ." as grmGold,
			SUM( pixel ) / ". $totalSqPixel ." * ". $totalSqCm ." * ". $factorGlass ." as grmGlass
		FROM aa_check_imgs_dt
		GROUP BY
			rgb,
			hex
		HAVING percentage > 0.009
		ORDER BY rgb_number ASC
	";

	$res = $dao->fetchAll( $sql );

	$tds[] = '
		<td rowspan="'. count( $res ) .'" style="vertical-align: top;">
			<img style="width: 100%;" src="file_upload/'. $param['data']['rows'][0]->file .'?rand='. rand() .'" />
		</td>
	';

	foreach( $res as $kr => $vr ) {

		$tds[] = '
			<td>
				<div style="height: 15px; background: rgb( '. $vr->rgb .' ); position: relative;" ></div>
			</td>
			<td class="C">'. $vr->rgb .'</td>
			<td class="C">'. $vr->hex .'</td>
			<td class="R">'. $vr->pixel .'</td>
			<td class="R">'. $vr->percentage .'%</td>
			<td class="R">'. $vr->sqCm .'</td>
			<td class="R">'. $vr->grmGold .'</td>
			<td class="R">'. $vr->grmGlass .'</td>
		';

		$trs[] = '<tr class="click-me" data-index="'. $kr .'">'. implode( '', $tds ) .'</tr>';

		$tds = array();
	}

	return '
		<div class="clear-fix" style="margin: 10px;">

			<table class="flexme3">'. implode( '', $trs ) .'</table>
		</div>

		<script>

			$( function(){

				res = '. json_encode( $res ) .';

				$( \'.click-me\' ).click( function(){

					lastIndex = $( this ).attr( \'data-index\' );

					totalSqcm = 0;

					totalGrmGold = 0;
					totalGrmGlass = 0;

					totalPercent = 0;

					for( i = 0; i <= lastIndex; ++i ) {

						totalSqcm += Number( res[i].sqCm );
						totalGrmGold += Number( res[i].grmGold );
						totalGrmGlass += Number( res[i].grmGlass );
						totalPercent += Number( res[i].percentage );
					}

					$( \'.modal-content\' )
						.html( \'<div style="padding: 10px;"><b>รายละเอียด </b><br><ul><li><b>พื้นที่</b> \'+ totalSqcm +\' (ตร.ซม)</li><li><b>พื้นที่</b> \'+ totalPercent +\'%</li><li><b>น้ำหนักทอง</b> \'+ totalGrmGold +\'grm (factor '. $factorGold .')</li><li><b>น้ำหนักแก้ว</b> \'+ totalGrmGlass +\'kg (factor '. $factorGlass .')</li></ul>\' )
						.parent()
						.removeClass( \'modal-sm\' )
						.addClass( \'modal-lg\' );

					$( \'#myModalPop\' ).modal( \'show\' );
				});
			});
		</script>
	';
}


//
//
function dashboardImgsHtml( $param ) {

	$textJson = json_decode( $param['data']['rows'][0]->text_json );

	$trs = array();

	$trs[] = '
		<tr>
			<th style="width: 20%;">ภาพตัวอย่าง</th>
			<th style="width: 10%;">color</th>
			<th style="width: 6%;">hex</th>
			<th style="width: 5%;">%</th>
			<th style="width: 5%;">sqCm.</th>
			<th style="width: 5%;">grm.</th>
			<th style="width: 5%;"></th>
		</tr>
	';

	$tds[] = '
		<td rowspan="'. $textJson->colorsNumber .'">
			<img src="file_upload/'. $param['data']['rows'][0]->file .'?rand='. rand() .'" style="width: 180px;"/>
		</td>
	';

	$id = 0;
	foreach( $textJson->trs as $kj => $vj ){

		$percentage = $vj->percentage;

		$sqCm = $vj->sqCm;

		$grm = $vj->grm;


		$tds[] = '
			<td>
				<div style="height: 15px; background: '. $kj .'; position: relative;" ></div>
			</td>
			<td class="C">'. $kj .'</td>

			<td class="R">'. $percentage .'%</td>

			<td class="R">'. $sqCm .'</td>
			<td class="R">'. $grm .'</td>

			<td><input type="checkbox" value="'. $kj .'" name="test" /></td>
		';
		$trs[] = '<tr>'. implode( '', $tds ) .'</tr>';

		$tds = array();

	}



	return '
		<div class="clear-fix" style="margin: 10px;">
			<div class="clear-fix show-result" style=""></div>
			<table class="flexme3">'. implode( '', $trs ) .'</table>
		</div>

		<script>

			$( function(){

				res = '. json_encode( $textJson->trs ) .'

				$( \'*[name="test"]\' ).click( function(){

					totalSqcm = 0;
					totalGrm = 0;
					totalPercent = 0;
					$( \'*[name="test"]\' ).each( function(){

						if( $( this ).filter( \':checked\' ).val() ) {

							totalSqcm += Number( res[$( this ).val()].sqCm );

							totalGrm += Number( res[$( this ).val()].grm );

							totalPercent += Number( res[$( this ).val()].percentage );
						}
					});

					totalSqcm = totalSqcm.toFixed( 2 );

					totalGrm = totalGrm.toFixed( 2 );

					totalPercent = totalPercent.toFixed( 2 );

					$( \'.show-result\' ).html( \'<b>รายละเอียดทองคำที่ต้องใช้ </b>: พื้นที่ \'+ totalSqcm +\' (ตร.ซม), น้ำหนัก \'+ totalGrm +\' grm. \'+ totalPercent +\'%\' );
				});

			});
		</script>
	';
}

//
//
function getUserJobPlan( $param ) {


	$rows = array();

	$param['filters'][] = "pl.user_id = ". $_SESSION[Uid] ."";

	foreach( testgogo( $param ) as $kp => $vp ) {

		$style = '';
		if( $vp->status_id == 1 )
			$style = ' style="background-color: #fafdda;"';

		$rows[] = '
			<tr'. $style .'>
				<td style="width: 5%; text-align: center;">'. ( count( $rows ) + 1 ) .'</td>
				<td style="" class="L">
					<span style="font-weight: bold;"><a target="_blank_" href="'. getLink( 73, array(), array( 'parent_id' => $vp->id ) ) .'" style="">
				'. $vp->name .'
					</a></span>
				</td>
				<td style="width: 10%; text-align: center;">
					'. $vp->work_time .'
				</td>
				<td style="width: 10%; text-align: center;">
					'. $vp->start_day .'
				</td>
				<td style="width: 12%; text-align: center;">
					'. $vp->end_day .'
				</td>

				<td style="width: 7%; text-align: center;">'. $vp->status .'</td>
			</tr>
		';
	}

	$divs[] = '<table class="flexme3 sorttable">'. implode( '', $rows ) .'</table>';


	return '

		<div class="clear-fix" style="margin: 10px;">'. implode( '<br>', $divs ) .'</div>

		<script>
		$( function() {
			$( \'.sorttable tbody\' )
			.sortable()
			.disableSelection()
			.mouseup(function(){

			});


		});
		</script>
	';

}



function userModelTime( $param = array() ) {
	//return;
	//arr( $param );
	
	//exit;
	$dao = getDb();
	
	$sql = "
	
		SELECT 
			*
		FROM admin_user

	";
	//where user_id = 27
	$sqlUnion = array();
	foreach( $param['data']['rows'] as $ka => $va ) {
	//foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		
		$j = json_decode( stripslashes( $va->model_time ) );
		
		if( empty( $j ) ) {
			
			continue;
			
		}
		
		//arr( $j );
		//exit;
		foreach( $j as $kj => $vj ) {
			
			if( is_numeric( $kj ) ) {
				
				$sql = "
				
					SELECT *
					FROM admin_model
					WHERE model_id = '". $kj ."'
				";
			}
			else {
				
				$sql = "
				
					SELECT *
					FROM admin_model
					WHERE model_alias = '". $kj ."'
				";
				
			}
			
			foreach( $dao->fetchAll( $sql ) as $ko => $vo ) {
				
				$sqlUnion[] = "
					SELECT
						'". $va->user_name ."' as user_name,
						'". $vj->time ."' as timffe,
						'". $vo->model_id ."' as model_id,
						'". $vo->model_alias ."' as model_alias,
						'". $va->user_id ."' as user_id,
						'". $vo->model_title ."' as title,
						'". $vo->new_config_id ."' as new_config_id
				";
			}
		}
		
		
	}
	//LAST_DAY( timffe ) as fad,
	
	$sql = "
		SELECT 
			
			timffe,
			model_id,
			model_alias,
			title,
			user_name,
			user_id
		FROM ( ". implode( ' UNION ', $sqlUnion ) ." ) as new_tb
		order by timffe desc	
	";
	
	$tables[] = '
		<div style="margin: 10px;" class="contain-flexme3">
			<div class="po-re pd-10-bd" style="padding: 0; border: none;">
				<center><b>การใช้โปรแกรมล่าสุด</b></center>
				<div role="tabpanel" class="tab-pane fade in " id="tab-117" style="">
				
				'. getTable____( $dao->fetchAll( $sql ) ) .'
	 
				</div>

			</div>
		</div>
	';
	
	
	$sql = "
		SELECT 
			new_config_id, 
			model_id, 
			model_alias, 
			
			MAX( timffe ) as maxtimffe
		FROM ( ". implode( ' UNION ', $sqlUnion ) ." ) as new_tb
		GROUP BY model_id
		order by maxtimffe ASC	
	";
	/*
	$tables[] = '
		<div style="margin: 10px;" class="contain-flexme3">
			<div class="po-re pd-10-bd" style="padding: 0; border: none;">
				<center><b>เพิ่มสิทธิบริษัท</b></center>
				<div role="tabpanel" class="tab-pane fade in " id="tab-117" style="">
				
				'. getTable____( $dao->fetchAll( $sql ) ) .'
	 
				</div>

			</div>
		</div>
	';
	
	*/
	
	
	return ''. implode( '<br>', $tables ) .'';
	

}

function makeFrontZero( $number, $require_zero = 2 ) {
	
	return str_pad( $number, $require_zero, 0, STR_PAD_LEFT );
	 
}

//
//
function getNumFormat( $num, $comma = ',', $dot = 2 ) {

	$num = str_replace( ',', '', $num );

	if ( !is_numeric( $num ) ) {
		return number_format( 0, $dot, '.', $comma );
	}

	return number_format( $num, $dot, '.', $comma );
}


//
//
function select( $check, $val, $true = 'selected', $false = NULL ) {

	if( $check == $val )
		return $true;

	return $false;
}

//
//
function getDesc( $va, $str_desc ) {

	if ( empty( $va ) ) {

		return '';
	}

	if ( empty( $str_desc ) )
		return '';

	$arrayReplace = array();
	
	foreach ( $va as $kb => $vb ) {
		
		$arrayReplace['['. $kb .']'] = $vb;
	}

	return str_replace( array_keys( $arrayReplace ), $arrayReplace, $str_desc );

}


//
//
function getTrOnStep( $columns, $printDoc = false ) {

	$maxRow = 0;
	$c = array();
	foreach ( $columns as $ka => $va ) {

		$va = convertObJectToArray( $va );

		$va['merg'] = 1;

		$va['column_name'] = $ka;

		if ( isset( $va['show'] ) && $va['show'] == 0 )
			continue;
		
		if( $printDoc == true ) {
			if ( isset( $va['show_on_doc'] ) && $va['show_on_doc'] == 0 )
				continue;
			
		}
		
		

		$ex = explode( '|', $va['label'] );

		if ( count( $ex ) > $maxRow )
			$maxRow = count( $ex );

		$c[] = $va;

	}

	for ( $i = ( $maxRow - 1 ); $i >= 0; --$i ) {

		$parents = array();
		foreach ( $c as $kc => $vc ) {

			$kc = $vc['label'];

			if ( !isset( $h[$kc] ) )
				$h[$kc] = 1;

			$ex = explode( '|', $kc );

			$h_ = $h[$kc];
			if ( isset( $ex[$i] ) ) {

				$label = $ex[$i];

				unset( $ex[$i] );

				$vc['label'] = $name = implode( '|', $ex );

			}
			else {

				$label = '';

				$name = $kc;

				$h[$kc] += 1;

			}

			$labels[$i][] = array( 'label' => $label, 'w' => $vc['w'], 'h' => $h_, 'merg' => $vc['merg'] );


			if ( !isset( $parents[$name] ) ) {
				$parents[$name] = $vc;
				$parents[$name]['w'] = 0;
				$parents[$name]['merg'] = 0;
			}

			$parents[$name]['w'] += $vc['w'];

			$parents[$name]['merg'] += $vc['merg'];

		}

		$c = $parents;
	}

	ksort( $labels );

	return $labels;

}


//
//
function getUserDepartment( $user_id, $page_id, $tableColumns = array()  ) {

	$dao = getDb();

	$sql = "
		SELECT
			*
		FROM admin_user_page
		WHERE user_id = ". $user_id ."
		AND page_id = " . $page_id;


	$res = $dao->fetch( $sql );


	if ( !$res ) {

		$sql = "

			SELECT
				SUM( add_row ) as add_row,
				SUM( edit ) as edit,
				SUM( delete_row ) as delete_row,
				GROUP_CONCAT( inspect ) as inspect,
				GROUP_CONCAT( prove ) as prove,
				GROUP_CONCAT( views_department_id ) as views_department_id,
				GROUP_CONCAT( views_book_id ) as views_book_id

			FROM admin_group_page WHERE group_id IN (

				SELECT
					group_id
				FROM admin_user_group

				WHERE user_id = ". $user_id ."
			)
			AND page_id = ". $page_id ."

		";
//arr( $sql );
		$res = $dao->fetch( $sql );
	}


	$arr = array(
		'department_id',
		'book_id'
	);

	//arr($tableColumns);

	foreach ( $arr as $ka => $va ) {

		$view = 'views_' . $va;

		$filters[$view] = '';


		if ( in_array( $va, $tableColumns ) ) {


			if ( empty( $res->$view ) )
				$filters[$view] = $va ." IS NULL";

			else {

				$ex = explode( ',', str_replace( array( '[', ']' ), '', $res->$view ) );

				if ( !in_array( 'all', $ex ) )
					$filters[$view] = $va ." IN ( '". implode( "','", $ex ) ."' )";
			}
		}

	}

	//arr( $filters);

	return $filters;
}


//
//
function getFilterByType( $param ) {


//arr($param);
	$keep = array();
	foreach ( $param as $ka => $va ) {
		//arr( $va );
		if ( $va->type == 'session' ) {
			if ( $va->name == 'user_id' ) {

				$va->name = Uid;
			}
//arr($_SESSION);
			if ( is_object( $_SESSION[$va->name] ) ) {

				$f = $va->f;
				$keep[$ka] = $_SESSION[$va->name]->$f;
			}
			else {

				$keep[$ka] = $_SESSION[$va->name];
			}
		}
		else if ( $va->type == 'rq' ) {
			if ( isset( $_REQUEST[$va->name] ) )
				$keep[$ka] = str_replace( ' ', '%', $_REQUEST[$va->name] );
		}
		else if ( $va->type == 'parameter' ) {
			$keep[$ka] = $_REQUEST[$va->name];
		}
		else if ( $va->type == 'sql' ) {

			$keep[$ka] = $dao->fetch( $va->name )->t;

		}
		else if ( $va->type == 'txt' ) {

			$keep[$ka] = $va->name;
		}
		else if ( $va->type == 'function' ) {

			$func_param = array();
			foreach( $va->param as $kb => $vb ) {

				$func_param[$kb] = $vb;
			}

			$keep[$ka] = call_user_func( $va->name, $func_param );
		}
		else if ( $va->type == 'condition' ) {
//arr( $va );
			//$func_param = array();
			foreach( $va->name as $kb => $vb ) {

				 $keep[$ka][] = $vb;
			}

			
		}

	}
	
	//arr( $keep );

	return $keep;
}





//
function loadImg( $location ) {


	if ( empty( $location ) || !file_exists( FILE_FOLDER .'/' . $location ) )
		return ;

	return '<span style="width: 25px; margin-right: 10px; display: inline-block;"><img class="img-circle"  data-target="#myModalPop" data-toggle="modal" src="'. FILE_URL . '/' . $location .'" style="width: 100%;"></span>';

}




function protectDtIdInBackup( $param = array() ) {
	
	$dao = getDb();
	
	$data = $param['data'];
	

	
	
	
	if( $param['action_type'] == 'delete' ) {
	//if( true ) {
		//arr( $param['parent_id'] );
		foreach( $param['data']['to_db'] as $ka => $va ) {
			
			//arr( $va );
			
			$sql = "
			
				SELECT *
				FROM aa.erp_stock_dt 
				WHERE parent_id IN ( ". $va['id'] ." )
			
			
			";
			
		//arr( $sql );
			foreach( $dao->fetchAll( $sql ) as $kz => $vz ) {
				
				
				$data['message'] = ' ไม่สามารถลบข้อมูลได้ ';

				$data['field']['product_id'] = $data['message'];

				$data['success'] = 0;

				return $data;
			}
			
			
		}
		
		
	}
	
	return $data;
	
	
}


function protectProduct( $param = array() ) {
	
	$dao = getDb();
	
	$data = $param['data'];
	
	
	
	if( $param['action_type'] == 'delete' ) {
	//if( true ) {
		//arr( $param['parent_id'] );
		foreach( $param['data']['to_db'] as $ka => $va ) {
			
			$sql = "
			
				SELECT
					new_tb.*
				FROM (
					(
						SELECT 
							CONCAT( id, ' ', doc_no ) as doc_nos
						FROM erp_stock_dt
						WHERE product_id IN ( ". $param['parent_id'] ." )
						LIMIT 0, 1
					) 
					UNION
					(
						SELECT 
							CONCAT( id, ' ', doc_no ) as doc_nos
						FROM erp_sale_order_dt
						WHERE product_id IN ( ". $param['parent_id'] ." )
						LIMIT 0, 1
					)  
				
				) as new_tb	
				where doc_nos IS NOT NULL
			";
			
			//arr( $sql );
			foreach( $dao->fetchAll( $sql ) as $kz => $vz ) {
				
				
				$data['message'] = ' ไม่สามารถลบข้อมูลได้  เนื่องจากเอกสาร '. $vz->doc_nos .'';

				$data['field']['product_code'] = $data['message'];

				$data['success'] = 0;

				return $data;
			}
			
			
		}
		
		
	}
	
	return $data;
	
	
}


function protectCompany( $param = array() ) {
	
	$dao = getDb();
	
	$data = $param['data'];
	
	if( $param['action_type'] == 'delete' ) {
	//if( true ) {
		//arr( $param['parent_id'] );
		foreach( $param['data']['to_db'] as $ka => $va ) {
			
			$sql = "
			
				SELECT
					new_tb.*
				FROM (
					(
						SELECT 
							CONCAT( doc_no ) as doc_nos
						FROM erp_stock
						WHERE company_id IN ( ". $param['parent_id'] ." )
						LIMIT 0, 1
					) 
					UNION
					(
						SELECT 
							CONCAT( doc_no ) as doc_nos
						FROM erp_sale_order
						WHERE company_id IN ( ". $param['parent_id'] ." )
						OR
						receive_id IN ( ". $param['parent_id'] ." )
						LIMIT 0, 1
					)  
					UNION
					(
						SELECT 
							CONCAT( doc_no ) as doc_nos
						FROM erp_ap_pay
						WHERE company_id IN ( ". $param['parent_id'] ." )
						LIMIT 0, 1
					)  
				
				) as new_tb	
				where doc_nos IS NOT NULL
			";
			
			//arr( $sql );
			foreach( $dao->fetchAll( $sql ) as $kz => $vz ) {
				
				if( 1 ) {
					$data['message'] = ' ไม่สามารถลบข้อมูลได้  เนื่องจากเอกสาร '. $vz->doc_nos .'';

					$data['field']['company_code'] = $data['message'];

					$data['success'] = 0;

					return $data;
				}
				
			}
			
			
		}
		
		
	}
	
	return $data;
	
	
}



//
//
function checkTradeOut( $param ) {

	$dao = getDb();

	$data = $param['data'];
	
	foreach( $param['data']['to_db'] as $kd => $vd ) {
		
		$sql = "
		
			SELECT
				product_id
			FROM (
			
				SELECT
					". $vd['product_id'] ." as product_id
			) as new_tb	
			WHERE product_id IN (
			
				SELECT
					dt.product_id
				FROM erp_stock_dt dt
				WHERE dt.parent_id = ". $param['parent_id'] ."
				AND dt.tbName = '". $param['tbNameCheck'] ."'
				GROUP BY 
					dt.product_id
			)
		";
		
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
			
			$data['success'] = 0;

			$data['field']['product_id'] = 'ไม่สารถรับโอนรายรายการเดียวกับเบิกได้';

			$data['message'] = $data['field']['product_id'];
			
		}
		
		if( $param['action_type'] == 'add' && $param['tbName'] == 'trade_in' ) {
		
			$sql = "
				SELECT
					count( * ) as t
				
				FROM erp_stock_dt dt
				
				WHERE dt.parent_id = ". $param['parent_id'] ."
				AND dt.tbName = '". $param['tbName'] ."'
				HAVING t > 0 
			";
			
			foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
				
				$data['success'] = 0;

				$data['field']['product_id'] = 'สามารถเลือกได้แค่รายการเดียว';

				$data['message'] = $data['field']['product_id'];
				
			}
			
		}
		
		
	}
	
	return $data;
	
}




//
//
function checkErpGl( $param ) {


//arr( $param  );
	$data = $param['data'];
	
	if( $param['action_type'] == 'add' ) {
		
		return $data;
	}
	
	
	$k_tb_name = $param['k_tb_name'];
	$main_id = $param['main_id'];
	$action_type = $param['action_type'];

	$dao = getDb();

	$gl_id = $data['to_db'][$k_tb_name]['gl_parent_id'];

	for ( $i =1; $i <= 20; ++$i ) {

		if ( empty( $gl_id ) )
			break;

		$sql = "
			SELECT
				gl_parent_id
			FROM erp_gl
			WHERE id = ". $gl_id ."
		";
		
	

		$res = $dao->fetch( $sql );


		$gl_parent_id = NULL;

		if ( $res )
			$gl_parent_id = $res->gl_parent_id;


		if ( $gl_parent_id == $main_id ) {

			$data['success'] = 0;

			$data['field']['gl_parent_id'] = 'เป็นบัญชีย่อยของบัญชีนี้';

			$data['message'] = 'Please Check';

			break;
		}

		$gl_id = $gl_parent_id;
	}

	if ( $data['to_db'][$k_tb_name]['gl_type_id'] == 2 ) {

		$sql = "
			SELECT
				COUNT( * ) as t
			FROM erp_gl
			WHERE gl_parent_id = ". $main_id ."
		";
		
		//arr( $sql );

		$res = $dao->fetch( $sql );

		if ( $res->t > 0 ) {

			$data['success'] = 0;

			$data['field']['gl_type_id'] = 'มีบัญชีย่อยอยู่ภายใน';

			$data['message'] = 'Please Check';
		}
	}


	return $data;
}



//
//
function checkOrder( $param ) {
//arr( $param['main_data_before']->book_id );

	///arr( $param['beforeUpdate'] );

	$dao = getDb();

	$data = $param['data'];

	return $data;
	foreach( $data['to_db'] as $kd => $vd ) {

		if( $param['action_type'] != 'delete' ) {

			if( isset( $param['updateFrom'] ) ) {

				if( isset( $param['main_data_before'] )  ) {

					foreach( $param['main_data_before'] as $km => $vm ) {

						if( $vm != $vd[$km] ) {

							if( in_array( $km, array( 'vat_type' ) ) ) {

								$sql = "
									SELECT
										dt.doc_no as doc_no
									FROM erp_stock_dt dt
									WHERE dt.tbName LIKE 'erp_sale_inv'
									AND dt.lock_parent_id = ". $vd['id'] ."
									LIMIT 0, 1
								";

								foreach( $dao->fetchAll( $sql ) as $kv => $vv ) {

									$data['message'] = 'มีข้อมูลที่เปิดใบกำกับภาษีที่เอกสาร ' . $vv->doc_no . ' กรุณาทำการลบรายการก่อนทำการแก้ไข';

									$data['field']['vat_type'] = $data['message'];

									$data['success'] = 0;

									return $data;
								}
							}
						}

					}
				}

				$sql = "
					SELECT
						saleman_id as t
					FROM erp_company_dt
					WHERE parent_id = ". $vd['company_id'] ."
					AND book_id = ". $vd['book_id'] ."
				";

				$res = $dao->fetch( $sql );
				
				if( !$res ) {
					
				 
				}
			}
			else {
				//arr( $param );
				
				if( $vd['is_not_check_stock'] == 0 ) {
					
					$sql = "
					
						SELECT 
							dd.book, 
							dd.product_code,
							dd.product_name,
							dd.product_grade, 
							dd.product_pack, 
							dd.product_id, 
							dd.color, 
							dd.mid,
							dd.total_qty,
							dd.so_qty,
							IFNULL( dd.total_qty, 0 ) - ( dd.so_qty ) as avilable,
							dd.saleable
						FROM (
						
							SELECT 
								new_tb.product_code,
								new_tb.product_name,
								new_tb.product_grade, 
								new_tb.product_id,
								new_tb.product_pack,
								new_tb.saleable, 
								new_tb.book,
								new_tb.book_id,
								new_tb.color,
								new_tb.mid,
								new_tb.total_qty,
								IFNULL( (
									SELECT 
										SUM( waiting_qty )
									FROM erp_sale_order_dt 
									WHERE product_id = new_tb.product_id
									AND color = new_tb.color
									AND admin_company_id = 1
									AND book_id = new_tb.book_id
								), 0 ) so_qty
								FROM (
							
									SELECT 
										l.product_code,
										l.product_name,
										l.product_grade, 
										l.product_id,
										l.product_pack,
										l.saleable, 
										stb.book,
										stb.book_id,
										stb.color,
										CONCAT( l.product_id, '-', replace( IFNULL( stb.color, '' ), '-', '****' ) ) AS mid,
										SUM( stb.qty * ( stb.admin_company_id = 1 ) ) as total_qty
									FROM erp_product l
									lEFT JOIN erp_stock_bal_zone stb ON l.product_id = stb.product_id 
									[WHERE]
									GROUP BY 
										l.product_id,
										stb.book_id,
										stb.color	
								) as new_tb	
							) as dd
							ORDER BY 
								dd.saleable DESC,
								dd.product_code asc , 
								dd.product_name asc , 
								dd.product_grade asc,
								dd.color asc,
								dd.book ASC
					";
					
					$filters = array();
					
					$filters['WHERE'][] = "l.product_id = ". $vd['product_id'] ."";
					$filters['WHERE'][] = "stb.book_id = ". $param['main_data_before']->book_id ."";
					$filters['WHERE'][] = "stb.color = '". $vd['color'] ."'";
					$filters['admin_company_id'] = 1;
					
					$sql = genCond_( $sql, $filters );
					
					//arr( $sql );
					$res = $dao->fetch( $sql );
					
					//arr( $res );
					$avilable[$vd['product_id']][$vd['color']][] = 0;
					if( $res ) {
						$avilable[$vd['product_id']][$vd['color']][] = $res->avilable;
					}
					
					if( $param['action_type'] == 'edit' ) {
						
						$avilable[$param['beforeUpdate']->product_id][$param['beforeUpdate']->color][] = $param['beforeUpdate']->qty;
					}
					
					//arr( $vd );
					
					
					$orderQty = $vd['qty_um'] * $vd['qty_rate'];
					$avilableQty = array_sum( $avilable[$vd['product_id']][$vd['color']] ) ;
					
					if( $orderQty > $avilableQty ) {
/*
						$data['message'] = 'ขายได้เพียง ' . ( $avilableQty / $vd['qty_rate'] ) . $vd['um_label'];
						$data['field']['qty_um'] = $data['message'];
						$data['success'] = 0;
						return $data;*/
					}			
				}
			}
		}

		if( $param['action_type'] != 'add' ) {

			if( isset( $param['updateFrom'] ) ) {

				if( $param['action_type'] == 'delete' ) {

					$sql = "
						SELECT
							dt.doc_no as doc_no
						FROM erp_stock_dt dt
						WHERE dt.tbName LIKE 'erp_sale_inv'
						AND dt.lock_parent_id = ". $vd['id'] ."
						LIMIT 0, 1
					";

					foreach( $dao->fetchAll( $sql ) as $kv => $vv ) {

						$data['message'] = 'มีข้อมูลที่เปิดใบกำกับภาษีที่เอกสาร ' . $vv->doc_no . ' กรุณาทำการลบรายการก่อนทำการแก้ไข';
						$data['field']['doc_no'] = $data['message'];
						$data['success'] = 0;
						return $data;
					}

					$data['success'] = 1;
					return $data;
				}
			}
			else {

				if( $param['action_type'] == 'delete' ) {
					
					$vd['qty_um'] = 0;
				}

				$sql = "
					SELECT
						SUM( dt.qty ) as t,
						GROUP_CONCAT( DISTINCT dt.doc_no ) as doc_no,
						( SELECT stock_um FROM erp_product WHERE product_id = dt.product_id ) as um_label
					FROM erp_stock_dt dt
					WHERE dt.tbName LIKE 'erp_sale_inv'
					AND dt.lock_dt_id = ". $vd['id'] ."
				";

				foreach( $dao->fetchAll( $sql ) as $kv => $vv ) {

					if( empty( $vv->t ) ) {

						continue;
					}

					$checks = array( 'round_two_digit', 'sale_sqm', 'um_label', 'price', 'discount' );

					foreach( $checks as $kc => $vc ) {


	
	
						if( $param['beforeUpdate']->$vc != $vd[$vc] ) {

							if( false ) {
								
								$data['success'] = 0;

								$data['message'] = 'ไม่สามารถเปลี่ยนแปลงรายการได้เนืองจากมีการส่งสินค้าแล้ว  fdfd ' . '('. $vv->doc_no .')';

								$data['field'][$vc] = $data['message'];

								return $data;
							}
						}

					}


					if( $vv->t > $vd['qty_um'] *  $vd['qty_rate']  ) {

						$data['message'] = 'ส่งไปแล้ว ' . $vv->t . $vv->um_label . ' ' . $vv->doc_no;
						$data['field']['qty_um'] = $data['message'];
						$data['field']['um_label'] = $data['message'];
						$data['success'] = 0;
						
						return $data;
						
					}
					
				}
			}
		}

		$data['success'] = 1;

		return $data;
	}
}



//
//
function checkScn( $param ) {


	$data = $param['data'];

	foreach( $param['data']['to_db'] as $kd => $vd ) {

		if( !empty( $vd['lock_parent_id'] ) && !empty( $vd['old_scn_id'] ) ) {


			$data['message'] = ' สามารถเลือกรายการใบแจ้งหนี้ได้รายการเดียว';

			$data['field']['lock_parent_id'] = $data['message'];
			$data['field']['old_scn_id'] = $data['message'];

			$data['success'] = 0;

			return $data;
		}
		if( empty( $vd['lock_parent_id'] ) && empty( $vd['old_scn_id'] ) ) {


			$data['message'] = ' กรุณาเลือกรายการใบแจ้งหนี้อย่างใดอย่างหนึง 1รายการ';

			$data['field']['lock_parent_id'] = $data['message'];
			$data['field']['old_scn_id'] = $data['message'];

			$data['success'] = 0;

			return $data;
		}



	}

	return $data;

}




//
//
function checkInsertParentStock( $param ) {

	$dao = getDb();

	$data = $param['data'];


	return $data;
	if( in_array( $param['action_type'], array( 'add' ) ) ) {

		return $data;
	}

	foreach( $param['data']['to_db'] as $kd => $vd ) {

		$sql = "
			SELECT
				*
			FROM erp_stock_dt
			WHERE parent_id = ". $vd['id'] ."
			LIMIT 0, 1
		";

		foreach( $dao->fetchAll( $sql ) as $kt => $vt ) {


			if( $vd['lock_parent_id'] != $param['main_data_before']->lock_parent_id ) {

				$data['field']['lock_parent_id'] = 'ไม่สามารถเปลี่ยนเอกสารได้ เนื่องจากมีรายการที่ส่งผลต่อสต็อค';

				$data['message'] = 'ไม่สามารถเปลี่ยนเอกสารได้ เนื่องจากมีรายการที่ส่งผลต่อสต็อค';

				$data['success'] = 0;

				return $data;
			}

			if( $vd['book_id'] != $param['main_data_before']->book_id ) {


				$data['message'] = 'ไม่สามารถเปลี่ยนเอกสารได้ เนื่องจากมีรายการที่ส่งผลต่อสต็อค';

				$data['field']['book_id'] = $data['message'];


				$data['success'] = 0;

				return $data;
			}
		}

		if( $vd['doc_date'] == $param['main_data_before']->doc_date  AND in_array( $param['action_type'], array( 'edit' ) ) ) {


			//$vd['doc_no'] = 'ddsdsfsdfa';
			$sql = "
				UPDATE erp_stock_dt dt
				SET
					dt.doc_no = '". $vd['doc_no'] ."'
				WHERE dt.parent_id = ". $param['parent_id'] ."

			";

			$dao->execDatas( $sql );

			return $data;
		}

		$date = MIN( array( $param['main_data_before']->doc_date, $vd['doc_date'] ) );

		$sql = "
			SELECT
				dt.product_id,
				IF( dt.parent_id = ". $param['parent_id'] .", '". $vd['doc_date'] ."', dt.doc_date ) as chgDate,
				IF( dt.parent_id = ". $param['parent_id'] .", '". $vd['doc_no'] ."', dt.doc_no ) as chgDocNo,
				CONCAT( dt.admin_company_id, '-', dt.product_id, '-', dt.color, '-', dt.zone_id, '-', dt.book_id ) as product,
				SUM( dt.qty * dt.factor ) as total_qty
			FROM erp_stock_dt dt
			[WHERE]
			GROUP BY
				product_id,
				chgDate,
				chgDocNo,
				product
			ORDER BY
				chgDate ASC,
				dt.order_number ASC
		";

		$replace['WHERE'][] = "dt.doc_date >= '". $date ."'";

		if( in_array( $param['action_type'], array( 'delete' ) ) ) {

			$replace['WHERE'][] = "dt.parent_id != ". $param['parent_id'];

			$replace['WHERE'][] = "dt.product_id IN (

				SELECT
					product_id
				FROM erp_stock_dt WHERE parent_id = ". $param['parent_id'] ."
			)";
		}
		else if( in_array( $param['action_type'], array( 'edit' ) ) ) {

			$replace['WHERE'][] = "dt.product_id IN (

				SELECT
					product_id
				FROM erp_stock_dt WHERE parent_id = ". $param['parent_id'] ."
			)";
		}

		$sql = genCond_( $sql, $replace );

	//arr( $sql );
		$_SESSION['productsUpdateStock'] = array();
		foreach( $dao->fetchAll( $sql ) as $ka => $vr ) {
			/*
			if( !isset( $total_qty_bal[$vr->product] ) ) {

				$sql = "
					SELECT
						pcz_qty_bal
					FROM erp_stock_dt dt
					WHERE CONCAT( dt.admin_company_id, '-', dt.product_id, '-', dt.color, '-', dt.zone_id, '-', dt.book_id ) = '". $vr->product ."'
					AND dt.doc_date < '". $date ."'
					ORDER BY order_number DESC
					LIMIT 0, 1
				";

				$total_qty_bal[$vr->product] = 0;
				foreach( $dao->fetchAll( $sql ) as $kp => $vp ) {

					$total_qty_bal[$vr->product] = $vp->pcz_qty_bal;
				}

			}

			$total_qty_bal[$vr->product] += $vr->total_qty;

			*/
			/*
			if( $total_qty_bal[$vr->product] < 0 ) {


				$sql = "
					SELECT
						*
					FROM erp_product
					WHERE product_id = ". $vr->product_id ."
				";
				foreach( $dao->fetchAll( $sql ) as $kp => $vp ) {

					$data['field']['doc_date'] =  'มี '. $vp->product_code .' สต็อคไม่ถูกต้องเมื่อ ' . $vr->chgDate . ' จำนวน ' . $total_qty_bal[$vr->product] . ' เอกสาร '. $vr->chgDocNo;

					$data['message'] = $data['field']['doc_date'];
				}


				$data['success'] = 0;

				return $data;
			}
			*/
			$_SESSION['productsUpdateStock'][$vr->product_id] = $vr->product_id;
		}

		$_SESSION['productsUpdateStock'][9999999] = 9999999;

		$sql = "
			SELECT
				product_id
			FROM erp_stock_dt
			WHERE parent_id = ". $param['parent_id'] ."
			GROUP BY product_id

		";

		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {

			//arr( $va );

			$_SESSION['productsUpdateStock'][$va->product_id] = $va->product_id;
		}


		$sql = "
			UPDATE erp_stock_dt dt
			SET
				dt.doc_date = '". $vd['doc_date'] ."',
				dt.doc_no = '". $vd['doc_no'] ."'
			WHERE dt.parent_id = ". $param['parent_id'] ."
		";

		$dao->execDatas( $sql );
	}

	//arr($_SESSION['productsUpdateStock']);

	//echo 'dsfasdasdfsd';
	//exit;




	return $data;

}




//
//
function checkOrder_______( $param ) {

	$dao = getDb();

	$data = $param['data'];

	foreach( $data['to_db'] as $kd => $vd ) {

		//arr( $vd );
		if( $param['action_type'] != 'delete' ) {


			if( isset( $param['updateFrom'] ) ) {

				$sql = "
					SELECT
						saleman_id as t
					FROM erp_company_dt
					WHERE parent_id = ". $vd['company_id'] ."
					AND book_id = ". $vd['book_id'] ."
				";
				//arr( $sql );
				$res = $dao->fetch( $sql );
				if( !$res ) {

					$data['message'] = 'กรุณsadsdsdsdาเพิ่มข้อมูลผู้ขายให้ครบถ้วddsdน' . ' <a target="blank___" class="web-bt" href="'. getLink( 31, array(), array( 'parent_id' => $vd['company_id'] ) ) .'">เพิ่ม</a>';

					$data['field']['company_id'] = $data['message'];

					$data['success'] = 0;

					return $data;
				}


			}


			//exit;

		}

		if( $param['action_type'] != 'add' ) {

			if( isset( $param['updateFrom'] ) ) {




				$sql = "
					SELECT
						dt.doc_no as doc_no
					FROM erp_stock_dt dt
					WHERE dt.tbName LIKE 'erp_sale_inv'
					AND dt.lock_parent_id = ". $vd['id'] ."
					LIMIT 0, 1

				";

				//arr( $sql );

				foreach( $dao->fetchAll( $sql ) as $kv => $vv ) {

					$data['message'] = 'มีข้อมูลที่เปิดใบกำกับภาษีที่เอกสาร ' . $vv->doc_no . ' กรุณาทำการลบรายการก่อนทำการแก้ไข';

					$data['field']['doc_no'] = $data['message'];

					$data['success'] = 0;

					return $data;
				}


				$data['success'] = 1;

				return $data;


			}
			else {


				if( $param['action_type'] == 'delete' ) {
					$vd['qty_um'] = 0;
				}

				$sql = "
					SELECT
						SUM( dt.qty ) as t,
						GROUP_CONCAT( DISTINCT dt.doc_no ) as doc_no,
						( SELECT stock_um FROM erp_product WHERE product_id = dt.product_id ) as um_label
					FROM erp_stock_dt dt
					WHERE dt.tbName LIKE 'erp_sale_inv'
					AND dt.lock_dt_id = ". $vd['id'] ."
				";

			//arr( $sql );

				foreach( $dao->fetchAll( $sql ) as $kv => $vv ) {

					if( empty( $vv->t ) ) {

						continue;
					}

					if( isset( $vd['product_color'] ) && $vd['product_color'] != $param['beforeUpdate']->product_color ) {


						$data['message'] = 'ไม่สามารถเปลี่ยนแปลงรายการได้เนืองจากมีการส่งสินค้าแล้ว  ' . $vv->t . $vv->um_label . ' ' . $vv->doc_no;

						$data['field']['product_color'] = $data['message'];

						$data['success'] = 0;

						return $data;
					}


					if( $vv->t > $vd['qty_um'] *  $vd['qty_rate']  ) {

						$data['message'] = 'ส่งไปแล้ว ' . $vv->t . $vv->um_label . ' ' . $vv->doc_no;

						$data['field']['qty_um'] = $data['message'];
						$data['field']['um_label'] = $data['message'];


						$data['success'] = 0;

						return $data;
					}

				}
			}

		}

		$data['success'] = 1;

		return $data;


		if( $vd['is_not_check_stock'] == 1 ) {

		}

		$sql = "
			SELECT
				IFNULL( dd.total_qty, 0 ) - ( IFNULL( dd.so_qty, 0 ) - IFNULL( dd.send_qty, 0 ) ) as avilable,
				( SELECT stock_um FROM erp_product WHERE product_id = dd.product_id ) as stock_um
			FROM (
				SELECT
					new_tb.book_id,
					new_tb.product_id,
					new_tb.color,
					SUM( new_tb.qty ) as total_qty,
					IFNULL( (
						SELECT
							SUM( qty ) - SUM( cancel_qty ) as qty
						FROM erp_sale_order_dt dt
						LEFT JOIN erp_sale_order o ON dt.parent_id = o.id
						[WHERE]
					), 0 ) as so_qty,
					SUM( new_tb.send_qty ) as send_qty
				FROM (
					SELECT
						dt.book_id,
						dt.product_id,
						dt.color,
						SUM( dt.qty * dt.factor ) AS qty,
						SUM( dt.qty * ( dt.tbName = 'erp_sale_inv' ) ) AS send_qty
					FROM erp_stock_dt dt
					WHERE dt.product_id = ". $vd['product_id'] ."
					AND dt.color = '". $vd['color'] ."'
					AND dt.admin_company_id = 1
					GROUP BY
						book_id,
						product_id,
						color
				) as new_tb
				GROUP BY
					book_id,
					product_id,
					color
			) as dd
		";

		$filters = array();


		if( !empty( $vd['id'] ) ) {

			$filters['WHERE'][] = "dt.id != ". $vd['id'];

		}

		$filters['WHERE'][] = "
			dt.product_id = new_tb.product_id
			AND dt.color = new_tb.color
			AND o.book_id = new_tb.book_id

		";

		$sql = genCond_( $sql, $filters );

		//
		//
		foreach( $dao->fetchAll( $sql ) as $kk => $vk ) {

			if( ( $vd['qty_um'] * $vd['qty_rate'] ) > $vk->avilable ) {

				$data['field']['qty_um'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

				$data['message'] = 'ขายได้ ' . $vk->avilable . ' '. $vk->stock_um ;

				$data['success'] = 0;

				break;
			}
		}

		return $data;
	}
}

//
//
function checkCreateUmiFile( $param ) {

	$dao = getDb();

	$data = $param['data'];

	foreach( $param['data']['to_db'] as $kd => $vd ) {
	//	arr( $vd );
		$sql = "
			SELECT
				IF( o.book_id = 38, 1199, 1299 ) as warehouse,
				date_format( dt.doc_date, '%d.%m.%Y' ) as doc_date,
				dt.doc_no,
				c.umi_code as custom_name,
				c.company_code as Ccompany_code,
				rc.company_code as Rcompany_code,
				rc.umi_code as receive_name,
				o.remark,
				p.umi_code as code,
				p.product_code as product,
				dt.color,
				ROUND( dt.qty_um, 0 ) as qty_um,
				dt.parent_id,
				o.sim_no,
				o.cancel_sim_no,
				o.umi_so_no
			FROM erp_sale_order_dt dt
			LEFT JOIN erp_sale_order o ON dt.parent_id = o.id
			LEFT JOIN erp_product p ON dt.product_id = p.product_id
			LEFT JOIN erp_product_umi pu ON p.umi_code = pu.id
			LEFT JOIN erp_company c ON o.company_id = c.company_id
			LEFT JOIN erp_company_umi cu ON c.umi_code = cu.id
			LEFT JOIN erp_company rc ON o.receive_id = rc.company_id
			LEFT JOIN erp_company_umi rcu ON rc.umi_code = rcu.id
			[WHERE]
			HAVING
				code IS NULL
				OR receive_name IS NULL
				OR custom_name IS NULL
			ORDER BY
				doc_no ASC
		";

		$filters['WHERE'][] = "dt.color != ''";
		$filters['WHERE'][] = "o.umi_so_no = ''";
		$filters['WHERE'][] = "dt.parent_id = ". $vd['id'] ."";

		$sql = genCond_( $sql, $filters );


		$keep = array();
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {

			$data['success'] = 0;
			$data['field']['doc_no'] = '';
			if( $va->custom_name == '' ) {
				$keep['บริษัท'][$va->Ccompany_code] = $va->Ccompany_code;
			}
			if( $va->receive_name == '' ) {
				$keep['บริษัท'][$va->Rcompany_code] = $va->Rcompany_code;
			}
			if( $va->code == '' ) {
				$keep['สินค้า'][] = $va->product;
			}



		}
		$data['message'] = '';

		foreach( $keep as $kp => $vp ) {

			$data['message'] .= ' | '. $kp .' '. implode( ', ', $vp ) .' ไม่มี umi code';
		}

		//arr( $keep );









		return $data;

	}


	return $data;

}

//
//
function checkInsertStockMove( $param ) {

//echo 'dfsdsadf';
	$dao = getDb();

	$data = $param['data'];

	foreach( $param['data']['to_db'] as $kd => $vd ) {


		$stockConfig = stockConfig( $vd['tbName'] );

		$sqlUnion[] = "
			(
				SELECT
					'a' as gname,
					dt.doc_date,
					CONCAT( dt.product_id, '-', dt.color, '-', dt.zone_id ) as product,
					SUM( dt.qty * dt.factor ) AS total_qty
				FROM erp_stock_dt dt
				[WHERE]
				GROUP BY
					doc_date,
					product
			)
		";

		if( in_array( $param['action_type'], array( 'edit', 'add' ) ) ) {

			if( !isset( $vd['qty_rate'] ) ) {
				$vd['qty_rate'] = 1;

			}

			if( !empty( $vd['zone_in_id'] ) ) {
			//if(true ) {

				$sqlUnion[] = "
					(
						SELECT
							'b' as gname,
							'". $vd['doc_date'] ."' as doc_date,
							CONCAT( ". $vd['product_id'] .", '-', '". $vd['color'] ."', '-', ". $vd['zone_id'] ." ) as product,
							" . ( $vd['qty_um'] * $vd['qty_rate'] * $stockConfig['factor'] ) ." AS total_qty

					)
				";

				$sqlUnion[] = "
					(
						SELECT
							'c' as gname,
							'". $vd['doc_date'] ."' as doc_date,
							CONCAT( ". $vd['product_id'] .", '-', '". $vd['color'] ."', '-', ". $vd['zone_in_id'] ." ) as product,
							" . ( $vd['qty_um'] * $vd['qty_rate'] * 1 ) ." AS total_qty

					)
				";

			}
			else {

				$sqlUnion[] = "
					(
						SELECT
							'd' as gname,
							'". $vd['doc_date'] ."' as doc_date,
							CONCAT( ". $vd['product_id'] .", '-', '". $vd['color'] ."', '-', ". $vd['zone_id'] ." ) as product,
							" . ( $vd['qty_um'] * $vd['qty_rate'] * $stockConfig['factor'] ) ." AS total_qty

					)
				";
			}
		}


		$sqlStock = "
			SELECT
				new_tb.doc_date,
				new_tb.product,
				SUM( new_tb.total_qty ) as total_qty
			FROM (". implode( ' UNION ', $sqlUnion ) .") as new_tb
			GROUP BY
				doc_date,
				product
			ORDER BY
			product ASC ,
				doc_date ASC


		";

		$replace = array();

		if( in_array( $param['action_type'], array( 'edit', 'delete' ) ) ) {

			if( !empty( $vd['move_pare'] ) ) {

				$replace['WHERE'][] = "dt.move_pare != " . $vd['move_pare'];
			}
			else {
				$replace['WHERE'][] = "dt.id != " . $param['main_id'];

			}
		}

		if( $param['action_type'] == 'edit' ) {

			$sql = "

				SELECT
					MIN( new_tb.start_date ) as start_date
				FROM (
					SELECT
						'". $vd['doc_date'] ."' as start_date
					UNION
					SELECT
						doc_date as start_date
					FROM erp_stock_dt
					WHERE id = ". $param['main_id'] ."
				) as new_tb

			";

			$date = $dao->fetch( $sql )->start_date;

		}
		else {

			$date = $vd['doc_date'];
		}


		//$date = '2000-01-01';
		$_SESSION['myStockDate'] = $date;

		if( in_array( $param['action_type'], array( 'add' ) ) ) {

			$product_colors[] = "CONCAT( '". $vd['product_id'] ."-". $vd['color'] ."' ) ";

		}
		else if( in_array( $param['action_type'], array( 'edit' ) ) ) {

			$product_colors[] = "CONCAT( '". $vd['product_id'] ."-". $vd['color'] ."' ) ";

			$product_colors[] = "( SELECT CONCAT( dt.product_id, '-', dt.color ) FROM  erp_stock_dt WHERE id = ". $param['main_id'] ." )";


		}
		else if( in_array( $param['action_type'], array( 'delete' ) ) ) {

			$product_colors[] = "( SELECT CONCAT( dt.product_id, '-', dt.color ) FROM  erp_stock_dt WHERE id = ". $param['main_id'] ." )";


		}

		//$replace['WHERE'][] = "CONCAT( dt.product_id, '-', dt.color ) IN ( ". implode( ',', $product_colors ) ." )";

		//$replace['WHERE'][] = "dt.doc_date >= '". $date ."'";

		$sqlStock = genCond_( $sqlStock, $replace );

	arr( $sqlStock );

		foreach( $dao->fetchAll( $sqlStock ) as $kr => $vr ) {

			//arr( $vr );
		//arr( $vr );
			if( !isset( $total_qty_bal[$vr->product] ) ) {

				$total_qty_bal[$vr->product] = 0;

			}

			$total_qty_bal[$vr->product] += $vr->total_qty;

			if( $total_qty_bal[$vr->product] < 0 ) {

				$data['field']['qty_um'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

				$data['message'] = 'ปริมาณเกินจำนวนในโซน ' . $total_qty_bal[$vr->product];

				$data['success'] = 0;

				return $data;
			}
		}
	}

	return $data;

}

//
//
function checkCancelOrder( $param ) {

	//arr( $param);

	$dao = getDb();

	$data = $param['data'];

	foreach( $data['to_db'] as $kd => $vd ) {

		//arr( $kd );

		if( $kd == 'erp_purchase_order_dt' ) {
			$sql = "
				SELECT
					dt.qty  - ( ". $vd['cancel_qty'] ." + IFNULL( st.send_qty, 0 ) ) as waiting_qty
				FROM erp_purchase_order_dt dt
				LEFT JOIN (
					SELECT
						dt.lock_dt_id,
						SUM( dt.qty ) AS send_qty
					FROM erp_stock_dt dt
					WHERE lock_dt_id = ". $vd['id'] ."
					AND dt.tbName = 'erp_purchase_inv'
					GROUP BY
						lock_dt_id
				) st ON dt.id = st.lock_dt_id
				WHERE dt.id = ". $vd['id'] ."
				HAVING waiting_qty < 0
			";

		}
		else {

			$sql = "
				SELECT
					dt.qty  - ( ". $vd['cancel_qty'] ." + IFNULL( st.send_qty, 0 ) ) as waiting_qty
				FROM erp_sale_order_dt dt
				LEFT JOIN (
					SELECT
						dt.lock_dt_id,
						SUM( dt.qty ) AS send_qty
					FROM erp_stock_dt dt
					WHERE lock_dt_id = ". $vd['id'] ."
					AND dt.tbName = 'erp_sale_inv'
					GROUP BY
						lock_dt_id
				) st ON dt.id = st.lock_dt_id
				WHERE dt.id = ". $vd['id'] ."
				HAVING waiting_qty < 0
			";


		}



	//arr($sql  );
		$filters = array();

		$sql = genCond_( $sql, $filters );

		//
		//
		foreach( $dao->fetchAll( $sql ) as $kk => $vk ) {

			$data['field']['cancel_qty'] = 'จำนวนเกินใบสั่งขาย';

			$data['message'] = 'จำนวนเกินใบสั่งขาย';

			$data['success'] = 0;

			break;

		}

	}

	return $data;

}


//
//
function checkMove( $param ) {

	$dao = getDb();

	$data = $param['data'];

	foreach( $param['data']['to_db'] as $kd => $vd ) {

		if( $param['action_type'] == 'delete' ) {

			$sql = "
				SELECT
					dt.zone_id,
					dt.product_id,
					dt.color,
					SUM( dt.qty * dt.factor ) AS total_qty
				FROM erp_stock_dt dt
				[WHERE]
				GROUP BY
					zone_id,
					product_id,
					color
				HAVING total_qty != 0
			";

			$filters['WHERE'][] = "dt.product_id = ". $vd['product_id'] ."";
			$filters['WHERE'][] = "dt.color = '". $vd['color'] ."'";
			$filters['WHERE'][] = "move_pare != ". $vd['move_pare'] ."";

		}
		else {

			$sql = "
				SELECT
					zone_id,
					product_id,
					color,
					SUM( new_tb.total_qty ) AS total_qty
				FROM (
					(
						SELECT
							dt.zone_id,
							dt.product_id,
							dt.color,
							SUM( dt.qty * dt.factor ) AS total_qty
						FROM erp_stock_dt dt
						[WHERE]
						GROUP BY
							zone_id,
							product_id,
							color
						HAVING total_qty != 0
					)
					UNION
					(
						SELECT
							(
								SELECT
									zone_id
								FROM erp_stock_dt
								WHERE id = ". $vd['lock_dt_id'] ."

							) as zone_id,
							". $vd['product_id'] ." as product_id,
							'". $vd['color'] ."' as color,
							'". ( $vd['qty'] * -1 ) ."' AS total_qty

					)
					UNION
					(
						SELECT
							". $vd['zone_id'] ." as zone_id,
							". $vd['product_id'] ." as product_id,
							'". $vd['color'] ."' as color,
							'". ( $vd['qty'] ) ."' AS total_qty

					)

				) as new_tb
				GROUP BY
					zone_id,
					product_id,
					color

			";

			$filters['WHERE'][] = "dt.product_id = ". $vd['product_id'] ."";
			$filters['WHERE'][] = "dt.color = '". $vd['color'] ."'";

			if( $param['action_type'] == 'edit' ) {
				$filters['WHERE'][] = "move_pare != ". $vd['move_pare'] ."";
			}
		}

		$sql = genCond_( $sql, $filters );

		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {


			if( $va->total_qty < 0 ) {

				$data['field']['qty_um'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

				$data['message'] = 'ปริมาณเกินจำนวนในโซน';

				$data['success'] = 0;


				return $data;
			}

		}


		return $data;

	}
}


//
//
function lockDoc( $param ) {

	$data = $param['data'];

	if( $param['action_type'] != 'add' ) {

		$lockName = isset( $param['lock_name'] )? $param['lock_name']: 'lock_doc';




		$param['main_data_before']->$lockName = ( int ) $param['main_data_before']->$lockName;

		if ( !empty( $param['main_data_before']->$lockName ) ) {


			$data['field']['id'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';


			if ( isset( $param['message'] ) ) {

				$data['message'] = $param['message'];
			}
			else {

				$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสารเกี่ยวข้อง';
			}

			$data['success'] = 0;
		}
	}



	return $data;
}


//
//oem_so_dt
function checkAdminUserPloblem( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$requestMinute = ( $data['to_db'][$k_tb_name]['splite_at_hr'] * 60 ) + $data['to_db'][$k_tb_name]['splite_at_min'];

	$sql = "
		SELECT

			( ( pl.work_hr * 60 ) + ( pl.work_min ) ) as total_time_minute,
			IFNULL( (
				SELECT
					COUNT( * )
				FROM admin_user_ploblem
				WHERE splite_ploblem_id = pl.id
				AND ( ( splite_at_hr * 60 ) + ( splite_at_min ) ) = ". $requestMinute ."
				AND id != ". $param['parent_id'] ."
			), 0 ) as ready_book_time
		FROM admin_user_ploblem pl
		where id = ". $data['to_db'][$k_tb_name]['splite_ploblem_id'] ."

	";

	$res = $dao->fetch( $sql );

	if( $res ) {

		if( $requestMinute >= $res->total_time_minute ) {

			$data['message'] = 'over time';

			$data['success'] = 0;

			$data['field']['splite_at_hr'] = 'over time';
			$data['field']['splite_at_min'] = 'over time';
		}
		else if( $res->ready_book_time > 0 ) {

			$data['message'] = 'this time already book';

			$data['success'] = 0;

			$data['field']['splite_at_hr'] = 'this time already book';
			$data['field']['splite_at_min'] = 'this time already book';
		}
	}

	return $data;
}

//
//104
function checkDocDate( $data, $k_tb_name, $main_id, $param = array() ) {


	$dao = getDb();


	$showColumns = $dao->showColumns( $k_tb_name );


	if ( in_array( 'doc_date', $showColumns ) && in_array( 'doc_no', $showColumns ) ) {

		if ( $param['action_type'] == 'add' ) {
			$sql = "

				SELECT
					MAX( doc_date ) as max_date
				FROM ". $k_tb_name ."
				WHERE doc_no LIKE CONCAT( LEFT( '". $data['to_db'][$k_tb_name]['doc_no'] ."', 9 ), '%' )
			";

			$res = $dao->fetch( $sql );

			/*if ( $data['to_db'][$k_tb_name]['doc_date'] < $res->max_date ) {

				$data['field']['doc_date'] = 'ต้องใช้ วันที่ '. $res->max_date . ' เป็นต้นไป';

				$data['message'] = 'กรอกข้อมูลไม่ถูกต้อง';
			}*/
		}
		else if ( $param['action_type'] == 'edit' ) {


			if ( gettime_( $data['to_db'][$k_tb_name]['doc_date'], 12 ) != gettime_( $param['main_data_before']->doc_date, 12 ) ) {

				$data['field']['doc_date'] = 'เดือน ไม่สัมพันธ์กับเลขที่เอกสาร';
				$data['message'] = 'เดือน ไม่สัมพันธ์กับเลขที่เอกสาร';

				return $data;
			}


			$sql = "
				SELECT
				(
					SELECT
						doc_date
					FROM ". $k_tb_name ."
					WHERE doc_no < '". $data['to_db'][$k_tb_name]['doc_no'] ."'
					AND doc_no LIKE CONCAT( LEFT( '". $data['to_db'][$k_tb_name]['doc_no'] ."', 9 ), '%' )
					ORDER BY doc_no DESC LIMIT 0, 1
				) as before_,
				(
					SELECT
						doc_date
					FROM ". $k_tb_name ."
					WHERE doc_no > '". $data['to_db'][$k_tb_name]['doc_no'] ."'
					AND doc_no LIKE CONCAT( LEFT( '". $data['to_db'][$k_tb_name]['doc_no'] ."', 9 ), '%' )

					ORDER BY doc_no ASC LIMIT 0, 1
				) as after_

			";
			$curdate = $data['to_db'][$k_tb_name]['doc_date'];

			$res = $dao->fetch( $sql );

			if ( !is_null( $res->before_ ) && !is_null( $res->after_ ) ) {

				if ( $curdate < $res->before_ || $curdate > $res->after_ ) {

					$data['field']['doc_date'] = 'require '. gettime_( $res->before_ ) .' - ' . gettime_( $res->after_ );

					$data['message'] = 'วันที่เอกสาร ไม่อยู่ในช่วง';
				}
			}
			else if ( !is_null( $res->before_ ) && is_null( $res->after_ ) ) {

				if ( $curdate < $res->before_ ) {

					$data['field']['doc_date'] = 'after '. gettime_( $res->before_ );

					$data['message'] = 'วันที่เอกสาร ไม่ถูกต้อง';

				}
			}
			else if ( is_null( $res->before_ ) && !is_null( $res->after_ ) ) {

				if ( $curdate > $res->after_ ) {

					$data['field']['doc_date'] = 'before or use '. gettime_( $res->after_ );
					$data['message'] = 'วันที่เอกสาร ไม่ถูกต้อง';

				}
			}


		}
	}


	return $data;
}

//
//oem_so_dt
function checkSaleOrderRequestUnlock ( $param ) {

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];
	//arr( $data );
	//echo 'dsadsdsfa';

	$fileName = str_replace( '.TXT', '', $data['to_db'][$k_tb_name]['doc_no'] ) . '.TXT' ;

	$files[] = '/home/sacuser/link/credit/'. $fileName .'';

	$files[] = 'test/destination_folder/'. $fileName .'';

	if ( !file_exists( $files[0] ) ) {

		$data['message'] = 'ไม่พบไฟล์นี้ในระบบ ต้องทำการ export ใหม่';

		$data['success'] = 0;

		$data['field']['doc_no'] = 'ไม่พบไฟล์นี้ในระบบ ต้องทำการ export ใหม่';
	}

	return $data;
}



//
//oem_so_dt
function checkOemSoDt ( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$arr_product_ids = array();

	if ( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT *
			FROM ". $param['k_tb_name'] ."
			WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
		";

		$row_before_edit = $dao->fetch( $sql );

		$arr_product_ids[] = $row_before_edit->products_dt_id;
	}

	if ( !empty( $data['to_db'][$k_tb_name]['products_dt_id'] ) )
		$arr_product_ids[] = $data['to_db'][$k_tb_name]['products_dt_id'];

	$checkDates[$param['main_data_before']->doc_date] = $param['main_data_before']->doc_date;

	$sql = "
		SELECT
			MAX( doc_date ) as t
		FROM oem_stock_hd

	";
	$res = $dao->fetch( $sql )->t;
	if ( !empty( $res ) )
		$checkDates[$res] = $res;

	$data['success'] = 1;
	foreach ( $checkDates as $kDate => $vDate ) {

		//
		// stock data
		$arrUnion = array();

		$filters = array();
		$filters[] = "a.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )";
		$filters[] = "a.doc_date <= '". $vDate ."'";

		$sql = "
			SELECT
				concat( 'a', a.id ) as id,
				a.products_dt_id,
				( ( a.qty - a.qty_return ) * a.qty_factor ) as stock_qty
			FROM oem_stock_dt a
			[cond]
		";

		$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );





		$filters = array();
		$filters[] = "so.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )";
		$filters[] = "hd.doc_date <= '". $vDate ."'";
		$filters[] = "
			so.id NOT IN (

				SELECT
					IFNULL( so_dt_id, 0 )
				FROM oem_stock_dt
			)
		";

		if ( !empty( $row_before_edit ) )
			$filters[] = "so.id != ". $row_before_edit->id;

		$sql = "
			SELECT
				concat( 'b', so.id ) as id,
				products_dt_id,
				( ( so.qty * -1  ) + so.cancel_qty ) as order_qty
			FROM oem_so_dt so
			LEFT JOIN oem_so_hd hd ON so.so_hd_id = hd.id
			[cond]


		";

		$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );


		//
		// input data

		if ( $param['action_type'] != 'delete' ) {

			if ( !isset( $data['to_db'][$k_tb_name]['qty_return'] ) ) {
				$data['to_db'][$k_tb_name]['qty_return'] = 0;
			}

			$arrUnion[] = "
				SELECT
					concat( 'c' ) as id,
					". $data['to_db'][$k_tb_name]['products_dt_id'] ." as products_dt_id,
					". ( ( $data['to_db'][$k_tb_name]['qty'] - $data['to_db'][$k_tb_name]['qty_return'] ) * -1 ) ." as stock_qty
			";
		}

		$sql = "
			SELECT
				new_tb.products_dt_id,
				SUM( new_tb.stock_qty ) as stock_qty,
				CONCAT( p.code, ' ', p.name, ' ', c.code ) as product
			FROM (". implode( ' UNION ', $arrUnion ) .") as new_tb
			LEFT JOIN oem_products_dt pt ON new_tb.products_dt_id = pt.id
			LEFT JOIN oem_products_color c ON pt.products_color_id = c.id
			LEFT JOIN oem_products p ON pt.products_id = p.id
			GROUP BY
				products_dt_id
			HAVING stock_qty != 0

		";

//arr( $sql );

		$keep = array();
		$totalStock = array();
		foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

			if ( !isset( $totalStock[$va->products_dt_id] ) ) {
				$totalStock[$va->products_dt_id] = 0;
			}

			$totalStock[$va->products_dt_id] += $va->stock_qty;
			if ( $va->stock_qty < 0 ) {

				$data['field']['products_dt_id'] = 1;

				$data['field']['qty'] = 1;

				$data['success'] = 0;

				$keep[$va->product][] = $va->stock_qty;
			}
		}

	//	if ( !empty( $row_before_edit ) )
		//	$filters[] = "a.id != ". $row_before_edit->id;


		foreach ( $keep as $ka => $va ) {

			$data['message'] = implode( ', ', $va ) . ' on ' . $vDate;
		}

		if ( $data['success'] == 0 )
			return $data;
	}

	return $data;
}



//
//oem_so_dt
function checkOemStockDt( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$arr_product_ids = array();

	if ( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT *
			FROM ". $param['k_tb_name'] ."
			WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
		";

		$row_before_edit = $dao->fetch( $sql );

		$arr_product_ids[] = $row_before_edit->products_dt_id;
	}

	if ( !empty( $data['to_db'][$k_tb_name]['products_dt_id'] ) )
		$arr_product_ids[] = $data['to_db'][$k_tb_name]['products_dt_id'];

	$checkDates[$param['main_data_before']->doc_date] = $param['main_data_before']->doc_date;

	$sql = "
		SELECT
			MAX( doc_date ) as t
		FROM oem_stock_hd

	";
	$res = $dao->fetch( $sql )->t;
	if ( !empty( $res ) )
		$checkDates[$res] = $res;

	$data['success'] = 1;
	foreach ( $checkDates as $kDate => $vDate ) {

		//
		// stock data
		$arrUnion = array();
		$filters = array();
		$filters[] = "a.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )";

		if ( !empty( $row_before_edit ) )
			$filters[] = "a.id != ". $row_before_edit->id;

		$filters[] = "a.doc_date <= '". $vDate ."'";

		$sql = "
			SELECT
				concat( 'a', a.id ) as id,
				a.products_dt_id,
				a.zone_id,
				( ( a.qty - a.qty_return ) * a.qty_factor ) as stock_qty
			FROM oem_stock_dt a
			[cond]
		";

		$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );

		//
		// input data

		if ( $param['action_type'] != 'delete' ) {

			if ( !isset( $data['to_db'][$k_tb_name]['qty_return'] ) ) {
				$data['to_db'][$k_tb_name]['qty_return'] = 0;
			}

			$arrUnion[] = "
				SELECT
					concat( 'd' ) as id,
					". $data['to_db'][$k_tb_name]['products_dt_id'] ." as products_dt_id,
					". $data['to_db'][$k_tb_name]['zone_id'] ." as zone_id,
					". ( ( $data['to_db'][$k_tb_name]['qty'] - $data['to_db'][$k_tb_name]['qty_return'] ) * $data['to_db'][$k_tb_name]['qty_factor'] ) ." as stock_qty
			";
		}

		$sql = "
			SELECT
				new_tb.products_dt_id,
				new_tb.zone_id,
				SUM( new_tb.stock_qty ) as stock_qty,
				z.name as zone_name,
				CONCAT( p.code, ' ', p.name, ' ', c.code ) as product
			FROM (". implode( ' UNION ', $arrUnion ) .") as new_tb
			LEFT JOIN oem_products_dt pt ON new_tb.products_dt_id = pt.id
			LEFT JOIN oem_zones z ON new_tb.zone_id = z.id
			LEFT JOIN oem_products_color c ON pt.products_color_id = c.id
			LEFT JOIN oem_products p ON pt.products_id = p.id
			GROUP BY
				products_dt_id,
				zone_id
			HAVING stock_qty != 0

		";

		$keep = array();
		$totalStock = array();
		foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

			if ( !isset( $totalStock[$va->products_dt_id] ) ) {
				$totalStock[$va->products_dt_id] = 0;
			}

			$totalStock[$va->products_dt_id] += $va->stock_qty;
			if ( $va->stock_qty < 0 ) {

				$data['field']['products_dt_id'] = 1;

				$data['field']['qty'] = 1;

				$data['success'] = 0;

				$keep[$va->product][] = $va->zone_name .' '. $va->stock_qty;
			}
		}

		$sql = "
			SELECT
				products_dt_id,
				SUM( ( so.qty - so.cancel_qty ) ) as order_qty
			FROM oem_so_dt so
			LEFT JOIN oem_so_hd hd ON so.so_hd_id = hd.id
			WHERE
			so.id NOT IN (

				SELECT
					IFNULL( so_dt_id, 0 )
				FROM oem_stock_dt
			)
			AND so.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )
			AND hd.doc_date <= '". $vDate ."'
			GROUP BY products_dt_id

		";

		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {


			$stock = 0;
			if ( isset( $totalStock[$va->products_dt_id] ) ) {

				$stock = $totalStock[$va->products_dt_id];
			}
			if ( ( $stock - $va->order_qty ) < 0 ) {

				$keep[$va->products_dt_id][] = 'มีจำนวนในใบสั่งขายเกินกว่าสต็อค';
				$data['field']['qty'] = 1;
			}
		}

		foreach ( $keep as $ka => $va ) {

			$data['message'] = implode( ', ', $va ) . ' on ' . $vDate;
		}

		if ( $data['success'] == 0 )
			return $data;
	}

	return $data;
}



//
//oem_so_dt
function checkOverThanOrder________( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$arr_product_ids = array();

	if ( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT *
			FROM ". $k_tb_name ."
			WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
		";
		$row_before_edit = $dao->fetch( $sql );

		$arr_product_ids[] = $row_before_edit->products_dt_id;
	}

	if ( !empty( $data['to_db'][$k_tb_name]['products_dt_id'] ) )
		$arr_product_ids[] = $data['to_db'][$k_tb_name]['products_dt_id'];

	//
	// sell order
	$filters = array();

	$filters[] = "a.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )";
	if ( !empty( $row_before_edit ) )
		$filters[] = "a.id != ". $row_before_edit->id;

	$sql = "
		SELECT
			'b',
			a.products_dt_id,
			( ( a.qty - a.cancel_qty ) * -1 ) as stock_qty
		FROM oem_so_dt a
		[cond]

	";
	$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );


	//
	// stock data
	$filters = array();
	$filters[] = "a.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )";
	$filters[] = "b.doc_date <= '". $param['main_data_before']->doc_date ."'";
	$filters[] = "a.so_dt_id IS NULL";

	$sql = "
		SELECT
			'a',
			a.products_dt_id,
			( ( a.qty - a.qty_return ) * a.qty_factor ) as stock_qty
		FROM oem_stock_dt a
		LEFT JOIN oem_stock_hd b ON a.stock_hd_id = b.id
		[cond]

	";

	$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );


	if ( $param['action_type'] != 'delete' ) {

		//
		// input data
		$arrUnion[] = "
			SELECT
				'c',
				". $data['to_db'][$k_tb_name]['products_dt_id'] ." as products_dt_id,
				". ( ( $data['to_db'][$k_tb_name]['qty'] - $data['to_db'][$k_tb_name]['cancel_qty'] ) * -1 ) ." as stock_qty
		";
	}

	$sql = "
		SELECT
			new_tb.products_dt_id,
			SUM( new_tb.stock_qty ) as stock_qty
		FROM (". implode( ' UNION ', $arrUnion ) .") as new_tb
		GROUP BY products_dt_id
		HAVING stock_qty != 0

	";
//arr( $sql );

	foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

		if ( $va->stock_qty < 0 ) {

			$data['field'] = $param['field'];
			$data['success'] = 0;
			$data['message'] = $param['message'] . 'ณ วันที่ ' . $param['main_data_before']->doc_date . '(มีของ '. ( ( $data['to_db'][$k_tb_name]['qty'] - $data['to_db'][$k_tb_name]['cancel_qty'] ) + $va->stock_qty ) .')';
		}
	}

	return $data;
}


//
//
function checkCancelQty( $param ) {

	$data = $param['data'];

	//arr( $data );

	$k_tb_name = $param['k_tb_name'];

	if ( $data['to_db'][$k_tb_name][$param['compare'][0]] > ( $data['to_db'][$k_tb_name]['qty'] - $data['to_db'][$k_tb_name]['send_qty'] ) ) {

		$data['field'] = $param['field'];

		$data['message'] = 'ไม่สามารถยกเลิกในจำนวนนี้ได้';
		if ( isset( $param['message'] ) ) {

			$data['message'] = $param['message'];
		}

		$data['success'] = 0;
	}


	return $data;
}

//
//oem_so_dt
function checkOemMoveDt( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$arr_product_ids = array();

	if ( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT
				*
			FROM ". $param['k_tb_name'] ."
			WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
		";

		$row_before_edit = $dao->fetch( $sql );

		$arr_product_ids[] = $row_before_edit->products_dt_id;
	}

	if ( !empty( $data['to_db'][$k_tb_name]['products_dt_id'] ) )
		$arr_product_ids[] = $data['to_db'][$k_tb_name]['products_dt_id'];

	$checkDates[$data['to_db'][$k_tb_name]['doc_date']] = $data['to_db'][$k_tb_name]['doc_date'];;

	$sql = "
		SELECT
			MAX( doc_date ) as t
		FROM oem_stock_hd

	";
	$res = $dao->fetch( $sql )->t;

	if ( !empty( $res ) )
		$checkDates[$res] = $res;


	$data['success'] = 1;
	foreach ( $checkDates as $kDate => $vDate ) {

		//
		// stock data
		$arrUnion = array();
		$filters = array();
		$filters[] = "a.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )";

		$filters[] = "a.doc_date <= '". $vDate ."'";


		if ( !empty( $row_before_edit ) )
			$filters[] = "IFNULL( a.move_dt_id, 0 ) != ". $row_before_edit->id;


		$sql = "
			SELECT
				concat( 'a', a.id ) as id,
				a.products_dt_id,
				a.zone_id,
				( ( a.qty - a.qty_return ) * a.qty_factor ) as stock_qty
			FROM oem_stock_dt a
			[cond]

		";
		$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );

		//
		// input data
		if ( $param['action_type'] != 'delete' ) {

			$arrUnion[] = "
				SELECT
					concat( 'b' ) as id,
					". $data['to_db'][$k_tb_name]['products_dt_id'] ." as products_dt_id,
					". $data['to_db'][$k_tb_name]['zone_out_id'] ." as zone_id,
					". ( $data['to_db'][$k_tb_name]['qty'] * -1 ) ." as stock_qty
			";

			$arrUnion[] = "
				SELECT
					concat( 'c' ) as id,
					". $data['to_db'][$k_tb_name]['products_dt_id'] ." as products_dt_id,
					". $data['to_db'][$k_tb_name]['zone_in_id'] ." as zone_id,
					". ( $data['to_db'][$k_tb_name]['qty'] ) ." as stock_qty
			";
		}

		$sql = "
			SELECT
				new_tb.products_dt_id,
				new_tb.zone_id,
				SUM( new_tb.stock_qty ) as stock_qty,
				z.name as zone_name,
				CONCAT( p.code, ' ', p.name, ' ', c.code ) as product
			FROM (". implode( ' UNION ', $arrUnion ) .") as new_tb
			LEFT JOIN oem_products_dt pt ON new_tb.products_dt_id = pt.id
			LEFT JOIN oem_zones z ON new_tb.zone_id = z.id
			LEFT JOIN oem_products_color c ON pt.products_color_id = c.id
			LEFT JOIN oem_products p ON pt.products_id = p.id
			GROUP BY products_dt_id, zone_id
			HAVING stock_qty != 0
		";

	//arr( $sql );

		$keep = array();
		foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {


			if ( $va->stock_qty < 0 ) {

				$data['field']['products_dt_id'] = 1;

				$data['field']['qty'] = 1;

				$data['success'] = 0;

				$keep[$va->product][] = $va->zone_name .' มีจำนวน'. $va->stock_qty;
			}


		}

		foreach ( $keep as $ka => $va ) {

			$data['message'] = implode( ', ', $va ) . ' ณ วันที่ ' . $vDate;
		}

		if ( $data['success'] == 0 )
			return $data;
	}

	return $data;


}


//
//104
function checkErpProductStandardCost ( $param ) {

	$dao = getDb();

	$data = $param['data'];
	$k_tb_name = $param['k_tb_name'];

	$sql = "
		SELECT
			CONCAT( product_width, '-', product_lenght ) as group_name
		FROM erp_product
		WHERE product_id IN ( ". $data['to_db'][$k_tb_name]['product_ids'] ." )
		GROUP BY group_name

	";

	//arr( $sql );

	if( $dao->getRowsCount( $sql ) > 1 ) {

		$data['success'] = 0;

		$data['field']['product_ids'] = 'กรุณาเลือกสินค้าที่มีขนาดเดียวกัน';

		$data['message'] = 'กรุณาเลือกสินค้าที่มีขนาดเดียวกัน';
	}



	return $data;
}



//
//
function checkErpStockDtFromOrder( $param ) {

	//arr( $param );

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	if ( isset( $param['main_data_before']->is_not_check_stock ) && $param['main_data_before']->is_not_check_stock == 1 ){

		return $data;
	}

	$checkDate = $data['to_db'][$k_tb_name]['doc_date']; // form data

	$key_action = -1; // form data

	$input_qty = $data['to_db'][$k_tb_name]['qty'] * $key_action; // form data

	$groupName = 'code_color';

	$code_zone_color = $data['to_db'][$k_tb_name]['product_id'] .'-'. $data['to_db'][$k_tb_name]['product_color_id'];

	//
	//
	$sqlUnion = $filters = array();

	$filters[] = "doc_date <= '". $checkDate ."'";

	if ( !empty( $param['pri_key'] ) ) {

		$sql = "

			SELECT
				CONCAT( product_id, '-', product_color_id ) as ". $groupName ."
			FROM  erp_sale_order_dt
			WHERE id = ". $param['pri_key'] ."

		";

		$res = $dao->fetch( $sql );

		$checkProductZones[$res->$groupName] = $res->$groupName;

		//$filters[] = "id != ". $param['pri_key'] ."";
	}

	$checkProductZones[$code_zone_color] = $code_zone_color;

	$keepVdz = array();

	foreach ( $checkProductZones as $kdz => $vdz ) {

		$sql = "
			SELECT
				". $groupName .",
				year_month_ as max_period_date,
				stock_master_zone_qty
			FROM erp_stock_master_zone_period
			WHERE year_month_ <=  '". $checkDate ."'
			AND ". $groupName ." = '". $vdz ."'
			ORDER BY max_period_date DESC
			LIMIT 0, 1
		";


		$res = $dao->fetch( $sql );

		if ( $res ) {

			$sqlUnion[] = "

				SELECT
					'". $res->$groupName ."' as ". $groupName .",
					'from_master' as from_,
					". $res->stock_master_zone_qty ." as stock_master_zone_qty
			";

			if ( $res->max_period_date == $checkDate ) {

				continue;
			}

			$keepVdz[] = "( ". $groupName ." = '". $vdz ."' AND doc_date > '". $res->max_period_date ."' )";
		}
		else {

			$keepVdz[] = "( ". $groupName ." = '". $vdz ."' )";
		}

	}

	if ( !empty( $keepVdz ) ) {
		$filters[] = "(". implode( ' OR ', $keepVdz ) .")";

		$sql = "
			SELECT
				". $groupName .",
				'from_stock' as from_,
				SUM( qty * stock_act_action ) as stock_master_zone_qty
			FROM erp_stock_dt
			[cond]
			GROUP BY ". $groupName ."

		";
		$sqlUnion[] = genCond( $sql, $filters );
	}




	if ( !in_array( $param['action_type'], array( 'delete', 'ready' ) ) ) {
		$sqlUnion[] = "

			SELECT
				'". $code_zone_color ."' as ". $groupName .",
				'from_key_data' as from_,
				". $input_qty ." as stock_master_zone_qty
		";

	}



	$filters = array();
	$filters[] = "doc_date <=  '". $checkDate ."'";
	$filters[] = "". $groupName ." IN ( '". implode( "', '", $checkProductZones ) ."' )";

	if ( !empty( $param['pri_key'] ) ) {

		$filters[] = "id != ". $param['pri_key'] ."";
	}

	$sql = "
		SELECT
			". $groupName ." as ". $groupName .",
			'from_order' as from_,
			SUM( qty * ". $key_action ." ) as stock_master_zone_qty
		FROM erp_sale_order_dt
		[cond]
		GROUP BY ". $groupName ."

	";
	$sqlUnion[] = genCond( $sql, $filters );



	$sql = "

		SELECT
			new_tb.". $groupName .",
			SUM( stock_master_zone_qty ) as stock_master_zone_qty
		FROM ( ". implode( ' UNION ', $sqlUnion ) ." ) as new_tb
		GROUP BY new_tb.". $groupName ."
		HAVING stock_master_zone_qty >= 0

	";

	//( $sql );


	$sql = "

		SELECT
			tmp.product_id,
			tmp.product_color_id,
			tmp.code_color,
			(
				SUM( total_qty )
				-
				(

					SELECT
						SUM( qty ) as order_qty
					FROM erp_sale_order_dt
					WHERE code_color = tmp.code_color
					GROUP BY code_color
				)

			) as total_qty,
			NULL as product_dt_id
		FROM (

			(

				SELECT
					new_ta.product_id,
					new_ta.product_color_id,
					new_ta.code_color,
					new_ta.last_period_date,
					new_ta.period_qty,
					new_ta.stock_qty,
					( new_ta.period_qty + new_ta.stock_qty ) as total_qty
				FROM (


					SELECT
						new_tb.product_id,
						new_tb.product_color_id,
						CONCAT( new_tb.product_id, '-', new_tb.product_color_id ) as code_color,
						MAX( new_tb.last_period_date ) as last_period_date,

						IFNULL( (
							SELECT
								stock_master_zone_qty
							FROM erp_stock_master_zone_period
							WHERE code_color_file = new_tb.code_color_file
							AND year_month_ = MAX( new_tb.last_period_date )
							LIMIT 0, 1

						), 0 ) as period_qty,

						IFNULL( (

							SELECT
								SUM( qty * stock_act_action ) as stock_master_zone_qty
							FROM erp_stock_dt
							WHERE code_color_file = new_tb.code_color_file
							AND doc_date > MAX( new_tb.last_period_date )
							AND doc_date <= '". $checkDate ."'


						), 0 ) as stock_qty,


						new_tb.product_code,
						new_tb.product_name,
						new_tb.product_color_code
					FROM (
						(
							SELECT
								'erp_stock_dt' as from_,
								product_id,
								product_color_id,
								code_color_file,
								'1970-01-01' as last_period_date,
								prdid_ as product_code,
								product_name,
								color_ as product_color_code
							FROM erp_stock_dt
							WHERE code_color IN ( '223154-503' )
							GROUP BY code_color_file
						)
						UNION
						(
							SELECT
								'erp_stock_master_zone_period' as from_,
								product_id,
								product_color_id,
								code_color_file,
								MAX( year_month_ ) as last_period_date,
								prdid_ as product_code,
								product_name,
								color_
							FROM erp_stock_master_zone_period
							WHERE code_color IN ( '223154-503' )
							AND year_month_ <= '". $checkDate ."'
							GROUP BY code_color_file
						)

					) as new_tb
					GROUP BY new_tb.code_color_file


				) as new_ta

			)



		) as tmp

		GROUP BY code_color

	";

	arr( $sql );
//	$res = $dao->fetchAll( $sql );


	exit;

	if ( !$res ) {

		$data['success'] = 0;

		$data['field']['product_id'] = 'product_id';
		$data['field']['product_color_id'] = 'product_color_id';
		$data['field']['qty_um'] = 'qty_um';
		$data['field']['qty'] = 'qty';

		$data['message'] = 'ปริมาณไม่พอสั่งขาย';
	}


	return $data;

}



//
//
function checkErpProductStandardCostParent( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$parentIds[] = $data['to_db'][$k_tb_name]['parent_id'];

	if ( in_array( $data['to_db'][$k_tb_name]['product_id'], $parentIds ) ) {

		$data['field']['product_id'] = 'fddfdffd';

		$data['success'] = 0;

		$data['message'] = 'ไม่สามารถอ้างรายการนี้ได้';

		return $data;
	}


	for ( $i = 0; $i <= 10; ++$i ) {

		$sql = "
			SELECT
				parent_id
			FROM erp_product_standard_cost_dt
			WHERE product_id IN ( ". implode( ', ', $parentIds ) ." )
		";

		$res = $dao->fetchAll( $sql );

		if ( !$res ) {

			break;
		}

		$parentIds = array();
		foreach ( $res as $ka => $va ) {

			$parentIds[] = $va->parent_id;

		}

		if ( in_array( $data['to_db'][$k_tb_name]['product_id'], $parentIds ) ) {

			$data['field']['product_id'] = 'fddfdffd';

			$data['success'] = 0;

			$data['message'] = 'ไม่สามารถอ้างรายการนี้ได้';

			return $data;
		}



	}


	return $data;



}


function checkErpStockDt( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	//arr( $data['to_db'][$k_tb_name]   );

	$checkDate = $param['main_data_before']->doc_date; // form data

	$key_action = 1; // form data

	$input_qty = $data['to_db'][$k_tb_name]['qty'] * $key_action; // form data

	$code_zone_color = $data['to_db'][$k_tb_name]['product_id'] .'-'. $data['to_db'][$k_tb_name]['zone_id'] .'-'. $data['to_db'][$k_tb_name]['product_color_id'];


	//$code_zone_color = '242146-1-3'; //form data

	//
	//
	$sqlUnion = $filters = array();

	$filters[] = "doc_date <=  '". $checkDate ."'";


	if (  !empty( $param['pri_key'] ) ) {

		$sql = "

			SELECT
				code_zone_color
			FROM erp_stock_dt
			WHERE from_id = ". $param['pri_key'] ."
			AND from_ = '". $param['k_tb_name'] ."'
		";

		$res = $dao->fetch( $sql );

		$checkProductZones[$res->code_zone_color] = $res->code_zone_color;

		$filters[] = "stock_dt_id != ". $param['pri_key'] ." AND from_ = '". $param['k_tb_name'] ."'";

	}

	$checkProductZones[$code_zone_color] = $code_zone_color;

	$keepVdz = array();
	foreach ( $checkProductZones as $kdz => $vdz ) {

		$sql = "
			SELECT
				code_zone_color,
				year_month_ as max_period_date,
				stock_master_zone_qty
			FROM erp_stock_master_zone_period
			WHERE year_month_ <= '". $checkDate ."'
			AND code_zone_color = '". $vdz ."'
			ORDER BY max_period_date DESC
			LIMIT 0, 1
		";

		$res = $dao->fetch( $sql );

		if ( $res ) {

			$sqlUnion[] = "

				SELECT
					'". $res->code_zone_color ."' as code_zone_color,
					'from_master_". $vdz ."' as from_,
					". $res->stock_master_zone_qty ." as stock_master_zone_qty
			";

			if ( $res->max_period_date == $checkDate ) {

				continue;
			}

			$keepVdz[] = "( code_zone_color = '". $vdz ."' AND doc_date > '". $res->max_period_date ."' )";
		}
		else {

			$keepVdz[] = "( code_zone_color = '". $vdz ."' )";
		}

	}

	if ( !empty( $keepVdz ) ) {

		$filters[] = "(". implode( ' OR ', $keepVdz ) .")";

		$sql = "
			SELECT
				code_zone_color,
				'from_stock' as from_,
				SUM( qty * stock_act_action ) as stock_master_zone_qty
			FROM erp_stock_dt
			[cond]
			GROUP BY code_zone_color

		";
		$sqlUnion[] = genCond( $sql, $filters );
	}

	if ( !in_array( $param['action_type'], array( 'delete', 'ready' ) ) ) {

		$sqlUnion[] = "

			SELECT
				'". $code_zone_color ."' as code_zone_color,
				'from_key_data' as from_,
				". $input_qty ." as stock_master_zone_qty
		";

	}


	$sql = "

		SELECT
			new_tb.code_zone_color,
			SUM( stock_master_zone_qty ) as stock_master_zone_qty
		FROM ( ". implode( ' UNION ', $sqlUnion ) ." ) as new_tb
		GROUP BY new_tb.code_zone_color
		HAVING stock_master_zone_qty >= 0
	";

//arr( $sql );
	$res = $dao->fetchAll( $sql );
////arr( $res );
//exit;
	//$res = $dao->fetchAll( $sql );



	if ( !$res ) {

		$data['success'] = 0;

		$data['field']['product_id'] = 'product_id';
		$data['field']['product_color_id'] = 'product_color_id';
		$data['field']['qty_um'] = 'qty_um';
		$data['field']['qty'] = 'qty';

		$data['message'] = 'ปริมาณไม่พอสั่งขาย';
	}


	return $data;
}




//
//
function checkErpStockDt_____( $param ) {

	$dao = getDb();


	if ( isset( $param['def_stock_act_action'] ) )
		$param['main_data_before']->stock_act_action = $param['def_stock_act_action'];

	$data = $param['data'];

	$param['config_name'] = array( 'config_stock_closed_date' );

	$getClosedDate = getConfigVal( $param );

	if ( $param['main_data_before']->doc_date <= $getClosedDate ) {

		$data['field']['zone_id'] = 'zone_id';
		$data['field']['zone_out_id'] = 'zone_out_id';
		$data['field']['zone_in_id'] = 'zone_in_id';

		$data['success'] = 0;
		$data['message'] = 'ปิดงวดวันที่ ' . $getClosedDate . ' ไปแล้ว';

		return $data;
	}


	$k_tb_name = $param['k_tb_name'];

	//
	// allow IF stock able = 0 every products
	$sql = "
		SELECT
			stockable as t
		FROM erp_product_dt
		WHERE product_dt_id = ". $data['to_db'][$k_tb_name]['product_dt_id'] ."
	";


	$res = $dao->fetch( $sql );

	if( $res && $res->t == 0 )
		return $data;

	$action_type = $param['action_type'];
	$parent_id = $param['parent_id'];
	$pri_key = $param['pri_key'];
	$qty_name = 'qty';

	if ( isset( $param['main_data_before']->is_not_check_stock ) && $param['main_data_before']->is_not_check_stock == 1 ){

		return $data;
	}



	$arr_product_ids = array();
	if ( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT *
			FROM ". $param['k_tb_name'] ."
			WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
		";

		$row_before_edit = $dao->fetch( $sql );

		$arr_product_ids[] = $row_before_edit->product_dt_id;

	}


	if ( !empty( $data['to_db'][$k_tb_name]['product_dt_id'] ) )
		$arr_product_ids[] = $data['to_db'][$k_tb_name]['product_dt_id'];


	$sql = "
		SELECT
			MAX( new_tb.pointDate ) as pointDate
		FROM
		(

			SELECT
				MAX( doc_date ) as pointDate
			FROM  erp_stock
			UNION
			SELECT
				MAX( doc_date ) as pointDate
			FROM erp_stock_tf

		) as new_tb
		HAVING pointDate != '". $param['main_data_before']->doc_date ."'

		UNION
		SELECT
			'". $param['main_data_before']->doc_date ."' as pointDate
	";
	$data['success'] = 1;

	foreach ( $dao->fetchAll( $sql ) as $kpd => $vpd ) {

		$removeUnion = "";
		if ( !empty( $row_before_edit ) && $k_tb_name == 'erp_stock_tf_dt' ) {
			$removeUnion = "

				UNION

				SELECT
					'zzz',
					product_dt_id,
					zone_out_id as zone_id,
					qty * 1 as qty,
					CONCAT( product_dt_id,'-', zone_out_id ) as group_name
				FROM ". $param['k_tb_name'] ."
				WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."

				UNION

				SELECT
					'zzzgg',
					product_dt_id,
					zone_in_id as zone_id,
					qty * -1 as qty,
					CONCAT( product_dt_id,'-', zone_in_id ) as group_name
				FROM ". $param['k_tb_name'] ."
				WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
			";
		}
		else if ( !empty( $row_before_edit ) && $k_tb_name != 'erp_sale_order_dt' ) {
			$removeUnion = "

				UNION

				SELECT
					'zzz',
					product_dt_id,
					zone_id as zone_id,
					qty * ". $param['main_data_before']->stock_act_action ." * -1 as qty,
					CONCAT( product_dt_id,'-', zone_id ) as group_name
				FROM ". $param['k_tb_name'] ."
				WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
			";
		}

		$arr_union = array();


		$arr_union[] = "

			SELECT
				'a',
				a.product_dt_id,
				a.zone_id as zone_id,
				total_qty
				-
				(
					(
						SELECT
							IFNULL( SUM( d.qty * d.stock_act_action ), 0 )
						FROM erp_stock_dt d
						WHERE d.product_dt_id = a.product_dt_id
						AND d.zone_id = a.zone_id
						AND d.doc_date > '". $vpd->pointDate ."'
					)
					+
					(
						SELECT
							IFNULL( SUM( d.qty * IF( d.zone_in_id = a.zone_id, 1, -1 ) ), 0)
						FROM erp_stock_tf_dt d
						WHERE d.product_dt_id = a.product_dt_id
						AND ( d.zone_out_id = a.zone_id OR d.zone_in_id = a.zone_id )
						AND d.doc_date > '". $vpd->pointDate ."'
					)
				) as qty,
				CONCAT( a.product_dt_id, '-', a.zone_id ) as group_name
			FROM erp_stock_dt_static a
			". $removeUnion ."
		";

		//
		// erp_sale_order_dt table
		if ( $k_tb_name != 'erp_sale_inv_dt' ) {


			$removeUnion = "";
			if ( !empty( $row_before_edit ) && $k_tb_name == 'erp_sale_order_dt' ) {
				$removeUnion = "

					UNION

					SELECT
						'zzz',
						product_dt_id,
						zone_id as zone_id,
						( qty - cancel_qty ) as qty,
						CONCAT( product_dt_id,'-', zone_id ) as group_name
					FROM ". $param['k_tb_name'] ."
					WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
				";
			}


			$arr_union[] = "
				SELECT

					'd',
					a.product_dt_id,
					a.zone_id,
					-1 * SUM( a.qty - a.cancel_qty - ( SELECT IFNULL( SUM( qty ), 0 ) FROM erp_sale_inv_dt WHERE sale_order_dt_id = a.id ) ) as qty,
					CONCAT( a.product_dt_id, '-', a.zone_id ) as group_name
				FROM erp_sale_order_dt a
				LEFT JOIN erp_sale_order b ON a.sale_order_id = b.id
				WHERE b.is_not_check_stock = 0
				AND a.product_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )
				GROUP BY group_name

				". $removeUnion ."

			";
		}


		//
		// input data
		if ( $action_type != 'delete' ) {

			if ( $k_tb_name == 'erp_sale_order_dt' ) {
				if ( !isset( $data['to_db'][$k_tb_name]['cancel_qty'] ) )
					$data['to_db'][$k_tb_name]['cancel_qty'] = 0;

				$qty = ( $data['to_db'][$k_tb_name]['qty'] - $data['to_db'][$k_tb_name]['cancel_qty'] ) * $param['main_data_before']->stock_act_action;


				//
				//
				$arr_union[] = "
					SELECT
						'e',

						". $data['to_db'][$k_tb_name]['product_dt_id'] .",
						". $data['to_db'][$k_tb_name]['zone_id'] .",
						". $qty .",
						CONCAT( ". $data['to_db'][$k_tb_name]['product_dt_id'] .", '-', ". $data['to_db'][$k_tb_name]['zone_id'] ." ) as group_name
				";
			}
			else if ( $k_tb_name == 'erp_stock_tf_dt' ) {

				//
				// move out
				$qty = $data['to_db'][$k_tb_name]['qty'] * -1;


				//
				//
				$arr_union[] = "
					SELECT
						'mo',

						". $data['to_db'][$k_tb_name]['product_dt_id'] .",
						". $data['to_db'][$k_tb_name]['zone_out_id'] .",
						". $qty .",
						CONCAT( ". $data['to_db'][$k_tb_name]['product_dt_id'] .", '-', ". $data['to_db'][$k_tb_name]['zone_out_id'] ." ) as group_name
				";

				//
				// move in
				$qty = $data['to_db'][$k_tb_name]['qty'] * 1;

				$arr_union[] = "
					SELECT
						'mi',

						". $data['to_db'][$k_tb_name]['product_dt_id'] .",
						". $data['to_db'][$k_tb_name]['zone_in_id'] .",
						". $qty .",
						CONCAT( ". $data['to_db'][$k_tb_name]['product_dt_id'] .", '-', ". $data['to_db'][$k_tb_name]['zone_in_id'] ." ) as group_name
				";
			}
			else {

				if ( isset( $data['to_db'][$k_tb_name]['qty_return'] ) )
					$qty = ( $data['to_db'][$k_tb_name][$qty_name] - $data['to_db'][$k_tb_name]['qty_return'] ) * $param['main_data_before']->stock_act_action;
				else
					$qty = ( $data['to_db'][$k_tb_name][$qty_name] ) * $param['main_data_before']->stock_act_action;

				//
				//
				$arr_union[] = "
					SELECT
						'e',

						". $data['to_db'][$k_tb_name]['product_dt_id'] .",
						". $data['to_db'][$k_tb_name]['zone_id'] .",
						". $qty .",
						CONCAT( ". $data['to_db'][$k_tb_name]['product_dt_id'] .", '-', ". $data['to_db'][$k_tb_name]['zone_id'] ." ) as group_name
				";

			}

		}


		$sql = "

			SELECT
				p.product_dt_id,
				p.product_name,
				p.admin_company_id,
				p.active,
				p.stockable,
				new_tb.zone_id,
				( SELECT zone_name FROM erp_zone WHERE zone_id = new_tb.zone_id ) zone_name,
				SUM( new_tb.qty ) as qty
			FROM ( ". implode( ' UNION ', $arr_union ) ." ) as new_tb
			LEFT JOIN erp_product_dt p ON new_tb.product_dt_id = p.product_dt_id
			WHERE new_tb.product_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )
			GROUP BY new_tb.product_dt_id, new_tb.zone_id
			HAVING qty != 0 AND stockable = 1

		";

		//arr( $sql );

		$zone_avilable = array();

		if ( $k_tb_name == 'erp_stock_tf_dt' ) {


			foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

				if ( $va->qty < 0 ) {

					$data['success'] = 0;
					$data['field']['zone_id'] = 'zone_id';

					$data['field'][$qty_name] = 'ยังไม่มีข้อมูลในระบบ';

				}

				if ( $data['to_db'][$k_tb_name]['zone_in_id'] != $va->zone_id )
					$zone_avilable[$va->product_name][] = $va->zone_name .' : '. $va->qty;
			}

		}
		else {

			foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

				if ( $va->qty < 0 ) {

					$data['success'] = 0;

					$data['field']['zone_id'] = 'zone_id';

					$data['field'][$qty_name] = 'ยังไม่มีข้อมูลในระบบ';

				}

				$zone_avilable[$va->product_name][] = $va->zone_name .' : '. $va->qty;
			}
		}

		//
		//
		foreach ( $zone_avilable as $ka => $va ) {

			$data['message'] = implode( ', ', $va ) . 'ณ วันที่ ' . $vpd->pointDate;
		}

		if ( $data['success'] == 0 ) {

			return $data;
		}
	}

	return $data;
}


//
//
function checkBookAndSaleNo( $param ){


	$dao = getDb();

	$data = $param['data'];

	$k_tb_name = $param['k_tb_name'];

	$action_type = $param['action_type'];

	$pri_key = $param['pri_key'];

	$sql = "

		SELECT
			COUNT( * ) as t
		FROM erp_sale_order
		WHERE id = ". $data['to_db'][$k_tb_name]['sale_order_id'] ."
		AND warehouse_id = ". $data['to_db'][$k_tb_name]['warehouse_id'] ."

	";

	if ( $dao->fetch( $sql )->t == 0 ) {

		$data['field']['book_id'] = 'ข้อมูลไม่สัมพันธ์กัน';
		$data['field']['sale_order_id'] = 'ข้อมูลไม่สัมพันธ์กัน ';
		$data['message'] = 'ข้อมูลไม่สัมพันธ์กัน';
	}

	return $data;
}

//
//
function checkErpBookCompanySaleOrder( $param ){

	$dao = getDb();

	$data = $param['data'];

	$k_tb_name = $param['k_tb_name'];


	$sql = "

		SELECT
			id
		FROM erp_book_company_sale_order
		WHERE company_id = ". $data['to_db'][$k_tb_name]['company_id'] ."
		AND book_id = ". $data['to_db'][$k_tb_name]['book_id'] ."
		LIMIT 0, 1
	";

	if ( !$dao->fetch( $sql ) ) {

		$data['field']['company_id'] = 'ยังไม่มีข้อมูลในระบบ';
		$data['field']['book_id'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'][] = '<div>ยังไม่ได้สร้างข้อมูลให้ร้านค้า</div>';


		$data['message'][] = openWIndow( $param );


		$data['message'] = implode( '', $data['message'] );

	}

	return $data;
}

//
//
function openWIndow ( $param ) {

	$k_tb_name = $param['k_tb_name'];

	if ( !empty( $param['openWIndow'] ) ) {

		$keep = array();
		if ( !empty( $param['send'] ) ) {
			foreach ( $param['send'] as $ks => $vs ) {
				$keep[$vs] = $param['data']['to_db'][$k_tb_name][$vs];
			}
		}

		return '<a class="web-bt" target="_blank" href="'. getLink( $param['openWIndow'], array(), $keep ) .'">ดำเนินการ</a>';

		return '

			<script>
				window.open( \''. getLink( $param['openWIndow'], array(), $keep ) .'\', \'_blank\', \'location=no,height=800,width=800,scrollbars=yes,status=yes\' );

			</script>
		';
	}

	return '';

}


//
//
function checkErpProductUmRate( $param ) {

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$sql = "
		SELECT
			new_tb.*
		FROM (
			SELECT
				product_um_id
			FROM erp_product
			WHERE product_id = ". $data['to_db'][$k_tb_name]['product_id'] ."

			UNION

			SELECT
				um_id
			FROM erp_product_um_rate
			WHERE product_id = ". $data['to_db'][$k_tb_name]['product_id'] ."
		) as new_tb
		WHERE product_um_id = ". $data['to_db'][$k_tb_name]['um_id'] ."
	";

	$res = $dao->fetch( $sql );


	$data['success'] = 1;

	if ( !$res ) {

		$data['message'][] = 'ยังไม่มีกรอกหน่วยให้สินค้า';

		$data['message'][] = openWIndow ( $param );

		$data['message'] = implode( '', $data['message'] );

		$data['success'] = 0;
		$data['field']['product_id'] = 'รายการนี้ ถูกล็อคอยู่ครับ';
		$data['field']['um_id'] = 'รายการนี้ ถูกล็อคอยู่ครับ';

	}

	return $data;
}

//
//104
function checkSacStockDt( $data, $k_tb_name, $action_type, $main_id, $pri_key = NULL ) {

	$dao = getDb();

	$stock_dt_qty = 0;

	if ( $action_type != 'delete' ) {
		$sql = "
			SELECT
				LEFT( act_stk_id, 1 ) as left_stock_act_code
			FROM ". DatabaseSac .".stock_hd
			WHERE hd_id = ". $main_id;

		$res = $dao->fetch( $sql );

		if ( $res->left_stock_act_code == 0 ) {
			$stock_dt_qty = $data['to_db'][$k_tb_name]['qty'];
		} else {
			$stock_dt_qty = $data['to_db'][$k_tb_name]['qty'] * -1;
		}
	}

	$arr_product_dt_id[] = $data['to_db'][$k_tb_name]['product_id'];


	$cond = '';
	if( !empty( $pri_key ) ) {

		$cond = "WHERE a.dt_id != ". $pri_key ."";

		$sql = "
			SELECT
				*
			FROM ". DatabaseSac .".stock_dt a WHERE a.dt_id = ". $pri_key ."";
		$res = $dao->fetch( $sql );
		$arr_product_dt_id[] = $res->product_id;

	}

	$sql = "
		SELECT
			new_tb.product_id,
			new_tb.zone_id,
			b.name as zone_name,
			SUM( new_tb.stock_dt_qty ) as sum_stock_dt_qty

		FROM (

			SELECT
				CONCAT( 'a', dt_id ),
				a.product_id,
				IF( LEFT( b.act_stk_id, 1 ) = 0, a.qty, a.qty * -1 ) as stock_dt_qty,
				a.zone_id
			FROM ". DatabaseSac .".stock_dt a

			LEFT JOIN ". DatabaseSac .".stock_hd b ON a.hd_id = b.hd_id
			". $cond ."


			UNION
			SELECT
				NULL,
				". $data['to_db'][$k_tb_name]['product_id'] .",
				". $stock_dt_qty .",
				". $data['to_db'][$k_tb_name]['zone_id'] ."

		) as new_tb
		LEFT JOIN ". DatabaseSac .".tb_zone b ON new_tb.zone_id = b.id
		GROUP BY new_tb.product_id, new_tb.zone_id

		HAVING new_tb.product_id IN ( ". implode( ', ', $arr_product_dt_id ) ." )
	";

	$res_ = $res = $dao->fetchAll( $sql );


	$keep = array();
	foreach ( $res as $ka => $va ) {

		$sum_stock_dt_qty = $va->sum_stock_dt_qty;
		if ( $data['to_db'][$k_tb_name]['zone_id'] == $va->zone_id ) {
			$sum_stock_dt_qty += $data['to_db'][$k_tb_name]['qty'];

		}

		if ( $sum_stock_dt_qty == 0 )
			continue;

		$keep[] = $va->zone_name .' '. $sum_stock_dt_qty .'';

	}

	//
	//
	foreach ( $res_ as $ka => $va ) {

		if ( $va->sum_stock_dt_qty < 0 ) {

			$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

			$data['message'] = 'มีของอยู่ในโซน ' . implode( ', ', $keep );

			break;
		}
	}

	return $data;

}

//
//
function checkSlabOnZone( $param ) {

	$dao = getDb();
	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];
	//HAVING zone_id = ". $data['to_db'][$k_tb_name]['zone_id'] ."
	$sql = "

		SELECT

			dt.zone_id,
			z.name as zone_name,
			sum( dt.qty * dt.factor ) as qty
		FROM stock_dt dt
		LEFT JOIN tb_zone z ON dt.zone_id = z.id
		WHERE dt.product_id = ". $data['to_db'][$k_tb_name]['product_id'] ."
		GROUP BY zone_id
		HAVING qty != 0
	";

	$data['success'] = 0;
	$keep = array();
	foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

		if ( $data['to_db'][$k_tb_name]['zone_id'] == $va->zone_id ) {

			$data['success'] = 1;
			break;
		}

		$keep[] = $va->zone_name;

	}

	if ( $data['success'] == 0 ) {

		$data['message'] = 'สามาเลือก : ' . implode( ', ', $keep );

		$data['field']['product_id'] = 'รายการนี้ ถูกล็อคอยู่ครับ';
		$data['field']['zone_id'] = 'รายการนี้ ถูกล็อคอยู่ครับ';
	}

	return $data;

}


//
//
function lockRow( $param ) {


	$data = $param['data'];

	foreach( $data['to_db'] as $kd => $vd ) {

		if ( !empty( $vd['lock_row'] ) ) {


			$data['field']['qty_rate'] = 'รายการนี้ ถูกล็อคอยู่ครับ';

			$data['message'] = 'รายการนี้ ถูกล็อคอยู่ครับ';
			$data['success'] = 0;
		}

	}
	//arr( $data );
	return $data;

}

//
//104
function checkSubDocument( $param ) {

	$dao = getDb();

	$data = $param['data'];

	$action_type = $param['action_type'];

	$parent_id = $param['parent_id'];

	$tb_name = isset( $param['tb_name'] )? $param['tb_name']: 'erp_stock_dt';

	$pri_name = isset( $param['pri_name'] )? $param['pri_name']: 'stock_id';

	$checkActions = empty( $param['checkActions'] )? array( 'edit', 'delete' ): $param['checkActions'];

	if ( in_array( $action_type, $checkActions ) ) {

		$sql = "
			SELECT
				COUNT( * ) as t
			FROM ". $tb_name ."
			WHERE ". $pri_name ." = ". $parent_id ."
		";

		$res = $dao->fetch( $sql );

		if ( $res->t > 0  ) {

			$data['success'] = 0;

			$data['field']['doc_no'] = 'มีบัญชีย่อยอยู่ภายใน';

			$data['message'] = 'กรุณาลบรายการย่อยก่อนดำเนินการ';
		}

	}

	return $data;
}


//
//oem_so_dt
function checkOverThanOrder( $param ) {


	//arr( $param );
	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	$arr_product_ids = array();

	if ( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT *
			FROM ". $k_tb_name ."
			WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
		";

		$row_before_edit = $dao->fetch( $sql );

		//arr($row_before_edit->so_dt_id);

		$arr_product_ids[] = $row_before_edit->products_dt_id;
	}

	if ( !empty( $data['to_db'][$k_tb_name]['products_dt_id'] ) )
		$arr_product_ids[] = $data['to_db'][$k_tb_name]['products_dt_id'];


	//
	// sell order
	$filters = array();
	//$filters[] = "a.products_dt_id IN ( ". implode( ', ', $arr_product_ids ) ." )";



	if ( !empty( $row_before_edit->so_dt_id ) )
		$filters[] = "a.id = ". $row_before_edit->so_dt_id;

	$sql = "
		SELECT
			'b',
			a.products_dt_id,
			SUM( ( a.qty - a.cancel_qty ) ) as stock_qty
		FROM oem_so_dt a
		[cond]
		GROUP BY products_dt_id
	";

	$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );



	//
	// stock data
	$filters = array();


	if ( !empty( $row_before_edit ) )
		$filters[] = "a.id != ". $row_before_edit->id;

	if ( !empty( $row_before_edit->so_dt_id ) )
		$filters[] = "a.so_dt_id = ". $row_before_edit->so_dt_id;


	$sql = "
		SELECT
			'a',
			a.products_dt_id,
			SUM( ( a.qty - a.qty_return ) * -1 ) as stock_qty
		FROM oem_stock_dt a
		[cond]
		GROUP BY products_dt_id
	";

	$arrUnion[] = genCond( $sql, $filters, $condTxt = "WHERE" );


	if ( $param['action_type'] != 'delete' ) {

		//
		// input data
		$arrUnion[] = "
			SELECT
				'c',
				". $data['to_db'][$k_tb_name]['products_dt_id'] ." as products_dt_id,
				". ( ( $data['to_db'][$k_tb_name]['qty'] ) * $param['factor'] ) ." as stock_qty
		";
	}



	$sql = "
		SELECT
			new_tb.products_dt_id,
			SUM( new_tb.stock_qty ) as stock_qty
		FROM (". implode( ' UNION ', $arrUnion ) .") as new_tb
		GROUP BY products_dt_id
		HAVING stock_qty != 0

	";
//	arr( $sql );

	foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {
		if ( $va->stock_qty < 0 ) {
			$data['field']['products_dt_id'] = 'fddfdffd';
			$data['field']['qty'] = 'dfdfdfd';

			$data['success'] = 0;
			$data['message'] = 'จำนวน ขัดแย้งกับใบสั่งขาย';
		}

	}

	return $data;
}



//
//
function checkSacProductDt( $param ) {
	
	//return;

	$dao = getDb();

	$k_tb_name = $param['k_tb_name'];

	$data = $param['data'];

	//
	//
	if ( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT *
			FROM ". DatabaseSac .".". $k_tb_name ."
			WHERE ". $param['current_config']->pri_key ." = ". $param['main_id'] ."
		";

		$row_before_edit = $dao->fetch( $sql );
	}

	//
	//
	$filters = array();
	$filters[] = "product_id = ". $param['parent_id'] ."";

	if ( !empty( $row_before_edit ) ) {

		$filters[] = "id != ". $row_before_edit->id;
	}

	//
	//
	$sql = "
		SELECT
			new_tb.*
		FROM (
			SELECT
				'a',
				plattern,
				h as w,
				w as h,
				qty
			FROM ". DatabaseSac .".tb_product_dt
			[cond]

			UNION

			SELECT
				'b',
				". $data['to_db'][$k_tb_name]['plattern'] ." as plattern,
				". $data['to_db'][$k_tb_name]['h'] ." as w,
				". $data['to_db'][$k_tb_name]['w'] ." as h,
				". $data['to_db'][$k_tb_name]['qty'] ." as qty
		) as new_tb

		ORDER BY plattern ASC
	";

	$sql = genCond( $sql, $filters, $condTxt = "WHERE" );

	$checkPlatten = array();
	foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

		if ( !isset( $checkPlatten[$va->plattern] ) ) {

			$checkPlatten[$va->plattern] = 1;
			$keepCo['0-0'] = array( 'x' => 0, 'y' => 0 );
		}

		for ( $i = 1; $i <= $va->qty; ++$i ) {

			$oversize = true;
			foreach ( $keepCo as $kb => $co ) {

				$end_x = $co['x'] + $va->w;

				$end_y = $co['y'] + $va->h;

				//
				// over size go to another coordinate
				if ( $end_y > $param['main_data_before']->product_w || $end_x > $param['main_data_before']->product_l ) {
					continue;
				}
				//
				// pass create new coordinate
				else {

					$oversize = false;
					unset( $keepCo[$co['x'] . '-' . $co['y']] );
					$keepCo[$end_x . '-' . $co['y']] = array( 'x' => $end_x, 'y' => $co['y'] );
					$keepCo[$co['x'] . '-' . $end_y] = array( 'x' => $co['x'], 'y' => $end_y );
					break;
				}
			}

			if ( $oversize ) {

				$data['field']['w'] = '1';
				$data['field']['h'] = '1';
				$data['field']['qty'] = '1';
				$data['success'] = 0;
				$data['message'] = 'กว้าง * ยาว เกินกว่า ขนาดสินค้า';
				return $data;
			}
		}
	}

	return $data;
}

//
//
function checkWorkTime( $param ){


	$dao = getDb();

	$data = $param['data'];

	$k_tb_name = $param['k_tb_name'];

	$action_type = $param['action_type'];

	$pri_key = $param['pri_key'];

	$cond = '';
	if ( $action_type == 'edit' ) {

		$cond = "
			-
			IFNULL(
				( SELECT
					SUM( break_min )
				FROM erp_stock_dt_rest_time

				WHERE stock_id = ". $pri_key ." ),
				0
			)
		";
	}

	$sql = "

		SELECT

			IFNULL( TIMESTAMPDIFF( MINUTE, CONCAT( '". $data['to_db'][$k_tb_name]['start_date'] ."', ' ', '". $data['to_db'][$k_tb_name]['start_time'] ."' ), CONCAT( '". $data['to_db'][$k_tb_name]['end_date'] ."', ' ', '". $data['to_db'][$k_tb_name]['end_time'] ."' ) ), 0 )
			". $cond ." as work_time


	";

	//arr( $sql );

	if ( $dao->fetch( $sql )->work_time <= 0 ) {

		$data['field']['start_date'] = 'คุณกรอกข้อมูลไม่ถูกต้อง';
		$data['field']['start_time'] = 'คุณกรอกข้อมูลไม่ถูกต้อง';
		$data['field']['end_date'] = 'คุณกรอกข้อมูลไม่ถูกต้อง';
		$data['field']['end_time'] = 'คุณกรอกข้อมูลไม่ถูกต้อง';

		$data['message'] = 'คุณกรอกเวลาทำงานไม่ถูกต้อง';
	}

	return $data;
}

//
//108
function checkErpStockTfDt( $param ) {


	$k_tb_name = $param['k_tb_name'];
	$data = $param['data'];
	if( $data['to_db'][$k_tb_name]['zone_out_id'] == $data['to_db'][$k_tb_name]['zone_in_id'] ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ย้ายในโซนเดียวกัน';

		return $data;
	}

	return checkErpStockDt( $param );
}

//
//104
function checkErpGlTrnPost( $param ) {

	$dao = getDb();

	$data = $param['data'];

	$sql = "

		SELECT
			COUNT( * ) as t
		FROM erp_gl_trn_dt
		WHERE from_config_id = ". $param['getView_config_id'] ."
		AND from_parent_id = ". $param['parent_id'] ."


	";



	$res = $dao->fetch( $sql );



	if ( $res && $res->t > 0 ) {


		$data['field']['doc_no'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

		$data['field']['product_dt_id'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีรายการย่อย';
	}


	return $data;

}

//
//104
function lockSubBySub( $param ) {

	//arr( $param );

	/*$param['tb_lock_dt'] = 'erp_purchase_return_dt'; table 2
	$param['tb_lock_dt_pri_name'] = 'purchase_inv_dt_id'; table 2

	$param['tb_current_dt'] = 'erp_purchase_inv_dt'; table 1
	$param['tb_current_dt_parent_name'] = 'purchase_inv_id';
	$param['find_name'] = 'id';*/

	$param['find_name'] = isset( $param['find_name'] )? $param['find_name']: 'id';

	$data = $param['data'];

	$dao = getDb();

	if ( !empty( $param['parent_id'] ) ) {

		$sql = "
			SELECT
				COUNT( * ) as t
			FROM ". $param['tb_lock_dt'] ."
			WHERE ". $param['tb_lock_dt_pri_name'] ." IN (
				SELECT
					". $param['find_name'] ."
				FROM ". $param['tb_current_dt'] ."
				WHERE ". $param['tb_current_dt_parent_name'] ." = ". $param['parent_id'] ."
			)

		";

		if ( $dao->fetch( $sql )->t > 0 ) {

			$data['field']['doc_no'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

			$data['field']['product_dt_id'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

			$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสารเกี่ยวข้อง ';
		}
	}

	return $data;
}

//
//
function checkErpSaleReturnDtHaveInvId ( $param ) {

	$dao = getDb();

	$data = $param['data'];

	$k_tb_name = $param['k_tb_name'];

	$action_type = $param['action_type'];

	$pri_key = $param['pri_key'];

	$cond = '';
	if( !empty( $pri_key ) ) {

		$cond = "AND id != ". $pri_key;
	}

	$sql = "

		SELECT
		(
			IFNULL( SUM( a.qty ), 0 )
			-
			(
				SELECT
					IFNULL( sum( qty ), 0 )
					+
					". $data['to_db'][$k_tb_name]['qty'] ."
				FROM erp_sale_return_dt
				WHERE sale_inv_dt_id = a.id

				". $cond ."
			)
		) as diff

		FROM erp_sale_inv_dt a

		WHERE id = ". $data['to_db'][$k_tb_name]['sale_inv_dt_id'] ."


	";

	if ( $action_type != 'delete' && $dao->fetch( $sql )->diff < 0 ) {

		$data['field']['qty_um'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'เปิดใบสั่งขายจำนวน ' . ( $dao->fetch( $sql )->diff + $data['to_db'][$k_tb_name]['qty'] );

	}

	return $data;
}


//
//
function checkErpSaleInvDtHaveOrderId ( $param ) {

	$dao = getDb();

	$data = $param['data'];

	$k_tb_name = $param['k_tb_name'];

	$action_type = $param['action_type'];

	$pri_key = $param['pri_key'];

	$cond = '';
	if( !empty( $pri_key ) ) {

		$cond = "AND id != ". $pri_key;
	}

	$sql = "

		SELECT
		(
			IFNULL( SUM( a.qty - a.cancel_qty ), 0 )
			-
			(
				SELECT
					IFNULL( sum( qty ), 0 )
					+
					". $data['to_db'][$k_tb_name]['qty'] ."
				FROM erp_sale_inv_dt
				WHERE sale_order_dt_id = a.id

				". $cond ."
			)
		) as diff

		FROM erp_sale_order_dt a

		WHERE id = ". $pri_key ."


	";

	//arr( $sql );
	///arr( $param );

	//$res =

	if ( $action_type != 'delete' && $dao->fetch( $sql )->diff < 0 ) {

		$data['field']['qty_um'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'เปิดใบสั่งขายจำนวน ' . ( $dao->fetch( $sql )->diff + $data['to_db'][$k_tb_name]['qty'] );

	}

	return $data;
}


//
//104
function lockHeadBySub( $param ) {

	//arr( $param );

	/*$param['tb_lock_dt'] = 'erp_purchase_return_dt';
	$param['tb_lock_dt_pri_name'] = 'purchase_inv_dt_id';

	$param['tb_current_dt'] = 'erp_purchase_inv_dt';
	$param['tb_current_dt_parent_name'] = 'purchase_inv_id';*/

	$data = $param['data'];

	$dao = getDb();

	if ( !empty( $param['parent_id'] ) ) {

		$sql = "
			SELECT
				COUNT( * ) as t
			FROM erp_sale_inv_dt
			WHERE sale_inv_id IN (

				SELECT
					id
				FROM erp_sale_inv
				WHERE stock_temp_id = ". $param['parent_id'] ."
			)

		";
//arr( $sql );
		//exit;

		if ( $dao->fetch( $sql )->t > 0 ) {

			$data['field']['doc_no'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

			$data['field']['product_dt_id'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

			$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสารเกี่ยวข้อง  ';
		}
	}

	return $data;
}


//
//
function checkErpStockTempDt( $param ) {

	$dao = getDb();

	$data = $param['data'];


	$k_tb_name = $param['k_tb_name'];

	$action_type = $param['action_type'];
	$pri_key = $param['pri_key'];

	$cond = '';
	if( !empty( $pri_key ) ) {

		$cond = "AND id != ". $pri_key;
	}

	$sql = "

		SELECT
		(
			IFNULL( SUM( a.qty_um ), 0 )
			-
			(
				SELECT
					IFNULL( sum( qty_um ), 0 )
					+
					". $data['to_db'][$k_tb_name]['qty_um'] ."
				FROM erp_stock_temp_dt
				WHERE sale_order_dt_id = a.id

				". $cond ."
			)
		) as diff

		FROM erp_sale_order_dt a

		WHERE id = ". $data['to_db'][$k_tb_name]['sale_order_dt_id'] ."


	";

	//arr( $sql );

	$res = $dao->fetch( $sql );

	if ( $res->diff < 0 ) {
	    $data['field']['qty_um'] = 'ยังไม่มีข้อมูลในระบบ';
		$data['message'] = 'สั่งขายจำนวน' . ( $res->diff + $data['to_db'][$k_tb_name]['qty_um'] );
	}

	return $data;

}

//
//95
function checkErpStockTempDt___( $data, $k_tb_name, $action_type , $pri_key = NULL ) {

	$dao = getDb();

	$sql = "

		SELECT
			(
				b.sale_order_dt_qty_um -
				(
					SELECT
						IFNULL( SUM( qty ), 0 ) + ". $data['to_db'][$k_tb_name]['stock_dt_qty_um'] ."
					FROM erp_stock_temp_dt
					WHERE stock_temp_dt_id != a.stock_temp_dt_id
					AND sale_order_dt_id = a.sale_order_dt_id
				)
			) as diff_order_dt_qty_um
		FROM erp_stock_temp_dt a
		LEFT JOIN erp_sale_order_dt b ON a.sale_order_dt_id = b.sale_order_dt_id
		WHERE a.stock_temp_dt_id = ". $pri_key ."
	";

	$res = $dao->fetch( $sql );

	if ( $res->diff_order_dt_qty_um < 0 ) {
		$data['field']['stock_dt_qty_um'] = 'ยังไม่มีข้อมูลในระบบ';
		$data['message'] = 'จำนวนเกินใบสั่งขาย';
	}

	return $data;
}


//
//104
function lockSubBySub_______( $param ) {

	return lockSubBySub( $param );

}

//
//104
function lockByRelateDoc( $param ) {

	$data = $param['data'];

	$dao = getDb();

	$sql = "
		SELECT
			b.doc_no
		FROM erp_stock_ref_id a
		LEFT JOIN erp_stock b ON a.stock_id = b.stock_id
		WHERE a.stock_ref_id = ". $param['parent_id'] ."
	";


	/*$sql = "
		SELECT *
FROM  erp_purchase_return_dt
WHERE  purchase_inv_dt_id =40

	";*/

	$res = $dao->fetch( $sql );

	if ( $res ) {

		$data['field']['doc_no'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

		$data['field']['product_dt_id'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร  '. $res->doc_no .' เกี่ยวข้อง';
	}

	return $data;
}



//
//104
function checkRelateDoc_( $data, $k_tb_name, $main_id, $tb_main = 'sac_purchase_order', $tb_main_pri_key = 'purchase_order_id', $tb_dt = 'sac_purchase_order_dt', $tb_dt_pri_key = 'purchase_order_dt_id', $tb_check_dt = 'sac_purchase_receive_dt' ) {

	$dao = getDb();

	$sql = "
		SELECT
			COUNT( * ) as t
		FROM ". $tb_main ." a
		WHERE a.". $tb_main_pri_key ." IN (

			SELECT
				". $tb_main_pri_key ."
			FROM ". $tb_dt ."

			WHERE ". $tb_dt_pri_key ." IN (

				SELECT
					". $tb_dt_pri_key ."
				FROM ". $tb_check_dt ."
			)
		)
		AND a.". $tb_main_pri_key ." = ". $main_id ."
	";

	$res = $dao->fetch( $sql );

	if ( $res && $res->t > 0 ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร เกี่ยวข้อง';
	}


	return $data;
}

//
//60
function checkErpStockTfDt_( $data, $k_tb_name, $action_type, $pri_key = NULL ) {


	if ( $action_type == 'delete' )
		$data['to_db'][$k_tb_name]['qty'] = 0;

	else {

		if( $data['to_db'][$k_tb_name]['zone_out_id'] == $data['to_db'][$k_tb_name]['zone_in_id'] ) {

			$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

			$data['message'] = 'ย้ายในโซนเดียวกัน';

			return $data;
		}
	}

	$dao = getDb();

	$cond = '';

	if( !empty( $pri_key ) )
		$cond = "WHERE a.dt_id != ". $pri_key;

	$sql = "
		SELECT
			new_tb.product_id,
			new_tb.zone_id,
			SUM( new_tb.qty ) as sum_stock_dt_qty

		FROM (

			SELECT
				CONCAT( 'a', dt_id ),
				a.product_id,
				a.zone_id,
				IF( LEFT( b.act_stk_id, 1 ) = 0, a.qty, a.qty * -1 ) as qty
			FROM ". DatabaseSac .".stock_dt a
			LEFT JOIN ". DatabaseSac .".stock_hd b ON a.hd_id = b.hd_id
			UNION

			SELECT
				CONCAT( 'b', dt_id ),
				a.product_id,
				a.zone_out_id,
				qty * -1
			FROM ". DatabaseSac .".stock_zone_dt a
			". $cond ."
			UNION

			SELECT
				CONCAT( 'c', dt_id ),
				a.product_id,
				a.zone_in_id,
				qty
			FROM ". DatabaseSac .".stock_zone_dt a
			". $cond ."
			UNION

			SELECT
				'input_out',
				". $data['to_db'][$k_tb_name]['product_id'] .",
				". $data['to_db'][$k_tb_name]['zone_out_id'] .",
				". $data['to_db'][$k_tb_name]['qty'] * -1 ."
			UNION

			SELECT
				'input_in',
				". $data['to_db'][$k_tb_name]['product_id'] .",
				". $data['to_db'][$k_tb_name]['zone_in_id'] .",
				". $data['to_db'][$k_tb_name]['qty'] ."
		) as new_tb
		GROUP BY new_tb.product_id, new_tb.zone_id
		HAVING new_tb.product_id = ". $data['to_db'][$k_tb_name]['product_id'] ."
		AND sum_stock_dt_qty != 0
	";

	$res = $dao->fetchAll( $sql );

	$total = array();
	foreach ( $res as $ka => $va ) {

		if( !isset( $total[$va->zone_id] ) ) {
			$total[$va->zone_id] = 0;
		}
		$total[$va->zone_id] += $va->sum_stock_dt_qty;
	}

	unset( $total[$data['to_db'][$k_tb_name]['zone_out_id']] );
	unset( $total[$data['to_db'][$k_tb_name]['zone_in_id']] );

	$keep_zone_data = array();
	foreach ( $total as $ka => $va ) {

		$sql = "SELECT * FROM ". DatabaseSac .".tb_zone WHERE id = ". $ka;
		$res_ = $dao->fetch( $sql );
		$keep_zone_data[] = ''. $res_->name .' : '. $total[$ka] .'';
	}

	//
	//
	foreach ( $res as $ka => $va ) {

		if ( $va->sum_stock_dt_qty < 0 ) {

			$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

			$data['message'] = 'จำนวนไม่พอย้ายโซนได้';

			if( !empty( $keep_zone_data ) )
				$data['message'] .= 'มีของอยู่ในโซน ' . implode( ', ', $keep_zone_data );

			break;
		}

	}

	return $data;
}


//
//104
function checkParentAction( $param ) {

	$data = $param['data'];

	$action_type = $param['action_type'];

	$parent_id = $param['parent_id'];

	$dao = getDb();

	$sql = "
		SELECT
			COUNT( * ) as t
		FROM erp_stock
		WHERE stock_id = ". $param['parent_id'] ."
		AND ( stock_act_id IS NULL OR stock_act_id = 0 )

	";

	$res = $dao->fetch( $sql );

	//arr( $res );

	if ( $res->t > 0  ) {

		//echo 'dasfdsfs';

		$data['success'] = 0;
		$data['field']['doc_date'] = 'มีบัญชีย่อยอยู่ภายใน';
		$data['field']['product_dt_id'] = 'มีบัญชีย่อยอยู่ภายใน';

		$data['message'] = $param['message'];
	}

	return $data;
}

//
//104
function checkTbDiliverDt( $data, $k_tb_name, $main_id, $action_type = NULL ) {


	$dao = getDb();

	if ( $action_type == 'delete' )
		return $data;

	$plan_ids = $data['to_db'][$k_tb_name]['plan_ids'];
	$cond = array();
	$cond[] = "
		id IN (". $plan_ids .")
	";

	if ( !empty( $main_id ) ) {

		$cond[] = "
			id IN (
				SELECT
					plan_id
				FROM ". DatabaseSac .".tb_diliver_dt a
				WHERE diliver_id = ". $main_id ."
			)
		";
	}
	$sql = "
		SELECT
			doc_no
		FROM ". DatabaseSac .".tb_plan
		WHERE ". implode( ' OR ', $cond ) ." GROUP BY doc_no
	";

	$res = $dao->fetchAll( $sql );

	$data['success'] = 1;
	if ( $res && count( $res )  > 1  ) {

		$data['success'] = 0;
		$data['field']['plan_ids'] = 'ไม่สามารถเลือกใบสั่ง ได้เกิน 1 ใบ';
		$data['message'] = 'กรุณาเลือกข้อมูลใหม่';
	}

	//
	// ตรวจสอบ พื้นที่
	$sql = "

		SELECT
			(
				SELECT
					sqm
				FROM ". DatabaseSac .".tb_cars
				WHERE id = ". $data['to_db'][$k_tb_name]['car_id'] ."
			) as car_area,
			SUM( area ) as product_area
		FROM ". DatabaseSac .".tb_plan
		WHERE id IN (
			SELECT
				a.plan_id
			FROM ". DatabaseSac .".tb_diliver_dt a
			LEFT JOIN ". DatabaseSac .".tb_diliver b ON a.diliver_id = b.id
			WHERE b.doc_date = '". $data['to_db'][$k_tb_name]['doc_date'] ."'
			AND b.car_id = '". $data['to_db'][$k_tb_name]['car_id'] ."'
			AND b.diliver_time = '". $data['to_db'][$k_tb_name]['diliver_time'] ."'

		)
		OR id IN (". $plan_ids .")
	";

	$res = $dao->fetch( $sql );
	if ( $res && ( $res->product_area > $res->car_area ) ) {

		$data['success'] = 0;
		$data['field']['car_id'] = 'OverLoad';
		$data['field']['plan_id'] = 'ยังไม่มีข้อมูลในระบบ';
		$data['message'] = 'OverLoad';
	}


	//
	// ตรวจสอบ วันจัดส่ง
	$cond = array();
	$cond[] = "

		SELECT
			id
		FROM ". DatabaseSac .".tb_plan
		WHERE id IN ( ". $plan_ids ." )
	";

	if ( !empty( $main_id ) ) {

		$cond[] = "
			SELECT

				plan_id

			FROM ". DatabaseSac .".tb_diliver_dt
			WHERE diliver_id = ". $main_id ."

		";
	}

	$sql = "

		SELECT
			'". $data['to_db'][$k_tb_name]['doc_date'] ."' as doc_deliver_date,
			ADDDATE( a.doc_date, + ( SELECT val FROM admin_param WHERE id = 1 ) ) as min_deliver_date
		FROM ". DatabaseSac .".tb_plan a
		WHERE a.id IN (". implode( ' UNION ', $cond ) .")
		HAVING min_deliver_date > doc_deliver_date
	";

	$res = $dao->fetch( $sql );

	if ( $res ) {

		$data['success'] = 0;
		$data['field']['doc_date'] = 'มีสินค้าผลิตไม่ทันกำหนดส่งที่เลือกไว้';
		$data['field']['plan_ids'] = 'มีสินค้าผลิตไม่ทันกำหนดส่งที่เลือกไว้';

		$data['message'] = 'กรุณาเลือกข้อมูลใหม่';
	}

	return $data;
}


//
//104
function checkVatType( $data, $k_tb_name, $main_id, $param = array() ) {

	$dao = getDb();

	$sql = "

		SELECT
			*
		FROM sac_purchase_order_dt
		[cond]
		ORDER BY purchase_order_dt_id ASC LIMIT 0, 1
	";

	$filters[] = "purchase_order_id = ". $main_id;

	if ( !empty( $_REQUEST['pri_key'] ) )
		$filters[] = "purchase_order_dt_id != ". $_REQUEST['pri_key'];

	$sql = genCond( $sql, $filters );

	$res = $dao->fetch( $sql );

	if ( $res && $res->purchase_order_dt_vat_type != $data['to_db'][$k_tb_name]['purchase_order_dt_vat_type'] ) {

		$data['field']['purchase_order_dt_vat_type'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถมี VAT ได้ 2 ประเภทในเอกสารเดียวกัน';
	}


	return $data;

}




//
//104
function checkSacPurchaseReceiveDtQty( $data, $k_tb_name, $main_id ) {

//arr( $data );

	$dao = getDb();

	$sql = "

		SELECT
			a.purchase_order_dt_qty -

			(

				SELECT

					IFNULL( SUM( purchase_receive_dt_qty ), 0 )
				FROM sac_purchase_receive_dt
				WHERE purchase_order_dt_id = a.purchase_order_dt_id
				AND purchase_receive_dt_id != ". $_REQUEST['pri_key'] ."

			) - ". $data['to_db'][$k_tb_name]['purchase_receive_dt_qty'] ." as diff
		FROM sac_purchase_order_dt a
		WHERE a.purchase_order_dt_id = ". $data['to_db'][$k_tb_name]['purchase_order_dt_id'] ."


	";

	//arr( $sql );

	$res = $dao->fetch( $sql );

	if ( $res && $res->diff < 0 ) {

		$data['field']['purchase_receive_dt_qty'] = 'ปริมาณเกินกว่าที่สั่งซื้อ';

		$data['message'] = 'ปริมาณเกินกว่าที่สั่งซื้อ';
	}


	return $data;


}

//
//104
function checkErpChequeIn( $data, $k_tb_name, $main_id, $param = array() ) {



	$dao = getDb();

	$sql = "


		SELECT
			SUM( new_tb.t ) as t

		FROM (
			SELECT
				COUNT( * ) as t
			FROM ". $param['tb_main'] ." a
			WHERE a.". $param['pri_key'] ." IN (

				SELECT
					". $param['pri_key'] ."
				FROM ". $param['tb_check'] ."

			)
			AND a.". $param['pri_key'] ." = ". $main_id ."
				UNION
			SELECT
				COUNT( * )
			FROM erp_gl_trn
			WHERE from_id = ". $main_id ."
			AND from_table LIKE '". $param['tb_main'] ."'
			AND gl_trn_post = 1


		) as new_tb
	";
	//
	//arr( $sql );

	$res = $dao->fetch( $sql );

	if ( $res && $res->t > 0 ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร เกี่ยวข้อง';
	}


	return $data;


}

//
//
function checkErpArPayDisc( $data, $k_tb_name, $main_id ) {

	$dao = getDb();

	$sql = "


		SELECT
			SUM( new_tb.t ) as t

		FROM (
			SELECT
				COUNT( * ) as t
			FROM erp_ar_pay_disc
			WHERE disc_id = (

				SELECT
					disc_id
				FROM erp_ar_disc
				WHERE sale_return_id = ". $main_id ."
			)
			". sqlCheckErpGlTrn( 'erp_sale_return', $main_id ) ."
		) as new_tb



	";

	//arr( $sql );

	$res = $dao->fetch( $sql );

	if ( $res && $res->t > 0 ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร เกี่ยวข้อง';

	}

	return $data;

}




//
//
function checkErpArPayTrn( $data, $k_tb_name, $main_id ) {

	$dao = getDb();

	$sql = "
		SELECT
			SUM( new_tb.t ) as t

		FROM (
			SELECT
				COUNT( * ) as t
			FROM erp_ar_pay_trn
			WHERE trn_id = (
				SELECT
					trn_id
				FROM erp_ar_trn
				WHERE sale_inv_id = ". $main_id ."
			)

				UNION
			SELECT
				COUNT( * ) as t
			FROM erp_sale_return
			WHERE sale_inv_id = ". $main_id ."
			". sqlCheckErpGlTrn( 'erp_sale_inv', $main_id ) ."
		) as new_tb
	";

	$res = $dao->fetch( $sql );

//	arr( $sql );

	if ( $res && $res->t > 0 ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร เกี่ยวข้อง';

	}

	return $data;

}



//
//
function sqlCheckErpGlTrn( $tb_name, $main_id ) {

	$sql = "

		UNION
		SELECT
			COUNT( * )
		FROM erp_gl_trn
		WHERE from_ LIKE '". $tb_name ."'
		AND sale_inv_id = ". $main_id ."
		AND gl_trn_post = 1
	";

	return $sql;


}

//
//
function checkErpApPayTrn( $data, $k_tb_name, $main_id ) {

	$dao = getDb();

	$sql = "
		SELECT
			SUM( new_tb.t ) as t

		FROM (
			SELECT
				COUNT( * ) as t
			FROM erp_ap_pay_trn
			WHERE trn_id = (
				SELECT
					trn_id
				FROM erp_ap_trn
				WHERE purchase_inv_id = ". $main_id ."
			)

				UNION
			SELECT
				COUNT( * ) as t
			FROM erp_purchase_return
			WHERE purchase_inv_id = ". $main_id ."

			". sqlCheckErpGlTrn( 'erp_purchase_inv', $main_id ) ."


		) as new_tb

	";



	$res = $dao->fetch( $sql );

	if ( $res && $res->t > 0 ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร เกี่ยวข้อง';

	}

	return $data;

}




//
//
function checkErpApPayDisc( $data, $k_tb_name, $main_id ) {

	$dao = getDb();

	$sql = "

		SELECT
			SUM( new_tb.t ) as t

		FROM (
			SELECT
				COUNT( * ) as t
			FROM erp_ap_pay_disc
			WHERE disc_id = (

				SELECT
					disc_id
				FROM erp_ap_disc
				WHERE purchase_return_id = ". $main_id ."
			)
			". sqlCheckErpGlTrn( 'erp_purchase_return', $main_id ) ."
		) as new_tb
	";

	$res = $dao->fetch( $sql );

	if ( $res && $res->t > 0 ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร เกี่ยวข้อง';

	}

	return $data;

}



//
// 78
function checkProduct( $data, $k_tb_name, $action_type , $pri_key = NULL, $main_id = NULL ) {
//arr( $data );
	//
	$dao = getDb();
//pu.product_um_name
//LEFT JOIN erp_product_um pu ON new_tb.product_um_id = pu.product_um_id
	$sql = "

		SELECT
			new_tb.product_id,
			new_tb.product_um_id


		FROM (
			SELECT
				product_id,
				product_um_id
			FROM erp_product_um_rate
			WHERE product_id = (

				SELECT
					product_id
				FROM erp_product_dt
				WHERE product_dt_id = ". $data['to_db'][$k_tb_name]['product_dt_id'] ."
			)
			AND product_um_id = ". $data['to_db'][$k_tb_name]['product_um_id'] ."

			UNION
			SELECT
				product_id,
				product_um_id

			FROM erp_product
			WHERE product_id = (

				SELECT
					product_id
				FROM erp_product_dt
				WHERE product_dt_id = ". $data['to_db'][$k_tb_name]['product_dt_id'] ."
			)
			AND product_um_id = ". $data['to_db'][$k_tb_name]['product_um_id'] ."

		) as new_tb
	";

	$res = $dao->fetch( $sql );

	if( !$res ) {
		$data['field']['product_dt_id'] = 'ยังไม่มีข้อมูลในระบบ';
		$data['field']['product_um_id'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'หน่วยนับสินค้าไม่ถูกต้อง กรุณาเลือกใหม่ หรือเพิ่มหน่วยนับใหม่ที่ต้องการ <a target="_blank" href="'. getLink( 32 ) .'">ที่นี่</a>';


	}
	return $data;
}




//
//104
function checkPurchaseReceiveDt( $data, $k_tb_name, $main_id ) {

	$dao = getDb();

	$sql = "
		SELECT
			COUNT( * ) as t
		FROM sac_purchase_receive_dt a
		WHERE purchase_receive_dt_id IN (

			SELECT
				purchase_receive_dt_id
			FROM sac_purchase_shipment_dt

			UNION
			SELECT
				purchase_receive_dt_id
			FROM sac_send_order_dt
		)
		AND a.purchase_receive_id = ". $main_id ."
	";

	//arr( $sql );

	$res = $dao->fetch( $sql );

	if ( $res && $res->t > 0 ) {

		$data['field']['qty'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'ไม่สามารถทำการแก้ไขได้เนื่องจากมีเอกสาร เกี่ยวข้อง';
	}


	return $data;


}



//
// 90
function checkErpGlTrnDt( $data, $k_tb_name, $action_type , $pri_key = NULL, $main_id = NULL ) {

	if (
		( $data['to_db'][$k_tb_name]['gl_trn_dt_debit'] == $data['to_db'][$k_tb_name]['gl_trn_dt_credit'] )

		||
		( $data['to_db'][$k_tb_name]['gl_trn_dt_debit'] != 0 && $data['to_db'][$k_tb_name]['gl_trn_dt_credit'] != 0 )
	) {

		$data['field']['gl_trn_dt_debit'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'กรอกข้อมูลผิด';

	}
	return $data;
}

//
//
function checkCompany( $data, $k_tb_name ) {

	//
	$dao = getDb();

	$sql = "
		SELECT
			count( * ) as t
		FROM erp_book_company_sale_order

		WHERE company_id = ". $data['to_db'][$k_tb_name]['company_id'] ."

		AND book_id = ". $data['to_db'][$k_tb_name]['book_id'];


	$res = $dao->fetch( $sql );

	if( $res && $res->t == 0 ) {
		$data['field']['company_id'] = $data['field']['book_id'] = 'ยังไม่มีข้อมูลในระบบ';
	}

	return $data;

}

//
// 114
function checkErpSaleReturnDt( $data, $k_tb_name, $pri_key = NULL, $main_id = NULL, $prefix = 'sale_' ) {

	$dao = getDb();


	$cond = '';

	if( !empty( $pri_key ) )
		$cond = "AND ". $prefix ."return_dt_id != ". $pri_key;

	$sql = "
		SELECT

			a.*

		FROM erp_". $prefix ."inv_dt a



		HAVING a.". $prefix ."inv_id = (
			SELECT
				". $prefix ."inv_id
			FROM erp_". $prefix ."return
			WHERE ". $prefix ."return_id = ". $main_id ."
		)
		AND (
			SELECT
				IFNULL( SUM( ". $prefix ."inv_dt_qty_um ), 0 ) + ". $data['to_db'][$k_tb_name][$prefix .'inv_dt_qty_um'] ."
			FROM  erp_". $prefix ."return_dt
			WHERE ". $prefix ."inv_dt_id = a.". $prefix ."inv_dt_id
			". $cond ."
		) <= a.". $prefix ."inv_dt_qty_um
	";

	$res = $dao->fetch( $sql );

	if ( !$res ) {
		$data['field'][$prefix .'inv_dt_qty_um'] = 'ยังไม่มีข้อมูลในระบบ';
		$data['message'] = 'จำนวนเกิน';
	}

	return $data;

}


//
// 78
function checkProductUm( $data, $k_tb_name, $action_type , $pri_key = NULL, $main_id = NULL ) {

	$dao = getDb();

	$sql = "

		SELECT
			new_tb.product_id,
			new_tb.product_um_id,
			pu.product_um_name

		FROM (
			SELECT
				product_id,
				product_um_id
			FROM erp_product_um_rate
			UNION
			SELECT
				product_id,
				product_um_id

			FROM erp_product
		) as new_tb
		LEFT JOIN erp_product_um pu ON new_tb.product_um_id = pu.product_um_id

		WHERE new_tb.product_id = (

			SELECT
				product_id
			FROM erp_product_dt
			WHERE product_dt_id = ". $data['to_db'][$k_tb_name]['product_dt_id'] ."
		)
		AND new_tb.product_um_id = ". $data['to_db'][$k_tb_name]['product_um_id'];

	$res = $dao->fetch( $sql );


	//arr( $res );
	if( !$res ) {
		$data['field']['product_dt_id'] = 'ยังไม่มีข้อมูลในระบบ';
		$data['field']['product_um_id'] = 'ยังไม่มีข้อมูลในระบบ';

		$data['message'] = 'กรุณาเพิ่มจำนวนต่อหน่วยที่สินค้าก่อน';

	}

	return $data;

}


//
//
function checkErpSaleInvDt( $data, $k_tb_name, $getData, $pri_key = NULL ) {


	//arr( $data );
	$dao = getDb();

	$sql = "

		SELECT
			new_tb.stock_temp_dt_id,

			b.stock_dt_qty - IFNULL( SUM( new_tb.sale_inv_dt_qty ), 0 ) as stock_dt_qty


		FROM (

			SELECT
				CONCAT( 'a', sale_inv_dt_id ),
				stock_temp_dt_id,
				sale_inv_dt_qty
			FROM erp_sale_inv_dt
			WHERE sale_inv_dt_id != ". $pri_key ."

			UNION
			SELECT
				NULL,
				". $getData->stock_temp_dt_id .",
				". $data['to_db'][$k_tb_name]['sale_inv_dt_qty_um'] * $data['to_db'][$k_tb_name]['product_um_rate']."

		) as new_tb
		LEFT JOIN erp_stock_temp_dt b ON new_tb.stock_temp_dt_id = b.stock_temp_dt_id

		WHERE new_tb.stock_temp_dt_id = ". $getData->stock_temp_dt_id ."

	";

	$res = $dao->fetch( $sql );


	if ( $res->stock_dt_qty < 0 ) {

		$data['field']['sale_inv_dt_qty_um'] = 'ยังไม่มีข้อมูลในระบบ';


		$data['message'] = 'จำนวนเกินใบสั่งขาย';

	}

	return $data;

}

//
//
function checkStockProductForOrder( $data, $k_tb_name ) {

	$dao = getDb();

	$stock_dt_qty = -1 * $data['to_db'][$k_tb_name]['sale_order_dt_qty_um'];

	$filters = array();

	$filters[] = "a.product_dt_id = ". $data['to_db'][$k_tb_name]['product_dt_id'];

	$cond = '';

	if ( !empty( $filters ) ) {

		$cond = "WHERE " . implode( ' AND ', $filters );
	}

	$sql = "

		SELECT
			IFNULL( SUM( IF( LEFT( c.stock_act_code, 1 ) = 0, b.stock_dt_qty, -1 * b.stock_dt_qty ) ), 0 ) as sum_stock_dt_qty
		FROM  erp_product_dt a
		LEFT JOIN erp_stock_dt b ON a.product_dt_id = b.product_dt_id
		LEFT JOIN erp_stock c ON b.stock_id = c.stock_id
	". $cond;

	$res = $dao->fetch( $sql );

	if ( $res ) {

		if ( ( $res->sum_stock_dt_qty + $stock_dt_qty ) < 0 ) {

			$data['field']['sale_order_dt_qty_um'] = 'มีสต็อกไม่พอ';
			$data['message'] = 'มีสต็อกไม่พอกับรายการจ่าย';
		}
	}

	return $data;
}

//
//
function checkEditSo( $param ) {

	$dao = getDb();

	$data = $param['data'];

	if( $param['action_type'] != 'add' ) {

		$sql = "
			SELECT
				doc_no as si_no
			FROM erp_stock si
			WHERE tbName = 'erp_sale_inv'
			AND lock_parent_id = ". $param['parent_id'] ."
			AND si.id IN (
				SELECT
					tb_id
				FROM admin_doc_inspect
				WHERE tb_name LIKE 'erp_stock'
			)
		";
if ( empty( $_SESSION['user']->user_admin ) ) {
	
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {

			$data['message'] = 'กรุณาทำการยกเลิกลายเซ็น ใบกำกับภาษีเลขที่ '. $va->si_no .' ก่อนทำรายการ';

			$data['field']['doc_no'] = $data['message'];

			$data['success'] = 0;

			return $data;
		}
}

	}

	return $data;
}

//
//
function checkInsertStock( $param ) {
	
	

	$dao = getDb();

	$data = $param['data'];
	
	
	return $data;

	foreach( $param['data']['to_db'] as $kd => $vd ) {

		if( in_array( $param['action_type'], array( 'edit', 'add' ) ) ) {
			
			
			if( isset( $param['check_color_in'] ) ) {
				
				if( $vd['color'] == $vd['color_in'] ) {
					
					$data['message'] = ' กรุณาเลือกเฉดต่างจากโอน';

					$data['field']['color_in'] = $data['message'];

					$data['success'] = 0;

					return $data;
					
				}
			}

			if( empty( $param['allow_zone'] ) ) {
				$sql = "

					SELECT
						*
					FROM erp_zone
					WHERE zone_id = ". $vd['zone_id'] ."
				";

				foreach( $dao->fetchAll( $sql ) as $kz => $vz ) {

					if( isset( $param['main_data_before']->book_id ) &&  $vz->book_id_ != $param['main_data_before']->book_id ) {

						$data['message'] = ' กรุณาเลือกโซนสินค้าให้ตรงกับเล่มเอกสาร';

						$data['field']['zone_id'] = $data['message'];

						$data['success'] = 0;

						return $data;

					}

				}
				
			}

			if( !isset( $param['moveOverWarehouse'] ) ) {

				if( !empty( $vd['zone_in_id'] ) ) {

					$sql = "
						SELECT
							*
						FROM erp_zone
						WHERE zone_id = ". $vd['zone_in_id'] ."
					";

					foreach( $dao->fetchAll( $sql ) as $kz => $vz ) {

						if( isset( $param['main_data_before']->book_id ) &&  $vz->book_id_ != $param['main_data_before']->book_id ) {

							$data['message'] = ' กรุณาเลือกโซนสินค้าให้ตรงกับเล่มเอกสาร';

							$data['field']['zone_in_id'] = $data['message'];

							$data['success'] = 0;

							return $data;
						}

					}

				}
			}


		}

		$stockConfig = stockConfig( $vd['tbName'] );


		if( in_array( $param['action_type'], array( 'edit', 'add' ) ) ) {

			if( !isset( $vd['qty_rate'] ) ) {

				$vd['qty_rate'] = 1;

			}

			if( !empty( $vd['zone_in_id'] ) ) {

				if( $vd['zone_in_id'] == $vd['zone_id'] && empty( $param['allow_same_zone'] ) ) {

					$data['message'] = 'คุณย้ายสินค้าลงในโซนเดิม กรุณาเลือกโซนใหม่';

					$data['field']['zone_in_id'] = $data['message'];


					$data['success'] = 0;

					return $data;


				}

				$sqlUnion[] = "
					(
						SELECT
							". $vd['product_id'] ." as product_id,
							'b' as gname,
							'". $vd['doc_date'] ."' as doc_date,
							CONCAT( 1, '-', ". $vd['product_id'] .", '-', '". $vd['color'] ."', '-', ". $vd['zone_id'] .", '-', ". $param['main_data_before']->book_id ." ) as product,
							" . ( $vd['qty_um'] * $vd['qty_rate'] * $stockConfig['factor'] ) ." AS total_qty

					)
				";

				$sqlUnion[] = "
					(
						SELECT
							". $vd['product_id'] ." as product_id,
							'c' as gname,
							'". $vd['doc_date'] ."' as doc_date,
							CONCAT( 1, '-', ". $vd['product_id'] .", '-', '". $vd['color'] ."', '-', ". $vd['zone_in_id'] .", '-', ". $param['main_data_before']->book_id ." ) as product,
							" . ( $vd['qty_um'] * $vd['qty_rate'] * 1 ) ." AS total_qty

					)
				";
			}
			else {

				$sqlUnion[] = "
					(
						SELECT
							". $vd['product_id'] ." as product_id,
							'd' as gname,
							'". $vd['doc_date'] ."' as doc_date,
							CONCAT( 1, '-', ". $vd['product_id'] .", '-', '". $vd['color'] ."', '-', ". $vd['zone_id'] .", '-', ". $param['main_data_before']->book_id ." ) as product,
							" . ( $vd['qty_um'] * $vd['qty_rate'] * $stockConfig['factor'] ) ." AS total_qty
					)
				";
			}
		}

		$sqlUnion[] = "
			(
				SELECT
					dt.product_id,
					'a' as gname,
					dt.doc_date,
					CONCAT( dt.admin_company_id, '-', dt.product_id, '-', dt.color, '-', dt.zone_id, '-', dt.book_id ) as product,
					SUM( dt.qty * dt.factor ) AS total_qty
				FROM erp_stock_dt dt
				[WHERE]
				GROUP BY
					dt.product_id,
					doc_date,
					product
			)
		";


		$sqlStock = "
			SELECT
				new_tb.product_id,
				new_tb.doc_date,
				new_tb.product,
				SUM( new_tb.total_qty ) as total_qty
			FROM (". implode( ' UNION ', $sqlUnion ) .") as new_tb
			GROUP BY
				product_id,
				doc_date,
				product
		";

//arr($sqlStock);
		$replace = array();

		if( in_array( $param['action_type'], array( 'edit', 'delete' ) ) ) {

			if( !empty( $vd['move_pare'] ) ) {

				$replace['WHERE'][] = "dt.move_pare != " . $vd['move_pare'];
			}
			else {
				$replace['WHERE'][] = "dt.id != " . $param['main_id'];

			}
		}

		if( $param['action_type'] == 'edit' ) {

			$sql = "

				SELECT
					MIN( new_tb.start_date ) as start_date
				FROM (
					SELECT
						'". $vd['doc_date'] ."' as start_date
					UNION
					SELECT
						doc_date as start_date
					FROM erp_stock_dt
					WHERE id = ". $param['main_id'] ."
				) as new_tb

			";

			$date = $dao->fetch( $sql )->start_date;

		}
		else {

			$date = $vd['doc_date'];
		}


		//$date = '1980-01-01';
		$_SESSION['myStockDate'] = $date;

		if( in_array( $param['action_type'], array( 'add' ) ) ) {

			$product_colors[] = "CONCAT( '". $vd['product_id'] ."-". $vd['color'] ."' ) ";

		}
		else if( in_array( $param['action_type'], array( 'edit' ) ) ) {

			$product_colors[] = "CONCAT( '". $vd['product_id'] ."-". $vd['color'] ."' ) ";

			$product_colors[] = "( SELECT CONCAT( product_id, '-', color ) FROM  erp_stock_dt WHERE id = ". $param['main_id'] ." )";

	///arr( $product_colors );
		}
		else if( in_array( $param['action_type'], array( 'delete' ) ) ) {

			$product_colors[] = "( SELECT CONCAT( product_id, '-', color ) FROM  erp_stock_dt WHERE id = ". $param['main_id'] ." )";

		}

		$replace['WHERE'][] = "CONCAT( dt.product_id, '-', dt.color ) IN ( ". implode( ',', $product_colors ) ." )";

		$replace['WHERE'][] = "dt.doc_date >= '". $date ."'";

		$sqlStock = genCond_( $sqlStock, $replace );

	//arr( $sqlStock );


	//12156-001-166-33


	//12156-001-261-33
		$_SESSION['productsUpdateStock'] = array();
		foreach( $dao->fetchAll( $sqlStock ) as $kr => $vr ) {

		//arr( $vr );
			if( !isset( $total_qty_bal[$vr->product] ) ) {

				$sql = "
					SELECT
						dt.id,
						doc_date,
						order_number,
						pcz_qty_bal,
						dt.doc_no
					FROM erp_stock_dt dt
					WHERE CONCAT( dt.admin_company_id, '-', dt.product_id, '-', dt.color, '-', dt.zone_id, '-', dt.book_id ) = '". $vr->product ."'
					AND dt.doc_date < '". $date ."'

					ORDER BY
						order_number DESC
					LIMIT 0, 1
				";

				$total_qty_bal[$vr->product] = 0;

				$doc_no = '';

				foreach( $dao->fetchAll( $sql ) as $kp => $vp ) {
					///echo 'dsadsff';
				///arr( $vp );
					$doc_no = $vp->doc_no;

					$total_qty_bal[$vr->product] = $vp->pcz_qty_bal;
				}
			}

			$total_qty_bal[$vr->product] += $vr->total_qty;
			/*
			if( $total_qty_bal[$vr->product] < 0 ) {

				$data['field']['qty_um'] = 'เลขที่นี้ถูกใช้อ้างอิงแล้ว';

				$data['message'] = $vr->product . 'ปริมาณเกินจำนวนในโซน ' . $total_qty_bal[$vr->product] . 'ณ วันที่ ' . $vr->doc_date . ' ' . $doc_no;

				$data['success'] = 0;

				return $data;
			}
			*/

			$_SESSION['productsUpdateStock'][$vr->product_id] = $vr->product_id;

		}


		$_SESSION['productsUpdateStock'][9999999] = 9999999;

		if( !empty( $param['beforeUpdate'] ) ) {
			$_SESSION['productsUpdateStock'][$param['beforeUpdate']->product_id] = $param['beforeUpdate']->product_id;

		}

	}

	return $data;
}


 
function updateSiamartProduction($param = array())
{

	//arr($param['data']['doc_date']);
	$dao_last_id = $param['parent_id'];

	$dao = getDb();


	$sql = "
	UPDATE siamart_production 
	SET 
		new_expect_date = CONCAT( SUBSTRING_INDEX( expect_date, '/', -1 ), '-', SUBSTRING_INDEX( expect_date, '/', 1 ) ,'-01'  ); 
	
	
	UPDATE siamart_production_plan_dt 
	SET 
		new_plan_month = CONCAT( SUBSTRING_INDEX( plan_month, '/', -1 ), '-', SUBSTRING_INDEX( plan_month, '/', 1 ) ,'-01'  ); 
	";
//	arr($sql);
	$dao->execDatas($sql);

	$sql = "
		DELETE FROM siamart_production_dt
		WHERE 
		(
			product_id NOT IN ( SELECT id FROM siamart_production_products )
			OR 
			product_id IN ( SELECT id FROM siamart_production_products WHERE active != 1 )
		
		)	
		AND parent_id = " . $dao_last_id . "
	";

	$dao->execDatas($sql);

	$sql = "
		REPLACE INTO siamart_production_dt ( 
			product,
			parent_id,
			product_id
		)
		SELECT 
			CONCAT( p.size, ' ', p.code, ' ', p.name ) as product,
			" . $dao_last_id . " as parent_id,
			p.id as product_id
		FROM siamart_production_products p
		WHERE p.active = 1
		AND p.id NOT IN ( 
			SELECT 
				product_id 
			FROM siamart_production_dt 
			WHERE parent_id = " . $dao_last_id . "  
		)
		ORDER BY 
			p.size ASC
	";
	

	$dao->execDatas($sql);
	//erp_product
	//erp_stock_bal_zone
	//erp_sale_order_dt 
	$sql = "
		UPDATE siamart_production_dt dt
		LEFT JOIN (
			
			SELECT 
				IFNULL( (
					SELECT 
						SUM( plan_qty ) 
					FROM siamart_production_plan_dt dt 
					LEFT JOIN siamart_production pd ON dt.parent_id = pd.id
					WHERE product_id = p.id 
					AND pd.doc_date < '" . $param['data']['doc_date'] . "'
					AND dt.done_qty = 0
				), 0 ) as wating,
				pc.main_stock,
				pc.sub_stock,
				offline_order.main_order,
				offline_order.sub_order,
				p.id as product_id

			FROM (
				SELECT 
					* 
				FROM siamart_production_products 
				WHERE active = 1
				ORDER BY size ASC
			) as p
			LEFT JOIN (
			
				SELECT 
					pc.code,
					SUM( pc.qty * ( pc.qty >= 300 ) ) as main_stock,
					SUM( pc.qty * ( pc.qty < 300 ) ) as sub_stock 
				FROM (
					SELECT 
						p.product_code_ as code,
						pc.qty as qty
						
					FROM erp_stock_bal_zone pc
					INNER JOIN erp_product p ON pc.product_id = p.product_id
					WHERE p.product_grade IN ( 'A', 1 )
				
				
				) as pc	
				GROUP BY
						code	
			) as pc ON p.code = pc.code 
			LEFT JOIN (
				SELECT 
					pc.code,
					SUM( pc.qty ) as main_order,
					SUM( pc.qty * ( pc.qty < 300 ) ) as sub_order 
				FROM (
				
					SELECT 
						p.product_code_ as code,
						waiting_qty as qty
					FROM erp_sale_order_dt so
					INNER JOIN erp_product p ON so.product_id = p.product_id
					WHERE p.product_grade IN ( 'A', 1 )
				) as pc	
				GROUP BY 
					code	
			) as offline_order ON p.code = offline_order.code 

		) as new_tb ON dt.product_id = new_tb.product_id
		SET 
			dt.sub_order = new_tb.sub_order, 
			dt.sub_stock = new_tb.sub_stock,
			dt.main_stock = new_tb.main_stock,
			dt.wating = new_tb.wating,
			dt.main_order = new_tb.main_order
		
		WHERE dt.parent_id = " . $dao_last_id . "
	";

	
	$dao->execDatas($sql);
}
//
//
function updateProductionPlanDt($dao_last_id = NULL)
{

	$dao = getDb();

	$sql = "
		DELETE FROM siamart_production_plan_dt 
		WHERE production_dt_id IN (
		
			SELECT 
				id 
			FROM siamart_production_dt 
			WHERE produce_atdate = 0
		)
	";

	$dao->execDatas($sql);

	$sql = "
		REPLACE INTO siamart_production_plan_dt ( 
			product_id, 
			production_dt_id, 
			parent_id, 
			product, 
			plan_month, 
			plan_qty,
			new_plan_month
		)
		SELECT 
			new_tb.product_id, 
			new_tb.production_dt_id, 
			new_tb.parent_id, 
			new_tb.product, 
			DATE_FORMAT( new_tb.new_plan_month, '%m/%Y'   ) as plan_month, 
			new_tb.plan_qty ,
			new_tb.new_plan_month
		FROM(

			SELECT 
				dt.product_id, 
				dt.id as production_dt_id, 
				dt.parent_id, 
				dt.product, 
				prd.expect_date as plan_month, 
				dt.produce_atdate - IFNULL( ( SELECT SUM( plan_qty ) FROM siamart_production_plan_dt WHERE production_dt_id = dt.id ), 0 ) as plan_qty ,
				IFNULL( ( SELECT ADDDATE( MAX( new_plan_month ), INTERVAL 1 month ) FROM siamart_production_plan_dt WHERE production_dt_id = dt.id ), prd.new_expect_date ) as new_plan_month
			FROM siamart_production_dt dt 
			LEFT JOIN siamart_production prd ON dt.parent_id = prd.id
			 
			HAVING plan_qty != 0
			
		) as new_tb	
	";

	//arr( $sql );

	$dao->execDatas($sql);
}



//
function updateSiFollowSo($param)
{


	$dao = getDb();


	$sql = "
		UPDATE erp_stock st
		INNER JOIN erp_sale_order so ON st.lock_parent_id = so.id
		SET 
			st.company_id = so.company_id,
			st.company_branch_no = so.branch,
			st.tax_no = so.tax_no,
			st.company_id = so.company_id
		
		WHERE st.id = " . $param['parent_id'] . "
	";

	//arr( $sql );
	$dao->execDatas($sql);
}

//
//
function siamartProductionProducts($param)
{

	$dao = getDb();


	if ($param['action_type'] == 'add') {

		$sql = "
			UPDATE siamart_production_products p
			LEFT JOIN siamart_product_pc pc ON p.pc_id = pc.id
			SET 
				p.g_type = LEFT( pc.group1, 2 ), 
				p.type = pc.group1, 
				p.size = pc.size, 
				p.code = pc.code, 
				p.name = REPLACE( pc.name, pc.size, '' ),
				p.active = 1 
			WHERE p.id = " . $param['parent_id'] . "
		";

		//arr( $sql );
		$dao->execDatas($sql);
	}
}


//
//
function updateScrDoc($param)
{

	$dao = getDb();

	$sql = "
		UPDATE erp_stock st
		INNER JOIN erp_select_si_dt dt ON st.id = dt.lock_parent_id
		INNER JOIN erp_select_si si ON dt.parent_id = si.id
		SET 
			st.saleman_id = si.saleman_id
		WHERE st.tbName = 'promotion'
	";

	$dao->execDatas($sql);
}

function copyGltrnDt($param)
{

	$dao = getDb();

	$sql = "
		DELETE FROM erp_gl_trn_dt 
		WHERE parent_id = " . $param['parent_id'] . "
		AND parent_id != " . $param['data']['copy_gl_id'] . ";
		
		INSERT INTO erp_gl_trn_dt ( new_gl, factor_credit, factor_debit, post, parent_id, close_month, his_name, admin_company_id, admin_gcompany_id, product_group, insert_type, g_name, credit_debit_bf, gl_name, credit_debit_bal, g_credit_debit_bal, order_number, tb_parent, doc_no, doc_date, gl_id, credit, debit, sum_credit_debit, remark, type ) 
	
		SELECT 
			
			new_gl, 
			factor_credit, 
			factor_debit, 
			post, 
			
			" . $param['parent_id'] . " as parent_id, 
			close_month, his_name, admin_company_id, admin_gcompany_id, product_group, insert_type, g_name, credit_debit_bf, gl_name, credit_debit_bal, g_credit_debit_bal, order_number, tb_parent, doc_no, doc_date, gl_id, credit, debit, sum_credit_debit, remark, type
		FROM erp_gl_trn_dt 
		WHERE parent_id = " . $param['data']['copy_gl_id'] . "
		AND parent_id != " . $param['parent_id'] . ";
	";

	$dao->execDatas($sql);
}



//
//
function updateSaleOrder($param)
{

	//arr( $param['main_data_before']->company_id );
	//arr( $param['data']['company_id'] );

	$dao = getDb();

	if ($param['action_type'] == 'edit') {

		if (isset($param['main_data_before']->company_id) && $param['data']['company_id'] != $param['main_data_before']->company_id) {


			$sql = "
				UPDATE
					erp_sale_order o
				LEFT JOIN (

					SELECT
						CONCAT( cdt.parent_id, '-', cdt.book_id ) as gName,
						cdt.saleman_id,
						cdt.pc_id,
						cdt.due,
						c.company_taxno,
						c.company_branch_no,
						c.company_tel,
						c.company_address,
						c.company_zipcode
					FROM erp_company_dt cdt
					LEFT JOIN erp_company c ON cdt.parent_id = c.company_id
				) cdt ON CONCAT( o.company_id, '-', o.book_id ) = cdt.gName
				SET
					o.doc_no = UPPER( o.doc_no ),
					o.due = cdt.due,
					o.saleman_id = cdt.saleman_id,
					o.pc_id =  cdt.pc_id,
					o.tax_no = cdt.company_taxno,
					o.branch = cdt.company_branch_no,
					o.tel = cdt.company_tel,
					o.custom_address = concat( REPLACE( cdt.company_address, cdt.company_zipcode , '' ), ' ',  cdt.company_zipcode ),
					o.send_location = concat( REPLACE( cdt.company_address, cdt.company_zipcode , '' ), ' ',  cdt.company_zipcode, ' โทร', cdt.company_tel ),
					o.receive_id = " . $param['data']['company_id'] . "
				WHERE o.id = " . $param['parent_id'] . "
			";
			//arr( $sql );

			$dao->execDatas($sql);
		} else {

			$sql = "
				UPDATE
					erp_sale_order o
				LEFT JOIN (

					SELECT
						CONCAT( cdt.parent_id, '-', cdt.book_id ) as gName,
						cdt.saleman_id,
						cdt.pc_id,
						cdt.due,
						c.company_taxno,
						c.company_branch_no,
						c.company_tel
					FROM erp_company_dt cdt
					LEFT JOIN erp_company c ON cdt.parent_id = c.company_id
				) cdt ON CONCAT( o.company_id, '-', o.book_id ) = cdt.gName
				SET
					o.doc_no = UPPER( o.doc_no ),
					o.due = IF( o.due = 0, cdt.due, o.due ),
					
					o.pc_id =  IF( o.pc_id = 0, cdt.pc_id, o.pc_id ),
					o.tax_no = IF( o.tax_no = '', cdt.company_taxno, o.tax_no ),
					o.branch = IF( o.branch = '', cdt.company_branch_no, o.branch ) ,
					o.tel = IF( o.tel = '', cdt.company_tel, o.tel )
				WHERE o.id = " . $param['parent_id'] . "
			";

			$dao->execDatas($sql);
		}
	} else {


		$sql = "
			UPDATE
				erp_sale_order o
			LEFT JOIN (

				SELECT
					CONCAT( cdt.parent_id, '-', cdt.book_id ) as gName,
					cdt.saleman_id,
					cdt.pc_id,
					cdt.due,
					c.company_taxno,
					c.company_branch_no,
					c.company_tel
				FROM erp_company_dt cdt
				LEFT JOIN erp_company c ON cdt.parent_id = c.company_id
			) cdt ON CONCAT( o.company_id, '-', o.book_id ) = cdt.gName
			SET
				o.doc_no = UPPER( o.doc_no ),
				o.due = IF( o.due = 0, cdt.due, o.due ),
				o.saleman_id = IF( o.saleman_id = 0, cdt.saleman_id, o.saleman_id ),
				o.pc_id =  IF( o.pc_id = 0, cdt.pc_id, o.pc_id ),
				o.tax_no = IF( o.tax_no = '', cdt.company_taxno, o.tax_no ),
				o.branch = IF( o.branch = '', cdt.company_branch_no, o.branch ) ,
				o.tel = IF( o.tel = '', cdt.company_tel, o.tel )
			WHERE o.id = " . $param['parent_id'] . "
		";

		$dao->execDatas($sql);
	}
}

//
//
function updateDeadFag($param)
{

	//arr( $param['data']['doc_date'] );
	return;

	$dao = getDb();

	$doc_date = $param['data']['doc_date'];

	$sql = "
		UPDATE erp_stock_dt dt 
		SET 
			dt.dead_stock_code = NULL,
			dt.dead_stock_auto = 0
		WHERE dt.doc_date >= '" . $doc_date . "'
		AND LAST_DAY( dt.doc_date ) <= LAST_DAY( '" . $doc_date . "' )
	";

	//arr( $sql );

	$dao->execDatas($sql);


	$sql = "
		UPDATE erp_stock_dt dt 
		INNER JOIN erp_product p ON p.product_id = dt.product_id
		INNER JOIN erp_stock_dead_dt olddt ON dt.product_id = olddt.product_id
		SET 
			dt.dead_stock_code = olddt.dead_stock_flag,
			dt.dead_stock_auto = 1
		WHERE dt.doc_date >= '" . $doc_date . "'
		AND LAST_DAY( dt.doc_date ) <= LAST_DAY( '" . $doc_date . "' )
		AND p.group_master_id IN ( 1, 2, 3 )
		AND dt.tbName = 'erp_sale_inv'
	";

	//AND dt.tbName = 'erp_sale_inv'
	//	arr( $sql );

	$dao->execDatas($sql);


	$sql = "
		UPDATE erp_stock_dt dt 
		INNER JOIN erp_product p ON p.product_id = dt.product_id
		SET 
			dt.dead_stock_code = 'DB0',
			dt.dead_stock_auto = 1	 
		WHERE dt.doc_date >= '" . $doc_date . "'
		AND LAST_DAY( dt.doc_date ) <= LAST_DAY( '" . $doc_date . "' )
		AND dt.tbName = 'erp_sale_inv'
		AND p.product_grade = 'B'
		AND dt.dead_stock_auto = 0
		AND p.group_master_id IN ( 1, 2, 3 )
	";

	$dao->execDatas($sql);
}

 

//
//
function importUmiCsvData($param)
{

	$main_id = $param['parent_id'];

	$dao = getDb();

	//arr( $param );
	$file = '/var/www/html/sac2015/file_upload/umi_data/fb36644bb45944c4db1c79e3dbdc2fb2.csv';

	//if ( true ) {
	if (!empty($_FILES['csv_file']['tmp_name'])) {


		$file = $_FILES['csv_file']['tmp_name'];

		$insert_table_name = 'aa.umi_data_dt';

		$sql = "
			DELETE FROM " . $insert_table_name . "
			WHERE  parent_id =  " . $main_id . "";

		$dao->execDatas($sql);

		set_time_limit(0);

		$objCSV = fopen($file, "r");

		//$dao->dbh->exec( 'SET NAMES tis620' );

		$i = 0;

		$skip = array();

		while (($objArr = fgetcsv($objCSV, 1000, ",")) !== FALSE) {

			++$i;

			if ($i == 1) {

				continue;
			}



			//exit;

			//$ex = explode( ';', $objArr[0] );
			//arr( $objArr);
			$ex = $objArr;
			//exit;
			//if( !isset( $ex[5] ) ) {

			//arr( $objArr);
			//arr( $ex );
			///	}

			//echo 'count';
			//echo count( $ex );
			//arr( $ex );


			//continue;

			$skip[] = $id = getSkipId($insert_table_name, 'id', $skip);

			$sqlUnion[] = "
				SELECT 
					" . $id . " as id,
					" . $main_id . " as parent_id, 
					'" . $ex[0] . "' as Material, 
					'" . $ex[1] . "' as DESCRIPTION, 
					'" . $ex[2] . "' as Plant, 
					'" . $ex[3] . "' as Batch, 
					'" . str_replace(',', '', $ex[4]) . "' as QTY_CONV, 
					'" . $ex[5] . "' as UM_CONV
			";


			if (count($sqlUnion) > 500) {

				$sql = "
					INSERT INTO " . $insert_table_name . " ( id, parent_id, Material, DESCRIPTION, Plant, Batch, QTY_CONV, UM_CONV ) 
					
					SELECT 
						id, parent_id, Material, DESCRIPTION, Plant, Batch, QTY_CONV, UM_CONV
					FROM (
					" . implode(' UNION ', $sqlUnion) . "
					) as new_tb 	

				";

				$dao->execDatas($sql);

				$sqlUnion = array();
			}
		}


		if (count($sqlUnion) > 0) {

			$sql = "
				INSERT INTO " . $insert_table_name . " ( id, parent_id, Material, DESCRIPTION, Plant, Batch, QTY_CONV, UM_CONV ) 
				
				SELECT 
					id, parent_id, Material, DESCRIPTION, Plant, Batch, QTY_CONV, UM_CONV
				FROM (
				" . implode(' UNION ', $sqlUnion) . "
				) as new_tb 	

			";

			$dao->execDatas($sql);

			$sqlUnion = array();
		}

		fclose($objCSV);
	}
}






//
//
function importCsv($param)
{

	$main_id = $param['parent_id'];

	$dao = getDb();

	if (!empty($_FILES['stock_count_hd_csv_file']['tmp_name'])) {


		$file = $_FILES['stock_count_hd_csv_file']['tmp_name'];

		$insert_table_name = 'tb_stock_count_dt';

		$cond['stock_count_hd_id'] = $main_id;

		$cond['stock_count_dt_csv'] = 1;

		$sql = "
			DELETE FROM " . $insert_table_name . "
			WHERE  stock_count_hd_id =  " . $main_id . "
			AND user_full_name = ''";
		$dao->execDatas($sql);

		set_time_limit(0);

		$objCSV = fopen($file, "r");

		$dao->dbh->exec('SET NAMES tis620');

		$i = 0;

		$keep = array();

		while (($objArr = fgetcsv($objCSV, 1000, ",")) !== FALSE) {

			++$i;

			if ($i == 1) {

				foreach ($cond as $ka => $va)
					$objArr[] = $ka;

				$sql = "INSERT INTO " . $insert_table_name . " (" . implode(',', $objArr) . ") VALUES ";
			} else {

				foreach ($cond as $ka => $va) {

					$objArr[] = $va;
				}

				$keep[] = "('" . implode("','", $objArr) . "')";
			}
		}

		$sql .= implode(',', $keep);

		$dao->execDatas($sql);

		fclose($objCSV);
	}


	$sql = "
		UPDATE tb_stock_count_dt a
		LEFT JOIN tb_stock_count_hd b ON a.stock_count_hd_id = b.stock_count_hd_id
		SET
			a.stock_count_hd_date = b.stock_count_hd_date,
			a.stock_count_hd_memo = b.stock_count_hd_memo
		WHERE a.stock_count_hd_id =  " . $main_id;

	$dao->execDatas($sql);
}




//
//
function runGlCloseScript($param = array())
{

	$dao = getDb();

	$sql = "
		SELECT *
		FROM erp_gl_close_script_dt
		WHERE parent_id = " . $param['parent_id'] . "
	";


	foreach ($dao->fetchAll($sql) as $ka => $va) {

		//arr( $va );
		$param = convertObJectToArray($va);
		$val = call_user_func($va->func_name, $param);
	}
}

//
//
function updateStockFromPromotion($param = array())
{
	//arr( $param );
	//arr(  $param['main_data_before']->doc_no);
	//return;
	$dao = getDb();

	$sql = "
			UPDATE
				erp_stock o
			LEFT JOIN (
				SELECT
					pro.parent_id,
					SUM( pro.amt * pro.factor ) as before_vat_dt,
					SUM( pro.amt * pro.factor ) * ( 1 + (  st.vat_rate / 100 ) ) as after_vat_dt
					
					
				FROM erp_gl_trn_promotion pro
				LEFT JOIN erp_stock st ON pro.parent_id = st.id
				WHERE parent_id = " . $param['parent_id'] . "
				GROUP BY
					parent_id
			) as new_tb ON o.id = new_tb.parent_id
			SET
				o.total_before_vat = new_tb.before_vat_dt,
				o.total_after_vat = new_tb.after_vat_dt,
				o.vat_bath = new_tb.after_vat_dt - new_tb.before_vat_dt,
				o.vat_no = IF( o.vat_bath  != 0, o.doc_no, '' ) 
			WHERE o.id = " . $param['parent_id'] . ";
		";

	$dao->execDatas($sql);
}


//
//
function deleteGlTrnDt($param)
{

	$dao = getDb();
	if ($param['action_type'] == 'delete') {

		$sql = "
			DELETE FROM erp_gl_trn_dt 
			WHERE tb_parent = '" . $param['current_config']->tb_main . "'
			AND parent_id  = " . $param['parent_id'] . "
		";

		$dao->execDatas($sql);
	} else if ($param['action_type'] == 'edit') {
		//arr( $param['current_config']->tb_main );
		$sql = "
			UPDATE erp_gl_trn_dt 
			
			SET 
				doc_no = '" . $param['data']['doc_no'] . "',
				doc_date = '" . $param['data']['doc_date'] . "' 
			WHERE parent_id  = " . $param['parent_id'] . "
			AND tb_parent = '" . $param['current_config']->tb_main . "'
		";

		//arr( $param['data']['doc_date'] );

		$dao->execDatas($sql);
	}
}

//
//
function updateErpProductStandardCost( $param ) {

	return true;
	$dao = getDb();

	$tab_one = getConfig_(352);

	$json = json_decode($tab_one->in_rows_sql);

	$json->tb_main = $tab_one->tb_main;

	$sql = "
		SELECT
			*
		FROM erp_product_standard_cost
		WHERE id = " . $param['parent_id'] . "
	";

	$parent_data = $dao->fetch($sql);

	$param['data']['main_id'] = $param['parent_id'];

	$sql = "
		SELECT
			SUM( new_tb.price_per_unit ) as cost
		FROM (" . genJsonSql($json, $param['data'], $config = NULL, $parent_data) . ") as new_tb
	";

	$vc = $dao->fetch($sql);

	$cost = !empty($vc->cost) ? $vc->cost : 0;

	$sql = "
		UPDATE erp_product_standard_cost
		SET
			cost = " . $cost . "
		WHERE id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);

	$sql = "
		SELECT
			*
		FROM erp_product_standard_cost_dt
		WHERE product_id = " . $param['parent_id'] . "
		AND type = 1
	";

	foreach ($dao->fetchAll($sql) as $ka => $va) {

		$sql = "
			UPDATE erp_product_standard_cost_dt
			SET
				total_amt = " . $cost . ",
				price = ( qty * " . $cost . " / lifetime )
			WHERE id = " . $va->id . "
		";

		$dao->execDatas($sql);

		$param['parent_id'] = $va->parent_id;

		updateErpProductStandardCost($param);
	}
}


//
//
function updateSelectSi($param)
{

	$dao = getDb();


	$sql = "
		UPDATE
			erp_select_si o
		LEFT JOIN erp_company_dt cdt ON CONCAT( o.company_id, '-', o.book_id ) = CONCAT( cdt.parent_id, '-', cdt.book_id )
		SET
			o.saleman_id = IF( o.saleman_id = 0, cdt.saleman_id, o.saleman_id )
			
		WHERE o.id = " . $param['parent_id'] . "
	";





	//arr( $sql );
	$dao->execDatas($sql);
}



//
//
function insertErpSelectSiDt($param)
{

	$dao = getDb();

	$sql = "
		DELETE FROM erp_select_si_dt 
		WHERE parent_id = " . $param['parent_id'] . "
			
 
	";

	$dao->execDatas($sql);

	$sql = "
		INSERT INTO erp_select_si_dt (  
			parent_id, 
			
			lock_parent_id 
		) 
		SELECT
			" . $param['parent_id'] . " as parent_id, 
			 
			st.id as lock_parent_id 
		FROM 
			erp_stock st 
		WHERE st.id IN (
			" . $param['data']['lock_parent_id']  . "
		)		
			
 
	";

	$dao->execDatas($sql);
}




//
//
function saveFDate( $param ) {
	
	$dates[] = $param['data']['doc_date'];

	

	$doc_date = MIN( $dates );
	
	
	$dao = getDb();
	
	$sql = "
		SELECT 
			
			st.id,
			st.tbName,
			st.extra_due_date,
			st.company_id,
			st.admin_company_id,
			c.follow_due_date,
			c.follow_due_date_buy
		FROM erp_stock st
		INNER JOIN erp_company c ON st.company_id = c.company_id
		[WHERE]
		
	
	";
	
	$filters = array();
	$filters['WHERE'][] = "st.doc_date >= '". $doc_date ."'";
	$filters['WHERE'][] = "st.tbName IN ( 'erp_purchase_inv', 'erp_sale_inv', 'ar', 'ap' )";
	//$filters['WHERE'][] = "id NOT IN ( SELECT lock_parent_id FROM erp_ap_pay_trn )";
	//$filters['WHERE'][] = "st.id = 20499";
	
	if( !empty( $param['data']['company_id'] ) ) {
		
		$filters['WHERE'][] = "st.company_id  = ". $param['data']['company_id'] ."";
	}
	
	if( !empty( $param['data']['company_group_id'] ) ) {
		
		$filters['WHERE'][] = "
		
			st.company_id
			IN (

				SELECT company_id
				FROM erp_company
				WHERE company_group_id = ". $param['data']['company_group_id'] ."
			)
		";
	}
	
	$sql = genCond_( $sql, $filters );
	
	//arr( $sql );
	
//exit;

	$res = $dao->fetchAll( $sql );
	
	$count = count( $res );
	
	$sqlUnion = array();
	foreach( $res as $ka => $va ) {
		//arr( $va );
		//exit;
		if( $va->follow_due_date_buy == 1 && in_array( $va->tbName, array( 'erp_purchase_inv', 'ap' ) ) ) {
	
			
			
			$sqlUnion[] = "
				SELECT
					". $va->id ." as id,
					'". $va->extra_due_date ."' as f_date,
					'". $va->extra_due_date ."' as f_date_remark
				
			";
		}
		else if( $va->follow_due_date == 1 && in_array( $va->tbName, array( 'erp_sale_inv', 'ar' ) ) ) {
		
			
			$sqlUnion[] = "
				SELECT
					". $va->id ." as id,
					'". $va->extra_due_date ."' as f_date,
					'". $va->extra_due_date ."' as f_date_remark
				
			";
			
		}
		else {
	
			$param['tbName'] = $va->tbName;
			$param['due_date'] = $va->extra_due_date;
			$param['view'] = 'getFdate';
			$param['parent_id'] = $va->company_id;
			$param['company_id'] = $va->company_id;
			$param['admin_company_id'] = $va->admin_company_id;
			$f_date = selectFDate( $param );
		
			$sqlUnion[] = "
				SELECT
					". $va->id ." as id,
					'". $f_date['f_date'] ."' as f_date,
					'". $f_date['f_date_remark'] ."' as f_date_remark
				
			";
		}
		
		if( count( $sqlUnion ) > 2000 OR ( $ka + 1 ) == $count ) {
			$sql = "
				UPDATE erp_stock st
				INNER JOIN (
				". implode( ' UNION ', $sqlUnion ) ."
				) as new_tb ON st.id = new_tb.id
				SET 
					st.f_date = new_tb.f_date, 
					st.f_date_remark = new_tb.f_date_remark
				
			";
			
		//arr( $sql );
			
			$dao->execDatas( $sql );
			
			
			$sqlUnion = array();
	//	exit;	
		
		}
		
		
		
	}
	
	
	
			//arr( $va );
//exit;

	
}


//
//
function updateCompanyGroupId($param)
{

	$dao = getDb();
	$sql = "
		UPDATE erp_company
		SET company_group_id = 0
		WHERE company_group_id = " . $param['parent_id'] . ";


	";

	//arr( $sql );

	$dao->execDatas($sql);

	$sql = "
		UPDATE erp_company
		SET company_group_id = " . $param['parent_id'] . "
		WHERE company_id IN (
			SELECT company_id
			FROM erp_company_group_dt
			WHERE parent_id = " . $param['parent_id'] . "
		)


	";

	//arr( $sql );

	$dao->execDatas($sql);
}

//
//
function updateSaleOrderDt($param)
{

	$dao = getDb();
	$sql = "
		UPDATE erp_sale_order_dt odt
		SET 
			odt.waiting_qty = qty - cancel_qty  -  IFNULL ( (
				SELECT 
					sum( qty )
				FROM erp_stock_dt
				WHERE lock_dt_id = odt.id
				AND tbName LIKE 'erp_sale_inv'
			), 0 )


	";

	$dao->execDatas($sql);
}

//
//$param['action'] = 'cancel';
//$param['action'] = 'create';
function createUmiOrderFile($param)
{

	$dao = getDb();

	//$dao->dbh->exec('SET NAMES tis-620');

	$json['message'] = 'no file create';

	$filters = array();

	if ($param['action'] == 'cancel') {

		$param['separate'] = chr(9);
		$param['table'] = 'erp_sale_order';
		$param['column'] = 'cancel_sim_no';
		$param['pri_key'] = 'id';
		$param['template'] = "CONCAT( 'SX[find].UMI' )";
		$param['LPAD'] = 6;
		$param['fileName'] = '/home/sacuser/umi_order_files/cancel/[sim_no]';
		$filters['WHERE'][] = "dt.color != ''";
		$filters['WHERE'][] = "o.umi_so_no != ''";
	} else {

		$param['separate'] = chr(9);
		$param['table'] = 'erp_sale_order';
		$param['column'] = 'sim_no';
		$param['pri_key'] = 'id';
		$param['template'] = "CONCAT( 'S[find].UMI' )";
		$param['LPAD'] = 7;

		if (false) {

			$param['fileName'] = '/var/www/html/aaa/keep/test/[sim_no]';
		} else {

			$param['fileName'] = '/home/sacuser/umi_order_files/create/[sim_no]';
		}

		$filters['WHERE'][] = "dt.color != ''";

		$filters['WHERE'][] = "o.umi_so_no = ''";
	}

	$filters['WHERE'][] = "dt.parent_id = " . $param['parent_id'];

	$sql = "
		SELECT
			o.warehouse_umi as warehouse,
			date_format( NOW(), '%d.%m.%Y' ) as doc_date,
			dt.doc_no,
			c.umi_code as custom_name,
			rc.umi_code as receive_name,
			CONCAT( o.remark, ' ( P/O No.',o.po_no, ')' ) as remark,
			p.umi_code as code,
			dt.color,
			ROUND( dt.qty_um, 0 ) as qty_um,
			dt.parent_id,
			o.sim_no,
			o.cancel_sim_no,
			o.umi_so_no
		FROM erp_sale_order_dt dt
		LEFT JOIN erp_sale_order o ON dt.parent_id = o.id
		LEFT JOIN erp_product p ON dt.product_id = p.product_id
		LEFT JOIN erp_company c ON o.company_id = c.company_id
		LEFT JOIN erp_company rc ON o.receive_id = rc.company_id
		[WHERE]
		ORDER BY
			doc_no ASC
	";

	$sql = genCond_($sql, $filters);


	//arr( $sql );

	//exit;
	$keeps = array();
	foreach ($dao->fetchAll($sql) as $ka => $va) {

		$va->remark = iconv('UTF-8', 'ISO-8859-11//TRANSLIT//IGNORE', $va->remark) . '';

		$gName = $va->parent_id;

		$keeps[$gName][] = $va;
	}

	///arr( $keeps );
	foreach ($keeps as $kg => $vg) {

		$cName = $param['column'];

		if ($vg[0]->$cName == '') {

			$sim_no = genSimNo($param);

			$sql = "
				UPDATE erp_sale_order
				SET
					" . $param['column'] . " = '" . $sim_no . "'
				WHERE id = " . $vg[0]->parent_id . "
			";

			$dao->execDatas($sql);
		} else {

			$sim_no = $vg[0]->$cName;
		}

		$rows = array();

		//
		//create rows header
		if ($param['action'] == 'cancel') {

			$row = array(
				'HDR',
				'Change',
				'ZS50',
				'1000',
				'10',
				'00',
				'1000',
				'601',
				$vg[0]->doc_date,
				$vg[0]->doc_no,
				$vg[0]->custom_name,
				$vg[0]->receive_name,
				'26000001',
				'0',
				$vg[0]->umi_so_no,
				'Z1',
				' ',
				$vg[0]->remark
			);
		} else {

			$row = array(
				'HDR',
				'Create',
				'ZS50',
				'1000',
				'10',
				'00',
				'1000',
				'601',
				$vg[0]->doc_date,
				$vg[0]->doc_no,
				$vg[0]->custom_name,
				$vg[0]->receive_name,
				'26000001',
				'0',
				' ',
				' ',
				'D',
				$vg[0]->remark
			);
		}

		$rows[] = implode($param['separate'], $row);

		//
		//create rows detail
		if ($param['action'] != 'cancel') {

			foreach ($vg as $ka => $va) {

				$row = array(
					'ITM',
					$va->code,
					$va->warehouse,
					' ',
					$va->color,
					$va->qty_um,
					'CTN'
				);

				$rows[] = implode($param['separate'], $row);
			}
		}

		$datas = implode("\n", $rows);

		$fileName = str_replace('[sim_no]', $sim_no, $param['fileName']);

		file_put_contents($fileName, $datas);

		$messages[] = 'file ' . $fileName . ' have been ' . $param['action'] . '';
	}

	if (!empty($messages))
		$json['message'] = implode('<br>', $messages);


	//echo json_encode( $json );
}


//
//
function insertOrderExpectDt($param)
{

	$dao = getDb();

	$sql = "
		SELECT
			*
		FROM aa_order_expect_config
		WHERE id = " . $param['data']['config_id'] . "
	";

	//
	//
	foreach ($dao->fetchAll($sql) as $ka => $va) {

		$sqlUnion = array();

		foreach (json_decode($va->config) as $kc => $vc) {

			$ex = explode(',', $vc->groups);

			if (count($ex) > 1) {

				continue;
			}

			$sqlUnion[] = "
				SELECT
					" . $param['parent_id'] . " as parent_id,
					'" . $vc->groups . "' as group_id,
					(
						SELECT
							before_vat
						FROM aa_order_expect_dt
						WHERE group_id = '" . $vc->groups . "'
						ORDER BY time_update DESC
						LIMIT 0, 1

					) as before_vat
			";
		}

		$sql = "
			INSERT INTO aa_order_expect_dt ( parent_id, group_id, before_vat )
			SELECT
				new_tb.*
			FROM (
			" . implode(' UNION ', $sqlUnion) . "

			) as new_tb
			WHERE new_tb.group_id NOT IN (
				SELECT
					group_id
				FROM aa_order_expect_dt
				WHERE parent_id = " . $param['parent_id'] . "
			)
		";

		$dao->execDatas($sql);
	}
}



//
//
function insertOrderExpectDtGroup($param)
{

	$dao = getDb();


	$sql = "
		INSERT INTO aa_order_expect_dt_group ( company_group_id, parent_id, type_id, before_vat )

		SELECT
			a.company_group_id,
			" . $param['parent_id'] . " as parent_id,
			m.id as type_id,
			(

				SELECT
					before_vat
				FROM aa_order_expect_dt_group
				WHERE company_group_id = a.company_group_id
				AND type_id = m.id
				ORDER BY time_update DESC
				LIMIT 0, 1
			) as before_vat
		FROM erp_company_group a
		LEFT JOIN erp_product_group_master m ON 1 = 1
		WHERE a.company_group_id IN ( 1, 2, 10, 9, 6, 5, 98, 99 )
		AND m.id IN ( 1, 2, 3, 4 )
		AND CONCAT( a.company_group_id, '-', m.id ) NOT IN
		(
			SELECT
				CONCAT( company_group_id, '-', type_id ) AS tt
			FROM aa_order_expect_dt_group
			WHERE parent_id = " . $param['parent_id'] . "
		)

	";



	$dao->execDatas($sql);
}



//
//
function insertErpProductUmRate( $param )
{


	$dao = getDb();


	$sql = "TRUNCATE erp_product_um_rate;";
	$dao->execDatas($sql);
	$sql = "
		
		INSERT INTO erp_product_um_rate ( sale_um, product_id, qty_rate, id, lock_row )
		SELECT
			new_tb.sale_um,
			new_tb.id,
			new_tb.qty_rate,
			( SELECT id FROM erp_product_um_rate WHERE sale_um LIKE new_tb.sale_um AND product_id = new_tb.id ) as id,
			1 as lock_row

		FROM (
			SELECT
				p.sale_um,
				p.id,
				p.product_pack as qty_rate
			FROM sma_products p
			
			UNION

			SELECT
				p.stock_um as sale_um,
				p.id,
				1 as qty_rate
			FROM sma_products p
			
		) as new_tb;
	";
//[WHERE][WHERE]
	$filters = array();
	//$filters['WHERE'][] = "p.id = " . $param['parent_id'] . "";

	$sql = genCond_($sql, $filters);
///arr( $sql );

		$dao->execDatas($sql);
	
	
	$sql = "
		REPLACE INTO sma_units ( code, name )
		SELECT	
			'' as code, 
			
			sale_um as name
		FROM 
			erp_product_um_rate	
			
		GROUP BY 
			name
				
	";
	
	$dao->execDatas($sql);
}



//
//
function updateErpStock($param = array())
{

	$dao = getDb();

	$sql = "
		UPDATE erp_stock st
		LEFT JOIN (
			SELECT
				parent_id,
				SUM( amt ) as total_before_vat

			FROM erp_stock_pay
			WHERE parent_id = " . $param['parent_id'] . "
			GROUP BY
				parent_id
		) as new_tb ON st.id = new_tb.parent_id
		SET
			st.total_before_vat = new_tb.total_before_vat,
			st.total_after_vat = new_tb.total_before_vat * ( 100 + st.vat_rate ) / 100,
			st.vat_bath = new_tb.total_before_vat * st.vat_rate / 100

		WHERE id = " . $param['parent_id'] . "
	";

	//arr( $sql );
	$dao->execDatas($sql);
}


//
//
function updatePaymentVat($param)
{

	$dao = getDb();

	$dates[] = $param['data']['doc_date'];

	if (isset($param['main_data_before']->doc_date)) {

		$dates[] = $param['main_data_before']->doc_date;

		$sql = "
			REPLACE INTO erp_company_address (
				company_id,
				first_name,
				sir_name,
				mr,
				address,
				address_name,
				room,
				floor_no,
				address_no,
				mu,
				soi,
				road,
				post_code_id,
				branch_no
			)
			SELECT
				company_id,
				first_name,
				sir_name,
				mr,
				address,
				address_name,
				room,
				floor_no,
				address_no,
				mu,
				soi,
				road,
				post_code_id,
				branch_no
			FROM aa_payment_vat
			WHERE id = " . $param['parent_id'] . "
		";
	} else {

		$sql = "
			UPDATE aa_payment_vat pv
			LEFT JOIN erp_company_address ca ON pv.company_id = ca.company_id
			SET
				pv.first_name = ca.first_name,
				pv.sir_name = ca.sir_name,
				pv.mr = ca.mr,
				pv.address_name = ca.address_name,
				pv.room = ca.room,
				pv.floor_no = ca.floor_no,
				pv.address_no = ca.address_no,
				pv.mu = ca.mu,
				pv.soi = ca.soi,
				pv.road = ca.road,
				pv.post_code_id = ca.post_code_id,
				pv.branch_no = ca.branch_no
			WHERE pv.id = " . $param['parent_id'] . "
		";
	}

	$dao->execDatas($sql);

	$date = min($dates);
	//	$date = '1980-01-01';

	$sql = "
		SELECT
			pv.address_no,
			pv.room,
			pv.floor_no,
			pv.address_name,
			pv.mu,
			pv.soi,
			pv.road,
			(
				SELECT
					CONCAT( khang, ' ', khet, ' ', province, ' ', post_code ) as t
				FROM aa_post_code
				WHERE id = pv.post_code_id
				LIMIT 0, 1
			) as post_code,
			pv.id,
			CONCAT( pv.tbName, pv.sac_branch_no, '-', date_format( pv.doc_date, '%Y-%M' ), '-', dt.name ) as g,
			doc_no
		FROM aa_payment_vat pv
		LEFT JOIN aa_payment_doc_type dt ON pv.doc_type_id = dt.id
		WHERE doc_date >=  '" . $date . "'
		ORDER BY
			pv.tbName ASC,
			pv.sac_branch_no ASC,
			pv.doc_date ASC,
			pv.doc_type_id ASC
	";

	//arr( $sql );

	//
	$addressConfig = array(
		'address_no' => array('label' => ''),
		'room' => array('label' => ''),
		'floor_no' => array('label' => 'ชั้นที่'),
		'address_name' => array('label' => ''),
		'mu' => array('label' => 'ม.'),
		'soi' => array('label' => 'ซ.'),
		'road' => array('label' => 'ถ.'),
		'post_code' => array('label' => '')
	);

	//echo 'dsfasadf';

	foreach ($dao->fetchAll($sql) as $ka => $va) {

		//arr( $va->g );

		if (!isset($doc_no[$va->g])) {

			$sql = "

				SELECT
					pv.id,
					CONCAT( pv.tbName, pv.sac_branch_no, '-', date_format( pv.doc_date, '%Y-%M' ), '-', dt.name ) as g,
					doc_no
				FROM aa_payment_vat pv
				LEFT JOIN aa_payment_doc_type dt ON pv.doc_type_id = dt.id
				WHERE doc_date < '" . $date . "'
				AND CONCAT( pv.tbName, pv.sac_branch_no, '-', date_format( pv.doc_date, '%Y-%M' ), '-', dt.name ) = '" . $va->g . "'
				ORDER BY
					pv.doc_no DESC
				LIMIT 0, 1
			";


			$doc_no[$va->g] = 0;
			foreach ($dao->fetchAll($sql) as $kb => $vb) {

				$doc_no[$va->g] = $vb->doc_no;
			}
		}

		//if( !isset( $doc_no[$va->g] ) )
		//	$doc_no[$va->g] = 0;

		$doc_no[$va->g] += 1;


		$keepAddress = array();

		foreach ($addressConfig as $kb => $vb) {

			///arr( $va->$vb );

			if (!empty($va->$kb)) {

				if (!empty($vb['label'])) {

					$keepAddress[] = $vb['label'] . $va->$kb;
				} else {

					$keepAddress[] = $va->$kb;
				}
			}
		}


		$sql = "
			UPDATE aa_payment_vat
			SET
				doc_no = " . $doc_no[$va->g] . ",
				gName = '" . $va->g . "',
				address = '" . implode(' ', $keepAddress) . "'
			WHERE id = " . $va->id . "
		";

		$dao->execDatas($sql);
	}
}

//
//
function insertStockCustomer($param)
{

	$dao = getDb();

	$sql = "
		REPLACE INTO erp_stock_customer ( id, tel, custom_address, tax_no, branch )

		SELECT
			st.id,
			c.company_tel as tel,
			c.company_address as custom_address,
			c.company_taxno as tax_no,
			c.company_branch_no as branch

		FROM erp_stock st
		LEFT JOIN erp_company c ON st.company_id = c.company_id
		WHERE st.id = " . $param['parent_id'] . "

	";

	$dao->execDatas($sql);
}

//
//
function importSaleResultDt($param)
{

	$dao = getDb();

	if (empty($param['data']['file']))
		return false;

	set_time_limit(0);

	$file = FILE_FOLDER . '/' . $param['data']['file'];
	//$file = '/var/www/html/ccc/file_upload/aa_sale_result/9eb87380a82b8943a97ae0c9b9a4d97f.csv';

	//$file = 'keep/aa_sale_result_dt.csv';

	$insert_table_name = 'aa_sale_result_dt';

	set_time_limit(0);

	$objCSV = fopen($file, "r");

	//$dao->dbh->exec( 'SET NAMES tis620' );


	$sql = "DELETE FROM " . $insert_table_name . " WHERE parent_id = " . $param['parent_id'] . "";

	$dao->execDatas($sql);
	$i = 0;
	$sqlUnion = array();
	while (($objArr = fgetcsv($objCSV, 1000, ",")) !== FALSE) {

		++$i;

		if ($i == 1) {
			$branchs = array();
			foreach ($objArr as $kb => $vb) {

				if (!empty($vb)) {

					$branchs[] = $vb;
				}
			}
		} else if ($i > 2) {

			//arr($objArr);

			if (empty($objArr[0])) {

				continue;
			}

			$article_id = $objArr[0];

			$product_name = $objArr[1];

			$size = $objArr[2];


			$position = 2;
			foreach ($branchs as $kb => $vb) {

				$branch_id = $vb;

				++$position;
				$qty = 0;
				if (!empty($objArr[$position]))
					$qty = str_replace(',', '', $objArr[$position]);

				++$position;
				$sale_sqm = 0;
				if (!empty($objArr[$position]))
					$sale_sqm = str_replace(',', '', $objArr[$position]);



				++$position;
				$sale = 0;
				if (!empty($objArr[$position]))
					$sale = str_replace(',', '', $objArr[$position]);

				$sqlUnion[] = "
					SELECT
						" . $i . " as id,
						" . $param['parent_id'] . " as parent_id,
						" . $article_id . " as article_id,
						'" . $branch_id . "' as branch_id,
						" . $qty . " as qty,
						" . $sale . " as sale,
						'" . $product_name . "' as product_name,
						'" . $size . "' as size,
						" . $sale_sqm . " as sale_sqm
				";
			}
		}

		if (count($sqlUnion) > 100) {

			$sql = "
				INSERT INTO " . $insert_table_name . " ( parent_id, article_id, branch_id, qty, sale, sale_sqm, product_name, size )
				SELECT
					new_tb.parent_id,
					new_tb.article_id,
					new_tb.branch_id,
					new_tb.qty,
					new_tb.sale,
					new_tb.sale_sqm,
					new_tb.product_name,
					new_tb.size
				FROM ( " . implode(' UNION ', $sqlUnion) . " ) as new_tb
			";

			//arr( $sql );
			$dao->execDatas($sql);

			$sqlUnion = array();
		}
	}


	if (count($sqlUnion) > 0) {

		$sql = "

			INSERT INTO " . $insert_table_name . " ( parent_id, article_id, branch_id, qty, sale, sale_sqm, product_name, size )
			SELECT
				new_tb.parent_id,
				new_tb.article_id,
				new_tb.branch_id,
				new_tb.qty,
				new_tb.sale,
				new_tb.sale_sqm,
				new_tb.product_name,
				new_tb.size
			FROM ( " . implode(' UNION ', $sqlUnion) . " ) as new_tb
		";

		//arr( $sql );
		$dao->execDatas($sql);

		$sqlUnion = array();
	}

	$sql = "

		REPLACE INTO aa_sale_product ( size, name, article_id )
		SELECT
			new_tb.size,
			new_tb.name,
			new_tb.article_id
		FROM (
			SELECT
				CONCAT( article_id ) as gname,
				size,
				product_name as name,
				article_id
			FROM aa_sale_result_dt
			WHERE article_id NOT IN ( SELECT article_id FROM aa_sale_product )
			GROUP BY gname
		) as new_tb

	";

	$dao->execDatas($sql);

	fclose($objCSV);
}



//
//
function insertSaleReturn($param)
{


	$dao = getDb();

	$doc_priority = 40;
	$tbName = 'erp_sale_return';
	$factor = 1;

	$sql = "
		SELECT
			dt.id as lock_dt_id,
			dt.parent_id as lock_parent_id,
			dt.product_id,
			dt.price,
			dt.discount,
			dt.um_id,
			dt.qty_um - IFNULL( new_tb.qty_um, 0 ) as qty_um,
			dt.qty - IFNULL( new_tb.qty, 0 ) as needQty,
			dt.qty_rate,
			dt.um_label,
			dt.vat_type
		FROM erp_stock_dt dt
		LEFT JOIN (
			SELECT
				lock_dt_id,
				SUM( qty_um ) as qty_um,
				SUM( qty ) as qty
			FROM erp_stock_dt
			WHERE tbName = '" . $tbName . "'
			AND lock_parent_id = " . $param['data']['lock_parent_id'] . "
			GROUP BY
				lock_dt_id
		) as new_tb ON dt.id = new_tb.lock_dt_id
		WHERE dt.parent_id = " . $param['data']['lock_parent_id'] . "
	";

	$stInzone = array();
	$skip = array();
	foreach ($dao->fetchAll($sql) as $kn => $vn) {

		$need = $vn->needQty;

		$qty = $need;

		$need -= $qty;


		if ($vn->needQty  == 0)
			continue;

		$qty_um = $vn->qty_um * ($qty / $vn->needQty);



		$id = getSkipId('erp_stock_dt', 'id', $skip);

		$skip[] = $id;

		$sqlUnion[] = "
			SELECT
				" . $param['parent_id'] . " as parent_id,
				" . $vn->product_id . " as product_id,
				1 as zone_id,
				" . $qty_um . " as qty_um,
				" . $qty . " as qty,
				" . $vn->price . " as price,
				'" . $vn->discount . "' as discount,
				" . $vn->um_id . " as product_um_id,
				" . $vn->lock_dt_id . " as lock_dt_id,
				" . $vn->lock_parent_id . " as lock_parent_id,
				NOW() as doc_date,
				NULL as id,
				'" . $param['data']['doc_no'] . "' as doc_no,
				'" . $tbName . "' as tbName,
				" . $factor . " as factor,
				" . $doc_priority . " as doc_priority,
				" . $vn->vat_type . " as vat_type,
				" . $vn->qty_rate . " as qty_rate,
				'" . $vn->um_label . "' as um_label
		";
	}

	if (!empty($sqlUnion)) {

		$sql = "
			INSERT INTO erp_stock_dt ( parent_id, product_id, zone_id, qty_um, qty, price, discount, um_id, lock_dt_id, lock_parent_id, doc_date, id, doc_no, tbName, factor, doc_priority, vat_type, qty_rate, um_label )

			SELECT
				new_tb.*
			FROM (
			" . implode(' UNION ', $sqlUnion) . "
			) as new_tb
		";

		$dao->execDatas($sql);
	}
}

//
//
function insertProductionDt($param)
{

	$dao = getDb();

	$sql = "
		REPLACE INTO erp_production_dt (
			stock_amt,
			stock_bt,
			stock_hp,
			stock_rpc,
			stock_etc,
			stock_bl,
			stock_total,
			stock_atdate,
			remark,
			produce_atdate,
			product_id,
			parent_id
		)
		SELECT
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			'',
			0,
			pro.id as product_id,
			pdc.id as parent_id
		FROM erp_production_product pro
		LEFT JOIN erp_production pdc ON 1 = 1
		WHERE pro.id NOT IN (
			SELECT
				product_id
			FROM erp_production_dt
			WHERE parent_id = pdc.id
		)
	";


	$dao->execDatas($sql);
}

//
//
function setStandardCostAverageConfigActive($param)
{

	$dao = getDb();


	updateStandardCostAverageConfig($param);
}

//
//
function updateStandardCostAverageConfig($param)
{

	$config_id = $param['parent_id'];

	$dao = getDb();

	$sql = "
		UPDATE erp_product_standard_cost_average_config c
		LEFT JOIN
		(
			SELECT
				SUM( production_capacity ) as production_capacity_sum,
				SUM( inderect_labor_of_month ) as inderect_labor_of_month_sum,
				SUM( depreciation_of_month ) as depreciation_of_month_sum,
				SUM( repair_fee_of_month ) as repair_fee_of_month_sum,
				SUM( electricity_of_month ) as electricity_of_month_sum,
				SUM( production_cost_of_month ) as production_cost_of_month_sum
			FROM erp_product_standard_cost_average
		) as new_tb	ON 1 = 1
		SET
			c.production_capacity_sum = new_tb.production_capacity_sum,
			c.indirect_labor_of_month_sum = new_tb.inderect_labor_of_month_sum,
			c.depreciation_of_month_sum = new_tb.depreciation_of_month_sum,
			c.repair_fee_of_month_sum = new_tb.repair_fee_of_month_sum,
			c.electricity_of_month_sum = new_tb.electricity_of_month_sum,
			c.production_cost_of_month_sum = new_tb.production_cost_of_month_sum
		WHERE c.id = " . $config_id . "

	";

	$dao->execDatas($sql);


	$sql = "
		UPDATE erp_product_standard_cost_average av
		SET
			av.production_capacity_refer_oven = (

				SELECT
					oven_capacity / production_capacity_sum * av.production_capacity
				FROM erp_product_standard_cost_average_config
				WHERE id = " . $config_id . "
			),
			av.production_capacity_weight = ( av.weight_of_mean * (

				SELECT
					oven_capacity / production_capacity_sum * av.production_capacity
				FROM erp_product_standard_cost_average_config
				WHERE id = " . $config_id . "
			) )
	";

	$dao->execDatas($sql);

	$sql = "
		UPDATE erp_product_standard_cost_average_config
		SET
			production_capacity_weight_sum = (
				SELECT
					SUM( av.production_capacity_weight )
				FROM  erp_product_standard_cost_average av
			)
		WHERE id = " . $config_id . "
	";

	$dao->execDatas($sql);

	$sql = "
		SELECT
			*
		FROM erp_product_standard_cost_average_config
		WHERE id = " . $config_id . "
	";

	$config = $dao->fetch($sql);

	$sql = "
		UPDATE erp_product_standard_cost_average av
		LEFT JOIN (

			SELECT
				*
			FROM erp_product_standard_cost_average_config
			WHERE id = " . $config_id . "
		) as cf ON 1 = 1
		SET
			av.inderect_labor_per_sqm = (
				(
					(
						( cf.indirect_labor_of_month_amt - cf.indirect_labor_of_month_sum )
						/
						cf.production_capacity_weight_sum
						*
						av.production_capacity_weight
					)
					+
					av.inderect_labor_of_month
				) / av.production_capacity_refer_oven
			),

			av.depreciation_per_sqm = ( ( cf.depreciation_of_month_amt - cf.depreciation_of_month_sum ) / cf.production_capacity_weight_sum * av.production_capacity_weight + av.depreciation_of_month ) / av.production_capacity_refer_oven,

			av.repair_fee_per_sqm = ( ( cf.repair_fee_of_month_amt - cf.repair_fee_of_month_sum ) / cf.production_capacity_weight_sum * av.production_capacity_weight + av.repair_fee_of_month ) / av.production_capacity_refer_oven,

			av.electricity_per_sqm = ( ( cf.electricity_of_month_amt - cf.electricity_of_month_sum )/ cf.production_capacity_weight_sum * av.production_capacity_weight + av.electricity_of_month ) / av.production_capacity_refer_oven,

			av.production_cost_per_sqm = ( ( cf.production_cost_of_month_amt - cf.production_cost_of_month_sum )/ cf.production_capacity_weight_sum * av.production_capacity_weight + av.production_cost_of_month ) / av.production_capacity_refer_oven
	";

	//arr( $sql );
	$dao->execDatas($sql);

	//arr( $config );

	$sql = "
		UPDATE erp_product_standard_cost_direct_labor
		SET
			baht_per_sqm = workers * " . $config->labor_rate . " / capacity_sqm
	";

	$dao->execDatas($sql);


	$sql = "
		UPDATE erp_product_standard_cost_gas
		SET
			baht_per_sqm = gas_unit_per_hour * " . $config->gas_rate . " / sqm_per_hour * " . $config->gas_price . "
	";

	$dao->execDatas($sql);


	$sql = "
		REPLACE INTO erp_product_standard_cost_average_static (
			production_capacity_refer_oven,
			production_capacity_weight,
			inderect_labor_per_sqm,
			depreciation_per_sqm,
			repair_fee_per_sqm,
			electricity_per_sqm,
			production_cost_per_sqm, admin_company_id, config_id, id  )

		SELECT
			production_capacity_refer_oven, production_capacity_weight, inderect_labor_per_sqm, depreciation_per_sqm, repair_fee_per_sqm, electricity_per_sqm, production_cost_per_sqm, admin_company_id, " . $config_id . " as config_id, id
		FROM
			erp_product_standard_cost_average;

		REPLACE INTO erp_product_standard_cost_direct_labor_static ( baht_per_sqm, admin_company_id, config_id, id )

		SELECT

			baht_per_sqm, admin_company_id, " . $config_id . " as config_id, id
		FROM
			erp_product_standard_cost_direct_labor;

		REPLACE INTO erp_product_standard_cost_gas_static ( baht_per_sqm, admin_company_id, config_id, id  )
		SELECT
			baht_per_sqm, admin_company_id, " . $config_id . " as config_id, id
		FROM
			erp_product_standard_cost_gas;

	";

	$dao->execDatas($sql);

	$sql = "
		SELECT
			parent_id
		FROM erp_product_standard_cost_dt
		WHERE type = 2
		GROUP BY parent_id
	";

	//
	//
	foreach ($dao->fetchAll($sql) as $ka => $va) {

		$param['parent_id'] = $va->parent_id;

		updateErpProductStandardCost($param);
	}
}

//
//
function updateErpChequeOut($data, $main_data_before, $type = 'out')
{

	//arr( $data );
	$dao = getDb();

	$sql = "
		UPDATE erp_cheque_" . $type . "
			SET
				pay_doc_no = '" . $main_data_before->doc_no . "',
				act_date = '" . $main_data_before->doc_date . "'

		WHERE id = " . $data['cheque_id'] . "
	";

	$dao->execDatas($sql);
}



//
//
function updateHeaderTotal($param)
{

	$dao = getDb();

	$sql = "
		UPDATE
			" . $param['headerTable'] . " a
		SET
			a.total_before_vat = IFNULL( (
				SELECT
					SUM( before_vat )
				FROM " . $param['tb_dt_source'] . "
				WHERE parent_id = a.id
			), 0 ),
			a.vat_bath = ROUND( a.vat_rate / 100 * a.total_before_vat, 2 ),
			a.total_after_vat = a.total_before_vat + a.vat_bath
		WHERE a.id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);
}


function updateStockHeader($param)
{

	$dao = getDb();

	$sql = "
		UPDATE
			erp_stock a
		SET
			a.total_before_vat = IFNULL( (
				SELECT
					SUM( before_vat )
				FROM erp_stock_dt
				WHERE parent_id = a.id
			), 0 ),
			a.vat_bath = ROUND( a.vat_rate / 100 * a.total_before_vat, 2 ),
			a.total_after_vat = a.total_before_vat + a.vat_bath
		WHERE a.id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);
}

//
//
function insertStockMaster($param)
{

	$dao = getDb();


	if (isset($param['end_date'])) {

		$close_date = $param['end_date'];
	} else {
		/*
		$sql = "
			SELECT
				LAST_DAY( '". $param['data']['doc_date'] ."' ) as end_date

		";

		$close_date = $dao->fetch( $sql )->end_date;
		*/

		$close_date = $param['data']['doc_date'];
	}

	$tables['erp_purchase_inv_dt'] = 1;
	$tables['erp_purchase_return_dt'] = -1;
	$tables['erp_sale_inv_dt'] = -1;
	$tables['erp_sale_return_dt'] = 1;


	$sqlUnion = array();
	foreach ($tables as $kt => $vt) {
		$sqlUnion[] = "
			(
				SELECT
					'" . $kt . "' as tbName,
					dt.product_id,
					dt.color_id,
					SUM( dt.qty * " . $vt . " ) as qty_bal,
					SUM( dt.before_vat * " . $vt . " ) as amt_bal
				FROM " . $kt . " dt
				[WHERE]
				GROUP BY
					product_id,
					color_id
			)
		";
	}

	$sql = "
		REPLACE INTO erp_master_stock ( close_date, product_id, color_id, qty_bal, amt_bal )
		SELECT
			'" . $close_date . "' as close_date,
			dt.product_id,
			dt.color_id,
			SUM( dt.qty_bal ) as qty_bal,
			SUM( dt.amt_bal ) as amt_bal

		FROM (
		" . implode(' UNION ', $sqlUnion) . "
		) as dt

		GROUP BY
			product_id,
			color_id
	";

	$replace = array();

	$replace['WHERE'][] = "dt.doc_date <= '" . $close_date . "'";

	$dao->execDatas(genCond_($sql, $replace));
}

function insertCostMaster($param)
{

	$dao = getDb();


	if (isset($param['end_date'])) {

		$close_date = $param['end_date'];
	} else {
		/*
		$sql = "
			SELECT
				LAST_DAY( '". $param['data']['doc_date'] ."' ) as end_date

		";

		$close_date = $dao->fetch( $sql )->end_date;
		*/
		$close_date = $param['data']['doc_date'];
	}

	$sqlStock = "
		SELECT
			product_id
		FROM erp_stock_dt
		[WHERE]
		GROUP BY product_id
	";

	$replace = array();

	$replace['WHERE'][] = "doc_date <= '" . $close_date . "'";

	$sqlUnion = array();
	foreach ($dao->fetchAll(genCond_($sqlStock, $replace)) as $kp => $vp) {

		$sql = "
			SELECT
				( dt.qty * dt.factor ) as qty,
				dt.qty_bal,
				dt.amt_bal,
				dt.product_id,
				( dt.before_vat * dt.factor ) as cost_amt,
				dt.tbName,
				dt.doc_date,
				dt.id,
				order_number,
				dt.cost_bal
			FROM erp_stock_dt dt
			[WHERE]
			[SORT]
		";

		$replace = array();

		$replace['WHERE'][] = "dt.doc_date <= '" . $close_date . "' AND dt.product_id = " . $vp->product_id . "";

		$replace['SORT'] = "
			ORDER BY
				doc_date DESC,
				doc_priority DESC,
				id DESC
			LIMIT 0, 1
		";

		foreach ($dao->fetchAll(genCond_($sql, $replace)) as $km => $vm) {

			$sqlUnion[] = "
				SELECT
					" . $vm->product_id . " as product_id,
					" . $vm->amt_bal . " as amt_bal,
					" . $vm->qty_bal . " as qty_bal,
					" . $vm->cost_bal . " as cost_bal,
					'" . $vm->doc_date . "' as doc_date,
					'" . $vm->order_number . "' as order_number,
					'" . $vm->id . "' as id,
					'" . $vm->tbName . "' as tbName,
					'" . $close_date . "' as close_date
			";
		}
	}

	$sql = "
		REPLACE INTO erp_master_cost ( product_id, amt_bal, qty_bal, cost_bal, doc_date, order_number, id, tbName, close_date )
		SELECT
			new_tb.*
		FROM (
		" . implode(' UNION ', $sqlUnion) . "
		) as new_tb;
	";

	//arr( $sql );
	$dao->execDatas($sql);
}

//
//
function setErpSaleOrder($param)
{


	$dao = getDb();

	if (empty($param['main_data_before']) || ($param['main_data_before']->company_id != $param['data']['company_id'])) {

		$sql = "
			UPDATE erp_sale_order a
			LEFT JOIN erp_company b ON a.company_id = b.company_id
			LEFT JOIN erp_book_company_sale_order c ON a.book_id = c.book_id AND a.company_id = c.company_id
			SET
				a.company_branch_no = b.company_branch_no,
				a.due = c.due,
				a.pc_id = c.pc_id,
				a.company_group_price_id = b.company_group_price_id,
				a.saleman_id = c.saleman_id
			WHERE  a.id = " . $param['main_id'] . "
		";

		$dao->execDatas($sql);
	}
}


/*
	updateErpArPay( $_REQUEST['main_id'], $type = 'ap' );

	inportGlTrnDt_( 'erp_ap_pay', $_REQUEST['main_id'], $this->getView->new_config_id );

	if ( in_array( $this->config->config_id, array( 134 ) ) )
		updateErpChequeOut( $data['to_db'][$k_tb_name], $this->main_data_before );
*/



//
//
function updateDashboardImgsOrders_($param)
{
	//echo 'dfasdfsd';
	$dao = getDb();


	$sql = "
		TRUNCATE TABLE aa_check_imgs_dt
	";


	$dao->execDatas($sql);


	$source_file = 'file_upload/' . $param['data']['file'];

	$im = imagecreatefromjpeg($source_file);

	$imgw = imagesx($im);

	$imgh = imagesy($im);

	$totalArea = $imgw * $imgh;

	$sql = "
		UPDATE aa_check_imgs
		SET
			sq_pixel = '" . $totalArea . "'
	";

	$dao->execDatas($sql);

	///$histo = array();
	$id = 1;
	for ($i = 0; $i < $imgw; $i++) {

		for ($j = 0; $j < $imgh; $j++) {

			$rgb = imagecolorat($im, $i, $j);

			$r = ($rgb >> 16) & 0xFF;

			$g = ($rgb >> 8) & 0xFF;

			$b = $rgb & 0xFF;

			$rgb = $r . ', ' . $g . ', ' . $b;

			$rgb_number = $r + $g + $b;

			$ex = explode(',', $rgb);

			$hex = sprintf("#%02x%02x%02x", $ex[0], $ex[1], $ex[2]);

			$sqlUnion[] = "
				SELECT
					'" . $id . "' as id,
					'" . $rgb . "' as rgb,
					" . ($rgb_number) . " as rgb_number,
					" . $param['parent_id'] . " as parent_id,
					'" . $hex . "' as hex
			";

			++$id;

			if (count($sqlUnion) > 100000) {

				$sql = "
					INSERT INTO aa_check_imgs_dt (
						rgb,
						rgb_number,
						hex,
						pixel
					)
					SELECT
						new_tb.rgb,
						new_tb.rgb_number,
						new_tb.hex,
						count( * ) as pixel
					FROM (
					" . implode(' UNION ', $sqlUnion) . "
					) as new_tb
					GROUP BY
						new_tb.rgb,
						new_tb.hex,
						new_tb.rgb_number;
				";

				$dao->execDatas($sql);

				$sqlUnion = array();
			}
		}
	}


	if (!empty($sqlUnion)) {

		$sql = "
			INSERT INTO aa_check_imgs_dt (
				rgb,
				rgb_number,
				hex,
				pixel
			)
			SELECT
				new_tb.rgb,
				new_tb.rgb_number,
				new_tb.hex,
				count( * ) as pixel
			FROM (
			" . implode(' UNION ', $sqlUnion) . "
			) as new_tb
			GROUP BY
				new_tb.rgb,
				new_tb.hex,
				new_tb.rgb_number;
		";

		$dao->execDatas($sql);

		$sqlUnion = array();
	}
}

//
//
function updateDashboardImgsOrders($param)
{

	$dao = getDb();

	$sql = "
		TRUNCATE TABLE aa_dashboard_imgs_dt
	";

	$dao->execDatas($sql);

	$img = new MakeImage;

	$param['data']['CLUSTERS'] = 2;


	$originFile = 'file_upload/' . $param['data']['file'];

	//$originFile = 'testImg/10451319659012.jpg';

	$img->load($originFile);


	$cutImgWh = 1000;

	$totalPixel = $img->image_info[0] * $img->image_info[1];

	$columnsNumber = floor($img->image_info[0] / $cutImgWh);

	$rowsNumber = floor($img->image_info[1] / $cutImgWh);

	$diffH = $img->image_info[1];

	$id = 1;
	//$file_pixel = 0;
	for ($r = 0; $r <= $rowsNumber; ++$r) {

		$top = $cutImgWh * $r;

		if ($top >= $img->image_info[1]) {

			break;
		}

		$diffH -= $cutImgWh;

		if ($diffH < 0) {
			$new_height = $img->image_info[1] - $top;
		} else {

			$new_height = $cutImgWh;
		}

		$diffW = $img->image_info[0];

		for ($c = 0; $c <= $columnsNumber; ++$c) {

			$left = $cutImgWh * $c;

			if ($left >= $img->image_info[0]) {

				break;
			}

			$diffW -= $cutImgWh;

			if ($diffW < 0) {
				$new_width = $img->image_info[0] - $left;
			} else {

				$new_width = $cutImgWh;
			}

			$img_name = 'testImg/' . $left . '-' . $top . '.jpg';

			$img->cut_img($originFile, $img_name, $new_width, $new_height, $cut = true, $center_cut = false, $left, $top, $resize = false);

			$json = file_get_contents('http://mkweb.bcgsc.ca/color-summarizer/?url=' . OnlineUrl . '/' . ProjectFolder . '' . $img_name . '&precision=vhigh&json=1&num_clusters=2');

			$json_decode = json_decode($json);

			$file_pixel = $new_width * $new_height;

			foreach ($json_decode->clusters  as $kc => $vc) {

				$pixelColors[$vc->hex[0]][] = $vc->f * $file_pixel;
			}
		}
	}

	$data = array();
	foreach ($pixelColors as $kj => $vj) {

		$data['trs'][$kj]['percentage'] = array_sum($vj) / $totalPixel * 100;

		$data['trs'][$kj]['sqCm'] = $param['data']['sqcm'] * $data['trs'][$kj]['percentage'] / 100;

		$data['trs'][$kj]['grm'] = $data['trs'][$kj]['sqCm'] * 0.0010589;
	}

	$data['colorsNumber'] = count($pixelColors);

	$text_json = json_encode($data);

	$sql = "
		UPDATE aa_dashboard_imgs
		SET
			text_json = '" . $text_json . "'
		WHERE id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);
}

//
//
function importSaleAnalysis_dt($param)
{


	$dao = getDb();

	if (empty($_FILES['file_name']['tmp_name'])) {

		return;
	}

	//$file = 'ddfdfdfdffd.csv';

	$table = 'aa_sale_analysis_dt';
	$sql = "
		DELETE FROM " . $table . "
		WHERE parent_id = " . $param['parent_id'] . ";
	";

	$dao->execDatas($sql);

	$file = $_FILES['file_name']['tmp_name'];

	$objCSV = fopen($file, "r");

	$i = 0;
	$columns['brand'] = "'[val]' as brand";
	$columns['sku'] = "'[val]' as sku";
	$columns['size'] = "'[val]' as size";
	$columns['name'] = "'[val]' as name";
	$columns['price'] = "REPLACE( '[val]', ',', '' ) as price";
	$columns['qty'] = "REPLACE( '[val]', ',', '' ) as qty";
	$columns['amt'] = "REPLACE( '[val]', ',', '' ) as amt";
	$columns['parent_id'] = $param['parent_id'] . " as parent_id";

	while (($objArr = fgetcsv($objCSV, 1000, ",")) !== FALSE) {

		++$i;

		if ($i == 1) {
			continue;
		}

		$keep = array();
		foreach ($columns as $kc => $vc) {

			$val = isset($objArr[count($keep)]) ? $objArr[count($keep)] : NULL;

			$keep[] = str_replace('[val]', $val, $vc);
		}

		$sqlUnion[] = "
			SELECT
				" . implode(',', $keep) . "
		";

		if (count($sqlUnion) > 500) {

			$sql = "
				INSERT INTO " . $table . " ( " . implode(',', array_keys($columns)) . " )
				SELECT
					new_tb.*
				FROM(
					" . implode(' UNION ', $sqlUnion) . "
				) as new_tb
			";

			$dao->execDatas($sql);

			$sqlUnion = array();
		}
	}

	if (!empty($sqlUnion)) {

		$sql = "
			INSERT INTO " . $table . " ( " . implode(',', array_keys($columns)) . " )
			SELECT
				new_tb.*
			FROM(
				" . implode(' UNION ', $sqlUnion) . "

			) as new_tb
		";

		$dao->execDatas($sql);

		$sqlUnion = array();
	}

	$sql = "
		REPLACE INTO aa_sale_analysis ( sku, size, name, time_update, brand )
		SELECT
			sku,
			size,
			name,
			NOW() as time_update,
			brand
		FROM aa_sale_analysis_dt
		WHERE sku NOT IN(
			SELECT
				sku
			FROM aa_sale_analysis
		)
		GROUP BY
			sku
	";

	$dao->execDatas($sql);
}


//
//
function updateStockDt($param)
{

	$dao = getDb();

	$sql = "
		UPDATE stock_dt dt
		SET factor = ( SELECT factor FROM tb_act_stk WHERE act_id = " . $param['data']['act_id'] . " )
		WHERE hd_id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);

	$sql = "

		UPDATE tb_product p
		SET
			p.qty = (

			SELECT

				SUM( dt.qty * dt.factor ) AS qty

			FROM stock_dt dt
			WHERE dt.product_id = p.product_id
		)

	";

	//arr( $sql );
	$dao->execDatas($sql);
}


//
//
function copyUserPrivileges($param)
{

	$dao = getDb();

	$from_user_id = $param['parent_id'];

	$to_user_id = $param['data']['user_id'];

	$sql = "
		REPLACE INTO admin_user_page ( allow_lock_tab, views_book_id, user_id, page_id, permission, user_update, add_row, edit, delete_row, inspect, prove, views_department_id, views_special )
		SELECT
			allow_lock_tab,
			views_book_id,
			" . $to_user_id . " as user_id,
			page_id,
			permission,
			user_update,
			add_row,
			edit,
			delete_row,
			inspect,
			prove,
			views_department_id,
			views_special
		FROM admin_user_page
		WHERE user_id = " . $from_user_id . "
		AND page_id NOT IN (
			SELECT
				page_id
			FROM admin_user_page
			WHERE user_id = " . $to_user_id . "
		)
	";

	//arr( $sql );

	$dao->execDatas($sql);

	$sql = "
		INSERT INTO admin_user_company ( user_id, company_id )
		SELECT
			" . $to_user_id . " as user_id,
			company_id
		FROM admin_user_company
		WHERE user_id = " . $from_user_id . "
		AND company_id NOT IN (
			SELECT
				company_id
			FROM admin_user_company
			WHERE user_id = " . $to_user_id . "

		)
	";
	$dao->execDatas($sql);

	$sql = "
		INSERT INTO admin_user_group ( user_id, group_id )
		SELECT
			" . $to_user_id . " as user_id,
			group_id
		FROM admin_user_group
		WHERE user_id = " . $from_user_id . "
		AND group_id NOT IN (
			SELECT
				group_id
			FROM admin_user_group
			WHERE user_id = " . $to_user_id . "
		)
	";
	$dao->execDatas($sql);
}

//
//
function updateAdminUserPloblem($param)
{


	//
	//
	$dao = getDb();


	if (!empty($param['main_data_before'])) {

		$new = $param['data']['order_number'];

		$old = $param['main_data_before']->order_number;

		if ($new > $old) {

			$sql = "
				SELECT
					*
				FROM admin_user_ploblem
				WHERE user_id = " . $_SESSION[Uid] . "
				ORDER BY
					status DESC,
					order_number ASC,
					time_update ASC
			";
		} else {
			$sql = "
				SELECT
					*
				FROM admin_user_ploblem
				WHERE user_id = " . $_SESSION[Uid] . "
				ORDER BY
					status DESC,
					order_number ASC,
					time_update DESC
			";
		}

		$i = 0;
		foreach ($dao->fetchAll($sql) as $ka => $va) {

			++$i;

			$sql = "

				UPDATE admin_user_ploblem
				SET
					order_number = " . $i . "
				WHERE id = " . $va->id . "

			";
			$dao->execDatas($sql);
		}
	}

	$sql = "

		SELECT
			new_tb.*
		FROM (
			SELECT
				MAX( order_number ) as t
			FROM admin_user_ploblem
			WHERE user_id = " . $_SESSION[Uid] . "
		) as new_tb
	";


	$res = $dao->fetch($sql);

	$sql = "DELETE FROM admin_user_ploblem_dt";
	$dao->execDatas($sql);

	$sql = "
		SELECT
			pl.*,
			( ( pl.work_hr * 60 ) + ( pl.work_min ) ) as work_time,
			(
				SELECT
					GROUP_CONCAT( id )
				FROM admin_user_ploblem
				WHERE splite_ploblem_id = pl.id
			) as insert_ids
		FROM admin_user_ploblem pl
		HAVING insert_ids IS NOT NULL
	";

	$order_number_sub = 0;
	$skip = array();
	$sqlUnion = array();
	foreach ($dao->fetchAll($sql) as $kres => $res) {

		$order_number = $res->order_number;

		$sql = "
			SELECT
				pl.*,
				( ( pl.splite_at_hr * 60 ) + ( pl.splite_at_min ) ) as work_time,
				( ( pl.work_hr * 60 ) + ( pl.work_min ) ) as work_time_minute

			FROM admin_user_ploblem pl
			WHERE id IN ( " . $res->insert_ids . " )
			ORDER BY time_update ASC
		";


		$work_min = 0;

		foreach ($dao->fetchAll($sql) as $ka => $va) {

			$name = $res->name;

			$work_min = $va->work_time - $work_min;

			$id = getSkipId('admin_user_ploblem_dt', 'id', $skip);

			$skip[] = $id;

			$sqlUnion[] = "
				SELECT
					" . $id . " as id,
					" . ++$order_number_sub . " as order_number_sub,
					" . $va->id . " as splite_ploblem_id,
					" . $va->splite_ploblem_id . " as ploblem_id,
					" . $res->job_owner . " as job_owner,
					'" . $res->why_cant_do . "' as why_cant_do,
					'" . $res->project_name . "' as project_name,
					'" . $name  . "' as name,
					" . $work_min . " as work_min,
					" . $order_number . " as order_number,
					'" . $res->doc_date . "' as doc_date,
					" . $res->user_id . " as user_id,
					'" . $res->detail . "' as detail,
					" . $res->active . " as active,
					'" . $res->link . "' as link,
					'" . $res->description . "' as description,
					NOW() as time_insert
			";

			$id = getSkipId('admin_user_ploblem_dt', 'id', $skip);

			$skip[] = $id;

			$sqlUnion[] = "

				SELECT
					" . $id . " as id,
					" . ++$order_number_sub . " as order_number_sub,
					" . $va->id . " as splite_ploblem_id,
					" . $va->id . " as ploblem_id,
					" . $va->job_owner . " as job_owner,
					'" . $va->why_cant_do . "' as why_cant_do,
					'" . $va->project_name . "' as project_name,
					'" . $va->name  . "' as name,
					" . $va->work_time_minute . " as work_min,
					" . $order_number . " as order_number,
					'" . $va->doc_date . "' as doc_date,
					" . $va->user_id . " as user_id,
					'" . $va->detail . "' as detail,
					" . $va->active . " as active,
					'" . $va->link . "' as link,
					'" . $va->description . "' as description,
					NOW() as time_insert
			";
		}

		$work_min = $res->work_time - $va->work_time;

		$id = getSkipId('admin_user_ploblem_dt', 'id', $skip);

		$skip[] = $id;

		$sqlUnion[] = "

			SELECT
				" . $id . " as id,
				" . ++$order_number_sub . " as order_number_sub,
				" . $va->id . " as splite_ploblem_id,
				" . $va->splite_ploblem_id . " as ploblem_id,
				" . $res->job_owner . " as job_owner,
				'" . $res->why_cant_do . "' as why_cant_do,
				'" . $res->project_name . "' as project_name,
				'" . $res->name  . "' as name,
				" . $work_min . " as work_min,
				" . $order_number . " as order_number,
				'" . $res->doc_date . "' as doc_date,
				" . $res->user_id . " as user_id,
				'" . $res->detail . "' as detail,
				" . $res->active . " as active,
				'" . $res->link . "' as link,
				'" . $res->description . "' as description,
				NOW() as time_insert
		";
	}


	$sql = "
		INSERT INTO admin_user_ploblem_dt

		( id, order_number_sub, splite_ploblem_id, ploblem_id, job_owner, why_cant_do, project_name, name, work_min, order_number, doc_date, user_id, detail, active, link, description, time_insert )
		SELECT
			new_tb.*
		FROM (
			" . implode(' UNION ', $sqlUnion) . "
		) as new_tb

	";

	$dao->execDatas($sql);


	getUserJobPlan__________($param);
}


//
//
function getUserJobPlan__________($param)
{

	$dao = getDb();

	$filters = array();
	/*if( !empty( $_REQUEST['user_id'][0] ) ) {

		$filters[] = encodePrikey( 'user_id', true ) ." = '". $_REQUEST['user_id'][0] ."'";
	}
	else if( !empty( $param['data']['rows'][0]->user_id ) ) {

		$filters[] = "user_id = ". $param['data']['rows'][0]->user_id ."";
	}
	else {
		$filters[] = "user_id = ". $_SESSION[Uid] ."";
	}*/

	$filters[] = "user_id IN ( 65, 17 )";
	$sql = "
		SELECT
			*
		FROM admin_user
		[cond]
	";

	$sql = genCond($sql, $filters);

	//$users = $dao->fetchAll( $sql );

	foreach ($dao->fetchAll($sql) as $ku => $user) {

		$sqlUnion = array();

		//
		//
		$sql = "
			SELECT
				concat( dt.id, 'admin_user_ploblem_dt' ) as id,
				dt.name,
				( ( dt.work_hr * 60 ) + ( dt.work_min ) ) as work_time,
				( ( pl.work_hr * 60 ) + ( pl.work_min ) ) as clon_work_hr,
				dt.order_number,
				dt.time_update,
				dt.ploblem_id as link_id,
				dt.order_number_sub,
				dt.why_cant_do,
				dt.description,
				dt.ploblem_id as parent_id,
				dt.status,
				pl.time_insert,
				pl.start_day,
				pl.project_name,
				dt.user_id,
				pl.doc_date
			FROM admin_user_ploblem_dt dt
			LEFT JOIN admin_user_ploblem pl ON dt.ploblem_id = pl.id
			[cond]
		";

		$filters = array();

		$filters[] = "pl.user_id = " . $user->user_id;

		$sqlUnion[] = genCond($sql, $filters);


		//
		//
		$sql = "

			SELECT
				concat( id, 'admin_user_ploblem' ) as id,
				concat( name ) as name,
				( ( pl.work_hr * 60 ) + ( work_min ) ) as work_time,
				( ( pl.work_hr * 60 ) + ( work_min ) ) as clon_work_hr,
				order_number,
				time_update,
				id as link_id,
				order_number_sub,
				why_cant_do,
				description,
				id as parent_id,
				pl.status,
				pl.time_insert,
				pl.start_day,
				pl.project_name,
				pl.user_id,
				pl.doc_date
			FROM admin_user_ploblem pl
			[cond]
		";

		$filters = array();

		$filters[] = "pl.user_id = " . $user->user_id;
		$filters[] = "( pl.splite_ploblem_id IS NULL OR pl.splite_ploblem_id = 0 )";
		$filters[] = "
			(
				SELECT
					COUNT( * )
				FROM admin_user_ploblem_dt
				WHERE ploblem_id = pl.id
			) = 0
		";
		$sqlUnion[] = genCond($sql, $filters);

		//
		//
		$sql = "
			SELECT
				IFNULL( new_tb.t, DATE_FORMAT( NOW(), '%Y-%m-%d' ) ) as t
			FROM (
				SELECT
					DATE_FORMAT( MIN( new_tb.doc_date ), '%Y-%m-%d' ) as t

				FROM (

					" . implode(' UNION ', $sqlUnion) . "
				) as new_tb
			) as new_tb

		";

		$start = $dao->fetch($sql);
		$start = $start->t;

		$specailHolidays = array();

		$specailHolidays[] = '2019-02-19';

		//
		//
		$sql = "
			SELECT
				SUM( work_time ) as t
			FROM (
				" . implode(' UNION ', $sqlUnion) . "
			) as new_tb
		";

		$totalTime = $dao->fetch($sql)->t;

		$workMinutePerDay = 8 * 60;

		$days = ceil($totalTime / $workMinutePerDay);

		$sql = "

			SELECT
				new_tb.*,
				IF( new_tb.status = 1, 'เสร็จ', 'รอ' ) as job_status
			FROM (

				" . implode(' UNION ', $sqlUnion) . "
			) as new_tb
			ORDER BY
				new_tb.status DESC,
				new_tb.order_number ASC,
				new_tb.order_number_sub ASC

		";

		$ploblems = $dao->fetchAll($sql);


		$rows = array();



		$ip = 0;
		for ($d = 0; $d < $days; ++$d) {

			$break = false;

			$sql = "
				SELECT
					new_tb.t,
					DATE_FORMAT( new_tb.t, '%a' ) as name
				FROM (
					SELECT
						DATE_FORMAT( ADDDATE( '" . $start . "', INTERVAL " . ($d * 1440) . " minute ), '%Y-%m-%d' ) as t
				) as new_tb
			";

			$day = $dao->fetch($sql);

			if (in_array($day->name, array('Sat', 'Sun')) || in_array($day->t, $specailHolidays)) {

				++$days;
			} else {

				$totalLine = 0;
				for ($n = 0; $n < count($ploblems); ++$n) {

					if (!isset($ploblems[$ip]))
						break;

					$ploblem = $ploblems[$ip];
					//arr( $ploblem );

					$totalLine += $ploblem->work_time;

					if ($totalLine > $workMinutePerDay) {

						$w = $ploblem->work_time - ($totalLine - $workMinutePerDay);

						$ploblems[$ip]->work_time -= $w;

						$break = true;
					} else {

						$w = $ploblem->work_time;

						++$ip;
					}

					//$wPercent = ( $w / 8 * 100 );

					if ($w != 0) {

						if (!isset($starts[$ploblem->parent_id])) {

							$starts[$ploblem->parent_id] = $day->t;
						}

						$ends[$ploblem->parent_id] = $day->t;
					}

					if ($break == true) {
						break;
					}
				}
			}
		}

		$sqlUnion = array();
		foreach ($ploblems as $kp => $vp) {

			if (isset($rows['sqlUnion'][$vp->parent_id]))
				continue;

			$sqlUnion[$vp->parent_id] = "
				SELECT

					'" . $starts[$vp->parent_id] . "' as start_day,
					'" . $ends[$vp->parent_id] . "' as end_day,
					'" . (count($sqlUnion) + 1) . "' as order_number,
					'" . $vp->project_name . " : " . $vp->name . "' as name,
					'" . getTimeMinute($vp->clon_work_hr) . "' as work_time,
					'" . $vp->job_status . "' as status,
					" . $vp->user_id . " as user_id
			";
		}

		$sql = "
			DELETE FROM admin_user_ploblem_report WHERE user_id = " . $user->user_id . ";
			INSERT INTO admin_user_ploblem_report ( start_day, end_day, order_number, name, work_time, status, user_id ) SELECT
				new_tb.*
			FROM ( " . implode(' UNION ', $sqlUnion) . " ) as new_tb;
		";

		$dao->execDatas($sql);
	}
}

//
//
function updatePurchaseOrderDt($param)
{
	//echo 'dsffsadfs';
	$main_id = $param['parent_id'];

	$fGroup = isset($param['fGroup']) ? $param['fGroup'] : 'purchase_order_id';

	$sub_tb = isset($param['sub_tb']) ? $param['sub_tb'] : 'sac_purchase_order_dt';

	$main_tb = isset($param['main_tb']) ? $param['main_tb'] : 'sac_purchase_order';

	$dao = getDb();

	$sql = "
		UPDATE " . $sub_tb . "
		SET
			purchase_order_dt_total = ( ROUND( purchase_order_dt_total, 2 ) - discout_baht ),
			purchase_order_dt_total_show =
				ROUND(
					IF( purchase_order_dt_vat_type = 2,
						purchase_order_dt_total,
						purchase_order_dt_total * 100 / ( 100 + purchase_order_vat_rate )
					)
				, 2 ),


			purchase_order_dt_total_after_vat =
				ROUND(
					IF( purchase_order_dt_vat_type = 2,
						purchase_order_dt_total * ( 100 + purchase_order_vat_rate ) / 100,
						purchase_order_dt_total
					)
				, 2 ),
			purchase_order_dt_vat_bath =
				ROUND( purchase_order_dt_total_after_vat - purchase_order_dt_total_show, 2 )

		WHERE " . $fGroup . " = " . $main_id . "";

	$dao->execDatas($sql);

	$sql = "
		UPDATE " . $main_tb . " a
		LEFT JOIN (
			SELECT
				" . $fGroup . ",
				SUM( purchase_order_dt_total_show ) as sum_purchase_order_dt_total_show,
				SUM( purchase_order_dt_total_after_vat ) as sum_purchase_order_dt_total_after_vat,
				SUM( purchase_order_dt_vat_bath ) as sum_purchase_order_dt_vat_bath
			FROM  " . $sub_tb . "
			GROUP BY " . $fGroup . "

		) d ON a." . $fGroup . " = d." . $fGroup . "

		SET
			a.purchase_order_total_before_vat = d.sum_purchase_order_dt_total_show,
			a.purchase_order_total_after_vat =  d.sum_purchase_order_dt_total_after_vat,
			a.purchase_order_vat_bath = d.sum_purchase_order_dt_vat_bath
		WHERE a." . $fGroup . " = " . $main_id;

	$dao->execDatas($sql);
}

//
//
function setUserProfilePage($param)
{

	//arr( $param['parent_id'] );

	//exit;

	$dao = getDb();

	$sql = "
		SELECT
			*
		FROM admin_department_menu
		WHERE department_id = (
			SELECT
				department_id
			FROM admin_user
			WHERE user_id = " . $param['parent_id'] . "
		)
	";

	$keep = array();
	$menu_ids = array();
	foreach ($dao->fetchAll($sql) as $ka => $va) {
		$keep[] = $va->menu_id;
		$menu_ids[] = $va->menu_id;
	}


	//
	//
	while (!empty($menu_ids)) {

		$sql = "

			SELECT *
			FROM admin_menu
			WHERE menu_parent IN ( '" . implode("','", $menu_ids) . "' )

		";

		$menu_ids = array();
		foreach ($dao->fetchAll($sql) as $kb => $vb) {
			$keep[] = $vb->menu_id;
			$menu_ids[] = $vb->menu_id;
		}
	}


	$sql = "
		SELECT *
		FROM admin_model
		WHERE menu_id IN ( '" . implode("','", $keep) . "' )";

	foreach ($dao->fetchAll($sql) as $ka => $va) {

		$config[] =

			array(
				'page_id' => $va->model_id,
				'permission' => 1,
				'edit' => 1,
				'add_row' => 1,
				'delete_row' => 1,
				'inspect' => NULL,
				'prove' => NULL,
				'views_department_id' => '%department_id;',

			);
	}


	$sql = "
		SELECT
			*
		FROM admin_user
		WHERE user_id = ?
	";

	$config[] = array(
		'page_id' => 126,
		'permission' => 1,
		'edit' => 1,
		'add_row' => 0,
		'delete_row' => 0,
		'inspect' => NULL,
		'prove' => NULL,
		'views_department_id' => NULL
	);

	$config[] = array(
		'page_id' => 73,
		'permission' => 1,
		'edit' => 1,
		'add_row' => 1,
		'delete_row' => 1,
		'inspect' => '%department_id;',
		'prove' => NULL,
		'views_department_id' => '%department_id;'
	);

	foreach ($dao->fetchAll($sql, array($param['parent_id'])) as $ka => $va) {

		foreach ($config as $kb => $vb) {
			$data = array();
			$data['user_id'] = $va->user_id;
			$data['page_id'] = $vb['page_id'];
			$data['permission'] = $vb['permission'];
			$data['edit'] = $vb['edit'];
			$data['add_row'] = $vb['add_row'];
			$data['delete_row'] = $vb['delete_row'];
			$data['inspect'] = str_replace('%department_id;', '[' . $va->department_id . ']', $vb['inspect']);
			$data['prove'] = str_replace('%department_id;', '[' . $va->department_id . ']', $vb['prove']);
			$data['views_department_id'] = str_replace('%department_id;', '[' . $va->department_id . ']', $vb['views_department_id']);

			$dao->insert('admin_user_page', $data);
		}


		if ($va->send_mail == 1) {

			$mail = $va->user_email;

			$ex = explode('@', $mail);

			$pass = $ex[0] . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

			$subject = 'แจ้งยูสเซอร์กับ รหัสผ่านโปรแกรม sac2015 ';
			$body = '
				ลิ้งค์เข้าใช้งาน: <a href="' . SiteName . '">คลิ๊กที่นี่</a><br><br>
				User: ' . $mail . ' <br><br>

				Pass: ' . $pass . '
			';

			sendMail($mail, $subject, $body);

			$sql = "
				UPDATE admin_user
				SET
					send_mail = 0,
					user_name = '" . $mail . "',
					user_password = MD5('" . $pass . "')
				WHERE user_id = " . $va->user_id;

			$dao->execDatas($sql);
		}
	}
}


//
//
function implodeSaleOrderRequestUnlock($param)
{

	$dao = getDb();

	$fileName = str_replace('.TXT', '', $param['data']['doc_no']) . '.TXT';



	$files[] = '/home/sacuser/link/credit/' . $fileName . '';

	$files[] = '/home/sacuser/link/credit/complete/' . $fileName . '';

	$json = file_get_contents($files[0]);

	$data = json_decode($json);

	$data = convertObJectToArray($data);

	//arr( $_SESSION['company_id'] );
	//$data['admin_company_id'] = $_SESSION['company_id'];

	$dao->dbh->exec('SET NAMES tis620');

	$condition = 'doc_no = \'' . $param['data']['doc_no'] . '\'';

	$dao->update($param['current_config']->tb_main, $data, $condition);

	rename($files[0], $files[1]);
}

//
//
function updateErpSaleOrderInvQty($param)
{

	$dao = getDb();

	$sql = "
		UPDATE
			erp_sale_order a
		LEFT JOIN (
			SELECT

				sale_order_id,
				SUM( qty ) as inv_qty
			FROM erp_sale_inv_dt
			WHERE sale_order_id = " . $param['data']['sale_order_id'] . "
			GROUP BY sale_order_id

		) b ON a.id = b.sale_order_id
		SET
			a.inv_qty = b.inv_qty


		WHERE a.id = " . $param['data']['sale_order_id'] . "
	";

	$dao->execDatas($sql);
}



//
//
function setProductStockCost($param)
{

	$dao = getDb();

	$product_dt_ids = array();

	if (!empty($param['main_data_before']->product_dt_ids))
		$product_dt_ids = explode(',', $param['main_data_before']->product_dt_ids);

	if (!empty($param['data']['product_dt_id'])) {

		$product_dt_ids[] = $param['data']['product_dt_id'];
	}

	$sql = "
		UPDATE erp_product
		SET update_stock_cost = 1
		WHERE product_id IN (
			SELECT
				product_id
			FROM erp_product_dt
			WHERE product_dt_id IN ( '" . implode("','", $product_dt_ids) . "' )
		)
	";

	$dao->execDatas($sql);
}



//
//
function updateErpSaleOrder($param)
{

	/*$headerTable = 'erp_sale_order';

	$tb_dt_source_main_key = 'sale_order_id';

	$tb_dt_source = 'erp_sale_order_dt';*/

	$headerTable = $param['headerTable'];
	$tb_dt_source_main_key = $param['tb_dt_source_main_key'];
	$tb_dt_source = $param['tb_dt_source'];

	$dao = getDb();

	$sql = "
		UPDATE
			" . $headerTable . " a
		LEFT JOIN (
			SELECT
				" . $tb_dt_source_main_key . ",
				ROUND( IFNULL( SUM( before_vat ), 0 ), 2 ) as sum_before_vat,
				ROUND( IFNULL( SUM( qty ), 0 ), 2 ) as order_qty
			FROM " . $tb_dt_source . "
			WHERE " . $tb_dt_source_main_key . " = " . $param['parent_id'] . "

		) b ON a.id = b." . $tb_dt_source_main_key . "
		SET
			a.total_before_vat = b.sum_before_vat,
			a.vat_bath = ROUND( a.vat_rate / 100 * b.sum_before_vat, 2 ),
			a.total_after_vat = ( 1 + ( a.vat_rate / 100 ) ) * b.sum_before_vat,
			a.order_qty = b.order_qty

		WHERE a.id = " . $param['parent_id'] . "
	";


	$dao->execDatas($sql);
}

//
//
function insertErpProductDt($param)
{

	$code_color = $param['data']['product_id'] . '-' . $param['data']['product_color_id'];

	$dao = getDb();

	$sql = "
		REPLACE INTO erp_product_dt (
			product_id,
			product_color_id,
			code_color,
			total_qty,
			product_code,
			product_name,
			product_color_code,
			product_dt_id
		)
		SELECT
			tmp.product_id,
			tmp.product_color_id,
			tmp.code_color,
			SUM( total_qty ) as total_qty,
			tmp.product_code,
			tmp.product_name,
			tmp.product_color_code,
			(
				SELECT
					product_dt_id
				FROM erp_product_dt
				WHERE code_color = tmp.code_color
			) as product_dt_id
		FROM (

			SELECT
				new_ta.product_id,
				new_ta.product_color_id,
				new_ta.code_color,
				new_ta.last_period_date,
				new_ta.period_qty,
				new_ta.stock_qty,
				( new_ta.period_qty + new_ta.stock_qty ) as total_qty,
				new_ta.product_code,
				new_ta.product_name,
				new_ta.product_color_code
			FROM (
				SELECT
					new_tb.product_id,
					new_tb.product_color_id,
					CONCAT( new_tb.product_id, '-', new_tb.product_color_id ) as code_color,
					MAX( new_tb.last_period_date ) as last_period_date,
					IFNULL( (
						SELECT
							stock_master_zone_qty
						FROM erp_stock_master_zone_period
						WHERE code_color_file = new_tb.code_color_file
						AND year_month_ = MAX( new_tb.last_period_date )
						LIMIT 0, 1

					), 0 ) as period_qty,
					IFNULL( (

						SELECT
							SUM( qty * stock_act_action ) as stock_master_zone_qty
						FROM erp_stock_dt
						WHERE code_color_file = new_tb.code_color_file
						AND doc_date > MAX( new_tb.last_period_date ) ), 0
					) as stock_qty,
					new_tb.product_code,
					new_tb.product_name,
					new_tb.product_color_code
				FROM (
					(
						SELECT
							'erp_stock_dt' as from_,
							product_id,
							product_color_id,
							code_color_file,
							'1970-01-01' as last_period_date,
							prdid_ as product_code,
							product_name,
							color_ as product_color_code

						FROM erp_stock_dt
						[cond]
						GROUP BY code_color_file

					)
					UNION
					(
						SELECT
							'erp_stock_master_zone_period' as from_,
							product_id,
							product_color_id,
							code_color_file,
							MAX( year_month_ ) as last_period_date,
							prdid_ as product_code,
							product_name,
							color_ as product_color_code
						FROM erp_stock_master_zone_period
						[cond]
						GROUP BY code_color_file

					)

				) as new_tb
				GROUP BY new_tb.code_color_file
			) as new_ta


		) as tmp

		GROUP BY code_color

	";

	$filters = array();
	$filters[] = "code_color = '" . $code_color . "'";


	$sql = genCond($sql, $filters);
	//arr( $sql );
	$dao->execDatas($sql);

	//$param['parent_id'] = $param['data']['product_id'];

	//updateErpProductDt( $param );


	//setProductStockCost( $param );
}







//
//
function insertOemStockDtFromMove($param)
{


	//arr($param );
	$dao = getDb();

	$zones['zone_in_id'] = array('qty_factor' => 1);
	$zones['zone_out_id'] = array('qty_factor' => -1);

	$keepIds = $sqlUnion = array();
	foreach ($zones as $kz => $vz) {

		$id = getSkipId('oem_stock_dt', 'id', $keepIds);

		$keepIds[] = $id;

		$sqlUnion[] = "

			SELECT
				" . $id . " as id,
				'" . $param['data']['products_dt_id'] . "' as products_dt_id,
				'" . $param['main_data_before']->doc_no . "' as doc_no,
				'" . $param['data']['doc_date'] . "' as doc_date,
				'" . $param['data']['products_color_id'] . "' as products_color_id,
				" . $param['parent_id'] . " as stock_hd_id,
				'" . $param['data']['products_id'] . "' as products_id,
				" . $vz['qty_factor'] . " as qty_factor,
				'" . $param['data']['qty'] . "' as qty,
				'" . $param['data'][$kz] . "' as zone_id,
				" . $param['main_id'] . " as move_dt_id

		";
	}

	$sql = "
		DELETE FROM oem_stock_dt WHERE move_dt_id = " . $param['main_id'] . ";
		INSERT INTO oem_stock_dt (
			id,
			products_dt_id,
			doc_no,
			doc_date,
			products_color_id,
			stock_hd_id,
			products_id,
			qty_factor,
			qty,
			zone_id,
			move_dt_id
		)

		SELECT
			new_tb.*
		FROM (
			" . implode(' UNION ', $sqlUnion) . "
		) as new_tb;

	";

	$filters = array();

	$sql = genCond($sql, $filters);
	//arr( $sql );
	$dao->execDatas($sql);
}



//
//
function uploadExpInv($param)
{

	$dao = getDb();

	if (empty($param['data']['stock_count_hd_csv_file']))
		return false;

	$file = FILE_FOLDER . '/' . $param['data']['stock_count_hd_csv_file'];

	$txt = file_get_contents($file);

	$table_name = 'aa_exp_inv';

	$dao->dbh->exec('SET NAMES tis620');

	$i = 0;

	$deleteDate = array();

	foreach (explode("\r\n", $txt) as $ke => $ve) {

		++$i;
		if ($i == 1) {

			//
			//
			foreach (explode('|', $ve) as $kc => $vc) {

				if (empty($vc)) {
					continue;
				}

				$column = str_replace(array(' ', '.', '(', ')', '%', '/'), array(''), $vc) . '_';

				$column = str_replace(array('@'), array('cost'), $column);

				$columns[] = $column;
			}
			continue;
		}

		$ex = explode('|', $ve);
		$keep = array();
		foreach ($columns as $kc => $vc) {

			if (!isset($ex[$kc])) {

				$val = '';
			} else {
				$val = addslashes(str_replace('\'', '', $ex[$kc]));

				if ($vc == 'Date_') {

					$exD = explode('/', $val);

					$val = '' . $exD[2] . '-' . $exD[1] . '-' . $exD[0] . '';
					$deleteDate[$val] = $val;
				}
			}

			$keep[] = "'" . $val . "' as " . $vc . "";
		}

		$sqlUnion[] = "

			SELECT
				" . implode(',', $keep) . "
		";

		if (count($sqlUnion) > 10000) {
			$sqls[] = "
				REPLACE INTO " . $table_name . " ( " . implode(',', $columns) . " )
				SELECT
					new_tb.*
				FROM (" . implode(' UNION ', $sqlUnion) . ") as new_tb;

			";
			$sqlUnion =  array();
		}
	}

	if (count($sqlUnion) > 0) {
		$sqls[] = "
			REPLACE INTO " . $table_name . " ( " . implode(',', $columns) . " )
			SELECT
				new_tb.*
			FROM (" . implode(' UNION ', $sqlUnion) . ") as new_tb;

		";
		$sqlUnion =  array();
	}

	$sql = "
		DELETE FROM " . $table_name . "

		WHERE Date_ IN ('" . implode("','", $deleteDate) . "');
	";

	$dao->execDatas($sql);

	foreach ($sqls as $ks => $vs) {

		$dao->execDatas($vs);
	}

	$sql = "

		INSERT INTO aa_exp_inv_intensive ( active, product, type, price )

		SELECT
			0 as active,
			ProductName_ as product,
			2 as type,
			0 as price

		FROM aa_exp_inv
		where ProductName_ NOT IN (
			SELECT
				product
			FROM aa_exp_inv_intensive
		)
		group by ProductName_
	";

	$dao->execDatas($sql);
}

//
//
function uploadYui($param)
{

	//$param['data']['max_sell_time'] = 3;

	if (empty($param['data']['stock_count_hd_csv_file']))
		return false;

	set_time_limit(0);

	$file = FILE_FOLDER . '/' . $param['data']['stock_count_hd_csv_file'];

	$objCSV = fopen($file, "r");

	$r = 0;

	$trs = array();
	while (($objArr = fgetcsv($objCSV, 0, ",")) !== FALSE) {

		++$r;

		//
		// header
		if ($r == 2) {

			$columns_title = array();

			$columns = array();



			for ($c = 0; $c <= 100; ++$c) {

				if (!isset($objArr[$c]))
					break;

				//
				// product detail
				if ($c <= 2) {

					$columns_title[] = '<Cell ss:StyleID="s62"><Data ss:Type="String"></Data></Cell>';
				}
				//
				// Order detail
				else if (!empty($objArr[$c])) {

					$label_index[] = $c;

					$columns[] = '<Cell ss:StyleID="s62"><Data ss:Type="String">' . $objArr[$c] . '</Data></Cell>';
				}
			}

			$columns_title[] = '<Cell ss:StyleID="s62"><Data ss:Type="String">Total</Data></Cell>';

			$columns = array_merge($columns_title, $columns);

			$trs[] = '<Row ss:AutoFitHeight="0">' . implode('', $columns) . '</Row>';
		} else if ($r > 3) {

			$columns_title = array();
			$columns = array();



			for ($c = 0; $c <= 100; ++$c) {

				if (!isset($objArr[$c]))
					break;
				//
				// product detail
				if ($c <= 2) {

					$columns_title[] = '<Cell ss:StyleID="s63"><Data ss:Type="String">' . $objArr[$c] . '</Data></Cell>';
				}
				//
				// Order detail
				else if (in_array($c, $label_index)) {


					$sellPerMonth = $objArr[$c + 1];

					if (empty($sellPerMonth) || $sellPerMonth == 0) {

						$columns[] = '<Cell ss:StyleID="s64"/>';
					} else {

						$min_qty = $param['data']['min_qty'];

						$sellTime = $min_qty / $sellPerMonth;


						for ($m = 1; $m <= 10; ++$m) {

							if ($sellTime > $param['data']['sale_on_month']) {

								$min_qty -= 1;

								$sellTime = $min_qty / $sellPerMonth;
							} else {

								break;
							}
						}

						$order = $min_qty - $objArr[$c];

						if ($order <= 0) {

							$order = 0;
							$columns[] = '<Cell ss:StyleID="s64" />';
						} else if ($order > $min_qty) {

							$order = $min_qty;
							$columns[] = '<Cell ss:StyleID="s64"><Data ss:Type="Number">' . $order . '</Data></Cell>';
						} else {

							$columns[] = '<Cell ss:StyleID="s64"><Data ss:Type="Number">' . $order . '</Data></Cell>';
						}
					}
				}
			}


			$columns_title[] = '<Cell ss:StyleID="s65" ss:Formula="=SUM(RC[' . (count($columns)) . ']:RC[1])"><Data ss:Type="Number">192</Data></Cell>';

			$columns = array_merge($columns_title, $columns);

			$trs[] = '<Row ss:AutoFitHeight="0">' . implode('', $columns) . '</Row>';

			$ExpandedColumnCount = count($columns);
		}
	}




	$columns = array();
	$columns[] = '<Cell ss:StyleID="s66"><Data ss:Type="String">' . $objArr[0] . '</Data></Cell>';
	$columns[] = '<Cell ss:StyleID="s66"><Data ss:Type="String">' . $objArr[1] . '</Data></Cell>';
	$columns[] = '<Cell ss:StyleID="s66"><Data ss:Type="String">' . $objArr[2] . '</Data></Cell>';

	$columns[] = '<Cell ss:StyleID="s65" ss:Formula="=SUM(R[-' . count($trs) . ']C:R[-1]C)"><Data ss:Type="Number">4</Data></Cell>';

	for ($c = 1; $c <= ($ExpandedColumnCount - 4); ++$c) {

		$columns[] = '<Cell ss:StyleID="s65" ss:Formula="=SUM(R[-' . count($trs) . ']C:R[-1]C)"><Data ss:Type="Number">4</Data></Cell>';
	}


	$trs[] = '<Row ss:AutoFitHeight="0">' . implode('', $columns) . '</Row>';


	//$file = "demo.xml";

	$html = '
		<?xml version="1.0"?>
		<?mso-application progid="Excel.Sheet"?>
		<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
		xmlns:o="urn:schemas-microsoft-com:office:office"
		xmlns:x="urn:schemas-microsoft-com:office:excel"
		xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
		xmlns:html="http://www.w3.org/TR/REC-html40">
		<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
		<Author>bomb</Author>
		<LastAuthor>bomb</LastAuthor>
		<Created>2017-08-01T07:04:08Z</Created>
		<LastSaved>2017-08-01T07:06:43Z</LastSaved>
		<Version>12.00</Version>
		</DocumentProperties>
		<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
		<WindowHeight>9705</WindowHeight>
		<WindowWidth>23655</WindowWidth>
		<WindowTopX>120</WindowTopX>
		<WindowTopY>390</WindowTopY>
		<ProtectStructure>False</ProtectStructure>
		<ProtectWindows>False</ProtectWindows>
		</ExcelWorkbook>
		<Styles>
		<Style ss:ID="Default" ss:Name="Normal">
		<Alignment ss:Vertical="Bottom"/>
		<Borders/>
		<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
		<Interior/>
		<NumberFormat/>
		<Protection/>
		</Style>
		<Style ss:ID="s62">
		<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
		<Borders>
		<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
		</Borders>
		<Font ss:FontName="Calibri" x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
		</Style>
		<Style ss:ID="s63">
		<Borders>
		<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
		</Borders>
		<Font ss:FontName="Times New Roman" x:Family="Roman" ss:Size="11"
		ss:Color="#000000" ss:Bold="1"/>
		<NumberFormat ss:Format="@"/>
		</Style>

		<Style ss:ID="s64">
		<Alignment ss:Horizontal="Right" ss:Vertical="Center" ss:WrapText="1"/>
		<Borders>
		<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
		</Borders>
		<Font ss:FontName="Calibri" x:Family="Swiss" ss:Color="#000000" />
		<NumberFormat ss:Format="Standard"/>
		</Style>

		<Style ss:ID="s65">
		<Alignment ss:Horizontal="Right" ss:Vertical="Center" ss:WrapText="1"/>
		<Borders>
		<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
		</Borders>
		<Font ss:FontName="Calibri" x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
		<Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
		<NumberFormat ss:Format="Standard"/>
		</Style>

		<Style ss:ID="s66">
		<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
		<Borders>
		<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
		<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
		</Borders>
		<Font ss:FontName="Times New Roman" x:Family="Roman" ss:Size="11"
		ss:Color="#000000" ss:Bold="1"/>
		<Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
		<NumberFormat ss:Format="@"/>
		</Style>
		</Styles>
		<Worksheet ss:Name="Sheet1">
		<Table ss:ExpandedColumnCount="' . $ExpandedColumnCount . '" ss:ExpandedRowCount="' . count($trs) . '" x:FullColumns="1"
		x:FullRows="1" ss:DefaultRowHeight="15">
		<Column ss:AutoFitWidth="0" ss:Width="309.75"/>
		<Column ss:AutoFitWidth="0" ss:Width="173.25"/>
		<Column ss:Index="4" ss:AutoFitWidth="0" ss:Width="55.5"/>
		<Column ss:AutoFitWidth="0" ss:Width="63"/>
		' . implode('', $trs) . '
		</Table>
		<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
		<PageSetup>
		<Header x:Margin="0.3"/>
		<Footer x:Margin="0.3"/>
		<PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
		</PageSetup>
		<Unsynced/>
		<Selected/>
		<Panes>
		<Pane>
		<Number>3</Number>
		<ActiveRow>5</ActiveRow>
		<ActiveCol>2</ActiveCol>
		</Pane>
		</Panes>
		<ProtectObjects>False</ProtectObjects>
		<ProtectScenarios>False</ProtectScenarios>
		</WorksheetOptions>
		</Worksheet>
		<Worksheet ss:Name="Sheet2">
		<Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
		x:FullRows="1" ss:DefaultRowHeight="15">
		<Row ss:AutoFitHeight="0"/>
		</Table>
		<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
		<PageSetup>
		<Header x:Margin="0.3"/>
		<Footer x:Margin="0.3"/>
		<PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
		</PageSetup>
		<Unsynced/>
		<ProtectObjects>False</ProtectObjects>
		<ProtectScenarios>False</ProtectScenarios>
		</WorksheetOptions>
		</Worksheet>
		<Worksheet ss:Name="Sheet3">
		<Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
		x:FullRows="1" ss:DefaultRowHeight="15">
		<Row ss:AutoFitHeight="0"/>
		</Table>
		<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
		<PageSetup>
		<Header x:Margin="0.3"/>
		<Footer x:Margin="0.3"/>
		<PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
		</PageSetup>
		<Unsynced/>
		<ProtectObjects>False</ProtectObjects>
		<ProtectScenarios>False</ProtectScenarios>
		</WorksheetOptions>
		</Worksheet>
		</Workbook>
	';

	$file = str_replace('csv', 'xml', $file);

	file_put_contents($file, trim($html));
}


function updateSaleResultDt($param)
{

	//arr( $param  );
	$dao = getDb();

	$sql = "
		UPDATE aa_sale_result_dt
		SET
			size =  '" . $param['data']['size'] . "',
			product_name = '" . $param['data']['name'] . "'
		WHERE article_id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);
}

//
//
function insertErpKeepProductCost($param)
{
	//arr( $param );
	//$k_tb_name = $param['k_tb_name'];

	$dao = getDb();

	$sqlUnion = array();
	foreach (explode(',', $param['data']['product_ids']) as $ka => $va) {

		$sqlUnion[] = "

			SELECT
				" . $param['parent_id'] . " as cost_id,
				" . $va . " as product_id
		";
	}

	$sql = "
		DELETE FROM erp_product_standard_cost_keep_products WHERE cost_id = " . $param['parent_id'] . ";

		REPLACE INTO erp_product_standard_cost_keep_products ( cost_id, product_id )

		SELECT
			new_tb.*
		FROM (
			" . implode(' UNION ', $sqlUnion) . "
		) as new_tb
		WHERE new_tb.product_id != 0
		;

	";

	$filters = array();

	$sql = genCond($sql, $filters);
	//arr( $sql );
	$dao->execDatas($sql);
}


//
//
function insertErpProductStandardCostFc($param)
{

	$dao = getDb();

	$sql = "
		REPLACE INTO erp_product_standard_cost_fc ( product_parent_id, fix_cost_id, admin_company_id )
		SELECT

			" .  $param['main_id'] . " as product_parent_id,
			c.id as fix_cost_id,
			1 as admin_company_id
		FROM erp_fix_cost c
		WHERE c.id NOT IN (
			SELECT
				fix_cost_id
			FROM erp_product_standard_cost_fc
			WHERE product_parent_id = " .  $param['main_id'] . "

		)
	";

	$filters = array();

	$sql = genCond($sql, $filters);


	$dao->execDatas($sql);
}

//
//
function updateErpProductDt($param)
{


	$dao = getDb();

	$sql = "
		UPDATE erp_product_dt a
		LEFT JOIN erp_product_color c ON a.product_color_id = c.product_color_id
		LEFT JOIN erp_product b ON a.product_id = b.product_id
		LEFT JOIN erp_product_um u ON b.product_um_id = u.product_um_id

		SET
			a.active = b.active,
			a.product_code = b.product_code,
			a.product_name = b.product_name,
			a.admin_company_id = b.admin_company_id,
			a.product_color_code = c.product_color_code,
			a.stockable = b.stockable,
			a.saleable = b.saleable,
			a.product_group_price_id = b.product_group_price_id,
			a.product_grade = b.product_grade,
			a.w = b.product_width,
			a.l = b.product_lenght,
			a.um = u.product_um_name,
			a.total_qty = 9999,
			a.code_color = CONCAT( a.product_id, '-', a.product_color_id )

		WHERE a.product_id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);
}



//
//
function uploadMoveSacFile($param)
{

	//arr( $param['data']['factory'] );

	if (!empty($_FILES['file_name']['tmp_name'])) {



		$file_get_contents = file_get_contents($_FILES['file_name']['tmp_name']);

		file_put_contents('/home/sacuser/sac-erp/' . $param['data']['factory'] . '/' . $_FILES['file_name']['name'], $file_get_contents);
	}
}

//
//
function updateErpProduct($param)
{
	$dao = getDb();


	$sql = "

		UPDATE erp_product
		SET
			product_group_price_id = " . $param['parent_id'] . "
		WHERE product_id IN ( " . $param['data']['product_ids'] . " )
	";

	//arr( $sql );
	$dao->execDatas($sql);
}

//
//
function updateErpCompany($param)
{
	$dao = getDb();


	$sql = "

		UPDATE erp_company
		SET
			company_group_price_id = " . $param['parent_id'] . "
		WHERE company_id IN ( " . $param['data']['company_ids'] . " )
	";

	$dao->execDatas($sql);
}


//
//
function importSacSendOrderDt($param)
{

	$k_tb_name = $param['k_tb_name'];

	$main_id = $param['parent_id'];

	$purchase_receive_id = $param['data']['purchase_receive_id'];


	$prefix = '';

	$dao = getDb();


	$sql = "
		SELECT
			(
				SELECT
					IFNULL( SUM( purchase_receive_dt_qty ), 0 )
				FROM " . $k_tb_name . "
				WHERE purchase_receive_dt_id = a.purchase_receive_dt_id
			) as sum_qty,
			a.*
		FROM sac_purchase_receive_dt a
		HAVING a.purchase_receive_id = " . $purchase_receive_id . "
		AND a.purchase_receive_dt_qty > sum_qty
		ORDER BY a.purchase_receive_dt_id ASC";

	foreach ($dao->fetchAll($sql) as $ka => $va) {

		$data = array();
		$data['send_order_dt_id'] = getSkipId($k_tb_name, 'send_order_dt_id');

		$data['send_order_id'] = $main_id;


		$data['purchase_order_dt_id'] = $va->purchase_order_dt_id;
		$data['purchase_receive_dt_id'] = $va->purchase_receive_dt_id;

		$data['purchase_receive_dt_qty'] = $va->purchase_receive_dt_qty - $va->sum_qty;

		$data['purchase_receive_dt_name'] = '';

		$data['purchase_receive_dt_comment'] = '';

		$data['purchase_order_dt_total'] = $va->purchase_order_dt_total * $data['purchase_receive_dt_qty'] / $va->purchase_receive_dt_qty;
		$data['purchase_order_dt_adj'] = $va->purchase_order_dt_adj;
		$data['purchase_order_dt_total_show'] = $va->purchase_order_dt_total_show;
		$data['purchase_order_dt_vat_bath'] = $va->purchase_order_dt_vat_bath;
		$data['purchase_order_dt_total_after_vat'] = $va->purchase_order_dt_total_after_vat;
		$data['purchase_order_dt_discount'] = $va->purchase_order_dt_discount;
		$data['purchase_order_vat_rate'] = $va->purchase_order_vat_rate;
		$data['purchase_order_dt_price'] = $va->purchase_order_dt_price;
		$data['purchase_order_dt_vat_type'] = $va->purchase_order_dt_vat_type;
		$data['purchase_items_id'] = $va->purchase_items_id;
		$data['remark'] = $va->remark;

		$dao->insert($k_tb_name, $data);
	}
}



//
//
function updateRowPurchaseShipment($param)
{

	$main_id = $param['parent_id'];

	$dao = getDb();

	$sql = "
		UPDATE sac_purchase_shipment a
		LEFT JOIN (
			SELECT
				purchase_shipment_id,
				SUM( purchase_shipment_dt_inserance ) as sum_purchase_shipment_dt_inserance,
				SUM( purchase_shipment_dt_indfrt ) as sum_purchase_shipment_dt_indfrt,
				SUM( purchase_shipment_dt_indserchg ) as sum_purchase_shipment_dt_indserchg,
				SUM( purchase_shipment_dt_indserpack ) as sum_purchase_shipment_dt_indserpack,
				SUM( purchase_shipment_dt_import_duty ) as sum_purchase_shipment_dt_import_duty,
				SUM( purchase_shipment_dt_vat ) as sum_purchase_shipment_dt_vat
			FROM sac_purchase_shipment_dt
			WHERE purchase_shipment_id = " . $main_id . "
			GROUP BY purchase_shipment_id

		) b ON a.purchase_shipment_id = b.purchase_shipment_id

		SET
			a.purchase_shipment_indtax = b.sum_purchase_shipment_dt_import_duty,
			a.purchase_shipment_indvat = b.sum_purchase_shipment_dt_vat,
			a.purchase_shipment_inserance = b.sum_purchase_shipment_dt_inserance,
			a.purchase_shipment_indfrt = b.sum_purchase_shipment_dt_indfrt,
			a.purchase_shipment_indserchg = b.sum_purchase_shipment_dt_indserchg,
			a.purchase_shipment_indserpack = b.sum_purchase_shipment_dt_indserpack
		WHERE a.purchase_shipment_id = " . $main_id . "

	";
	$dao->execDatas($sql);
}




//
//
function importShipmentDt($param)
{

	$main_id = $param['parent_id'];

	$purchase_receive_id = $param['data']['purchase_receive_id'];
	$dao = getDb();


	$sql = "
		DELETE FROM sac_purchase_shipment_dt
		WHERE purchase_shipment_id = ?";

	$dao->execDatas($sql, array($main_id));

	$sql = "
		INSERT INTO sac_purchase_shipment_dt (
			purchase_receive_dt_id,
			purchase_shipment_id,
			purchase_order_dt_total,
			purchase_shipment_dt_inserance,
			purchase_shipment_dt_indfrt,
			purchase_shipment_dt_indserchg,
			purchase_shipment_dt_indserpack,
			user_id,
			purchase_shipment_dt_vat,
			purchase_shipment_dt_import_duty
		)

		SELECT
			a.purchase_receive_dt_id,
			c.purchase_shipment_id,
			a.purchase_order_dt_total_show,
			ROUND( c.purchase_shipment_inserance * a.purchase_order_dt_total_show / b.purchase_order_total_before_vat, 2 ),
			ROUND( c.purchase_shipment_indfrt * a.purchase_order_dt_total_show / b.purchase_order_total_before_vat, 2 ),
			ROUND( c.purchase_shipment_indserchg * a.purchase_order_dt_total_show / b.purchase_order_total_before_vat, 2 ),
			ROUND( c.purchase_shipment_indserpack * a.purchase_order_dt_total_show / b.purchase_order_total_before_vat, 2 ),
			" . $_SESSION[Uid] . ",
			ROUND( c.purchase_shipment_indvat * a.purchase_order_dt_total_show / b.purchase_order_total_before_vat, 2 ),
			ROUND( c.purchase_shipment_indtax * a.purchase_order_dt_total_show / b.purchase_order_total_before_vat, 2 )
		FROM sac_purchase_receive_dt a
		LEFT JOIN sac_purchase_receive b ON a.purchase_receive_id = b.purchase_receive_id
		LEFT JOIN sac_purchase_shipment c ON b.purchase_receive_id = c.purchase_receive_id
		WHERE
			a.purchase_receive_id = " . $purchase_receive_id;

	//arr( $sql );
	$dao->execDatas($sql);
}

//
//
function updateRowVat_($param)
{

	$main_id = $param['parent_id'];

	$fGroup = isset($param['fGroup']) ? $param['fGroup'] : 'purchase_order_id';

	$sub_tb = isset($param['sub_tb']) ? $param['sub_tb'] : 'sac_purchase_order_dt';

	$main_tb = isset($param['main_tb']) ? $param['main_tb'] : 'sac_purchase_order';

	$dao = getDb();

	$sql = "
		UPDATE " . $sub_tb . "
		SET
			purchase_order_dt_total = ROUND( purchase_order_dt_total, 2 ),
			purchase_order_dt_total_show =
				ROUND(
					IF( purchase_order_dt_vat_type = 2,
						purchase_order_dt_total,
						purchase_order_dt_total * 100 / ( 100 + purchase_order_vat_rate )
					)
				, 2 ),


			purchase_order_dt_total_after_vat =
				ROUND(
					IF( purchase_order_dt_vat_type = 2,
						purchase_order_dt_total * ( 100 + purchase_order_vat_rate ) / 100,
						purchase_order_dt_total
					)
				, 2 ),
			purchase_order_dt_vat_bath =
				ROUND( purchase_order_dt_total_after_vat - purchase_order_dt_total_show, 2 )

		WHERE " . $fGroup . " = " . $main_id . "";

	$dao->execDatas($sql);

	$sql = "
		UPDATE " . $main_tb . " a
		LEFT JOIN (
			SELECT
				" . $fGroup . ",
				SUM( purchase_order_dt_total_show ) as sum_purchase_order_dt_total_show,
				SUM( purchase_order_dt_total_after_vat ) as sum_purchase_order_dt_total_after_vat,
				SUM( purchase_order_dt_vat_bath ) as sum_purchase_order_dt_vat_bath
			FROM  " . $sub_tb . "
			GROUP BY " . $fGroup . "

		) d ON a." . $fGroup . " = d." . $fGroup . "

		SET
			a.purchase_order_total_before_vat = d.sum_purchase_order_dt_total_show,
			a.purchase_order_total_after_vat =  d.sum_purchase_order_dt_total_after_vat,
			a.purchase_order_vat_bath = d.sum_purchase_order_dt_vat_bath
		WHERE a." . $fGroup . " = " . $main_id;

	$dao->execDatas($sql);
}




//
//
function autoInsertGroupProgram($param)
{

	if ($param['data']['set_permission'] == 0) {

		return false;
	}
	$dao = getDb();

	$page_id = $param['data']['model_id'];

	$sql = "
		SELECT
			new_config_id
		FROM admin_model
		WHERE model_id = " . $page_id . "
		AND new_config_id IS NOT NULL
	";
	$res = $dao->fetch($sql);
	//arr( $res );
	//arr( $sql );
	//exit;

	if (!$res)
		return false;

	$config = getConfig_($res->new_config_id);

	$group_id =  $param['main_id'];

	$sqlUnion = array();

	$add_row = isset($param['add_row']) ? $param['add_row'] : 1;
	$edit = isset($param['edit']) ? $param['edit'] : 1;
	$delete_row = isset($param['delete_row']) ? $param['delete_row'] : 1;

	if (in_array(1, array($add_row, $edit, $delete_row))) {

		$sqlUnion[$page_id] = "
			SELECT
				NULL as allow_lock_tab,
				'all' as views_book_id,
				" . $group_id . " as group_id,
				" . $page_id . " as page_id,
				1 as permission,
				" . $add_row . " as add_row,
				" . $edit . " as edit,
				" . $delete_row . " as delete_row,
				'' as inspect,
				'' as prove,
				'all' as views_department_id,
				0 as views_special,
				1 as auto_insert
		";
	}



	if (!empty($config->tab_config)) {

		foreach (json_decode($config->tab_config) as $kt => $vt) {

			$configTab = getConfig_($vt->tab_id);

			if (!empty($configTab->before_action)) {

				foreach (json_decode($configTab->before_action) as $ka => $va) {

					if (isset($va->openWIndow)) {

						$param['add_row'] = 1;
						$param['edit'] = 0;
						$param['delete_row'] = 0;
						$param['data']['model_id'] = $va->openWIndow;
						autoInsertGroupProgram($param);
					}
				}
			}
		}
	}

	if (!empty($config->before_action)) {

		foreach (json_decode($config->before_action) as $ka => $va) {

			if (isset($va->openWIndow)) {
				$param['add_row'] = 1;
				$param['edit'] = 0;
				$param['delete_row'] = 0;
				$param['data']['model_id'] = $va->openWIndow;

				autoInsertGroupProgram($param);
			}
		}
	}

	$dao->execDatas("

		INSERT INTO admin_group_page (
			allow_lock_tab,
			views_book_id,
			group_id,
			page_id,
			permission,
			add_row,
			edit,
			delete_row,
			inspect,
			prove,
			views_department_id,
			views_special,
			auto_insert
		)
		SELECT
			new_tb.*
		FROM (
			" . implode(" UNION ", $sqlUnion) . "
		) as new_tb
	");

	/*	$dao->execDatas( "
		UPDATE admin_group
		SET set_permission = 0
		WHERE group_id = ". $group_id ."

	" );*/
}



//
//
function insertErpSlabOrderDt($param)
{

	//arr( $param );
	$dao = getDb();

	$sqls[] = "

		INSERT INTO erp_slab_order_dt (
			sale_order_id,
			w,
			l,
			qty,
			parent_id,
			selected_dt_id
		)


		SELECT
			dt.order_id,
			dt.w,
			dt.l,
			dt.qty - IFNULL( (
				SELECT
					SUM( qty )
				FROM erp_slab_order_dt
				WHERE selected_dt_id = dt.id

			), 0 ) as qty,
			" . $param['main_id'] . ",
			dt.id
		FROM erp_selected_dt dt
		WHERE order_id IN ( " . $param['data']['sale_order_ids'] . " )
		AND type = 'cut'
		HAVING qty != 0
	";


	$sql = implode('', $sqls);

	//arr( $sql );

	$dao->execDatas($sql);
}

function resetErpSlabOrderImg($param)
{
	//arr( $param['parent_id'] );
	$dao = getDb();
	//
	$sql = "
		UPDATE erp_slab_order
		SET img_html = ''
		WHERE id = " . $param['parent_id'] . "
	";
	$dao->execDatas($sql);
}

//
//
function insertErpSaleOrderSelectedDt($param)
{


	$dao = getDb();

	$sqls[] = "

		INSERT INTO erp_selected_dt (
			qty,
			type,
			sale_order_dt_id,
			parent_id,
			order_id,
			w,
			l

		)
	";

	if (isset($param['type']) && $param['type'] == 'stock') {

		$sqls[] = "


			SELECT
				dt.qty - IFNULL( (
					SELECT
						SUM( qty )
					FROM erp_selected_dt
					WHERE sale_order_dt_id = dt.id
					AND type = 'stock'
				), 0 ) as qty,
				'stock',
				dt.id,
				" . $param['main_id'] . ",
				" . $param['data']['sale_order_id'] . ",
				p.product_width,
				p.product_lenght
			FROM erp_sale_order_dt dt
			LEFT JOIN erp_product p ON dt.product_id = p.product_id
			WHERE sale_order_id = " . $param['data']['sale_order_id'] . "
			HAVING qty != 0

		";
	} else {


		$sqls[] = "

			SELECT
				dt.qty - IFNULL( (
					SELECT
						SUM( qty )
					FROM erp_selected_dt
					WHERE sale_order_dt_id = dt.id

				), 0 ) as qty,
				'cut',
				dt.id,
				" . $param['main_id'] . ",
				" . $param['data']['sale_order_id'] . ",
				p.product_width,
				p.product_lenght
			FROM erp_sale_order_dt dt
			LEFT JOIN erp_product p ON dt.product_id = p.product_id
			WHERE sale_order_id = " . $param['data']['sale_order_id'] . "
			HAVING qty != 0

		";
	}

	$sql = implode('', $sqls);

	$dao->execDatas($sql);
}

//
//
function updateTbProduct($param)
{


	$dao = getDb();

	$sql = "

		UPDATE tb_product p
		SET
			p.qty = (

			SELECT

				SUM( dt.qty * dt.factor ) AS qty

			FROM stock_dt dt
			WHERE dt.product_id = p.product_id
		)

		WHERE p.product_id = " . $param['data']['product_id'] . "
	";

	//arr( $sql );
	$dao->execDatas($sql);
}


//
//
function rollBackQtyReTurn($param)
{

	$dao = getDb();
	$sql = "

		UPDATE oem_stock_dt
		SET qty_return = 0

		WHERE stock_hd_id NOT IN (
			SELECT
				IFNULL( do_id, 0 )
			FROM oem_stock_hd
			WHERE do_id IS NOT NULL
		)
	";

	$dao->execDatas($sql);


	$sql = "

		UPDATE oem_stock_dt
		SET
			cost = 0,
			discount = '',
			amt = 0

		WHERE stock_hd_id NOT IN (
			SELECT
				do_id
			FROM oem_inv

		)
	";

	$dao->execDatas($sql);
}


//
//
function insertOemStockDt($param)
{

	$dao = getDb();

	if (isset($param['main_data_before']) && ($param['main_data_before']->so_hd_id != $param['data']['so_hd_id'] || $param['main_data_before']->doc_date != $param['data']['doc_date'])) {


		$sql = "
			DELETE FROM oem_stock_dt

			WHERE stock_hd_id = '" . $param['main_id'] . "'
		";

		$dao->execDatas($sql);
	}

	$sql = "
		SELECT
			a.id,
			a.products_dt_id as products_dt_id,
			'" . $param['data']['doc_no'] . "' as doc_no,
			'" . $param['data']['doc_date'] . "' as doc_date,
			a.products_color_id as products_color_id,
			'" . $param['main_id'] . "' as stock_hd_id,
			a.products_id as products_id,
			-1 as qty_factor,
			SUM( qty - cancel_qty )
			+
			IFNULL( (
				SELECT
					SUM( qty * qty_factor )
				FROM oem_stock_dt
				WHERE so_dt_id = a.id
			), 0 ) as needQty
		FROM oem_so_dt a
		WHERE a.so_hd_id = '" . $param['data']['so_hd_id'] . "'
		GROUP BY products_dt_id, a.id
		HAVING needQty != 0

	";
	//arr( $sql );
	//arr( $param );

	foreach ($dao->fetchAll($sql) as $ka => $va) {

		$sql = "
			SELECT
				new_tb.zone_id,
				SUM( stock_qty ) as stock_qty
			FROM (
				SELECT
					CONCAT( 'a-', a.id ) as id,
					a.zone_id,
					( ( a.qty - a.qty_return ) * a.qty_factor ) as stock_qty
				FROM oem_stock_dt a
				WHERE a.products_dt_id = '" . $va->products_dt_id . "'
				AND a.doc_date <= '" . $param['data']['doc_date'] . "'




			) as new_tb
			GROUP BY zone_id
			HAVING stock_qty != 0
		";

		//arr( $sql );

		$needQty = $va->needQty;

		foreach ($dao->fetchAll($sql) as $kb => $vb) {

			$qty = $needQty;

			if ($needQty > $vb->stock_qty) {

				$qty = $vb->stock_qty;

				$needQty -= $vb->stock_qty;
			}

			$sql = "
				INSERT INTO oem_stock_dt (
					id,
					so_dt_id,
					products_dt_id,
					doc_no,
					doc_date,
					products_color_id,
					stock_hd_id,
					products_id,
					qty_factor,
					qty,
					zone_id,
					move_dt_id
				)

				SELECT
					" . getSkipId('oem_stock_dt', 'id') . " as id,
					'" . $va->id . "' as so_dt_id,
					'" . $va->products_dt_id . "' as products_dt_id,
					'" . $va->doc_no . "' as doc_no,
					'" . $va->doc_date . "' as doc_date,
					'" . $va->products_color_id . "' as products_color_id,
					'" . $va->stock_hd_id . "' as stock_hd_id,
					'" . $va->products_id . "' as products_id,
					'" . $va->qty_factor . "' as qty_factor,
					'" . $qty . "' as qty,
					'" . $vb->zone_id . "' as zone_id,
					NULL as move_dt_id
			";
			//arr( $sql );
			$dao->execDatas($sql);
		}
	}
}




//
//
function updateAction($param)
{
}

//
//
function insertOEMProductDt($param)
{

	$dao = getDb();

	//
	$sql = "
		INSERT INTO oem_products_dt (
			id,
			products_id,
			products_color_id
		)

		SELECT

			'" . $param['data']['products_dt_id'] . "' as product_id,
			" . $param['data']['products_id'] . " as product_id,
			" . $param['data']['products_color_id'] . " as product_color_id

	";

	//arr( $sql );

	$dao->execDatas($sql);
}


//
//
function updateErpStockDt_________($param)
{

	return;
	//$data, $main_data_before, $action_type

	//arr($action_type);
	/*erp_stock sort by doc_date asc

	erp_stock_dt stock_act_action + มาก่อน -

	stock_dt_auto_calc 0 มาก่อน 1

	erp_stock sort by doc_no asc

	if autocal == 0 ///  autocal = 0
		stock_dt_amt / stock_dt_qty = stock_dt_cost
	else /// autocal = 1
		stock_dt_amt_bal  บรรทัดก่อนหน้า   / stock_dt_qty_bal  บรรทัดก่อนหน้า  = stock_dt_cost ตัวมันเอง
		stock_dt_cost * stock_dt_qty = stock_dt_amt  ตัวมันเอง
	endif

	autocal == 0 .or. autocal == 1 ทำทั้งคู่  ไว้ให้  record หลัง
	stock_dt_qty_bal บรรทัดก่อนหน้า  + stock_dt_qty * action ตัวมันเอง = stock_dt_qty_bal ตัวมันเอง
	stock_dt_amt_bal บรรทัดก่อนหน้า + stock_dt_amt * action ตัวมันเอง = stock_dt_amt_bal ตัวมันเอง*/

	//( $_REQUEST );

	//arr( $data );
	$dao = getDb();

	$param['config_name'] = array('config_stock_closed_date');

	$getClosedDate = getConfigVal($param);

	$sql = "
		SELECT
			LAST_DAY( '" . $getClosedDate . "' ) as end_of_month,
			MAX( doc_date ) as last_doc_date
		FROM erp_stock
	";

	$res = $dao->fetch($sql);

	$last_doc_date = $res->last_doc_date;

	$end_of_closed_date_month = $res->end_of_month;

	$sql = "
		SELECT
			product_id
		FROM erp_product
		WHERE update_stock_cost = 1
	";

	//
	//
	foreach ($dao->fetchAll($sql) as $kp => $vp) {

		$product_id = $vp->product_id;

		$sql = "
			SELECT
				stock_master_qty as stock_dt_qty_bal,
				stock_master_amt as stock_dt_amt_bal
			FROM erp_stock_master
			WHERE admin_company_id = " . $_SESSION['user']->company_id . "
			AND product_id = " . $product_id . "
		";

		$master_stock = $dao->fetch($sql);

		if (!$master_stock) {

			$master_stock = new stdClass;
			$master_stock->stock_dt_qty_bal = 0;
			$master_stock->stock_dt_amt_bal = 0;
		}

		$sql = "
			SELECT
				a.*
			FROM erp_stock_dt a
			LEFT JOIN erp_stock b ON a.stock_id = b.stock_id
			WHERE a.product_id = " . $product_id . "
			AND b.doc_date > '" . $getClosedDate . "'
			ORDER BY
				b.doc_date ASC,
				a.stock_act_action DESC,
				a.stock_dt_auto_calc ASC,
				b.doc_no ASC
		";

		$rowBefore = $master_stock;
		foreach ($dao->fetchAll($sql) as $ka => $va) {

			$data_ = array();

			if ($va->stock_dt_auto_calc == 1) {

				if ($rowBefore->stock_dt_qty_bal != 0) {

					$data_['stock_dt_cost'] = $rowBefore->stock_dt_amt_bal / $rowBefore->stock_dt_qty_bal;
				} else {

					$data_['stock_dt_cost'] = 0;
				}

				$data_['stock_dt_amt'] = $data_['stock_dt_cost'] * $va->qty;

				$data_['stock_dt_qty_bal'] = $rowBefore->stock_dt_qty_bal + ($va->qty * $va->stock_act_action);

				$data_['stock_dt_amt_bal'] = $rowBefore->stock_dt_amt_bal + ($data_['stock_dt_amt'] * $va->stock_act_action);
			} else {

				$data_['stock_dt_qty_bal'] = $rowBefore->stock_dt_qty_bal + ($va->qty * $va->stock_act_action);

				$data_['stock_dt_amt_bal'] = $rowBefore->stock_dt_amt_bal + ($va->stock_dt_amt * $va->stock_act_action);
			}

			//
			//$data_['have_update'] = 0;
			$dao->update('erp_stock_dt', $data_, "stock_dt_id = " . $va->stock_dt_id);

			$sql = "
				SELECT
					*
				FROM erp_stock_dt
				WHERE stock_dt_id = " . $va->stock_dt_id . "
			";

			$rowBefore = $dao->fetch($sql);
		}

		//
		// insert edit erp_stock_master_period
		/*$end_of_month = $end_of_closed_date_month;
		$keep = array();
		for ( $i = 1; $i <= 200; ++$i ) {

			$sql = "
				SELECT
					LAST_DAY( ADDDATE( '". $end_of_month ."', +1 ) ) as end_of_month
			";

			$end_of_month = $dao->fetch( $sql )->end_of_month;


			$ex = explode( '-', $end_of_month );

			$keep[] = "
				SELECT
					'". $ex[0] ."-". $ex[1] ."' as stock_master_period_year_month,
					SUM( a.qty * a.stock_act_action ) + ". $master_stock->stock_dt_qty_bal ." as stock_master_qty,
					SUM( a.stock_dt_amt ) + ". $master_stock->stock_dt_amt_bal ." as stock_master_amt

				FROM erp_stock_dt a
				LEFT JOIN erp_stock b ON a.stock_id = b.stock_id
				WHERE a.product_id = ". $product_id ."
				AND b.doc_date <= '". $end_of_month ."'
				AND b.doc_date > '". $getClosedDate ."'
			";

			if ( gettime_( $end_of_month, 12 ) == gettime_( $last_doc_date, 12 ) ) {
				break;
			}
		}

		$sql = "
			REPLACE INTO erp_stock_master_period (
				admin_company_id,
				product_id,
				stock_master_period_year_month,
				stock_master_qty,
				stock_master_amt
			)
			SELECT
				". $_SESSION['user']->company_id .",
				". $product_id .",
				new_tb.stock_master_period_year_month,
				new_tb.stock_master_qty,
				new_tb.stock_master_amt
			FROM (
				". implode( ' UNION ', $keep ) ."
			) as new_tb
		";

		$dao->execDatas( $sql );*/
	}
}


//
//
function updateSn($param)
{

	$dao = getDb();


	//arr( $param );

	$sql = "
		UPDATE erp_sn
		SET
			from_config_id = " . $_REQUEST['parent_config_id'] . ",
			from_parent_id = " . $_REQUEST['main_id'] . ",
			admin_company_id = " . $_SESSION['user']->company_id . "

		WHERE id = " . $param['main_id'] . "
	";

	$dao->execDatas($sql);
}

//
//
function insertGl($param)
{


	$dao = getDb();


	$sql = "

		SELECT
			id
		FROM erp_gl_trn
		WHERE from_config_id = " . $param['getView_config_id'] . "
		AND from_parent_id = " . $param['parent_id'] . "
	";

	$data_['id'] = empty($dao->fetch($sql)->id) ? NULL : $dao->fetch($sql)->id;


	$trn_data = $param['main_data_before'];


	$data_['doc_no'] = $trn_data->doc_no;
	$data_['doc_date'] = $trn_data->doc_date;
	$data_['from_'] = NULL;
	$data_['sale_inv_id'] = NULL;
	$data_['user_id'] = $_SESSION[Uid];
	$data_['admin_company_id'] = $_SESSION['user']->company_id;
	$data_['department_id'] = $_SESSION['user']->department_id;

	$data_['from_config_id'] = $param['getView_config_id'];
	$data_['from_parent_id'] = $param['parent_id'];

	$gl_trn_id = $dao->replace('erp_gl_trn', $data_);


	$sql = "
		INSERT INTO erp_gl_trn_dt (
			from_config_id,
			from_parent_id,
			gl_trn_id,
			gl_id,
			" . $param['cd'] . "
			gl_trn_dt_type,
			gl_trn_dt_desc
		)

		SELECT
			" . $param['getView_config_id'] . ",
			" . $param['parent_id'] . ",
			" . $gl_trn_id . ",

			new_tb.gl_id,
			new_tb.debit,
			new_tb.credit,
			new_tb.gl_trn_dt_type,
			new_tb.desc_ as gl_trn_dt_desc
		FROM
		(

			SELECT
				CONCAT( 'a-', a.id ),
				" . getConfigVal($param) . " as gl_id,
				0 as credit,
				a.amt as debit,
				1 as gl_trn_dt_type,
				CONCAT( 'จ่ายชำระหนี้ให้ ', c.bank_account_name )as desc_
			FROM erp_cheque_" . $param['chgType'] . "_clearing_dt a
			LEFT JOIN erp_cheque_in_clearing b ON a.parent_id = b.id
			LEFT JOIN erp_bank_account c ON b.bank_account_id = c.id
			WHERE a.parent_id = " . $param['parent_id'] . "

			UNION

			SELECT
				CONCAT( 'b' ),
				c.gl_id,
				SUM( a.amt ),
				0,
				1 as gl_trn_dt_type,
				CONCAT( 'จ่ายชำระหนี้ให้ ', c.bank_account_name )as desc_
			FROM erp_cheque_" . $param['chgType'] . "_clearing_dt a
			LEFT JOIN erp_cheque_in_clearing b ON a.parent_id = b.id
			LEFT JOIN erp_bank_account c ON b.bank_account_id = c.id
			WHERE a.parent_id = " . $param['parent_id'] . "


		) as new_tb
	";

	//arr( $sql );
	$dao->execDatas($sql);
}


//
//
function setErpSaleReturn($param)
{

	$dao = getDb();

	$sql = "
		UPDATE erp_sale_return a
		LEFT JOIN erp_sale_inv b ON a.sale_inv_id = b.id
		LEFT JOIN erp_warehouse c ON a.warehouse_id = c.warehouse_id
		SET
			a.company_id = b.company_id,
			a.pc_id = b.pc_id,
			a.due = b.due,
			a.company_branch_no = b.company_branch_no,
			a.chg_id  = b.company_id,
			a.chg_branch_no = b.company_branch_no,
			a.saleman_id = b.saleman_id,
			a.chg_due = b.due,
			a.admin_company_branch_id = c.company_branch_id,
			a.receive_id = b.receive_id,
			a.vat_rate = b.vat_rate
		WHERE  a.id = " . $param['main_id'] . "

	";
	$dao->execDatas($sql);
}


//
//
function setErpSaleInv($param)
{

	$dao = getDb();

	$sql = "
		UPDATE erp_sale_inv a
		LEFT JOIN erp_sale_order b ON a.sale_order_id = b.id
		LEFT JOIN erp_warehouse c ON a.warehouse_id = c.warehouse_id
		SET
			a.company_id = b.company_id,
			a.pc_id = b.pc_id,
			a.due = b.due,
			a.company_branch_no = b.company_branch_no,
			a.chg_id  = b.company_id,
			a.chg_branch_no = b.company_branch_no,
			a.saleman_id = b.saleman_id,
			a.chg_due = b.due,
			a.admin_company_branch_id = c.company_branch_id,
			a.receive_id = b.receive_id
		WHERE  a.id = " . $param['main_id'] . "

	";
	$dao->execDatas($sql);
}


//
//
function setErpProduct($param)
{

	//arr( $param['data'] );
	$dao = getDb();


	$sql = "

		UPDATE erp_product
		SET product_group_price_id = " . $param['main_id'] . "
		WHERE product_id IN ( " . $param['data']['product_ids'] . " )

	";

	//arr( $sql );
	$dao->execDatas($sql);


	//exit;

}



//
//
function setErpPurchaseInv($param)
{


	$dao = getDb();

	if (empty($param['main_data_before']) || ($param['main_data_before']->company_id != $param['data']['company_id'])) {
		$sql = "
			UPDATE erp_purchase_inv a
			LEFT JOIN erp_company b ON a.company_id = b.company_id


			SET
				a.company_branch_no = b.company_branch_no,
				a.due = b.purchase_due

			WHERE  a.id = " . $param['main_id'] . "

		";
		$dao->execDatas($sql);
	}
}


//
//
function setDataFromId($param)
{

	//arr( $param['main_data_before'] );

	$dao = getDb();


	$sql = "
		SELECT
			*
		FROM " . $param['current_config']->tb_main . "
		WHERE id = " . $param['main_id'] . "
	";

	$res = $dao->fetch($sql);

	//arr( $res );

	if (!empty($res->sale_order_dt_id)) {


		$sql = "

			UPDATE " . $param['current_config']->tb_main . " a

			LEFT JOIN " . $param['checkFrom']->tb_name . " b ON a." . $param['checkFrom']->pri_name . " = b.id

				SET

					a.product_id = b.product_id,
					a.product_dt_id = b.product_dt_id,
					a.product_um_rate = b.product_um_rate,
					a.zone_id = b.zone_id,
					a.product_um_id = b.product_um_id,
					a.price = b.price,
					a.discount = b.discount,
					a.qty = b.product_um_rate * " . $param['data']['qty_um'] . ",
					a.before_vat = b.before_vat / b.qty_um * " . $param['data']['qty_um'] . "

			WHERE a.id = " . $param['main_id'] . "
		";

		//arr( $sql );

		$dao->execDatas($sql);
	}
}

//
//
function resetTrnGL($tb_main_source, $tb_main_source_pri_key, $main_id)
{
	$dao = getDb();
	$sql = "

		DELETE FROM " . $tb_main_source . "
		WHERE " . $tb_main_source_pri_key . " = " . $main_id . ";
	";

	$dao->execDatas($sql);


	$sql = "
		DELETE
			FROM
		erp_gl_trn
		WHERE from_table = '" . $tb_main_source . "'
		AND sale_inv_id = " . $main_id;

	$dao->execDatas($sql);
}


//
// 79
function insertErpApTrn($param)
{

	$main_id = $param['parent_id'];

	$dao = getDb();

	$tb_main_source = 'erp_ap_trn';
	$tb_main_source_pri_key = 'purchase_inv_id';
	$tb_dt_source = 'erp_purchase_inv_dt';



	$sql = "

		DELETE FROM " . $tb_main_source . "
		WHERE " . $tb_main_source_pri_key . " = " . $main_id . ";
	";

	$dao->execDatas($sql);


	$sql = "
		DELETE
			FROM
		erp_gl_trn
		WHERE from_table = '" . $tb_main_source . "'
		AND sale_inv_id = " . $main_id;

	$dao->execDatas($sql);


	//resetTrnGL( $tb_main_source, $tb_main_source_pri_key, $main_id );

	$sql = "

		SELECT
			a.id,
			'erp_purchase_inv' as from_,
			a.purchase_inv_due as due,
			a.company_id as company_id,
			a.total_before_vat as amt,
			a.vat_bath as vat,
			a.total_after_vat as nprice,
			c.company_name,
			a.doc_no,
			a.doc_date ,
			a.company_branch_no as company_branch_no,
			" . $_SESSION['user']->company_id . ",
			a.book_id
		FROM erp_purchase_inv a
		LEFT JOIN erp_company c ON a.company_id = c.company_id
		WHERE a.id = " . $main_id . "
	";

	$res = $dao->fetch($sql);


	$data[$tb_main_source_pri_key] = $main_id;
	$data['from_'] = $res->from_;
	$data['due'] = $res->due;
	$data['company_id'] = $res->company_id;
	$data['amt'] = $res->amt;
	$data['vat'] = $res->vat;
	$data['nprice'] = $res->nprice;
	$data['note'] = 'ซื้อสินค้า / บริการจาก ' . $res->company_name;
	$data['doc_no'] = $res->doc_no;
	$data['doc_date'] = $res->doc_date;
	$data['company_branch_no'] = $res->company_branch_no;
	$data['admin_company_id'] = $_SESSION['user']->company_id;
	$data['book_id'] = $res->book_id;

	$from_id = $dao->insert($tb_main_source, $data);

	inportGlTrnDt($tb_main_source, 'trn_id', $gl_trn_jn_id = 3, $from_id, $data, $main_id);
}


//
//
function inportGlTrnDt($tb_main_source = 'erp_ap_trn', $tb_main_source_pri_key = 'purchase_inv_id', $gl_trn_jn_id = 4, $from_id = 0, $trn_data = [], $main_id = NULL)
{

	$dao = getDb();


	$sql = "
		DELETE
			FROM
		erp_gl_trn
		WHERE from_table = '" . $tb_main_source . "'
		AND from_id = " . $from_id;

	$dao->execDatas($sql);

	if ($tb_main_source == 'erp_ap_trn') {

		$gl_config_vat_name = 'gl_config_vat_buy_gl_id';

		$company_gl_id = 'supplier_gl_id';

		$product_gl_id = 'buy_gl_id';

		$remark = 'ซื้อสินค้า / บริการจาก ';

		$debit_credit = '
			gl_trn_dt_credit,
			gl_trn_dt_debit,
		';

		$from_ = 'erp_purchase_inv';
		$total_name = 'before_vat';

		$tb_dt_source = 'erp_purchase_inv_dt';
		$tb_dt_source_main_key = 'purchase_inv_id';
	} else if ($tb_main_source == 'erp_ap_disc') {

		$gl_config_vat_name = 'gl_config_vat_buy_gl_id';

		$company_gl_id = 'supplier_return_gl_id';

		$product_gl_id = 'return_buy_gl_id';

		$remark = 'ลดหนี้จาก ';

		$debit_credit = '
			gl_trn_dt_debit,
			gl_trn_dt_credit,
		';
		$from_ = 'erp_purchase_return';
		$total_name = 'purchase_inv_dt_total';
		$tb_dt_source = 'erp_purchase_return_dt';
		$tb_dt_source_main_key = 'purchase_return_id';
	} else if ($tb_main_source == 'erp_ar_trn') {

		$gl_config_vat_name = 'gl_config_vat_sale_gl_id';

		$company_gl_id = 'customer_gl_id';

		$product_gl_id = 'sale_gl_id';

		$remark = 'ขายสินค้า / บริการให้ ';

		$debit_credit = '
			gl_trn_dt_debit,
			gl_trn_dt_credit,
		';
		$from_ = 'erp_sale_inv';

		$total_name = 'sale_inv_dt_total';

		$tb_dt_source = 'erp_sale_inv_dt';
		$tb_dt_source_main_key = 'sale_inv_id';
	} else if ($tb_main_source == 'erp_ar_disc') {

		$gl_config_vat_name = 'gl_config_vat_sale_gl_id';

		$company_gl_id = 'customer_return_gl_id';

		$product_gl_id = 'return_gl_id';

		$remark = 'รับคืนขายจาก ';

		$debit_credit = '
			gl_trn_dt_credit,
			gl_trn_dt_debit,
		';
		$from_ = 'erp_sale_return';
		$total_name = 'sale_inv_dt_total';
		$tb_dt_source = 'erp_sale_return_dt';
		$tb_dt_source_main_key = 'sale_return_id';
	}

	$param['config_name'] = array($gl_config_vat_name);

	$data_['doc_no'] = $trn_data['doc_no'];
	$data_['doc_date'] = $trn_data['doc_date'];
	$data_['book_id'] = $trn_data['book_id'];
	$data_['from_'] = $from_;
	$data_['sale_inv_id'] = NULL;
	$data_['user_id'] = $_SESSION[Uid];
	$data_['admin_company_id'] = $_SESSION['user']->company_id;
	$data_['department_id'] = $_SESSION['user']->department_id;
	$data_['jn_id'] = $gl_trn_jn_id;
	$data_['from_id'] = $from_id;
	$data_['from_table'] = $tb_main_source;
	$data_['sale_inv_id'] = $main_id;


	$id = $dao->insert('erp_gl_trn', $data_, $upDuplicate = false, $action = 'REPLACE');

	$skip = array();
	$new_id = getSkipId('erp_gl_trn_dt', 'id', $skip);
	$skip[] = $new_id;

	$sqlUnion[] = "
		SELECT
			a." . $tb_main_source_pri_key . ",
			b." . $company_gl_id . " as gl_id,
			a.nprice as gl_trn_dt_debit,
			0 as gl_trn_dt_credit,
			3 as gl_trn_dt_type,
			" . $new_id . " as new_id
		FROM " . $tb_main_source . " a
		LEFT JOIN erp_company b ON a.company_id = b.company_id
		WHERE a." . $tb_main_source_pri_key . " = " . $from_id . "
	";


	$new_id = getSkipId('erp_gl_trn_dt', 'id', $skip);
	$skip[] = $new_id;

	$sqlUnion[] = "
		SELECT
			a." . $tb_main_source_pri_key . ",
			" . getConfigVal($param) . " as gl_id,
			0 as gl_trn_dt_debit,
			a.vat as gl_trn_dt_credit,
			2 as gl_trn_dt_type,
			" . $new_id . " as new_id
		FROM " . $tb_main_source . " a
		WHERE a." . $tb_main_source_pri_key . " = " . $from_id . "
	";


	$new_id = getSkipId('erp_gl_trn_dt', 'id', $skip);
	$skip[] = $new_id;

	if (empty($trn_data['gl_id'])) {

		$sqlUnion[] = "
			SELECT
				a." . $tb_main_source_pri_key . ",
				d." . $product_gl_id . " as gl_id,
				0 as gl_trn_dt_debit,
				SUM( b." . $total_name . " ) as gl_trn_dt_credit,
				1 as gl_trn_dt_type,
				" . $new_id . " as new_id
			FROM " . $tb_main_source . " a
			LEFT JOIN " . $tb_dt_source . " b ON a." . $tb_dt_source_main_key . " = b." . $tb_dt_source_main_key . "
			LEFT JOIN erp_product_dt c ON b.product_dt_id = c.product_dt_id
			LEFT JOIN erp_product d ON c.product_id = d.product_id
			WHERE a." . $tb_main_source_pri_key . " = " . $from_id . "
			GROUP BY a." . $tb_dt_source_main_key . ", d." . $product_gl_id . "
		";
	} else {

		$sqlUnion[] = "

			SELECT
				a." . $tb_main_source_pri_key . ",
				" . $trn_data['gl_id'] . " as gl_id,
				0 as gl_trn_dt_debit,
				a.amt as gl_trn_dt_credit,
				1 as gl_trn_dt_type,
				" . $new_id . " as new_id
			FROM " . $tb_main_source . " a
		";
	}

	$sql = "
		INSERT INTO erp_gl_trn_dt (
			gl_trn_id,
			gl_id,
			" . $debit_credit . "
			gl_trn_dt_type,
			gl_trn_dt_desc,
			id
		)

		SELECT
			" . $id . " as gl_trn_id,
			new_tb.gl_id,
			new_tb.gl_trn_dt_debit,
			new_tb.gl_trn_dt_credit,
			new_tb.gl_trn_dt_type,
			CONCAT( '" . $remark . "', b.company_name ) as gl_trn_dt_desc,
			new_tb.new_id
		FROM " . $tb_main_source . " a
		LEFT JOIN erp_company b ON a.company_id = b.company_id
		LEFT JOIN(
			" . implode(' UNION ', $sqlUnion) . "

		) new_tb ON a." . $tb_main_source_pri_key . " = new_tb." . $tb_main_source_pri_key . "
		WHERE a." . $tb_main_source_pri_key . " = " . $from_id . "
	";

	//arr( $sql );

	$dao->execDatas($sql);
}

//
// 79
function insertConfig($param)
{

	$dao = getDb();

	$sql = "
		INSERT INTO erp_config (
			admin_company_id,
			key_id,
			val
		)

		SELECT
			" . $param['main_id'] . ",
			id,
			NULL
		FROM erp_config_keys

		ON DUPLICATE KEY UPDATE time_update = NOW()
	";

	//arr( $sql );
	//exit;
	$dao->execDatas($sql);
}

//
//
function insertProductionFormulaRaw($param)
{

	$dao = getDb();

	$main_id = $param['main_id'];

	$data = $param['data'];

	$sql = "
		DELETE
		FROM erp_production_formula_raw
		WHERE product_id NOT IN ( " . $data['product_ids'] . " )
		AND production_formula_dt_id = " . $main_id . "

	";
	$dao->execDatas($sql);

	$sql = "
		INSERT INTO erp_production_formula_raw
		(
			production_formula_dt_id,
			product_id,
			admin_company_id
		)

		SELECT
			" . $main_id . ",
			product_id,
			" . $_SESSION['user']->company_id . "
		FROM erp_product
		WHERE product_id IN ( " . $data['product_ids'] . " )

		ON DUPLICATE KEY
		UPDATE time_update = NOW()
	";

	$dao->execDatas($sql);
}

//
//
function insertMultiValue($param)
{

	$main_id = $param['main_id'];
	$data = $param['data'];


	$dao = getDb();

	$sql = "
		DELETE
		FROM erp_stock_ref_id
		WHERE stock_id = " . $main_id . "
	";
	$dao->execDatas($sql);

	$sql = "
		INSERT INTO erp_stock_ref_id
		(
			stock_id,
			stock_ref_id
		)

		SELECT
			" . $main_id . ",
			stock_id
		FROM erp_stock
		WHERE stock_id IN ( " . $data['stock_ids'] . " )
	";

	$dao->execDatas($sql);
}


//
//
function importErpBookStockAct($main_id, $data)
{

	$dao = getDb();

	$sql = "
		DELETE
		FROM erp_book_stock_act
		WHERE book_id = " . $main_id . "
	";
	$dao->execDatas($sql);

	$sql = "
		INSERT INTO erp_book_stock_act
		(
			book_id,
			stock_act_id
		)

		SELECT
			" . $main_id . ",
			stock_act_id
		FROM erp_stock_act
		WHERE stock_act_id IN ( " . $data['stock_act_ids'] . " )
	";

	$dao->execDatas($sql);
}


//
//
function importTbDiliverDt($main_id, $data, $k_tb_name)
{
	$dao = getDb();


	$sql = "
		INSERT INTO " . DatabaseSac . ".tb_diliver_dt
		(
			plan_id,
			diliver_id,
			area,
			product,
			w,
			plan_no
		)

		SELECT
			a.id,
			" . $main_id . ",
			a.area,
			CONCAT( c.product_code_group_name, ' ', SUBSTRING_INDEX( b.name, '-', 1 )  ),
			a.w,
			a.doc_no

		FROM " . DatabaseSac . ".tb_plan a
		LEFT JOIN " . DatabaseSac . ".tb_product b ON a.product_id = b.product_id
		LEFT JOIN " . DatabaseSac . ".tb_product_code_group c ON b.product_code_group_id = c.product_code_group_id
		WHERE a.id IN ( " . $data['plan_ids'] . " )
	";

	$dao->execDatas($sql);
}






//
//
function updateRowVat($main_id, $fGroup = 'purchase_order_id', $sub_tb = 'sac_purchase_order_dt', $main_tb = 'sac_purchase_order', $sub_prikey = 'purchase_order_dt_id', $qty_name = 'purchase_order_dt_qty', $dot = 2)
{

	$dao = getDb();

	$sql = "
		UPDATE " . $sub_tb . "
		SET
			purchase_order_dt_total = ROUND( purchase_order_dt_total, 2 ),
			purchase_order_dt_total_show =
				ROUND(
					IF( purchase_order_dt_vat_type = 2,
						purchase_order_dt_total,
						purchase_order_dt_total * 100 / ( 100 + purchase_order_vat_rate )
					)
				, 2 ),


			purchase_order_dt_total_after_vat =
				ROUND(
					IF( purchase_order_dt_vat_type = 2,
						purchase_order_dt_total * ( 100 + purchase_order_vat_rate ) / 100,
						purchase_order_dt_total
					)
				, 2 ),
			purchase_order_dt_vat_bath =
				ROUND( purchase_order_dt_total_after_vat - purchase_order_dt_total_show, 2 )

		WHERE " . $fGroup . " = " . $main_id . "";

	$dao->execDatas($sql);

	$sql = "
		UPDATE " . $main_tb . " a
		LEFT JOIN (
			SELECT
				" . $fGroup . ",
				SUM( purchase_order_dt_total_show ) as sum_purchase_order_dt_total_show,
				SUM( purchase_order_dt_total_after_vat ) as sum_purchase_order_dt_total_after_vat,
				SUM( purchase_order_dt_vat_bath ) as sum_purchase_order_dt_vat_bath
			FROM  " . $sub_tb . "
			GROUP BY " . $fGroup . "

		) d ON a." . $fGroup . " = d." . $fGroup . "

		SET
			a.purchase_order_total_before_vat = d.sum_purchase_order_dt_total_show,
			a.purchase_order_total_after_vat =  d.sum_purchase_order_dt_total_after_vat,
			a.purchase_order_vat_bath = d.sum_purchase_order_dt_vat_bath
		WHERE a." . $fGroup . " = " . $main_id;

	$dao->execDatas($sql);
}

//
//
function updateProoveBt($main_id, $tb_main = 'sac_purchase_request', $tb_sub = 'sac_purchase_request_dt', $pri_key = 'purchase_request_id')
{

	$dao = getDb();

	$sql = "
		UPDATE " . $tb_main . " a
		SET a.proove_bt = IF(
			(
				SELECT
					COUNT( * )
				FROM " . $tb_sub . "
				WHERE " . $pri_key . " = a." . $pri_key . "
			) > 0, 1, 0
		)
		WHERE a." . $pri_key . " = " . $main_id . "
	";

	$dao->execDatas($sql);
}

//
// 79
function insertErpArDisc($main_id)
{


	$dao = getDb();

	$tb_main_source = 'erp_ar_disc';
	$tb_main_source_pri_key = 'sale_return_id';
	$tb_dt_source = 'erp_sale_return_dt';

	resetTrnGL($tb_main_source, $tb_main_source_pri_key, $main_id);




	$sql = "
		SELECT
			a." . $tb_main_source_pri_key . ",
			'erp_sale_return' as from_,
			a.doc_date as doc_date,
			IF( a.sale_return_spc = 'yes', a.chg_company_id, a.company_id ) as company_id,
			a.sale_order_total_before_vat as trn_amt,
			a.sale_order_vat_bath as trn_vat,
			a.sale_order_total_after_vat as trn_nprice,
			IF( a.sale_return_spc = 'yes', ( 100 + a.sale_return_chg_percent ) / 100, 1 ) as ratio,
			IF( a.sale_return_spc = 'yes', a.sale_return_chg_due, a.sale_return_due ) as ar_disc_cdue,
			c.company_name as note,
			a.company_id as company_cid,
			a.doc_no,
			a.company_branch_no as company_branch_no,
			a.sale_return_chg_due as ar_disc_cdelay,
			" . $_SESSION['user']->company_id . " as admin_company_id,
			a.sale_return_due as ar_disc_cdue,
			a.book_id,
			(
				SELECT
					COUNT( * )
				FROM erp_sale_return_dt
				WHERE " . $tb_main_source_pri_key . "  = a." . $tb_main_source_pri_key . "
			) as t
		FROM erp_sale_return a
		LEFT JOIN erp_company c ON a.company_id = c.company_id
		WHERE a." . $tb_main_source_pri_key . " = " . $main_id;

	$res = $dao->fetch($sql);

	if (!$res || $res->t == 0)
		return;

	//arr( $res );
	$data[$tb_main_source_pri_key] = $res->$tb_main_source_pri_key;
	$data['from_'] = $res->from_;
	$data['doc_date'] = $res->doc_date;
	$data['company_id'] = $res->company_id;
	$data['amt'] = $res->trn_amt * $res->ratio;
	$data['vat'] = $res->trn_vat * $res->ratio;
	$data['nprice'] = $res->trn_nprice * $res->ratio;
	$data['note'] = 'รับคืนขายจาก ' . $res->note;
	$data['company_cid'] = $res->company_cid;
	$data['doc_no'] = $res->doc_no;
	$data['company_branch_no'] = $res->company_branch_no;
	$data['ar_disc_cdelay'] = $res->ar_disc_cdelay;
	$data['admin_company_id'] = $res->admin_company_id;
	$data['ar_disc_cdue'] = $res->ar_disc_cdue;
	$data['due'] = $res->ar_disc_cdue;
	$data['book_id'] = $res->book_id;
	$data['edit_bt'] = 0;

	$from_id = $dao->insert($tb_main_source, $data);

	inportGlTrnDt($tb_main_source, 'disc_id', $gl_trn_jn_id = 4, $from_id, $data, $main_id);
}





//
// 79
function insertErpArTrn($main_id)
{


	$dao = getDb();

	$tb_main_source = 'erp_ar_trn';
	$tb_main_source_pri_key = 'sale_inv_id';
	$tb_dt_source = 'erp_sale_inv_dt';

	resetTrnGL($tb_main_source, $tb_main_source_pri_key, $main_id);




	$sql = "


		SELECT

			IF( a.sale_inv_spc = 'yes', ( 100 + a.sale_inv_chg_percent ) / 100, 1 ) as ratio,
			IF( a.sale_inv_spc = 'yes', a.sale_inv_chg_due, a.sale_inv_due ) as due,
			IF( a.sale_inv_spc = 'yes', a.chg_id, a.company_id ) as company_id,
			a.sale_order_total_before_vat as amt,
			a.sale_order_vat_bath as vat,
			a.sale_order_total_after_vat as nprice,
			a." . $tb_main_source_pri_key . ",
			c.company_name as note,
			b.doc_no as ord_no,
			a.company_id as company_cid,
			a.doc_no as doc_no,
			a.doc_date as doc_date,
			a.company_branch_no as company_branch_no,
			a.sale_inv_chg_due as ar_trn_cdelay,
			" . $_SESSION['user']->company_id . " as admin_company_id,
			a.sale_inv_due as ar_trn_cdue,
			a.admin_company_branch_id,
			a.admin_company_branch_no,
			a.book_id,
			(
				SELECT
					COUNT( * )
				FROM " . $tb_dt_source . "
				WHERE " . $tb_main_source_pri_key . "  = a." . $tb_main_source_pri_key . "
			) as t

		FROM erp_sale_inv a
		LEFT JOIN erp_sale_order b ON a.sale_order_id = b.sale_order_id
		LEFT JOIN erp_company c ON a.company_id = c.company_id
		WHERE a." . $tb_main_source_pri_key . " = " . $main_id;

	$res = $dao->fetch($sql);
	if (!$res || $res->t == 0)
		return;


	//arr( $res );
	$data[$tb_main_source_pri_key] = $res->$tb_main_source_pri_key;
	$data['due'] = $res->due;
	$data['company_id'] = $res->company_id;
	$data['amt'] = $res->amt * $res->ratio;
	$data['vat'] = $res->vat * $res->ratio;;
	$data['nprice'] = $res->nprice * $res->ratio;;
	$data['note'] = 'ขายสินค้าให้ ' . $res->note;
	$data['ord_no'] = $res->ord_no;
	$data['company_cid'] = $res->company_cid;
	$data['doc_no'] = $res->doc_no;
	$data['doc_date'] = $res->doc_date;
	$data['company_branch_no'] = $res->company_branch_no;
	$data['ar_trn_cdelay'] = $res->ar_trn_cdelay;
	$data['admin_company_id'] = $res->admin_company_id;
	$data['ar_trn_cdue'] = $res->ar_trn_cdue;
	$data['admin_company_branch_id'] = $res->admin_company_branch_id;
	$data['admin_company_branch_no'] = $res->admin_company_branch_no;
	$data['book_id'] = $res->book_id;
	$data['edit_bt'] = 0;


	$from_id = $dao->insert($tb_main_source, $data);

	inportGlTrnDt($tb_main_source, 'trn_id', $gl_trn_jn_id = 3, $from_id, $data, $main_id);
}


//
// 79
function insertErpApDisc($main_id)
{


	$dao = getDb();

	$tb_main_source = 'erp_ap_disc';
	$tb_main_source_pri_key = 'purchase_return_id';
	$tb_dt_source = 'erp_purchase_return_dt';

	resetTrnGL($tb_main_source, $tb_main_source_pri_key, $main_id);


	$sql = "
		SELECT
			a.book_id,
			a." . $tb_main_source_pri_key . ",

			a.company_id as company_id,
			a.sale_order_total_before_vat as amt,
			a.sale_order_vat_bath as vat,
			a.sale_order_total_after_vat as nprice,
			'erp_purchase_return' as from_,
			c.company_name,
			a.company_id as company_cid,
			a.doc_no as trn_no,
			a.doc_date as trn_date,
			a.company_branch_no as company_branch_no,
			" . $_SESSION['user']->company_id . " as admin_company_id,
			(

				SELECT
					COUNT( * )
				FROM erp_purchase_return_dt
				WHERE purchase_return_id = a.purchase_return_id

			) as t

		FROM erp_purchase_return a
		LEFT JOIN erp_company c ON a.company_id = c.company_id
		WHERE a." . $tb_main_source_pri_key . " = " . $main_id . "";

	$res = $dao->fetch($sql);


	//arr( $sql );

	if (!$res || $res->t == 0)
		return;

	$data[$tb_main_source_pri_key] = $res->$tb_main_source_pri_key;
	$data['from_'] = $res->from_;
	$data['company_id'] = $res->company_id;
	$data['amt'] = $res->amt;
	$data['vat'] = $res->vat;
	$data['nprice'] = $res->nprice;
	$data['note'] = 'คืนสินค้าให้' . $res->company_name;
	$data['doc_no'] = $res->trn_no;
	$data['doc_date'] = $res->trn_date;
	$data['company_branch_no'] = $res->company_branch_no;
	$data['admin_company_id'] = $res->admin_company_id;
	$data['book_id'] = $res->book_id;


	$from_id = $dao->insert($tb_main_source, $data);

	inportGlTrnDt($tb_main_source, 'disc_id', $gl_trn_jn_id = 4, $from_id, $data, $main_id);
}


//
//
/*
function updateSaleOrder( $main_id, $fGroup = 'sale_order_id', $sub_tb = 'erp_sale_order_dt', $main_tb = 'erp_sale_order', $sum_sale_order_dt_total = 'sale_order_dt_total' ) {

	$dao = getDb();

	$sql = "
		UPDATE
			". $main_tb ." a
		LEFT JOIN (
			SELECT
				". $fGroup .",
				ROUND( IFNULL( SUM( ". $sum_sale_order_dt_total ." ), 0 ), 2 ) as  sum_sale_order_dt_total
			FROM ". $sub_tb ."
			GROUP BY ". $fGroup ."
		) b ON a.". $fGroup ." = b.". $fGroup ."
		SET
			sale_order_total_before_vat = b.sum_sale_order_dt_total,
			sale_order_vat_bath = ROUND( sale_order_vat_rate / 100 * b.sum_sale_order_dt_total, 2 ),
			sale_order_total_after_vat = ROUND( b.sum_sale_order_dt_total + ROUND( sale_order_vat_rate / 100 * b.sum_sale_order_dt_total, 2 ), 2 )
		WHERE a.". $fGroup ." = " . $main_id;

	$dao->execDatas( $sql );
}

*/


//
//
function importErpStockDtFromPurchaseReturn($main_id, $action_type, $prefix = 'erp_sale_return')
{

	$dao = getDb();

	$sql = "
		DELETE
			FROM
		erp_stock
		WHERE from_ = '" . $prefix . "'
		AND sale_inv_id = " . $main_id;
	$dao->execDatas($sql);

	$sql = "
		DELETE
			FROM
		erp_stock_dt
		WHERE from_ = '" . $prefix . "'
		AND sale_inv_id = " . $main_id;
	$dao->execDatas($sql);

	$sql = "
		SELECT
			a.*,
			(
				SELECT
					COUNT( * )
				FROM " . $prefix . "_dt
				WHERE purchase_return_id = a.purchase_return_id
			) as t,
			c.stock_act_code
		FROM " . $prefix . " a
		LEFT JOIN erp_book b ON a.book_id = b.book_id
		LEFT JOIN erp_stock_act c ON b.stock_act_id = c.stock_act_id
		WHERE a.purchase_return_id = " . $main_id;

	$res = $dao->fetch($sql);

	if (!$res || $res->t == 0)
		return;

	$data_['sale_inv_id'] = $res->purchase_return_id;

	$data_['doc_no'] = $res->doc_no;

	$data_['doc_date'] = $res->doc_date;

	$data_['user_id'] = $_SESSION[Uid];

	$data_['admin_company_id'] = $_SESSION['user']->company_id;

	$data_['stock_act_code'] = $res->stock_act_code;

	$data_['from_'] = $prefix;

	$data_['warehouse_id'] = $res->warehouse_id;

	$data_['book_id'] = $res->book_id;

	$id = $dao->insert('erp_stock', $data_, $upDuplicate = false, $action = 'REPLACE');

	if (!empty($id)) {
		$sql = "

			INSERT INTO erp_stock_dt (
				from_,
				stock_id,
				sale_inv_id,
				product_dt_id,
				stock_dt_qty,
				zone_id,
				have_update,
				stock_dt_auto_calc,
				stock_dt_amt,
				stock_dt_cost

			)
			SELECT
				'" . $prefix . "',
				" . $id . ",
				" . $res->purchase_return_id . ",
				a.product_dt_id,
				SUM( a.purchase_inv_dt_qty_um ),
				a.zone_id,
				1,
				1,
				a.purchase_inv_dt_total,
				a.purchase_inv_dt_total / SUM( a.purchase_inv_dt_qty_um * a.product_um_rate )
			FROM " . $prefix . "_dt a
			WHERE a.purchase_return_id = " . $res->purchase_return_id . "
			GROUP BY a.product_dt_id, a.zone_id
		";

		$dao->execDatas($sql);
	}
}

//
//
function importErpStockDt_($main_id, $action_type, $prefix = 'erp_sale_return')
{

	$dao = getDb();

	$sql = "
		DELETE
			FROM
		erp_stock
		WHERE from_ = '" . $prefix . "'
		AND sale_inv_id = " . $main_id;
	$dao->execDatas($sql);

	$sql = "
		DELETE
			FROM
		erp_stock_dt
		WHERE from_ = '" . $prefix . "'
		AND sale_inv_id = " . $main_id;
	$dao->execDatas($sql);

	$sql = "
		SELECT
			a.*,
			(
				SELECT
					COUNT( * )
				FROM erp_sale_return_dt
				WHERE sale_return_id = a.sale_return_id
			) as t,
			c.stock_act_code
		FROM erp_sale_return a
		LEFT JOIN erp_book b ON a.book_id = b.book_id
		LEFT JOIN erp_stock_act c ON b.stock_act_id = c.stock_act_id
		WHERE a.sale_return_id = " . $main_id;

	$res = $dao->fetch($sql);

	if (!$res || $res->t == 0)
		return;

	$data_['sale_inv_id'] = $res->sale_return_id;

	$data_['doc_no'] = $res->doc_no;

	$data_['doc_date'] = $res->doc_date;

	$data_['user_id'] = $_SESSION[Uid];

	$data_['admin_company_id'] = $_SESSION['user']->company_id;

	$data_['stock_act_code'] = $res->stock_act_code;

	$data_['from_'] = $prefix;

	$data_['warehouse_id'] = $res->warehouse_id;

	$data_['book_id'] = $res->book_id;

	$id = $dao->insert('erp_stock', $data_, $upDuplicate = false, $action = 'REPLACE');

	if (!empty($id)) {
		$sql = "

			INSERT INTO erp_stock_dt (
				from_,
				stock_id,
				sale_inv_id,
				product_dt_id,
				qty,
				zone_id,
				have_update,
				stock_dt_auto_calc,
				stock_dt_amt,
				stock_dt_cost

			)
			SELECT
				'" . $prefix . "',
				" . $id . ",
				" . $res->sale_return_id . ",
				a.product_dt_id,
				SUM( a.sale_inv_dt_qty_um ),
				a.zone_id,
				1,
				1,
				a.sale_inv_dt_total,
				a.sale_inv_dt_total / SUM( a.sale_inv_dt_qty_um * a.product_um_rate )
			FROM erp_sale_return_dt a
			WHERE a.sale_return_id = " . $res->sale_return_id . "
			GROUP BY a.product_dt_id, a.zone_id
		";

		$dao->execDatas($sql);
	}
}


//
//
function importErpStockDt($main_id, $action_type, $data = array(), $prefix = 'inv')
{


	$dao = getDb();

	$prefix = 'erp_sale_inv';

	$sql = "
		DELETE
			FROM
		erp_stock
		WHERE from_ = '" . $prefix . "'
		AND sale_inv_id = " . $main_id;
	$dao->execDatas($sql);

	$sql = "
		DELETE
			FROM
		erp_stock_dt
		WHERE from_ = '" . $prefix . "'
		AND sale_inv_id = " . $main_id;
	$dao->execDatas($sql);

	$sql = "
		SELECT
			(
				SELECT
					COUNT( * )
				FROM erp_sale_inv_dt
				WHERE sale_inv_id = a.sale_inv_id
			) as t,
			a.*,
			c.stock_act_code
		FROM erp_sale_inv a
		LEFT JOIN erp_book b ON a.book_id = b.book_id
		LEFT JOIN erp_stock_act c ON b.stock_act_id = c.stock_act_id
		WHERE a.sale_inv_id = " . $main_id;

	$res = $dao->fetch($sql);


	//arr( $sql );
	if (!$res || $res->t == 0)
		return;


	$data_['sale_inv_id'] = $res->sale_inv_id;
	$data_['warehouse_id'] = $res->warehouse_id;

	$data_['doc_no'] = $res->doc_no;

	$data_['doc_date'] = $res->doc_date;

	$data_['user_id'] = $_SESSION[Uid];

	$data_['admin_company_id'] = $_SESSION['user']->company_id;

	$data_['stock_act_code'] = $res->stock_act_code;

	$data_['from_'] = $prefix;

	$id = $dao->insert('erp_stock', $data_, $upDuplicate = false, $action = 'REPLACE');


	if (!empty($id)) {
		$sql = "

			INSERT INTO erp_stock_dt (
				from_,
				stock_id,
				sale_inv_id,
				product_dt_id,
				qty,
				zone_id,
				have_update,
				stock_dt_auto_calc,
				stock_dt_amt,
				stock_dt_cost
			)
			SELECT
				'" . $prefix . "',
				" . $id . ",
				" . $res->sale_inv_id . ",
				a.product_dt_id,
				SUM( a.sale_inv_dt_qty_um * a.product_um_rate ),
				IF( a.zone_id = '', b.zone_id, a.zone_id ),
				1,
				0,
				a.sale_inv_dt_total,
				a.sale_inv_dt_total / SUM( a.sale_inv_dt_qty_um * a.product_um_rate )
			FROM erp_sale_inv_dt a
			LEFT JOIN erp_stock_temp_dt b ON a.stock_temp_dt_id = b.stock_temp_dt_id
			WHERE a.sale_inv_id = " . $res->sale_inv_id . "
			GROUP BY a.product_dt_id, b.zone_id

		";

		$dao->execDatas($sql);
	}
}


//
//
function importErpSaleInvDt_($main_id, $data)
{

	$dao = getDb();

	$sql = "
		INSERT INTO erp_sale_inv_dt (
			sale_inv_dt_qty_um,
			sale_inv_dt_qty,
			sale_inv_id,
			sale_order_dt_id,
			sale_inv_dt_total,
			product_dt_id,
			sale_inv_dt_price,
			sale_inv_dt_discount,
			product_um_rate,
			product_um_id,
			zone_id
		)

		SELECT
			0 as sale_inv_dt_qty_um,
			0 as sale_inv_dt_qty,
			" . $main_id . " as sale_inv_id,
			b.sale_order_dt_id,
			0 as sale_inv_dt_total,
			new_tb.product_dt_id,
			b.sale_order_dt_price as sale_inv_dt_price,
			b.sale_order_dt_discount as sale_inv_dt_discount,
			b.product_um_rate,
			b.product_um_id,
			new_tb.zone_id

		FROM (

			SELECT
				CONCAT( 'a', a.product_dt_id, a.zone_id ),
				a.product_dt_id,
				a.zone_id,
				SUM( IF( LEFT( b.stock_act_code, 1 ) = 0, a.qty, a.qty * -1 ) ) as qty
			FROM erp_stock_dt a
			LEFT JOIN erp_stock b ON a.stock_id = b.stock_id
			GROUP BY a.product_dt_id, a.zone_id

			UNION
			SELECT
				CONCAT( 'b', product_dt_id, zone_id ),
				product_dt_id,
				zone_id,
				IFNULL( SUM( sale_inv_dt_qty * -1 ), 0 )
			FROM erp_sale_inv_dt
			WHERE zone_id IS NOT NULL
			GROUP BY product_dt_id, zone_id
		) as new_tb

		LEFT JOIN erp_sale_order_dt b ON new_tb.product_dt_id = b.product_dt_id
		LEFT JOIN erp_sale_order c ON b.sale_order_id = c.sale_order_id
		WHERE c.sale_order_id = " . $data['sale_order_id'] . "
		AND b.sale_order_dt_qty > (
			SELECT IFNULL( SUM( sale_inv_dt_qty ), 0 )
			FROM erp_sale_inv_dt
			WHERE sale_order_dt_id = b.sale_order_dt_id
		)

		GROUP BY new_tb.product_dt_id, new_tb.zone_id, b.sale_order_dt_id
		HAVING SUM( new_tb.qty ) > 0
		AND new_tb.zone_id IN (
			SELECT
				zone_id
			FROM erp_zone
			WHERE warehouse_id = " . $data['warehouse_id'] . "
		)

	";

	//arr( $sql );
	$dao->execDatas($sql);
}


//
//94
function importErpStockTempDt($main_id, $data = array())
{

	$dao = getDb();

	$sql = "

		INSERT INTO erp_stock_temp_dt (
			qty,
			qty_um,
			sale_order_dt_id,
			stock_temp_id,
			product_dt_id,
			product_um_id,
			product_um_rate,
			zone_id
		)
		SELECT
			0,
			0,
			b.sale_order_dt_id,
			" . $main_id . " as stock_temp_id,
			new_tb.product_dt_id,
			b.product_um_id,
			b.product_um_rate,
			new_tb.zone_id

		FROM (

			SELECT
				CONCAT( 'a', a.product_dt_id, a.zone_id ),
				a.product_dt_id,
				a.zone_id,
				SUM( IF( LEFT( b.stock_act_code, 1 ) = 0, a.qty, a.qty * -1 ) ) as qty
			FROM erp_stock_dt a
			LEFT JOIN erp_stock b ON a.stock_id = b.stock_id
			GROUP BY a.product_dt_id, a.zone_id

			UNION
			SELECT
				CONCAT( 'b', product_dt_id, zone_id ),
				product_dt_id,
				zone_id,
				qty * -1
			FROM erp_stock_temp_dt

		) as new_tb

		LEFT JOIN erp_sale_order_dt b ON new_tb.product_dt_id = b.product_dt_id
		LEFT JOIN erp_sale_order c ON b.sale_order_id = c.sale_order_id
		WHERE c.sale_order_id = " . $data['sale_order_id'] . "
		AND b.sale_order_dt_qty > (
			SELECT IFNULL( SUM( stock_dt_qty_um ), 0 )
			FROM erp_stock_temp_dt
			WHERE sale_order_dt_id = b.sale_order_dt_id
		)



		GROUP BY new_tb.product_dt_id, new_tb.zone_id, b.sale_order_dt_id
		HAVING SUM( new_tb.qty ) > 0
		AND new_tb.zone_id IN (
            	SELECT
					zone_id
				FROM erp_zone
				WHERE warehouse_id = " . $data['warehouse_id'] . "
         	)
	";
	//arr( $data );
	//exit;
	$dao->execDatas($sql);
}




//
//
function importErpSaleInvDt($main_id, $stock_temp_id)
{

	$dao = getDb();

	$sql = "
		INSERT INTO erp_sale_inv_dt (
			sale_inv_dt_qty,
			sale_inv_dt_qty_um,
			sale_inv_dt_total,
			product_dt_id,
			sale_inv_id,
			stock_temp_dt_id,
			sale_inv_dt_price,
			sale_inv_dt_discount,
			product_um_rate,
			product_um_id,
			zone_id
		)

		SELECT
			( a.stock_dt_qty_um - IFNULL( SUM( b.sale_inv_dt_qty_um ), 0 ) ) * c.product_um_rate,
			( a.stock_dt_qty_um - IFNULL( SUM( b.sale_inv_dt_qty_um ), 0 ) ) as sale_inv_dt_qty_um,
			( a.stock_dt_qty_um - IFNULL( SUM( b.sale_inv_dt_qty_um ), 0 ) ) / c.sale_order_dt_qty_um * c.sale_order_dt_total,
			a.product_dt_id,
			" . $main_id . ",
			a.stock_temp_dt_id,
			c.sale_order_dt_price,
			c.sale_order_dt_discount,
			c.product_um_rate,
			c.product_um_id,
			a.zone_id
		FROM erp_stock_temp_dt a
		LEFT JOIN erp_sale_inv_dt b ON a.stock_temp_dt_id = b.stock_temp_dt_id
		LEFT JOIN erp_sale_order_dt c ON a.sale_order_dt_id = c.sale_order_dt_id
		WHERE a.stock_temp_id = " . $stock_temp_id . "
		AND a.zone_id IS NOT NULL
		GROUP BY a.stock_temp_dt_id
		HAVING sale_inv_dt_qty_um > 0
	";

	$dao->execDatas($sql);
}

function saveFdateDueDate($param)
{

	$dao = getDb();
	/*
	$sql = "
		SELECT 
			
			ADDDATE( NOW(), INTERVAL -2 month ) as t
	";
	
	arr( $dao->fetch( $sql ) );
	*/
	if ($param['action_type'] == 'edit') {
		//echo 'dsfadsfdfsa';
		$param['data']['doc_date'] = '1980-01-01';


		saveFDate($param);
	}
}

//
//
function InsertACost($param)
{

	$dao = getDb();

	$m = gettime_($param['data']['doc_date'], $index = 12);

	if ($param['action_type'] == 'add') {

		//
		$sql = "
			INSERT INTO aa_a_cost (  
				main_group, 
				sub_group, 
				parent_id, 
				doc_no,
				remark,
				doc_date,
				total_after_vat,
				admin_company_id,
				fix_pay_day
			)
			SELECT 
				main_group, 
				sub_group, 
				" . $param['parent_id'] . " as parent_id, 
				'doc_no" . $param['parent_id'] . "' as doc_no,
				remark,
				date_format( doc_date, '" . $m . "-%d' ) as doc_date,
				total_after_vat,
				1 as admin_company_id,
				fix_pay_day
			FROM aa_a_cost
			WHERE parent_id = " . $param['data']['copy_id'] . "
		";
		//arr( $sql );
		$dao->execDatas($sql);
	} else if ($param['action_type'] == 'edit') {

		//
		$sql = "
			UPDATE aa_a_cost 
			
			SET 
				doc_date = date_format( doc_date, '" . $m . "-%d' )
			
			WHERE parent_id = " . $param['parent_id'] . "
			
		";
		$dao->execDatas($sql);
	}
}


//
//
function updateErpArPay($param = array())
{

	$dao = getDb();

	$sql = "
		UPDATE erp_ap_pay a
		LEFT JOIN
		(
			SELECT
				a.id,
				IFNULL( (
					SELECT
						SUM( amt )
					FROM erp_ap_pay_trn
					WHERE parent_id = a.id
					AND stock_type IN ( 'si', 'pi', 'ap', 'ar', 'erp_purchase_inv', 'erp_sale_inv' )

				), 0 )
				+
				IFNULL( (
					SELECT
						SUM( amt )
					FROM erp_gl_trn_pay
					WHERE parent_id = a.id

				), 0 ) as trn,
				0 as trn_return,
				(
					SELECT
						IFNULL( SUM( amt ), 0 )
					FROM erp_ap_pay_trn
					WHERE parent_id = a.id
					AND stock_type IN ( 'sir', 'pir', 'CN', 'dp', 'dr', 'erp_purchase_return', 'erp_sale_return' )
				) as disc,
				(
					SELECT
						IFNULL( SUM( amt ), 0 )
					FROM erp_ap_pay_cheque
					WHERE parent_id = a.id
				) as cheque,
				(
					SELECT
						IFNULL( SUM( amt ), 0 )
					FROM erp_ap_pay_bank_account
					WHERE parent_id = a.id
				) as bank_account,
				(
					SELECT
						IFNULL( SUM( amt ), 0 )
					FROM erp_ap_pay_payment
					WHERE parent_id = a.id
				) as payment,
				(
					SELECT
						IFNULL( SUM( amt ), 0 )
					FROM erp_ap_pay_vat
					WHERE parent_id = a.id
				) as pay_vat,
				
				
				IFNULL( (
					SELECT
						SUM( amt )
					FROM erp_ap_pay_trn
					WHERE parent_id = a.id
					

				), 0 ) as total_erp_ap_pay_trn
				
			FROM erp_ap_pay a
		) b ON a.id = b.id
		SET
			a.trn = b.trn,
			a.disc = b.disc,
			a.cheque = b.cheque,
			a.bank_account = b.bank_account,
			a.payment = b.payment,
			a.doc_balanch = b.trn - b.disc - b.cheque - b.bank_account - b.payment - b.pay_vat,
			a.proove_bt = IF( ROUND( b.trn - b.disc - b.cheque - b.bank_account - b.payment - b.pay_vat, 2 ) = 0, 1, 0 ),
			a.pay_vat = b.pay_vat,
			a.trn_return = b.trn_return,
			a.total_erp_ap_pay_trn = b.total_erp_ap_pay_trn
		WHERE a.id = " . $param['parent_id'] . "
	";

	$dao->execDatas($sql);



	if (!isset($param['updateList'])) {
		////I//F( true ) {

		$sql = "
			SELECT 
				new_tb.* 
			FROM (
			
				SELECT 
					IF( 
						tr.stock_type IN (
							'si', 'ar', 'erp_sale_inv', 'erp_purchase_inv'
						),
						'pay_trn_ids',
						
						
						
						IF( 
							tr.stock_type IN (
								 'sir', 'CN', 'dr', 'erp_sale_return' ,  'pir', 'CN', 'dp', 'erp_purchase_return'
							),
							'pay_disc_ids',
							
							'fddfffddfdf'
						) 
					) 
					
					as update_file,
					
					tr.parent_id, 
					tr.lock_parent_id
				FROM erp_ap_pay_trn tr
				WHERE tr.stock_type
				IN (
					'si', 'ar', 'erp_sale_inv', 'erp_purchase_inv',
					
					
					 'sir', 'CN', 'dr', 'erp_sale_return' ,  'pir', 'CN', 'dp', 'erp_purchase_return'
				)
				AND tr.parent_id IN ( " . $param['parent_id'] . " ) OR 1 = 0
				
				
				
				UNION 
				
				
				SELECT 
					'pay_cheque_ids' as update_file,
					parent_id, 
					cheque_id as lock_parent_id
				FROM erp_ap_pay_cheque 
				WHERE parent_id IN ( " . $param['parent_id'] . " ) OR 1 = 0
			) as new_tb	
		";


		//arr( $sql );


		$keep = array();
		foreach ($dao->fetchAll($sql) as $ka => $va) {

			$keep[$va->parent_id][$va->update_file][$va->lock_parent_id] = $va;
		}

		//arr( $keep );

		$updates = array('pay_trn_ids', 'pay_disc_ids', 'pay_cheque_ids');
		$sqlUnion = array();
		foreach ($keep as $kp => $vp) {

			$selects = array();
			$sets = array();

			$selects[] = "" . $kp . " as id";
			foreach ($updates as $ku => $vu) {


				if (empty($vp[$vu])) {


					$selects[] = "'' as " . $vu . "";
				} else {

					$selects[] = "'" . implode(',', array_keys($vp[$vu])) . "' as " . $vu . "";
				}

				$sets[] = "a." . $vu . " = new_tb." . $vu . "";
			}


			$sqlUnion[] = "
				SELECT
					" . implode(', ', $selects) . "
			";

			if (count($sqlUnion) > 500) {

				$sql = "
				
					UPDATE erp_ap_pay a 
					INNER JOIN (
						" . implode(' UNION ', $sqlUnion) . "
					) new_tb ON a.id = new_tb.id
					SET 
						" . implode(', ', $sets) . "
					
				";


				$dao->execDatas($sql);

				$sqlUnion = array();
			}
		}

		if (count($sqlUnion) > 0) {

			$sql = "
			
				UPDATE erp_ap_pay a 
				INNER JOIN (
					" . implode(' UNION ', $sqlUnion) . "
				) new_tb ON a.id = new_tb.id
				SET 
					" . implode(', ', $sets) . "
				
			";


			$dao->execDatas($sql);

			$sqlUnion = array();
		}
	}







	//	arr( $sql );



}


//
//
function insertApPayTrn($param)
{

	$dao = getDb();


	//echo $param['data']['pay_trn_ids'];

	//echo '<br>';
	//echo $param['data']['pay_disc_ids'];

	$arr1 = explode(',', $param['data']['pay_trn_ids']);
	$arr2 = explode(',', $param['data']['pay_disc_ids']);


	$stids = array_merge($arr1, $arr2);

	//arr( $stids );


	$sql = "
		DELETE
		FROM erp_ap_pay_trn
		WHERE parent_id = " . $param['parent_id'] . "
		AND lock_parent_id NOT IN ( " . implode(', ', $stids) . " )
		AND ( 
		
			( stock_type NOT IN ( 'erp_purchase_return' )  AND  tbName = 'erp_ar_pay' )
			OR 
			
			( stock_type NOT IN ( 'erp_sale_return' )  AND  tbName = 'erp_ap_pay' )
		)

	";

	//erp_sale_return
	$dao->execDatas($sql);

	$sql = "
		DELETE
		FROM erp_ap_pay_cheque
		WHERE parent_id = " . $param['parent_id'] . "
		AND cheque_id NOT IN ( " . $param['data']['pay_cheque_ids'] . " )
	";
	//arr( $sql );
	$dao->execDatas($sql);


	//return;
	$sql = "

		INSERT INTO erp_ap_pay_trn (
			admin_company_id,
			doc_no,
			company_id,
			stock_type,
			total_after_vat,
			amt,
			doc_date,
			status,
			lock_parent_id,
			tbName,
			parent_id
		)
		SELECT
			1 as admin_company_id,
			'" . $param['data']['doc_no'] . "' as doc_no,
			'" . $param['data']['company_id'] . "' as company_id,
			IF( si.tbName IN( 'ap', 'erp_purchase_inv' ), 'erp_purchase_inv',

				IF( si.tbName IN( 'ar', 'erp_sale_inv' ), 'erp_sale_inv',

					IF( si.tbName IN( 'dp', 'erp_purchase_return' ), 'erp_purchase_return',


						IF( si.tbName IN( 'dr', 'erp_sale_return', 'promotion' ), 'erp_sale_return', '' )
					)
				)

			) as stock_type,
			si.total_after_vat,
			si.total_after_vat - IFNULL( (
				SELECT
					SUM( amt )
				FROM erp_ap_pay_trn
				WHERE lock_parent_id = si.id
			), 0 ) as amt,
			'" . $param['data']['doc_date'] . "' as doc_date,
			1 as status,
			si.id as lock_parent_id,
			'" . $param['data']['tbName'] . "' as tbName,
			" . $param['parent_id'] . " as parent_id
		FROM erp_stock si
		WHERE si.id IN ( " . implode(', ', $stids) . " )
		HAVING amt != 0


	";

	//arr( $sql );

	$dao->execDatas($sql);



	$sql = "



		INSERT INTO erp_ap_pay_cheque ( tbName, doc_date, parent_id, cheque_id, amt, admin_company_id )
		SELECT
			'" . $param['data']['tbName'] . "' as tbName,
			NOW() as doc_date,
			" . $param['parent_id'] . " as parent_id,
			si.id as cheque_id,
			si.nprice - IFNULL( (
				SELECT
					SUM( amt )
				FROM erp_ap_pay_cheque
				WHERE cheque_id = si.id
			), 0 ) as amt,
			1 as admin_company_id
		FROM erp_cheque si
		WHERE si.id IN ( " . $param['data']['pay_cheque_ids'] . " )
		HAVING amt != 0
	";

	//	arr( $sql );
	$dao->execDatas($sql);
}


//
//
function WIMG( $url ) {
	return base_url() . $url;
}


//
//
function genCond( $sql, $filters = array(), $condTxt = "WHERE", $replaceText = '[cond]' ) {

	$cond = '';
	if ( !empty( $filters ) ) {

		$cond = $condTxt . " " . implode( ' AND ', $filters );

	}

	return str_replace( $replaceText, $cond, $sql );
}





//
//
function removeMoneyComma( $val ) {

	$val = str_replace( ',', '', $val );

	$val = floatval( $val );

	return $val;
}

//
//
function getConfig( $config_id ) {
	
	$dao = getDb();
	
	$sql = "
		SELECT
			*
		FROM  admin_model_config
		WHERE  config_id = ". $config_id . "";
	$res = $dao->fetch( $sql );


	if ( $res ) {
		
		//arr( $res );

	//	$a = json_decode( stripcslashes( $res->config_detail ) );
		$a = json_decode( $res->config_detail );

		if ( empty( $a ) )
			return false;



		foreach( $a as $kc => $vc ) {
			
			//$a->$kc = stripcslashes( $a->$kc );
		}
		
		
		
		//arr( $a );

		$a->config_id = $res->config_id;
		$a->config_doc_head_id = $res->config_doc_head_id;

		$a->config_comment = $res->config_comment;

		//$a->config_table_head = $res->config_table_head;

		$a->database = $res->config_database;

		unset( $a->new_sort_key );

		return $a;

	}
	return false;
}



//
//
function createPage( $data, $countRow = true ) {


	$dao = getDb();

	$data['select'] = isset( $data['select'] )? $data['select']: '*';

	//
	//
	if ( $countRow ) {

		$data['total'] = $dao->getRowsCount( $data['sql'] );
		//$data['total'] = 9999;

		if( empty( $data['perPage'] ) ) {
			
			$data['perPage'] = 1;
			
			
		}
		
		$data['maxPage'] = ceil( $data['total'] / $data['perPage'] );

	} else {

		$data['total'] = NULL;
		$data['maxPage'] = NULL;
	}

	if ( empty( $data['page'] ) ) {

		if ( empty( $_REQUEST['page'] ) ) {

			$data['page'] = 1;
		}
		else if ( $_REQUEST['page'] == 'last' ) {

			if ( is_numeric( strpos( $data['sql'], '[LIMIT]' ) ) ) {

				$data['total'] = $dao->getRowsCount( str_replace( '[LIMIT]', '', $data['sql'] ) );
				
			}
			else {
				
				
				$data['total'] = $dao->getRowsCount( $data['sql'] );
			}
			

			$data['page'] = ceil( $data['total'] / $data['perPage'] );

		}
		else {
			$data['page'] = $_REQUEST['page'];
		}

	}

	$data['start'] = $data['perPage'] * ( $data['page'] - 1 );

	$data['sql_all'] = $data['sql'];


	if ( is_numeric( strpos( $data['sql'], '[LIMIT]' ) ) ) {
		
		$data['sql'] = str_replace( '[LIMIT]', " LIMIT ". $data['start'] .", " . $data['perPage'], $data['sql'] );
	}
	else {
		
		
		$data['sql'] .= " LIMIT ". $data['start'] .", " . $data['perPage'];
	}
	


///$data['sql'] = "dsfaafds";
	$data['rows'] = $dao->fetchAll( $data['sql'] );


 
	//unset( $data['sql'] );

	$data['message'] = 'Ready';

	return $data;
}
//
//
function label( $code ) {

	$data['bank_acn_id'] = 'รหัสบัญชีธนาคาร';
	$data['chq_out_no'] = 'เลขที่เช็คจ่าย';
	$data['pay_to'] = 'สั่งจ่าย';
	$data['acp_only'] = 'A/C Payee Only';
	$data['pay_date'] = 'วันที่จ่าย';
	$data['status_document_name'] = 'สถานะ';
	$data['customer_name'] = 'ชื่อลูกค้า';
	$data['customer_addr1'] = 'ที่อยู่';
	$data['customer_addr2'] = 'ที่อยู่ที่สอง';
	$data['customer_tel'] = 'เบอร์โทรลูกค้า';
	$data['customer_fax'] = 'เบอร์แฟกส์ลูกค้า';
	$data['shop_short_name'] = 'ชื่อย่อร้านค้า';
	$data['shop_name'] = 'ชื่อร้านค้า';
	$data['stock_products_name'] = 'รายการสินค้า';
	$data['stock_products_code'] = 'รหัสสินค้า';
	$data['stock_products_bal'] = 'คงเหลือ';
	$data['doc_no'] = 'เลขที่เอกสาร';
	$data['department_name'] = 'แผนก';
	$data['purchase_order_no'] = 'ใบสั่งซื้อเลขที่';
	$data['gl_code'] = 'เลขที่บัญชี';
	$data['gl_name'] = 'ชื่อบัญชี';
	$data['product_group_master_code'] = 'รหัสกลุ่มสินค้า (หลัก)';
	$data['product_group_master_name'] = 'ชื่อกลุ่มสินค้า (หลัก)';
	$data['product_group_second_code'] = 'รหัสกลุ่มสินค้า (รอง)';
	$data['product_group_second_name'] = 'ชื่อกลุ่มสินค้า (รอง)';
	$data['product_group_code'] = 'รหัสกลุ่มสินค้า';
	$data['product_group_name'] = 'ชื่อกลุ่มสินค้า';
	$data['act'] = 'สถานะใช้งาน';
	$data['product_group_color_code'] = 'รหัสกลุ่มสินค้า (สี)';
	$data['product_group_color_name'] = 'ชื่อกลุ่มสินค้า (สี)';
	$data['product_group_type_code'] = 'รหัสกลุ่มสินค้า (ประเภท)';
	$data['product_group_type_name'] = 'ชื่อกลุ่มสินค้า (ประเภท)';
	$data['product_group_size_code'] = 'รหัสกลุ่มสินค้า (ขนาด)';
	$data['product_group_size_name'] = 'ชื่อกลุ่มสินค้า (ขนาด)';
	$data['sale_order_book_code'] = 'เล่ม';
	$data['warehouse_name'] = 'คลัง';
	$data['sale_order_no'] = 'เลขที่ใบสั่งขาย';
	$data['sum_stock_dt_qty'] = 'คงเหลือ';
	$data['code'] = 'รหัส';
	$data['name'] = 'ชื่อ';
	$data['grade'] = 'เกรด';
	$data['color'] = 'สี';
	$data['purchase_receive_no'] = 'เลขที่ใบรับของ';
	$data['purchase_order_no'] = 'เลขที่ใบสั่งซื้อ';
	$data['havePriceChangeTitle'] = 'เปลี่ยนราคาสินค้า / อนุมัติ';
	$data['zone_avilable'] = 'ปริมาณในโซน';
	$data['total_qty'] = 'ปริมาณสต็อค';
	$data['total_net_so'] = 'ปริมาณค้างส่ง';
	$data['total_avi'] = 'ปริมาณขายได้';
	$data['product_color'] = 'รหัส : สินค้า / LOT (S/N)';
	$data['zone_name'] = 'โซน';
	$data['product_um_name'] = 'หน่วยนับ';
	$data['re_number'] = 'ใบรับสินค้าสั่งซื้อ';
	$data['purchase_order_no_'] = 'ใบสั่งซื้อ';
	$data['dor'] = 'เลขที่ใบส่งของ';
	$data['lock_doc'] = 'เอกสารถูกล็อคใช้งาน';
	
	

	//
	//
	if ( isset( $data[$code] ) )
		return $data[$code];

	return $code;
}
//
//
function getView( $model_id ) {


	$dao = getDb();

/*
	$sql = "
		SELECT
			a.*,
			b.new_config_id,
			b.model_id,
			b.model_alias
		FROM admin_user_page a
		LEFT JOIN admin_model b ON a.page_id = b.model_id
		WHERE a.user_id = ". $_SESSION[Uid] ."
		AND a.page_id = " . $model_id;

	$res = $dao->fetch( $sql );
*/

//arr( $_SESSION['u']->group_id );
	//if ( !$res ) {
		$sql = "
			SELECT
				a.*,
				b.new_config_id,
				b.model_id,
				b.model_alias
			FROM admin_group_page a
			LEFT JOIN admin_model b ON a.page_id = b.model_id
			WHERE a.page_id = ". $model_id ."
			AND group_id IN (
				". $_SESSION['u']->group_id ."
			)
		";
		$res = $dao->fetch( $sql );
//	}


	if ( !$res ) {
		$sql = "
			SELECT
				model_id,
				model_alias,
				model_title

			FROM admin_model
			WHERE model_id = ". $model_id ."
		";

		$res = $dao->fetch( $sql );
	}


	//$res->model_id = $model_id;

	return $res;

}

//
//
function getUserPages( $user_id = 0, $user_level = 0, $all = false, $filters = array() ) {

	global $dao;

	$cond = '';
	if ( !$all ) {

		if ( !empty( $user_id ) ) {

			$filters['WHERE'][] = "

				user_level != 0
				AND model_id IN (
					SELECT
						page_id
					FROM admin_user_page
					WHERE user_id = ". $user_id ."
					UNION

					SELECT
						126
					UNION

					SELECT
						page_id
					FROM admin_group_page
					WHERE group_id IN (
						SELECT
							group_id
						FROM admin_user_group
						WHERE user_id = ". $user_id ."

					)
				)

			";

		} else {

			$filters['WHERE'][] = "user_level = 0";

		}
	}

	if ( isset( $_REQUEST['model_hotkey'] ) ) {

		$filters['WHERE'][] = "model_id = '". $_REQUEST['model_hotkey'] ."' OR model_hotkey = '". $_REQUEST['model_hotkey'] ."'";
	}

	$filters['WHERE'][] = "front_page = 0";

	

	$sql = "
		SELECT
			*
		FROM admin_model
		[WHERE]
		ORDER BY model_order ASC

	";

	//
	$sql = genCond_( $sql, $filters );

	$keep = array();
	foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

		$keep[$va->model_alias] = $va;
	}

	return $keep;
}


//
//
function popup_form( $str_desc = NULL, $config_id = 11 ) {

	return 'dsaadssdfdf';


}

//
//
function updateApPayTrn( $param ) {

	$dao = getDb();

	$dates[] = $param['data']['doc_date'];

	if( isset( $param['main_data_before'] ) ) {

		$dates[] = $param['main_data_before']->doc_date;
	}
	
	$date = MIN( $dates );
	
	$sql = "
		SELECT
			trn.lock_parent_id,
		
			pay.doc_date,
			IF( pay.proove_bt = 0, 0, trn.amt ) as amt,
			IFNULL( si.total_after_vat, 0 ) as total_after_vat,
			IFNULL( si.commission_expect, 0 ) as commission_expect,
			trn.id,
			IF( pay.proove_bt = 0, 0, IFNULL( ( trn.amt / si.total_after_vat * si.commission_expect ), 0 ) ) as commission_paid,
			pay.company_id,
			trn.order_number
		FROM erp_ap_pay_trn trn
		LEFT JOIN erp_ap_pay pay ON trn.parent_id = pay.id
		LEFT JOIN (
			SELECT
				dt.parent_id,
				SUM( dt.after_vat ) as total_after_vat,
				SUM( dt.commission + dt.extra_commission ) as commission_expect
			FROM erp_stock_dt dt
			GROUP BY
				parent_id
		) as si ON trn.lock_parent_id = si.parent_id
		[WHERE]
		ORDER BY
			trn.lock_parent_id ASC,
			pay.doc_date ASC,
			trn.id ASC
	";

	$filters = array();
	//$filters['WHERE'][] = "trn.stock_id IN ( ". $stock_ids ." )";
	//$filters['test'] = "WHERE dt.parent_id IN ( ". $stock_ids ." )";
	
	$filters['WHERE'][] = "pay.doc_date >= '". $date ."'";
	
	$sql = genCond_( $sql, $filters );
 
 //arr( $sql );
	//
	//
	foreach( $dao->fetchAll( $sql )  as $ka => $va ) {
//arr( $va );

		$gName = $va->lock_parent_id;
		if( !isset( $balance[$gName] ) ) {

			$sql = "
				SELECT
					trn.order_number,
					trn.amt_bal,
					trn.commission_paid_balance
				FROM erp_ap_pay_trn trn
				LEFT JOIN erp_ap_pay pay ON trn.parent_id = pay.id
				[WHERE]
				ORDER BY
					trn.order_number DESC
				
				LIMIT 0, 1
			";

			$filters = array();
			$filters['WHERE'][] = "pay.doc_date < '". $date ."'";
			$filters['WHERE'][] = "trn.lock_parent_id = ". $gName;
			$sql = genCond_( $sql, $filters );

			$balance[$gName] = new stdClass;
			$balance[$gName]->order_number = 0;
			$balance[$gName]->amt_bal = 0;
			$balance[$gName]->commission_paid_balance = 0;

			foreach( $dao->fetchAll( $sql ) as $kb => $vb ) {

				$balance[$gName] = $vb;

			}
		}

		$balance[$gName]->commission_paid_balance += $va->commission_paid;

		$balance[$gName]->order_number += 1;

		$balance[$gName]->amt_bal += $va->amt;

		$commission_paid_diff = $va->commission_expect - $balance[$gName]->commission_paid_balance;

		$amt_diff = $va->total_after_vat - $balance[$gName]->amt_bal;

		$sqlUnion[] = "
			SELECT
				". $va->id ." as id,
				". $va->commission_expect ." as commission_expect,
				". $va->commission_paid ." as commission_paid,
				". $balance[$gName]->commission_paid_balance ." as commission_paid_balance,
				". $commission_paid_diff ." as commission_paid_diff,
				". $balance[$gName]->order_number ." as order_number,
				". $balance[$gName]->amt_bal ." as amt_bal,
				". $amt_diff ." as amt_diff,
				". $va->total_after_vat ." as total_after_vat,
				'". $va->doc_date ."' as doc_date,
				". $va->company_id ." as company_id
		";

		$updateIds[] = $va->id;
	}

	if( !empty(  $sqlUnion ) ) {

		$sql = "
			UPDATE erp_ap_pay_trn trn
			INNER JOIN (
			". implode( ' UNION ', $sqlUnion  ) ."

			) as new_tb ON trn.id = new_tb.id
			SET
				trn.commission_expect = new_tb.commission_expect,
				trn.commission_paid = new_tb.commission_paid,
				trn.commission_paid_balance = new_tb.commission_paid_balance,
				trn.commission_paid_diff = new_tb.commission_paid_diff,
				trn.order_number = new_tb.order_number,
				trn.amt_bal = new_tb.amt_bal,
				trn.amt_diff = new_tb.amt_diff, 
				trn.total_after_vat = new_tb.total_after_vat,
				trn.doc_date = new_tb.doc_date,
				trn.company_id = new_tb.company_id
			WHERE trn.id IN ( ". implode( ', ', $updateIds ) ." )
		";
		
	//	arr( $sql );

		$dao->execDatas( $sql );
	}
}





//
//
function getStrPercent( $txt, $def_val = '0%' ) {

	if ( empty( $txt ) )
		return $def_val;

	$txt = trim( $txt );

	$concat = '%';

	if ( strpos( $txt, '+' ) === FALSE &&  substr( $txt, -1 ) != '%' ) {
		$concat = '';
	}

	$txt = str_replace( array( ' ', '%' ), '', $txt );

	//
	//Cut from start each 1 chalacter
	while( substr( $txt, 0, 1 ) == '+' ) {
		$txt = substr( $txt, 1 );
	}

	//
	// Cut from end each 1 chalacter
	while( in_array( substr( $txt, -1 ), array( '+', '.' ) ) ) {

		$txt = substr( $txt, 0, -1 );

	}

	$not_allow = array (
		'..', '++', '.+'
	);

	foreach ( $not_allow as $v ) {
		if ( strpos( $txt, $v ) !== FALSE ) {
			return $def_val;
		}
	}

	for( $i = 0; $i < strlen( $txt ); ++$i ) {

		$substr = substr( $txt, $i, 1 );

		if ( is_numeric( $substr ) || in_array( $substr, array( '+', '.' ) ) )
			continue;

		return $def_val;
	}

	$txt = str_replace( array( '%' ), '', $txt );

	$txt = $txt . $concat;

	return $txt;

}


//$param['doc_date'] = '2021-01-11';
//$param['product_id'] = 8848;
//$param['g'] = 0, 1
function getNowAveCost( $param = array() ) {
	 
	$dao = getDb();

	$doc_date = $param['doc_date'];

	$sql = "
		SELECT
			( SUM( new_tb.yogma_amt ) + SUM( new_tb.in_month_amt ) ) / (  SUM( new_tb.yogma_qty ) + SUM( new_tb.in_month_qty ) )  as now_ave_cost
		FROM (
			(
				SELECT 
					0 as in_month_qty,
					0 as in_month_amt,
					dt.[qty_bal] as yogma_qty,
					dt.[amt_bal] as yogma_amt
				FROM erp_stock_dt dt 
				WHERE dt.doc_date < date_format( '". $doc_date ."', '%Y-%m-01' )   
				AND dt.product_id = ". $param['product_id'] ."
				[admincom]
				ORDER BY 
					dt.order_number desc 
				LIMIT 0, 1 
			)
			UNION
			(
				SELECT 
					IFNULL( SUM( ( dt.qty * dt.factor ) ), 0 ) as in_month_qty,
					IFNULL( SUM( ( dt.[cost_amt]  * dt.factor ) ), 0 )  as in_month_amt,
					0 as yogma_qty,
					0 as yogma_amt
				FROM erp_stock_dt dt 
				WHERE LAST_DAY( dt.doc_date ) = LAST_DAY( '". $doc_date ."' )
				AND dt.product_id = ". $param['product_id'] ."
				[admincom]
				AND dt.act_id IN (
					SELECT 
						stock_act_id
					FROM erp_stock_act
					WHERE tbName != ''
					AND auto_cal_cost = 0
				) 
				[mix_trade_filter]
				AND cost_adj = 0
			)
		
		) as new_tb	
	";
	
	//
	if( !empty( $param['trade_mix'] ) ) {
		
		$filters['mix_trade_filter'] = "AND dt.tbName NOT IN ( 'mix_in', 'trade_in', 'mix_out', 'trade_out', 'produce_in' )";
	}
	else {
		$filters['mix_trade_filter'] = "AND dt.tbName NOT IN ( 'mix_out', 'trade_out'  )";
	}
	
	
	if( !empty( $param['g'] ) ) {
		
		$filters['admincom'] = " AND dt.admin_gcompany_id = 1";
		$filters['admincomGetBal'] = "AND admin_gcompany_id = dt.admin_gcompany_id";
		$filters['qty_bal'] = "g_qty_bal";
		$filters['amt_bal'] = "g_amt_bal";
		$filters['cost_amt'] = "g_cost_amt";
		
		
	}
	else {
		
		$filters['admincom'] = "AND dt.admin_company_id = 1";
		$filters['admincomGetBal'] = "AND admin_company_id = dt.admin_company_id";
		$filters['qty_bal'] = "qty_bal";
		$filters['amt_bal'] = "amt_bal";
		$filters['cost_amt'] = "cost_amt";
	}
	
		
	$sql = genCond_( $sql, $filters );

//arr( $sql );
//
if( $param['product_id'] == 15091 ) {
//
//arr( $sql );
 

}

	foreach(  $dao->fetchAll( $sql ) as $ka => $va ) {
	
		if( $va->now_ave_cost != 0 ) {
			
			return $va->now_ave_cost;
		}
		
	}
	
	
	
	///ai in month 
	
	/// qty yogma != 0
	
	$sql = "
		SELECT 
			GROUP_CONCAT( distinct act_id ) as act_ids 
		FROM erp_stock_dt dt 
		WHERE LAST_DAY( dt.doc_date ) = LAST_DAY( '". $doc_date ."' )
		AND dt.product_id = ". $param['product_id'] ."
		AND factor = 1
		[admincom]
		HAVING act_ids = 5
				
	";
	$filters = array();
	if( !empty( $param['g'] ) ) {
		$filters['admincom'] = "";
		
	}
	else {
		$filters['admincom'] = "AND dt.admin_company_id = 1";
		
	}
	
	$sql = genCond_( $sql, $filters );
	
	
	//
	
	foreach( $dao->fetchAll( $sql )  as $ka => $va ) {

		return 0;
	}
	
	
	$sql = "
		SELECT 
			dt.[qty_bal] as yogma_qty
		FROM erp_stock_dt dt 
		WHERE dt.doc_date < date_format( '". $doc_date ."', '%Y-%m-01' )   
		AND dt.product_id = ". $param['product_id'] ."
		[admincom]
		
		ORDER BY 
			dt.order_number desc 
		LIMIT 0, 1 
	";
	//HAVING yogma_qty != 0
	
	$filters = array();
	if( !empty( $param['g'] ) ) {
		
		$filters['admincom'] = "";
		$filters['qty_bal'] = "g_qty_bal";
	}
	else {
		
		$filters['admincom'] = "AND dt.admin_company_id = 1";
		$filters['qty_bal'] = "qty_bal";
	}
	
	$sql = genCond_( $sql, $filters );
	
		//
if( $param['product_id'] == 13875 ) {
//
//arr( $sql );
 
//exit;
}


	//arr( $sql );
	foreach( $dao->fetchAll( $sql )  as $ka => $va ) {
		
		if( $va->yogma_qty != 0  ) {
			
			return 0;
		}
		
	}
	

		
	if( !empty( $param['g'] ) ) {
		
		$admin_company_id = 0;
		
	}
	else {
		$admin_company_id = 1;
	}

	$sql = "
		SELECT 
			cost_ave
		FROM cost_month_raw_ave
		WHERE admin_company_id = ". $admin_company_id ."
		AND product_id = ". $param['product_id'] ."
		AND last_update < '". $doc_date ."'
		AND cost_ave != 0 
		ORDER BY last_update DESC
		LIMIT 0, 1
	";


	$res = $dao->fetch( $sql );
	
	if( $res ) {
		
		return $res->cost_ave;
	}

	
	return 0;
	
}




//
//
function getSiDetil( $param ) {
	//arr( $param['parent_id'] );
	//return;
	//arr( $param['data']['rows'][0]->lock_parent_id );
	$dao = getDb();
		
	$sql = "
		SELECT
			dt.qty,
			dt.id,
			dt.doc_no,
			dt.doc_date,
			CONCAT( p.product_code, ' ', p.product_name ) as product,
		 
			p.product_grade,
			z.zone_name,
			dt.sqm,
			dt.price,
			dt.before_vat,
			dt.qty_um,
			CONCAT( '<input autocomplete=\"off\" onkeyup=\"javascript:controlnumbers( this, fm_numeric );\" type=\"text\" name=\"qty_return[', dt.id ,']\" value=\"0\">' ) as qty_return,
			IFNULL( ( SELECT SUM( qty_um ) FROM erp_stock_dt WHERE lock_dt_id = dt.id ), 0 ) as ready_return,
			dt.um_label
		FROM erp_stock_dt dt
		LEFT JOIN erp_zone z ON dt.zone_id = z.zone_id
		LEFT JOIN erp_product p ON dt.product_id = p.product_id
		[WHERE]
		HAVING qty_um > ready_return
	";

//
	$date = $param['data']['rows'][0]->doc_date;
	

	
	$filters = array();
	$filters['WHERE'][] = "dt.parent_id = ". $param['data']['rows'][0]->lock_parent_id ."";
	
	$sql = genCond_( $sql, $filters );
	 
	$config['um_label'] = array( 'a' => 'L', 'label' => 'หน่วย' );
	$config['pczgc'] = array( 'a' => 'L' );
	$config['qty'] = array( 'a' => 'R' );
	$config['pd'] = array( 'a' => 'L' );
	$config['qty_um'] = array( 'a' => 'R', 'label' => 'จำนวนขาย' );
	$config['g_qty_bal'] = array( 'a' => 'R' );
	$config['g_cost_bal'] = array( 'a' => 'R' );
	$config['send'] = array( 'a' => 'R' );
	$config['qty_return'] = array( 'a' => 'R', 'label' => 'กรอกจำนวนรับคืน' );
	$config['amt_bal'] = array( 'a' => 'R' );
	$config['cost_bal'] = array( 'a' => 'R' );
	$config['cost_amt'] = array( 'a' => 'R' );
	$config['product'] = array( 'a' => 'L' );
	$config['qty_bal'] = array( 'a' => 'R' );
	$config['ready_return'] = array( 'a' => 'R', 'label' => 'คืนแล้ว' );
	
	
	$tables = getTable____( $dao->fetchAll( $sql ), $config );
	//$tables   = '';
	if( $tables  ) {
		
		return '
			<form action="'. setLink( 'ajax/insertSaleReturn' ) .'" enctype="multipart/form-data">
				<input type="hidden" name="parent_id" value="'. $param['parent_id'] .'">
				<div style="margin: 10px; ">
					<div class="po-re pd-10-bd" style="padding: 0; border: none;">
						<center><b class="red">**กรอกรายการสินค้าที่รับคืน**</b></center>
					</div>
				</div>

				'. $tables .'
				<br>
				<div class="clear-fix">
					<div class="fr"><input class="web-bt" type="submit" value="ยืนยันการบันทึกรายการรับคืน" /></div>
				</div>
				
			</form>
			
			<br><br>
		
		';

	}
	

}


//
//
function getTable____( $datas, $config = array() ) {
	
	
	//arr( $config );
	//exit;
	if( empty( $datas ) ) {
		
		return '';
	}
	
	$keep = array();
	foreach( $config as $kc => $vc ) {
		$keep[$kc] = convertObJectToArray( $vc );
		
	}
	
	
	$config = $keep;
	
	
	$r = 0;
	foreach( $datas as $kg => $vg ) {
		
		if( $r == 0 ) {
			$tds = array();
			$tds[] = '<th>No.</th>';
			foreach( $vg as $kt => $vt ) {
				
				if( isset( $config[$kt]['label'] ) ) {
					$tds[] = '<th>'. $config[$kt]['label'] .'</th>';
					
				}
				else {
					
					$tds[] = '<th>'. ( $kt ) .'</th>';
				}
			}
			
			$trHead = '<tr>'. implode( '', $tds ) .'</tr>';
			
			
			$trs[] = $trHead;
		}
		
		$tds = array();
		$tds[] = '<td class="">'. ( $r + 1  ) .'</td>';
		foreach( $vg as $kt => $vt ) {
			
			if( isset( $config[$kt]['a'] ) ) {
				$tds[] = '<td class="'. $config[$kt]['a'] .'">'. $vt .'</td>';
				
			}
			else {
				
				$tds[] = '<td class="C">'. $vt .'</td>';
			}
		
			
		}
		
		$trs[] = '<tr>'. implode( '', $tds ) .'</tr>';
		
		++$r;
	}
	
	$trs[] = $trHead;
	
	if( empty( $trs ) ) 
		return false;
	
	return  '<table class="flexme3">'. implode( '', $trs ) .'</table>';
}



  


function asdfdsfdfgagaddf( $param ) {

	global $dao;
	
	$dynamicColumnsW = $param['dynamicColumnsW'];
	//arr( $_REQUEST);
	
	$sql = "
		SELECT 
			dt.plan_month as label 
		FROM siamart_production_plan_dt dt
		[WHERE]
		GROUP by 
			label
		ORDER BY new_plan_month ASC	
	";
	
	//$replace = array();
//	if( !empty( $_REQUEST['id'] ) ) 
	//	$replace['WHERE'][] = " dt.parent_id  IN ( ". implode( ',', $_REQUEST['id'] ) ." )";
	
	$sql = genCond_( $sql, $param['filters'] );
	//arr( $sql );
	
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		$keep[] = $va->label;
		
		$cName = 'c'. $ka .'';
		
	
		$newColumns['dynamicColumnsSql'][] = "SUM( plan_qty * ( plan_month = '". $va->label ."' ) ) as ". $cName ."";
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $va->label );
	
		$newColumns['dynamicColumnsConfig'][$cName]->w = 15;
	}
	
	$cName = 'total_plan_qty';
	

	$newColumns['dynamicColumnsSql'][] = "SUM( plan_qty * ( plan_month IN( '". implode( "', '", $keep ) ."' )  ) ) as ". $cName ."";
	
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม' );

	$newColumns['dynamicColumnsConfig'][$cName]->w = 15;
	
	
	//$newColumns['dynamicColumns'] = 6;
	
	
	//arr($newColumns );

	return $newColumns;
}





function productionPlanColumns( $param ) {
	
	global $dao;
	
	$sql = "
		SELECT 
			plan_month   
		FROM siamart_production_plan_dt
		
		GROUP BY 
			plan_month
		ORDER BY
			new_plan_month ASC
	";
	
	$filters = array();
	if( !empty( $param['filters'] ) ) {
		
		$sql = gencond_( $sql, $param['filters'] );
		
	}
	
	$i = 0;
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
		
		$cName = 'c'. $kc .'';
		
		$label = $vc->plan_month;
		
		$newColumns['dynamicColumnsSql'][] = "SUM( plan_qty * ( plan_month = '". $vc->plan_month ."' ) ) as ". $cName;
	
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
		
		$newColumns['dynamicColumnsConfig'][$cName]->w = 18;
	
	
	
		
	}
	
	return $newColumns;
}


//
//
function tileStockColums( $param ) {
	
	if( true ) {
		$arr = array();
		$arr['sqm_active'] = array( 'label' => 'Active' );
		$arr['sqm_non_active'] = array( 'label' => 'non-Active' );
		$arr['sqm_sed'] = array( 'label' => 'เศษ' );
		$arr['sqm_total'] = array( 'label' => 'รวม' );
		$w = 15;
		foreach( $arr as $ka => $va ) {
			
			$cName = $ka;
			
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff ) as ". $cName ."
			";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'ตรม.|'. $va['label'] .'', $w, $forum_on_ready = NULL, $dot = 0  );
		}
	
	
	}
	if( true ) {
		
		$w = 21;
		$arr = array();
		$arr['amt_active'] = array( 'label' => 'Active' );
		$arr['amt_non_active'] = array( 'label' => 'non-Active' );
		$arr['amt_sed'] = array( 'label' => 'เศษ' );
		$arr['amt_total'] = array( 'label' => 'รวม' );
		
		foreach( $arr as $ka => $va ) {
			
			
			$cName = $ka;
			
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff ) as ". $cName ."
			";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'จำนวนเงิน|'. $va['label'] .'', $w, $forum_on_ready = NULL, $dot = 2  );
		}
		
	}
	
	
	
	
	return $newColumns;
	
	
	//arr( $_REQUEST);
	$setT = 60;
	$setTN = 60 - 5;

	global $dao;
	
	//
	$param['due_date'] = $_REQUEST['real_get'][0][0];
	$param['real_get'] = $_REQUEST['real_get'][0][0];
	$param['real_pay'] = $_REQUEST['real_pay'][0][1];
	$param['report_date'] = $_REQUEST['report_date'][0][0];
	$param['fdatePlus'] = isset( $_REQUEST['defFdatePlus'] )? $_REQUEST['defFdatePlus']: 7;
	
	
	//real_pay[0][1]
	//arr( $param );
	//$param['data']['doc_date'] = '2021-04-01';
	saveFDateReport( $param );
	

	
	
	$start = $_REQUEST['showOndate'][0][0];
	
	$end = $_REQUEST['showOndate'][0][1];
	
	$reportTime = isset( $_REQUEST['reportTime'] )? $_REQUEST['reportTime']: 'day';
	
	$keepC = array();
	
	
	if( isset( $param['time_status'] ) ) {
		
		
		
		$cName = 'time_status';
		
		if( $reportTime == 'month' ) {
			$newColumns['dynamicColumnsSql'][] = "
			
			
				CASE 
					WHEN new_tb.factor = 1 THEN '-'
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) ) THEN '> ". $setT ."'
					
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( '". $start ."', '%Y-%m-01' ) ) THEN '< ". $setT ."'
					
					
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date >= '". $end ."' ) THEN 'หลัง'
					
					WHEN new_tb.status = 'bad' THEN 'มีปัญหา'
					ELSE date_format( f_date, '%m/%y' )
				END as ". $cName ."
			
			";
		}
		else {
			
			$newColumns['dynamicColumnsSql'][] = "
			
			
				CASE 
					WHEN new_tb.factor = 1 THEN '-'
					
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( ADDDATE( '". $start ."', INTERVAL -". $setT ." day ), '%Y-%m-%d' ) ) THEN '> ". $setT ."'
					
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( '". $start ."', '%Y-%m-%d' ) ) THEN '< ". $setT ."'
					
					
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date >= '". $end ."' ) THEN 'หลัง'
					
					WHEN new_tb.status = 'bad' THEN 'มีปัญหา'
					ELSE date_format( f_date, '%m/%y' )
				END as ". $cName ."
			
			";
		}
		
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label = 'สถานะ', $w = 18, $forum_on_ready = NULL, $dot = 2, $inputformat = '', 'C', 0 );
	}
	
	

	
	$cName = 'bad';
	$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'bad' ) ) as ". $cName ."
	";
	
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'มีปัญหา', $w = 17, $forum_on_ready = NULL, $dot = 2  );

		
	if( true ) {
		$cName = 'before_show_bad';
		
		if( $reportTime == 'month' ) {
			
			$newColumns['dynamicColumnsSql'][] = "SUM( 
			
				new_tb.diff 
				
				* 
				
				( new_tb.status = 'good' ) 
				
				
				* 
				
				( new_tb.f_date < date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) ) 
			
			) as ". $cName ."
			";
		}
		else {
			
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * ( new_tb.f_date < ADDDATE( '". $start ."', INTERVAL -". $setT ." day ) ) ) as ". $cName ."
			";
		}
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'ก่อน|นานกว่า'. $setT .'', $w = 21, $forum_on_ready = NULL, $dot = 2  );
		
	}	
	
	$cName = 'before_show';
	
	if( $reportTime == 'month' ) {
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * 
		
		( new_tb.f_date >= date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) AND   new_tb.f_date < date_format( '". $start ."', '%Y-%m-01' ) ) 
		
		) as ". $cName ."
		";
	}
	else {
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * 
		
		( new_tb.f_date >= ADDDATE( '". $start ."', INTERVAL -". $setT ." day ) 
		
		AND 
		
		new_tb.f_date < '". $start ."' ) ) as ". $cName ."
		";
		
	}
	

	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'ก่อน|ใน'. $setT .'', $w = 21, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1 );
		
	$curentD = $start;
	for( $i = 1; $i <= 100; ++$i ) {
		
		$cName = 'c_'. $i .'';
		
		if( $reportTime == 'month' ) {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%m/%y' ) as label,
					LAST_DAY( '". $curentD ."' ) as curentD,
					ADDDATE( LAST_DAY( '". $curentD ."' ), INTERVAL 1 day ) as nextD
					
			";
			
			$res = $dao->fetch( $sql );
			
			$newColumns['dynamicColumnsSql'][] = "SUM( 
				new_tb.diff 
				* 
				(  new_tb.f_date >= date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) )
				* 
				( new_tb.status = 'good' ) * ( LAST_DAY( new_tb.f_date ) = '". $res->curentD ."' ) ) as ". $cName ."
				";
				
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label, $w = 19, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = 'green' );
		}
		else {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%d/%m/%y' ) as label,
					date_format( '". $curentD ."', '%Y-%m-%d' ) as curentD,
					ADDDATE( '". $curentD ."', INTERVAL 1 day ) as nextD	
			";
			
			$res = $dao->fetch( $sql );
		
			/*
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * ( new_tb.f_date = '". $res->curentD ."' ) ) as ". $cName ."
			";
			*/
			/*
		
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * ( IF( new_tb.f_date > '". $param['due_date'] ."', new_tb.f_date, new_tb.f_date_report ) = '". $res->curentD ."' ) ) as ". $cName ."
			";
			*/
			$newColumns['dynamicColumnsSql'][] = "
			
			SUM( 
				new_tb.diff 
				* 
				(  new_tb.f_date >= date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) )
				* 
				( new_tb.status = 'good' ) 
				* 
				( 
					new_tb.f_date = '". $res->curentD ."' 
				) 
				
			
			) as ". $cName ."
			";
			
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label, $w = 19, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1  );
		}//new_tb.f_date_report
		
		if( $res->nextD > $end ) {
			
			break;
		}
		
		
		$curentD = $res->nextD;
	}
	
	$w = 20;
	
	if( true ) {
		
		$cName = 'after_show';
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * ( new_tb.f_date >= '". $res->nextD ."' ) ) as ". $cName ."
		";
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'หลัง', $w, $forum_on_ready = NULL, $dot = 2  );
	}
	
	
	if( true ) {
		
		$cName = 'total';
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff ) as ". $cName ."
		";
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม', $w, $forum_on_ready = NULL, $dot = 2  );
	}
	
	return $newColumns;
		
}



//
//
function bpBrColums( $param ) {
	
	global $dao;
	
	$start = $_REQUEST['showOndate'][0][0];
	
	$end = $_REQUEST['showOndate'][0][1];
	
	$reportTime = isset( $_REQUEST['reportTime'] )? $_REQUEST['reportTime']: 'day';
	//$reportTime = 'month';
	
	$keepC = array();
	
	$curentD = $start;
	for( $i = 1; $i <= 100; ++$i ) {
		
		
		if( $reportTime == 'month' ) {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%m/%y' ) as label,
					LAST_DAY( '". $curentD ."' ) as curentD,
					ADDDATE( LAST_DAY( '". $curentD ."' ), INTERVAL 1 day ) as nextD
					
			";
			
			$res = $dao->fetch( $sql );
			

			$cName = 'c_e'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( in_expect = 1 AND LAST_DAY( new_tb.doc_date ) = '". $res->curentD ."'  ) ) as ". $cName ."
				";
			
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label.'|ก่อนเดือน', $w = 19, $forum_on_ready = NULL, $dot = 2  );
			
			
			
			$cName = 'c_'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( in_expect = 0 AND LAST_DAY( new_tb.doc_date ) = '". $res->curentD ."'  ) ) as ". $cName ."
				";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label.'|ในเดือน', $w = 19, $forum_on_ready = NULL, $dot = 2 );
				
		}
		else {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%d/%m/%y' ) as label,
					date_format( '". $curentD ."', '%Y-%m-%d' ) as curentD,
					ADDDATE( '". $curentD ."', INTERVAL 1 day ) as nextD	
			";
			
			$res = $dao->fetch( $sql );
			
			
			$cName = 'c_'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.doc_date = '". $res->curentD ."' ) ) as ". $cName ."
				";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label, $w = 19, $forum_on_ready = NULL, $dot = 2  );
				
		}
		
		if( $res->nextD > $end ) {
			
			break;
		}
		
		
		$curentD = $res->nextD;
	}
	
	$w = 20;
	
	
	if( true ) {
		
		$cName = 'total';
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff ) as ". $cName ."
		";
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม', $w, $forum_on_ready = NULL, $dot = 2  );
	}
	
	return $newColumns;
		
}

//
//
function sixMonthBefore( $param ) {
	
	global $dao;
	
	//arr( $_REQUEST['days'] );
	
	sort($_REQUEST['days']);
	
	//arr( $_REQUEST['days'] );
	
	$curentD = $_REQUEST['expect_date'][0][0];
	
	for( $m = 6; $m > 0; --$m ) {
		
		$sql = "
			SELECT
				date_format( '". $curentD ."', '%m/%Y' ) as my_,
				ADDDATE( '". $curentD ."', INTERVAL -1 month ) as next_d_,
				date_format( ADDDATE( '". $curentD ."', INTERVAL -". ( $m - 1 ) ." month ), '%m/%Y' ) as my,
				ADDDATE( '". $curentD ."', INTERVAL -". $m ." month ) as next_d
		";
		
		$res = $dao->fetch( $sql );
		
		if( isset( $_REQUEST['show_qty'] ) && $_REQUEST['show_qty'] == 1 ) {
			
			$cName = 'qty'. $m .'';
			$label = 'จำนวน (แผ่น)|'.$res->my.'';
			$sumSql = "SUM( 
			
				dt.qty 
			
				* 
				
				( date_format( dt.doc_date, '%m/%Y' ) = '". $res->my ."' ) 
				* 
				
				( DATE_FORMAT( dt.doc_date, '%d' ) >= ". $_REQUEST['days'][0] ." ) 
				
				* 
				
				( DATE_FORMAT( dt.doc_date, '%d' ) <= ". $_REQUEST['days'][1] ." ) 
			
			)";
			$keep['qty'][] = $sumSql;
			$newColumns['dynamicColumnsSql'][] = "". $sumSql ." as ". $cName ."";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 0  );
			$newColumns['dynamicColumnsConfig'][$cName]->w = 17;
		}
		else {
			
			$cName = 'sqm'. $m .'';
			$label = 'จำนวน (sqm)|'.$res->my.'';
			
			$sumSql = "SUM( 
				p.sqm_per_1 * dt.qty 
				
				* 
				
				( date_format( dt.doc_date, '%m/%Y' ) = '". $res->my ."' )  
				
				* 
				
				( DATE_FORMAT( dt.doc_date, '%d' ) >= ". $_REQUEST['days'][0] ." ) 
				
				* 
				
				( DATE_FORMAT( dt.doc_date, '%d' ) <= ". $_REQUEST['days'][1] ." ) 
				
			)";
			$keep['sqm'][] = $sumSql;
			$newColumns['dynamicColumnsSql'][] = "". $sumSql ." as ". $cName ."";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2 );
			$newColumns['dynamicColumnsConfig'][$cName]->w = 17;
		}
	}
	
	if( isset( $_REQUEST['show_qty'] ) && $_REQUEST['show_qty'] == 1 ) {
		
		$cName = 'total_qty';
		$label = 'จำนวน (แผ่น)|รวม';
		$newColumns['dynamicColumnsSql'][] = "( ". implode( '+', $keep['qty'] ) ." ) as ". $cName ."";
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 0 );
		$newColumns['dynamicColumnsConfig'][$cName]->w = 19;
	}
	else {
		
		
		$cName = 'total_sqm';
		$label = 'จำนวน (sqm)|รวม';
		$newColumns['dynamicColumnsSql'][] = "( ". implode( '+', $keep['sqm'] ) ." ) as ". $cName ."";
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2 );
		$newColumns['dynamicColumnsConfig'][$cName]->w = 19;
	}
	
	
	
	return $newColumns;
	
}


//
//
function glBeforeYear( $param ) {
	
	
	
	$cName = 'test';
	
	$label = 'test';
	
	$newColumns['dynamicColumnsSql'][] = "1 as ". $cName ."";

	$forum_on_ready = NULL;
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready, $dot = 2  );
	$newColumns['dynamicColumnsConfig'][$cName]->w = 22;
	$newColumns['dynamicColumnsConfig'][$cName]->sum = 1;
	$newColumns['dynamicColumnsConfig'][$cName]->sum = '{"forum_on_ready":"5 * [year_ave]"}';
	
	
	
	
	
	
	global $dao;
	
	$looking = $_REQUEST['expect_date'][0][0];
	
	$curentD = '2021-01-01';
	
	//arr( $curentD );
	$test = array();
	for( $m = 1; $m <= 12; ++$m ) {
		
		$sql = "
			SELECT
				date_format( '". $curentD ."', '%m/%Y' ) as my,
				date_format( '". $curentD ."', '%m/%Y' ) as label,
				date_format( '". $curentD ."', '%Y-%m-01' ) as checkMonth,
				ADDDATE( '". $curentD ."', INTERVAL 1 month ) as next_my
				
		";
		
		$res = $dao->fetch( $sql );
		//arr( $looking );
		//arr( $res->checkMonth );
		if( $looking >= $res->checkMonth ) {
			
			$keep[] = 1;
		
		}
		
		$cName = 'c_'. $m .'';
		
		$label = $res->label ;
		
		$test[] = "SUM( dt.sum_credit_debit * ( DATE_FORMAT( dt.last_update, '%m/%Y' ) = '". $res->my ."' ) )";
		$newColumns['dynamicColumnsSql'][] = "SUM( dt.sum_credit_debit * ( DATE_FORMAT( dt.last_update, '%m/%Y' ) = '". $res->my ."' ) ) as ". $cName ."";
		
	
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
		
	
		$newColumns['dynamicColumnsConfig'][$cName]->w = 22;
	

		$curentD = $res->next_my;
		
		
		
	}
	
	//arr( $keep );
	
	//echo count( $keep );

	$cName = 'total';
	
	$label = 'รวม';
	
	$newColumns['dynamicColumnsSql'][] = "(". implode( ' + ', $test ) .") as ". $cName ."";


	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );

	
	$newColumns['dynamicColumnsConfig'][$cName]->w = 22;


	
	$cName = 'year_ave';
	
	$label = 'เฉลี่ย';
	
	$newColumns['dynamicColumnsSql'][] = "1 as ". $cName ."";

	$forum_on_ready = NULL;
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready, $dot = 2  );
	$newColumns['dynamicColumnsConfig'][$cName]->w = 22;
	$newColumns['dynamicColumnsConfig'][$cName]->sum = 1;
	$newColumns['dynamicColumnsConfig'][$cName]->sum = '{"forum_on_ready":"[total] / '. count( $keep ) .'"}';

	
	
	

	return $newColumns;
	
	
	

}


//
//
/*
WHERE gl_code LIKE '%E.55.%.%.00%' 
OR gl_code LIKE '%E.53.%.%.00%' 

แต่งลาย != 0    AND granit != 0   
	C_sale = yod_C + yod_G 
	G_sale = yod_C + yod_G 
	
แต่งลาย = 0    AND granit != 0
	C_sale = 0
	G_sale = yod_G * 2
   
แต่งลาย != 0    AND granit = 0
	C_sale = yod_C * 2
	G_sale = 0
   

*/
function hgkhkdshdkdskdf( $param ) {
	
	

	
	global $dao;
	
		//arr( $_REQUEST['report_date'][0][0]);
	
	$param['doc_date'] = $_REQUEST['report_date'][0][0];	
		
	$filters = array();
	$filters['WHERE'][] = "( new_gl = 1 OR close_month = 1 )";
	$filters['WHERE'][] = "LAST_DAY( doc_date ) = LAST_DAY( '". $param['doc_date'] ."' )";
	$filters['WHERE'][] = "admin_company_id = ". $_SESSION['company_id'] ."";
	
	
	$sql = "delete from erp_gl_trn_dt [WHERE]";
	$sql = genCond_( $sql, $filters );
	
	//arr( $sql );
		
	$dao->execDatas( $sql );
	
	$sql = "delete from erp_gl_trn [WHERE]";
	
	$sql = genCond_( $sql, $filters );
	//	arr( $sql );

	
	$dao->execDatas( $sql );
	
	//exit;
	

	$sql = "
		SELECT 
			*
		FROM erp_product_group_master
		WHERE gl_ids !=  '0'
		 
	";
	$gl_ids = array();
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
		//arr( $vc );
		
		$gl_ids = array_merge( json_decode( $vc->gl_ids ), $gl_ids );
		
		$config[$vc->cName] = array( 'id' => $vc->id, 'gl_ids' => json_decode( $vc->gl_ids ), 'ave_product_group_factor' => $vc->ave_product_group_factor );
	}
	
	//arr( $config );
	
	//
	//
	$test = $keep = array();

	//
	//
	$gogo = $test = $keep = array();
	//$keep[] = "'total after factor' as label"; 
	
	foreach( $config as $kc => $vc ) {
		
		
		$keep[] = "
			SUM( dt.product_group LIKE '%". $vc['id'] ."%' OR dt.product_group = 0 ) as factor_". $kc .",
			
			ROUND( SUM( ( dt.product_group LIKE '%". $vc['id'] ."%' OR dt.product_group = 0 ) * dt.sum_credit_debit * ( dt.gl_id IN ( ". implode( ', ', $vc['gl_ids'] ) ." ) ) ), 2 ) as ". $kc .",
			
			SUM( dt.sum_credit_debit * ( dt.gl_id IN ( ". implode( ', ', $vc['gl_ids'] ) ." ) ) ) as yod_". $kc ."
		";
		
		$gogo[] = "new_tb.yod_". $kc ."";
		
		$test[] = "new_tb.". $kc ."";
		
		
	
	}
	
	
	//arr( $keep );
	$sql = "
		SELECT
			new_tb.*,
			'' as total_factor,
			". implode( ' + ', $gogo ) ." as total_yod,
			". implode( ' + ', $test ) ." as total,
			'-' as gl_ids
		FROM (
		
			SELECT
				". implode( ',', $keep ) ."
			FROM erp_gl_trn_dt dt
			LEFT JOIN erp_gl gl ON dt.gl_id = gl.id
			[WHERE]
			
		) as new_tb	
	
	";
	$filters = $param['filters'];
	//$filters['WHERE'][] = "dt.product_group != '' AND dt.gl_id IN ( ". implode( ',', $gl_ids ) ." )";
	$filters['WHERE'][] = "dt.gl_id IN ( ". implode( ',', $gl_ids ) ." )";
	//$filters['WHERE'][] = "gl.gl_code LIKE '%E.55.%.%.00%' 
//OR gl.gl_code LIKE '%E.53.%.%.00%'";

	if( $_SESSION['company_id'] == 1 ) {
		
		$filters['WHERE'][] = "dt.admin_company_id = ". $_SESSION['company_id'] ."";
	}
	else {
		
		$filters['WHERE'][] = "dt.admin_gcompany_id = ". $_SESSION[gCompany] ."";
	}
	
	$filters['WHERE'][] = "dt.close_month != 1";

	$sql = gencond_( $sql, $filters );
	 
//	arr( $sql );

	foreach( $dao->fetchAll( $sql ) as $kh => $header ) {
		
		
		//arr( $header );
		
		$clone = clone( $header );
		
		$sql = "
			SELECT 
				gl.gl_code,
				CONCAT( 'gl: (', dt.gl_id, ') ', gl.gl_code, ' ', gl.gl_name ) as gname,
				gl.gl_name,
				dt.doc_no,
				dt.gl_id,
				dt.remark,
				
				
				SUM( dt.sum_credit_debit ) as sum_credit_debit,
				SUM( dt.credit ) as credit,
				SUM( dt.debit ) as debit,
				
			
				IF( dt.product_group = 0, '1,2,3,4', dt.product_group ) as product_group,
				IF( dt.product_group = 0, '1,2,3,4', dt.product_group ) as product_group__,
				dt.doc_date
			FROM erp_gl_trn_dt dt
			LEFT JOIN erp_gl gl ON dt.gl_id = gl.id
			[WHERE]
			GROUP BY 
				dt.id,
				product_group__,
				dt.gl_id
			ORDER BY 
				product_group,
				dt.gl_id
		";
		
		//arr( $sql );
		
		$filters = $param['filters'];
		
		
			
		$filters['WHERE'][] = "dt.admin_company_id = ". $_SESSION['company_id'] ."";
		
		
		//$filters['WHERE'][] = "dt.admin_company_id = ". $_SESSION['company_id'] ."";
		
		//$filters['WHERE'][] = "dt.product_group != '' AND dt.gl_id IN ( ". implode( ',', $gl_ids ) ." )";
		$filters['WHERE'][] = "( gl.gl_code LIKE '%E.55.%.%.00%' 
OR gl.gl_code LIKE '%E.53.%.%.00%' )";
		
		$sql = gencond_( $sql, $filters );
		
		//arr( $sql);
		//exit;
		$keep = array();
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
			
			//if( empty( $va->product_group ) ) {
				
				//$va->product_group = '1, 2, 3, 4';
			//}
			
			//arr( $va->product_group );
			
			$va->mode = '';
			
			
			//c | m | t | g
			//$va->mode = 'c | m | t | g';
			if(  !empty( $va->product_group ) ) {
				$sql = "
					SELECT  
						group_concat( distinct cName  SEPARATOR ' | ' ) as mode
					FROM erp_product_group_master
					WHERE id IN( ". $va->product_group ." ) 
				";
				
				//arr( $sql );
				foreach( $dao->fetchAll( $sql ) as $km => $vm ) {
					
					$va->mode = $vm->mode;
				}
			}
			
			
			$va->header_label = '#';
			$va->header_label_c = 'แต่งลาย';
			$va->header_label_m = 'MONO';
			$va->header_label_t = 'TRADING';
			$va->header_label_g = 'GRANITE';
			$va->header_label_total = 'รวม';
		
		
		
		
		
			$va->header_yod_label = 'a';
			$va->header_yod_c = $header->yod_c;
			$va->header_yod_m = $header->yod_m;
			$va->header_yod_t = $header->yod_t;
			$va->header_yod_g = $header->yod_g;
			$va->header_total_yod = $header->total_yod;
			


			$va->header_factor_label = 'factor';
			$va->header_factor_c = $header->factor_c;
			$va->header_factor_m = $header->factor_m;
			$va->header_factor_t = $header->factor_t;
			$va->header_factor_g = $header->factor_g;
			$va->header_total_factor = '';
			
			
			$va->header_total_label = 'รวม';
			$va->header_c = $header->c;
			$va->header_m = $header->m;
			$va->header_t = $header->t;
			$va->header_g = $header->g;
			$va->header_total = $header->total;
			
			$ex = explode( ',', $va->product_group );
			
			$master_total[$va->product_group] = array();
			
			foreach( $config as $kc => $vc ) {
				
				if( in_array( $vc['id'], $ex ) ) {
					
					$master_total[$va->product_group][$vc['id']] = $header->$kc;
				
				}
			}
			
			
			$total = 0;	
			
			
	
			
			$va->factor_c = 0;
			$va->factor_g = 0;
			$va->factor_t = 0;
			$va->factor_m = 0;
			foreach( $config as $kc => $vc ) {
				
				
				
				$val = 0;
				
				if( in_array( $vc['id'], $ex )  ) {
					
					$name = 'factor_'. $kc .'';
					
					$name2 = 'yod_'. $kc .'';
					$va->$name = $clone->$name2;
					
					
					
				}
				
				
					
				$va->$kc = $val;
				
				$total += $val;	
			}
			
			
			if( $va->factor_c !=0 && $va->factor_g != 0 ) {
				
				$val = $va->factor_c + $va->factor_g;
				$va->factor_c = $val;
				$va->factor_g = $val;
				
			}
			else if( $va->factor_c ==0 && $va->factor_g != 0 ) {
			
				$va->factor_c = 0;
				$va->factor_g *= 2;
				
			}
			else if( $va->factor_c !=0 && $va->factor_g == 0 ) {
		
				
				
				$va->factor_c *= 2;
				$va->factor_g = 0;
				
			}
			
			
			$factorTotal = $va->factor_c + $va->factor_m + $va->factor_g + $va->factor_t;
			
			$sumAvr = 0;
			
			$sumFactor = 0;
			
			foreach( $config as $kc => $vc ) {
				
				$val = 0;
				
				if( in_array( $vc['id'], $ex ) ) {
					
					$name = 'factor_'. $kc .'';
					
					if( true ) {
							
						if( ROUND( $sumFactor + $va->$name, 2 ) == ROUND( $factorTotal, 2 ) ) {
							
							
							$val = ROUND( $va->sum_credit_debit - $sumAvr, 2 );
							
						}
						else {
							
							$val = ROUND( $va->$name / ( $factorTotal ) * $va->sum_credit_debit, 2 );
							
							$sumAvr += $val;
							
							$sumFactor = $sumFactor + $va->$name;
							
						}
					}
					else {
						
						
						if( $factorTotal == 0 ) {
							$val = 0;
						}
						else {
							$val = ROUND( $va->$name / ( $factorTotal ) * $va->sum_credit_debit, 2 );
						}
						
					}
					
				}
					
				$va->$kc = $val;
				
			}
				
			$adjust = $va->sum_credit_debit - $sumAvr;
				
			$va->total = $total;
			
			
			
			$keep[] = $va;
			
			
			//arr( $va );
			$insert[$va->product_group][$va->gl_id][] = $va;
		}
		
		insertGlGG( $insert );
		
		return $keep;
			
		
	}
	
	
}

function insertGlGG( $insert ) {
	
	global $dao;
	
	
	
	$skip = array();
	$i = 0;
	foreach( $insert as $kz => $vz ) {
		
		foreach( $vz as $ki => $vi ) {
			
			++$i;
			
			$parent = $vi[0];
			
			$ex = explode( '.', $parent->gl_code );
			
			unset( $ex[count($ex)-1] );
			
			$gl_parent = implode( '.', $ex );
			
			
			if( $_SESSION['company_id'] != 1  ) {
				$sql = "
					SELECT
						DATE_FORMAT( ADDDATE( '". $parent->doc_date ."', INTERVAL 543 year ), 'JC%y%m-T". makeFrontZero( $i, 4 ) ."' ) as t
				";
			}
			else {
				
				$sql = "
					SELECT
						DATE_FORMAT( ADDDATE( '". $parent->doc_date ."', INTERVAL 543 year ), 'JC%y%m-". makeFrontZero( $i, 4 ) ."' ) as t
				";
			}
			
			$doc_no = $dao->fetch( $sql )->t;
			
			$sql = "
				REPLACE INTO erp_gl_trn (
					book_id,
					edit_bt,
					admin_company_id,
					doc_no,
					doc_date,
					user_id,
					lock_doc,
					gl_trn_note,
					jn_id,
					close_month,
					new_gl
				)

				SELECT
					6 as book_id,
					1 as edit_bt,
					". $_SESSION['company_id'] ." as admin_company_id,
					'". $doc_no ."' as doc_no,
					LAST_DAY( '". $parent->doc_date ."' ) as doc_date,
					". $_SESSION[Uid] ." user_id,
					1 as lock_doc,
					'โอนปิด ". $parent->gl_name ."' as gl_trn_note,
					5 as jn_id,
					0 as close_month,
					1  as new_gl
			";
			

	//arr( $sql );
			$dao->execDatas( $sql );
				
			$parent_id = $dao->lastId;
			
			
			$totals = array();
			
			$findTotal = array(  'credit', 'debit', 'c', 'm', 't', 'g', 'sum_credit_debit' );
			foreach( $vi as $ko => $vo ) {
				
		//	arr( $vo );
			
				foreach( $findTotal as $kt => $vt ) {
					$totals[$vt][] = $vo->$vt;
					
				}

			}
			
			$id = getSkipId( 'erp_gl_trn_dt', 'id', $skip );

			$skip[] = $id;
			
			if( array_sum( $totals['debit'] ) >  array_sum( $totals['credit'] ) ) {
				
				$sqlUnion[] = "
					SELECT
						'erp_gl_trn' as tb_parent,
						". $_SESSION['company_id'] ." as admin_company_id,
						'". $doc_no ."' as doc_no,
						". $parent->gl_id ." as gl_id,
						LAST_DAY( '". $parent->doc_date ."' ) as doc_date,
						0 as debit,
						". array_sum( $totals['sum_credit_debit'] ) ." as credit,
						
						'manual' as insert_type,
						'โอนปิด ". $parent->gl_name ." ' as remark,
						0 as close_month,
						'1' as type,
						". $id ." as id,
						". $parent_id ." as parent_id,
						1 as new_gl
				";
			}
			else {
				
				$sqlUnion[] = "
					SELECT
						'erp_gl_trn' as tb_parent,
						". $_SESSION['company_id'] ." as admin_company_id,
						'". $doc_no ."' as doc_no,
						". $parent->gl_id ." as gl_id,
						LAST_DAY( '". $parent->doc_date ."' ) as doc_date,
						". array_sum( $totals['sum_credit_debit'] ) * -1   ." as debit,
						0 as credit,
						
						'manual' as insert_type,
						'โอนปิด ". $parent->gl_name ." ' as remark,
						0 as close_month,
						'2' as type,
						". $id ." as id,
						". $parent_id ." as parent_id,
						1 as new_gl
				";
				
			}
			
			
			
			
			$findTotal = array( 'c', 'g', 'm', 't' );
			
				
				
				///arr( $totals );
		//	arr( $vo );
			
			foreach( $findTotal as $kt => $vt ) {
				
				if( array_sum( $totals[$vt] ) == 0 ) {
					
					continue;
				}
				


				$sql = "
					SELECT 
						* 
					FROM erp_gl 
					WHERE gl_code = '". $gl_parent .".0". ( $kt + 1 ) ."'
				";
				
				$gl_child = $dao->fetch( $sql );
				
				//arr( $gl_child );
				
				$id = getSkipId( 'erp_gl_trn_dt', 'id', $skip );

				$skip[] = $id;
				
				
				
				if( array_sum( $totals['debit'] ) >  array_sum( $totals['credit'] ) ) {
					
					$sqlUnion[] = "
						SELECT
							'erp_gl_trn' as tb_parent,
							". $_SESSION['company_id'] ." as admin_company_id,
							'". $doc_no ."' as doc_no,
							". $gl_child->gl_id ." as gl_id,
							LAST_DAY( '". $parent->doc_date ."' ) as doc_date,
							". array_sum( $totals[$vt] ) ." as debit,
							0 as credit,
							
							'manual' as insert_type,
							'โอนปิด  ". $gl_child->gl_name ." ' as remark,
							0 as close_month,
							'3' as type,
							". $id ." as id,
							". $parent_id ." as parent_id,
							1 as new_gl
					";
				}
				else {
					
					$sqlUnion[] = "
						SELECT
							'erp_gl_trn' as tb_parent,
							". $_SESSION['company_id'] ." as admin_company_id,
							'". $doc_no ."' as doc_no,
							". $gl_child->gl_id ." as gl_id,
							LAST_DAY( '". $parent->doc_date ."' ) as doc_date,
							0 as debit,
							". array_sum( $totals[$vt] ) * -1 ." as credit,
							
							'manual' as insert_type,
							'โอนปิด  ". $gl_child->gl_name ." ' as remark,
							0 as close_month,
							'3' as type,
							". $id ." as id,
							". $parent_id ." as parent_id,
							1 as new_gl
					";
				}
			}
		}
	}
	
	$sql = "
		INSERT INTO erp_gl_trn_dt (
			tb_parent,
			admin_company_id,
			doc_no,
			gl_id,
			doc_date,
			debit,
			credit,
			insert_type,
			remark,
			close_month,
			type,
			id,
			parent_id,
			new_gl
			
		)
		SELECT
			new_tb.*
		FROM (
		". implode( ' UNION ', $sqlUnion ) ."
		) as new_tb
			
	";
	
//arr( $sql );
	$dao->execDatas( $sql );

	
	
	
}
	



//
//
function dfsadssd( $param ) {
	
	global $dao;
	
	
	$sql = "
		SELECT 
			DATE_FORMAT( s.doc_date, '%m/%y' ) AS m, 
			IF( p.type = '-', 'ไม่ระบุ', p.type ) as t
		 
		FROM aa_sale_result_dt dt
		LEFT JOIN aa_sale_result s ON dt.parent_id = s.id
		LEFT JOIN aa_sale_product p ON dt.article_id = p.article_id
		[WHERE]
		GROUP BY 
			m, 
			t
		ORDER BY 
			s.doc_date ASC, 
			p.type DESC
	";
	
	$res = array( 
		'qty' => array( 'label' => 'จำนวน' ),
		'sale' => array( 'label' => 'บาท' )
	);
	
	$filters = array();
	if( !empty( $param['filters'] ) ) {
		
		$sql = gencond_( $sql, $param['filters'] );
		
	}
	
	$i = 0;
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
		
		$cName = 'sale_'. $kc .'';
		
		$label = $vc->m . '|     Sale/'. $vc->t;
		
		$newColumns['dynamicColumnsSql'][] = "SUM( dt.sale * ( p.type = '". $vc->t ."' ) * ( date_format( s.doc_date, '%m/%y' ) = '". $vc->m ."' ) ) as ". $cName;
	
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
		
		$newColumns['dynamicColumnsConfig'][$cName]->w = 18;
	
	
	
		$cName = 'qty_'. $kc .'';
		
		$label = $vc->m . '|   Qty/'. $vc->t . ' '   ;
		
		$newColumns['dynamicColumnsSql'][] = "SUM( dt.qty * ( p.type = '". $vc->t ."' ) * ( date_format( s.doc_date, '%m/%y' ) = '". $vc->m ."' ) ) as ". $cName;
	
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
		
		$newColumns['dynamicColumnsConfig'][$cName]->w = 18;
		
	}
	
	
	$cName = 'sale';
	
	$label = 'Total|     Sale';
	
	$newColumns['dynamicColumnsSql'][] = "SUM( dt.sale * ( 1 ) * ( 1 ) ) as ". $cName;

	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
	
	$newColumns['dynamicColumnsConfig'][$cName]->w = 18;



	$cName = 'qty';
	
	$label = 'Total|   Qty';
	
	$newColumns['dynamicColumnsSql'][] = "SUM( dt.qty * ( 1 ) * ( 1 ) ) as ". $cName;

	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
	
	$newColumns['dynamicColumnsConfig'][$cName]->w = 18;


	
	
	
	
	
	

	return $newColumns;

}


//
//
function soProductSecondGroup( $param ) {
	
	global $dao;
	
	
	$sql = "
		SELECT 
			c.company_group_id,
			tcg.company_group_name
		FROM erp_sale_order_dt as dt	
		LEFT JOIN erp_sale_order st ON dt.parent_id = st.id 
		LEFT JOIN erp_company c ON st.company_id = c.company_id 
		LEFT JOIN erp_company_group tcg ON c.company_group_id = tcg.company_group_id 
		[WHERE]
		GROUP BY 
			c.company_group_id,
			tcg.company_group_name	
		ORDER BY 
			tcg.company_group_name ASC
			
	";
	
	$res = array( 
		'qty' => array( 'label' => 'จำนวน' ),
		'before_vat' => array( 'label' => 'บาท' )
	);
	
	$filters = array();
	if( !empty( $param['filters'] ) ) {
		
		$sql = gencond_( $sql, $param['filters'] );
		
	}
	
	$i = 0;
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
		if( empty( $vc->company_group_name )   ) {
			$vc->company_group_name = 'SAC';
		}
		
		foreach( $res as $kr => $vr ) {
			
			$cName = $vc->company_group_id . '_'. $kr .'';
			
			$label = $vc->company_group_name . '|'. $vr['label'];
			
			//dt.before_vat * ( dt.qty - dt.cancel_qty  ) /  dt.qty
			$sumSql = "
			IFNULL( SUM( ". $kr ." * ( dt.qty - dt.cancel_qty  ) /  dt.qty * ( c.company_group_id = ". $vc->company_group_id ." ) ), 0 )";
			
			$keep[$kr][] = $sumSql;
			
			$newColumns['dynamicColumnsSql'][] = $sumSql ." as ". $cName;
		
			if( $kr == 'qty' ) {
				
				$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 0  );
			}
			else {
				$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
			}
			
			$newColumns['dynamicColumnsConfig'][$cName]->w = 19;

		}
		
		++$i;
		
	}
	
	foreach( $res as $kr => $vr ) {
		
		$cName = 'total_'. $kr .'_';
		
		$label = 'รวม|'. $vr['label'] .'';
		

		$newColumns['dynamicColumnsSql'][] = "( ". implode( '+', $keep[$kr] ) ." ) as ". $cName ."";
		

		if( $kr == 'qty' ) {
			
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 0  );
		}
		else {
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
		}

		
		$newColumns['dynamicColumnsConfig'][$cName]->w = 19;
		
	}
	
	return $newColumns;

}




//
//
function saleProductSecondGroup( $param ) {
	
	//arr( $param );
	
	//exit;
	
	global $dao;
	
	
	$sql = "
		SELECT 
			c.company_group_id,
			tcg.company_group_name
		FROM erp_stock_dt as dt	
		LEFT JOIN erp_stock st ON dt.parent_id = st.id 
		LEFT JOIN erp_company c ON st.company_id = c.company_id 
		LEFT JOIN erp_company_group tcg ON c.company_group_id = tcg.company_group_id 
		[WHERE]
		GROUP BY 
			c.company_group_id,
			tcg.company_group_name	
		ORDER BY 
			tcg.company_group_name ASC
			
	";
	
	$res = array( 
		'qty' => array( 'label' => 'จำนวน' ),
		'before_vat' => array( 'label' => 'บาท' )
	);
	
	$filters = array();
	if( !empty( $param['filters'] ) ) {
		
		$sql = gencond_( $sql, $param['filters'] );
		
	}
	
	$i = 0;
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
	
		
		if( empty( $vc->company_group_name )   ) {
			$vc->company_group_name = 'SAC';
		}
		
		foreach( $res as $kr => $vr ) {
			
			$cName = $vc->company_group_id . '_'. $kr .'';
			
			$label = $vc->company_group_name . '|'. $vr['label'];
			
			
			$sumSql = "
			IFNULL( SUM( ". $kr ." * ( c.company_group_id = ". $vc->company_group_id ." ) ), 0 )";
			
			$keep[$kr][] = $sumSql;
			
			$newColumns['dynamicColumnsSql'][] = $sumSql ." as ". $cName;
		
			if( $kr == 'qty' ) {
				
				$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 0  );
			}
			else {
				$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
			}
			
			$newColumns['dynamicColumnsConfig'][$cName]->w = 19;

		}
		
		++$i;
		
	}
	
	foreach( $res as $kr => $vr ) {
		
		$cName = 'total_'. $kr .'_';
		
		$label = 'รวม|'. $vr['label'] .'';
		

		$newColumns['dynamicColumnsSql'][] = "( ". implode( '+', $keep[$kr] ) ." ) as ". $cName ."";
		

		if( $kr == 'qty' ) {
			
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 0  );
		}
		else {
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label, $w = 0, $forum_on_ready = NULL, $dot = 2  );
		}

		
		$newColumns['dynamicColumnsConfig'][$cName]->w = 19;
		
	}
	
	return $newColumns;

}


//
//
function ggg( $param ) {
	
	global $dao;

	if( !empty( $_REQUEST['report_date'][0][1] ) ) {
		
		$maxYear = $_REQUEST['report_date'][0][1];
		
	}
	else {
		$sql = "
		
			SELECT
				DATE_FORMAT( MAX( ADDDATE( things_date, INTERVAL ( 100 / things_depreciation_rate ) year ) ), '%Y' ) as max_year
			FROM tb_things

		";
		$years = $dao->fetch( $sql );
		$maxYear = $years->max_year;
	}
	
	
	$minYear = 2020;
	if( !empty( $_REQUEST['report_date'][0][0] ) ) {
		
		$minYear = $_REQUEST['report_date'][0][0];
	}
		
	$dynamicColumnsW = $param['dynamicColumnsW'];
	
	
	for( $y = $minYear; $y <= $maxYear; ++$y ) {
		
		for( $m = 1; $m <= 12; ++$m ) {
			
			$sql = "
				SELECT
					DATE_FORMAT( '2019-". makeFrontZero( $m ) ."-01', '%b' ) as m
			";
			
			$res = $dao->fetch( $sql );
			
			$newColumns['dynamicColumnsSql'][] = "444 as years_". $y ."_". $m;
			
			$newColumns['dynamicColumnsConfig']['years_'. $y .'_'. $m] = getStandardColumn( 'ราคาหักค่าเสือมในปี '. $y .'|'. $res->m );
			
			$newColumns['dynamicColumnsConfig']['years_'. $y .'_'. $m]->w = $dynamicColumnsW;
		}
	
		$newColumns['dynamicColumnsSql'][] = "444 as years_". $y ."";
		
		$newColumns['dynamicColumnsConfig']['years_'. $y] = getStandardColumn( 'ราคาหักค่าเสือมในปี '. $y .'| รวม' );
	
		$newColumns['dynamicColumnsConfig']['years_'. $y]->w = $dynamicColumnsW + 1;
	}

	return $newColumns;
}



//
//
function thingsReport( $param ) {

	global $dao;
	
	if( !empty( $_REQUEST['report_date'][0][1] ) ) {
		
		$maxYear = $_REQUEST['report_date'][0][1];
		
	}
	else {
		$sql = "
			SELECT
				DATE_FORMAT( MAX( ADDDATE( things_date, INTERVAL ( 100 / things_depreciation_rate ) year ) ), '%Y' ) as max_year
			FROM tb_things

		";
		$years = $dao->fetch( $sql );
		$maxYear = $years->max_year;
	}
	
	
	$minYear = 2020;
	if( !empty( $_REQUEST['report_date'][0][0] ) ) {
		
		$minYear = $_REQUEST['report_date'][0][0];
		
	}
	
	$filters['WHERE'][] = "th.admin_company_id = ". $_SESSION['company_id'];
	
	$param['sql'] = gencond_( $param['sql'], $filters );
	
	//arr($param['sql']);
	foreach ( $dao->fetchAll( $param['sql'] ) as $ka => $va ) {
		
		$skip = true; 
		for( $y = $minYear; $y <= $maxYear; ++$y ) {
			
			$totalThisMonth = array();
			
			for( $m = 1; $m <= 12; ++$m ) {
				
				$toDay = $y .'-'. makeFrontZero( $m ) .'-01';
				
				$sql = "
					SELECT
						ADDDATE( '". $toDay ."', INTERVAL -1 day ) as start_day,
						LAST_DAY( '". $toDay ."' ) as end_day
				";
				
				$res = $dao->fetch( $sql );
			
				$calculateAsset = calculateAsset(
					$va->things_cost_price,
					$va->things_depreciation_rate,
					$va->things_carcass_price,
					$va->things_rerun_date,
					$res->end_day,
					$res->start_day,
					$va->things_sale_date,
					$va->things_rerun_date
				);
				
				$name = 'years_'. $y .'_'. $m .'';
				$va->$name = $calculateAsset['this_month'];
				
				$totalThisMonth[] = $calculateAsset['this_month'];

				if( !empty( $calculateAsset['this_month'] ) ) {
					$skip = false;
				}
			}
			
			$name = 'years_'. $y;
			
			$va->$name = array_sum( $totalThisMonth );
			
			if( !empty( $va->$name ) ) {
				$skip = false;
			}
		}
		
		if( $skip == true ) {
			continue;
		}
		
		$columns = array();
		foreach( $va as $kc => $vc ) {
			
			$columns[] = "'". $vc ."' as ". $kc ."";
		}
		
		$keepSql[] = "
			SELECT
				". implode( ',', $columns ) ."
		";
		
		if( !empty( $_REQUEST['report_limit'][0][0] ) ) {
			
			if( count( $keepSql ) == $_REQUEST['report_limit'][0][0] ) {
			
				break;
			}
		}
		
	}


	if( isset( $param['type'] ) && $param['type'] == 1 ) {
		
		$sql = "
			SELECT
				new_tb.*
			FROM (
			". implode( ' UNION ', $keepSql ) ."
			) as new_tb
		";
		
	}
	else {
		
		$columns = array();
		$columns[] = "new_tb.things_type_name";
		$columns[] = "new_tb.office";
		$columns[] = "SUM( new_tb.things_cost_price ) as things_cost_price";
		
		for( $y = $minYear; $y <= $maxYear; ++$y ) {
			
			for( $m = 1; $m <= 12; ++$m ) {
				
				$columns[] = "SUM( new_tb.years_". $y ."_". $m ." ) as years_". $y ."_". $m ."";
			}
			
			$columns[] = "SUM( new_tb.years_". $y ." ) as years_". $y;
		}
		
		
		if( isset( $param['groupBy'] ) ) {
			
			$sql = "
				SELECT
					". implode( ', ', $columns ) ."
				FROM (
				
				". implode( ' UNION ', $keepSql ) ."
				) as new_tb	
				GROUP BY 
					office,
					things_type_name
				ORDER BY 
					office,
					things_type_name
			";
		}
		else {

			$sql = "
				SELECT
					". implode( ', ', $columns ) ."
				FROM (
				
				". implode( ' UNION ', $keepSql ) ."
				) as new_tb	
				GROUP BY
					things_type_name
				ORDER BY
					things_type_name
			";
				
		}
	}
	
	$keep = array();
	foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {
		
		$keep[] = $va;
	}
	
	return $keep;
}

//
//
function stockReport( $param ) {
	
	global $dao;
	
	$keep = array();
	foreach( $dao->fetchAll( $param['sql'] ) as $kd => $vd ) {
		
		if( !isset( $products[$vd->product_id] ) ) {
			
			$sql = "
				SELECT
					CONCAT( 'ยอดยกมาวันที่ ', dt.doc_date ) doc_date,
					dt.doc_no,
					concat( p.product_code, ' ', p.product_name ) as product,
					dt.qty_bal as p_qty,
					0 as m_qty,
					dt.qty_bal,
					dt.amt_bal,
					dt.cost_bal,
					dt.product_id
				FROM erp_stock_dt dt
				LEFT JOIN erp_product p ON dt.product_id = p.product_id
				where dt.product_id = ". $vd->product_id ." AND dt.doc_date < '". $vd->doc_date ."'
				ORDER BY
					dt.product_id DESC,
					dt.order_number DESC
					LIMIT 0, 1
			";
			
			//arr( $sql );
			
			foreach( $dao->fetchAll( $sql ) as $kh => $vh ) {
				
				$keep[] = $vh;
			}
			
			$products[$vd->product_id] = 1;
		}
		
		$keep[] = $vd;
	}
	
	return $keep;
}

//
//
function calCost() {

	global $dao;

	$param['config_name'] = array( 'config_stock_closed_date' );

	$getClosedDate = getConfigVal( $param );

	$sql = "
		SELECT
			a.product_id,
			IFNULL( SUM( b.stock_master_zone_qty ), 0 ) as stock_dt_qty_bal,
			IFNULL( SUM( b.stock_master_zone_amt ), 0 ) as stock_dt_amt_bal
		FROM erp_product a
		LEFT JOIN erp_stock_master_zone_period b ON a.product_id = b.product_id
		WHERE a.update_stock_cost = 1

		GROUP BY a.product_id
	";

	//arr( $sql );
	//
	//
	foreach ( $dao->fetchAll( $sql ) as $kp => $vp ) {

		$product_id = $vp->product_id;

		$sql = "
			SELECT
				a.*
			FROM erp_stock_dt a
			WHERE a.product_id = ". $product_id ."
			AND a.doc_date > '". $getClosedDate ."'
			ORDER BY
				a.doc_date ASC,
				a.action_order ASC,
				a.stock_act_action DESC,
				a.stock_dt_auto_calc ASC,
				a.doc_no ASC
		";

		//arr( $sql );

		$rowBefore = $vp;
		foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {

			$va->qty -= $va->qty_return;

			$data_ = array();

			//
			// plus and produce
			if ( $va->action_order == 1 ) {

				$sql = "
					SELECT
						IFNULL(
							(
								SELECT
									SUM( stock_dt_amt )
								FROM erp_stock_dt
								WHERE stock_id = ". $va->stock_id ."
								AND action_order = 0
							)
							/
							(
								SELECT
									SUM( qty )
								FROM erp_stock_dt
								WHERE stock_id = ". $va->stock_id ."
								AND action_order = 1
							)
							* ". $va->qty ."
							, 0
						) as stock_dt_amt

				";

				$data_['stock_dt_amt'] = $dao->fetch( $sql )->stock_dt_amt;

				$data_['stock_dt_qty_bal'] = $rowBefore->stock_dt_qty_bal + ( $va->qty * $va->stock_act_action );

				$data_['stock_dt_amt_bal'] = $rowBefore->stock_dt_amt_bal + ( $data_['stock_dt_amt'] * $va->stock_act_action );

				$data_['stock_dt_cost'] = $data_['stock_dt_amt'] / $va->qty;

			}
			//
			// minus
			else if ( $va->stock_dt_auto_calc == 1 ) {

				if ( $rowBefore->stock_dt_qty_bal != 0 ) {

					$data_['stock_dt_cost'] = $rowBefore->stock_dt_amt_bal / $rowBefore->stock_dt_qty_bal;
				}
				else {

					$data_['stock_dt_cost'] = 0;
				}

				$data_['stock_dt_amt'] = $data_['stock_dt_cost'] * $va->qty;

				$data_['stock_dt_qty_bal'] = $rowBefore->stock_dt_qty_bal + ( $va->qty * $va->stock_act_action );

				$data_['stock_dt_amt_bal'] = $rowBefore->stock_dt_amt_bal + ( $data_['stock_dt_amt'] * $va->stock_act_action );
			}
			//
			// plus
			else {

				if ( $va->qty != 0 ) {

					$data_['stock_dt_cost'] = $va->stock_dt_amt / $va->qty;
				}
				else {

					$data_['stock_dt_cost'] = 0;
				}

				$data_['stock_dt_qty_bal'] = $rowBefore->stock_dt_qty_bal + ( $va->qty * $va->stock_act_action );

				$data_['stock_dt_amt_bal'] = $rowBefore->stock_dt_amt_bal + ( $va->stock_dt_amt * $va->stock_act_action );
			}


			$dao->update( 'erp_stock_dt', $data_, "stock_dt_id = " . $va->stock_dt_id );

			$rowBefore = ( object ) $data_;
		}
	}

	$sql = "
		UPDATE
			erp_product
		SET update_stock_cost = 0
	";
	$dao->execDatas( $sql );
}


//
//
function setNewReport( $param ) {

	global $dao;



	$keep = array();
	foreach ( $param['data'] as $ka => $va ) {

		if ( empty( $stock_dt_cost_pres[$va->product_id] ) ) {

			$sql = "
				SELECT
					a.*
				FROM erp_stock_dt a
				LEFT JOIN erp_stock b ON a.stock_id = b.stock_id
				WHERE a.product_id = ". $va->product_id ."
				AND a.stock_act_id IN (". getConfigVal( $param ) .")
				ORDER BY
					b.doc_date ASC,
					a.stock_act_action DESC,
					a.stock_dt_auto_calc ASC,
					b.doc_no ASC
			";

			$res = $dao->fetchAll( $sql );

			$stock_dt_ids = array();
			foreach ( $res as $kb => $vb ) {

				$stock_dt_ids[] = $vb;

			}

			if ( isset( $stock_dt_ids[count($stock_dt_ids)-1] ) )
				$stock_dt_cost_pres[$va->product_id] = $stock_dt_ids[count($stock_dt_ids)-1]->stock_dt_cost;
			else
				$stock_dt_cost_pres[$va->product_id] = 0;
		}

		$va->stock_dt_cost_pres  = $stock_dt_cost_pres[$va->product_id];
		$va->stock_dt_amt_pres    = $va->stock_dt_cost_pres * $va->qty;

		if ( empty( $stock_dt_cost_avg[$va->product_id] ) ) {

			$sql = "
				SELECT
					a.*
				FROM erp_stock_dt a
				LEFT JOIN erp_stock b ON a.stock_id = b.stock_id
				WHERE a.product_id = ". $va->product_id ."
				ORDER BY
					b.doc_date ASC,
					a.stock_act_action DESC,
					a.stock_dt_auto_calc ASC,
					b.doc_no ASC
			";

			$res = $dao->fetchAll( $sql );

			$stock_dt_ids = array();
			foreach ( $res as $kb => $vb ) {

				$stock_dt_ids[] = $vb;

			}

			if ( isset( $stock_dt_ids[count($stock_dt_ids)-1] ) ) {

				$stock_dt_cost_avg[$va->product_id] = $stock_dt_ids[count($stock_dt_ids)-1]->stock_dt_amt_bal / $stock_dt_ids[count($stock_dt_ids)-1]->stock_dt_qty_bal;
			}
			else {

				$stock_dt_cost_avg[$va->product_id] = 0;
			}
		}

		$va->stock_dt_cost_avg = $stock_dt_cost_avg[$va->product_id];

		$va->stock_dt_amt_avg = $va->stock_dt_cost_avg * $va->qty;

		$keep[] = $va;

	}

	return $keep;

}




//
//
function fdateColums( $param ) {
	
	$only_intime = 'only_intime';
	
//arr( $_REQUEST['include_real'] );
	$setT = 60;
	$setTN = 60 - 5;

	global $dao;
	
	if( $_REQUEST['include_real'] == 1 ) {
		
		$param['real_get'] = $_REQUEST['real_get'][0][0];
		$param['real_pay'] = $_REQUEST['real_pay'][0][1];
		//$param['report_date'] = $_REQUEST['report_date'][0][0];
		
	}
	else {
		
		
		
		$param['real_get'] = $_REQUEST['report_date'][0][0];
		$param['real_pay'] = $_REQUEST['report_date'][0][0];
		//$param['report_date'] = $_REQUEST['report_date'][0][0];
	}
	$param['fdatePlus'] = isset( $_REQUEST['defFdatePlus'] )? $_REQUEST['defFdatePlus']: 7;
	
	//real_pay[0][1]
	//arr( $param );
	//$param['data']['doc_date'] = '2021-04-01';
	
	if( $_REQUEST['new_fdate'] == 1 ) {
		
		saveFDateReport( $param );
	}
	
	$start = $_REQUEST['showOndate'][0][0];
	
	$end = $_REQUEST['showOndate'][0][1];
	
	$reportTime = isset( $_REQUEST['reportTime'] )? $_REQUEST['reportTime']: 'day';
	
	$keepC = array();
	
	//arr($param['time_status']);
	
	if( isset( $param['time_status'] ) ) {
		
		
		
		$cName = 'time_status';
		
		if( $reportTime == 'month' ) {
			
			$newColumns['dynamicColumnsSql'][] = "
			
			
				CASE 
					WHEN new_tb.factor = 1 THEN '-'
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) ) THEN '> ". $setT ."'
					
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( '". $start ."', '%Y-%m-01' ) ) THEN '< ". $setT ."'
					
					
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date >= '". $end ."' ) THEN 'หลัง'
					
					WHEN new_tb.status = 'bad' THEN 'มีปัญหา'
					ELSE date_format( f_date, '%m/%y' )
				END as ". $cName ."
			
			";
		}
		else {
			
			$newColumns['dynamicColumnsSql'][] = "
			
			
				CASE 
					WHEN new_tb.factor = 1 THEN '-'
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( ADDDATE( '". $start ."', INTERVAL -". $setT ." day ), '%Y-%m-%d' ) ) THEN '> ". $setT ."'
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date < date_format( '". $start ."', '%Y-%m-%d' ) ) THEN '< ". $setT ."'
					WHEN ( new_tb.status = 'good' ) AND ( new_tb.f_date >= '". $end ."' ) THEN 'หลัง'
					WHEN new_tb.status = 'bad' THEN 'มีปัญหา'
					ELSE date_format( f_date, '%m/%y' )
				END as ". $cName ."
			
			";
		}
		
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $label = 'สถานะ', $w = 18, $forum_on_ready = NULL, $dot = 2, $inputformat = '', 'C', 0 );
	}
	
	

	if( !isset( $_REQUEST[$only_intime] ) ) {
		
		//
		//before_show bad
		$cName = 'bad';
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'bad' ) ) as ". $cName ."
		";
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'มีปัญหา', $w = 17, $forum_on_ready = NULL, $dot = 2  );
		
		
		//
		//before_show over 60
		$cName = 'before_show_bad';
		
		if( $reportTime == 'month' ) {
			
			$newColumns['dynamicColumnsSql'][] = "SUM(
				new_tb.diff 
				* 
				( new_tb.status = 'good' ) 
				* 
				( new_tb.f_date < date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) ) 
			
			) as ". $cName ."";
		}
		else {
			
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * ( new_tb.f_date < ADDDATE( '". $start ."', INTERVAL -". $setT ." day ) ) ) as ". $cName ."
			";
		}
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'ก่อน|นานกว่า'. $setT .'', $w = 21, $forum_on_ready = NULL, $dot = 2  );
		
		
		//
		//before_show in 60
		$cName = 'before_show';
		
		if( $reportTime == 'month' ) {
			
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * 
			
			( new_tb.f_date >= date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) AND   new_tb.f_date < date_format( '". $start ."', '%Y-%m-01' ) ) 
			
			) as ". $cName ."
			";
		}
		else {
			
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * 
			
			( new_tb.f_date >= ADDDATE( '". $start ."', INTERVAL -". $setT ." day ) 
			
			AND 
			
			new_tb.f_date < '". $start ."' ) ) as ". $cName ."
			";
			
		}
		

		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'ก่อน|ใน'. $setT .'', $w = 21, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1 );
		
	}
	


		
	$curentD = $start;
	for( $i = 1; $i <= 100; ++$i ) {
		
		$cName = 'c_'. $i .'';
		
		if( $reportTime == 'month' ) {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%m/%y' ) as label,
					LAST_DAY( '". $curentD ."' ) as curentD,
					ADDDATE( LAST_DAY( '". $curentD ."' ), INTERVAL 1 day ) as nextD
					
			";
			
			$res = $dao->fetch( $sql );
			
			$newColumns['dynamicColumnsSql'][] = "SUM( 
				new_tb.diff 
				* 
				(  new_tb.f_date >= date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) )
				* 
				( new_tb.status = 'good' ) * ( LAST_DAY( new_tb.f_date ) = '". $res->curentD ."' ) ) as ". $cName ."
				";
				
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label, $w = 19, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = 'green' );
		}
		else {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%Y_%m_%d' ) as cName,
					date_format( '". $curentD ."', '%d/%m/%y' ) as label,
					date_format( '". $curentD ."', '%Y-%m-%d' ) as curentD,
					ADDDATE( '". $curentD ."', INTERVAL 1 day ) as nextD	
			";
			
			
			
			$res = $dao->fetch( $sql );
		


			$cName = $res->cName;
			$newColumns['dynamicColumnsSql'][] = "
			
			SUM( 
				new_tb.diff 
				* 
				(  new_tb.f_date >= date_format( ADDDATE( '". $start ."', INTERVAL -". $setTN ." day ), '%Y-%m-01' ) )
				* 
				( new_tb.status = 'good' ) 
				* 
				( 
					new_tb.f_date = '". $res->curentD ."' 
				) 
				
			
			) as ". $cName ."
			";
			
			if( isset( $_REQUEST[$only_intime] ) ) {
				
				
				//arr( $cName );
				
				
				$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label, $w = 19, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = NULL, $column_setting = '{"only_have_data":"1","toggle":{"1":"pink","2":"grey"}}' );
				
			}
			else {
				
				$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label, $w = 19, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = NULL, $column_setting = NULL );
			}
			
			
		}//new_tb.f_date_report
		
		
		///getStandardColumn( $label = 'test', $w = 0, $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = NULL )
		if( $res->nextD > $end ) {
			
			break;
		}
		
		
		$curentD = $res->nextD;
	}
	
	$w = 20;
	
	
	
	
	if( !isset( $_REQUEST[$only_intime] ) ) {
		
		$cName = 'after_show';
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff * ( new_tb.status = 'good' ) * ( new_tb.f_date >= '". $res->nextD ."' ) ) as ". $cName ."
		";
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'หลัง', $w, $forum_on_ready = NULL, $dot = 2  );
	}
	
	
	//
	//รวม
	$cName = 'total';
	if( isset( $_REQUEST[$only_intime] ) ) {
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff  * ( new_tb.f_date >= '". $start ."' AND  new_tb.f_date <= '". $end ."'  ) ) as ". $cName ."
		";
		
	}
	else {
		
		$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.diff ) as ". $cName ."
		";
	}
	
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม', $w, $forum_on_ready = NULL, $dot = 2  );
	
	return $newColumns;
		
}


//
//
function StockOnProcessColumns( $param ) {
	
	//arr( $param['filters']['WHERE'] );

	global $dao;
	

	$sql = "

		SELECT
			SUM( fQtySqm ) as totalSqm,
			CASE 
				WHEN new_tb.a2 IN ( '=' ) THEN 1
				WHEN new_tb.a2 IN ( '&' ) THEN 2
				WHEN new_tb.a2 IN ( '+' ) THEN 3
				WHEN new_tb.a2 IN ( 1 ) THEN 4
				ELSE 5
			END AS order_number,
			new_tb.a2,
			new_tb.fullName,
			CONCAT( 
				product_group_size_code, '|',
				CASE 
				
					WHEN new_tb.a2 IN ( 4, 5 ) THEN 4
					WHEN new_tb.a2 IN ( '+' ) AND new_tb.fullName LIKE '%เป่าไฟ%' THEN '+F'
					WHEN new_tb.a2 IN ( 1 ) AND new_tb.fullName LIKE '%เป่าไฟ%'  THEN '1F'
					ELSE new_tb.a2
				END 
			) AS cName,
			CONCAT( 

				CASE 
					WHEN new_tb.fullName LIKE '%G+00F%' THEN 'ค่าเป่าไฟ'
					WHEN new_tb.a2 IN ( '=' ) THEN 'บล๊อก'
					WHEN new_tb.a2 IN ( '&' ) THEN 'ไม่ขัด'
					WHEN ( new_tb.a2 IN ( '+' ) AND new_tb.fullName  LIKE '%เป่าไฟ%' )  THEN 'ขัด,เป่าไฟ'
					WHEN new_tb.a2 IN ( '+' ) THEN 'ขัด'
					WHEN ( new_tb.a2 IN ( 1 ) AND new_tb.fullName LIKE '%เป่าไฟ%'  ) THEN 'F/G,เป่าไฟ'
					WHEN new_tb.a2 IN ( 1 ) THEN 'F/G'
					WHEN new_tb.a2 IN ( 2 ) THEN 'เจาะอ่าง'
					WHEN new_tb.a2 IN ( 3 ) THEN 'เจียรบัว'
					WHEN new_tb.a2 IN ( 4, 5 ) THEN 'กระแทก'
					ELSE 'OTHER'
				END,
				
				IF( new_tb.a2 IN ( 1 ), 
					CONCAT( '|', product_group_size_name ),
					''
				)
				
			) AS label
			
			
		FROM (
			SELECT
				dt.qty * dt.factor * p.sqm_factor as fQtySqm,
				sz.product_group_size_name,
				sz.product_group_size_code,
				p.fullName,
				RIGHT( LEFT( p.product_code, 2 ), 1 ) as a2
			FROM erp_product p
			
			LEFT JOIN erp_product_group_size sz ON p.product_group_size_id = sz.product_group_size_id
			INNER JOIN erp_stock_dt dt ON p.product_id = dt.product_id
			[WHERE] 
		) as new_tb 
		GROUP BY 
			cName
		[HAVING]	
		ORDER BY 
			order_number, 
			cName
	";
	
	$filters = array();
	
	
	$filters['WHERE'] = $param['filters']['WHERE'];
	//$filters['HAVING'][] = "label = 'ค่าเป่าไฟ'";
	$filters['HAVING'][] = "totalSqm != 0";

	$sql = genCond_( $sql, $filters );
	
	//arr( $sql );
	
	if( false ) {
			$tables[] = getTable____( $dao->fetchAll( $sql )  );
			$html = '
			
				<form data-form="147" id="frm_main" action="sdsdsdsd" enctype="multipart/form-data">

					<input type="submit" value="Save">

					<input type="hidden" name="ajax" value="1">

								
					<div class="clear-fix">'. implode( '<br>', $tables ) .'</div>
				</form>';
			
			echo   '
				<!DOCTYPE html>
				<html lang="en-US">
				<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
				<link rel="Shortcut Icon" type="image/x-icon" href="imgs/logo.gif" />
				<title>admin_product sacth.dyndns.org:888</title>
				<base href="http://125.26.18.4:999/aaa/">

				<link rel="stylesheet" type="text/css" href="template/def/css/jquery.fancybox-1.3.4.css?rand=250433090" />
				<link rel="stylesheet" type="text/css" href="template/def/css/bootstrap.min.css?rand=637178774" />
				<link rel="stylesheet" type="text/css" href="template/def/css/a_css.css?rand=225516902" />
				<link rel="stylesheet" type="text/css" href="template/def/css/flexigrid.pack.css?rand=362219422" />

				<style>
					td,th{
						border: solid 1px #ccc;
					}
					table {
					border-collapse: collapse;
					}
					.my-copy .my-detail{

						display: none;
					}
					}
				</style>
				</head>
				<body>


				 '. $html .'
				</body>
				</html>

			';
			
			exit;
		
	}

	$keepC = array();
	
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
		//arr( $vc );
		
		$cName = ''. $kc .'_sqm';
		$newColumns['dynamicColumnsSql'][] = "
			SUM( fQtySqm * ( a2 = '". $vc->cName ."' ) ) as ". $cName ."
		";
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( '' . $vc->label, $w = 14, $forum_on_ready = NULL, $dot = 2  );
		
	}
	
	$cName = 'total_sqm';
	$newColumns['dynamicColumnsSql'][] = "
		SUM( fQtySqm ) as ". $cName ."
	";
	
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม', $w = 15, $forum_on_ready = NULL, $dot = 2  );
	
	return $newColumns;	
	

		
}

//
//
function yeryieyyuasdfudf( $param ) {
	
	global $dao;
	
	$tbNames[] = 'erp_ap_pay_trn';
	//$tbNames[] = 'erp_gl_trn_pay';
	
	$tbNames[] = 'erp_ap_pay_cheque';
	$tbNames[] = 'erp_ap_pay_bank_account';
	$tbNames[] = 'erp_ap_pay_vat';
	$tbNames[] = 'erp_ap_pay_payment';
	//$tbNames = array();
	   
	//arr( $tbNames);
	foreach( $tbNames as $kt => $tbName ) {
		
		
		if( $tbName == 'erp_ap_pay_trn' ) {
			$sql = "
				SELECT
					parent_id,
					stock_type,
					id,
					tbUpdate
				FROM (
					SELECT
						'a' as test,
						'". $tbName ."' as tbUpdate,
						parent_id,
						stock_type,
						id
					FROM ". $tbName ." 
					
					UNION 
					
					SELECT
						'b' as test,
						'erp_gl_trn_pay' as tbUpdate,
						parent_id,
						'erp_purchase_inv' as stock_type,
						id
					FROM erp_gl_trn_pay
				
				) as new_tb
			
			";
			
		}
		else {
			
			$sql = "
				SELECT
					'". $tbName ."' as tbUpdate,
					parent_id,
					'". $tbName ."' as stock_type,
					id
				FROM ". $tbName ." 
			
			";
			
		}
		
	
		$keep = array();
		
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
			
			
			$gname = ''. $va->parent_id .'-'. $va->stock_type .'';
				
			
		
			
			$keep[$gname][] = 1;
			
			$sqlUnion[$va->tbUpdate][] = "
			
				SELECT
					". $va->id ." as id,
					". count( $keep[$gname] ) ." as n
			";
			
			if( count( $sqlUnion[$va->tbUpdate] ) > 900 ) {
				
				$sql = "
				
					UPDATE ". $va->tbUpdate ." trn
					INNER JOIN (
						". implode( ' UNION ', $sqlUnion[$va->tbUpdate] ) ."
					) as new_tb ON trn.id = new_tb.id
					SET trn.n = new_tb.n
					
				";
				
				//arr( $sql );
				
				$dao->execDatas( $sql );
				
				$sqlUnion[$va->tbUpdate] = array();
				
			}
			
		}
		
		foreach( $sqlUnion as $kt => $vt ) {
			 
				
			$sql = "
			
				UPDATE ". $kt ." trn
				INNER JOIN (
					". implode( ' UNION ', $vt ) ."
				) as new_tb ON trn.id = new_tb.id
				SET trn.n = new_tb.n
				
			";
			
			//arr( $sql );
			
			$dao->execDatas( $sql );
			
			///$sqlUnion[$va->tbUpdate] = array();
			
		}
		
	
	}
	
	

	
	
	
	
	
	//////////////////////////////////////////
	
	$cName = 'amt_total';
	
	$newColumns['dynamicColumnsSql'][] = '145 as total_sqm';
	
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'ตั้งหนี้-ชำระ', $w = 15, $forum_on_ready = NULL, $dot = 2  );
	//$newColumns['dynamicColumnsConfig'] = array();
	return $newColumns;	
	
}


//
//
function dadgsdggdds( $param ) {
	
	global $dao;
	
	
	$ws['trn_amt'] = 18;
	$ws['dueXamt'] = 20;
	$ws['due'] = 10;
	
	$param = array();
	if( isset( $_REQUEST['showDueXamt'] ) ) {
		$param['show'] = 1;
		
	}
	else {
		
		$param['show'] = 0;
	}

	
	$start = $_REQUEST['showOndate'][0][0];
	
	$end = $_REQUEST['showOndate'][0][1];
	
		
	$sql = "
		SELECT 
			
			IF( 1 = 1,
				IF( cg.company_group_name IS NULL, 
					c.company_name, 
					cg.company_group_name
				),
				IF( cg.company_group_name IS NULL, 
					IF( ap.company_id IN ( SELECT company_id FROM erp_company_interest ), c.company_name, 'Other' ), 
					
					IF( c.company_group_id IN ( SELECT company_group_id FROM erp_gcompany_interest ), cg.company_group_name, 'Other' )
				)
			) as company_group,
		
			ap.company_id,
			'' as id,
			trn.parent_id,
			trn.id as trn_id,
			
			'' as tabName,
			
			st.doc_date as st_date,
			0 as pay_due,
			trn.amt as trn_amt,
			ap.doc_date as ap_date
		FROM erp_ap_pay_trn trn
		INNER JOIN erp_ap_pay ap ON trn.parent_id = ap.id
		
		LEFT JOIN erp_company c ON ap.company_id = c.company_id
		LEFT JOIN erp_company_group cg ON c.company_group_id = cg.company_group_id
		
		
		
		INNER JOIN erp_stock st ON trn.lock_parent_id = st.id
		[WHERE]
		ORDER BY 
			trn.parent_id,
			trn.id ASC
	";
	$filters = array();
	///$filters['WHERE'][] = "trn.parent_id IN  ( 664, 1477 )";
	//$filters['WHERE'][] = "trn.parent_id IN  ( 364 )";
	//$filters['WHERE'][] = "trn.parent_id IN  ( 1446 )";
	$filters['WHERE'][] = "trn.stock_type IN ( 'si', 'ar', 'erp_sale_inv', 'erp_purchase_inv' )";
	
	$filters['WHERE'][] = "LAST_DAY( ap.doc_date ) <= LAST_DAY( '". $end ."' )";
	$filters['WHERE'][] = "ap.doc_date >= '". $start ."'";
	$sql = genCond_( $sql, $filters );
	
	
	$keep = array();
	
	$id = 0;
	foreach( $dao->fetchAll( $sql ) as $kt => $vt ) {
		
		$subSql = "
			SELECT
				
				'' as id,
				parent_id,
				tbName,
				tabName,
				pay_date,
			
				ROUND( chq_amt, 2 ) as chq_amt
			FROM (
				(
					SELECT 
						chap.parent_id,
						ap.tbName,
						'cheque' as tabName,
						ch.doc_no as ch_no,
						CASE
							WHEN ap.tbName = 'erp_ap_pay' THEN ap.doc_date
							WHEN ADDDATE( ch.doc_date, INTERVAL 1 day ) <= ap.doc_date THEN ap.doc_date
							ELSE ADDDATE( ch.doc_date, INTERVAL 1 day )
						END as pay_date,
						0 as pay_due,
						chap.id as chq_id,
						chap.amt as chq_amt
					FROM erp_ap_pay_cheque chap
					INNER JOIN erp_cheque ch ON chap.cheque_id = ch.id
					INNER JOIN erp_ap_pay ap ON chap.parent_id = ap.id
					[WHERE] 
				)
				
				UNION 
				(
					SELECT 
						chap.parent_id,
						ap.tbName,
						'bank' as tabName,
						bank.bank_account_name as ch_no,
						ap.doc_date as pay_date,
						0 as pay_due,
						chap.id as chq_id,
						chap.amt as chq_amt
					FROM erp_ap_pay_bank_account chap
					INNER JOIN erp_bank_account bank ON chap.bank_account_id = bank.id
					INNER JOIN erp_ap_pay ap ON chap.parent_id = ap.id
					[WHERE] 
				
				)
				
			) as new_tb
			
			ORDER BY 
				tabName ASC,
				chq_id ASC
			
		";
		
		$filters = array();
		$filters['WHERE'][] = "chap.parent_id = ". $vt->parent_id ."";
		
		
		$haveCompare = false;
		if( !isset( $parents[$vt->parent_id] ) ) {
		
			$parents[$vt->parent_id] = 1;
		
			$sql = "SELECT SUM( chq_amt ) as t FROM ( ". genCond_( $subSql, $filters ) ." ) as new_tb ";
			
			$total_chq_amt[$vt->parent_id] = 0;
			
			foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
				
				$total_chq_amt[$vt->parent_id] = $vc->t;
			}
		}
		
		if( $total_chq_amt[$vt->parent_id] == 0 ) {
			
			continue;
		}
		
		$sql = genCond_( $subSql, $filters );
		
		$total = array();
		foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
			
			$sql = "
				SELECT
					DATEDIFF( '". $vc->pay_date ."', '". $vt->st_date ."'  ) as t
			";
			
			
			
			$clone = clone( $vc );
			
			$clone->id = ++$id;
			$clone->company_group = $vt->company_group;
			$clone->company_id = $vt->company_id;
			$clone->ap_date = $vt->ap_date;
			$clone->trn_id = $vt->trn_id;
			$clone->trn_amt = $vt->trn_amt;
			
			$trn_amt_ave = ROUND( $vt->trn_amt * $clone->chq_amt / $total_chq_amt[$vt->parent_id], 2 );
			
			$clone->trn_amt_ave = $trn_amt_ave;
			$clone->adj = 'no';
			
			$clone->pay_due = $dao->fetch( $sql )->t;
			
			$clone->dueXamt = $clone->pay_due * $trn_amt_ave;
			$total['trn_amt_ave'][] = $trn_amt_ave;
			$total['dueXamt'][] = $clone->dueXamt;
			$clone->dueXamt_bal = array_sum( $total['dueXamt'] );
			$keep[$vt->trn_id][] = $clone;
			
		}
	
		$adj = ROUND( $vt->trn_amt - array_sum( $total['trn_amt_ave'] ), 2 );
		
		if( $adj != 0 ) {
			
			$trn_amt_ave = $adj;
			
			$clone = clone( $keep[$vt->trn_id][count($keep[$vt->trn_id])-1] );
			$clone->trn_amt_ave = $adj;
			$clone->dueXamt = $clone->pay_due * $adj;
			$total['dueXamt'][] = $clone->dueXamt;
			$clone->dueXamt_bal = array_sum( $total['dueXamt'] );
			
			$clone->adj = 'yes';
			$keep[$vt->trn_id][] = $clone;
		}
			
		$sql = "
			UPDATE erp_ap_pay_trn 
			SET 
				dueXamt = ". array_sum( $total['dueXamt'] ) ."
			WHERE id = ". $vt->trn_id ."
		";
		
		//arr( $sql );
		
		$dao->execDatas( $sql );
		
	}


	///////////////////////////////////////
	
	
	$reportTime = isset( $_REQUEST['reportTime'] )? $_REQUEST['reportTime']: 'day';
	//$reportTime = 'month';
	
	$keepC = array();
	
	$curentD = $start;
	for( $i = 1; $i <= 100; ++$i ) {
		
		
		if( $reportTime == 'month' ) {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%m/%y' ) as label,
					LAST_DAY( '". $curentD ."' ) as curentD,
					ADDDATE( LAST_DAY( '". $curentD ."' ), INTERVAL 1 day ) as nextD
					
			";
			
			$res = $dao->fetch( $sql );
			

			$cName1 = 'trn_amt'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.trn_amt * ( LAST_DAY( new_tb.ap_date ) = '". $res->curentD ."'  ) ) as ". $cName1 ."
				";
				
					

				
			$newColumns['dynamicColumnsConfig'][$cName1] = getStandardColumn( $res->label.'|บาท', $ws['trn_amt'], $forum_on_ready = NULL, $dot = 2 );
			
			
			
		
			
			
			$cName2 = 'dueXamt'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.dueXamt * ( LAST_DAY( new_tb.ap_date ) = '". $res->curentD ."'  ) ) as ". $cName2 ."
				";
				
			$newColumns['dynamicColumnsConfig'][$cName2] = getStandardColumn( $res->label.'|dueXamt', $ws['dueXamt'], $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = NULL, $column_setting = NULL, $param );
			
			
			$cName = 'ave'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "999 as ". $cName ."
			";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label.'|due', $ws['due'], $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = '{"forum_on_ready":"['. $cName2 .'] /  ['. $cName1 .']"}' );
			
			
		}
		else {
			
			$sql = "
				SELECT
					date_format( '". $curentD ."', '%d/%m/%y' ) as label,
					date_format( '". $curentD ."', '%Y-%m-%d' ) as curentD,
					ADDDATE( '". $curentD ."', INTERVAL 1 day ) as nextD	
			";
			
			$res = $dao->fetch( $sql );
			
			
			$cName1 = 'trn_amt'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.trn_amt * ( new_tb.ap_date = '". $res->curentD ."'  ) ) as ". $cName1 ."
				";
				
					

				
			$newColumns['dynamicColumnsConfig'][$cName1] = getStandardColumn( $res->label.'|บาท', $ws['trn_amt'], $forum_on_ready = NULL, $dot = 2 );
			
			
			
		
			
			
			$cName2 = 'dueXamt'. $i .'';
			$newColumns['dynamicColumnsSql'][] = "SUM( new_tb.dueXamt * ( new_tb.ap_date = '". $res->curentD ."'  ) ) as ". $cName2 ."
				";
				
			$newColumns['dynamicColumnsConfig'][$cName2] = getStandardColumn( $res->label.'|dueXamt', $ws['dueXamt'], $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = NULL, $column_setting = NULL, $param );
			
			
			$cName = 'ave'. $i .'';
		
			$newColumns['dynamicColumnsSql'][] = "999 as ". $cName ."
			";
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( $res->label.'|due', $ws['due'], $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = '{"forum_on_ready":"['. $cName2 .'] /  ['. $cName1 .']"}' );
		}
		
		if( $res->nextD > $end ) {
			
			break;
		}
		
		
		$curentD = $res->nextD;
	}
	
	
	
	
	if( true ) {
		
		
		$cName = 'trn_amt';
		
		$newColumns['dynamicColumnsSql'][] = "SUM( trn_amt  ) as ". $cName ."
		";
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม|บาท', $ws['trn_amt'], $forum_on_ready = NULL, $dot = 2  );
	
	
		
		$cName = 'dueXamt';
		
		$newColumns['dynamicColumnsSql'][] = "SUM( dueXamt ) as ". $cName ."
		";
		
		
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม|dueXamt', $ws['dueXamt'], $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum = 1, $bg = NULL, $column_setting = NULL, $param );
	
		
		
		
	
	
		
		$cName = 'dueAve';
		$newColumns['dynamicColumnsSql'][] = "999 as ". $cName ."
		";

		$sum = '{"forum_on_ready":"[dueXamt] /  [trn_amt]"}';
		$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม|due', $ws['due'], $forum_on_ready = NULL, $dot = 2, $inputformat = 'money', $a = 'R', $sum, $bg = NULL, $column_setting = NULL, $param = array()   );
	
	
	}
	
	return $newColumns;
		
}





//
//
function setCostSaleColumns() {
	
	global $dao;
	
	$sql = "
		SELECT 
			report_order,
			tbName,
			IF( stock_act_id = 34, 1, 
				IF( stock_act_id = 28, 12, 
				
					IF( stock_act_id = 9, 24, 
						IF( stock_act_id = 5, 20, 
							IF( stock_act_id = 38, 33, 
								IF( stock_act_id = 39, 14, stock_act_id )
							
							) 
						)
					)
				)
			) as new_id,
			
			IF( stock_act_id IN( 28, 12, 9, 24, 5, 20 ), -1, factor ) as factor_,
			

			GROUP_CONCAT( stock_act_id ) as stock_act_id_,
			stock_act_name,
			report_label
		FROM erp_stock_act
		WHERE tbName != ''
		GROUP BY 
			new_id
	 
		ORDER BY 
			factor_ DESC, 
			report_order ASC
		 
	";
	
	
	$cName = 'total_before';
	
	$newColumns['dynamicColumnsSql'][] = "
	
		IF(
			row_id = 4,
			SUM( new_tb.total * ( t = 'before' ) ) / SUM( new_tb.qtys * ( t = 'before' ) ),
			
			SUM( new_tb.total * ( t = 'before' ) )
		) as total_before
	";
	
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'ยกมา', $w = 22, $forum_on_ready = NULL, $dot = 2  );
	
	
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		
		$sql = "
			UPDATE sac2015_test.erp_stock_act SET 
			
			report_order = ". ( $ka * 5 ) ."
			WHERE stock_act_id IN ( ". $va->stock_act_id_ ." )
		";
		

		$cName = $va->tbName;
		
		$w = 19;
		if( $va->factor_ == 1 ) {
			
			
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn(  'รับ|' . $va->report_label, $w, $forum_on_ready = NULL, $dot = 2  );
		}
		else {
			
			
			
			$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn(  'จ่าย|' . $va->report_label, $w, $forum_on_ready = NULL, $dot = 2  );
		}

		
		
		
		$newColumns['dynamicColumnsSql'][] = "
			IF(   
				row_id = 4,
				SUM( new_tb.total * ". $va->factor_ ." * ( t = 'intime' ) * ( act_id IN ( ". $va->stock_act_id_ ." )  ) ) / SUM( new_tb.qtys * ". $va->factor_ ." * ( t = 'intime' ) * ( act_id IN ( ". $va->stock_act_id_ ." )  ) ),
				
				SUM( new_tb.total * ". $va->factor_ ." * ( t = 'intime' ) * ( act_id IN ( ". $va->stock_act_id_ ." )  ) ) 
			) as ". $cName ."
		";
	
	}

	
	
	
	$cName = 'total';
	
	$newColumns['dynamicColumnsSql'][] = "
	
		IF(
				
			row_id = 4,
			SUM( new_tb.total ) / SUM( new_tb.qtys ),
			SUM( new_tb.total )
		) as ". $cName ."
	";
	
	$newColumns['dynamicColumnsConfig'][$cName] = getStandardColumn( 'รวม', $w = 22, $forum_on_ready = NULL, $dot = 2  );
	
	return $newColumns;	

	
}

//
//
function getExtension( $file_name ) {
	$extension = explode( '.', $file_name );
	return $extension[count( $extension ) - 1];
}
//
//
function showtime( $time ) {
	try {
		$date = new DateTime( $time );
	}
	catch ( Exception $e ) {
		echo $e->getMessage();
	}

	$mytime = $date->format( 'd/m/Y H:i' );
	return  $mytime;
}


function getCompanyFilters( $param = array() ) {
	
	
	
	
	if( !isset( $param['prefix'] ) ) {
		$param['prefix'] = '';
	}
	
	if( !empty( $param['g'] ) ) {
		/*
		$filters = "
			". $param['prefix'] ."admin_company_id IN ( 
				SELECT 
					company_id
				FROM admin_company
				WHERE company_group_id = ". $_SESSION['user']->company_group_id ."  
		)";	
		*/
		
	}
	else {
	

		$filters = $param['prefix'] ."admin_company_id IN ( ". $_SESSION['u']->user_company_id ." )";	
	}
	
	
	return $filters;

}



//
//
function getXmlStyle() {
	
	$style['label'] = array( 
		'id' => 's73',
		'style' => '
			<Style ss:ID="s73">
			<Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
			<Borders>
			<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
			</Borders>
			<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
			ss:Bold="1"/>
			</Style>
		' 
	);
	
	$style['header'] = array( 
		'id' => 's64',
		'style' => '
			<Style ss:ID="s64">
			<Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
			<Borders>
			<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
			</Borders>
			<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
			ss:Bold="1"/>
			</Style>
		' 
	);

	$style['text_left'] = array( 
		'id' => 's66',
		'style' => '
			<Style ss:ID="s66">
			<Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
			<Borders>
			<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
			</Borders>
			<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
			</Style>
		' 
	);
	
	$style['number'] = array( 
		'id' => 's68',
		'style' => '
			<Style ss:ID="s68">
			<Alignment ss:Horizontal="Right" ss:Vertical="Bottom"/>
			<Borders>
			<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
			<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
			</Borders>
			<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
			<NumberFormat ss:Format="Standard"/>
			</Style>
		' 
	);
	
	$style['Default'] = array( 
		'id' => 'Default',
		'style' => '
			<Style ss:ID="Default" ss:Name="Normal">
			<Alignment ss:Vertical="Bottom"/>
			<Borders/>
			<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
			<Interior/>
			<NumberFormat/>
			<Protection/>
			</Style>
		' 
	);

	return $style;
}

//$param['discount'] = 10+10%
//$param['price'] = 778
function getDiscountBath( $param = array() ) {
	
	if( is_numeric( strpos( $param['discount'], '%' ) )  ) {
		
		$discount = str_replace( '%', '', $param['discount'] ); 
		
		$ex = explode( '+', $discount);
		
		$price = $param['price'];
		foreach( $ex as $ke => $ve ) {
			$price *= ( 100 - $ve ) / 100;
		}
		
		$discount_bath = $param['price'] - $price;
		
	}
	else {
		
		$discount_bath = $param['discount'];
	}
	
	return $discount_bath;

}

//
//$param['parent_table'] = sac_purchase_order
//$param['sub_table'] = sac_purchase_order_dt
function autoUpdateVat( $param = array() ) {
	 
	$dao = getDb();
	
	$sql = "
	
		SELECT 
			dt.qty * dt.price as amt,
			dt.id,
			dt.discount
		FROM ". $param['sub_table'] ." dt
	";
	
	
	///arr( $sql );
	$sqlUnion = array();
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		//arr( $va );
		$param['discount'] = $va->discount;
		$param['price'] = $va->amt;
		

		$va->amt -= getDiscountBath( $param );
	
		$sqlUnion[$va->id] = "
			SELECT 
				". $va->amt ." as amt,
				". $va->id ." as id
		";
	}
	
	
	//update detail
	$sql = "
		UPDATE ". $param['sub_table'] ." dt 
		INNER JOIN ". $param['parent_table'] ." po ON dt.parent_id = po.id
		INNER JOIN (
			". implode( ' UNION ', $sqlUnion ) ."
			 
		) as new_tb ON dt.id = new_tb.id
	
		SET 
			before_vat = IF( po.vat_type = 1, new_tb.amt / ( 1 + ( po.vat_rate / 100 ) ), new_tb.amt ) , 
			after_vat = IF( po.vat_type = 1, new_tb.amt, new_tb.amt * ( 1 + ( po.vat_rate / 100 ) ) ) 
	";
//arr( $sql );	
	$dao->execDatas( $sql );
	
	//update header
	$sql = "
		UPDATE ". $param['parent_table'] ." po 
		INNER JOIN (
			SELECT 
				SUM( dt.before_vat ) as total_before_vat,
				SUM( dt.after_vat ) as total_after_vat,
				dt.parent_id
			FROM ". $param['sub_table'] ." dt
			GROUP BY 
				dt.parent_id
		) as new_tb ON po.id = new_tb.parent_id
		SET 
			purchase_order_total_before_vat = new_tb.total_before_vat, 
			purchase_order_vat_bath = new_tb.total_before_vat * ( po.vat_rate / 100 ), 
			purchase_order_total_after_vat = new_tb.total_before_vat * ( 1+ ( po.vat_rate / 100 ) )
	";
	
	$dao->execDatas( $sql );
	
	
	//
	// adjust vat
	$sql = "
		UPDATE ". $param['sub_table'] ." dt 
		INNER JOIN ". $param['parent_table'] ." po ON dt.parent_id = po.id
		INNER JOIN (
			SELECT 
				MAX( dt.id ) as adjust_id,
				SUM( dt.after_vat ) as total_after_vat	
			FROM ". $param['sub_table'] ." dt
			GROUP BY 
				dt.parent_id
			
		) as new_tb ON dt.id = new_tb.adjust_id
		SET
			dt.after_vat = dt.after_vat + (
			
				po.purchase_order_total_after_vat - new_tb.total_after_vat
			)
	";
	//arr( $sql );
	$dao->execDatas( $sql );
	
}




//
//rci_stock_dt
function importGetOrderDt( $param = array() ) {
	
	$dao = getDb();
	
	$main_id = $param['parent_id'];

	$purchase_order_id = $param['data']['purchase_order_id'];
	
	$sql = "
		INSERT INTO sac_purchase_receive_dt (  
			lock_dt_id, 
			um, 
			discount, 
			purchase_items_id, 
			parent_id, 
			qty, 
			price,
			lock_parent_id
			 
		)  
		SELECT 
			podt.id as lock_dt_id, 
			podt.purchase_items_um as um, 
			podt.discount, 
			podt.purchase_items_id, 
			". $param['parent_id'] ." as parent_id, 
			podt.qty  - IFNULL( (
				SELECT 
					SUM( qty ) 
				
				FROM sac_purchase_receive_dt 
				
				WHERE lock_dt_id = podt.id
			
			), 0 ) as diff_qty, 
			price,
			parent_id as lock_parent_id
		FROM sac_purchase_order_dt podt
		WHERE podt.parent_id = ". $purchase_order_id ."
		HAVING diff_qty > 0

	";
	
//
	
	$dao->execDatas( $sql );
	
	
	$sql = "
		UPDATE sac_purchase_receive r
		INNER JOIN sac_purchase_order po ON r.purchase_order_id = po.id
		SET 
			r.vat_rate = po.vat_rate, 
			r.vat_type = po.vat_type 
		WHERE r.id = ?
	";
//arr( $sql );	
	
	$dao->execDatas( $sql, array( $main_id ) );
}

//$param['LPAD'] = 4;
//$param['column'] = 'doc_no';
//$param['table'] = 'erp_ap_pay';
//$param['template'] = 'DATE_FORMAT( ADDDATE( NOW(), INTERVAL 543 year ), 'PR%y%m-[find]' ';
function genDocNo( $param = array() ) {

	$param['LPAD'] = 4;
	
	
	
	if( !isset( $param['column'] ) ) {
		
		$param['column'] = 'doc_no';
	}
	if( !isset( $param['table'] ) ) {
		
		$param['table'] = 'erp_sale_order';
	}
	
	if( !isset( $param['pri_key'] ) ) {
		
		$param['pri_key'] = 'id';
	}
	
  
	
	if( !isset( $param['prefix'] ) ) {
		
		$param['prefix'] = $param['table'];
	}
	
	if( $param['column'] != 'doc_no' ) {
		 
		
		$param['gogo'] = 'DATE_FORMAT( ADDDATE( NOW(), INTERVAL 543 year ), \''. $param['prefix'] .'%yC'. $_SESSION['u']->user_company_id .'-[find]\' ) ';
		
	}
	else {
		
		$param['gogo'] = 'DATE_FORMAT( ADDDATE( NOW(), INTERVAL 543 year ), \''. $param['prefix'] .'%y%mC'. $_SESSION['u']->user_company_id .'-[find]\' ) ';
	}
	 
	
	
	
	$sql = "
		SELECT
			replace(
				new_tb.myText,
				'[find]',
				LPAD(
					IFNULL(
						(
							SELECT
								MAX( REPLACE( REPLACE( ". $param['column'] .", new_tb.front, '' ), new_tb.back, '' ) ) + 1
							FROM ". $param['table'] ." 
							[WHERE] 
						),
						1
					),
					". $param['LPAD'] .",
					0
				)
			) as t	
		FROM (
			SELECT
				SUBSTRING_INDEX( new_tb.myText, '[find]', 1 ) as front,
				SUBSTRING_INDEX( new_tb.myText, '[find]', -1 ) as back,
				new_tb.myText
			FROM (
				SELECT
					". $param['gogo'] ." as myText
			) as new_tb
		) as new_tb
	";
	
	$filters = array();
	$filters['WHERE'][] = "REPLACE( REPLACE( ". $param['column'] .", new_tb.front, '' ), new_tb.back, '' ) REGEXP '^[0-9]+$'";
	
	if( !empty( $param['replace'] ) ) {
		$filters['WHERE'][] = "". $param['pri_key'] ." != ". $param['replace'][$param['pri_key']] ."";
	}
	
	$sql = genCond_( $sql, $filters );	
 
//	arr( $sql );
	return $sql;
	
}


//
//
function getConfig_( $config_id ) {

	$dao = getDb();
	
	$sql = "
		SELECT
			b.config_columns_id,
			b.config_columns_name,
			a.*
		FROM admin_model_config a
		LEFT JOIN admin_model_config_columns b ON a.config_doc_head_id = b.config_columns_id
		WHERE a.config_id = " . $config_id;


	foreach( $dao->fetchAll( $sql ) as $kres => $res ) {

		$config = json_decode( $res->config_detail );
		
		
		///
		foreach( $config as $kc => $vc ) {
			
			$config->$kc = stripcslashes( $config->$kc );
		}

	//	exit;

		if ( !empty( $config->in_rows_sql ) ) {

			$json = json_decode( $config->in_rows_sql );
			
			$json->sql = isset( $config->row_sql )? $config->row_sql: NULL;

			$config->in_rows_sql = json_encode( $json );
		}

		$config->signalBlocks = !empty( $config->signalBlocks )? json_decode( $config->signalBlocks ) : array();

		$config->database = $res->config_database;

		$config->config_comment = $res->config_comment;

		$config->config_doc_head = $res->config_columns_name;

		$sql = "
			SELECT
				a.div_class,
				a.config_columns_id,
				a.config_columns_name,
				a.config_columns_w,
				a.config_columns_label,
				a.config_columns_position,
				IF(
					a.config_override_id IS NULL,
					a.config_columns_detail,
					(
						SELECT
							config_columns_detail
						FROM admin_model_config_columns
						WHERE config_columns_id = a.config_override_id
					)
				) as config_columns_detail
			FROM admin_model_config_columns a
			WHERE a.config_id = ". $config_id ."
			ORDER BY a.config_columns_order ASC
		
		";
		


		$keep = array();

		foreach ( $dao->fetchAll( $sql ) as $ka => $va ) {
			

//arr($va->config_columns_id);
//arr( json_decode( stripcslashes( $va->config_columns_detail ) ) )  ;

			if( empty( $va->config_columns_detail  ) ) {
				continue;
			}
			$keep[$va->config_columns_name] = json_decode( stripcslashes( $va->config_columns_detail ) );
	///arr( $ka );
	///arr( $va );

//arr(json_decode( stripcslashes( $va->config_columns_detail ) ));

			///if() {}
			
			$name = 0;
			
			$keep[$va->config_columns_name]->require = $keep[$va->config_columns_name]->$name;
			
			$name = 7;
			
			$keep[$va->config_columns_name]->default_val = $keep[$va->config_columns_name]->$name;
			
			 
			
			
			 
			$keep[$va->config_columns_name]->position = $va->config_columns_position;
			//$keep[$va->config_columns_name]->position = 'TL';
		
			
			$keep[$va->config_columns_name]->w = $va->config_columns_w;

			$keep[$va->config_columns_name]->config_columns_id = $va->config_columns_id;

			$keep[$va->config_columns_name]->label = $va->config_columns_label;
			$keep[$va->config_columns_name]->div_class = $va->div_class;
		}
		
		//exit;
		
		$config->columns = convertObJectToArray( $keep );

		$config->config_id = $res->config_id;
		
		

		return $config;
	}
	
	return false;
}



 
//
//
function getMySql( $config, $getView = NULL, $status_filter = array(), $operation = 'LIKE', $tb_parent = NULL, $outerFuncFilter = array(), $sort = NULL, $search = true, $close_all_filters = false, $txt_filter = '%filter;', $txt_cond = 'HAVING', $main_config = array(), $getOnlyFilter = false ) {

	$dao = getDb();

	$pri_key = isset( $config->pri_key )? $config->pri_key: NULL;

	$config->tb_main = isset( $config->tb_main )? $config->tb_main: NULL;

	$multi_tables = !empty( $config->multi_tables )? json_decode( $config->multi_tables ): array();

	if( !empty( $outerFuncFilter ) )
		$data['filters'][] = implode( ' AND ', $outerFuncFilter );


	if ( $search ) {

		if ( !empty( $_REQUEST['multi-check']['name'] ) && !empty( $_REQUEST['multi-check']['val'] ) ) {

			$data['filters'][] = $_REQUEST['multi-check']['name'] ." IN ( '". implode( "', '", $_REQUEST['multi-check']['val'] ) ."' )";
		}

		if ( empty( $_REQUEST['clearFilterSort'] ) ) {

			foreach ( $multi_tables as $ka => $va ) {

				if ( ( isset( $_REQUEST[$ka] ) && is_numeric( $_REQUEST[$ka] ) ) || !empty( $_REQUEST[$ka] ) ) {

					if ( is_array( $_REQUEST[$ka] ) ) {

						$data['filters'][] = $ka ." IN ( '". implode( '\',\'', $va ) ."' )";

					} else {



						$va = addslashes( addslashes( trim( str_replace( array( ' ', '"' ), '%', $_REQUEST[$ka] ) ) ) );


						$va = str_replace( ' ', '%', $va );

						if ( $operation == 'LIKE' ) {
							if( substr( $va, 0, 1 ) == '|' ) {
								
								$data['filters'][] = $ka . " ". $operation ." '". substr( $va, 1 ) ."%'";
							}
							else {
								
								$data['filters'][] = $ka . " ". $operation ." '%". $va ."%'";
							}
							
							
							
							
						}
						else
							$data['filters'][] = $ka . " ". $operation ." '". $va ."'";

					}
				}
			}
		}
	}
	

	if ( !$close_all_filters ) {

		$showColumns = $dao->showColumns( $config->tb_main );
 
		if ( in_array( 'admin_company_id', $showColumns ) ) {


			$data['filters'][] = "admin_company_id = " . $_SESSION['u']->user_company_id;

		}
		
		if ( in_array( 'user_id', $showColumns ) && in_array( 'post', $showColumns ) ) {
			
			$data['filters'][] = "( user_id = ". $_SESSION['user_id'] ." OR post = 1 )";
			
			
		}
		
		
	}

	$keep = array();
	if ( !empty(  $config->more_filter_sql  ) ) {

		$more_filter_sql = json_decode( $config->more_filter_sql );

		//
		//
		if ( !empty( $more_filter_sql->param ) ) {
	
			$keep = getFilterByType( $more_filter_sql->param );
		}
		
		if( !empty( $more_filter_sql->sql ) )
			$data['filters'][] = $more_filter_sql->sql;
	}
	
	if( $getOnlyFilter )
		return $data['filters'];

	//
	//
	$sql_filter = '';
	if ( !empty( $data['filters'] ) ) {

		if ( !empty( $data['filters'] ) )
			$sql_filter = $txt_cond . " " . implode( ' AND ', $data['filters'] );
	}

	$sql = str_replace( array( $txt_filter ), array(  $sql_filter ), $config->main_sql );

	foreach ( $keep as $ka => $va ) {
		$sql = str_replace( $ka, $va, $sql );
	}


	if( empty( $main_config ) ) {

		$main_config = new stdClass;
		$main_config->pri_key = NULL;
		$main_config->tb_main = NULL;
	}

	if ( !empty( $_REQUEST['sort'] ) ) {

		$sort = 'ORDER BY '. $_REQUEST['sort'][0] .' '. $_REQUEST['sort'][1];
	}
	else if ( !empty( $config->main_sql_sort ) ) {

		$sort = $config->main_sql_sort;
	}
	else if ( is_null( $sort ) ) {

		$sort = 'ORDER BY ' . $config->pri_key . ' DESC';
	}

	$arr_replace = array(
		$txt_filter => $sql_filter,
		'[tb_main]' => $config->tb_main,
		'[tb_parent]' => $main_config->tb_main,
		'[pri_key]' => $pri_key,
		'[main_prikey]' => $main_config->pri_key,
		'[sort]' => $sort,
		'[FILE_URL]' => FILE_URL
	);

	if ( isset( $main_config->current_tb_parent ) ) {

		$arr_replace['[current_tb_parent]'] = $main_config->current_tb_parent;
		$arr_replace['[current_main_prikey]'] = $main_config->current_pri_key;
	}

	$keep = array();
	if ( !empty(  $config->main_sql_str_replace  ) ) {
		$main_sql_str_replace = json_decode( $config->main_sql_str_replace );

		foreach ( getFilterByType( $main_sql_str_replace->param ) as $ka => $va ) {

			$arr_replace[$ka] = $va;
		}
	}

	//
	$sql = str_replace(
		array_keys( $arr_replace ),
		$arr_replace,
		$sql
	);

	
	$filters = array();
	if( !empty( $getView->permission_config ) ) {
		
	
		foreach( json_decode( $getView->permission_config ) as $kc => $vc ) {
		
			if( in_array( $kc, array( 'WHERE', 'HAVING' ) ) ) {
				
				$filters[$kc][] =  genJsonSql( $vc, $old_data = array(), $config = NULL, $parent_data = NULL );	
			}
			
								
		}
		
		
	}
	$sql = genCond_( $sql, $filters );
// arr( $sql );
	return $sql;
}


//
//
function stockConfig( $tbName = NULL ) {
	
	$dao = getDb();
	
	$sql = "
		SELECT 
			doc_priority,
			stock_act_id as act_id,
			factor,
			tbName
		FROM erp_stock_act
		WHERE tbName = '". $tbName ."'
	";
	
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
		return convertObJectToArray( $vc );
	}

	$config['move_out'] = array( 'doc_priority' => 11, 'act_id' => 28, 'factor' => -1 );
	$config['move_in'] = array( 'doc_priority' => 13, 'act_id' => 12, 'factor' => 1 );
	$config['ai'] = array( 'doc_priority' => 5, 'factor' => 1, 'act_id' => 5 );
	$config['ao'] = array( 'doc_priority' => 6, 'factor' => -1, 'act_id' => 20 );
	$config['erp_purchase_inv'] = array( 'doc_priority' => 10, 'factor' => 1, 'act_id' => 1 );
	$config['erp_purchase_return'] = array( 'doc_priority' => 20, 'factor' => -1, 'act_id' => 18 );
	$config['produce_out'] = array( 'doc_priority' => 25, 'factor' => -1, 'act_id' => 17 );
	$config['produce_out_return'] = array( 'doc_priority' => 26, 'factor' => 1, 'act_id' => 4 );
	$config['produce_in'] = array( 'doc_priority' => 27, 'factor' => 1, 'act_id' => 2 );
	$config['erp_sale_return'] = array( 'doc_priority' => 40, 'factor' => 1, 'act_id' => 3 );
	$config['erp_sale_inv'] = array( 'doc_priority' => 60, 'factor' => -1, 'act_id' => 16 );
	$config['erp_sale_inv'] = array( 'doc_priority' => 10, 'factor' => 1, 'act_id' => 16 );
	return $config[$tbName];
	
}


function updateStockCost( $param ) {
	
	$dao = getDb();
	
	$sql = "
		UPDATE erp_stock_dt 
		SET 
			cost_amt = IF( act_id IN ( 1, 3 ), before_vat, 0 )
				
	
		
	";
	

	$dao->execDatas( $sql );
	
	
	$sql = "
	
		SELECT 
			admin_company_id,
			date_format( doc_date, '%Y-%m-01' ) as t 
			
		FROM erp_stock_dt 
		
		GROUP by 
			admin_company_id,
			t 
		
		ORDER BY 
			t ASC
	";
	
	//arr( $sql );
	
	
	
	
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		
		$date = $va->t;
	
		
		
		for( $i = 1; $i <= 5; ++$i ) {
			/*
			$sql = "
			
				SELECT 
					* 
				FROM erp_stock_dt 
				
				WHERE cost_amt = 0
				
				AND doc_date >= '". $date ."'
				
				LIMIT 0, 1
				
			";
			*/
			//$res = $dao->fetch( $sql );
	///arr( $res );		
			//if( !$res ) {
				
				//break;
			//}
			

			//send cost of buy and produce 
			// receive is sell produce_out buy_return
			// same doc same company
			
			$sql = "
				UPDATE erp_stock_dt dt
				INNER JOIN (
					SELECT 
						product_id,
						SUM( cost_amt * factor ) / SUM( qty  * factor ) as cost_ave
					FROM erp_stock_dt
					WHERE 
						(
						
							( act_id IN ( 1, 2, 3 )  AND LAST_DAY( doc_date ) = LAST_DAY( '". $date ."'  )    )
						
						
							OR 
							
							doc_date < '". $date ."'
						
						)
						
						 
						
						AND admin_company_id = ". $va->admin_company_id ."
					
						
						
					GROUP BY 
						product_id
				) as new_tb ON dt.product_id = new_tb.product_id
				SET 
					dt.cost_amt = new_tb.cost_ave * dt.qty
					
				WHERE dt.act_id IN ( 16, 17, 18 )	
				AND LAST_DAY( dt.doc_date ) = LAST_DAY( '". $date ."'  )
				AND dt.admin_company_id = ". $va->admin_company_id ."
				
			";
			
			
			//arr( $sql );
				
			$dao->execDatas( $sql );
		
		
			//send cost of produce_out 
			// receive is produce_in
			// same doc same company
			$sql = "
				UPDATE erp_stock_dt dt
				INNER JOIN (
					SELECT 
						parent_id,
						SUM( qty ) as total_qty
						
					FROM erp_stock_dt
					WHERE act_id IN ( 2 )
					
					GROUP BY 
						parent_id
				) as new_tt ON dt.parent_id = new_tt.parent_id
				
				INNER JOIN (
					SELECT 
						parent_id,
						SUM( cost_amt ) as total_cost_amt
						
					FROM erp_stock_dt
					WHERE act_id IN ( 17 )
					AND LAST_DAY( doc_date ) = LAST_DAY( '". $date ."'  ) 
					AND admin_company_id = ". $va->admin_company_id ."
					GROUP BY 
						parent_id
				) as new_tb ON dt.parent_id = new_tb.parent_id
				SET 
					dt.cost_amt = new_tb.total_cost_amt * dt.qty / new_tt.total_qty
					
				WHERE dt.act_id IN ( 2 )	
				 
				
			";
			
	 //	arr( $sql );
			$dao->execDatas( $sql );
		}
	}
}




//
//
function updateProductGl( $param ) {

	$dao = getDb();

	$filters = array();
	if (isset($param['updateAll'])) {
	} else {

		//$filters['WHERE'][] = "p.id = " . $param['parent_id'] . "";
	}

	$sql = "
		UPDATE sma_products p
		
		
		SET
		 
		
			p.buy_gl_id = 589,
			p.return_buy_gl_id = 589,
			p.sale_gl_id = 2178,
			p.return_gl_id = 2193,		
			p.fullname = CONCAT( 
				p.code, 
				
				IF( p.grade = '' OR p.grade IS NULL, ' ', CONCAT(  ' (', p.grade, ') ' ) ) , 
				
				' ', 
				
				p.name, ' (',p.stock_um, ') ' 
				
			)
		[WHERE]
	";
//
	$sql = genCond_($sql, $filters);

//arr( $sql );

	$dao->execDatas($sql);



	$sql = "
		UPDATE erp_product_group_second 
		SET 
			id = product_group_second_id
	";

	//$dao->execDatas($sql);
}





//
//$param['data']['doc_date'] = '1980-01-01'
//$param['main_data_before']->doc_date,
//$param['data']['doc_no']
//$param['main_data_before']->doc_no
function insertGlTrn( $param = array() ) {

	$dao = getDb();
	
	$tbGlDt = 'erp_gl_trn_dt';
	
	$dates[] = $param['data']['doc_date'];

	if( isset( $param['main_data_before']->doc_date ) ) {

		$dates[] = $param['main_data_before']->doc_date;
	}

	$doc_date = MIN( $dates );
	
	
	if( isset( $param['main_data_before']->doc_no ) ) {
		
		$sql = "
			SELECT * FROM ". $tbGlDt ." 
			WHERE doc_date >= '". $doc_date ."'
			AND doc_no = '". $param['main_data_before']->doc_no ."';
		";
		
		$backup = array();
		
		foreach( $dao->fetchAll( $sql ) as $kb => $vb ) {
			$backup[$vb->id] = $vb;
			
		}
		
		
		$sql = "
			DELETE FROM ". $tbGlDt ." 
			WHERE doc_date >= '". $doc_date ."'
			AND doc_no = '". $param['main_data_before']->doc_no ."';
		";
		
		$dao->execDatas( $sql );
		
	}
	
	//arr($backup);
	
	$sql = "
		SELECT
			dt.*
		FROM ". $tbGlDt ." dt
		WHERE dt.doc_date < '". $doc_date ."'
		ORDER BY order_number DESC
		LIMIT 0, 1
	";
 
	$order_number = 0;
	foreach( $dao->fetchAll( $sql ) as $kb => $vb ) {
		$order_number = $vb->order_number;

	}
//erp_product
	$replace = array();
	$replace['WHERE'][] = "dt.doc_date >= '". $doc_date ."'";
	$replace['HAVING'][] = "tbName NOT IN ( 'produce_out', 'produce_in', 'produce_out_return', 'move', 'move_in', 'move_out' 'ai', 'ao', 'lot' )";
	$replace['HAVING'][] = "( debit + credit ) != 0";
	$replace['HAVING'][] = "use_it = 1";
	$replace['HAVING'][] = "doc_no = '". $param['data']['doc_no'] ."'";

	$sql = "
		SELECT
			new_tb.id,
			new_tb.gName,
			new_tb.doc_date,
			new_tb.doc_no,
			new_tb.gl_id,
			new_tb.tbName as tb_parent,
			
			
			if( new_tb.tbName = 'erp_ap_pay', 
				new_tb.remark, 
				CONCAT( new_tb.remark )

			) as remark,
			

			IF( new_tb.debit < 0, new_tb.debit * -1,  IF( new_tb.credit < 0, 0, new_tb.credit ) ) as credit,
			IF( new_tb.debit < 0, 0, IF( new_tb.credit < 0, new_tb.credit * -1, new_tb.debit ) ) as debit,
			new_tb.insert_type,
			new_tb.admin_company_id,
			product_group,
			new_tb.close_month,
			new_tb.parent_id
		FROM (
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'promotion' ) as gName,
					dtt.gl_id,
					3 as type,
					999 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					dtt.remark,
					( dtt.amt * ( dtt.factor = 1 ) ) as debit,
					( dtt.amt * ( dtt.factor = -1 ) ) as credit,
					IF( dtt.factor = 0, 0, 1 ) as use_it,
					dtt.id as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_gl_trn_promotion dtt
				LEFT JOIN erp_stock dt ON dtt.parent_id = dt.id
				[WHERE]
				
				[HAVING]
			)
			UNION
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'vat_pay' ) as gName,
					IF( dt.tbName = 'erp_ap_pay', 2164, 502 )  as gl_id,
					3 as type,
					999 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_ap_pay' as tbName,
					CONCAT( 'หักภาษี ณ ที่จ่าย'  ) as remark,
					IF( dt.tbName = 'erp_ap_pay', 0, SUM( pVat.amt ) ) as debit,
					IF( dt.tbName = 'erp_ap_pay', SUM( pVat.amt ), 0 ) as credit,
					dt.proove_bt as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_ap_pay_vat pVat 
				LEFT JOIN aa_payment_vat v ON pVat.cheque_id = v.id
				LEFT JOIN erp_ap_pay dt ON pVat.parent_id = dt.id
				
				
				[WHERE]
				GROUP BY
					gl_id,
					type,
					
					doc_date,
					doc_no,
					tbName,
					remark,
					use_it,
					dt.admin_company_id
				[HAVING]
			)
			UNION
		
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'chq_pay' ) as gName,
					CONCAT( IF( dt.tbName = 'erp_ap_pay', 2156, 461 ) ) as gl_id,
					3 as type,
					999 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_ap_pay' as tbName,
					CONCAT( IF( dt.tbName = 'erp_ap_pay', 'จ่ายหนี้ด้วยเช็ค ' , 'รับชำระหนี้ด้วยเช็ค' )   ) as remark,
					IF( dt.tbName = 'erp_ap_pay', 0, SUM( paychq.amt ) ) as debit,
					IF( dt.tbName = 'erp_ap_pay', SUM( paychq.amt ), 0 ) as credit,
					dt.proove_bt as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_ap_pay_cheque paychq 
				LEFT JOIN erp_cheque chq ON paychq.cheque_id = chq.id
				LEFT JOIN erp_ap_pay dt ON paychq.parent_id = dt.id

				[WHERE]
				GROUP BY
					gl_id,
					type,
				
					doc_date,
					doc_no,
					tbName,
					remark,
					use_it,
					dt.admin_company_id
				[HAVING]
			)
		
			UNION
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'company_pay' ) as gName,
					IF( dt.tbName = 'erp_ap_pay', c.supplier_gl_id, c.customer_gl_id )   as gl_id,
					3 as type,
					st.company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_ap_pay' as tbName,
					IF( dt.tbName = 'erp_ap_pay', 'จ่ายชำระหนี้ให้' , 'รับชำระหนี้จาก' )   as remark,
					IF( dt.tbName = 'erp_ap_pay', SUM( dtt.amt ), 0 ) as debit,
					IF( dt.tbName = 'erp_ap_pay', 0, SUM( dtt.amt ) ) as credit,
					( dt.proove_bt AND dtt.stock_type IN ( 'erp_sale_inv', 'erp_purchase_inv' ) ) as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_ap_pay_trn dtt  
				LEFT JOIN erp_stock st ON dtt.lock_parent_id = st.id
				LEFT JOIN erp_ap_pay dt ON dtt.parent_id = dt.id
				LEFT JOIN erp_company c ON st.company_id = c.company_id
				[WHERE]
				GROUP BY
					gl_id,
					type,
					company_id,
					doc_date,
					doc_no,
					tbName,
					use_it,
					dt.admin_company_id,
					dtt.stock_type
				[HAVING]
			)
			UNION
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'fff' ) as gName,
					IF( 
						dt.tbName = 'erp_ap_pay', 
						IF( dtt.stock_type = 'erp_purchase_return', c.supplier_return_gl_id, c.customer_gl_id ),
						IF( dtt.stock_type = 'erp_sale_return', c.customer_return_gl_id, c.supplier_gl_id )
 
					)  as gl_id,
					
					3 as type,
					st.company_id as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_ap_pay' as tbName,
					IF( dt.tbName = 'erp_ap_pay', 'จ่ายชำระหนี้ให้  ' , 'รับชำระหนี้จาก' )   as remark,
					IF( dt.tbName = 'erp_ap_pay', 0, SUM( dtt.amt ) ) as debit,
					IF( dt.tbName = 'erp_ap_pay', SUM( dtt.amt ), 0 ) as credit,
					dt.proove_bt as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_ap_pay_trn dtt 
				LEFT JOIN erp_stock st ON dtt.lock_parent_id = st.id
				LEFT JOIN erp_ap_pay dt ON dtt.parent_id = dt.id
				LEFT JOIN erp_company c ON st.company_id = c.company_id
				WHERE dtt.stock_type IN ( 'erp_sale_return', 'erp_purchase_return' )
				GROUP BY
					gl_id,
					type,
					company_id,
					doc_date,
					doc_no,
					tbName,
					use_it,
					dt.admin_company_id
				[HAVING]
			)
			
			UNION
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'lotcebit' ) as gName,
					p.buy_gl_id as gl_id,
					1 as type,
					999 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					act.stock_act_name as remark,
					0 as debit,
					SUM( dt.before_vat ) as credit,
					1 as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.parent_id as parent_id
				FROM erp_stock_dt dt
				LEFT JOIN erp_stock_act act ON dt.act_id = act.stock_act_id
				LEFT JOIN sma_products p ON dt.product_id = p.id
				WHERE dt.doc_date >= '". $doc_date ."' 
				AND dt.tbName IN ( 'lot'  )
				GROUP BY
					gl_id,
				
					doc_date,
					doc_no,
					tbName,
					remark,
					dt.admin_company_id
				[HAVING]
			)
			UNION
		
			(
				SELECT
					
			
				
					'auto' as insert_type,
					CONCAT( 'lotdebit' ) as gName,
					p.buy_gl_id as gl_id,
					1 as type,
					199 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					act.stock_act_name as remark,
					SUM( dt.before_vat ) as debit,
					0 as credit,
					1 as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.parent_id as parent_id
				FROM erp_stock_dt dt
				LEFT JOIN erp_stock_act act ON dt.act_id = act.stock_act_id
				LEFT JOIN sma_products p ON dt.product_id = p.id
				WHERE dt.doc_date >= '". $doc_date ."' 
				AND dt.tbName IN ( 'lot'  )
				GROUP BY
					gl_id,
					
					doc_date,
					doc_no,
					tbName,
					remark,
					dt.admin_company_id
				[HAVING]
			)
			UNION
		
		
		
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'lyyyyyyyy' ) as gName,
					IF( dt.tbName IN ( 'erp_purchase_inv', 'ap' ), c.supplier_gl_id,

						IF( dt.tbName = 'erp_sale_return', c.customer_return_gl_id,

							IF( dt.tbName = 'erp_sale_inv', c.customer_gl_id, c.supplier_return_gl_id )
						)
					) as gl_id,
					3 as type,
					dt.company_id as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					IF( dt.tbName IN ( 'erp_purchase_inv'  ), 'ซื้อสินค้าจาก ', 
					
						IF( dt.tbName IN ( 'erp_sale_inv'  ), 'ขายสินค้า', 
						
							IF( dt.tbName IN ( 'erp_sale_return'  ), 'รับคืนสินค้าจาก ', 
							
								IF( dt.tbName IN ( 'erp_purchase_return' ), 'คืนสินค้าให้ ', 
									dt.remark )

							)
						)	
					) as remark,
					IF( dt.tbName IN ( 'erp_purchase_inv', 'erp_sale_return', 'ap', 'dr' ), 0, SUM( dt.total_after_vat ) ) as debit,
					IF( dt.tbName IN ( 'erp_purchase_inv', 'erp_sale_return', 'ap', 'dr' ), SUM( dt.total_after_vat ), 0 ) as credit,
					IF( dt.tbName IN ( 'promotion'  ), 0, 1 ) as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_stock dt
				
				LEFT JOIN erp_company c ON dt.company_id = c.company_id
				[WHERE]
				GROUP BY
					gl_id,
					type,
					company_id,
					doc_date,
					doc_no,
					tbName,
					remark,
					dt.admin_company_id
				[HAVING]
			)
			UNION
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'promotionHeader' ) as gName,
					c.customer_return_gl_id as  gl_id,
					3 as type,
					dt.company_id as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					dt.remark,
					( dt.total_before_vat * ( dt.total_before_vat < 0 ) ) as debit,
					( dt.total_before_vat * ( dt.total_before_vat > 0 ) ) as credit,
					1 as use_it,
					dt.id as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_stock dt
				LEFT JOIN erp_company c ON dt.company_id = c.company_id
				WHERE dt.tbName = 'promotion' AND dt.doc_date >= '". $doc_date ."'
				[HAVING]
			)
			UNION


			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'jjjppppp' ) as gName,
					IF( dt.tbName = 'erp_purchase_inv', p.buy_gl_id,

						IF( dt.tbName = 'erp_sale_return', p.return_gl_id,

							IF( dt.tbName = 'erp_sale_inv', p.sale_gl_id, p.return_buy_gl_id )
						)
					) as gl_id,
					1 as type,
					99 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					act.stock_act_name as remark,
					IF( dt.tbName IN ( 'erp_purchase_inv', 'erp_sale_return'  ), SUM( dt.before_vat ), 0 ) as debit,
					IF( dt.tbName IN ( 'erp_purchase_inv', 'erp_sale_return'  ), 0, SUM( dt.before_vat ) ) as credit,
					1 as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.parent_id as parent_id
				FROM erp_stock_dt dt
				LEFT JOIN erp_stock_act act ON dt.act_id = act.stock_act_id
				LEFT JOIN sma_products p ON dt.product_id = p.id
				WHERE dt.doc_date >= '". $doc_date ."' 
				AND dt.tbName IN ( 'erp_purchase_inv', 'erp_sale_return', 'erp_sale_inv', 'erp_purchase_return' )
				GROUP BY
					gl_id,
					
					doc_date,
					doc_no,
					tbName,
					dt.admin_company_id
				[HAVING]
			)
			UNION
		
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'wweewppp' ) as gName,
					dtt.gl_id,
					3 as type,
					99 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					dt.remark,
					dtt.amt as debit,
					0 as credit,
					1 as use_it,
					dtt.id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_stock_pay dtt
				LEFT JOIN erp_stock dt ON dtt.parent_id = dt.id
				[WHERE]
				
				[HAVING]
			)
			UNION
		
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'general_pay' ) as gName,
					dtt.gl_id,
					3 as type,
					99 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_ap_pay' as tbName,
					dtt.remark  as remark,
					SUM( IF( dt.tbName = 'erp_ap_pay', dtt.amt, 0 ) ) as debit,
					SUM( IF( dt.tbName = 'erp_ap_pay', 0, dtt.amt ) ) as credit,
					dt.proove_bt as use_it,
					dtt.id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_gl_trn_pay dtt 
				LEFT JOIN erp_ap_pay dt ON dtt.parent_id = dt.id
			
				[WHERE]
				GROUP BY 
					dtt.gl_id,
					
					dt.doc_date,
					dt.doc_no,
					dtt.remark,
					use_it,
					dt.admin_company_id
					
				
				[HAVING]
			)
			UNION
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'kkk' ) as gName,
					IF( dt.tbName IN ( 'erp_purchase_inv', 'ap' ),  IF( dt.vat_no = '', 561, 560 ),
						IF( dt.tbName IN( 'erp_sale_return', 'dr' ), 559, IF( dt.tbName = 'erp_sale_inv', 559, 560 ) )
					) as gl_id,
					2 as type,
					999 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					IF( dt.tbName IN ( 'erp_purchase_inv', 'ap' ), IF( dt.tbName IN ( 'erp_purchase_inv' ), 'ซื้อ', dt.remark ),

						IF( dt.tbName IN( 'erp_sale_return', 'dr' ), 'รับคืนขาย', IF( dt.tbName = 'erp_sale_inv', 'ขายสินค้า', 'คืนสินค้าให้' ) )
					) as remark,
				
					IF( dt.tbName IN ( 'erp_purchase_inv', 'erp_sale_return', 'ap', 'dr', 'promotion'  ), 
						
						IF( dt.tbName IN (  'dsfdsfdsf' ), 
							SUM( dt.total_after_vat ), 
							SUM( dt.vat_bath ) 
						), 0 
						
					) as debit,
					IF( 
						dt.tbName IN ( 'erp_purchase_inv', 'erp_sale_return', 'ap', 'dr'    ), 0, 
						
						IF( dt.tbName IN ( 'dr' ), SUM( dt.total_after_vat ), SUM( dt.vat_bath ) )
						
					) as credit,
					1 as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_stock dt
				[WHERE]
				GROUP BY
					gl_id,
					type,
					
					doc_date,
					doc_no,
					tbName,
					remark,
					dt.admin_company_id

				[HAVING]
			)
			UNION
		
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'yerrereers' ) as gName,
					IF( dt.tbName = 'erp_cheque_in_clearing', 461, 2156 ) as gl_id,
					3 as type,
					0 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_cheque_clearing' as tbName,
					dt.remark,
					IF( dt.tbName = 'erp_cheque_in_clearing', 0, SUM( dtt.amt ) ) as debit,
					IF( dt.tbName = 'erp_cheque_in_clearing', SUM( dtt.amt ), 0 ) as credit,
					IF( dtt.cheque_id = 0, 0, 1 ) as use_it,
					dt.id as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_cheque_clearing_dt dtt
				LEFT JOIN erp_cheque_clearing dt ON dtt.parent_id = dt.id
				LEFT JOIN erp_bank_account ba ON dt.bank_account_id = ba.id
				[WHERE]
				GROUP BY
					gl_id,
					doc_date,
					doc_no,
					remark,
					admin_company_id,
					use_it
				[HAVING]
			)
			UNION	
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'aaerredsdsa' ) as gName,
					ba.gl_id,
					3 as type,
					0 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_cheque_clearing' as tbName,
					dt.remark,
					IF( dt.tbName = 'erp_cheque_in_clearing', dtt.amt, 0 ) as debit,
					IF( dt.tbName = 'erp_cheque_in_clearing', 0, dtt.amt ) as credit,
					IF( dtt.cheque_id = 0, 0, 1 ) as use_it,
					dtt.id as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_cheque_clearing_dt dtt
				LEFT JOIN erp_cheque_clearing dt ON dtt.parent_id = dt.id
				LEFT JOIN erp_bank_account ba ON dt.bank_account_id = ba.id
				[WHERE]
				[HAVING]
			)
			UNION	
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'other_pay' ) as gName,
					pm.gl_id,
					3 as type,
					999 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_ap_pay' as tbName,
					dtt.remark,
					IF( dt.tbName = 'erp_ap_pay', 0, SUM( dtt.amt ) ) as debit,
					IF( dt.tbName = 'erp_ap_pay', SUM( dtt.amt ), 0 ) as credit,
					dt.proove_bt as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_ap_pay_payment dtt
				LEFT JOIN erp_payment_out pm ON dtt.payment_id = pm.id
				LEFT JOIN erp_ap_pay dt ON dtt.parent_id = dt.id
				[WHERE]
				GROUP BY
					gl_id,
			
					doc_date,
					doc_no,
					remark,
					use_it,
					dt.admin_company_id
				[HAVING]
			)
			UNION
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'ppppiuouoe' ) as gName,
					dt.gl_id,
					1 as type,
					99 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_stock' as tbName,
					dt.remark as remark,
					IF( dt.tbName IN ( 'ap', 'dr' ), SUM( dt.total_before_vat ), 0 ) as debit,
					IF( dt.tbName IN ( 'ap', 'dr' ), 0, SUM( dt.total_before_vat ) ) as credit,
					1 as use_it,
					45454 as id,
					dt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_stock dt
				WHERE dt.doc_date >= '". $doc_date ."' 
				AND dt.tbName IN ( 'dr', 'ar', 'dp' )
				GROUP BY
					gl_id,
					type,
					
					doc_date,
					doc_no,
					tbName,
					dt.admin_company_id

				[HAVING]
			)
		
			UNION
			(
				SELECT
					'auto' as insert_type,
					CONCAT( 'ccc' ) as gName,
					b.gl_id,
					3 as type,
					999 as company_id,
					dt.doc_date,
					dt.doc_no,
					'erp_ap_pay' as tbName,
					IF( dtt.remark != '',  dtt.remark,  IF( dt.tbName = 'erp_ap_pay', CONCAT( 'โอนเงิน ', b.bank_account_name, ' to ' ), CONCAT( 'รับโอนเงิน ', b.bank_account_name, ' from ' ) )     ) as remark,
					IF( dt.tbName = 'erp_ap_pay', 0, SUM( dtt.amt ) ) as debit,
					IF( dt.tbName = 'erp_ap_pay', SUM( dtt.amt ), 0 ) as credit,
					dt.proove_bt as use_it,
					45454 as id,
					dtt.admin_company_id,
					'' as product_group,
					0 as close_month,
					dt.id as parent_id
				FROM erp_ap_pay_bank_account dtt
				LEFT JOIN erp_bank_account b ON dtt.bank_account_id = b.id
				LEFT JOIN erp_ap_pay dt ON dtt.parent_id = dt.id
				[WHERE]
				GROUP BY
					gl_id,
					type,
					
					doc_date,
					doc_no,
					tbName,
					remark,
					use_it,
					dt.admin_company_id
				[HAVING]
			)
			 

		) new_tb
		LEFT JOIN erp_company c ON new_tb.company_id = c.company_id
		ORDER BY
			new_tb.gName ASC,
			new_tb.gl_id ASC
	";

	$sql = genCond_( $sql, $replace );

	$skip = array();
	
///arr( $sql ); 

	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
	//arr( $va  );
//arr( $va );		
		
		++$order_number;
		
		$gName = $va->gl_id . '-'. $va->admin_company_id;

		$sql = "
			SELECT
				gl_gr.credit as credit,
				gl_gr.debit as debit,
				CONCAT ( gl.gl_code, ' ', gl.gl_name ) as gl_name
			FROM erp_gl gl
			LEFT JOIN erp_gl_group gl_gr ON gl.gl_group_id = gl_gr.id
			WHERE gl.id = ". $va->gl_id ."
		";
		
		$gl[$va->gl_id] = $dao->fetch( $sql );

		if( empty( $gl[$va->gl_id] ) ) {

			//continue;
		}
//arr( $va->gl_id );

		if( !isset( $balance[$gName] ) ) {

			$balance[$gName] = new stdClass;
			$balance[$gName]->credit_debit_bf = 0;
			$balance[$gName]->order_number = 0;
			$balance[$gName]->credit_debit_bal = 0;

		}

		$balance[$gName]->order_number += 1;

		$sum_credit_debit = 0;

		$balance[$gName]->credit_debit_bal += $sum_credit_debit;

		$id = getSkipId( $tbGlDt, 'id', $skip );
		
		$skip[] = $id;
		
		$product_group = '';
		$remark = $va->remark;
		
		if( isset( $backup[$id] ) ) {
			
			if( $va->gl_id == $backup[$id]->gl_id && $va->credit == $backup[$id]->credit && $va->debit == $backup[$id]->debit ) {
				
				$product_group = $backup[$id]->product_group;
				$remark = $backup[$id]->remark;
			}
		}
		
		$sqlUnion[] = "
			SELECT
				'". $va->doc_date ."' as doc_date,
				'". $va->doc_no ."' as doc_no,
				'". $va->gl_id ."' as gl_id,
				'". $va->tb_parent ."' as tb_parent,
				'". $remark ."' as remark,
				'". $va->credit ."' as credit,
				'". $va->debit ."' as debit,
				". $order_number ." as order_number,
				". $sum_credit_debit ." as sum_credit_debit,
				". $balance[$gName]->credit_debit_bal ." as credit_debit_bal,
				
				". $balance[$gName]->credit_debit_bf ." as credit_debit_bf,
				'". $va->gName ."' as g_name,
				". $id ." as id,
				'". $va->insert_type ."' as insert_type,
				'". $product_group ."' as product_group,
				". $va->admin_company_id ." as admin_company_id,
				". $va->close_month ." as close_month,
				". $va->parent_id ." as parent_id
		";
		
		$balance[$gName]->credit_debit_bf = $sum_credit_debit;
	}

	
	if( !empty( $sqlUnion ) ) {

		$sql = "
			INSERT INTO ". $tbGlDt ." (
				doc_date,
				doc_no,
				gl_id,
				tb_parent,
				remark,
				credit,
				debit,
				order_number,
				sum_credit_debit,
				credit_debit_bal,
				
				credit_debit_bf,
				g_name,
				id,
				insert_type,
				product_group,
				admin_company_id,
				close_month,
				parent_id

			)
			SELECT
				new_tb.doc_date,
				new_tb.doc_no,
				new_tb.gl_id,
				new_tb.tb_parent,
				new_tb.remark,
				new_tb.credit,
				new_tb.debit,
				new_tb.order_number,
				new_tb.sum_credit_debit,
				new_tb.credit_debit_bal,
				
				new_tb.credit_debit_bf,
				new_tb.g_name,
				new_tb.id,
				new_tb.insert_type,
				new_tb.product_group,
				new_tb.admin_company_id,
				new_tb.close_month,
				new_tb.parent_id
			FROM (

				". implode( ' UNION ', $sqlUnion ) ."
			) as new_tb
		";
 //arr( $sql );
		$dao->execDatas( $sql );
	}

}



function comfirm_user_package($param,$filed)
{
	// arr($filed);exit;
	// $fmodel->send_email($a);
	if(true){

		send_email($param['data']);
		expired_date($param['data']);
	}else{
		send_email($param['data']);
		expired_date($param['data']);
	}
	
	
	return ;
	// $dao = getDb();

		
	// $sql = "
	// 	SELECT
	// 		*
	// 	FROM admin_department_menu
	// 	WHERE department_id = (
	// 		SELECT
	// 			department_id
	// 		FROM admin_user
	// 		WHERE user_id = " . $param['parent_id'] . "
	// 	)
	// ";

	// $keep = array();
	// $menu_ids = array();
	// foreach ($dao->fetchAll($sql) as $ka => $va) {
	// 	$keep[] = $va->menu_id;
	// 	$menu_ids[] = $va->menu_id;
	// }


	// //
	// //
	// while (!empty($menu_ids)) {

	// 	$sql = "

	// 		SELECT *
	// 		FROM admin_menu
	// 		WHERE menu_parent IN ( '" . implode("','", $menu_ids) . "' )

	// 	";

	// 	$menu_ids = array();
	// 	foreach ($dao->fetchAll($sql) as $kb => $vb) {
	// 		$keep[] = $vb->menu_id;
	// 		$menu_ids[] = $vb->menu_id;
	// 	}
	// }


	// $sql = "
	// 	SELECT *
	// 	FROM admin_model
	// 	WHERE menu_id IN ( '" . implode("','", $keep) . "' )";

	// foreach ($dao->fetchAll($sql) as $ka => $va) {

	// 	$config[] =

	// 		array(
	// 			'page_id' => $va->model_id,
	// 			'permission' => 1,
	// 			'edit' => 1,
	// 			'add_row' => 1,
	// 			'delete_row' => 1,
	// 			'inspect' => NULL,
	// 			'prove' => NULL,
	// 			'views_department_id' => '%department_id;',

	// 		);
	// }


	// $sql = "
	// 	SELECT
	// 		*
	// 	FROM admin_user
	// 	WHERE user_id = ?
	// ";

	// $config[] = array(
	// 	'page_id' => 126,
	// 	'permission' => 1,
	// 	'edit' => 1,
	// 	'add_row' => 0,
	// 	'delete_row' => 0,
	// 	'inspect' => NULL,
	// 	'prove' => NULL,
	// 	'views_department_id' => NULL
	// );

	// $config[] = array(
	// 	'page_id' => 73,
	// 	'permission' => 1,
	// 	'edit' => 1,
	// 	'add_row' => 1,
	// 	'delete_row' => 1,
	// 	'inspect' => '%department_id;',
	// 	'prove' => NULL,
	// 	'views_department_id' => '%department_id;'
	// );

	// foreach ($dao->fetchAll($sql, array($param['parent_id'])) as $ka => $va) {

	// 	foreach ($config as $kb => $vb) {
	// 		$data = array();
	// 		$data['user_id'] = $va->user_id;
	// 		$data['page_id'] = $vb['page_id'];
	// 		$data['permission'] = $vb['permission'];
	// 		$data['edit'] = $vb['edit'];
	// 		$data['add_row'] = $vb['add_row'];
	// 		$data['delete_row'] = $vb['delete_row'];
	// 		$data['inspect'] = str_replace('%department_id;', '[' . $va->department_id . ']', $vb['inspect']);
	// 		$data['prove'] = str_replace('%department_id;', '[' . $va->department_id . ']', $vb['prove']);
	// 		$data['views_department_id'] = str_replace('%department_id;', '[' . $va->department_id . ']', $vb['views_department_id']);

	// 		$dao->insert('admin_user_page', $data);
	// 	}


	// 	if ($va->send_mail == 1) {

	// 		$mail = $va->user_email;

	// 		$ex = explode('@', $mail);

	// 		$pass = $ex[0] . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

	// 		$subject = 'แจ้งยูสเซอร์กับ รหัสผ่านโปรแกรม sac2015 ';
	// 		$body = '
	// 			ลิ้งค์เข้าใช้งาน: <a href="' . SiteName . '">คลิ๊กที่นี่</a><br><br>
	// 			User: ' . $mail . ' <br><br>

	// 			Pass: ' . $pass . '
	// 		';

	// 		sendMail($mail, $subject, $body);

	// 		$sql = "
	// 			UPDATE admin_user
	// 			SET
	// 				send_mail = 0,
	// 				user_name = '" . $mail . "',
	// 				user_password = MD5('" . $pass . "')
	// 			WHERE user_id = " . $va->user_id;

	// 		$dao->execDatas($sql);
	// 	}
	// }
	
	

}


function getBombImg( $myImg = NULL ) {
	
	if( file_exists( $myImg ) ) {
				
		return base_url( $myImg ) . '?rand='. rand() .'';
	}
	
	return base_url( 'assets/images/' . $_SESSION['u']->gender . '.png' );
}


function getBombLacyForm() {
	
	if( !Home ) {
		
		return false;
	}
	return '
		<script>
		
		$( function() {
			
			$( \'input\' ).each( function() {
				me = $( this );
				if( me.attr( \'name\' ) == \'action\'  || me.attr( \'name\' ) == \'token\'   ) {
				 
				}
				else if( me.attr( \'type\' ) == \'text\' ) {
					me.val( \'โหมดพิมพ์เร็วบอมเปิดอยู่\' ); 
				}
				else if( me.attr( \'type\' ) == \'number\' ) {
					me.val( \'0899999998\' ); 
				}
				else {
					me.val( \'bombfastmode@lasy.type\' ); 
				}
				
			}); 
			
		});
		</script>
 
	';
}



function send_email($data = array()) {
	$CI =& get_instance();
	$config = array(
		'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp',
		'smtp_host' => 'smtp.musionnext.com',
		'smtp_port' => 465,//465
		'smtp_user' => 'nattasit@musionnext.com', // change it to yours test@musionnext.com
		'smtp_pass' => 'EsNdPwQxiNzFIK', // change it to yours AGgA9ZDa7gZz
		'smtp_crypto' => 'ssl', //can be 'ssl' or 'tls' for example
		'mailtype' => 'html',
		'smtp_timeout' => '5', //in seconds
		'charset' => 'UTF-8',
		'wordwrap' => TRUE
	);

	// var_dump($data);exit;
					
	if(isset($data['user_id'])){            
		$id = $data['user_id'];
	}else if(isset($_SESSION['user_id'])){
		$id = $_SESSION['user_id'];
	}
	if(isset($id)){
		$sql = "
			SELECT
				*, sma_groups.id as oId
			FROM
				sma_users
			LEFT JOIN erp_user_company ON erp_user_company.user_id = sma_users.id
			LEFT JOIN sma_groups ON sma_users.request_group_id = sma_groups.id
			WHERE
				sma_users.id = " . $id . "
		";
		$user_data_payment = $CI->db->query($sql)->row();
	}
	
	

	
		if ($data['active'] == "payment" ) {
			// $date = date_create($data['date_pay']);
			$date = date("Y-m-d");


			$param['viewName'] = "email_tempate/packageOrder";
			$param['payment_data']= $data;
			$param['payment_user'] = $user_data_payment;
        	$pay_template = $CI->load->view('emailView',$param,true);
			// echo $pay_template;exit;
			
			$CI->load->library('email', $config);
			$CI->email->set_newline("\r\n");
			$CI->email->from($config['smtp_user'],'Supporter');
			$CI->email->to($user_data_payment->email); //$user_data_payment->email
			$CI->email->subject('Package Order');
			$CI->email->message($pay_template);
			// $CI->email->attach(base_url() . $data['file'], "inline");
			$email_log = array(
				'receive_user_id'	=> 1,
				'sender_user_id'	=> $user_data_payment->id,
				'time_update'		=> date("Y-m-d H:i:s"),
				'email_subject'		=> 'คำสั่งซื้อ Package ระบบ ERP',
				'email_body'		=> 'หมายเลขคำสั่งซื้อ : '.makeFrontZero($user_data_payment->oId).' '.$user_data_payment->name.' '.$user_data_payment->price
			);
			
			if($CI->email->send()){				
				email_log($email_log);
				return;
				// $errors = array( 
				// 	'success' => 1, 
				// 	'message' => 'ระบบทำการส่งเรื่องชำระเงินเรียบร้อยแล้ว รอการอีเมลล์ตอบกลับการยืนยันชำระเงิน'
				// );
				// echo json_encode( $errors );
			}else{
				return;
				// $errors = array( 'success' => 0, 'field' => array(), 'message' => 'ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง' );
				
				// echo json_encode( $errors );
			}

		} 

		else if( $data['active'] == 1){
		
			$date = date_create($data['date_pay']);
			$param['date_pay'] = $date;
			$param['time_pay'] = $data['time_pay'];
			$param['data'] = $user_data_payment;
			$param['viewName'] = "email_tempate/payment_success";
        	$payment_temp = $CI->load->view('emailView',$param,true);

		// echo  $payment_temp;exit;
		$CI->load->library('email', $config);
		$CI->email->set_newline("\r\n");
		$CI->email->from($config['smtp_user'],'Supporter');
		$CI->email->to($user_data_payment->email); //
		$CI->email->subject('Payment Success');
		$CI->email->message($payment_temp);
			$email_log = array(
			'receive_user_id'	=> 1,
			'sender_user_id'	=> $user_data_payment->id,
			'time_update'		=> date("Y-m-d H:i:s"),
			'email_subject'		=> 'คำสั่งซื้อของคุณได้รับการชำระเงินเเล้ว',
			'email_body'		=> 'หมายเลขคำสั่งซื้อ : '.makeFrontZero($user_data_payment->oId).' '.$user_data_payment->name.' '.$user_data_payment->price
			);
		if($CI->email->send()){
			email_log($email_log);
			return;
			// $errors = array( 
			// 	'success' => 1, 
			// 	'message' => 'ระบบทำการส่งเรื่องชำระเงินเรียบร้อยแล้ว รอการอีเมลล์ตอบกลับการยืนยันชำระเงิน'
				
				
			// );
			// echo json_encode( $errors );
		}else{
			// $errors = array( 'success' => 0, 'field' => array(), 'message' => 'ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง' );
			
			// echo json_encode( $errors );
			return;
		}
	}
	else if( $data['active'] == 2){

		
		$date = date_create($data['date_pay']);
		$param['date_pay'] = $date;
		$param['time_pay'] = $data['time_pay'];
		$param['data'] = $user_data_payment;
		$param['viewName'] = "email_tempate/payment_not_success";
        $payment_not_temp = $CI->load->view('emailView',$param,true);
		
			// var_dump($data['date_pay']);exit;
			$data['package_send'] =
				"
					<html>
					<head>
						<title>ยืนยันคำสั่งซื้อ</title>
					</head>
					<body>
						<h2>คำสั่งซื้อของคุณไม่ผ่าน</h2>                        
						" . form_open_token() . "                                
							<p>หมายเลขคำสั่งซื้อ : ".makeFrontZero($user_data_payment->oId)."</p>          
							<p>ผู้ซื้อ : " . $user_data_payment->first_name . " " . $user_data_payment->last_name . "</p>
							<p>วันที่ : " . date_format($date, "d/m/Y") . "</p>
							<p>เวลา : " . $data['time_pay'] . "</p>
							<p>Package : " . $user_data_payment->name . "</p> 
							<p>ราคา : " . $user_data_payment->price . "</p>
							<p>ต้องขออภัยคำสั่งซื้อของคุยไม่ผ่าน</p>
											
						
					</body>
					</html>
					";
			// echo  $data['package_send'];
			$CI->load->library('email', $config);
			$CI->email->set_newline("\r\n");
			$CI->email->from($config['smtp_user'],'Supporter');
			$CI->email->to($user_data_payment->email); //
			$CI->email->subject("Payment isn't Success");
			$CI->email->message($payment_not_temp);
			$email_log = array(
				'receive_user_id'	=> 1,
				'sender_user_id'	=> $user_data_payment->id,
				'time_update'		=> date("Y-m-d H:i:s"),
				'email_subject'		=> 'คำสั่งซื้อของคุณไม่ผ่าน',
				'email_body'		=> 'หมายเลขคำสั่งซื้อ : '.makeFrontZero($user_data_payment->oId).' '.$user_data_payment->name.' '.$user_data_payment->price
			);

			if($CI->email->send()){
				email_log($email_log);
				// $errors = array( 
				// 	'success' => 1, 
				// 	'message' => 'ระบบทำการส่งเรื่องชำระเงินเรียบร้อยแล้ว รอการอีเมลล์ตอบกลับการยืนยันชำระเงิน'
					
					
				// );
				// echo json_encode( $errors );
				return;
			}else{
				// $errors = array( 'success' => 0, 'message' => 'ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง' );//ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง
				
				// echo json_encode( $errors );
				return;
			}
		}
		else if( $data['active'] == 'reset_pass'){

				
				$param['data'] = $data;
				$date = now();
				$param['viewName'] = "email_tempate/resetPassword";
				// var_dump($param);
				$tempate = $CI->load->view('emailView',$param,true);
				// echo $tempate ;exit;
				$CI->load->library('email', $config);
				$CI->email->set_newline("\r\n");
				$CI->email->from($config['smtp_user'],'Supporter');
				$CI->email->to($data['email']); //
				$CI->email->subject('Reset Password');
				$CI->email->message($tempate);
				$email_log = array(
					'receive_user_id'	=> 1,
					'sender_user_id'	=> $data['user_id'],
					'time_update'		=> date("Y-m-d H:i:s"),
					'email_subject'		=> 'Reset Password',
					'email_body'		=> 'Email : '.$data['email']. 'Code Ref : '.$data['ref_no']
				);

				$html = '
		
					<div class="row align-items-center justify-content-center">
					<div class="col-lg-5 col-md-8 col-12">
						<div class="register-wrap p-5 bg-light shadow rounded-custom">
							<h1 class="fw-bold h3">กรอกข้อมูลเพื่อเปลี่ยนรหัสผ่าน</h1>							
							
								<div class="row">
									<div style="text-align: center; color: #2898CB; padding-top: 30px;">
										<h2 style="padding-left:20px; padding-right: 20px;">ERP Musion</h2>
									</div>
									<div style="text-align: center; margin-top: 40px;">
										<h3 style="color: #2898CB; padding-left:20px; padding-right: 20px;">ยินดีด้วยเราได้ส่งรหัสอ้างอิงให้เรียบร้อยแล้ว</h3>
								
									</div>
								</div>
						
						 
									<div class="card-sigin " style="padding-bottom: 15px;">
										<div class="main-signup-header">
											กรุณาตรวจสอบอีเมล์ <span style="color: green;">'. $data['email'] .'</span>เพื่อนำรหัสอ้างอิงมาใช้ในการรีเซตรหัสผ่าน
										</div>
									</div>
									<div class="d-grid gap-2 col-6 mx-auto" style="margin-top: 30px;">
										<a class="btn btn-success" href="'.front_link(13).'">รีเซ็ตรหัสผ่าน</a>
									</div>
								</div>
								
							
						</div>
					</div>
				</div>
				';
				
				
		
				if($CI->email->send()){
					// email_log($email_log);
					$errors = array( 
						'success' => 1, 
						'message' => 'ระบบทำการส่งเรื่องรีเซ็ตรหัสผ่าน',
						'html' => $html						
						
					);
					echo json_encode( $errors );
				}else{
					$errors = array( 'success' => 0, 'message' => 'ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง' );//ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง
					
					echo json_encode( $errors );
				}
			}if( $data['active'] == 'subscribe'){
		
					$date = now();
					$param['viewName'] = "email_tempate/subscribe";
        			$sub_temp = $CI->load->view('emailView',$param,true);

					
					// echo  $data['package_send'];
					$CI->load->library('email', $config);
					$CI->email->set_newline("\r\n");
					$CI->email->from($config['smtp_user'],'Supporter');
					$CI->email->to($data['email']); //
					$CI->email->subject('Thank you For Subscribe');
					$CI->email->message($sub_temp);
					$email_log = array(
						'receive_user_id'	=> 1,
						'sender_user_id'	=> 99,
						'time_update'		=> date("Y-m-d H:i:s"),
						'email_subject'		=> 'Thank you For Subscribe',
						'email_body'		=> 'ขอบคุณที่กดติดตามเรา'
					);

					if($CI->email->send()){
						email_log($email_log);
						$errors = array( 
							'success' => 1, 
							'message' => 'ขอบคุณที่กดติดตามเรา'													
							
						);
						echo json_encode( $errors );
					}else{
						$errors = array( 'success' => 0, 'message' => 'ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง' );//ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง
						
						echo json_encode( $errors );
					}
				}

				if ($data['active'] == "payment_form" ) {
					$date = date_create($data['date_pay']);
					// $date = date("Y-m-d");
		
		
					$param['viewName'] = "email_tempate/package_for_buyer";
					$param['payment_data']= $data;
					$param['date_pay']= $data['date_pay'];
					$param['payment_user'] = $user_data_payment;
					$pay_template = $CI->load->view('emailView',$param,true);
					// echo $pay_template;exit;
					
					$CI->load->library('email', $config);
					$CI->email->set_newline("\r\n");
					$CI->email->from($config['smtp_user'],'Supporter');
					$CI->email->to($user_data_payment->email); //$user_data_payment->email
					$CI->email->subject('Package Order');
					$CI->email->message($pay_template);
					// $CI->email->attach(base_url() . $data['file'], "inline");
					$email_log = array(
						'receive_user_id'	=> 1,
						'sender_user_id'	=> $user_data_payment->id,
						'time_update'		=> date("Y-m-d H:i:s"),
						'email_subject'		=> 'คำสั่งซื้อ Package ระบบ ERP',
						'email_body'		=> 'หมายเลขคำสั่งซื้อ : '.makeFrontZero($user_data_payment->oId).' '.$user_data_payment->name.' '.$user_data_payment->price
					);
					
					if($CI->email->send()){				
						email_log($email_log);
						// return;
						$errors = array( 
							'success' => 1, 
							'message' => 'ระบบทำการส่งเรื่องชำระเงินเรียบร้อยแล้ว รอการอีเมลล์ตอบกลับการยืนยันชำระเงิน'
						);
						echo json_encode( $errors );
					}else{
						// return;
						$errors = array( 'success' => 0, 'field' => array(), 'message' => 'ระบบไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง' );
						
						echo json_encode( $errors );
					}
		
				} 
	
	
	// sending email


}//end send Emaill Function



function email_log($data = array()){
	$dao = getDb();
	$idata = [					
		'receive_user_id'	=> $data['receive_user_id'],
		'sender_user_id'	=> $data['sender_user_id'],
		'time_update'		=> $data['time_update'],
		'email_subject'		=> $data['email_subject'],
		'email_body'		=> $data['email_body']
	];
	
	$result = $dao->insert('admin_email',$idata);
	
	return;
	

}

function expired_date( $data = array() ){
	date_default_timezone_set("Asia/Bangkok");
	$CI =& get_instance();
	$exp = 14;

	if( $data['package_type'] == "month"){
		$exp = 30;
	}else if($data['package_type'] == "year"){
		$exp = 365;
	}
	// var_dump($data['user_id']);exit;
	$expired_date = date("Y/m/d");
	
	$sql_date = " SELECT 
				DATE_FORMAT( ADDDATE( '".$expired_date."', INTERVAL ".$exp." day ), '%Y-%m-%d' ) as expired
			FROM
				sma_users
			WHERE
				sma_users.id = ".$data['user_id'].";";
	$date = $CI->db->query($sql_date)->row();

	$sql = "
		UPDATE sma_users 
		SET end_date = '".$date->expired."' 
		WHERE sma_users.id = ".$data['user_id'].";
	";
	$CI->db->query($sql);
	
	return;
}



//
//
function getLink( $model_id, $sub = array(), $get = array() ) {

	$ex = explode( '/', uri_string() );
	$dao = getDb();

	$sql = "
		SELECT *
		FROM admin_model
		WHERE model_id = ". $model_id ."";

	//$data = $dao->fetch( $sql );
	
	
	$get['token'] = get_token( $action = 'post', $attributes = array(), $hidden = array() );
	foreach( $dao->fetchAll( $sql ) as $ka => $data ) {
		
		$sub_model = '';

		if ( !empty( $sub ) )
			$sub_model = '/' . implode( '/', $sub );
		
		 
		//$link = ''. getCustomUrl() .'/' . $data->model_alias . $sub_model;
		$link = $data->model_alias . $sub_model;

		if ( !empty( $get ) )
			$link .= '?' . http_build_query( $get, 'flags_' );

		return  $link;
	}
}

//
//
function setLink( $link, $get = array() ) {
	
	
	//arr( $link ); 
/// exit;
	if(!empty($link))
		$link = $link;
	
	//$get['sdaf'] = 'dfasadssdfs';

	if ( !empty( $get ) )
		$link .= '?' . http_build_query( $get, 'flags_' );

 
	//$company = $_SESSION['u']->user_company_name;
    return base_url( $link );
	 
    
}

function getCustomUrl() {
	
	$company_name = $_SESSION['u']->user_company_name;
	
	
	return base_url() . 'admin/'. $company_name;
}


function getFrontLoginButton() {
	
	if ( !empty( $_SESSION['u'] ) ) {
		
		return '

			<div class="action-btns text-end me-5 me-lg-0 d-none d-md-block d-lg-block">
			
				<a class="btn account"  href="'. base_url( 'admin' ) .'">

				 '. $_SESSION['u']->first_name .'</a>
				
				 
				<a href="'. base_url( 'admin/logout' ) .'" class="text-dark btn "><i class="bx bx-log-out"></i> ออกจากระบบ</a>
				
				 
			</div>
		';
	}
	
	return '
		<div class="action-btns text-end me-5 me-lg-0 d-none d-md-block d-lg-block">
			<a href="'. front_link( 3 ) .'" class="btn btn-link text-decoration-none me-2">เข้าสู่ระบบ</a>
			<a href="'. front_link( 10 ) .'" class="btn btn-primary">ทดลองใช้ฟรี 14 วัน</a>
		</div>
	';
}

function getRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}
function insertAutoStock( $params = array() ) {
	
}

//
//$param['tb_dt'] = 'erp_stock_dt';
//$param['parent_id'] = 1;
//$param['data']['tbName'] = 'erp_sale_inv';
//$param['tb_parent'] = 'erp_stock'
function updateVat( $param ) {
	///return;
	$dao = getDb();
	
	
	if( true ) {
		
		$sql = "
			SELECT
				st.doc_no,
				st.doc_date,
				st.vat_type,
				st.vat_rate,
				dt.id,
				dt.discount,
				dt.price,
				dt.qty_um,
				dt.lock_dt_id
			FROM ". $param['tb_dt'] ." dt
			INNER JOIN ". $param['tb_parent'] ." st ON dt.parent_id = st.id
			WHERE dt.parent_id = ". $param['parent_id'] ."
		";
		
	//arr( $sql ); 
		$sqlUnion = array();
		$ids = array();
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
			
			if( is_numeric( strpos( $va->discount, '%' ) )  ) {
				
				$discount = str_replace( '%', '', $va->discount ); 
				
				$ex = explode( '+', $discount);
				
				$price = $va->price;
				foreach( $ex as $ke => $ve ) {
					
					$price *= ( 100 - $ve ) / 100;
				}
				
				$discount_bath = $va->price - $price;
				
			}
			else {
				
				$discount_bath = $va->discount;
			}
			
			if( empty( $discount_bath ) )  {
				$discount_bath = 0;
			}
		
		
			$sqlUnion[] = "
				SELECT
					". $va->id ." as id,
					( 100 + ". $va->vat_rate ." ) / 100 as vat_rate,
					ROUND( ( ". $va->price ." - ". $discount_bath ." ) * ". $va->qty_um ." , 2 ) as amt,
					'". $va->doc_no ."' as doc_no,
					'". $va->doc_date ."' as doc_date,
					". $va->vat_type ." as vat_type
					
			";

		}
		
		
		$sql = "	
			UPDATE ". $param['tb_dt'] ." dt
			INNER JOIN (
				". implode( ' UNION ', $sqlUnion ) ."
			) as new_tb ON dt.id = new_tb.id
			LEFT JOIN erp_stock_act act ON dt.tbName = act.tbName
			SET
				dt.before_vat = IF( new_tb.vat_type = 2, new_tb.amt, new_tb.amt / new_tb.vat_rate ),
				dt.after_vat = IF( new_tb.vat_type = 2, new_tb.amt * new_tb.vat_rate, new_tb.amt ),
				dt.qty = ( dt.qty_um * dt.qty_rate ),
				
				dt.act_id = act.stock_act_id,
				dt.factor = act.factor,
				dt.doc_no = new_tb.doc_no,
				dt.doc_date = new_tb.doc_date
				
				 
		";	
		
//arr( $sql );
		$dao->execDatas( $sql );

		$sql = "
			UPDATE
				". $param['tb_parent'] ." o
			INNER JOIN (
				SELECT
					parent_id,
					SUM( before_vat ) as before_vat_dt,
					SUM( after_vat ) as after_vat_dt,
					SUM( after_vat ) - SUM( before_vat ) as vat_bath,
					1 as check_
				FROM ". $param['tb_dt'] ."
				WHERE parent_id = ". $param['parent_id'] ."
				GROUP BY
					parent_id
					
			) as new_tb ON o.id = new_tb.parent_id
			SET
			
				o.vat_bath = IF( new_tb.check_ IS NULL, 0, new_tb.after_vat_dt - new_tb.before_vat_dt ),
			
				o.total_before_vat = new_tb.before_vat_dt,
				o.total_after_vat = new_tb.after_vat_dt,
				
				o.vat_adjust = new_tb.after_vat_dt - ROUND( o.total_before_vat * ( 100 + o.vat_rate ) /100, 2 )
			
		";
		
/// arr( $sql );
		
		$dao->execDatas( $sql );
		
	
		$sql = "
			UPDATE ". $param['tb_dt'] ." dt
			INNER JOIN (
				SELECT 
					( 
						SELECT
							MIN( id )
						FROM ". $param['tb_dt'] ."
						WHERE parent_id = so.id

					) as dt_id, 
					so.id,
					so.vat_adjust AS vat_adjust
				FROM ". $param['tb_parent'] ." so
				WHERE so.id = ". $param['parent_id'] ."
				HAVING dt_id IS NOT NULL
				
			) new_tb ON dt.id = new_tb.dt_id

			SET 
				dt.after_vat = dt.after_vat - new_tb.vat_adjust	
			
		";	

 ///arr( $sql );
		///$dao->execDatas( $sql );
		
		
		
		
	// arr( $sql );
		return;
	}
	
	if( $param['tb_dt'] == 'erp_stock_dt' ) {
		
		
		$sql = "
			SELECT
				st.doc_no,
				st.doc_date,
				st.vat_type,
				st.vat_rate,
				dt.id,
				dt.discount,
				dt.price,
				dt.qty_um,
				dt.lock_dt_id,
				st.use_vat_adjust
			FROM ". $param['tb_dt'] ." dt
			INNER JOIN ". $param['tb_parent'] ." st ON dt.parent_id = st.id
			WHERE dt.parent_id = ". $param['parent_id'] ."
		";
		
	 
		$sqlUnion = array();
		$ids = array();
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
			
			if( is_numeric( strpos( $va->discount, '%' ) )  ) {
				
				$discount = str_replace( '%', '', $va->discount ); 
				
				$ex = explode( '+', $discount);
				
				$price = $va->price;
				foreach( $ex as $ke => $ve ) {
					
					$price *= ( 100 - $ve ) / 100;
				}
				
				$discount_bath = $va->price - $price;
				
			}
			else {
				
				$discount_bath = $va->discount;
			}
			
			if( empty( $discount_bath ) )  {
				$discount_bath = 0;
			}
			
			if(  in_array( $param['data']['tbName'], array( 'erp_sale_inv', 'erp_sale_return' ) ) ) {
				
				if(  in_array( $param['data']['tbName'], array( 'erp_sale_inv' ) ) ) {
					
					$sqlUnion[] = "
						SELECT
							". $va->id ." as id,
							( 100 + ". $va->vat_rate ." ) / 100 as vat_rate,
							
							ROUND( ( ( SELECT dt.price FROM erp_sale_order_dt dt WHERE dt.id = ". $va->lock_dt_id ." ) - ". $discount_bath ." ) * ". $va->qty_um .", 2 ) as amt,
							". $va->vat_type ." as vat_type,
							'". $va->doc_no ."' as doc_no,
							'". $va->doc_date ."' as doc_date,
							( SELECT dt.price FROM erp_sale_order_dt  dt WHERE dt.id = ". $va->lock_dt_id ." ) as price
					";
				}
				else {
			
					$sqlUnion[] = "
						SELECT
							". $va->id ." as id,
							( 100 + ". $va->vat_rate ." ) / 100 as vat_rate,
							
							ROUND( ( ". $va->price ." - ". $discount_bath ." ) * ". $va->qty_um ." , 2 ) as amt,
							". $va->vat_type ." as vat_type,
							'". $va->doc_no ."' as doc_no,
							'". $va->doc_date ."' as doc_date,
							". $va->price ." as price
					";
					
					
				}
				
			}
			if(  in_array( $param['data']['tbName'], array( 'erp_purchase_inv'  ) ) ) {
				
				$sqlUnion[] = "
					SELECT
						". $va->id ." as id,
						( 100 + ". $va->vat_rate ." ) / 100 as vat_rate,
						
						IF( ". $va->qty_um ." = 0, ( ". $va->price ." - ". $discount_bath ." ), ROUND( ( ". $va->price ." - ". $discount_bath ." ) * ". $va->qty_um .", 2 ) )  as amt,
						
						
						
						". $va->vat_type ." as vat_type,
						'". $va->doc_no ."' as doc_no,
						'". $va->doc_date ."' as doc_date,
						". $va->price ." as price
				";
			}
			
			else {
				
				$sqlUnion[] = "
					SELECT
						". $va->id ." as id,
						( 100 + ". $va->vat_rate ." ) / 100 as vat_rate,
						ROUND( ( ". $va->price ." - ". $discount_bath ." ) * ". $va->qty_um .", 2 ) as amt,
						". $va->vat_type ." as vat_type,
						'". $va->doc_no ."' as doc_no,
						'". $va->doc_date ."' as doc_date,
						". $va->price ." as price
				";
				
			}
			
			$ids[] = $va->id;
			
		}
	//echo $use_vat_adjust;
	
		$sql = "
			UPDATE erp_stock_dt dt
			INNER JOIN erp_stock_act act ON dt.tbName = act.tbName
			INNER JOIN (
				". implode( ' UNION ', $sqlUnion ) ."
			) as new_tb ON dt.id = new_tb.id
			SET
				dt.before_vat = IF( new_tb.vat_type = 2, new_tb.amt, new_tb.amt / new_tb.vat_rate ),
				dt.after_vat = IF( new_tb.vat_type = 2, new_tb.amt * new_tb.vat_rate, new_tb.amt ),
				dt.qty = ( dt.qty_um * dt.qty_rate ),
				
				dt.act_id = act.stock_act_id,
				dt.factor = act.factor,
				dt.doc_no = new_tb.doc_no,
				dt.doc_date = new_tb.doc_date,
				dt.price = new_tb.price
			WHERE dt.id IN ( ". implode( ', ', $ids ) ." );
		";
		
		
// arr( $sql );
	
		$dao->execDatas( $sql );
	
	
		
		$sql = "
			UPDATE
				". $param['tb_parent'] ." o
			LEFT JOIN (
				SELECT
					parent_id,
					SUM( before_vat ) as before_vat_dt,
					SUM( after_vat ) as after_vat_dt,
					SUM( after_vat ) - SUM( before_vat ) as vat_bath,
					1 as check_
				FROM ". $param['tb_dt'] ."
				WHERE parent_id = ". $param['parent_id'] ."
				GROUP BY
					parent_id
					
			) as new_tb ON o.id = new_tb.parent_id
			SET
				o.vat_adjust = IF( o.use_vat_adjust = 1, new_tb.after_vat_dt - ROUND( new_tb.before_vat_dt * ( 100 + o.vat_rate ) /100, 2 ),  0 ),
				o.total_before_vat = new_tb.before_vat_dt,
				o.total_after_vat = new_tb.after_vat_dt - o.vat_adjust,
				o.vat_bath = IF( new_tb.check_ IS NULL, 0, o.total_after_vat - o.total_before_vat )
			WHERE o.skip_update_vat = 0	
			AND o.id = ". $param['parent_id'] .";
		";
		
		//arr( $sql );
		
		$dao->execDatas( $sql );
		
	
		$sql = "
			UPDATE ". $param['tb_dt'] ." dt
			INNER JOIN (
				SELECT 
					( 
						SELECT
							MIN( id )
						FROM ". $param['tb_dt'] ."
						WHERE parent_id = so.id

					) as dt_id, 
					so.id,
					so.vat_adjust AS vat_adjust
				FROM ". $param['tb_parent'] ." so
				WHERE so.id = ". $param['parent_id'] ."
				HAVING dt_id IS NOT NULL
				
			) new_tb ON dt.id = new_tb.dt_id

			SET 
				dt.after_vat = dt.after_vat - new_tb.vat_adjust	
			WHERE dt.parent_id = ". $param['parent_id'] ."	
		";	

//arr( $sql );
		$dao->execDatas( $sql );
	
	}
	else {
		
		if( $param['tb_dt'] == 'erp_sale_order_dt' ) {
			
			$sql = "
				SELECT
					dt.id,
					so.vat_type,
					so.vat_rate,
					dt.discount,
					dt.price
				
				FROM ". $param['tb_dt'] ." dt
				INNER JOIN ". $param['tb_parent'] ." so ON dt.parent_id = so.id
				WHERE dt.parent_id = ". $param['parent_id'] ."
			";
			
		}
		else {
			$sql = "
				SELECT
					dt.id,
					so.vat_type,
					so.vat_rate,
					dt.discount,
					dt.price
					
				FROM ". $param['tb_dt'] ." dt
				INNER JOIN ". $param['tb_parent'] ." so ON dt.parent_id = so.id
				WHERE dt.parent_id = ". $param['parent_id'] ."
			";
			
			
		}
		
		
		
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {

			if( is_numeric( strpos( $va->discount, '%' ) )  ) {
				
				$discount = str_replace( '%', '', $va->discount ); 
				
				$ex = explode( '+', $discount);
				
				$price = $va->price;
				foreach( $ex as $ke => $ve ) {
					$price *= ( 100 - $ve ) / 100;
				}
				
				$discount_bath = $va->price - $price;
				
			}
			else {
				
				$discount_bath = $va->discount;
			}
			
			if( empty( $discount_bath ) ) {
				$discount_bath = 0;
			}
			
			
			if( $param['tb_dt'] == 'erp_sale_order_dt' ) {
				
				$sql = "	
					UPDATE ". $param['tb_dt'] ." dt
					LEFT JOIN (
						SELECT
							dt.id,
							( 100 + ". $va->vat_rate ." ) / 100 as vat_rate,
							ROUND( ( dt.price - ". $discount_bath ." ) * dt.qty_um , 2 )  as amt
						
						FROM ". $param['tb_dt'] ." dt
						WHERE dt.id = ". $va->id ."
					) as new_dt ON dt.id = new_dt.id
					SET
						dt.before_vat = IF( ". $va->vat_type ." = 2, new_dt.amt, new_dt.amt / new_dt.vat_rate ),
						dt.after_vat = IF( ". $va->vat_type ." = 2, new_dt.amt * new_dt.vat_rate, new_dt.amt ),
						dt.qty = ( dt.qty_um * dt.qty_rate )
						 
						
					WHERE dt.id = ". $va->id ."
				";	
				
	 
	 
	 
			}
			else {
				
				$sql = "	
					UPDATE ". $param['tb_dt'] ." dt
					LEFT JOIN (
						SELECT
							dt.id,
							( 100 + ". $va->vat_rate ." ) / 100 as vat_rate,
							ROUND( ( dt.price - ". $discount_bath ." ) * dt.qty_um, 2 )  as amt
						
						FROM ". $param['tb_dt'] ." dt
						WHERE dt.id = ". $va->id ."
					) as new_dt ON dt.id = new_dt.id
					SET
						dt.before_vat = IF( ". $va->vat_type ." = 2, new_dt.amt, new_dt.amt / new_dt.vat_rate ),
						dt.after_vat = IF( ". $va->vat_type ." = 2, new_dt.amt * new_dt.vat_rate, new_dt.amt ),
						dt.qty = ( dt.qty_um * dt.qty_rate )
						
					WHERE dt.id = ". $va->id ."
				";	
				
			}
			
		 
				
			$dao->execDatas( $sql );
			
		}
		
		$sql = "
			UPDATE
				". $param['tb_parent'] ." o
			LEFT JOIN (
				SELECT
					parent_id,
					SUM( before_vat ) as before_vat_dt,
					SUM( after_vat ) as after_vat_dt,
					1 as check_
					
				FROM ". $param['tb_dt'] ."
				WHERE parent_id = ". $param['parent_id'] ."
				GROUP BY
					parent_id
			) as new_tb ON o.id = new_tb.parent_id
			SET
				o.vat_adjust = new_tb.after_vat_dt - ROUND( new_tb.before_vat_dt * ( 100 + o.vat_rate ) /100, 2 ),
				o.total_before_vat = IFNULL( new_tb.before_vat_dt, 0 ),
				o.total_after_vat = new_tb.after_vat_dt - o.vat_adjust,
				o.vat_bath = IF( new_tb.check_ IS NULL, 0, o.total_after_vat - o.total_before_vat )
				
				
			WHERE o.skip_update_vat = 0	
			AND o.id = ". $param['parent_id'] .";
		";	
		


		$dao->execDatas( $sql );
				
		
		$sql = "
			UPDATE ". $param['tb_dt'] ." dt
			INNER JOIN (
				SELECT 
					( 
						SELECT
							MIN( id )
						FROM ". $param['tb_dt'] ."
						WHERE parent_id = so.id

					) as dt_id, 
					so.id,
					so.vat_adjust AS vat_adjust
				FROM ". $param['tb_parent'] ." so
				WHERE so.id = ". $param['parent_id'] ."
				HAVING dt_id IS NOT NULL
				
			) new_tb ON dt.id = new_tb.dt_id

			SET 
				dt.after_vat = dt.after_vat - new_tb.vat_adjust	
			WHERE dt.parent_id = ". $param['parent_id'] ."	
		";	

//arr( $sql );
		$dao->execDatas( $sql );
		
		
	}

}


///$type == 'form'
//$type == 'check'
function getBombCapcha( $result = array() ) {
	
	if( CapchaForm == false ) { 
		return NULL;
	} 
	
	 
	if( !empty( $result ) ) { // have form data return check true or false
		
		if( !isset( $_SESSION['readyCapcha'] ) ) {
			
			if( true ) {
				
				if( !empty( $result['g-recaptcha-response'] ) ){
					$_SESSION['readyCapcha'] = 1;					
				}	
				else {
					
					$errors['success'] = 0;
					
					$errors['message'] = 'กรุณาเช็ค Capcha เพือยืนยันตัวตน';

					echo json_encode( $errors );
					
					exit;
				}	
					
				
			}
			else {
				
				
				if( !empty( $result['g-recaptcha-response'] ) ){
					$captcha = $result['g-recaptcha-response'];
				}	
				else {
					
					$captcha = 'dsafddsaasdf';
				}	
					
				$secretKey = CapchaDataSecretkey;

				$ip = $_SERVER['REMOTE_ADDR'];

				
				$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode( $secretKey ) .  '&response=' . urlencode( $captcha );
				$response = file_get_contents( $url );
				$responseKeys = json_decode( $response, true );		
						
				if( empty( $responseKeys['success'] ) ) {
							
					$errors['success'] = 0;
					
					$errors['message'] = 'กรุณาเช็ค Capcha เพือยืนยันตัวตน';

					echo json_encode( $errors );
					
					exit;
				}
				else {
					
					$_SESSION['readyCapcha'] = 1;
				}
			}
			
			
			
		}
		
		return true;
	}
	
	unset( $_SESSION['readyCapcha'] );
	
	// no form data return check capcha
	return '
		 <script src=\'https://www.google.com/recaptcha/api.js\' async defer></script>
		<div class="g-recaptcha" data-sitekey="'. CapchaDataSitekey .'"></div>
	';
	
}





//
//$param['data']['tbName'] == 'erp_sale_return'
//$param['parent_id']
//$param['data']['doc_no']
//$param['data']['lock_parent_id']
function insertPurchaseInv( $param ) {
	
	$dao = getDb();
	
	$stockConfig = stockConfig( $param['data']['tbName'] );
	
	$tbName = $param['data']['tbName'];

	$factor = $stockConfig['factor'];

	$act_id = $stockConfig['act_id'];
	
	
	///arr($stockConfig);
	//arr($param['data']['tbName']);
	if (  in_array( $param['data']['tbName'], array( 'erp_purchase_inv', 'erp_sale_inv', 'erp_sale_return' ) ) ) {
		
		$parent_id = $param['parent_id'];
		
		$copyFrom = isset( $param['copyFrom'] )? $param['copyFrom']: 'sac_purchase_order';
		$copyFromDt = isset( $param['copyFromDt'] )? $param['copyFromDt']: 'sac_purchase_order_dt';
	
		$sql = "
			UPDATE 
				erp_stock st
			INNER JOIN ". $copyFrom ." l ON st.lock_parent_id = l.id
			SET 
				st.vat_type = l.vat_type, 
				st.vat_rate = l.vat_rate, 
				st.due = l.due, 
				st.company_id = l.company_id 
			WHERE st.id = ". $parent_id ."
		";
	
 //arr( $sql );	
		$dao->execDatas($sql);
		

		$sql = "
			INSERT INTO erp_stock_dt ( product_id, qty, act_id, parent_id,  zone_id, qty_um,  price, discount, lock_dt_id, lock_parent_id, doc_date, doc_no, tbName, factor, qty_rate, um_label, admin_company_id )

			SELECT
				dt.product_id, 
				( ( dt.qty_um ) - IFNULL( new_tb.qty_um, 0 ) ) as qty, 
				". $act_id ." as act_id, 
				". $parent_id ." as parent_id,  
				NULL as zone_id, 
				( ( dt.qty_um ) - IFNULL( new_tb.qty_um, 0 ) )  as qty_um,
				dt.price, 
				dt.discount, 
				dt.id as lock_dt_id, 
				dt.parent_id as lock_parent_id, 
				'" . $param['data']['doc_date'] . "' as doc_date,
				'" . $param['data']['doc_no'] . "' as doc_no,
				'" . $tbName . "' as tbName,
				" . $factor . " as factor,
				1 as qty_rate, 
				dt.um_label, 
				". $_SESSION['u']->user_company_id ." as admin_company_id
			FROM ". $copyFromDt ." dt
			LEFT JOIN (
				SELECT
					lock_dt_id,
					SUM( qty_um ) as qty_um,
					SUM( qty ) as qty
				FROM erp_stock_dt
				WHERE tbName = '". $param['data']['tbName'] ."'
				AND lock_parent_id = " . $param['data']['lock_parent_id'] . "
				GROUP BY
					lock_dt_id
			) as new_tb ON dt.id = new_tb.lock_dt_id
			WHERE dt.parent_id = " . $param['data']['lock_parent_id'] . "
			HAVING qty_um > 0
				
		";
	
	//arr( $sql );
		$dao->execDatas($sql);
			
		return true;
	}
//echo 'dfsdfadsfdd';
 



	if ( $param['data']['tbName'] == 'erp_sale_inv' ) {

 //arr( $sql );
 
		
 
	} else {
		
	}
	
		$sql = "
			SELECT
				dt.qty_um as test,
				dt.id as lock_dt_id,
				dt.parent_id as lock_parent_id,
				dt.product_id,
				dt.price,
				dt.discount,
				dt.um_id,
				( ( dt.qty - IFNULL( dt.cancel_qty, 0 ) ) - IFNULL( new_tb.qty, 0 ) ) / dt.qty_rate as qty_um,
				( dt.qty - IFNULL( dt.cancel_qty, 0 ) ) - IFNULL( new_tb.qty, 0 ) as needQty,
				dt.qty_rate,
				dt.um_label
			FROM erp_stock_dt dt
			LEFT JOIN (
				SELECT
					lock_dt_id,
					SUM( qty ) as qty
				FROM erp_stock_dt
				WHERE tbName = '". $param['data']['tbName'] ."'
				AND lock_parent_id = " . $param['data']['lock_parent_id'] . "
				GROUP BY
					lock_dt_id
			) as new_tb ON dt.id = new_tb.lock_dt_id
			WHERE dt.parent_id = " . $param['data']['lock_parent_id'] . "
			HAVING needQty > 0
		";
	
//arr( $sql );
	$skip = array();
	foreach ( $dao->fetchAll( $sql ) as $kn => $vn) {
		
//arr( $vn );
		$replace = array();
		
		$replace['WHERE'][] = "dt.product_id = " . $vn->product_id . "";
	
		$stockSql = "
			SELECT
				new_tb.remark,
				new_tb.zone_id,
				SUM( new_tb.stockQty ) as stockQty
			FROM (
				(
					SELECT
						'' as remark,
						dt.zone_id,
						SUM( dt.qty * dt.factor ) as stockQty
					FROM erp_stock_dt dt
					[WHERE]
					GROUP BY
						zone_id
					HAVING stockQty > 0
					
				)
				
			) as new_tb
			GROUP BY
				zone_id
			ORDER BY 
				stockQty ASC,
				zone_id ASC
		";

		$sql = genCond_($stockSql, $replace );

//arr( $sql );
		//exit;
		$total = array();

		$keep = array();
		foreach ($dao->fetchAll( $sql ) as $kz => $vz) {
			$keep[] = $vz->stockQty;
		}

		//foreach ( $dao->fetchAll( $sql ) as $kz => $vz) {

			if ( array_sum( $keep ) < $vn->needQty ) {

				//continue;
			}


//arr( $vz );
			$total[] = $vz->stockQty;

			$qty = $vz->stockQty;

			//
			if ( array_sum( $total ) >= $vn->needQty ) {
				
				$qty = ROUND( ( $vz->stockQty + $vn->needQty ) - array_sum( $total ), 2);

				
			}

			$qty_um = $vn->qty_um * ($qty / $vn->needQty);

			

			$sql = "
				INSERT INTO erp_stock_dt ( product_id, qty, act_id, parent_id,  zone_id, qty_um,  price, discount, lock_dt_id, lock_parent_id, doc_date, doc_no, tbName, factor, qty_rate, um_label, admin_company_id )

				SELECT

					" . $vn->product_id . " as product_id,
					
					" . $qty . " as qty,
					" . $act_id . " as act_id,
					" . $param['parent_id'] . " as parent_id,
					'" . $vz->zone_id . "' as zone_id,
					" . $qty_um . " as qty_um,
					" . $vn->price . " as price,
					'" . $vn->discount . "' as discount,
					" . $vn->lock_dt_id . " as lock_dt_id,
					" . $vn->lock_parent_id . " as lock_parent_id,
					'" . $param['data']['doc_date'] . "' as doc_date,
					'" . $param['data']['doc_no'] . "' as doc_no,
					'" . $tbName . "' as tbName,
					" . $factor . " as factor,
					" . $vn->qty_rate . " as qty_rate,
					'" . $vn->um_label . "' as um_label,
					". $_SESSION['u']->user_company_id ." as admin_company_id
			";
//arr( $sql );
			//if (!empty($param['data']['implode_from_doc'])) {

		 //
				$dao->execDatas($sql);
			//}

			//$_SESSION['productsUpdateStock'][$vn->product_id] = $vn->product_id;

			//if (array_sum($total) >= $vn->needQty) {

				//break;
			//}
		//}
	}
}





//
//
function insertErpSaleInvDt( $param ) {

	$dao = getDb();


	$stockConfig = stockConfig($param['data']['tbName']);
	
	

	$doc_priority = $stockConfig['doc_priority'];

	$tbName = $param['data']['tbName'];

	$factor = $stockConfig['factor'];

	$act_id = $stockConfig['act_id'];
	
	
	$parent_id = $param['parent_id'];



	if ( $param['data']['tbName'] == 'erp_sale_inv' ) {

 //arr( $sql );
 
		$sql = "
			SELECT
				dt.qty_um as test,
				dt.id as lock_dt_id,
				dt.parent_id as lock_parent_id,
				dt.product_id,
				dt.price,
				dt.discount,
				dt.um_id,
				( ( dt.qty - IFNULL( dt.cancel_qty, 0 ) ) - IFNULL( new_tb.qty, 0 ) ) / dt.qty_rate as qty_um,
				( dt.qty - IFNULL( dt.cancel_qty, 0 ) ) - IFNULL( new_tb.qty, 0 ) as needQty,
				dt.qty_rate,
				dt.um_label
			FROM erp_sale_order_dt dt
			LEFT JOIN (
				SELECT
					lock_dt_id,
					SUM( qty ) as qty
				FROM erp_stock_dt
				WHERE tbName = '". $param['data']['tbName'] ."'
				AND lock_parent_id = " . $param['data']['lock_parent_id'] . "
				GROUP BY
					lock_dt_id
			) as new_tb ON dt.id = new_tb.lock_dt_id
			WHERE dt.parent_id = " . $param['data']['lock_parent_id'] . "
			HAVING needQty > 0
		";
		
		
	
 
	} else {
		
		
		$sql = "
			UPDATE 
				erp_stock st
			INNER JOIN erp_stock l ON st.lock_parent_id = l.id
			SET 
				st.vat_type = l.vat_type, 
				st.vat_rate = l.vat_rate, 
				st.due = l.due, 
				st.company_id = l.company_id 
			WHERE st.id = ". $parent_id ."
		";
		
		 
		$dao->execDatas($sql);
		
		$sql = "
			SELECT
				dt.qty_um as test,
				dt.id as lock_dt_id,
				dt.parent_id as lock_parent_id,
				dt.product_id,
				dt.price,
				dt.discount,
				dt.um_id,
				( ( dt.qty - IFNULL( dt.cancel_qty, 0 ) ) - IFNULL( new_tb.qty, 0 ) ) / dt.qty_rate as qty_um,
				( dt.qty - IFNULL( dt.cancel_qty, 0 ) ) - IFNULL( new_tb.qty, 0 ) as needQty,
				dt.qty_rate,
				dt.um_label
			FROM erp_stock_dt dt
			LEFT JOIN (
				SELECT
					lock_dt_id,
					SUM( qty ) as qty
				FROM erp_stock_dt
				WHERE tbName = '". $param['data']['tbName'] ."'
				AND lock_parent_id = " . $param['data']['lock_parent_id'] . "
				GROUP BY
					lock_dt_id
			) as new_tb ON dt.id = new_tb.lock_dt_id
			WHERE dt.parent_id = " . $param['data']['lock_parent_id'] . "
			HAVING needQty > 0
		";
		
	}
	

	$skip = array();
	foreach ( $dao->fetchAll( $sql ) as $kn => $vn ) {
		//arr( $vn );

		$replace = array();
		
		$replace['WHERE'][] = "dt.product_id = " . $vn->product_id . "";
	
		$stockSql = "
			SELECT
				new_tb.remark,
				new_tb.zone_id,
				SUM( new_tb.stockQty ) as stockQty
			FROM (
				(
					SELECT
						'' as remark,
						dt.zone_id,
						SUM( dt.qty * dt.factor ) as stockQty
					FROM erp_stock_dt dt
					[WHERE]
					GROUP BY
						zone_id
					HAVING stockQty > 0
					
				)
				
			) as new_tb
			GROUP BY
				zone_id
			ORDER BY 
				stockQty ASC,
				zone_id ASC
		";

		$sql = genCond_($stockSql, $replace );

//arr( $sql );
		//exit;
		$total = array();

		$keep = array();
		foreach ($dao->fetchAll( $sql ) as $kz => $vz) {
			$keep[] = $vz->stockQty;
		}

		foreach ( $dao->fetchAll( $sql ) as $kz => $vz) {

			if ( array_sum( $keep ) < $vn->needQty ) {

				continue;
			}


//arr( $vz );
			$total[] = $vz->stockQty;

			$qty = $vz->stockQty;

			//
			if ( array_sum( $total ) >= $vn->needQty ) {
				
				$qty = ROUND( ( $vz->stockQty + $vn->needQty ) - array_sum( $total ), 2);

				
			}

			$qty_um = $vn->qty_um * ($qty / $vn->needQty);

			

			$sql = "
				INSERT INTO erp_stock_dt ( product_id, qty, act_id, parent_id,  zone_id, qty_um,  price, discount, lock_dt_id, lock_parent_id, doc_date, doc_no, tbName, factor, qty_rate, um_label, admin_company_id )

				SELECT

					" . $vn->product_id . " as product_id,
					
					" . $qty . " as qty,
					" . $act_id . " as act_id,
					" . $param['parent_id'] . " as parent_id,
					'" . $vz->zone_id . "' as zone_id,
					" . $qty_um . " as qty_um,
					" . $vn->price . " as price,
					'" . $vn->discount . "' as discount,
					" . $vn->lock_dt_id . " as lock_dt_id,
					" . $vn->lock_parent_id . " as lock_parent_id,
					'" . $param['data']['doc_date'] . "' as doc_date,
					'" . $param['data']['doc_no'] . "' as doc_no,
					'" . $tbName . "' as tbName,
					" . $factor . " as factor,
					" . $vn->qty_rate . " as qty_rate,
					'" . $vn->um_label . "' as um_label,
					". $_SESSION['u']->user_company_id ." as admin_company_id
			";
///arr( $sql );
			if (!empty($param['data']['implode_from_doc'])) {

		 //
				$dao->execDatas($sql);
			}

			//$_SESSION['productsUpdateStock'][$vn->product_id] = $vn->product_id;

			if (array_sum($total) >= $vn->needQty) {

				break;
			}
		}
	}
}


function renderGlTree( $param = array(), $gl_parent_id = NULL, $step = 0, $style = NULL, $filters = array() ) {
	
	$dao = getDb();
	
	if( !empty( $gl_parent_id ) ) {
		
		$filters['WHERE'][] = "gl.gl_parent_id = ". $gl_parent_id  ."";
		
	}
	else {
		
		if( empty( $filters ) ) 
			$filters['WHERE'][] = "gl.gl_parent_id = 0";
		
	}
	
	
	$sql =  $param['sub_config']->main_sql;
	$sql = str_replace( array( '%filter;', '[sort]' )  , array( '  [WHERE] [HAVING] ', '  ORDER BY gl.order_number ASC  ' ), $sql );
	
	

	
	$sql = genCond_( $sql, $filters );
//arr( $sql );	
 
////exit;
	$lis = array();
	
	$parentActive = false;
	
	$childLi = $dao->fetchAll( $sql );
	
	$countChildLi = count( $childLi );
	
	foreach( $childLi as $ka => $va ) {
		
		//arr( $va );
		$sdfsdf = '';
	
		$sdfsdf = 'dsaddsdsfadfffdsaf';
	
		$style_ = 'style="display: block;" data-goog="dfssdds"';
		
		$getBombUl = renderGlTree( $param, $va->id, ( $step + 1 ), $style );
		
		 
		 
		
		if( !empty( $getBombUl ) ) {
			
			$class = '';
			
			if( $param['parent_id'] == $va->id ) {
				$class = 'gl_active';
			}
	
	
			$lis[] = '
				<li class="bomb-gl" style="margin-left: '. ( $step * 15 ) .'px;">
			
					<a style="" data-save="" class="" href="'. setLink( 'table_gl', array( 'parent_id' => $va->id ) ) .'">
						
					   
						<span class="text '. $class .'"  style="">'. $va->full_name .' </span>
						
					</a>
			
				'. $getBombUl .'</li>
			';
		}
		
	}
	
	return '<ul '. $style .' class="nav">'. implode( '', $lis ) .'</ul>';
	
}


function gl_tree( $param = array() ) {
	
	//arr( $param['sub_config']->main_sql );
	
	//exit;
	$filters = array();
	 //arr( $_SESSION['user_serch'][174] );
	 
	if( !empty( $_SESSION['user_serch'][174] ) ) {
		foreach( $_SESSION['user_serch'][174] as $kh => $vh ) {
			
			if( $vh == '' ) {
				continue;
			}
			$filters['HAVING'][] = "". $kh ." LIKE '%". $vh ."%' ";
		} 
	} 
	 
	 
	return  '
		<style>
			.bomb-gl {
				border-left: dashed 1px #876c6c;
			}
			.gl_active {
				
				background-color: #2e3670;color: #f3d4d4;padding: 5px 19px;border-radius: 13px;margin-left: -4px;font-size: 122%;
			}
		</style>	
		 <div style="padding: 20px; background-color: #ffff;" class="box">'. renderGlTree( $param, $gl_parent_id = NULL, $step = 0, $style = NULL, $filters ) .'</div>
	
	';
	
	
	
}


//
//
function updateGlGroup($param)
{

 
	$dao = getDb();
	
	$sql = "
		UPDATE erp_gl SET gl_id = id
	";
	$dao->execDatas($sql);
	
	$main_id = $param['main_id'];
	$data = $param['data'];

	$sql = "
		SELECT
			*
		FROM erp_gl
		WHERE id IN (
			SELECT
				gl_parent_id
			FROM erp_gl
			WHERE id = " . $main_id . "
		)
	";

//arr( $sql );
	$res = $dao->fetch($sql);

	if ($res)
		$gl_group_id = $res->gl_group_id;
	else {

		$gl_group_id = $data['gl_group_id'];
	}

	$gl_ids = array($main_id);

	//
	//
	for ($i = 1; $i <= 100; ++$i) {

		if (empty($gl_ids))
			break;

		$gl_ids_ = array();
		
		
		$sql = "

			UPDATE erp_gl
			SET gl_group_id = " . $gl_group_id . "
			WHERE id IN ( ". implode( ',', $gl_ids ) ." )
		";
		
		
		
		$dao->execDatas($sql);
		
		foreach ($gl_ids as $ka => $va) {


			$sql = "
				SELECT
					*
				FROM erp_gl
				WHERE gl_parent_id = " . $va . "
			";
			
			
			foreach ($dao->fetchAll($sql) as $kb => $vb) {
				$gl_ids_[] = $vb->id;
			}
		}

		$gl_ids = $gl_ids_;
	}
}

//
//$param['tbName'] = 'color_in';
//$param['data']['color_in'] = 'color_in';
//$param['parent_id'] = 4545454;
//$param['data']['move_pare'] = 4545454;
//$param['main_id'] = 445445
function insertStockMove( $param ) {

 	$dao = getDb();
	
	
	//arr( $param );
	
	if( isset( $param['tbName'] ) && $param['tbName'] == 'color_in' ) {
		
		$tbName = $param['tbName'];
		
	}
	else if( isset( $param['tbName'] ) && $param['tbName'] == 'move_in' ) {
		
		$tbName = 'move_in';
		
		
	}
	else {
		
		$tbName = 'move_in';
		
		
	}

	$stockConfig = stockConfig( $tbName );

	$parent_id = $param['parent_id'];

	$sql = "
		DELETE
		FROM erp_stock_dt
		WHERE move_pare = ". $param['data']['move_pare'] ."
		AND tbName IN ( '". $tbName ."'  )
		
		AND factor = 1
		AND parent_id = ". $param['parent_id'] ."
	";
	
	//arr( $sql );

	$dao->execDatas( $sql );

	$sql = "
		INSERT INTO erp_stock_dt (  move_pare, lock_dt_id, product_id, act_id, parent_id, doc_no, doc_date,  tbName, qty_um, qty, factor, qty_rate, zone_id, 
		



		admin_company_id, um_label )

		SELECT
		
		
			dt.move_pare,
			dt.id,
			dt.product_id,
			
			". $stockConfig['act_id'] ." as act_id,
			dt.parent_id,
			dt.doc_no,
			dt.doc_date,
			'". $tbName ."' as tbName,
			dt.qty_um,
			dt.qty,
			". $stockConfig['factor'] ." as factor,
			
			dt.qty_rate,
		
			
			". $param['data']['zone_in_id'] ." as zone_id,
			
			admin_company_id,
		
			um_label
		FROM erp_stock_dt dt
		WHERE dt.id = ". $param['main_id'] ."
	";
	
//	arr( $sql );

	$dao->execDatas( $sql );
}

function DateThai($strDate)
	{
		$strYear = date("Y",strtotime($strDate))+543;
		$strMonth= date("n",strtotime($strDate));
		$strDay= date("j",strtotime($strDate));
		$strHour= date("H",strtotime($strDate));
		$strMinute= date("i",strtotime($strDate));
		$strSeconds= date("s",strtotime($strDate));
		$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
		$strMonthThai=$strMonthCut[$strMonth];
		return "$strDay $strMonthThai $strYear";
	}

function set_best_seller($data, $field = null){//, $field
		
		$dao = getDb();
		$CI =& get_instance();
        $sql = "SELECT * FROM sma_groups WHERE pagekage = 1 ORDER BY order_number";
        $list_package = $dao->fetchAll($sql);
		if(isset($field['best_seller'])){
			foreach($list_package as $k => $v){            
				if($v->best_seller == 0){
					$update = "UPDATE ".$data." SET best_seller = ".$field['best_seller']." WHERE sma_groups.id = ".$field['id']." ";
					
					$b = $CI->db->query($update);
					
	
				}else
				if ($v->id != $field['id']){
					$update = "UPDATE ".$data." SET best_seller = 0 WHERE sma_groups.id = ".$v->id." ";
					
					$b = $CI->db->query($update);
					
				}
				
			}
		}
        
		// exit;

}

function getBombSlideAlert() {
	
	$dao = getDb();
	
	$sql = "
		SELECT 
			m.*,
			u.first_name as sender
		FROM admin_email m
		INNER JOIN sma_users u ON m.sender_user_id = u.id 
		WHERE m.close = 0 AND m.receive_user_id = 1 LIMIT 0, 1
	";
	
	
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		
		
		
		return '
		 
			<div class="sliding-ad"  style="">
				<span style="position:relative;display:block;">
					<span class="close-ad" style="cursor:pointer;position:absolute;top:-2px;right:0;text-decoration:none;">X</span>
					<a href="'.  getLink( 126, array(), array( 'currentTab' => 604 ) ) .'" style="color:#777;text-decoration:none;">
						<span style="float:left;display:inline-block;width:80px;margin-right:15px;">
						<img alt="" src="https://brp.musion.co.th/assets/images/male.png" class="mini_avatar img-rounded" style="width:100%;">
						
						 
						
						</span>
						<h2 style="font-size:16px;font-weight:bold;margin:0;padding:0 0 5px 0;">คุณมีข้อความแจ้งเตือนใหม่</h2>
						<span style="font-size:13px;">('. $va->sender .')  '. $va->email_subject .'</span>
					</a>
				</span>
			</div>
		
			<script>
				$( function() {
					
					$( \'.sliding-ad\' )
						.addClass( \'show\' )
						.click(function() {
						
						me = $( this );
						me.parents( \'.sliding-ad\' ).removeClass( \'show\' );
						
						
						$.get( \''. setLink( 'ajax/closeNotialert' ) .'\', {}, function() {
							
							
						});
					});
					
					
					
					$( \'.close-ad\' ).click(function() {
						
						me = $( this );
						me.parents( \'.sliding-ad\' ).removeClass( \'show\' );
						
						
						$.get( \''. setLink( 'ajax/closeNotialert' ) .'\', {}, function() {
							
							
						});
					});
					
					
				});
			</script>
		';
		
	}
	 
}




function getBombUl__( $menu_id = NULL, $db = null, $style = NULL, $step = 1, $getTopParent = false ) {
	
	$_SESSION['parentLiHaveToactive'] = 0;
	
	$dao = getDb();
	
	if( !empty( $menu_id ) ) {
		
		$replace['cond1'] = "WHERE menu_parent = ". $menu_id ."";
		$replace['cond2'] = "WHERE menu_id  = ". $menu_id ." AND every_body_login = 1 AND show_on_menu = 1";
		
	}
	else {
		
		$replace['cond1'] = "WHERE menu_parent IS NULL";
		$replace['cond2'] = "WHERE menu_id IS NULL AND every_body_login = 1 AND show_on_menu = 1";
	}
	$sql = "
		SELECT 
			*
		FROM (
			(
				SELECT 
					have_child,
					iconic as iconic,
					'menu' as g,
					menu_title as title,
					menu_id as id,
					'-' as alias,
					menu_order as order_number
				FROM admin_menu 
				[cond1]
				ORDER BY 
					menu_order ASC
			)
			UNION 
			(
				SELECT 
					0 as have_child,
					'dd' as iconic,
					'page' as g,
					model_title as title,
					model_id as id,
					model_alias as alias,
					model_order as order_number
				FROM admin_model 
				[cond2]
				
				AND model_id IN (
					SELECT 
						page_id 
					FROM admin_group_page 
					WHERE group_id = (
					
						SELECT  
							group_id  
						FROM sma_users 
						WHERE id = ". $_SESSION['user_id'] ."
					)
				)
				ORDER BY 
					model_order ASC
			)
		) as new_tb 
		ORDER BY 
			order_number ASC
	";
	
	
	$sql = genCond_( $sql, $replace );
	
//arr( $sql );exit;
	$lis = array();
	
	$parentActive = false;
	
	$childLi = $dao->fetchAll( $sql );
	
	$countChildLi = count( $childLi );
	
	foreach( $childLi as $ka => $va ) {
		
		
		$sdfsdf = '';
	
		$sdfsdf = 'dsaddsdsfadfffdsaf';
		
		if( $va->g == 'menu' ) {
			
			if( $getTopParent == true ) {
				
				
				
				$style_ = 'style="display: block;" data-goog="dfssdds"';
				
			}
			else {
				
				$sql = "
					SELECT 
						* 
					FROM user_menu 
					WHERE user_id = ". $_SESSION['user_id'] ." 
					AND menu_id = ". $va->id ."
				";
			
				$style_ = '';
				$style_ = 'style="display: block;" data-goog="dfssdds"';
				foreach( $dao->fetchAll( $sql ) as $km => $vm ) {
				
					//$style_ = 'style="display: block;" data-goog="dfssdds"';
				}
			}
			
			$getBombUl = getBombUl( $va->id, $db, $style_,  ( $step + 1 ) );
			
			if( $getTopParent == true ) {
				
				if( empty( $getBombUl ) ) {
					continue;
				}
				
				//$title = $va->title .' '. $va->have_child;
				$title = $va->title;
				$link = setLink( 'ajax/saveMenu', array( 'id' => $va->id ) );
				
				
				if( $va->id == $_SESSION['u']->last_menu_id ) {
					
					$a = '<a class="nav-link active" data-id="'. $va->id .'" href="'. $link .'" style="margin-left: 15px;">'. $title .'</a>';
					
				}
				else {
					
					$a = '<a class="nav-link" data-id="'. $va->id .'" href="'. $link .'" style="margin-left: 15px;">'. $title .'</a>';
					
					
				}
				
				$menus[$va->id] = $getBombUl;
				
				$lis[] = $a;
				
				//$getBombUl;
				continue;
			}
			
			
			
			if( !empty( $getBombUl ) ) {
		
				$icon = '<i class="fa fa-play" aria-hidden="true"></i>';
				if( isset( $va->iconic ) ) {
					
					$icon = $va->iconic;
				}
				$icon = '';
				if( !empty( $_SESSION['parentLiHaveToactive'] ) ) {
					
					$lis[] = '
						<li class="menu" >
					
							<a style="font-weight: bold; background-color: #0096f1; margin: 0 25px;border-radius: 18px; " menu-id="'.$va->id.'" data-save="'. setLink( 'ajax/checkUser_id/'. $va->id .'' ) .'" class="dropmenu save-menu '. $va->have_child .'" href="#">
								'. $icon .'
							   
								<span class="text" style="font-size: 98%; color: white;">'. $va->title .' </span>
								<span class="chevron closed"></span>
							</a>
					
						'. $getBombUl .'</li>
					';
				}
				else {
					
					$lis[] = '
						<li class="menu" >
					
							<a style="margin: 0 25px; font-size: 17px; font-weight: bold;" menu-id="'.$va->id.'" data-save="'. setLink( 'ajax/checkUser_id/'. $va->id .'' ) .'" class="dropmenu save-menu" href="#">
								'. $icon .'
							   
								<span class="text">'. $va->title .' '. $va->have_child .'</span>
								<span class="chevron closed"></span>
							</a>
					
						'. $getBombUl .'</li>
					';
				}
			}
		}
		else {
			
			 
			$active = '';
			if( ex( 1 ) == $va->alias  ) {
				
				$parentActive = true;
				$active = 'active';
				$lis[] = '<li class=" '. $active .'" >

					<a style="border-bottom: 1px solid #d0dbe5; border-radius: 19px;" menu-id="'.$va->id.'" class="submenu '. $sdfsdf .'" href="'. getLink( $va->id ) .'">
						<i class="fa fa-info-circle"></i>
							
						<span class="text" style="font-size: 105%;font-weight: bold;/* color: #ffffff; */">'. $va->title .'</span>
					</a>

					</li>
				';
				
			}
			else {
				$lis[] = '
					<li class=" '. $active .'" >
						<a style="" menu-id="'.$va->id.'" class="submenu '. $sdfsdf .'" href="'. getLink( $va->id ) .'">
							<i class="fa fa-info-circle"></i>
							
							<span class="text">'. $va->title .'</span>
						</a>

					</li>
				';
				
			}
		}
	}
	
	
	if( $getTopParent == true ) {
				
		$gogo['menus'] = $menus;
		$gogo['divs'] = implode( '', $lis );
		
		return $gogo;
	}
	

	if( empty( $lis ) ) {
		return implode( '', $lis );
	}
	

	if( $parentActive == true ) {
		$_SESSION['parentLiHaveToactive'] = 1;
		return '<ul '. $style .' class="dfdsaaadsadfafddsfdf">'. implode( '', $lis ) .'</ul>';
	}
	
	return '<ul '. $style .' class="dfdsaaadsadfafddsfdf">'. implode( '', $lis ) .'</ul>';
}



function getAdminMenus() {
	
	//arr( $_SESSION['u']);
	if( empty( $_SESSION['u']->admin ) ) {
		
		return false;
	}
	
	$dao = getDb();
	$sql = "
		SELECT 
			m.new_config_id,
			fp.*
		FROM admin_model m
		INNER JOIN erp_front_page fp ON m.model_id = fp.old_model_id
		WHERE m.every_body_login = 1
		
	";
	
	foreach( $dao->fetchAll( $sql ) as $ka => $va ) {
		
		$lis[] = '<li class="slide"><a class="side-menu__item" data-bs-toggle="slide" href="'. front_link( $va->id ) .'">'. $va->icon .'<span class="side-menu__label">'. $va->title .'</span></a></li>';
	}

	return implode( '', $lis );
}


//
//
function getTable( $datas, $config = array(), $addColumns = array()  ) {
	
	//arr( $config );
	///exit;
	
	
	if( empty( $datas ) ) {
		
		//return '';
	}
	
	$pri_key = $config->pri_key;
	
	$keep = array();
	foreach( $config->columns as $kc => $vc ) {
		$keep[$kc] = convertObJectToArray( $vc );
		
	}
	
	
	$columns = $keep;
	
	
	$r = 0;
	
	
	$tds = array();
	$tds[] = '<th>No.</th>';
	
	foreach( $addColumns as $kc => $vc ) {
		
		$tds[] = '<th>'. $vc['label'] .'</th>';
		
	}
	
	foreach( $columns as $kc => $vc ) {
		
		if( empty( $vc['show'] ) ) {
			continue;
		}
		
		$tds[] = '<th>'. $vc['label'] .'</th>';
		
	}
	
	$trHead = '<tr>'. implode( '', $tds ) .'</tr>';
	
	
	$trs[] = $trHead;
		
	
	foreach( $datas as $kg => $vg ) {
		
		
		$tds = array();
		$tds[] = '<td class="">'. ( $r + 1  ) .'</td>';
		
		foreach( $addColumns as $kc => $vc ) {
			
			
			$val = str_replace( 'ddfsadfdsdf', $vg->$pri_key, $vc['val'] );
			
			$tds[] = '<td>'. $val .'</td>';
			
		}
		
		foreach( $columns as $kc => $vc ) {
			
			if( empty( $vc['show'] ) ) {
				continue;
			}
			
			$val = NULL;
			if( isset( $vg->$kc ) ) {
				
				$val = $vg->$kc;
			}
			
			
			$val = getVal( $val , $vc, $status = 'r', $res = array(), $comma = ',', $n = NULL, $main_id = NULL, $parent_data = NULL  );
			
			

		 
			$tds[] = '<td class="'. $vc['a'] .'">'. $val .'</td>';
			
		
			
		}
		
		
		
		$trs[] = '<tr>'. implode( '', $tds ) .'</tr>';
		
		++$r;
	}
	
	//$trs[] = $trHead;
	
	if( empty( $trs ) ) 
		return false;
	
	return  '<table class="flexme3">'. implode( '', $trs ) .'</table>';
}



//
//
function updateCompany( $param )
{

	$dao = getDb();


	$sql = "

		UPDATE erp_company c
		SET
			c.supplier_gl_id = ( SELECT gl_id FROM erp_company_supplier_gl WHERE id = c.company_is_supplier ),

			c.supplier_return_gl_id = ( SELECT return_gl_id FROM erp_company_supplier_gl WHERE id = c.company_is_supplier ),

			c.customer_gl_id = ( SELECT gl_id FROM erp_company_supplier_gl WHERE id = c.company_is_customer ),

			c.customer_return_gl_id = ( SELECT return_gl_id FROM erp_company_supplier_gl WHERE id = c.company_is_customer )
		WHERE c.company_id = " . $param['parent_id'] . "


	";

	$dao->execDatas($sql);
	
	
	$sql = "
	
		SELECT 
			CONCAT( p.khang, ' ', p.khet, ' ', p.province, ' ', p.post_code ) as u,
			c.*
		FROM erp_company c 
		INNER JOIN erp_post_code p ON c.post_code_id = p.id
	";
	
	
	foreach( $dao->fetchAll( $sql ) as $kc => $vc ) {
		
		

		$cityclean = str_replace (" ", "+", $vc->u );
		
		$ch = curl_init( 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $cityclean . '&sensor=false&key=AIzaSyDYPZOYx6WArxqMN8d3-aJnuguank7SAIw&v=weekly' );
		
		curl_setopt_array( $ch, array(
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 60,
		));


		$gogo = curl_exec( $ch );

		
		///$geoloc = json_decode( $gogo, true );


		
		
		
		$sql = "
			UPDATE erp_company 
			SET 
				map_detail = '". $gogo ."' 
			WHERE company_id = ". $vc->company_id ."
		";
		
		$dao->execDatas( $sql );
	}



}


//
// 
function getBombUl( $menu_id = NULL, $db = null, $style = NULL, $step = 1, $getTopParent = false ) {
 
	$_SESSION['parentLiHaveToactive'] = 0;
	
	$dao = getDb();
	
	if( !empty( $menu_id ) ) {
		
		$replace['cond1'] = "WHERE menu_parent = ". $menu_id ."";
		$replace['cond2'] = "WHERE menu_id  = ". $menu_id ." AND every_body_login = 1 AND show_on_menu = 1";
		
	}
	else {
		
		$replace['cond1'] = "WHERE menu_parent IS NULL";
		$replace['cond2'] = "WHERE menu_id IS NULL AND every_body_login = 1 AND show_on_menu = 1";
	}
	$sql = "
		SELECT 
			*
		FROM (
			(
				SELECT
					'' as model_param,
					direct_link,
					have_child,
					iconic as iconic,
					'menu' as g,
					menu_title as title,
					menu_id as id,
					'-' as alias,
					menu_order as order_number
				FROM admin_menu 
				[cond1]
				ORDER BY 
					menu_order ASC
			)
			UNION 
			(
				SELECT 
					model_param,
					NULL as direct_link,
					0 as have_child,
					'dd' as iconic,
					'page' as g,
					model_title as title,
					model_id as id,
					model_alias as alias,
					model_order as order_number
				FROM admin_model 
				[cond2]
				
				AND model_id IN (
					SELECT 
						page_id 
					FROM admin_group_page 
					WHERE group_id = (
					
						SELECT  
							group_id  
						FROM sma_users 
						WHERE id = ". $_SESSION['user_id'] ."
					)
				)
				ORDER BY 
					model_order ASC
			)
		) as new_tb 
		ORDER BY 
			order_number ASC
	";
	
	
	$sql = genCond_( $sql, $replace );
	
//arr( $sql );exit;
	$lis = array();
	
	$parentActive = false;
	
	$childLi = $dao->fetchAll( $sql );
	
	$countChildLi = count( $childLi );
	
	foreach( $childLi as $ka => $va ) {
		
		
		$sdfsdf = '';
	
		$sdfsdf = 'dsaddsdsfadfffdsaf';
		
		if( $va->g == 'menu' ) {
			
		
			$style_ = 'style="display: block;" data-goog="dfssdds"';
			
			$getBombUl = getBombUl( $va->id, $db, $style_,  ( $step + 1 ) );
			
			if( $getTopParent == true ) {
				
				$link = setLink( 'ajax/saveMenu', array( 'id' => $va->id ) );
				
				$title = $va->title;
			 
				if( !empty( $va->direct_link ) ) {
				
					$class = 'nav-link'; 
				
					//$ex = explode( '/' , uri_string() );
					
					if( uri_string() == $va->direct_link ) {
						
						$_SESSION['closeLeftMenu'] = 1;
						
						$class = 'nav-link active'; 
						
						$haveTopActive = true;
					}
					
				
	
  //arr( $ex );	
//exit;	
					
					$a = '<a class="'. $class .'" data-link="'. $va->direct_link .'"  href="'. $link .'" style="margin-right: 5px;">'. $title .' </a> ';	
					$lis[] = $a;
					continue;	
				}
				
				
				if( empty( $getBombUl ) ) {
					continue;
				}
				
				if(  $_SESSION['parentLiHaveToactive'] == 1 ) {
					//$va->id == 
					
					$_SESSION['u']->last_menu_id = $va->id;
					$class = 'nav-link active'; 
						
				}
				else {
					
					$class = 'nav-link'; 
					
				}
				
				$a = '<a class="'. $class .'" data-link="" data-id="'. $va->id .'" href="'. $link .'" style="margin-right: 5px;">'. $title .' </a>';
				
				$menus[$va->id] = $getBombUl;
				
				$lis[] = $a;
				
				continue;
			}
			
			if( !empty( $getBombUl ) ) {
		
				$icon = '<i class="fa fa-play" aria-hidden="true"></i>';
				if( isset( $va->iconic ) ) {
					
					$icon = $va->iconic;
				}
				
				if( !empty( $_SESSION['parentLiHaveToactive'] ) ) {
					
					$lis[] = '
						<li class="menu" >
					
							<a style="font-weight: bold; background-color: #0096f1; margin: 0 25px;border-radius: 18px; margin-left: '. ( $step * 15 ) .'px;" menu-id="'.$va->id.'" data-save="'. setLink( 'ajax/checkUser_id/'. $va->id .'' ) .'" class="dropmenu save-menu '. $va->have_child .'" href="#">
								'. $icon .'
							   
								<span class="text" style="font-size: 98%; color: white;">'. $va->title .' </span>
								<span class="chevron closed"></span>
							</a>
					
						'. $getBombUl .'</li>
					';
				}
				else {
					
					$lis[] = '
						<li class="menu" >
					
							<a style="margin: 0 25px; font-size: 17px; font-weight: bold; margin-left: '. ( $step * 15 ) .'px;" menu-id="'.$va->id.'" data-save="'. setLink( 'ajax/checkUser_id/'. $va->id .'' ) .'" class="dropmenu save-menu" href="#">
								'. $icon .'
							   
								<span class="text">'. $va->title .' '. $va->have_child .'</span>
								<span class="chevron closed"></span>
							</a>
					
						'. $getBombUl .'</li>
					';
				}
			}
		}
		else {
			
			if( $va->model_param == 'a' ) {
				
				$link = getLink( $va->id, array(), array( 'formView' => $va->model_param ) );
			}
			else {
				
				$link = getLink( $va->id, array(), array() );
			}
			
			
			 
			$active = '';
			if( ex( 1 ) == $va->alias  ) {
				
				$parentActive = true;
				$active = 'active';
				
				
				$lis[] = '<li class="page '. $active .'" >

					<a style="border-bottom: 1px solid #d0dbe5; border-radius: 19px; margin: 0 '. ( $step * 15 ) .'px;" menu-id="'.$va->id.'" class="submenu '. $sdfsdf .'" href="'. $link .'">
						<i class="fa fa-info-circle"></i>
							
						<span class="text" style="font-size: 105%;font-weight: bold;/* color: #ffffff; */">'. $va->title .'</span>
					</a>

					</li>
				';
				
			}
			else {
				$lis[] = '
					<li class="page '. $active .'" >
						<a style="margin: 0 '. ( $step * 15 ) .'px;" menu-id="'.$va->id.'" class="submenu '. $sdfsdf .'" href="'. $link .'">
							<i class="fa fa-info-circle"></i>
							
							<span class="text">'. $va->title .'</span>
						</a>

					</li>
				';
				
			}
		}
	}
	
	
	if( $getTopParent == true ) {
				
		$gogo['menus'] = $menus;
		$gogo['divs'] = implode( '', $lis );
		
		return $gogo;
	}
	

	if( empty( $lis ) ) {
		return implode( '', $lis );
	}
	

	if( $parentActive == true ) {
		$_SESSION['parentLiHaveToactive'] = 1;
		return '<ul '. $style .' class="dfdsaaadsadfafddsfdf">'. implode( '', $lis ) .'</ul>';
	}
	
	return '<ul '. $style .' class="dfdsaaadsadfafddsfdf">'. implode( '', $lis ) .'</ul>';
}

function ex( $index = 'last', $getPath = false ) {
	
	 
	$ex = explode( '/' , uri_string() );
	
  ///arr( $ex );
	
	if ( $index == 'last' ) {
			
		return str_replace( '', '', $ex[count($ex)-1] );
	}

	$i = 1;
	foreach( $ex as $ka => $va ) {
		 
		
		$get[$i] = str_replace( '', '', $va );
		
		++$i;
	}

	//
	//
	if ( $getPath ) {
		unset( $get[0] );
		//unset( $get[1] );
	
		return implode( '/', $get );
	}
	
 //arr( $index );
// arr( $get );
	if ( isset( $get[$index] ) )
		return $get[$index];

	return false;
}


function getDocStatusBt( $datas = array(), $getView = array(), $config = array() ) {
	
	$dao = getDb();
	
	$param['tbName'] = $config->tb_main;
	
	$showColumns = $dao->showColumns( $config->tb_main );
	
	if ( !in_array( 'user_id', $showColumns ) OR !in_array( 'post', $showColumns ) ) {
		
		return;	
	}
	
	 
	
	$pri_key = $config->pri_key;
	$param['id'] = $datas->$pri_key;
	$param['link'] = ''. $getView->model_alias .'/proveDoc';
	
	if( empty( $datas->post ) ) {
		
		$status = ' สถานะเอกสาร: ร่าง';
		$buttons[] = '<a href="'. setLink( $param['link'], array( 'post' => 1, 'parent_id' => $param['id'] ) ) .'" style="width: 100%;  text-align: left;" title="" class="btn btn-default">โพสเอกสารนี้</a>';
		
	}
	else {
	
		$sql = "
			SELECT
				pt.*
			FROM admin_doc_inspect pt
			WHERE pt.tb_id = ". $param['id'] ."

			AND pt.tb_name = '". $param['tbName'] ."'
		";
		$prove = false;
		foreach( $dao->fetchAll( $sql ) as $ka => $va ) {

			$prove = true;
			
			$status = ' สถานะเอกสาร: อนุมัติ';
			
			$buttons[] = '<a href="'. setLink( $param['link'], array( 'parent_id' => $param['id'] ) ) .'" style="width: 100%;  text-align: left;" title="" class="btn btn-default">ยกเลิกอนุมัติ</a>';
			
		}

		if( $prove == false ) {
			
			$status = ' สถานะเอกสาร: โพส';
			
			$buttons[] = '<a href="'. setLink( $param['link'], array( 'post' => 0, 'parent_id' => $param['id'] ) ) .'" style="width: 100%; text-align: left;" title="" class="btn btn-default">ยกเลิกการโพสเอกสาร</a>';

			$buttons[] = '<a href="'. setLink( $param['link'], array( 'parent_id' => $param['id'] ) ) .'" style="width: 100%;  text-align: left;" title="" class="btn btn-default">อนุมัติเอกสารนี้</a>';
		}

	}
	
	return '
		<div class="btn-group">
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title="พิมพ์เอกสารหน้าปัจจุบัน">'. $status .'</a>
			
			<div class="dropdown-menu" role="menu" style="padding: 0;">'. implode( '', $buttons ) .'</div>
		</div>
	
	';
	
}


//
//
function getFilesInDirectory( $paths = array() ) {

	$data['folders'] = array();

	$data['files'] = array();

	for( $i = 1; $i <= 100; ++$i ) {

		if( empty( $paths ) ) {

			break;
		}

		$keep_path = array();
		foreach( $paths as $kp => $path ) {
			
			$ex = explode( '/', $path );
			
			
			if( $ex[count( $ex )-1] == '' ){
				
				unset( $ex[count( $ex )-1] );
			}
			//exit;
			$path = implode( '/', $ex );
		

			$dir = opendir( $path );

			while( $file = readdir( $dir ) ) {
				if( !in_array( $file, array( '.', '..' ) ) ) {

					$keepPath = array();

					if( $path != '' ) {
						
						$keepPath[] = $path;
					}
					
					if( $file != '' ) {
						
						$keepPath[] = $file;
					}
					

					

					$newPath = implode( '/', $keepPath );

					if( is_dir( $newPath ) ) {

						if( $newPath != '' ) {
							$keep_path[] = $newPath;

							$data['folders'][] = $newPath;
							
						}
					}
					else {

						if( $file != '' ) 
							$data['files'][] = $file;
						
						if( $newPath != '' ) 
							$data['files_path'][] = $newPath;
					}
				}

			}
		}

		$paths = $keep_path;
	}

	return $data;
}


function form_open_token() {
	
	return '<input type="hidden" name="'. get_token( 'name' ) .'" value="'. get_token() .'" />';
}

function get_token( $get = 'val' ) {
	
	if( $get == 'val' )
		return csrf_hash();
	
	 
	return csrf_token();
	
}



function front_link( $id = NULL, $sub = NULL, $get = array(), $token = true ){
	
	
	$sql = "
		SELECT 
			* 
		FROM aa_front_page 
		WHERE id = ". $id ."
	";
	
	$keep = array();
	
	if( $token == true ) 
		$get[get_token( 'name' )] = get_token( 'val' );
	foreach( getDb()->fetchAll( $sql ) as $ka => $va ) {
		
		$keep[] = $va->alias;
		
		if( !empty( $sub ) ) {
			$keep[] = $sub;
		}

		if ( !empty( $get ) )
			$keep[] = '?' . http_build_query( $get, 'flags_' );
		
		
		return base_url( implode( '/', $keep ) ) . '';
	}
	
	return base_url();
	
	
	if( !ShowUserCompany ) {
	}
	else {
		
		$sql = "
			SELECT 
				* 
			FROM erp_front_page 
			WHERE id = ". $id ."
		";
		
		$keep = array();
		$get['token__'] = get_token( $action = 'post', $attributes = array(), $hidden = array() );
		foreach( getDb()->fetchAll( $sql ) as $ka => $va ) {
			
			if( !empty( $_SESSION['u'] ) ) {
		
				$keep[] = $_SESSION['u']->user_company_name;
			}
			
			$keep[] = $va->KeyName;
			
			if( !empty( $sub ) ) {
				$keep[] = $sub;
			}

			if ( !empty( $get ) )
				$keep[] = '?' . http_build_query( $get, 'flags_' );
			
			
			return base_url( implode( '/', $keep ) ) . '';
		}
		
		return base_url();
	}
	
}


//
//
function comeBack() {
	
	if( isset( $_SERVER['HTTP_REFERER'] ) )
		return $_SERVER['HTTP_REFERER'];
	
	return front_link( 18 );
}


function loadProduct( $params = array() ) {
	
	
	return '
	<div class="row row-sm">
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products1.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color:#FFF;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: brown;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(46, 48, 173);"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(4, 104, 4);"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น SC026 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">555 บาท</h5>
										</div>
									</div>
								</div>
							</div>
			
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
	
	<div class="">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products2.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color:#FFF;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: brown;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(46, 48, 173);"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(4, 104, 4);"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น FC115 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">555 บาท</h5>
										</div>
									</div>
								</div>
							</div>
			
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="box-image">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products3.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color:#FFF;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: brown;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(46, 48, 173);"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(4, 104, 4);"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น AC007 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">777 บาท</h5>
										</div>
									</div>
								</div>
							</div>
			
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="box-image">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products4.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes1.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes2.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes3.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes4.png\');"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4">GOLDCITY รุ่น BJ035 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">555 บาท</h5>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products5.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color:#FFF;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: brown;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(46, 48, 173);"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(4, 104, 4);"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4">GOLDCITY รุ่น FF515 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">555 บาท</h5>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="box-image">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products6.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes5.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes6.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes7.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes8.png\');"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4">GOLDCITY รุ่น RE099 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">950 บาท</h5>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="box-image">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products7.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color:#FFF;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: brown;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(46, 48, 173);"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(4, 104, 4);"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4">GOLDCITY รุ่น DD638 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">555 บาท</h5>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products1.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes9.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes10.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes11.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes12.png\');"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น SW145 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">955 บาท</h5>
										</div>
									</div>
								</div>
							</div>


							<!-- You Recently Viewed Items -->
							<h2 class="mb-3 mt-4" style="padding-top: 60px;padding-bottom: 20px;">You Recently Viewed Items</h2>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products5.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color:#FFF;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: brown;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(46, 48, 173);"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(4, 104, 4);"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น OF256 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">555 บาท</h5>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="box-image">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products6.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes5.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes6.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes7.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes8.png\');"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น FR589 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1500</span>
											<h5 class="mb-0 mt-2 text-danger">755 บาท</h5>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="box-image">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products7.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color:#FFF;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: brown;"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(46, 48, 173);"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-color: rgb(4, 104, 4);"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น NB586 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1560 บาท</span>
											<h5 class="mb-0 mt-2 text-danger">455 บาท</h5>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-lg-3 col-xl-3 col-sm-6">
								<div class="card">
									<div class="card-body h-100">
										<div class="">
											<div class="d-flex product-sale">
												<i class="mdi mdi-heart-outline ms-auto wishlist"></i>
											</div>
											<a href="addtocart.php"><img class="w-100" src="page/assets/img/products/products1.png" alt="product-image"></a>
										</div>
										<!-- Color -->
										<div class="form-group mt-3">
											<div class="colors d-flex me-3 mt-2">
												<div class="row gutters-xs">
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes9.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes10.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes11.png\');"></span>
														</label>
													</div>
													<div class="w-auto me-2 ps-0 pe-0">
														<label class="colorinput">
															<input name="color" type="radio" value="" class="colorinput-input">
															<span class="colorinput-color" style="background-image:url(\'page/assets/img/detail-shoes/detail-shoes12.png\');"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
										<!--End Color -->
										<div class="">
											<p class="mb-2 mt-4 ">GOLDCITY รุ่น GH236 รองเท้าผ้าใบ รองเท้าแฟชั่น รองเท้าผ้าใบผูกเชือก แผ่นรองพื้นผลิตจาก Extra Soft EVA ใส่สบาย</p>
											<span class="text-discount">1450</span>
											<h5 class="mb-0 mt-2 text-danger">855 บาท</h5>
										</div>
									</div>
								</div>
							</div>

						</div>
	
	';
}


function getConfigLink( $params = array() ) {
	
 
	if( !empty( $_SESSION['u']->admin ) ) {
		
		return '
			<a target="_blank" href="'. front_link( 340, $params['config_id'] ) .'" class="btn btn-default">
				<strong class=""><i class="fa fa-cogs"></i></strong> '. $params['id'] .' 
				
			</a>
		';
		
		//'. json_encode( $params ) .'
	}
	
	
	
}

//
//
function encodePriKey( $id, $str = false ) {

	$KeyWord = 'BombKeyYu';
	if ( $str )
		return "MD5( CONCAT( '". $KeyWord ."', ". $id ." ) )";

	return md5( $KeyWord . $id );
}




//
//
function genJsonSql_( $json, $old_data = array(), $config = NULL, $parent_data = NULL ) {
	

	$replace = array();

	foreach ( $json->param as $kb => $vb ) {
		
		if (  $vb->type == 'parent_data' ) {
			
			foreach( $parent_data as $kp => $vp ) {
				$replace[$kp] = $vp;
				
			}
		}
		
	}
	
	if ( isset( $json->sql ) ) {
		
		$sql = $json->sql;
	}
	
	$sql = genCond_( $sql, $replace );
	
	return $sql;
	
}



//
//
function genJsonSql( $json, $old_data = array(), $config = NULL, $parent_data = NULL ) {
	
	$parent_data = ( array ) $parent_data;

	$old_data = ( array ) $old_data;

	$json = ( array ) $json;

	$sql = '';

	if ( isset( $json['sql'] ) ) {

		$sql = $json['sql'];

		if ( !empty( $json['param'] ) ) {

			$json['param'] = ( array ) $json['param'];

			$loop = 1;
			
			foreach ( $json['param'] as $kb => $vb ) {

				$vb = ( array ) $vb;

				$json['param'][$kb] = NULL;

				if ( isset( $vb['type'] ) ) {

					if ( $vb['type'] == 'func' ) {
						
						$vb['replace'] = $old_data;

						$json['param'][$kb] = call_user_func( $vb['name'], $vb );
						
					}
					else if ( $vb['type'] == 'rq' ) {

						if ( isset( $vb['tb_name'] ) ) {

							if ( !empty( $config ) ) {
						
								$json['param'][$kb] = @$_REQUEST[$vb['name']];
							}
							else {

								$json['param'][$kb] = @$_REQUEST[$vb['tb_name']][$vb['name']];
							}
						}
						else {

							$json['param'][$kb] = isset( $_REQUEST[$vb['name']] )? $_REQUEST[$vb['name']]: NULL;

						}
					}
					else if ( isset( $parent_data[$vb['name']] ) && $vb['type'] == 'parent_data' ) {

						$json['param'][$kb] = $parent_data[$vb['name']];
						
					}
					else if ( $vb['type'] == 'parameter' ) {
//$old_data['doc_no'] = 4545;


						$json['param'][$kb] = $old_data[$vb['name']];
						//$json['param'][$kb] = 'sdssdsds';
						//$json['param'][$kb] = 'fdsasdsd';
					}
					else if ( $vb['type'] == 'session' ) {

						if ( $vb['name'] == 'user_id' ) {

							$vb['name'] = Uid;
						}

						if ( is_object( $_SESSION[$vb['name']] ) ) {

							$f = $vb['f'];
							
							$json['param'][$kb] = $_SESSION[$vb['name']]->$f;
						}
						else {

							$json['param'][$kb] = $_SESSION[$vb['name']];
						}
					}
					else if ( $vb['type'] == 'txt' ) {

						++$loop;

						$json['param'][$kb] = $vb['name'];
					}
				}
				else {
					if( isset( $vb[0] ) )
						$json['param'][$kb] = $vb[0];
				}
			}
		}
	}
	
	if( !empty( $json['param'] ) ) {
		
		foreach( $json['param'] as $kp => $vp ) {
			$sql = str_replace( array_keys( $json['param'] ), $json['param'], $sql );
		}
	}
	

	if ( isset( $config ) ) {

		$arr_replace = array(
			'[tb_main]' => $config->tb_main,
			'[pri_key]' => $config->pri_key,
			'[tb_parent]' => $config->tb_main,
			'[main_prikey]' => $config->pri_key,
			'[parent_config_Id]' => $config->config_id,
			'[FILE_URL]' => FILE_URL
		);
		//arr( $config );
	//	echo $config->tb_main;

		if ( isset( $json['tb_main'] ) ) {
			$arr_replace['[tb_sub]'] = $json['tb_main'];
		}

		$sql = str_replace( array_keys( $arr_replace ), $arr_replace, $sql );

	}

	return $sql;
}



function getAdminMenu( $parent_id = NULL ) {
	/*
	ELECT * FROM aa_front_page WHERE parent_id IS NULL ORDER BY config_id DESC
	*/
	
	if( empty( $_SESSION['u']->admin ) ) {
		
		
		if( empty( $parent_id ) ) {
			
			//$lis[] = '<li aria-haspopup="true"><a href="'. base_url( 'logout' ) .'" style="border: 1px solid #fff;padding: 7px 12px;margin: 7px 5px 5px 5px;border-radius: 30px;background: #FDD400;color: #fff;">ออกจากระบบ</a></li>';
		}
		
		
		//return implode( '', $lis );
		
		return false;
	}
	if( empty( $parent_id ) ) {
		
		$sql = "
			SELECT 
				* 
			FROM aa_front_page 
			WHERE user_login = 1  
			AND ( admin_menu IS NOT NULL OR admin_menu != '0' )
			AND `active` = 1
			AND parent_id IS NULL
			ORDER BY order_number ASC
		";
	}
	else {
		
		$sql = "
			SELECT 
				* 
			FROM aa_front_page 
			WHERE user_login = 1  
			AND ( admin_menu IS NOT NULL OR admin_menu != '0' )
			AND `active` = 1
			AND parent_id = ". $parent_id ."
			ORDER BY order_number ASC
		";
	}
	
	$lis = array();
	foreach( getDb()->fetchAll( $sql ) as $ka => $va ) {
		
		$getAdminMenu = getAdminMenu( $va->id );
		
		$ul = '';
		
		if( !empty( $getAdminMenu ) ) {
			$ul = '<ul class="sub-menu">'. $getAdminMenu .'</ul>';
		}
		
		$menu_link = front_link( $va->id );
		
		$test = json_decode( $va->admin_menu );
		
		if( isset( $test->link_to ) ) {
			$menu_link = front_link( $test->link_to->id, $test->link_to->sub );
			//$menu_link = base_url( $test->link_to );
		}
		
		$lis[] = '
			<li aria-haspopup="true">
				<a href="'. $menu_link .'" >'. $va->title .'</a>'. $ul .'
			</li>
		';
	}
	
	
	if( empty( $parent_id ) ) {
		
		$lis[] = '<li aria-haspopup="true"><a href="'. base_url( 'logout' ) .'" style="border: 1px solid #fff;padding: 7px 12px;margin: 7px 5px 5px 5px;border-radius: 30px;background: #FDD400;color: #fff;">ออกจากระบบ</a></li>';
	}
	
	
	return implode( '', $lis );
	
	
}

function newsLink( $id ) {
	
	return front_link( 6, $id );
}

function getVdosLinks() {
	return '
		<div class="container">
		<hr>
			<div class="row">

				<div class="col-lg-4">
					<div class="container-fluid">
						<div class="mt-5">
							<a href="#" data-bs-toggle="modal" data-bs-target="#Modal13">
								<div class="vdotitle1 wrapper-card-img">
									<img src="front/assets/img/kla.png" alt="">
									<p>เช็กลิสต์เสพติดศัลยกรรม "ทำน้อย</p>
								</div>
								<p class="text-muted">Craig Bator - 27 Das 2020</p>
								<p>แม๊!...หนูอยากเป็น “ผู้จัดการมรดก” …รู้หรือไม่ “ผู้จัดการมรดก” ไม่ได้หมายความว่าคุณจะได้รับทรัพย์มรดก...ความเข้าใจผิดๆ ที่อาจทำให้คุณติดคุก!</p>
							</a>
						</div>
					</div>
					<!-- Modal -->
					<div class="modal fade" id="Modal13" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<iframe width="100%" height="315" src="https://www.youtube.com/embed/10f2QJPE-9I" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					<div style="margin-top: 20px; margin-bottom: 20px;">
						<img src="front/assets/img/icon/clock (2).png" alt="" style="width: 10px; height: 10px; margin-left: 10px;">
						<span>2 ชั่วโมงที่แล้ว</span>
						<a href="#"><span class="text-success">การเมือง</span></a>
					</div>
				</div>
			</div>
		</div>
	
	';
	
}


function showParagraph( $text = NULL, $strip_tags = array( 'img' ) ) {
	
	$text = htmlspecialchars_decode( $text );
	
	
	$strip_tags[] = 'script';
	foreach( $strip_tags as $ks => $vs ) {
		$text = strip_tags( $text, $vs );
		
	}
	
	return $text;
}


//
//
function timeToText( $time ) {
	
	
	date_default_timezone_set('Asia/Bangkok');
    $now = date('Y-m-d H:i:s');
	
	
	return getDayHour( $time, $now );
	
	
	
	
	$dao = getDb();

	$sql = "
		SELECT
			( DATEDIFF( NOW(), '". $time ."' ) ) AS datediff_,
			YEAR( NOW() ) - YEAR( '". $time ."' ) AS yeardiff_,
			MONTH( NOW() ) - MONTH( '". $time ."' ) AS monthdiff_


		"; //30

	$res = $dao->fetch( $sql );

//arr( $res );
	//$txt = '';
	$txt = $time;
	
	///'' . gettime_( $time, 8 );
	
	//arr( $txt );
	if ( $res->yeardiff_ != 0 ) {
		
		///echo 'dsfaasdfsdfa';

		$ex = explode( ' ', gettime_( $time, 7 ) );
		$txt = getMonthLongName( $ex[0] ) . ' ' . $ex[1];



	}
	else if ( $res->monthdiff_ != 0 ) {
		///echo 'ttttt';

		$ex = explode( ' ', gettime_( $time, 9 ) );
		$txt = $ex[0] . ' ' . getMonthLongName( $ex[1] );

	}
	else if ( $res->datediff_ > 7 ) {
		 
		$txt = 'วันที่ ' . gettime_( $time, 11 );

	}
	else if ( $res->datediff_ > 0 ) {
	///	echo 'wwww';
		$getWeekDetail = getWeekDetail( gettime_( $time, 10 ) );

		$txt = '' . $getWeekDetail['label'];



	}


	return $txt;
}

//
//
function getMonthLongName( $index ) {

	//arr( )
	
	$arr = array( 'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม' );


	//return $arr[0];
	if( !isset( $arr[$index - 1] ) ) {
		return false;
	}
	return $arr[$index - 1];
}


//
//
function gettime_( $time, $index = 1, $thaiTime = false, $custom = NULL ) {

	$dao = getDb();
	
	$version[1] = '%d/%m/%Y';
	$version[2] = '%d';
	$version[3] = '%d-%m-%Y';
	$version[4] = '%Y-%m-%d %H:%i:%s';
	$version[5] = '%Y-%m-%d';
	$version[6] = '%Y';
	$version[7] = '%m %Y';
	$version[8] = '%H:%i';
	$version[9] = '%e %m';
	$version[10] = '%a';
	$version[11] = '%e';
	$version[12] = '%Y-%m';
	$version[13] = '%d/%m/%y';
	$version[14] = 'l dS \o\f F Y h:i:s A';
	$version[15] = '%m/%Y';
	$version[16] = '%d/%m/%Y %H:%i:%s';
	$version[17] = '%m';
	$version[18] = '%Y-%m-01';
	$version[19] = '%d-%m-%Y %H:%i';
	if ( empty( $time ) ) {

		$sql = "SELECT DATE_FORMAT( NOW(), '". $version[$index] ."' ) as t";

	}
	else if ( !empty( $custom ) ) {

		$sql = "SELECT DATE_FORMAT( ADDDATE( '". $time ."', INTERVAL ". $custom ." ), '". $version[$index] ."' ) as t";

	}
	else if ( $thaiTime == true ) {

		$sql = "SELECT DATE_FORMAT( ADDDATE( '". $time ."', INTERVAL 543 year ), '". $version[$index] ."' ) as t";

	}
	else {

		$sql = "SELECT DATE_FORMAT( '". $time ."', '". $version[$index] ."' ) as t";
	}
	
	foreach( $dao->fetchAll( $sql ) as $ka => $res ) {
		
		return $res->t;
	}

	 

	
	return '&nbsp;';
	
	
}



//
//
function getVal( $val, $va, $status = 'r', $res = array(), $comma = ',', $n = NULL, $main_id = NULL, $parent_data = NULL  ){
	

	$dao = getDb();
  
	if ( is_object( $va ) ) {
		$va = convertObJectToArray( $va );
	}

	if( !empty( $va['showOnReady'] ) ) {
		
		return  getDesc( $res, $va['showOnReady'] );
	}
	else if ( isset( $va['inputformat'] ) && $va['inputformat'] == 'help' ) {

		if ( in_array( $va['helpdetail']->type, array( 'look_comment' ) ) ) {

			$val = getDesc( $res, $va['helpdetail']->label );

		}
		else if ( in_array( $va['helpdetail']->type, array( 'help_full', 'multi_check' ) ) ) {
			
			//arr( $va['helpdetail']);
			
			if( !empty( $va['showOnReady'] ) ) {
				
				$val =  getDesc( $res, $va['showOnReady'] );
				 
				
			}
			else{
				

				$clip = '';
				if( !empty( $va['ref_model_id'] ) ) {

					$sql = "
						SELECT

							b.new_config_id
						FROM admin_model b
						WHERE b.model_id = " . $va['ref_model_id'];

					$ref_view = $dao->fetch( $sql );

					//
					//
					if ( empty( $ref_view->new_config_id ) )
						return '<span class="red">ยังไม่ได้ให้สิทธิ์การใช้งาน </span>';

				//echo $ref_view->new_config_id;
					$ref_config = getConfig_( $ref_view->new_config_id );


					$va['helpdetail']->main_sql = $ref_config->main_sql;

					$va['helpdetail']->pri_key = $ref_config->pri_key;

					$va['helpdetail']->label = $ref_config->label;
				}
				

				$sql_help_detail = $va['helpdetail']->main_sql;
				//echo $sql_help_detail;
				
				//echo '<br>';
				//echo '<br>';
				$explode = explode( ',', $val );

				$sql_help_detail = str_replace( '%filter;', "HAVING ". $va['helpdetail']->pri_key ." IN ( '". implode( "', '", $explode ) ."' )", $sql_help_detail );

				$sql_help_detail = str_replace( '?', "*", $sql_help_detail );

				$sql_help_detail = str_replace( '[having]', '', $sql_help_detail );

				$sql_help_detail = str_replace( '[company_id]', '1', $sql_help_detail );
				
				

				if ( !empty( $va['helpdetail']->more_filter_sql ) ) {

					$more_filter_sql = json_decode( $va['helpdetail']->more_filter_sql );

					//
					//
					if ( !empty( $more_filter_sql->param ) ) {
						$loop = 1;
						foreach ( $more_filter_sql->param as $kb => $vb ) {

							if ( $vb->type == 'session' ) {

								if ( $vb->name == 'user_id' ) {

									$vb->name = Uid;
								}

								if ( is_object( $_SESSION[$vb->name] ) ) {

									$f = $vb->f;

									$keep[$kb] = $_SESSION[$vb->name]->$f;
								}
								else {

									$keep[$kb] = $_SESSION[$vb->name];
								}
							}
							else if ( $vb->type == 'rq' ) {
								if ( isset( $_REQUEST[$vb->name] ) )
									$keep[$kb] = str_replace( ' ', '%', $_REQUEST[$vb->name] );
							}
							else if ( $vb->type == 'parameter' ) {
								$keep[$kb] = $_REQUEST[$vb->name];
							}
							else if ( $vb->type == 'sql' ) {

								$keep[$kb] = $dao->fetch( $vb->name )->t;

							}
							else if ( $vb->type == 'txt' ) {

								++$loop;

								$keep[$kb] = $va->name;
							}
						}
					}
				}
				
				
				$keep['[sort]'] = '';
				
				$keep['[LIMIT]'] = '';

				$sql_help_detail = str_replace( array_keys( $keep ), $keep, $sql_help_detail );
				
				$sql_help_detail = genCond_( $sql_help_detail, array() );
				
	// arr( $sql_help_detail);

				$res2 = $dao->fetchAll( $sql_help_detail, array( ':val' => $val ) );

				if ( !$res2 ) {
					$val = '-';
				}
				else {
					$keep = array();
					
					//$maxShow = 3;
					$pri_key = $va['helpdetail']->pri_key;
					
					foreach ( $res2 as $kb => $vb ) {

						if ( $status == 'r' ) {
//arr( $vb );
							$keep[] = getDesc( $vb, $va['helpdetail']->label ) . '';
							
							//$va['helpdetail']->label;
							//

						}
						else {

							$delButton = '';
							if( in_array( $va['helpdetail']->type, array( 'multi_check' ) ) ) {

								$delButton = '
									<span class="remove-list-line" data-pri_key="'. $vb->$pri_key .'">
										<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
									</span>
								';
							}



							$keep[] = '
								<div style="padding:0;">

									'. getDesc( $vb, strip_tags( $va['helpdetail']->label ) ) .'

									'. $delButton .'

								</div>
							';
						}
					}

					$val = implode( '', $keep );
					
					return '' . $val;
					//$val = 'dsddsfsdad';
				}
			
			
				
			}
			
			

		
		}
		
		
	}

	if ( isset( $va['forum_on_ready'] ) && $va['forum_on_ready'] != '' && !empty( $res ) ) {

		$keep = array();

		foreach ( $res as $kd => $vd ) {

			$keep['['. $kd .']'] = $vd;
		}

		$keep[','] = '';

		$str = str_replace( array_keys( $keep ), array_values( $keep ), $va['forum_on_ready'] );

		$sql = "
			SELECT
				( ". $str ." ) as t
		";

		$cal = $dao->fetch( $sql );

		if ( $cal ) {

			$val = $cal->t;
		}
		else {
			$val = 0;
		}
	}


	if ( !empty( $va['inputformat'] ) ) {

		if ( $va['inputformat'] == 'time' ) {

				if (!empty( $val ) ) {

					$val = gettime_( $val, 19 );
				}
				else {
					$val = '';
				}

			 
		}
		else if ( $va['inputformat'] == 'short_date' ) {
		

			if ( !empty( $val ) ) {

				if ( is_numeric( strpos( $val, '-' ) ) ) {

					$ex_date = explode( '-', $val );

					if ( isset( $ex_date[1], $ex_date[0], $ex_date[2] ) ) {

						$val =  gettime_( $val, 13, $thaiTime = false, $custom = NULL );
					}
				}
				else {
					$val = '';
				}
			}
		}
		else if ( $va['inputformat'] == 'date' ) {
		

			if ( !empty( $val ) ) {

				if ( is_numeric( strpos( $val, '-' ) ) ) {

					$ex_date = explode( '-', $val );

					if ( isset( $ex_date[1], $ex_date[0], $ex_date[2] ) ) {

						$val = $ex_date[2] . '/' . makeFrontZero( $ex_date[1], $require_zero = 2 ) . '/' . makeFrontZero( $ex_date[0], $require_zero = 2 );
					}
				}
				else {
					$val = '';
				}
			}
		}
		elseif ( $va['inputformat'] == 'money' ) {

			if ( $status == 'r' && $val == 0 ) {

				$val = '-';
			}
			else {
				$dot = 2;
				if ( isset( $va['dot'] ) ) {

					$dot = $va['dot'];
				}

				$val = getNumFormat( $val, $comma, $dot );
			}

		}
		elseif ( $va['inputformat'] == 'percent' ) {

			$val = getStrPercent( $val );
		}
		elseif ( $va['inputformat'] == 'str_percent' ) {

			if ( !is_numeric( strpos( $val, '%' ) ) )
				$val = getNumFormat( $val, $comma, 2 );

			if ( $status == 'r' && $val == '0%' ) {

				$val = '-';
			}
		}
		elseif ( $va['inputformat'] == 'thaidate' ) {

			$val = getThTimeFormat( $val );
		}
		elseif ( $va['inputformat'] == 'comment' ) {

			$val = nl2br( $val );
		}
		elseif ( $va['inputformat'] == 'password' && $status == 'r' ) {

			$val = '';
		}
		elseif ( $va['inputformat'] == 'auto_number' && $status == 'r' ) {
			if( !empty( $va['record'] ) ) {
				
				
				
				$val = makeFrontZero( $n, $va['record'] );
			}
			else {
				
				$val = $n;
			}
		
			 
			
		}
		elseif ( $va['inputformat'] == 'csv' && $status == 'r' ) {
// 
			if( !empty( $val ) && file_exists( $val ) ) {
				 
					
				$val = '<a target="_blank" href="'. $val .'?rand='. rand() .'"><i class="fa fa-paperclip" aria-hidden="true"></i></a>';
				 
				
				
			}
			else {
				$val = '';
			}
			
		}
	}

	if ( $status == 'r' ) {

		if ( !empty( $va['input_type'] ) ) {

			$json = json_decode( $va['input_type'] );
			
			
			if ( in_array( $json->type, array( 'hashtag' ) ) ) {

				$val  = strip_tags( $val ); 
			//	$val  = 'dsfafdsafsdadfs'; 
			} 
			else if ( $json->type == 'month' ) {
				
				$ex = explode( '-', $val );
				
				$val  = ''. $ex[1] .'/'. $ex[0] .'';
			}
			else if ( $json->type == 'select' ) {

				$cond = 'HAVING';
				if ( !empty( $json->cond ) ) {
					$cond = 'WHERE';
				}

				$filter = $cond ." ". $json->pri_key ." = '". $val ."'";

				$sql = str_replace( array( '%filter;' ), array( $filter ), $json->sql );

				$json->sql = $sql;

				$sql = genJsonSql( $json, $res );
				
				//arr( $json->replaceSql->sdddsds );
				
				if( isset( $json->replaceSql ) ) {
					
					foreach( $json->replaceSql as $kr => $vr ) {
						
						$gg[$kr] = $vr;
					}
					
					foreach( $res as $kk => $vk ) {
						$gg[$kk] = $vk;
					}
					
					$sql = genCond_( $sql, $gg );
					
				}
				
				
				$val  = getDesc( $dao->fetch( $sql ), $json->desc );
	
			}
			else if ( $json->type == 'file' ) {

				if ( isset( $json->showas ) && isset( $res->id ) ) {
					
			//arr( $res );
				//	exit;
					$val = '<img class="full-size" data-target="#myModalPop" data-toggle="modal" src="'. setLink( 'ajax/loadBarCode/'. $va['config_columns_id'] .'/'. $res->id .'' ) .'" >';
					

				}
				else if ( !empty( $val ) && file_exists(  $val ) ) {
					$val = '<img class="full-size" data-target="#myModalPop" data-toggle="modal" src="'. base_url( $val . '?rand='. rand() .'' ) .'" >';
					

				}
				else {
					$val = '<img class="full-size" src="'. base_url( 'themes/default/admin/assets/images/noAvatar.png' ) .'">';
				}
				
				
			}
			else if ( $json->type == 'checkbox' ) {

				if ( !empty( $val ) ) {

					$val = 'ใช่';

				}
				else {
					$val = 'ไม่ใช่';
				}
			}
		}

		if ( empty( $val ) )
			$val = '';
	}

	$val = stripslashes( $val );
	
	return $val;
	
	
	//
}






