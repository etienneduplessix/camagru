<?php
declare(strict_types=1);

error_reporting(E_ALL);

/**
 * Class Config
 *
 * Handles loading the configuration settings.
 */
class Config {
    private static ?array $config = null;

    public static function get(): array {
        if (self::$config === null) {
            // Using ROOT_DIR if defined; otherwise, fallback to __DIR__
            $configPath = (defined('ROOT_DIR') ? ROOT_DIR : __DIR__) . '/conf/config.ini';
            self::$config = parse_ini_file($configPath);
        }
        return self::$config;
    }
}


class Auth {
    private const SESSION_KEY = 'moi';
    private const TOKEN_KEY = 'sessiontoken';

    /**
     * Logs out the current user.
     */
    public static function logOut(): void {
        session_unset();
    }

    /**
     * Checks whether a user is currently logged in.
     *
     * @return bool
     */
    public static function isLoggedIn(): bool {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Returns the current logged-in username.
     *
     * @return string|null
     */
    public static function currentUser(): ?string {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    /**
     * Redirects to a given URL and exits.
     *
     * @param string $href
     */
    private static function redirectTo(string $href): void {
        header("Location: {$href}");
        exit;
    }
}

/**
 * Class Utils
 *
 * Provides utility methods.
 */
class Utils {
    /**
     * Returns the current page (script) name.
     *
     * @return string
     */
    public static function currentPage(): string {
        return $_SERVER['PHP_SELF'];
    }

    /**
     * Sets an error message in the session and redirects to an error page.
     *
     * @param string $errorMessage
     */
    public static function redirectError(string $errorMessage): void {
        $_SESSION['err'] = [
            'date'    => date("Y-m-d H:i:s"),
            'message' => $errorMessage
        ];
        self::redirectTo('error.php');
    }

    /**
     * Redirects to a given URL and exits.
     *
     * @param string $href
     */
    private static function redirectTo(string $href): void {
        header("Location: {$href}");
        exit;
    }
}
?>
