/* eslint-disable camelcase */
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	ExternalLink,
	PanelBody,
	PanelRow,
	Placeholder,
	Popover,
	Spinner,
	ToggleControl,
	TextControl,
	TextareaControl,
	SelectControl,
	CheckboxControl,
	Notice
} from '@wordpress/components';
import {
	render,
	Component,
	Fragment
} from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { registerPlugin } from '@wordpress/plugins';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import './css/settings.css';
import apppLogo from '../assets/appp-logo.png';

/**
 * Block Editor Panel Component
 */
const AppSignalPanel = () => {
	const { editPost } = useDispatch('core/editor');
	const postMeta = useSelect((select) => {
		return select('core/editor').getEditedPostAttribute('meta') || {};
	}, []);

	const sendNotification = postMeta['appsignal_send_notification'] || false;
	const title = postMeta['appsignal_notification_title'] || '';
	const message = postMeta['appsignal_notification_message'] || '';

	const updateMeta = (key, value) => {
		editPost({ meta: { [key]: value } });
	};

	return (
		<PluginDocumentSettingPanel
			name="appsignal-push-panel"
			title={__('Push Notification', 'apppresser-onesignal')}
			className="appsignal-push-panel"
		>
			<ToggleControl
				label={__('Send Notification', 'apppresser-onesignal')}
				checked={sendNotification}
				onChange={(value) => updateMeta('appsignal_send_notification', value)}
			/>
			<TextControl
				label={__('Notification Title', 'apppresser-onesignal')}
				value={title}
				onChange={(value) => updateMeta('appsignal_notification_title', value)}
			/>
			<TextareaControl
				label={__('Notification Message', 'apppresser-onesignal')}
				value={message}
				onChange={(value) => updateMeta('appsignal_notification_message', value)}
			/>
			{sendNotification && (
				<Button
					isSecondary
					style={ { width: '100%', justifyContent: 'center' } }
					onClick={() => {
						apiFetch({
							path: '/appsignal/v1/send-push',
							method: 'POST',
							data: { title, message },
						}).then(() => {
							// eslint-disable-next-line no-alert
							window.alert(__('Push notification sent!', 'apppresser-onesignal'));
						}).catch(() => {
							// eslint-disable-next-line no-alert
							window.alert(__('Failed to send push notification.', 'apppresser-onesignal'));
						});
					}}
					disabled={!message.trim()}
				>
					{__('Send Push Notification', 'apppresser-onesignal')}
				</Button>
			)}
		</PluginDocumentSettingPanel>
	);
};

if ( typeof registerPlugin === 'function' ) {
	registerPlugin( 'appsignal-push-notification', {
		render: AppSignalPanel,
	} );
}

/**
 * Plugin Settings Page Component
 */
class App extends Component {
	constructor() {
		super(...arguments);

		this.changeOptions = this.changeOptions.bind(this);
		this.saveOptions = this.saveOptions.bind(this);
		this.sendTestMessage = this.sendTestMessage.bind(this);

		this.roles = [];

		this.state = {
			isAPILoaded: false,
			isAPISaving: false,
			onesignal_app_id: '',
			onesignal_rest_api_key: '',
			github_access_token: '',
			onesignal_testing: false,
			onesignal_access: [],
			post_types_auto_push: [],
			onesignal_segments: [],
			onesignal_message: '',
			roles: [],
			postTypes: [],
			segments: [],
			notice: null,
		};
	}

	componentDidMount() {
		apiFetch({ path: '/appsignal/v1/options' }).then((options) => {
			this.setState({
				onesignal_app_id: options.onesignal_app_id || '',
				onesignal_rest_api_key: options.onesignal_rest_api_key || '',
				github_access_token: options.github_access_token || '',
				onesignal_testing: options.onesignal_testing || false,
				onesignal_access: options.onesignal_access || [],
				post_types_auto_push: options.post_types_auto_push || [],
				onesignal_segments: options.onesignal_segments || [],
			});
		});

		apiFetch({ path: '/appsignal/v1/roles' }).then((roles) => {
			this.setState({ roles });
		});

		apiFetch({ path: '/appsignal/v1/post-types' }).then((postTypes) => {
			this.setState({ postTypes });
		});

		apiFetch({ path: '/appsignal/v1/segments' }).then((segments) => {
			this.setState({ segments, isAPILoaded: true });
		});
	}

	changeOptions(option, value) {
		this.setState({ [option]: value });
	}

	saveOptions() {
		this.setState({ isAPISaving: true });

		const options = {
			onesignal_app_id: this.state.onesignal_app_id,
			onesignal_rest_api_key: this.state.onesignal_rest_api_key,
			github_access_token: this.state.github_access_token,
			onesignal_testing: this.state.onesignal_testing,
			onesignal_access: this.state.onesignal_access,
			post_types_auto_push: this.state.post_types_auto_push,
			onesignal_segments: this.state.onesignal_segments,
		};

		apiFetch({
			path: '/appsignal/v1/options',
			method: 'POST',
			data: options
		}).then(() => {
			this.setState({
				isAPISaving: false,
				notice: { type: 'success', message: __('Settings saved successfully!') }
			});
			setTimeout(() => this.setState({ notice: null }), 3000);
		}).catch(() => {
			this.setState({
				isAPISaving: false,
				notice: { type: 'error', message: __('Failed to save settings.') }
			});
		});
	}

	sendTestMessage() {
		if (!this.state.onesignal_message.trim()) {
			this.setState({
				notice: { type: 'error', message: __('Please enter a test message.') }
			});
			return;
		}

		apiFetch({
			path: '/appsignal/v1/test-message',
			method: 'POST',
			data: { message: this.state.onesignal_message }
		}).then((response) => {
			this.setState({
				notice: { type: 'success', message: response.message },
				onesignal_message: ''
			});
			setTimeout(() => this.setState({ notice: null }), 3000);
		}).catch((error) => {
			this.setState({
				notice: { type: 'error', message: error.message || __('Failed to send test message.') }
			});
		});
	}

	render() {
		return (
			<Fragment>
				<div className="appsignal-header">
					<div className="appsignal-container">
						<div className="appsignal-logo" style={{ display: 'flex', alignItems: 'center' }}>
							<div className="appp-icon"><img style={{ width: '50px', marginRight: '10px', borderRadius: '4px' }} src={apppLogo} alt="AppPresser" /></div>
							<div>
								<h1 style={{ margin: '0px' }}>{__('AppSignal')}</h1>
								<p style={{ margin: '10px 0px 0px 0px' }}>{__('By AppPresser')}</p>
							</div>
						</div>
					</div>
				</div>

				{!this.state.isAPILoaded ? (
					<div className="appsignal-spinner-center">
						<Placeholder>
							<div className="d-flex justify-content-center">
								<Spinner />
							</div>
						</Placeholder>
					</div>
				) : (
					<div className="appsignal-main">

						<PanelBody title={__('OneSignal Configuration')} initialOpen={true}>
							<PanelRow>
								<TextControl
									label={__('OneSignal App ID')}
									value={this.state.onesignal_app_id}
									help={
										<Fragment>
											{__('The App ID for OneSignal. ')}
											<ExternalLink
												href="https://documentation.onesignal.com/docs/keys-and-ids"
												target="_blank"
												rel="noopener noreferrer"
											>
												{__('Learn more.')}
											</ExternalLink>
										</Fragment>
									}
									placeholder={__('OneSignal App ID')}
									onChange={value => this.changeOptions('onesignal_app_id', value)}
								/>
							</PanelRow>
							<PanelRow>
								<TextControl
									label={__('OneSignal REST Key')}
									value={this.state.onesignal_rest_api_key}
									help={__('The OneSignal REST Secret Key. DO NOT expose this key to anyone.')}
									placeholder={__('OneSignal REST Key')}
									onChange={value => this.changeOptions('onesignal_rest_api_key', value)}
								/>
							</PanelRow>
						</PanelBody>

						<PanelBody title={__('Access Control')}>
							<PanelRow>
								<BaseControl
									label={__('User Role Access')}
									help={__('Choose user roles that can access push notifications.')}
								>
									<div className="appsignal-checkbox-group">
										{this.state.roles.map(role => (
											<CheckboxControl
												key={role.value}
												label={role.label}
												checked={this.state.onesignal_access.includes(role.value)}
												onChange={checked => {
													const newAccess = checked
														? [...this.state.onesignal_access, role.value]
														: this.state.onesignal_access.filter(r => r !== role.value);
													this.changeOptions('onesignal_access', newAccess);
												}}
											/>
										))}
									</div>
								</BaseControl>
							</PanelRow>
						</PanelBody>

						<PanelBody title={__('Post Types')}>
							<PanelRow>
								<BaseControl
									label={__('Post Push')}
									help={__('Choose post types to add push metabox.')}
								>
									<div className="appsignal-checkbox-group">
										{this.state.postTypes.map(postType => (
											<CheckboxControl
												key={postType.value}
												label={postType.label}
												checked={this.state.post_types_auto_push.includes(postType.value)}
												onChange={checked => {
													const newTypes = checked
														? [...this.state.post_types_auto_push, postType.value]
														: this.state.post_types_auto_push.filter(t => t !== postType.value);
													this.changeOptions('post_types_auto_push', newTypes);
												}}
											/>
										))}
									</div>
								</BaseControl>
							</PanelRow>
						</PanelBody>

						<PanelBody title={__('Segments & Testing')}>
							<PanelRow>
								<ToggleControl
									label={__('Testing Mode')}
									help={
										<Fragment>
											{__('Send notifications to testing segment. ')}
											<ExternalLink
												href="https://documentation.onesignal.com/docs/segmentation"
												target="_blank"
												rel="noopener noreferrer"
											>
												{__('Learn more.')}
											</ExternalLink>
										</Fragment>
									}
									checked={this.state.onesignal_testing}
									onChange={checked => this.changeOptions('onesignal_testing', checked)}
								/>
							</PanelRow>
							<PanelRow>
								<BaseControl
									label={__('Segments')}
									help={__('Select the segments to send notifications to.')}
								>
									<div className="appsignal-checkbox-group">
										{this.state.segments.map(segment => (
											<CheckboxControl
												key={segment.value}
												label={segment.label}
												checked={this.state.onesignal_segments.includes(segment.value)}
												onChange={checked => {
													const newSegments = checked
														? [...this.state.onesignal_segments, segment.value]
														: this.state.onesignal_segments.filter(s => s !== segment.value);
													this.changeOptions('onesignal_segments', newSegments);
												}}
											/>
										))}
									</div>
								</BaseControl>
							</PanelRow>
						</PanelBody>

						<PanelBody title={__('Test Message')}>
							<PanelRow>
								<TextControl
									label={__('Test Message')}
									value={this.state.onesignal_message}
									help={__('Send a test message to selected segments.')}
									placeholder={__('Enter test message...')}
									onChange={value => this.changeOptions('onesignal_message', value)}
								/>
							</PanelRow>
							<PanelRow>
								<div className="appsignal-text-field-button-group flex-right">
									<Button
										isSecondary
										disabled={!this.state.onesignal_message.trim()}
										onClick={() => this.sendTestMessage()}
									>
										{__('Send Test Message')}
									</Button>
								</div>
							</PanelRow>
						</PanelBody>

						<div className="appsignal-text-field-button-group flex-right">
							<Button
								isPrimary
								isLarge
								disabled={this.state.isAPISaving}
								onClick={() => this.saveOptions()}
								className="save-button"
							>
								{this.state.isAPISaving ? __('Saving...') : __('Save Settings')}
							</Button>
						</div>

						{this.state.notice && (
							<div className="appsignal-notice">
								<Notice
									status={this.state.notice.type}
									isDismissible={true}
									onRemove={() => this.setState({ notice: null })}
								>
									{this.state.notice.message}
								</Notice>
							</div>
						)}

					</div>
				)}
			</Fragment>
		);
	}
}

// Only render the settings page if the root element exists
const settingsRoot = document.getElementById('appsignal');
if (settingsRoot) {
	render(
		<App />,
		settingsRoot
	);
}
