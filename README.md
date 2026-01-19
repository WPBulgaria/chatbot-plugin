=== WPBulgaria Chatbot ===
Contributors: sashevuchkov
Tags: chatbot, ai, gemini, google ai, conversational ai
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 0.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An AI-powered chatbot for WordPress using Google Gemini API with plan-based authorization and usage limits.

== Description ==

WPBulgaria Chatbot brings the power of Google's Gemini AI to your WordPress site. Create intelligent conversational experiences with a fully-featured chatbot that supports streaming responses, conversation history, file search, and flexible usage plans.

= Features =

* **Google Gemini Integration** - Powered by Google's latest Gemini AI models (default: gemini-2.5-flash)
* **Real-time Streaming** - SSE-based streaming responses for a natural chat experience
* **Conversation History** - Persistent chat storage with full message history
* **Plan-based Authorization** - Create custom plans with limits for chats, questions, history size, and question length
* **Global Usage Limits** - Set monthly limits for total chats and questions across all users
* **File Search** - Attach files to enhance AI responses with contextual knowledge
* **Admin Dashboard** - React-based admin interface for easy configuration
* **Embeddable Chat Widget** - Modern, themeable chat interface via shortcode
* **REST API** - Full REST API for headless integrations
* **Role-based Access** - Control who can use the chatbot based on WordPress capabilities

= Use Cases =

* Customer support chatbot
* Knowledge base assistant
* Content recommendation engine
* Interactive FAQ system
* Lead generation conversations

== Installation ==

1. Upload the `wpb-chatbot` folder to the `/wp-content/plugins/` directory
2. Run `composer install` in the plugin directory to install dependencies
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **Chatbot** in the admin menu to configure

= Requirements =

* PHP 8.1 or higher
* WordPress 6.0 or higher
* Google Gemini API key
* Composer (for dependency installation)

= Dependencies =

The plugin uses the following packages (installed via Composer):

* `google-gemini-php/client` - Official Google Gemini PHP client
* `guzzlehttp/guzzle` - HTTP client for API requests
* `ramsey/uuid` - UUID generation for unique identifiers

== Configuration ==

= Basic Setup =

1. Go to **Chatbot** in the WordPress admin menu
2. Enter your Google Gemini API key in the Settings tab
3. Configure optional system instructions to customize AI behavior
4. Set global monthly limits for chats and questions

= Plan Configuration =

Create usage plans to control access:

* **Name** - Plan identifier (e.g., "Free", "Pro", "Admin")
* **Total Chats** - Maximum chats per period (-1 for unlimited)
* **Total Questions** - Maximum questions per period (-1 for unlimited)
* **History Size** - Messages retained in context (-1 for unlimited)
* **Question Size** - Maximum question length in words (-1 for unlimited)
* **Period** - Reset period (day, week, month, year, lifetime)

= Default Plans =

* **Public Plan** - Applied to non-logged-in users
* **Default Plan** - Applied to logged-in users without an assigned plan

== Usage ==

= Shortcode =

Embed the chatbot anywhere using the shortcode:

`[wpbulgaria_chatbot]`

= Programmatic Access =

Access the chatbot services programmatically:

`php
// Get the chat model
$chatModel = wpb_chatbot_app(WPBulgaria\Chatbot\Models\ChatModel::class);

// Get plan service for usage checks
$planService = wpb_chatbot_app(WPBulgaria\Chatbot\Services\PlanService::class);

// Check if user can create a chat
$canChat = $planService->canCreateChat($userId);
`

== REST API ==

The plugin provides a full REST API under the `wpb-chatbot/v1` namespace.

= Endpoints =

**Chats**

* `GET /wp-json/wpb-chatbot/v1/chats` - List all chats
* `GET /wp-json/wpb-chatbot/v1/chats/{id}` - Get specific chat
* `POST /wp-json/wpb-chatbot/v1/chats` - Create new chat with message
* `POST /wp-json/wpb-chatbot/v1/chats/{id}` - Continue existing chat
* `PUT /wp-json/wpb-chatbot/v1/chats/{id}` - Update chat title
* `DELETE /wp-json/wpb-chatbot/v1/chats/{id}` - Trash a chat
* `POST /wp-json/wpb-chatbot/v1/chats/{id}/restore` - Restore trashed chat
* `GET /wp-json/wpb-chatbot/v1/chats/stream` - Stream chat response (SSE)

**Configuration**

* `GET /wp-json/wpb-chatbot/v1/configs` - Get plugin configuration
* `POST /wp-json/wpb-chatbot/v1/configs` - Update configuration

**Plans**

* `GET /wp-json/wpb-chatbot/v1/plans` - List all plans
* `POST /wp-json/wpb-chatbot/v1/plans` - Create a plan
* `PUT /wp-json/wpb-chatbot/v1/plans/{id}` - Update a plan
* `DELETE /wp-json/wpb-chatbot/v1/plans/{id}` - Delete a plan

**Files**

* `GET /wp-json/wpb-chatbot/v1/files` - List files
* `POST /wp-json/wpb-chatbot/v1/files` - Upload file
* `DELETE /wp-json/wpb-chatbot/v1/files/{id}` - Delete file

= Authentication =

All API requests require authentication via WordPress REST API nonce or application passwords.

== Frequently Asked Questions ==

= How do I get a Google Gemini API key? =

Visit [Google AI Studio](https://aistudio.google.com/) to create an API key for Gemini.

= Can I customize the chat widget appearance? =

Yes, the chat widget supports theming through the admin settings. You can customize colors and appearance via the Chat Theme configuration.

= How are usage limits calculated? =

Usage limits are calculated based on the plan's period setting:
- **Day**: Resets daily at midnight UTC
- **Week**: Resets every Monday at midnight UTC
- **Month**: Resets on the 1st of each month
- **Year**: Resets on January 1st
- **Lifetime**: Never resets

= Can anonymous users use the chatbot? =

Yes, if you configure a Public Plan, anonymous users can use the chatbot with the limits defined in that plan.

= What happens when limits are reached? =

When a user reaches their plan limit, they receive an error message indicating the limit has been reached. For global limits, all users are affected until the next period.

== Screenshots ==

1. Admin dashboard - Configuration settings
2. Chat widget - Frontend conversation interface
3. Plans management - Create and manage usage plans

== Changelog ==

= 0.0.3 =
* Added plan-based authorization system
* Added global monthly usage limits
* Added question size limits (word count)
* Added anonymous user support with public plans
* Improved error handling with detailed auth error messages
* Added usage summary endpoints

= 0.0.2 =
* Added streaming responses via SSE
* Added file search functionality
* Added chat history management
* Improved admin interface

= 0.0.1 =
* Initial release
* Basic Gemini integration
* Chat functionality
* Admin configuration

== Upgrade Notice ==

= 0.0.3 =
This version adds plan-based authorization. After upgrading, configure your plans in the admin dashboard to control user access.

== Developer Notes ==

= Architecture =

The plugin follows a service-oriented architecture with dependency injection:

* **Container** - PSR-11 compatible DI container
* **Services** - Business logic (GeminiService, PlanService, ChatService)
* **Models** - Data access layer (ChatModel, PlanModel, ConfigsModel)
* **Auth** - Authorization layer with factory pattern for testing
* **Actions** - REST API action handlers
* **Validators** - Input validation

= Hooks =

The plugin does not currently expose custom hooks but respects WordPress core hooks for REST API and authentication.

= Testing =

The auth system includes mock classes for testing:

`php
// Enable mock mode via constants
define('_WPB_CHATBOT_DEBUG', true);
define('_WPB_CHATBOT_UNLOCK_API', '!!!unlock it all now');
`

= Contributing =

Contributions are welcome! Please follow WordPress coding standards and include tests for new features.
