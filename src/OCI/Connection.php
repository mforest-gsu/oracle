<?php

declare(strict_types=1);

namespace Oracle\OCI;

use Oracle\OCI\Exception\OCIException;

class Connection
{
    /**
     * @var resource|false $handle
     */
    private mixed $handle = false;
    private string $username = "";
    private string $password = "";
    private string|null $connection = null;
    private string $charset = "";
    private int $sessionMode = \OCI_DEFAULT;
    private bool $persistent = false;
    private bool $exclusive = false;


    /**
     * @param string $username
     * @param string $password
     * @param string|null $connection
     * @param string $charset
     * @param int $sessionMode
     * @param bool $persistent
     * @param bool $exclusive
     */
    public function __construct(
        string $username = "",
        string $password = "",
        string|null $connection = null,
        string $charset = "",
        int $sessionMode = \OCI_DEFAULT,
        bool $persistent = false,
        bool $exclusive = false
    ) {
        $this
            ->setUsername($username)
            ->setPassword($password)
            ->setConnection($connection)
            ->setCharset($charset)
            ->setSessionMode($sessionMode)
            ->setPersistent($persistent)
            ->setExclusive($exclusive);
    }


    public function __destruct()
    {
        $this->close();
    }


    /**
     * @param bool $force
     * @return $this
     */
    public function connect(bool $force = false): self
    {
        if (is_resource($this->handle) && $force === false) {
            return $this;
        }

        $connect = match (true) {
            $this->isExclusive() => \oci_new_connect(...),
            $this->isPersistent() => \oci_pconnect(...),
            default => \oci_connect(...)
        };

        $this->handle = $connect(
            $this->getUsername(),
            $this->getPassword(),
            $this->getConnection(),
            $this->getCharset(),
            $this->getSessionMode()
        );

        return is_resource($this->handle)
            ? $this
            : throw new OCIException(null, "Unable to create connection handle");
    }


    /**
     * @param string|resource|false $sql
     * @return Statement
     */
    public function parse(mixed $sql): Statement
    {
        if (is_resource($sql)) {
            $sql = stream_get_contents($sql);
        }
        if (!is_string($sql)) {
            throw new OCIException(null, "Invalid sql string");
        }

        $stmt = oci_parse($this->connect()->getHandle(), $sql);

        return is_resource($stmt)
            ? new Statement($this, $stmt)
            : throw new OCIException($this->getHandle());
    }


    /**
     * @return $this
     */
    public function commit(): self
    {
        return \oci_commit($this->getHandle()) === true
            ? $this
            : throw new OCIException($this->getHandle());
    }


    /**
     * @return $this
     */
    public function rollback(): self
    {
        return \oci_rollback($this->getHandle()) === true
            ? $this
            : throw new OCIException($this->getHandle());
    }


    /**
     * @return $this
     */
    public function close(): self
    {
        if (is_resource($this->handle)) {
            \oci_close($this->handle);
        }
        $this->handle = false;
        return $this;
    }


    #region Getters/Setters

    /**
     * @return resource
     */
    protected function getHandle(): mixed
    {
        return is_resource($this->handle)
            ? $this->handle
            : throw new OCIException(null, "Connection handle not initialized");
    }


    public function getUsername(): string
    {
        return $this->username;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }


    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


    public function getConnection(): string|null
    {
        return $this->connection;
    }
    public function setConnection(string|null $connection): self
    {
        $this->connection = $connection;
        return $this;
    }


    public function getCharset(): string
    {
        return $this->charset;
    }
    public function setCharset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }


    public function getSessionMode(): int
    {
        return $this->sessionMode;
    }
    public function setSessionMode(int $sessionMode): self
    {
        $this->sessionMode = $sessionMode;
        return $this;
    }


    public function isPersistent(): bool
    {
        return $this->persistent;
    }
    public function setPersistent(bool $persistent): self
    {
        $this->persistent = $persistent;
        return $this;
    }


    public function isExclusive(): bool
    {
        return $this->exclusive;
    }
    public function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    #endregion Getters/Setters
}
