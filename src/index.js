/* eslint-disable camelcase */
/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const {
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
	SelectControl,
	CheckboxControl,
	Notice
} = wp.components;

const {
	render,
	Component,
	Fragment
} = wp.element;

/**
 * Internal dependencies
 */
import './css/settings.css';
import apppLogo from '../assets/appp-logo.png';

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
		// Load options
		wp.apiRequest({ path: '/appsignal/v1/options' }).then((options) => {
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

		// Load roles
		wp.apiRequest({ path: '/appsignal/v1/roles' }).then((roles) => {
			this.setState({ roles });
		});

		// Load post types
		wp.apiRequest({ path: '/appsignal/v1/post-types' }).then((postTypes) => {
			this.setState({ postTypes });
		});

		// Load segments
		wp.apiRequest({ path: '/appsignal/v1/segments' }).then((segments) => {
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

		wp.apiRequest({
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

		wp.apiRequest({
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

						{/* OneSignal Configuration */}
						<PanelBody title={__('OneSignal Configuration')} initialOpen={true}>
							<PanelRow>
								<TextControl
									label={__('OneSignal App ID')}
									value={this.state.onesignal_app_id}
									help={
										<Fragment>
										{__('The App ID for OneSignal. ' )}
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
							{/* <PanelRow>
								<TextControl
									label={__('Github Personal Access Token')}
									value={this.state.github_access_token}
									help={__('Token required for plugin updates.')}
									placeholder={__('Github Access Token')}
									onChange={value => this.changeOptions('github_access_token', value)}
								/>
							</PanelRow> */}
						</PanelBody>

						{/* Access Control */}
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

						{/* Post Types */}
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

						{/* Segments and Testing */}
						<PanelBody title={__('Segments & Testing')}>
							<PanelRow>
								<ToggleControl
									label={__('Testing Mode')}
									help={
										<Fragment>
											{__('Send notifications to testing segment. ' )}
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
						{/* Test Message */}
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

						{/* Save Settings */}
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

						{/* Fixed Notice */}
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

render(
	<App />,
	document.getElementById('appsignal')
);