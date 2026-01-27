<?php

namespace WPBulgaria\Chatbot\Providers;

use WPBulgaria\Chatbot\Container\Container;
use WPBulgaria\Chatbot\Services\GeminiService;
use WPBulgaria\Chatbot\Services\ChatService;
use WPBulgaria\Chatbot\Services\PlanService;
use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Models\OptionModel;
use WPBulgaria\Chatbot\Models\FileModel;
use WPBulgaria\Chatbot\Models\PlanModel;
use WPBulgaria\Chatbot\Models\SearchFileModel;
use WPBulgaria\Chatbot\Models\ChatModel;
use WPBulgaria\Chatbot\Models\ChatbotModel;
use WPBulgaria\Chatbot\Contracts\AuthInterface;
use WPBulgaria\Chatbot\Models\PostModel;
use WPBulgaria\Chatbot\Auth\ChatsAuth;
use WPBulgaria\Chatbot\Auth\ChatbotAuth;
use WPBulgaria\Chatbot\Auth\ConfigsAuth;
use WPBulgaria\Chatbot\Auth\FilesAuth;
use WPBulgaria\Chatbot\Auth\PlansAuth;
use WPBulgaria\Chatbot\Transactions\File\FileRemoveTransaction;
use WPBulgaria\Chatbot\Transactions\File\FileUseTransaction;
use WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory;
use WPBulgaria\Chatbot\Auth\Factory\ChatbotAuthFactory;
use WPBulgaria\Chatbot\Auth\Factory\ConfigsAuthFactory;
use WPBulgaria\Chatbot\Auth\Factory\FilesAuthFactory;
use WPBulgaria\Chatbot\Auth\Factory\PlansAuthFactory;

defined('ABSPATH') || exit;

/**
 * Main application service provider
 */
class AppServiceProvider extends ServiceProvider {

    /**
     * Register application services
     */
    public function register(Container $container): void {

        // Register OptionModel as singleton
        $container->singleton(OptionModel::class, function ($c) {
            return new OptionModel();
        });

        // Register FileModel as singleton
        $container->singleton(FileModel::class, function ($c) {
            return new FileModel();
        });

        // Register PlanModel as singleton
        $container->singleton(PlanModel::class, function ($c) {
            return new PlanModel($c->make(OptionModel::class));
        });

        // Register SearchFileModel as singleton
        $container->singleton(SearchFileModel::class, function ($c) {
            return new SearchFileModel($c->make(GeminiService::class), $c->make(ConfigsModel::class));
        });

        // Register ConfigsModel as singleton (config doesn't change during request)
        $container->singleton(ConfigsModel::class, function ($c) {
            return new ConfigsModel($c->make(PostModel::class));
        });

        // Register PostModel as singleton
        $container->singleton(PostModel::class, function ($c) {
            return new PostModel();
        });

        // Register ChatModel as singleton
        $container->singleton(ChatModel::class, function ($c) {
            return new ChatModel($c->make(GeminiService::class), $c->make(PostModel::class), $c->make(ChatsAuthFactory::class), $c->make(ChatbotModel::class));
        });

        // Register ChatbotModel as singleton
        $container->singleton(ChatbotModel::class, function ($c) {
            return new ChatbotModel($c->make(PostModel::class), $c->make(ConfigsModel::class));
        });
        
        // Register ChatsAuth as singleton
        $container->singleton(ChatsAuth::class, function ($c) {
            return new ChatsAuth(
                $c->make(ConfigsModel::class),
                $c->make(PlanService::class)
            );
        });

        // Register ChatbotAuth as singleton
        $container->singleton(ChatbotAuth::class, function ($c) {
            return new ChatbotAuth($c->make(ConfigsModel::class));
        });

        // Register ConfigsAuth as singleton
        $container->singleton(ConfigsAuth::class, function ($c) {
            return new ConfigsAuth($c->make(ConfigsModel::class));
        });

        // Register FilesAuth as singleton
        $container->singleton(FilesAuth::class, function ($c) {
            return new FilesAuth($c->make(ConfigsModel::class));
        });

        // Register PlansAuth as singleton
        $container->singleton(PlansAuth::class, function ($c) {
            return new PlansAuth($c->make(ConfigsModel::class));
        });

        // Register FileRemoveTransaction as singleton
        $container->singleton(FileRemoveTransaction::class, function ($c) {
            return new FileRemoveTransaction($c->make(SearchFileModel::class), $c->make(FileModel::class));
        });

        // Register FileUseTransaction as singleton
        $container->singleton(FileUseTransaction::class, function ($c) {
            return new FileUseTransaction($c->make(SearchFileModel::class), $c->make(FileModel::class));
        });

        // Register GeminiService as singleton
        $container->singleton(GeminiService::class, function ($c) {
            return new GeminiService(
                $c->make(ConfigsModel::class)
            );
        });

        // Register ChatService as singleton
        $container->singleton(ChatService::class, function ($c) {
            return new ChatService(
                $c->make(GeminiService::class)
            );
        });

        // Register PlanService as singleton
        $container->singleton(PlanService::class, function ($c) {
            return new PlanService(
                $c->make(PlanModel::class),
                $c->make(ConfigsModel::class),
                $c->make(PostModel::class)
            );
        });

        // Register ChatsAuthFactory as singleton
        $container->singleton(ChatsAuthFactory::class, function ($c) {
            return ChatsAuthFactory::create(
                $c->make(ConfigsModel::class),
                $c->make(PlanService::class)
            );
        });

        // Register ChatbotAuthFactory as singleton
        $container->singleton(ChatbotAuthFactory::class, function ($c) {
            return ChatbotAuthFactory::create($c->make(ConfigsModel::class));
        });

        // Register ConfigsAuthFactory as singleton
        $container->singleton(ConfigsAuthFactory::class, function ($c) {
            return ConfigsAuthFactory::create($c->make(ConfigsModel::class));
        });

        // Register FilesAuthFactory as singleton
        $container->singleton(FilesAuthFactory::class, function ($c) {
            return FilesAuthFactory::create($c->make(ConfigsModel::class));
        });

        
        // Register PlansAuthFactory as singleton
        $container->singleton(PlansAuthFactory::class, function ($c) {
            return PlansAuthFactory::create($c->make(ConfigsModel::class));
        });

        // Register aliases for convenience
        $container->alias(GeminiService::class, 'gemini');
        $container->alias(ChatService::class, 'chat');
        $container->alias(ConfigsModel::class, 'config');
        $container->alias(PlanService::class, 'plan');
    }

    /**
     * Bootstrap application services
     */
    public function boot(Container $container): void {
        // Any initialization that needs to happen after all services are registered
    }
}
