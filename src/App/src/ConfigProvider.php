<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App;

use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Dot\Mapper\Factory\DbMapperFactory;
use Frontend\App\Entity\CronStatEntity;
use Frontend\App\Entity\PackageEntity;
use Frontend\App\Entity\PackageEntityHydrator;
use Frontend\App\Entity\UserMessageEntity;
use Frontend\App\Factory\ContactFormFactory;
use Frontend\App\Form\ContactForm;
use Frontend\App\Form\UserMessageFieldset;
use Frontend\App\Listener\UserMessageMapperEventListener;
use Frontend\App\Mapper\CronStatDbMapper;
use Frontend\App\Mapper\PackageDbMapper;
use Frontend\App\Mapper\UserMessageDbMapper;
use Frontend\App\Service\PackageService;
use Frontend\App\Service\UserMessageService;
use Frontend\App\Service\UserMessageServiceInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package Frontend\App
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),

            'templates' => $this->getTemplates(),

            'dot_form' => $this->getForms(),

            'dot_mapper' => $this->getMappers(),

            'dot_hydrator' => $this->getHydrators(),
        ];
    }

    public function getHydrators()
    {
        return [
            'hydrator_manager' => [
                'factories' => [
                    PackageEntityHydrator::class => InvokableFactory::class,
                ]
            ]
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                UserMessageService::class => InvokableFactory::class,
                PackageService::class => AnnotatedServiceFactory::class,
            ],
            'aliases' => [
                UserMessageServiceInterface::class => UserMessageService::class,
                'UserMessageService' => UserMessageServiceInterface::class,
                'PackageService' => PackageService::class,
            ]
        ];
    }

    public function getMappers(): array
    {
        return [
            'mapper_manager' => [
                'factories' => [
                    UserMessageDbMapper::class => DbMapperFactory::class,
                    PackageDbMapper::class => DbMapperFactory::class,
                    CronStatDbMapper::class => DbMapperFactory::class,
                ],
                'aliases' => [
                    UserMessageEntity::class => UserMessageDbMapper::class,
                    PackageEntity::class => PackageDbMapper::class,
                    CronStatEntity::class => CronStatDbMapper::class,
                ]
            ],
            'options' => [
                UserMessageEntity::class => [
                    'mapper' => [
                        'event_listeners' => [
                            UserMessageMapperEventListener::class,
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => [__DIR__ . '/../templates/app'],
                'error' => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
                'page' => [__DIR__ . '/../templates/page'],
                'partial' => [__DIR__ . '/../templates/partial'],
            ],
        ];
    }

    public function getForms()
    {
        return [
            'form_manager' => [
                'factories' => [
                    UserMessageFieldset::class => InvokableFactory::class,
                    ContactForm::class => ContactFormFactory::class,
                ],
                'aliases' => [
                    'UserMessageFieldset' => UserMessageFieldset::class,
                    'ContactForm' => ContactForm::class,
                ]
            ],
        ];
    }
}
