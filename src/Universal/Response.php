<?php

/*
 * Bear CMS Universal
 * https://github.com/bearcms/universal
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Universal;

/**
 * 
 */
class Response
{

    /**
     *  The response body.
     * 
     * @var string 
     */
    public $content = null;

    /**
     * The MIME type.
     * 
     * @var string 
     */
    public $mimeType = null;

    /**
     * A list of headers in the following format: ['name'=>'value', 'name'=>'value']
     * 
     * @var array 
     */
    public $headers = [];
}
