<?php

defined( 'ABSPATH' ) || exit;

// GET /chats - List all chats
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::list',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $result = $auth->list((int) $request->get_param('user_id') ?: get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});


// GET /chats - List all chats
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/chats', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::list',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $result = $auth->list((int) $request->get_param('user_id') ?: get_current_user_id());
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// GET /chats/{id} - Get specific chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::get',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $result = $auth->get(get_current_user_id(), $request->get_param('id'));
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// POST /chats - Create new chat with message
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/chats', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::chat',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $chatbotId = $request->get_param('chatbot_id');

            if (!$auth->validateQuestionSize(get_current_user_id(), $request->get_param('message') ?? '', $chatbotId)) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }


            $result = $auth->chat(get_current_user_id(), null, $chatbotId);
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// POST /chats/{id} - Continue existing chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/chats/(?P<id>\d+)', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::chat',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $chatbotId = $request->get_param('chatbot_id');
            if (!$auth->validateQuestionSize(get_current_user_id(), $request->get_param('message') ?? '', $chatbotId)) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }

            if (!$auth->currentUserId()) {
                $messages = wpb_chatbot_app(\WPBulgaria\Chatbot\Models\ChatModel::class)->getMessages($request->get_param('id'));

                if (!$auth->canAnnonAskQuestion(count($messages), $chatbotId)) {
                    return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
                }
            }

            $result = $auth->chat(get_current_user_id(), $request->get_param('id'), $chatbotId);
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// PATCH /chats/{id} - Update chat title
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::updateTitle',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $result = $auth->updateTitle(get_current_user_id(), $request->get_param('id'));
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// DELETE /chats/{id} - Trash chat (soft delete)
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::trash',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $result = $auth->trash(get_current_user_id(), $request->get_param('id'));
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// DELETE /chats/{id}/force - Permanently delete chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)/force', array(
        'methods' => 'DELETE',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::remove',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $result = $auth->remove(get_current_user_id(), $request->get_param('id'));
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// POST /chats/{id}/restore - Restore trashed chat
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chats/(?P<id>\d+)/restore', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::restore',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $result = $auth->restore(get_current_user_id(), $request->get_param('id'));
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});


// POST /chats/stream - Create new chat and stream the response
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/chats/stream', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::stream',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $chatbotId = $request->get_param('chatbot_id');

            if (!$auth->validateQuestionSize(get_current_user_id(), $request->get_param('message') ?? '', $chatbotId)) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }

            $result = $auth->stream(get_current_user_id(), null, $chatbotId);
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});

// POST /chats/{id}/stream - Continue existing chat and stream the response
add_action( 'rest_api_init', function () {
    register_rest_route( WPB_CHATBOT_API_PREFIX, '/chatbots/(?P<chatbot_id>\d+)/chats/(?P<id>\d+)/stream', array(
        'methods' => 'POST',
        'callback' => 'WPBulgaria\Chatbot\Actions\ChatAction::stream',
        'permission_callback' => function ($request) {
            $auth = wpb_chatbot_app(\WPBulgaria\Chatbot\Auth\Factory\ChatsAuthFactory::class);
            $chatbotId = $request->get_param('chatbot_id');

            if (!$auth->validateQuestionSize(get_current_user_id(), $request->get_param('message') ?? '', $chatbotId)) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }


            if (!$auth->currentUserId()) {
                $messages = wpb_chatbot_app(\WPBulgaria\Chatbot\Models\ChatModel::class)->getMessages($request->get_param('id'));

                if (!$auth->canAnnonAskQuestion(count($messages), $chatbotId)) {
                    return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
                }
            }


            $result = $auth->stream(get_current_user_id(), $request->get_param('id'), $chatbotId);
            if ($auth->hasError() && !$result) {
                return new \WP_Error("unauthorized", $auth->getError()->getMessage(), array("status" => 401));
            }
            return $result;
        }
    ) );
});
