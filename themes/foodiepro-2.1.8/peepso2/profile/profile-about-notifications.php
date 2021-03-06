<?php
$user = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());

$can_edit = FALSE;
if($user->get_id() == get_current_user_id() || current_user_can('edit_users')) {
    $can_edit = TRUE;
}

if(!$can_edit) {
    PeepSo::redirect(PeepSo::get_page('activity'));
} else {

    $PeepSoProfile = PeepSoProfile::get_instance();
    ?>

    <div class="peepso ps-page-profile ps-page--preferences">

        <section id="mainbody" class="ps-page-unstyled">
            <section id="component" role="article" class="ps-clearfix">

                <?php if($can_edit) { PeepSoTemplate::exec_template('profile', 'profile-about-tabs', array('tabs' => $tabs, 'current_tab'=>'notifications'));} ?>

                <div class="ps-preferences__notifications-actions">
                    <h3 class="ps-page-title"><?php echo __('Shortcuts','peepso-core');?></h3>
                    <p><?php echo __('Quickly manage all your preferences at once', 'peepso-core');?>:</p>

                    <div class="ps-preferences-notifications__menu" role="menu">
                        <a class="ps-preferences-notifications__menu-item" role="menuitem" href="<?php echo admin_url('admin-ajax.php?action=peepso_user_unsubscribe_emails&redirect')?>" data-action="disable">
                            <?php echo __('Disable all e-mail notifications', 'peepso-core');?>
                        </a>
                        <a class="ps-preferences-notifications__menu-item" role="menuitem" href="<?php echo admin_url('admin-ajax.php?action=peepso_user_subscribe_emails&redirect') ?>" data-action="enable" data-context="<?php echo isset($context) ? isset($context) : '';?>">
                            <?php echo __('Enable all e-mail notifications', 'peepso-core');?>
                        </a>
                        <a class="ps-preferences-notifications__menu-item" role="menuitem" href="<?php echo admin_url('admin-ajax.php?action=peepso_user_unsubscribe_onsite&redirect') ?>" data-action="disable">
                            <?php echo __('Disable all on-site notifications', 'peepso-core');?>
                        </a>
                        <a class="ps-preferences-notifications__menu-item" role="menuitem" href="<?php echo admin_url('admin-ajax.php?action=peepso_user_subscribe_onsite&redirect') ?>" data-action="enable" data-context="<?php echo isset($context) ? isset($context) : '';?>">
                            <?php echo __('Enable all on-site notifications', 'peepso-core');?>
                        </a>
                    </div>
                </div>

                <div class="ps-list--column cfield-list creset-list ps-js-profile-list">
                    <div class="cfield-list creset-list">
                        <?php $PeepSoProfile->preferences_form_fields('notifications', TRUE); ?>
                        <?php do_action('peepso_render_profile_about_notifications_after'); ?>
                    </div>
                </div>
            </section><!--end component-->
        </section><!--end mainbody-->
    </div><!--end row-->
<?php }