<?php

/*
 * Bear CMS Universal
 * https://github.com/bearcms/universal
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

/**
 * 
 */
class Universal
{

    /**
     * Creates a new BearCMS/Universal instance.
     * 
     * @param array $config The Bear CMS configuration.
     */
    public function __construct(array $config = [])
    {
        $app = new \BearFramework\App();

        if (!isset($config['appSecretKey'])) {
            throw new \Exception('The appSecretKey option is required!');
        }

        $app->enableErrorHandler([
            'logErrors' => isset($config['logErrors']) ? (int) $config['logErrors'] > 0 : true,
            'displayErrors' => isset($config['displayErrors']) ? (int) $config['displayErrors'] > 0 : false,
        ]);

        if (isset($config['dataDir']) && strlen($config['dataDir']) > 0) {
            $app->data->useFileDriver($config['dataDir']);
        } else {
            throw new \Exception('The dataDir option is required!');
        }
        $app->cache->useAppDataDriver();
        if (isset($config['logsDir']) && strlen($config['logsDir']) > 0) {
            $app->logs->useFileLogger($config['logsDir']);
        } else {
            $app->logs->useNullLogger();
        }

        $bearCMSConfig = [
            'serverUrl' => isset($config['serverURL']) ? $config['serverURL'] : 'https://r05.bearcms.com/',
            'appSecretKey' => $config['appSecretKey'],
            'addDefaultThemes' => true,
            'defaultThemeID' => 'none', //isset($config['defaultThemeID']) ? $config['defaultThemeID'] : 'bearcms/themeone',
            'maxUploadsSize' => null,
            'features' => ['ELEMENTS', 'FILES', 'ABOUT', 'SETTINGS', 'USERS'],
            'htmlSandboxUrl' => 'https://cdn8.amcn.in/htmlSandbox.min.html',
            'uiColor' => isset($config['uiColor']) ? $config['uiColor'] : null,
            'uiTextColor' => isset($config['uiTextColor']) ? $config['uiTextColor'] : null,
            'whitelabel' => isset($config['whitelabel']) ? $config['whitelabel'] : false,
            'appSpecificServerData' => [
                'clientID' => 'bearcms/universal'
            ],
            'autoCreateHomePage' => false
        ];
        $app->addons->add('bearcms/bearframework-addon');
        $app->bearCMS->initialize($bearCMSConfig);
        $resp = $app->routes->getResponse($app->request);
        if ($resp !== null) {
            if ($resp instanceof \BearFramework\App\Response\FileReader) {
                $app->send($resp);
            }
            $headersList = $resp->headers->getList();
            $headers = [];
            foreach ($headersList as $header) {
                if ($header->name !== 'Content-Type') {
                    $headers[$header->name] = $header->value;
                }
            }
            $response = $this->makeResponse($resp->content, (string) $resp->headers->getValue('Content-Type'), $headers);
            $this->send($response);
            exit;
        }
    }

    /**
     * Creates a new response object.
     * 
     * @param string $content The response content.
     * @param string $mimeType The response MIME type.
     * @param string $headers The response headers in the following format: ['name'=>'value', 'name'=>'value'].
     * @return \BearCMS\Universal\Response
     */
    public function makeResponse(string $content, string $mimeType = 'text/html', array $headers = []): \BearCMS\Universal\Response
    {
        $response = new Universal\Response();
        $response->content = $content;
        $response->mimeType = $mimeType;
        $response->headers = $headers;
        return $response;
    }

    /**
     * Sends the response provided to the client.
     * 
     * @param \BearCMS\Universal\Response $response The response to send.
     * @return void
     */
    public function send(\BearCMS\Universal\Response $response): void
    {
        $app = \BearFramework\App::get();
        $resp = $this->makeInternalResponse($response);
        $app->send($resp);
    }

    /**
     * Enables output capturing.
     * 
     * @return void
     */
    public function captureStart(): void
    {
        ob_start();
    }

    /**
     * Ends the output capturing and sends the updated response to the client.
     * 
     * @return void
     */
    public function captureSend(): void
    {
        $content = ob_get_clean();
        $response = $this->makeResponse($content);
        $this->send($response);
    }

    /**
     * Enables automatic capturing, updating and sending the response.
     * 
     * @return void
     */
    // public function enableAutoCapture(): void
    // { // NOT READY YET. Has some problems in bearcms->process()
    //     ob_start(function ($content) {
    //         $response = $this->makeResponse($content);
    //         $response->headers['X-asd'] = '3';
    //         $resp = $this->makeInternalResponse($response);
    //         $this->send($response);
    //         return $response->content;
    //     });
    // }

    /**
     * Creates a BearFramework\App\Response from BearCMS\Universal\Response
     * 
     * @param \BearCMS\Universal\Response $response The source response.
     * @return \BearFramework\App\Response
     */
    private function makeInternalResponse(\BearCMS\Universal\Response $response): \BearFramework\App\Response
    {
        $app = \BearFramework\App::get();
        $isHTMLResponse = $response->mimeType === 'text/html';
        if ($isHTMLResponse) {
            $resp = new \BearFramework\App\Response\HTML();
        } else {
            $resp = new \BearFramework\App\Response();
            $resp->headers->set($resp->headers->make('Content-Type', $response->mimeType));
        }
        $resp->content = $response->content;
        foreach ($response->headers as $name => $value) {
            $resp->headers->set($resp->headers->make($name, $value));
        }
        if ($isHTMLResponse) {
            $app->bearCMS->apply($resp);
        }
        return $resp;
    }
}
