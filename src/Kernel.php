<?php

namespace App;

use App\DependencyInjection\FormExtension;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;


    public function build(ContainerBuilder $container): void {
        $container->registerExtension(new FormExtension());
    }
}
