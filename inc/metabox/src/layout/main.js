import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
const { __ } = wp.i18n;
const {
	Component,
} = wp.element;

const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;

class AstraMetaBox extends Component {
	constructor(props) {
		super(props);

		this.state = {
			color: '#000000',
			popup_cust_width: 0,
			popup_cust_height: 0,
			colorValue: '#f00',
			gradientValue: 'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',
		};
	}

	render() {

		const HrSeparator = () => {
			return (
				<hr className="cpro-editor__separator" />
			);
		}

		const { design, configure, live } = this.props.meta;

		const icon = <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" xmlSpace="preserve" viewBox="0 0 70 70">
			<path fill="none" d="M41.852 14.342c-3.354 4.316-6.037 9.069-8.571 13.898-1.109 2.115-2.218 4.229-3.151 6.427.664-1.563 1.931-3.073 2.905-4.449 3.182-4.493 6.58-8.826 9.998-13.141C46.989 12.09 53.229 4.152 52.48 4.653c-4.195 2.798-7.565 5.743-10.628 9.689z" />
			<path fill="#FFFFF" d="M34.684 18.629c-.682 1.087-1.771 3.654-2.964 6.724l-6.293.643-10.984 16.15 9.485-6.693 1.827 1.121c-2.212 3.146-2.24 5.059-2.24 5.059l6.268 4.473s2.36-.939 4.599-3.623l1.762 1.275-2.739 11.278 10.979-16.148-1.664-6.039c2.598-2.38 4.803-4.52 5.769-5.742 3.169-4 4.099-8.787 6.785-15.867h.002c.973-3.32 1.438-6.782.832-9.968-3.188.607-6.236 2.311-8.97 4.434l.005.004c-5.745 4.877-10.358 9.566-12.459 12.919zm8.348-1.552c-3.418 4.314-6.816 8.648-9.998 13.141-.975 1.376-2.241 2.887-2.905 4.449.934-2.198 2.042-4.312 3.151-6.427 2.534-4.829 5.217-9.582 8.571-13.898 3.063-3.946 6.434-6.891 10.628-9.689.75-.501-5.491 7.436-9.447 12.424z" />
			<path fill="#FFFFF" d="M23.284 42.127c-.022.046-13.607 24.394-13.728 24.723-.165.445.45.847.836.524.317-.261 18.469-20.179 19.059-20.835l-6.167-4.412zM13.242 62.321l.096-.172a.417.417 0 0 1 .037.026c-.044.052-.088.098-.133.146zm-.775-7.267c-4.803-5.096-7.75-11.955-7.75-19.494 0-15.687 12.763-28.449 28.451-28.449 3.101 0 4.506.04 7.304.962 1.223-1.154 2.524-2.5 3.883-3.741-3.93-1.639-6.663-1.938-11.187-1.938C14.849 2.394 0 17.243 0 35.56c0 9.18 3.732 17.483 9.759 23.486.016.015.038.015.057.022l2.705-3.857c-.005-.066-.021-.122-.054-.157zm44.936-42.059c-.677 1.809-1.4 3.36-1.923 4.892 4.427 5.018 6.138 10.474 6.138 17.673 0 15.69-12.764 28.451-28.45 28.451-5.374 0-8.733-.405-13.024-3.001-1.162 1.268-2.287 2.173-3.261 3.234 5.173 3.332 9.676 4.483 16.285 4.483 18.316 0 33.165-14.852 33.165-33.168 0-9.333-2.721-16.536-8.93-22.564z" />
			<path fill="#FFFFF" d="M13.242 62.321c.045-.048.089-.095.133-.146a.209.209 0 0 0-.037-.026c-.033.06-.063.115-.096.172z" />
		</svg>;
		return (
			<>
				<PluginSidebarMoreMenuItem
					target="theme-meta-panel"
					icon={icon}
				>
					{'Convert Pro Popup Settings'}
				</PluginSidebarMoreMenuItem>
				<PluginSidebar
					isPinnable={true}
					icon={icon}
					name="theme-meta-panel"
					title={'Convert Pro Popup Settings'}
				>
					<>
						<MakePublish makePublish={live} onChangeMetaValue={(val, key) => {

							this.props.setMetaFieldValue(val, key);

						}} />
					</>

					<div className="astra-sidebar-container components-panel__body is-opened"> </div>
				</PluginSidebar>
			</>
		);
	}
}
export default compose(
	withSelect((select) => {
		const postMeta = select('core/editor').getEditedPostAttribute('meta');
		const oldPostMeta = select('core/editor').getCurrentPostAttribute('meta');
		const { __experimentalGetPreviewDeviceType = null } = select('core/edit-post');

		let deviceType = __experimentalGetPreviewDeviceType ? __experimentalGetPreviewDeviceType() : null;

		if (0 === postMeta.configure.length) {
			postMeta.configure = defaultAttributes;
		}

		return {
			meta: { ...oldPostMeta, ...postMeta },
			oldMeta: oldPostMeta,
			deviceType: deviceType,
		};
	}),

	withDispatch((dispatch) => {
		return {
			setMetaFieldValue: (value, field) => dispatch('core/editor').editPost(
				{ meta: { [field]: value } }
			),
		}

	}),
)(AstraMetaBox);
