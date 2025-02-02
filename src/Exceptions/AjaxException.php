<?php

declare(strict_types = 1);

namespace Weiran\Framework\Exceptions;

/**
 * AjaxException
 */
class AjaxException extends BaseException
{
    /**
     * @var array collection response contents
     */
    protected $contents;

    /**
     * Constructor.
     * @param array|string $contents contents
     */
    public function __construct($contents)
    {
        if (is_string($contents)) {
            $contents = ['result' => $contents];
        }

        $this->contents = $contents;

        parent::__construct(json_encode($contents));
    }

    /**
     * Returns invalid fields.
     */
    public function getContents()
    {
        return $this->contents;
    }
}
