<?php

namespace App\Services;

class NamingService
{
    /**
     * List of reserved usernames for routes and special pages.
     */
    protected static array $reservedUsernames = [
        'settings', 'explore', 'notifications', 'login', 'logout', 'signup',
        'new', 'repositories', 'dashboard', 'admin', 'api', 'about', 'contact',
    ];

    /**
     * Returns true if the username is valid and not reserved.
     */
    public static function isValidUsername(string $username): bool
    {
        if (in_array($username, self::$reservedUsernames, true)) {
            return false;
        }
        return (bool) preg_match(self::usernameRegex(), $username);
    }

    /**
     * Returns the regex for a valid username (GitHub style).
     */
    public static function usernameRegex(): string
    {
        // 1-39 chars, lowercase, alphanumeric or hyphens, not start/end with hyphen, no consecutive hyphens
        return '/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}$/';
    }

    /**
     * Returns true if the repository name is valid.
     */
    public static function isValidRepositoryName(string $repoName): bool
    {
        if (strlen($repoName) < 1 || strlen($repoName) > 100) {
            return false;
        }
        if (!preg_match(self::repositoryNameRegex(), $repoName)) {
            return false;
        }
        // No consecutive dots, hyphens, or dot-hyphen/hyphen-dot/underscore combos
        if (preg_match('/(\.\.|--|\.-|-\.|__)/', $repoName)) {
            return false;
        }
        return true;
    }

    /**
     * Returns the regex for a valid repository name (GitHub style).
     */
    public static function repositoryNameRegex(): string
    {
        // 1-100 chars, lowercase, alphanumeric, hyphens, underscores, dots
        // Not start/end with . or -, no consecutive . or -
        return '/^[a-z\d](?:[a-z\d._-]{0,98}[a-z\d])?$/';
    }

    /**
     * Returns true if the repository path is valid.
     */
    public static function isValidRepositoryPath(string $path): bool
    {
        return (bool) preg_match(self::repositoryPathRegex(), $path);
    }

    /**
     * Returns the regex for a valid repository path: username/reponame
     */
    public static function repositoryPathRegex(): string
    {
        // username + slash + repo
        return '/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}\/[a-z\d](?:[a-z\d._-]{0,98}[a-z\d])?$/';
    }

    /**
     * Returns true if the given username is reserved.
     */
    public static function isReservedUsername(string $username): bool
    {
        return in_array($username, self::$reservedUsernames, true);
    }

    /**
     * Returns the array of reserved usernames.
     */
    public static function reservedUsernames(): array
    {
        return self::$reservedUsernames;
    }
}
