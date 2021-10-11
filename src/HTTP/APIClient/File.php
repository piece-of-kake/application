<?php

namespace PoK\HTTP\APIClient;

class File
{
    /**
     * @var string
     */
    private $parameterName;

    /**
     * @var Psr\Http\Message\StreamInterface | string | mixed
     */
    private $contents;

    /**
     * @var string
     */
    private $fileName;

    /**
     * File constructor.
     * @param string $parameterName The name of under which the file will be available in the request. You get the file by this key.
     * @param mixed $contents Can be a Psr\Http\Message\StreamInterface or result of file_get_contents():string or something else.. I haven't really tested what can be here.
     * @param string $fileName The actual name of the file.
     */
    public function __construct(string $parameterName, $contents, string $fileName)
    {
        $this->parameterName = $parameterName;
        $this->contents = $contents;
        $this->fileName = $fileName;
    }

    public function compile()
    {
        return [
            'name' => $this->parameterName,
            'contents' => $this->contents,
            'filename' => $this->fileName
        ];
    }
}