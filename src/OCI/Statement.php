<?php

declare(strict_types=1);

namespace Oracle\OCI;

use Oracle\OCI\Exception\OCIException;

class Statement
{
    /**
     * @param Connection $connection
     * @param resource $statement
     */
    public function __construct(
        private Connection $connection,
        private mixed $statement
    ) {
    }


    public function __destruct()
    {
        \oci_free_statement($this->statement);
    }


    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }


    /**
     * @param string $name
     * @param mixed $value
     * @param int $maxlength
     * @param int $type
     * @return self
     */
    public function bindByName(
        string $name,
        mixed &$value,
        int $maxlength = -1,
        int $type = \SQLT_CHR
    ): self {
        return \oci_bind_by_name($this->statement, $name, $value, $maxlength, $type) === true
            ? $this
            : throw new OCIException($this->statement);
    }


    /**
     * @param int $mode
     * @return $this
     */
    public function execute(int $mode = \OCI_COMMIT_ON_SUCCESS): self
    {
        return \oci_execute($this->statement, $mode) === true
            ? $this
            : throw new OCIException($this->statement);
    }


    /**
     * @return iterable<int,mixed[]>
     */
    public function query(): iterable
    {
        $rowNum = 0;
        for ($row = $this->execute()->fetch(); is_array($row); $row = $this->fetch()) {
            yield ++$rowNum => $row;
        }
    }


    /**
     * @return mixed[]|null
     */
    public function fetch(): array|null
    {
        $row = \oci_fetch_assoc($this->statement);
        return is_array($row) ? $row : null;
    }
}
