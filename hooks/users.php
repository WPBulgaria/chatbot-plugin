<?php

use WPBulgaria\Chatbot\Models\PlanModel;
use WPBulgaria\Chatbot\Models\ConfigsModel;
use WPBulgaria\Chatbot\Models\ChatbotModel;

defined('ABSPATH') || exit;

/**
 * Assign default chat plan to newly registered user for all chatbots
 * 
 * @param int $userId The ID of the newly created user
 * @return void
 */
function wpb_chatbot_assign_default_plan_on_registration(int $userId): void {
    if ($userId <= 0) {
        return;
    }

    try {
        $planModel = wpb_chatbot_resolve(PlanModel::class);
        $configsModel = wpb_chatbot_resolve(ConfigsModel::class);
        $chatbotModel = wpb_chatbot_resolve(ChatbotModel::class);
        
        // Get all chatbots
        $chatbotsResult = $chatbotModel->list(100, 1);
        $chatbots = $chatbotsResult['chatbots'] ?? [];
        
        if (empty($chatbots)) {
            return;
        }

        // Assign default plan for each chatbot
        foreach ($chatbots as $chatbot) {
            $chatbotId = $chatbot['id'];
            $configs = $configsModel->view($chatbotId, true);
            $defaultPlanId = $configs['defaultPlan'] ?? null;
    
            // Assign the plan to the user for this chatbot
            if (!empty($defaultPlanId)) {
                $planModel->setUserPlan($chatbotId, $userId, $defaultPlanId);
            }
        }
    } catch (\Exception $e) {
        error_log('WPB Chatbot: Failed to assign default plan on user registration - ' . $e->getMessage());
    }
}
add_action('user_register', 'wpb_chatbot_assign_default_plan_on_registration');

/**
 * Display chatbot plan selection fields for all chatbots on user profile page
 * 
 * @param WP_User $user The user object
 * @return void
 */
function wpb_chatbot_display_user_plan_field(WP_User $user): void {
    if (!current_user_can('edit_users')) {
        return;
    }

    try {
        $planModel = wpb_chatbot_resolve(PlanModel::class);
        $chatbotModel = wpb_chatbot_resolve(ChatbotModel::class);
        
        // Get all chatbots
        $chatbotsResult = $chatbotModel->list(100, 1);
        $chatbots = $chatbotsResult['chatbots'] ?? [];
        
        if (empty($chatbots)) {
            return;
        }


        ?>
        <h2><?php esc_html_e('Chatbot Plans', 'wpbulgaria-chatbot'); ?></h2>
        <table class="form-table" role="presentation">
            <?php foreach ($chatbots as $chatbot): ?>
                <?php
                $chatbotId = $chatbot['id'];
                $plans = $planModel->list($chatbotId);
                
                if (empty($plans)) {
                    continue;
                }

                // Get current plan for this chatbot
                $currentPlanId = $planModel->getUserPlanId($chatbotId, $user->ID);
                ?>
                <tr>
                    <th>
                        <label for="wpb_chatbot_user_plan_<?php echo esc_attr($chatbotId); ?>">
                            <?php echo esc_html($chatbot['title'] ?? __('Untitled Chatbot', 'wpbulgaria-chatbot')); ?>
                        </label>
                    </th>
                    <td>
                        <select 
                            name="wpb_chatbot_user_plans[<?php echo esc_attr($chatbotId); ?>]" 
                            id="wpb_chatbot_user_plan_<?php echo esc_attr($chatbotId); ?>" 
                            class="regular-text"
                        >
                            <option value="">
                                <?php esc_html_e('-- Select Plan --', 'wpbulgaria-chatbot'); ?>
                            </option>
                            <?php foreach ($plans as $plan): ?>
                                <option 
                                    value="<?php echo esc_attr($plan['id']); ?>"
                                    <?php selected($currentPlanId, $plan['id']); ?>
                                >
                                    <?php echo esc_html($plan['name'] ?? __('Unnamed Plan', 'wpbulgaria-chatbot')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($chatbot['description'])): ?>
                            <p class="description">
                                <?php echo esc_html($chatbot['description']); ?>
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    } catch (\Exception $e) {
        error_log('WPB Chatbot: Failed to display user plan fields - ' . $e->getMessage());
    }
}
add_action('show_user_profile', 'wpb_chatbot_display_user_plan_field');
add_action('edit_user_profile', 'wpb_chatbot_display_user_plan_field');

/**
 * Save chatbot plan selections for all chatbots when user profile is updated
 * 
 * @param int $userId The ID of the user being updated
 * @return void
 */
function wpb_chatbot_save_user_plan_field(int $userId): void {
    if (!current_user_can('edit_user', $userId)) {
        return;
    }

    // Verify nonce for security
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $userId)) {
        return;
    }

    if (!isset($_POST['wpb_chatbot_user_plans']) || !is_array($_POST['wpb_chatbot_user_plans'])) {
        return;
    }

    try {
        $planModel = wpb_chatbot_resolve(PlanModel::class);
        
        // Clear existing plans
        delete_user_meta($userId, PlanModel::USER_PLAN_META_KEY);
        
        // Save plans for each chatbot
        foreach ($_POST['wpb_chatbot_user_plans'] as $chatbotId => $planId) {
            $chatbotId = absint($chatbotId);
            $planId = sanitize_text_field($planId);
            
            if (empty($chatbotId) || empty($planId)) {
                continue;
            }

            // Verify the plan exists for this chatbot
            $plan = $planModel->get($chatbotId, $planId);
            if (empty($plan)) {
                continue;
            }

            $planModel->setUserPlan($chatbotId, $userId, $planId);
        }
    } catch (\Exception $e) {
        error_log('WPB Chatbot: Failed to save user plans - ' . $e->getMessage());
    }
}
add_action('personal_options_update', 'wpb_chatbot_save_user_plan_field');
add_action('edit_user_profile_update', 'wpb_chatbot_save_user_plan_field');
