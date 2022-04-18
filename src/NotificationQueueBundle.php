<?php

namespace Like\NotificationQueueBundle;

use Like\NotificationQueueBundle\DependencyInjection\NotificationQueueExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NotificationQueueBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->registerExtension(new NotificationQueueExtension());
    }
}
