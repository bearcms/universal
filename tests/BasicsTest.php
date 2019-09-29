<?php

/*
 * Bear CMS Universal
 * https://github.com/bearcms/universal
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class BasicsTest extends PHPUnit\Framework\TestCase
{

    private function getInstance(): \BearCMS\Universal
    {
        $tempDir = sys_get_temp_dir() . '/bearcmsuniversal/' . uniqid();
        mkdir($tempDir, 0777, true);

        $dataDir = $tempDir . '/data';
        mkdir($dataDir, 0777, true);

        $logsDir = $tempDir . '/logs';
        mkdir($logsDir, 0777, true);

        $universal = new \BearCMS\Universal([
            'dataDir' => $dataDir,
            'logsDir' => $logsDir,
            'appSecretKey' => '111-XXX', // not real secret key
        ]);
        return $universal;
    }

    /**
     * 
     */
    public function testSend()
    {
        $universal = $this->getInstance();
        $response = $universal->makeResponse('Hi123');
        ob_start();
        $universal->send($response);
        $output = ob_get_clean();
        $this->assertTrue(strpos($output, '<meta name="generator" content="Bear CMS (powered by Bear Framework)">') !== false);
        $this->assertTrue(strpos($output, 'Hi123') !== false);
    }

    /**
     * 
     */
    public function testCapture()
    {
        $this->expectOutputRegex('/Bear CMS \(powered by Bear Framework\)/');
        $this->expectOutputRegex('/Hi123/');
        $universal = $this->getInstance();
        $universal->captureStart();
        echo 'Hi123';
        $universal->captureSend();
    }

    /**
     * 
     */
//    public function testAutoCapture()
//    {
//        $this->expectOutputRegex('/Bear CMS \(powered by Bear Framework\)/');
//        $this->expectOutputRegex('/Hi123/');
//        $universal = $this->getInstance();
//        $universal->autoCapture();
//        echo 'Hi123';
//    }

}
