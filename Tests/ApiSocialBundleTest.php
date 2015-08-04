<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Tests;

use Ant\Bundle\ApiSocialBundle\ApiSocialBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiSocialBundleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testBuild()
    {
        $apiSocialBundle = new ApiSocialBundle();
        $container = new ContainerBuilder();
        $apiSocialBundle->build($container);

        $bundle_path = $container->getParameter('ant.api_social.root_dir');
        $bundle_images_path = $container->getParameter('ant.api_social.images_dir');

        $this->assertTrue(is_dir($bundle_path));
        $this->assertFileExists($bundle_path.DIRECTORY_SEPARATOR.'ApiSocialBundle.php');
        $this->assertTrue(is_dir($bundle_images_path));
    }
}
