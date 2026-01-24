<?php

use WPBulgaria\Chatbot\Models\PlanModel;
use WPBulgaria\Chatbot\Models\ConfigsModel;

defined('ABSPATH') || exit;

/**
 * Assign default chat plan to newly registered user
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
        
        // Get default plan ID from configs
        $configs = $configsModel->view(true);
        $defaultPlanId = $configs['defaultPlanId'] ?? null;
        
        // If no default plan ID is set in configs, use the first available plan
        if (empty($defaultPlanId)) {
            $plans = $planModel->list();
            if (!empty($plans)) {
                $defaultPlanId = $plans[0]['id'] ?? null;
            }
        }
        
        // Assign the plan to the user
        if (!empty($defaultPlanId)) {
            $planModel->setUserPlan($userId, $defaultPlanId);
        }
    } catch (\Exception $e) {
        error_log('WPB Chatbot: Failed to assign default plan on user registration - ' . $e->getMessage());
    }
}
add_action('user_register', 'wpb_chatbot_assign_default_plan_on_registration');

/**
 * Display chatbot plan selection field on user profile page
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
        $plans = $planModel->list();
        
        if (empty($plans)) {
            return;
        }

        $currentPlanId = $planModel->getUserPlanId($user->ID);
        
        ?>
        <h2><?php esc_html_e('Chatbot Plan', 'wpbulgaria-chatbot'); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th>
                    <label for="wpb_chatbot_user_plan">
                        <?php esc_html_e('Chat Plan', 'wpbulgaria-chatbot'); ?>
                    </label>
                </th>
                <td>
                    <select 
                        name="wpb_chatbot_user_plan" 
                        id="wpb_chatbot_user_plan" 
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
                                <?php if (!empty($plan['messageLimit'])): ?>
                                    (<?php echo esc_html($plan['messageLimit']); ?> 
                                    <?php esc_html_e('messages', 'wpbulgaria-chatbot'); ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Select the chatbot plan for this user.', 'wpbulgaria-chatbot'); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    } catch (\Exception $e) {
        error_log('WPB Chatbot: Failed to display user plan field - ' . $e->getMessage());
    }
}
add_action('show_user_profile', 'wpb_chatbot_display_user_plan_field');
add_action('edit_user_profile', 'wpb_chatbot_display_user_plan_field');

/**
 * Save chatbot plan selection when user profile is updated
 * 
 * @param int $userId The ID of the user being updated
 * @return void
 */
function wpb_chatbot_save_user_plan_field(int $userId): void {
    if (!current_user_can('edit_user', $userId)) {
        return;
    }

    if (!isset($_POST['wpb_chatbot_user_plan'])) {
        return;
    }

    try {
        $planModel = wpb_chatbot_resolve(PlanModel::class);
        $selectedPlanId = sanitize_text_field($_POST['wpb_chatbot_user_plan']);
        
        if (empty($selectedPlanId)) {
            delete_user_meta($userId, PlanModel::USER_PLAN_META_KEY);
            return;
        }

        // Verify the plan exists
        $plan = $planModel->get($selectedPlanId);
        if (empty($plan)) {
            return;
        }

        $planModel->setUserPlan($userId, $selectedPlanId);
    } catch (\Exception $e) {
        error_log('WPB Chatbot: Failed to save user plan - ' . $e->getMessage());
    }
}
add_action('personal_options_update', 'wpb_chatbot_save_user_plan_field');
add_action('edit_user_profile_update', 'wpb_chatbot_save_user_plan_field');
