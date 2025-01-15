<?php

declare(strict_types=1);

namespace Oracle\OCI\Exception;

class OCIException extends \Exception
{
    private int $offset = 0;
    private string $sqlText = '';


    /**
     * @param resource|null $resource
     * @param string|null $message
     */
    public function __construct(
        mixed $resource = null,
        string|null $message = null
    ) {
        /** @var array{code:int,message:string,offset:int,sqltext:string}|false $error */
        $error = oci_error($resource);
        if (!is_array($error)) {
            $error = null;
        }

        parent::__construct(
            $error['message'] ?? $message ?? '',
            $error['code'] ?? 0,
            null
        );

        $this->offset = $error['offset'] ?? 0;
        $this->sqlText = $error['sqltext'] ?? '';
    }


    public function getOffset(): int
    {
        return $this->offset;
    }


    public function getSqlText(): string
    {
        return $this->sqlText;
    }
}
