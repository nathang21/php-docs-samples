<?php
/**
 * Copyright 2018 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Cloud\Test\Memorystore;

use Google\Cloud\TestUtils\AppEngineDeploymentTrait;
use Google\Cloud\TestUtils\FileUtil;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/../../../../testing/FileUtil.php';

class DeployMySQLTest extends \PHPUnit_Framework_TestCase
{
    use AppEngineDeploymentTrait;

    public function testIndex()
    {
        $resp = $this->client->request('GET', '/');

        $this->assertEquals('200', $resp->getStatusCode());
        $this->assertRegExp('/Visitor number: \d+/', (string) $resp->getBody());
    }

    public static function beforeDeploy()
    {
        if (!($host = getenv('REDIS_HOST'))
            || !($port = getenv('REDIS_PORT'))) {
            var_dump($host, $port);
            self::markTestSkipped('Set the REDIS_HOST and REDIS_PORT '
                . 'environment variables');
        }

        $tmpDir = FileUtil::cloneDirectoryIntoTmp(__DIR__ . '/..');
        self::$gcloudWrapper->setDir($tmpDir);
        chdir($tmpDir);

        $appYaml = Yaml::parse(file_get_contents('app.yaml'));
        $appYaml['env_variables']['REDIS_HOST'] = $host;
        $appYaml['env_variables']['REDIS_PORT'] = $port;

        file_put_contents('app.yaml', Yaml::dump($appYaml));
    }
}