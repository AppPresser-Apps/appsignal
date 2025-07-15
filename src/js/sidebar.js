import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { ToggleControl, PanelRow, Button, TextControl, TextareaControl, CheckboxControl } from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { useState, useEffect, useRef } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';

const AppsignalDocumentSettingPanel = ( { meta, setMeta } ) => {

    const postStatus = useSelect( ( select ) => {
        return select( 'core/editor' ).getCurrentPost().status;
    }, [] );

    const [isSending, setIsSending] = useState(false);
    const [sendStatus, setSendStatus] = useState(null);
    const [titleError, setTitleError] = useState(null);
    const [messageError, setMessageError] = useState(null);
    const [localToggleValue, setLocalToggleValue] = useState(null);
    const { all_segments, default_segments } = window.appsignalOneSignalData;

    const { createNotice } = useDispatch('core/notices');
    const { savePost } = useDispatch( 'core/editor' );

        // Track previous status to detect transition to 'publish'
        const prevStatus = useRef(postStatus);

        useEffect(() => {
            if (prevStatus.current !== 'publish' && postStatus === 'publish') {
                // Post has just been published!
                // Place your logic here (e.g., show a notice, call an API, etc.)
                console.log('Post was published!');
                sendNotification();
            }
            prevStatus.current = postStatus;
        }, [postStatus]);

        useEffect(() => {
            // Initialize segments if not set.
            if (meta && typeof meta.appsignal_notification_segments === 'undefined') {
                setMeta({ ...meta, appsignal_notification_segments: default_segments });
            }
        }, [meta]);

    if ( ! meta ) {
        return null;
    }

    /**
     * Send push notification via API
     */
    const sendNotification = async () => {

        const title = meta.appsignal_notification_title || '';
        const message = meta.appsignal_notification_message || '';

        let hasError = false;
        if (!title.trim()) {
            setTitleError(__('Title is required.', 'apppresser-onesignal'));
            hasError = true;
        } else {
            setTitleError(null);
        }

        if (!message.trim()) {
            setMessageError(__('Message is required.', 'apppresser-onesignal'));
            hasError = true;
        } else {
            setMessageError(null);
        }

        if (hasError) {
            return;
        }

        setIsSending(true);
        setSendStatus(null);

        try {
            await savePost();

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
            createNotice('success', __('Push notification sent successfully!', 'apppresser-onesignal'), { id: 'appsignal-notice', isDismissible: true });
        } catch (error) {
            console.error('Error sending push notification:', error);
            setSendStatus('error');
            createNotice('error', __('Failed to send push notification. Please try again.', 'apppresser-onesignal'), { id: 'appsignal-notice', isDismissible: true });
        } finally {
            setIsSending(false);
        }
    };

        /**
     * Get help text based on post status
     */
        const getHelpText = () => {
            return __('A notification will be sent when this post is published.', 'apppresser-onesignal');
        };

    return (
        <PluginDocumentSettingPanel
            name="appsignal-document-setting-panel"
            title={ __( 'Push Notification', 'apppresser-onesignal' ) }
        >
            <ToggleControl
                label={__('Send push notification', 'apppresser-onesignal')}
                checked={meta.appsignal_send_notification}
                onChange={(value) => {
                    setMeta({ ...meta, appsignal_send_notification: value })
                }}
                disabled={ !meta.appsignal_notification_title || !meta.appsignal_notification_message || postStatus === 'publish' }
                help={getHelpText()}
            />
            <TextControl
                label={ __( 'Title', 'apppresser-onesignal' ) }
                value={ meta.appsignal_notification_title || '' }
                onChange={ ( value ) => {
                    setMeta( { ...meta, appsignal_notification_title: value } );
                    if ( value.trim() ) {
                        setTitleError( null );
                    }
                } }
                maxLength={30}
                help={`${(meta.appsignal_notification_title || '').length}/30`}
            />
            { titleError && <div style={ { color: '#d63638', marginTop: '-10px', fontSize: '12px', marginBottom: '10px' } }>{ titleError }</div> }
            <TextareaControl
                label={ __( 'Message', 'apppresser-onesignal' ) }
                value={ meta.appsignal_notification_message || '' }
                onChange={ ( value ) => {
                    setMeta( { ...meta, appsignal_notification_message: value } );
                    if ( value.trim() ) {
                        setMessageError( null );
                    }
                } }
                maxLength={60}
                help={`${(meta.appsignal_notification_message || '').length}/60`}
            />
            { messageError && <div style={ { color: '#d63638', marginTop: '-10px', fontSize: '12px', marginBottom: '10px' } }>{ messageError }</div> }

            <h2 style={{ fontSize: '14px', marginTop: '20px', marginBottom: '8px' }}>{ __( 'Segments', 'apppresser-onesignal' ) }</h2>
            { all_segments && Object.entries( all_segments ).map( ( [ key, name ] ) => (
                <CheckboxControl
                    key={ key }
                    label={ name }
                    checked={ ( meta.appsignal_notification_segments || [] ).includes( key ) }
                    onChange={ ( isChecked ) => {
                        const currentSegments = meta.appsignal_notification_segments || [];
                        const newSegments = isChecked
                            ? [ ...currentSegments, key ]
                            : currentSegments.filter( ( segment ) => segment !== key );
                        setMeta( { ...meta, appsignal_notification_segments: newSegments } );
                    } }
                />
            ) ) }

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
    
                    {/* {sendStatus === 'success' && (
                        <div style={{ color: '#00a32a', marginTop: '8px', fontSize: '14px' }}>
                            {__('Push sent successfully!', 'apppresser-onesignal')}
                        </div>
                    )}
    
                    {sendStatus === 'error' && (
                        <div style={{ color: '#d63638', marginTop: '8px', fontSize: '14px' }}>
                            {__('Failed to send push. Please try again.', 'apppresser-onesignal')}
                        </div>
                    )} */}
                </div>
            </PanelRow>
        </PluginDocumentSettingPanel>
    );
};

const ComposedPanel = compose( [
    withSelect( ( select ) => {
        const meta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );
        return {
            meta: meta,
        };
    } ),
    withDispatch( ( dispatch ) => {
        return {
            setMeta( newMeta ) {
                dispatch( 'core/editor' ).editPost( { meta: newMeta } );
            },
        };
    } ),
] )( AppsignalDocumentSettingPanel );

registerPlugin( 'appsignal-document-setting-panel', {
    render: ComposedPanel,
} );