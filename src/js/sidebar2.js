import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { ToggleControl, PanelRow, Button, TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { registerPlugin } from '@wordpress/plugins';
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';

/**
 * Push Notification Panel Component
 * Allows users to configure and send push notifications for posts
 */
const PushNotificationPanel = () => {
    // Component state
    const [isSending, setIsSending] = useState(false);
    const [sendStatus, setSendStatus] = useState(null);
    const [wasPublished, setWasPublished] = useState(false);
    const [localToggleValue, setLocalToggleValue] = useState(null);
    const [notificationTitle, setNotificationTitle] = useState('');
    const [notificationMessage, setNotificationMessage] = useState('');

    // WordPress data selectors
    const { isPublished, isNewPost, meta } = useSelect((select) => ({
        isPublished: select('core/editor').isCurrentPostPublished(),
        isNewPost: select('core/editor').isCleanNewPost(),
        meta: select('core/editor').getEditedPostAttribute('meta') || {},
    }));

    // WordPress data dispatchers
    const { editPost } = useDispatch('core/editor');
    const { createNotice } = useDispatch('core/notices');

    // Get current notification setting (default to '0' if not set)
    const metaNotificationSetting = meta._appsignal_send_notification || '0';
    const currentToggleValue = localToggleValue !== null ? localToggleValue : metaNotificationSetting;

    // Initialize text controls with existing meta values
    useEffect(() => {
        if (meta._appsignal_notification_title !== undefined && meta._appsignal_notification_title !== notificationTitle) {
            setNotificationTitle(meta._appsignal_notification_title);
        }
        if (meta._appsignal_notification_message !== undefined && meta._appsignal_notification_message !== notificationMessage) {
            setNotificationMessage(meta._appsignal_notification_message);
        }
    }, [meta._appsignal_notification_title, meta._appsignal_notification_message]);

    /**
     * Send push notification via API
     */
    const sendNotification = async () => {
        setIsSending(true);
        setSendStatus(null);

        try {
            const response = await window.fetch(
                `${window.appsignalOneSignalData?.rest_url}appsignal/v1/send`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': window.appsignalOneSignalData?.nonce || '',
                    },
                    body: JSON.stringify({
                        post_id: window.appsignalOneSignalData?.post_id || 0,
                    }),
                }
            );

            if (!response.ok) {
                throw new Error('Failed to send notification');
            }

            setSendStatus('success');
            createNotice('success', __('Push notification sent successfully!', 'apppresser-onesignal'));
        } catch (error) {
            console.error('Error sending push notification:', error);
            setSendStatus('error');
            createNotice('error', __('Failed to send push notification. Please try again.', 'apppresser-onesignal'));
        } finally {
            setIsSending(false);
        }
    };

    /**
     * Handle automatic notification sending when post is published
     */
    useEffect(() => {
        const shouldSendOnPublish = currentToggleValue === '1';

        if (isPublished && !wasPublished && shouldSendOnPublish) {
            sendNotification();
            setWasPublished(true);
        } else if (!isPublished) {
            setWasPublished(false);
        }
    }, [isPublished, wasPublished, currentToggleValue]);

    /**
     * Update notification meta value
     * @param {boolean} value - Whether to send notifications
     */
    const updateNotificationSetting = (value) => {
        const newValue = value ? '1' : '0';

        // Update local state for immediate UI feedback
        setLocalToggleValue(newValue);

        // Update WordPress meta
        editPost({
            meta: { ...meta, _appsignal_send_notification: newValue },
        });
    };

    /**
     * Update notification title
     * @param {string} value - Notification title
     */
    const updateNotificationTitle = (value) => {
        setNotificationTitle(value);
        editPost({
            meta: { ...meta, _appsignal_notification_title: value },
        });
    };

    /**
     * Update notification body
     * @param {string} value - Notification body
     */
    const updateNotificationMessage = (value) => {
        setNotificationMessage(value);
        editPost({
            meta: { ...meta, _appsignal_notification_message: value },
        });
    };

    /**
     * Get help text based on post status
     */
    const getHelpText = () => {
        if (isPublished && !isNewPost) {
            return __('Push notifications are only sent when first publishing a post.', 'apppresser-onesignal');
        }
        if (isNewPost) {
            return __('A notification will be sent when this post is first published.', 'apppresser-onesignal');
        }
        return __('A notification will be sent when this post is published.', 'apppresser-onesignal');
    };

    return (
        <PluginDocumentSettingPanel
            name="appsignal-push-notification"
            title={__('Push Notification', 'apppresser-onesignal')}
            className="appsignal-push-notification-panel"
        >
            <PanelRow>
                <ToggleControl
                    label={__('Send push notification', 'apppresser-onesignal')}
                    checked={currentToggleValue === '1'}
                    onChange={updateNotificationSetting}
                    disabled={isPublished && !isNewPost}
                    help={getHelpText()}
                />
            </PanelRow>
            <PanelRow>
                <TextControl
                    label={__('Title', 'apppresser-onesignal')}
                    value={notificationTitle || ''}
                    onChange={updateNotificationTitle}
                />
            </PanelRow>
            <PanelRow>
                <TextControl
                    label={__('Message', 'apppresser-onesignal')}
                    value={notificationMessage || ''}
                    onChange={updateNotificationMessage}
                />
            </PanelRow>


            <PanelRow>
                <div style={{ width: '100%' }}>
                    <Button
                        variant="secondary"
                        onClick={sendNotification}
                        isBusy={isSending}
                        disabled={isSending}
                        style={{ marginTop: '16px', width: '100%', textAlign: 'center', display: 'flex', justifyContent: 'center' }}
                    >
                        {isSending
                            ? __('Sending...', 'apppresser-onesignal')
                            : __('Send Push', 'apppresser-onesignal')
                        }
                    </Button>

                    {sendStatus === 'success' && (
                        <div style={{ color: '#00a32a', marginTop: '8px', fontSize: '14px' }}>
                            {__('Push sent successfully!', 'apppresser-onesignal')}
                        </div>
                    )}

                    {sendStatus === 'error' && (
                        <div style={{ color: '#d63638', marginTop: '8px', fontSize: '14px' }}>
                            {__('Failed to send push. Please try again.', 'apppresser-onesignal')}
                        </div>
                    )}
                </div>
            </PanelRow>
        </PluginDocumentSettingPanel>
    );
};

// Register the plugin
registerPlugin('appsignal-push-notification', {
    render: () => <PushNotificationPanel />,
    icon: 'megaphone',
});
