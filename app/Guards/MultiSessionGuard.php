<?php

namespace App\Guards;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class MultiSessionGuard extends SessionGuard
{
    /**
     * The session key for this guard.
     *
     * @var string
     */
    protected $sessionKey;

    /**
     * Create a new authentication guard.
     *
     * @param  string  $name
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  \Illuminate\Http\Request|null  $request
     * @param  string|null  $sessionKey
     * @return void
     */
    public function __construct($name, UserProvider $provider, Session $session, Request $request = null, $sessionKey = null)
    {
        parent::__construct($name, $provider, $session, $request);
        $this->sessionKey = $sessionKey ?: $name;
    }

    /**
     * Get the name of the guard. Prepends the session key to make it unique.
     */
    public function getName()
    {
        return 'login_' . $this->sessionKey . '_' . sha1(static::class);
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        if ($this->loggedOut) {
            return;
        }

        return $this->user()
                    ? $this->user()->getAuthIdentifier()
                    : $this->session->get($this->getName());
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
        $this->loggedOut = false;
        $this->fireAuthenticatedEvent($user);
    }

    /**
     * Update the session with the given ID.
     *
     * @param  string  $id
     * @return void
     */
    protected function updateSession($id)
    {
        $this->session->put($this->getName(), $id);
        $this->session->migrate(true);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $this->clearUserDataFromStorage();

        if (! is_null($this->user) && ! empty($this->user->getRememberToken())) {
            $this->cycleRememberToken($this->user);
        }

        if (isset($this->events)) {
            $this->events->dispatch(new \Illuminate\Auth\Events\Logout($this->name, $this->user));
        }

        $this->user = null;
        $this->loggedOut = true;
    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->forget($this->getName());
        $this->session->forget($this->getRecallerName());
    }

    /**
     * Get the cookie creator instance used by the guard.
     *
     * @return \Illuminate\Contracts\Cookie\QueueingFactory
     *
     * @throws \RuntimeException
     */
    public function getCookieJar()
    {
        if (! isset($this->cookie)) {
            throw new \RuntimeException('Cookie jar has not been set.');
        }

        return $this->cookie;
    }

    /**
     * Get the unique remember token name for this guard.
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_' . $this->sessionKey . '_' . sha1(static::class);
    }
}